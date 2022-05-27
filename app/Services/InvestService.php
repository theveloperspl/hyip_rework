<?php

namespace App\Services;

use App\Classes\Advcash\authDTO;
use App\Classes\Advcash\findTransaction;
use App\Classes\Advcash\MerchantWebService;
use App\Classes\Converter;
use App\Events\NewDeposit;
use App\Models\Address;
use App\Models\Currency;
use App\Models\Investment;
use App\Models\User;
use CoinpaymentsAPI;
use Exception;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Webpatser\Uuid\Uuid;


class InvestService
{
    private CoinpaymentsAPI $coinPaymentsApi;
    private ReferralsService $referralsService;
    private CurrencyService $currencyService;
    private InvestmentService $investmentService;

    public function __construct(ReferralsService $referralsService, CurrencyService $currencyService, InvestmentService $investmentService)
    {
        $this->coinPaymentsApi = new CoinpaymentsAPI(config('coinpayments.private_key'), config('coinpayments.public_key'), 'json');
        $this->referralsService = $referralsService;
        $this->currencyService = $currencyService;
        $this->investmentService = $investmentService;
    }

    /**
     * Process and create investment
     *
     * @param User $user
     * @param int $currency
     * @param float $amount
     * @param $planClass
     * @return Investment
     * @throws Exception
     */
    public function newInvestment(User $user, int $currency, float $amount, $planClass): Investment
    {
        $usd = Converter::toCents($amount);
        $currency = Currency::find($currency);

        if ($currency->type === 'crypto') {
            $amount = Converter::fiatToSatoshi($usd, $currency->long);
        } else {
            $amount = $usd;
        }

        $unique = false;
        $uuid = '';
        while (!$unique) {
            $uuid = Uuid::generate()->string;
            $uuidExists = $this->investmentService->investmentExists($uuid);
            if (!$uuidExists) {
                $unique = true;
            }
        }

        $profit = $this->calculateProfit($currency, $amount, $planClass);
        $cycles = $planClass->getCycles();

        $investment = new Investment();
        $investment->uuid = $uuid;
        $investment->user_id = $user->id;
        $investment->currency_id = $currency->id;
        $investment->amount = $amount;
        $investment->profit = $profit;
        $investment->usd = $usd;
        $investment->cycles = $cycles;
        $investment->plan = $planClass->getInternalCode();
        $investment->save();

        return $investment;
    }

    /**
     * Calculate profits based on plan, currency and investment amount
     *
     * @param Currency $currency
     * @param int $amount
     * @param $planClass
     * @return int
     */
    public function calculateProfit(Currency $currency, int $amount, $planClass): int
    {
        $scale = 2;
        if ($currency->type === 'crypto') {
            $scale = 8;
        }

        $planType = $planClass->getType();
        $profit = 0;
        switch ($planType) {
            case 'daily':
            case 'split':
                $profit = $planClass->calculateProfits($amount, $scale);
                break;
            case 'after':
                $profit = $planClass->calculateReturn($amount, $scale);
                break;
        }

        return $profit;
    }

    /**
     * Fetch crypto address for selected investment
     *
     * @param Investment $investment
     * @param Currency $currency
     * @return Address
     * @throws Exception
     */
    private function generateInvestmentAddress(Investment $investment, Currency $currency): Address
    {
        $investmentId = $investment->uuid;
        $currencyBigCode = $currency->big;

        $url = url("/crypto_ping/{$investmentId}/{$currencyBigCode}");
        $addressResponse = $this->coinPaymentsApi->GetCallbackAddressWithIpn($currencyBigCode, $url);

        $address = new Address();
        $address->investment_id = $investmentId;
        $address->address = $addressResponse['result']['address'];
        if (!empty($addressResponse['result']['dest_tag'])) {
            $address->destination_tag = $addressResponse['result']['dest_tag'];
        }
        $address->save();

        return $address;
    }

    /**
     * Display proper investment screen
     *
     * @param Investment $investment
     * @return Application|Factory|View
     * @throws BindingResolutionException
     */
    public function loadInvestView(Investment $investment): View
    {
        //check if plan is still available for investing
        loadPlan($investment->plan);

        $investView = '';
        $currency = $investment->currency;

        if ($currency->type === 'crypto') {
            if ($currency->mode === 'automatic') {
                if (!$investment->address) {
                    try {
                        $address = $this->generateInvestmentAddress($investment, $currency);
                        $address = $address->only('address', 'destination_tag');
                    } catch (Exception $exception) {
                        return view('errors.cards.investment_failed');
                    }
                } else {
                    $address = $investment->address->only('address', 'destination_tag');
                }
            } else {
                $address = [];
            }
        } else {
            $address = $investment->uuid;
        }

        if ($currency->type === 'crypto') {
            $investmentAmount = Converter::toBitcoin($investment->amount);
            $investView = 'invest.crypto';
        } else {
            $investmentAmount = Converter::toDollars($investment->amount);
            switch ($currency->long) {
                case 'pm':
                    $investView = 'invest.pm';
                    break;
                case 'payeer':
                    $investView = 'invest.payeer';
                    break;
                case 'advcash':
                    $investView = 'invest.advcash';
                    break;
            }
        }

        return view($investView, compact('investment', 'address', 'investmentAmount'));
    }

    /**
     * Activate selected investment
     *
     * @param Investment $investment
     * @param string $hash
     * @return Investment
     */
    public function activateInvestment(Investment $investment, string $hash): Investment
    {
        $investment->hash = $hash;
        $investment->status = '1';
        $investment->save();

        return $investment;
    }

    /**
     * Process Advcash deposit
     *
     * @param $transactionId
     * @param Investment $investment
     * @param $planClass
     * @return JsonResponse
     */
    public function processAdvcash($transactionId, Investment $investment, $planClass): JsonResponse
    {
        $scale = 2;
        $alreadyDeposited = $this->investmentService->transactionHashExists($transactionId);
        if (!$alreadyDeposited) {
            $advcashWebService = new MerchantWebService();

            $advcash = new authDTO();
            $advcash->apiName = config('advcash.api_name');
            $advcash->accountEmail = config('advcash.email');
            $advcash->authenticationToken = $advcashWebService->getAuthenticationToken(config('advcash.api_key'));

            $findTransaction = new findTransaction();
            $findTransaction->arg0 = $advcash;
            $findTransaction->arg1 = $transactionId;

            try {
                $transactionResponse = $advcashWebService->findTransaction($findTransaction);
            } catch (Exception $e) {
                return response()->json(['type' => 'error', 'message' => __('invest.empty')]);
            }

            if ($transactionResponse->return->currency == 'USD' &&
                $transactionResponse->return->id === $transactionId &&
                $transactionResponse->return->comment === $investment->uuid &&
                $transactionResponse->return->direction == 'INCOMING' &&
                $transactionResponse->return->status == 'COMPLETED') {
                $received = $transactionResponse->return->amount;
                $cents = Converter::toCents($received);
                $planMinimum = $planClass->getMinimumDeposit();
                $planMaximum = $planClass->getMaximumDeposit();

                if ($cents < $planMinimum) {
                    return response()->json(['type' => 'error', 'message' => __('invest.minimum', ['min' => Converter::toDollars($planMinimum)])]);
                }

                if ($cents > $planMaximum) {
                    return response()->json(['type' => 'error', 'message' => __('invest.maximum', ['max' => Converter::toDollars($planMaximum)])]);
                }

                if ($cents !== $investment->amount) {
                    return response()->json(['type' => 'error', 'message' => __('invest.empty')]);
                }

                //process referrals here
                if (config('referrals.enabled')) {
                    $this->referralsService->processReferralCommission($cents, $scale, $investment);
                }
                //check if user activated currency earlier
                $activeCurrencies = $this->currencyService->userActiveCurrencies($investment->user_id);
                if (!$activeCurrencies->contains($investment->currency_id)) {
                    $this->currencyService->addCurrency($investment->user_id, $investment->currency_id);
                }
                //activate investment
                $investment = $this->activateInvestment($investment, $transactionResponse->return->id);
                //deploy new investment event
                event(new NewDeposit($investment));

                return response()->json(['type' => 'success', 'status' => 'active', 'message' => __('invest.success')]);
            }

            return response()->json(['type' => 'error', 'message' => __('invest.empty')]);
        } else {
            return response()->json(['type' => 'error', 'message' => __('invest.already')]);
        }
    }
}

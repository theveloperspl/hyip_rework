<?php

use App\View\Components\CurrenciesDropdown;
use App\View\Components\CurrenciesPills;
use App\View\Components\ReferralRates;
use App\View\Components\WithdrawalMinimums;
use ChrisKonnertz\BBCode\BBCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\File;
use ReCaptcha\ReCaptcha;
use Telegram\Bot\Helpers\Emojify;
use Telegram\Bot\Keyboard\Keyboard;

///**
// * Update .env file content
// * @param array $data
// */
//if (!function_exists('update_env')) {
//    function update_env($data = []): void
//    {
//        $envFile = base_path('.env');
//
//        if (file_exists($envFile)) {
//            $envContent = file_get_contents($envFile);
//            foreach ($data as $key => $value) {
//                file_put_contents($envFile, str_replace($key . '=' . env($key), $key . '=' . $value, $envContent));
//            }
//        }
//    }
//}

/**
 * @param Request $request
 * @param string $response
 * @param string $action
 * @return bool
 */
if (!function_exists('passedCaptcha')) {
    function passedCaptcha(Request $request, string $response, string $action): bool
    {
        if (config('app.debug')) {
            return true;
        }

        try {
            $recaptcha = new ReCaptcha(config('recaptcha.secret_key'));
            $ip = $request->ip();
            $resp = $recaptcha->setExpectedHostname(getDomain(config('app.url')))->setExpectedAction($action)->setScoreThreshold(config('recaptcha.score_threshold'))->verify($response, $ip);
            if ($resp->isSuccess()) {
                return true;
            }
        } catch (RuntimeException $exception) {
            return false;
        }

        return false;
    }
}

/**
 * @param $domain
 * @return string
 */
if (!function_exists('getDomain')) {
    function getDomain($domain): string
    {
        $parse = parse_url($domain);
        return $parse['host'];
    }
}

if (!function_exists('bcround')) {
    function bcround($number, $precision = 0)
    {
        if (str_contains($number, '.')) {
            if ($number[0] !== '-') {
                return bcadd($number, '0.' . str_repeat('0', $precision) . '5', $precision);
            }

            return bcsub($number, '0.' . str_repeat('0', $precision) . '5', $precision);
        }
        return $number;
    }
}

/**
 * Emojify Telegram bot keyboard and return for future use in
 *
 * @return Keyboard
 */
if (!function_exists('getEmojifiedTelegramKeyboard')) {
    function getEmojifiedTelegramKeyboard(): Keyboard
    {
        $keyboard = config('constants.telegram_keyboard');

        array_walk_recursive($keyboard, function (&$item) {
            $item = Emojify::text($item);
        });

        return Keyboard::make(['keyboard' => $keyboard]);
    }
}

/**
 * Parse FAQ to replace all macros with corresponding strings or widgets
 *
 * @param string $toBeParsed
 * @param array|null $parsers
 * @param array|null $additionalParameters
 * @return mixed
 */
if (!function_exists('faqParser')) {
    function faqParser(string $toBeParsed, array $parsers = null, array $additionalParameters = null): string
    {
        $bbcode = new BBCode();
        //parse known macros
        foreach ((array)$parsers as $parser) {
            switch ($parser) {
                case '[platform-name]':
                    $toBeParsed = str_replace($parser, config('app.name'), $toBeParsed);
                    break;
                case '[platform-email]':
                    $toBeParsed = str_replace($parser, config('app.email'), $toBeParsed);
                    break;
                case '[company-name]':
                    $toBeParsed = str_replace($parser, config('company.name'), $toBeParsed);
                    break;
                case '[company-address]':
                    $toBeParsed = str_replace($parser, config('company.address'), $toBeParsed);
                    break;
                case '[company-number]':
                    $toBeParsed = str_replace($parser, config('company.number'), $toBeParsed);
                    break;
                case '[launch-date]':
                    $toBeParsed = str_replace($parser, config('company.launch_date'), $toBeParsed);
                    break;
                case '[referral-levels]':
                    $toBeParsed = str_replace($parser, config('referrals.levels'), $toBeParsed);
                    break;
                case '[br]':
                    $toBeParsed = str_replace($parser, '<br/>', $toBeParsed);
                    break;
                case '[currencies-dropdown]':
                    $dropdown = App::make(CurrenciesDropdown::class)->render();
                    $toBeParsed = str_replace($parser, $dropdown, $toBeParsed);
                    break;
                case '[currencies-pills]':
                    $pills = App::make(CurrenciesPills::class)->render();
                    $toBeParsed = str_replace($parser, $pills, $toBeParsed);
                    break;
                case '[referral-rates]':
                    $rates = App::make(ReferralRates::class)->render();
                    $toBeParsed = str_replace($parser, $rates, $toBeParsed);
                    break;
                case '[withdrawal-minimums]':
                    $minimums = App::make(WithdrawalMinimums::class)->render();
                    $toBeParsed = str_replace($parser, $minimums, $toBeParsed);
                    break;
            }
        }
        //look for BBCODE macros too and parse them
        return $bbcode->render($toBeParsed, false, false);
    }
}

/**
 * Set value in .env file programmatically
 *
 * @param string $envKey
 * @param string $envValue
 * @return mixed
 */
if (!function_exists('setEnvironmentValue')) {
    function setEnvironmentValue(string $envKey, string $envValue): void
    {
        $envFile = app()->environmentFilePath();
        $str = file_get_contents($envFile);

        $str .= "\n"; // In case the searched variable is in the last line without \n
        $keyPosition = strpos($str, "{$envKey}=");
        $endOfLinePosition = strpos($str, PHP_EOL, $keyPosition);
        $oldLine = substr($str, $keyPosition, $endOfLinePosition - $keyPosition);
        $str = str_replace($oldLine, "{$envKey}={$envValue}", $str);
        $str = substr($str, 0, -1);

        $fp = fopen($envFile, 'w');
        fwrite($fp, $str);
        fclose($fp);
    }
}

/**
 * Get available wallet validators
 *
 * @return array
 */
if (!function_exists('getAvailableValidators')) {
    function getAvailableWalletValidators(): array
    {
        $files = File::files(app_path("Validators"));
        $validators = [];
        foreach ($files as $file) {
            $fileName = $file->getBasename();
            if ($fileName !== 'Validation.php') {
                $validators[] = str_replace('.php', '', $fileName);
            }
        }

        return $validators;
    }
}

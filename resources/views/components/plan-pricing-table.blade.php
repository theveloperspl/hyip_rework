
<div class="pricingTable">
    <div class="pricingTable-header">
        <h3 class="title">{{ $plan->getName() }}</h3>
    </div>
    <div class="price-value">
        <span class="amount">
            @isPlanDynamicProfits($plan)
                        {{ $plan->getJoinedPercentage() }}%
            @notDynamicProfits
                        {{ $plan->getPercentage() }}%
            @endPlanDynamicProfits
        </span>
        @switch($plan->getType())
            @case('after')
                <span class="duration">
                    {{ __('invest.after', ['cycles' => $plan->getCycles()]) }}
                </span>
                @break
            @case('split')
                <span class="duration">
                    {{ __('invest.after', ['cycles' => $plan->calculateSteps()->formatSteps('comas')]) }}
                </span>
                <span class="duration mt-2 fs-18">
                    {{ __('invest.total_return', ['total' => $plan->getTotalPercentage()]) }}
                </span>
                @break
            @default
                <span class="duration">
                    @isPlanInfinite($plan)
                        {!! __("invest.{$period}", ['cycles' => config('constants.infinity_symbol')]) !!}
                    @notInfinite
                        {{ __("invest.{$period}", ['cycles' => $plan->getCycles()]) }}
                    @endPlanInfinite
                </span>
                <span class="duration mt-2 fs-18">
                    @isPlanDynamicProfits($plan)
                        {{ __('invest.total_return', ['total' => "{$plan->getTotalPercentage()['minimum']}-{$plan->getTotalPercentage()['maximum']}"]) }}
                    @notDynamicProfits
                        @isPlanInfinite($plan)
                            {!! __('invest.total_return', ['total' => config('constants.infinity_symbol')]) !!}
                        @notInfinite
                            {{ __('invest.total_return', ['total' => $plan->getTotalPercentage()]) }}
                        @endPlanInfinite
                    @endPlanDynamicProfits
                </span>
        @endswitch
    </div>
    <div class="pricing-content">
        <ul>
            <li>{{ __('invest.minimum', ['min' => Converter::toDollars($plan->getMinimumDeposit())]) }}</li>
            <li>{{ __('invest.maximum', ['max' => Converter::toDollars($plan->getMaximumDeposit())]) }}</li>
            <li>{{ __('withdraw.instant') }}</li>
            <li>
                @principalReturn($plan)
                    {{ __('invest.principal_return') }}
                @else
                    {{ __('invest.principal_included') }}
                @endprincipalReturn
            </li>
            @isPlanCancelable($plan)
            <!-- <li>{{ __('invest.cancelable') }}</li> -->
                @if($plan->getEarlyCancellationFee() > 0)
                    <li>{{ __('invest.cancel_fee', ['percentage' => $plan->getEarlyCancellationFee()]) }}</li>
                    <li>{{ __('invest.cancellation_fee_period', ['days' => $plan->getCancelPeriod()]) }}</li>
                @else
                    <li>{{ __('invest.no_cancel_fee') }}</li>
                @endif
            <li>{{ $plan->getBalanceCooldown() > 0 ? __('invest.return_delay', ['hours' => $plan->getBalanceCooldown()]) : __('invest.instant_return') }}</li>
            @endPlanCancelable
        </ul>
        @if($routeName === 'panel.invest')
            <a href="{{ route('invest.load_module', ['plan' => $plan->getInternalCode()]) }}" class="btn btn-primary btn-block plan-selector">{{ __('common.select') }}</a>
        @elseif($routeName === 'invest.load_module')
            <a href="{{ route('panel.invest') }}" class="btn btn-danger btn-block">{{ __('invest.change') }}</a>
        @endif
    </div>
</div>

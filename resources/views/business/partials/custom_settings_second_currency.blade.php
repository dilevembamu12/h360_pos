{{--  personnalize custom code 06022024-MULTIDEVISE002 --}}

@php
//dd($currencies);
//dd($business);

if(empty($business->second_currency_settings)){
    $business->second_currency_settings="[]";
}
@endphp
<div class="pos-tab-content">
    <div class="row">
        


        <div class="col-sm-12">
            <strong>@lang('lang_v1.payment_options'): @show_tooltip(__('lang_v1.payment_option_help'))</strong>
        </div>
        <div class="col-sm-12">
            <strong>Devise de base : {{$currencies[$business->currency_id]}}</strong>
        </div>


        <div class="col-sm-12">
            <strong></strong>
            <div class="form-group">
                <table class="table table-condensed table-striped">
                    <thead>
                        <tr>
                            <th class="text-center">@lang('lang_v1.enable')</th>
                            
                            <th class="text-center">Devise principale</th>
                            <th class="text-center">Taux d'echange</th>
                            <th class="text-center">Devise secondaie</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($currencies as $key => $value)
                            @php if($key==$business->currency_id) continue @endphp
                            <tr>
                                <td class="text-center">{!! Form::checkbox(
                                    'currency_is_enabled_'.$key,
                                    1,
                                    isset(json_decode($business->second_currency_settings,true)['currency_'.$key]) ,
                                ) !!}</td>
                                
                                
                                <td class="text-center">1 {{$currencies[$business->currency_id]}}</td>
                                <td class="text-left">
                                    <div class="input-group">
                                        <span class="input-group-addon">
                                            <i class="fa fa-equals"></i>
                                        </span>
                                        @php
                                        //dd(array_key_exists('currency_'.$key,json_decode($business->second_currency_settings,true)));
                                        @endphp
                                        {!! Form::text('currency_'.$key, array_key_exists('currency_'.$key,json_decode($business->second_currency_settings,true)) ? @num_format(json_decode($business->second_currency_settings,true)['currency_'.$key]) : @num_format(0), ['class' => 'form-control input_number']); !!}
                                    </div>
                                </td>

                                <td class="text-center">{{ $value }}</td>
                                

                            </tr>
                        @endforeach

                    </tbody>
                </table>
            </div>
        </div>
        




        {{--
        <div class="col-sm-12">
            <strong>@lang('lang_v1.payment_options'): @show_tooltip(__('lang_v1.payment_option_help'))</strong>
            <div class="form-group">
                <table class="table table-condensed table-striped">
                    <thead>
                        <tr>
                            <th class="text-center">@lang('lang_v1.payment_method')</th>
                            <th class="text-center">@lang('lang_v1.enable')</th>
                            <th class="text-center @if (empty($accounts)) hide @endif">
                                @lang('lang_v1.default_accounts') @show_tooltip(__('lang_v1.default_account_help'))</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $default_payment_accounts = !empty($location->default_payment_accounts) ? json_decode($location->default_payment_accounts, true) : [];
                        @endphp
                        @foreach ($payment_types as $key => $value)
                            <tr>
                                <td class="text-center">{{ $value }}</td>
                                <td class="text-center">{!! Form::checkbox(
                                    'default_payment_accounts[' . $key . '][is_enabled]',
                                    1,
                                    !empty($default_payment_accounts[$key]['is_enabled']),
                                ) !!}</td>
                                <td class="text-center @if (empty($accounts)) hide @endif">
                                    {!! Form::select(
                                        'default_payment_accounts[' . $key . '][account]',
                                        $accounts,
                                        !empty($default_payment_accounts[$key]['account']) ? $default_payment_accounts[$key]['account'] : null,
                                        ['class' => 'form-control input-sm'],
                                    ) !!}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        --}}















    </div>
</div>


{{--  **************************************************** --}}
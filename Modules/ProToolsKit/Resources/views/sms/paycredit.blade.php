@php
    use App\Business;
    use App\System;

    $system_currency = System::getCurrency();
    $code = strtolower($system_currency->code);

//    dd($gateways);
@endphp
@php
//    dd($plan);
@endphp

@extends($layout)

@section('title', __('superadmin::lang.subscription'))

@section('content')

    <!-- Main content -->
    <section class="content">

        @include('superadmin::layouts.partials.currency')

        <div class="box box-success">
            <div class="box-header">
                <h3 class="box-title">@lang('superadmin::lang.pay_and_subscribe')</h3>
            </div>

            <div class="box-body">
                <div class="col-md-8">
                    <h3>
                        {{ $plan->name }}

                        (<span class="display_currency"
                            data-currency_symbol="true">{{ number_format($plan->amount, session('business.currency_precision', 2), session('currency')['decimal_separator'], session('currency')['thousand_separator']) }}</span>
                        <small>
                            / {{ $plan->duration }} Jours
                        </small>)
                    </h3>
                    <ul>

                        @if (!empty($plan->sms->is_allowed))
                            <li>
                                {{ $plan->sms->credits }} SMS Credit
                            </li>
                        @endif


                        @if (!empty($plan->whatsapp->is_allowed))
                            <li>
                                {{ $plan->whatsapp->credits }} Whatsapp Credit
                            </li>
                        @endif



                        @if (!empty($plan->email->is_allowed))
                            <li>
                                {{ $plan->email->credits }} Email Credit
                            </li>
                        @endif

                        <li>1 Credit for 160 plain word</li>
                        <li>1 Credit for 70 unicode word</li>
                        <li>1 Credit for 320 word</li>
                        <li>1 Credit for per Email</li>

                    </ul>
                    <ul class="list-group">
                        @foreach ($gateways as $k => $v)
                            <div class="list-group-item">
                                <b>@lang('superadmin::lang.pay_via', ['method' => $v])</b>
                                <div class="row" id="paymentdiv_{{ $k }}">
                                    @php
                                        $view = 'protoolskit::sms.partials.pay_' . $k;
                                    @endphp
                                    @includeIf($view)
                                </div>
                            </div>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </section>
@endsection

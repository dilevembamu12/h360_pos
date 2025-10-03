@php

    use App\Business;
    use App\Utils\BusinessUtil;
    use App\Currency;

    
    $business = Business::where('id', auth()->user()->business_id)->first();

    ///dd(auth()->user());

    $businessUtil = new BusinessUtil();
    $_currencies = $businessUtil->allCurrencies();
    $__currencies = json_decode($business->second_currency_settings, true);

    $currencies = [$business->currency_id => $_currencies[$business->currency_id]];

    //dd($_currencies );

    //dd($_currencies [$_GET['currency']] );

    if (!empty($__currencies)) {
        $currencies += $__currencies;
    }

    //dd($business->currency_id);

    foreach ($currencies as $key => $value) {
        if ($key == $business->currency_id) {
            continue;
        }
        $currency_id = explode('_', $key)[1];
        $currencies[$key] = str_replace('(', "($value", $_currencies[$currency_id]);
    }

    //avant de faire quoique c'est soit: veriffierrr que le currrency passé en parametrre estt valide
$second_currency = Currency::find($transaction->location->second_currency);
    /*
		dd($second_currency);

            $request->session()->put('currency', [
                'id' => $currency->id,
                'code' => $currency->code,
                'symbol' => $currency->symbol,
                'thousand_separator' => $currency->thousand_separator,
                'decimal_separator' => $currency->decimal_separator,
                ]);
    			*/
  

@endphp

@php
//dd(session('currency')['code']);
//dd(session('currency')['symbol']);
    //dd($business);
    //dd(json_decode($transaction));
    //dd(json_decode($transaction->location));
    //dd(json_decode($transaction->location->default_payment_accounts));
    $account_id = 0;
    foreach (json_decode($transaction->location->default_payment_accounts) as $key => $value) {
        if (!empty($value->is_enabled)) {
            $account_id = $value->account;
        }
    }
    //dd($account_id);
@endphp
<div class="modal-dialog" role="document">
    <div class="modal-content">

        {!! Form::open([
            'url' => action([\App\Http\Controllers\TransactionPaymentController::class, 'store']),
            'method' => 'post',
            'id' => 'transaction_payment_add_form',
            'files' => true,
        ]) !!}
        {!! Form::hidden('transaction_id', $transaction->id) !!}
        @if (!empty($transaction->location))
            {!! Form::hidden('default_payment_accounts', $transaction->location->default_payment_accounts, [
                'id' => 'default_payment_accounts',
            ]) !!}
        @endif
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">@lang('purchase.add_payment') [en {{session('currency')['code']}} ({{session('currency')['symbol']}})] , Taux du jour 1{{session('currency')['symbol']}}=<span class="display_currency"
              data-currency_symbol="true">{{    number_format(  str_replace($second_currency->decimal_separator,'.', str_replace($second_currency->thousand_separator, '',  $transaction->location->second_currency_rate))    , session('business.currency_precision', 2), session('currency')['decimal_separator'], session('currency')['thousand_separator'])}}  {{$second_currency->symbol}}</span></h4>
              <h4 style="color: red">S.V.P veuillez inserrer le montant payé en devise de base qui est: {{$_currencies[$business->currency_id] }} </h4>
        </div>

        <div class="modal-body">
            <div class="row">
                @if (!empty($transaction->contact))
                    <div class="col-md-4">
                        <div class="well">
                            <strong>
                                @if (in_array($transaction->type, ['purchase', 'purchase_return']))
                                    @lang('purchase.supplier')
                                @elseif(in_array($transaction->type, ['sell', 'sell_return']))
                                    @lang('contact.customer')
                                @endif
                            </strong>:{{ $transaction->contact->full_name_with_business }}<br>
                            <strong>@lang('business.business'): </strong>{{ $transaction->contact->supplier_business_name }}
                        </div>
                    </div>
                @endif
                <div class="col-md-4">
                    <div class="well">
                        @if (in_array($transaction->type, ['sell', 'sell_return']))
                            <strong>@lang('sale.invoice_no'): </strong>{{ $transaction->invoice_no }}
                        @else
                            <strong>@lang('purchase.ref_no'): </strong>{{ $transaction->ref_no }}
                        @endif
                        @if (!empty($transaction->location))
                            <br>
                            <strong>@lang('purchase.location'): </strong>{{ $transaction->location->name }}
                        @endif
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="well">
                        <strong>@lang('sale.total_amount'): </strong><span class="display_currency"
                            data-currency_symbol="true">{{ $transaction->final_total }}</span><br>
                        <strong>@lang('purchase.payment_note'): </strong>
                        @if (!empty($transaction->additional_notes))
                            {{ $transaction->additional_notes }}
                        @else
                            --
                        @endif
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    @if (!empty($transaction->contact))
                        <strong>@lang('lang_v1.advance_balance'):</strong> <span class="display_currency"
                            data-currency_symbol="true">{{ $transaction->contact->balance }}</span>

                        {!! Form::hidden('advance_balance', $transaction->contact->balance, [
                            'id' => 'advance_balance',
                            'data-error-msg' => __('lang_v1.required_advance_balance_not_available'),
                        ]) !!}
                    @endif
                </div>
            </div>
            <div class="row payment_row">
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('method', __('purchase.payment_method') . ':*') !!}
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fas fa-money-bill-alt"></i>
                            </span>
                            {!! Form::select('method', $payment_types, $payment_line->method, [
                                'class' => 'form-control select2 payment_types_dropdown',
                                'required',
                                'style' => 'width:100%;',
                            ]) !!}
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('paid_on', __('lang_v1.paid_on') . ':*') !!}
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </span>
                            {!! Form::text('paid_on', @format_datetime($payment_line->paid_on), [
                                'class' => 'form-control',
                                'readonly',
                                'required',
                            ]) !!}
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('amount', __('sale.amount') ." [en ".session('currency')['code']."(".session('currency')['symbol'].")]" . ':*') !!}
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fas fa-money-bill-alt"></i>
                            </span>
                            {!! Form::text('amount', @num_format($payment_line->amount), [
                                'class' => 'form-control input_number payment_amount',
                                'required',
                                'placeholder' => 'Amount',
                                'data-rule-max-value' => $payment_line->amount,
                                'data-msg-max-value' => __('lang_v1.max_amount_to_be_paid_is', ['amount' => $amount_formated]),
                            ]) !!}
                        </div>
                    </div>
                </div>

                @php
                    $pos_settings = !empty(session()->get('business.pos_settings'))
                        ? json_decode(session()->get('business.pos_settings'), true)
                        : [];
                    $enable_cash_denomination_for_payment_methods = !empty(
                        $pos_settings['enable_cash_denomination_for_payment_methods']
                    )
                        ? $pos_settings['enable_cash_denomination_for_payment_methods']
                        : [];
                @endphp

                @if (
                    !empty($pos_settings['enable_cash_denomination_on']) &&
                        $pos_settings['enable_cash_denomination_on'] == 'all_screens')
                    <input type="hidden" class="enable_cash_denomination_for_payment_methods"
                        value="{{ json_encode($pos_settings['enable_cash_denomination_for_payment_methods']) }}">
                    <div class="clearfix"></div>
                    <div class="col-md-12 cash_denomination_div @if (!in_array($payment_line->method, $enable_cash_denomination_for_payment_methods)) hide @endif">
                        <hr>
                        <strong>@lang('lang_v1.cash_denominations')</strong>
                        @if (!empty($pos_settings['cash_denominations']))
                            <table class="table table-slim">
                                <thead>
                                    <tr>
                                        <th width="20%" class="text-right">@lang('lang_v1.denomination')</th>
                                        <th width="20%">&nbsp;</th>
                                        <th width="20%" class="text-center">@lang('lang_v1.count')</th>
                                        <th width="20%">&nbsp;</th>
                                        <th width="20%" class="text-left">@lang('sale.subtotal')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach (explode(',', $pos_settings['cash_denominations']) as $dnm)
                                        <tr>
                                            <td class="text-right">{{ $dnm }}</td>
                                            <td class="text-center">X</td>
                                            <td>{!! Form::number("denominations[$dnm]", null, [
                                                'class' => 'form-control cash_denomination input-sm',
                                                'min' => 0,
                                                'data-denomination' => $dnm,
                                                'style' => 'width: 100px; margin:auto;',
                                            ]) !!}</td>
                                            <td class="text-center">=</td>
                                            <td class="text-left">
                                                <span class="denomination_subtotal">0</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="4" class="text-center">@lang('sale.total')</th>
                                        <td>
                                            <span class="denomination_total">0</span>
                                            <input type="hidden" class="denomination_total_amount" value="0">
                                            <input type="hidden" class="is_strict"
                                                value="{{ $pos_settings['cash_denomination_strict_check'] ?? '' }}">
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                            <p class="cash_denomination_error error hide">@lang('lang_v1.cash_denomination_error')</p>
                        @else
                            <p class="help-block">@lang('lang_v1.denomination_add_help_text')</p>
                        @endif
                    </div>
                    <div class="clearfix"></div>
                @endif
                @if (!empty($accounts))
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('account_id', __('lang_v1.payment_account') . ':') !!}
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fas fa-money-bill-alt"></i>
                                </span>
                                {!! Form::select('account_id', $accounts, !empty($account_id) ? $account_id : '', [
                                    'class' => 'form-control select2',
                                    'id' => 'account_id',
                                    'style' => 'width:100%;',
                                ]) !!}
                                {{-- Form::select("account_id", $accounts, !empty($payment_line->account_id) ? $payment_line->account_id : '' , ['class' => 'form-control select2', 'id' => "account_id", 'style' => 'width:100%;']); --}}
                            </div>
                        </div>
                    </div>
                @endif
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('document', __('purchase.attach_document') . ':') !!}
                        {!! Form::file('document', [
                            'accept' => implode(',', array_keys(config('constants.document_upload_mimes_types'))),
                        ]) !!}
                        <p class="help-block">
                            @includeIf('components.document_help_text')</p>
                    </div>
                </div>
                <div class="clearfix"></div>
                @include('transaction_payment.payment_type_details')
                <div class="col-md-12">
                    <div class="form-group">
                        {!! Form::label('note', __('lang_v1.payment_note') . ':') !!}
                        {!! Form::textarea('note', $payment_line->note, ['class' => 'form-control', 'rows' => 3]) !!}
                    </div>
                </div>
            </div>
        </div>

        <div class="modal-footer">
            <button type="submit" class="btn btn-primary">@lang('messages.save')</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">@lang('messages.close')</button>
        </div>

        {!! Form::close() !!}

    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->

@php
//on doit securiser cettte partie disponible que pour les utilisateurs authentifiés
use App\Currency;
use App\Transaction;

$second_currency = Currency::find($transaction->location->second_currency);

//dd($second_currency->code);
if (!empty($transaction->second_currency) && !empty($transaction->second_currency_rate)) {
//dd(85);
}

//j'apdate la transaction pour que ca ne soit plus sous suspense si il y a une ne serait-ce un petit paiement
if ($transaction->payment_status != 'due') {
$transaction = Transaction::find($transaction->id);
$transaction->is_suspend = 0;
$transaction->save();
}

//dd(json_decode($transaction->location));

function num_f(
$input_number,
$add_symbol = false,
$business_details = null,
$is_quantity = false
//{{--  personnalize custom code 23032024-MULTIDEVISE030 -- 24032024}}
        ,
        $custom_symbol = null
        //----------------------- END PERSONNALIZE CUSTOM CODE-----------------------------------//////
    ) {
        $thousand_separator = !empty($business_details) ? $business_details->thousand_separator : session('currency')['thousand_separator'];
        $decimal_separator = !empty($business_details) ? $business_details->decimal_separator : session('currency')['decimal_separator'];

        $currency_precision = !empty($business_details) ? $business_details->currency_precision : session('business.currency_precision', 2);

        if ($is_quantity) {
            $currency_precision = !empty($business_details) ? $business_details->quantity_precision : session('business.quantity_precision', 2);
        }  

        //dd($input_number);

        $formatted = number_format($input_number, $currency_precision, $decimal_separator, $thousand_separator);

        if ($add_symbol) {
            $currency_symbol_placement = !empty($business_details) ? $business_details->currency_symbol_placement : session('business.currency_symbol_placement');
            $symbol = !empty($business_details) ? $business_details->currency_symbol : session('currency')['symbol'];

            //{{--  personnalize custom code 23032024-MULTIDEVISE030 -- 24032024}}
            //attache les informations des devises liées à la location
            if (!empty($custom_symbol)) {
                $symbol =$custom_symbol;
            }
            //----------------------- END PERSONNALIZE CUSTOM CODE-----------------------------------//////

            if ($currency_symbol_placement == 'after') {
                $formatted = $formatted . ' ' . $symbol;
            } else {
                $formatted = $symbol . ' ' . $formatted;
            }
        }

        return $formatted;
    }
@endphp

@extends('layouts.guest')
@section('title', $title)
@section('content')

    <div class="container">
        <div class="spacer"></div>
        <div class="row">
            <div class="col-xs-12 col-md-6 col-md-offset-3">
                <div class="box box-primary">
                    <div class="box-body">
                        <table class="table no-border">
                            <tr>
                                @if (!empty($transaction->business->logo))
                                    <td class="width-50 text-center">
                                        <img src="{{ asset('uploads/business_logos/' . $transaction->business->logo) }}"
                                            alt="Logo" style="max-width: 80%;">
                                    </td>
                                @endif
                                <td class="text-center">
                                    <address>
                                        <strong>{{ $transaction->business->name }}</strong><br>
                                        {{ $transaction->location->name ?? '' }}
                                        @if (!empty($transaction->location->landmark))
                                            <br>{{ $transaction->location->landmark }}
                                        @endif
                                        @if (!empty($transaction->location->city) || !empty($transaction->location->state) || !empty($transaction->location->country))
                                            <br>{{ implode(',', array_filter([$transaction->location->city, $transaction->location->state, $transaction->location->country])) }}
                                        @endif

                                        @if (!empty($transaction->business->tax_number_1))
                                            <br>{{ $transaction->business->tax_label_1 }}:
                                            {{ $transaction->business->tax_number_1 }}
                                        @endif

                                        @if (!empty($transaction->business->tax_number_2))
                                            <br>{{ $transaction->business->tax_label_2 }}:
                                            {{ $transaction->business->tax_number_2 }}
                                        @endif

                                        @if (!empty($transaction->location->mobile))
                                            <br>@lang('contact.mobile'): {{ $transaction->location->mobile }}
                                        @endif
                                        @if (!empty($transaction->location->email))
                                            <br>@lang('business.email'): {{ $transaction->location->email }}
                                        @endif
                                    </address>
                                </td>
                            </tr>
                        </table>
                        <h4 class="box-title">@lang('lang_v1.payment_for_invoice_no'): {{ $transaction->invoice_no }}</h4>
                        <table class="table no-border">
                            <tr>
                                <td>
                                    <strong>@lang('contact.customer'):</strong><br>
                                    {!! $transaction->contact->contact_address !!}
                                </td>
                            </tr>
                            <tr>
                                <td><strong>@lang('sale.sale_date'):</strong> {{ $date_formatted }}</td>
                            </tr>
                            <tr>
                                <td>
                                    <h4>@lang('sale.total_amount'): <span>{{ $total_amount }}</span></h4>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <h4>@lang('sale.total_paid'): <span>{{ $total_paid }}</span></h4>
                                </td>
                            </tr>
                        </table>

                        @if ($transaction->payment_status != 'paid')
                            <table class="table no-border">
                                <tr>
                                    <td>
                                        <h4>@lang('sale.total_payable'): <span>{{ $total_payable_formatted }}</span></h4>
                                    </td>
                                </tr>
                            </table>
                            <div class="spacer"></div>
                            <div class="spacer"></div>
                            <div class="width-50 text-center f-left">
                                <form action="{{ route('confirm_payment', ['id' => $transaction->id]) }}" method="POST">
                                    <input type="hidden" name="gateway" value="razorpay">
                                    <!-- Note that the amount is in paise -->
                                    <script src="https://checkout.razorpay.com/v1/checkout.js" data-key="{{ $pos_settings['razor_pay_key_id'] }}"
                                        data-amount="{{ $total_payable * 100 }}" data-buttontext="Pay with Razorpay"
                                        data-name="{{ $transaction->business->name }}" data-theme.color="#3c8dbc"></script>
                                    {{ csrf_field() }}
                                </form>
                            </div>
                            @if (!empty($pos_settings['stripe_public_key']) && !empty($pos_settings['stripe_secret_key']))
                                @php
                                    $code = strtolower($business_details->currency_code);
                                @endphp

                                <div class="width-50 text-center f-left">
                                    <form action="{{ route('confirm_payment', ['id' => $transaction->id]) }}"
                                        method="POST">
                                        {{ csrf_field() }}
                                        <input type="hidden" name="gateway" value="stripe">
                                        <script src="https://checkout.stripe.com/checkout.js" class="stripe-button"
                                            data-key="{{ $pos_settings['stripe_public_key'] }}"
                                            data-amount="@if (in_array($code, ['bif', 'clp', 'djf', 'gnf', 'jpy', 'kmf', 'krw', 'mga', 'pyg', 'rwf', 'ugx', 'vnd', 'vuv', 'xaf', 'xof', 'xpf'])) {{ $total_payable }} @else {{ $total_payable * 100 }} @endif"
                                            data-name="{{ $transaction->business->name }}" data-description="Pay with stripe"
                                            data-image="https://stripe.com/img/documentation/checkout/marketplace.png" data-locale="auto"
                                            data-currency="{{ $code }}"></script>
                                    </form>
                                </div>
                            @endif

                            @if (!empty($pos_settings['flexpay_merchant']) && !empty($pos_settings['flexpay_token']))
                                @php
                                    $code = strtolower($business_details->currency_code);
                                @endphp


                                <div class="width-100 text-center f-left">
                                    <img style="display: none" src="{{ asset('uploads/custom/mobilemoney/load.gif') }}" />
                                    <style>
                                        .swal2-timer-progress-bar {
                                            background: rgb(233 20 20);
                                        }

                                        .flexpay-button-el {
                                            overflow: hidden;
                                            display: inline-block;
                                            visibility: visible !important;
                                            background-image: -webkit-linear-gradient(#28a0e5, #015e94);
                                            background-image: -moz-linear-gradient(#28a0e5, #015e94);
                                            background-image: -ms-linear-gradient(#28a0e5, #015e94);
                                            background-image: -o-linear-gradient(#28a0e5, #015e94);
                                            background-image: -webkit-linear-gradient(#28a0e5, #015e94);
                                            background-image: -moz-linear-gradient(#28a0e5, #015e94);
                                            background-image: -ms-linear-gradient(#28a0e5, #015e94);
                                            background-image: -o-linear-gradient(#28a0e5, #015e94);
                                            background-image: linear-gradient(#28a0e5, #015e94);
                                            -webkit-font-smoothing: antialiased;
                                            border: 0;
                                            padding: 1px;
                                            text-decoration: none;
                                            -webkit-border-radius: 5px;
                                            -moz-border-radius: 5px;
                                            -ms-border-radius: 5px;
                                            -o-border-radius: 5px;
                                            border-radius: 5px;
                                            -webkit-box-shadow: 0 1px 0 rgba(0, 0, 0, 0.2);
                                            -moz-box-shadow: 0 1px 0 rgba(0, 0, 0, 0.2);
                                            -ms-box-shadow: 0 1px 0 rgba(0, 0, 0, 0.2);
                                            -o-box-shadow: 0 1px 0 rgba(0, 0, 0, 0.2);
                                            box-shadow: 0 1px 0 rgba(0, 0, 0, 0.2);
                                            -webkit-touch-callout: none;
                                            -webkit-tap-highlight-color: transparent;
                                            -webkit-user-select: none;
                                            -moz-user-select: none;
                                            -ms-user-select: none;
                                            -o-user-select: none;
                                            user-select: none;
                                            cursor: pointer
                                        }

                                        .flexpay-button-el::-moz-focus-inner {
                                            border: 0;
                                            padding: 0
                                        }

                                        .flexpay-button-el span {
                                            display: block;
                                            position: relative;
                                            padding: 0 12px;
                                            height: 30px;
                                            line-height: 30px;
                                            background: #1275ff;
                                            background-image: -webkit-linear-gradient(#7dc5ee, #008cdd 85%, #30a2e4);
                                            background-image: -moz-linear-gradient(#7dc5ee, #008cdd 85%, #30a2e4);
                                            background-image: -ms-linear-gradient(#7dc5ee, #008cdd 85%, #30a2e4);
                                            background-image: -o-linear-gradient(#7dc5ee, #008cdd 85%, #30a2e4);
                                            background-image: -webkit-linear-gradient(#7dc5ee, #008cdd 85%, #30a2e4);
                                            background-image: -moz-linear-gradient(#7dc5ee, #008cdd 85%, #30a2e4);
                                            background-image: -ms-linear-gradient(#7dc5ee, #008cdd 85%, #30a2e4);
                                            background-image: -o-linear-gradient(#7dc5ee, #008cdd 85%, #30a2e4);
                                            background-image: linear-gradient(#7dc5ee, #008cdd 85%, #30a2e4);
                                            font-size: 14px;
                                            color: #fff;
                                            font-weight: bold;
                                            font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
                                            text-shadow: 0 -1px 0 rgba(0, 0, 0, 0.25);
                                            -webkit-box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.25);
                                            -moz-box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.25);
                                            -ms-box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.25);
                                            -o-box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.25);
                                            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.25);
                                            -webkit-border-radius: 4px;
                                            -moz-border-radius: 4px;
                                            -ms-border-radius: 4px;
                                            -o-border-radius: 4px;
                                            border-radius: 4px
                                        }

                                        .flexpay-button-el:not(:disabled):active,
                                        .flexpay-button-el.active {
                                            background: #005d93
                                        }

                                        .flexpay-button-el:not(:disabled):active span,
                                        .flexpay-button-el.active span {
                                            color: #eee;
                                            background: #008cdd;
                                            background-image: -webkit-linear-gradient(#008cdd, #008cdd 85%, #239adf);
                                            background-image: -moz-linear-gradient(#008cdd, #008cdd 85%, #239adf);
                                            background-image: -ms-linear-gradient(#008cdd, #008cdd 85%, #239adf);
                                            background-image: -o-linear-gradient(#008cdd, #008cdd 85%, #239adf);
                                            background-image: -webkit-linear-gradient(#008cdd, #008cdd 85%, #239adf);
                                            background-image: -moz-linear-gradient(#008cdd, #008cdd 85%, #239adf);
                                            background-image: -ms-linear-gradient(#008cdd, #008cdd 85%, #239adf);
                                            background-image: -o-linear-gradient(#008cdd, #008cdd 85%, #239adf);
                                            background-image: linear-gradient(#008cdd, #008cdd 85%, #239adf);
                                            -webkit-box-shadow: inset 0 1px 0 rgba(0, 0, 0, 0.1);
                                            -moz-box-shadow: inset 0 1px 0 rgba(0, 0, 0, 0.1);
                                            -ms-box-shadow: inset 0 1px 0 rgba(0, 0, 0, 0.1);
                                            -o-box-shadow: inset 0 1px 0 rgba(0, 0, 0, 0.1);
                                            box-shadow: inset 0 1px 0 rgba(0, 0, 0, 0.1)
                                        }

                                        .flexpay-button-el:disabled,
                                        .flexpay-button-el.disabled {
                                            background: rgba(0, 0, 0, 0.2);
                                            -webkit-box-shadow: none;
                                            -moz-box-shadow: none;
                                            -ms-box-shadow: none;
                                            -o-box-shadow: none;
                                            box-shadow: none
                                        }

                                        .flexpay-button-el:disabled span,
                                        .flexpay-button-el.disabled span {
                                            color: #999;
                                            background: #f8f9fa;
                                            text-shadow: 0 1px 0 rgba(255, 255, 255, 0.5)
                                        }
                                    </style>
                                    <form action="{{Request::url()}}"
                                    method="GET" onsubmit="triggerMobilemoney(this)" id="flexpay_form" >
                                        {{ csrf_field() }}
                                        <input type="hidden" name="gateway" value="flexpay">
                                        <input type="hidden" name="phone" value="">













                                        @if (!empty($transaction->second_currency) && !empty($transaction->second_currency_rate))
                                            <button type="submit" class="flexpay-button-el"
                                                style="visibility: visible;"><span style="display: block; min-height: 30px;"
                                                    onclick="currency_code='{{ $second_currency->code }}';currency_rate={{(float) $transaction->second_currency_rate}};trigger_range()">Mobile Money
                                                    ({{ num_f( (float) $transaction->second_currency_rate *    (float)$total_payable, false, $business_details) }} {{ $second_currency->code }})</span></button>


                                            @if ($transaction->second_currency != Session::get('business.currency_id'))
                                                <button type="submit" class="flexpay-button-el"
                                                    style="visibility: visible;"><span
                                                        style="display: block; min-height: 30px;"
                                                        onclick="currency_code='{{ $business_details->currency_code }}';currency_rate=1;trigger_range()">Mobile
                                                        Money ({{$total_payable}} {{ $business_details->currency_code }})</span></button>
                                            @endif
                                        @else
                                            <button type="submit" class="flexpay-button-el"
                                                style="visibility: visible;"><span style="display: block; min-height: 30px;"
                                                    onclick="currency_code='{{ $business_details->currency_code }}';currency_rate=1">Mobile
                                                    Money ({{$total_payable}} {{ $business_details->currency_code }})</span></button>
                                        @endif
                                    </form>
                                </div>
                            @endif
                        @else
                            <table class="table no-border">
                                <tr>
                                    <td>
                                        <h4>@lang('sale.payment_status'): <span class="text-success">@lang('lang_v1.paid')</span></h4>
                                    </td>
                                </tr>
                            </table>
                        @endif
                        <div class="spacer"></div>

                        <div class="" style="padding-top: 50px">
                            <hr>
                            @if ($transaction->payment_status != 'paid')
                                <a class="add_payment_modal"
                                    href="{{ action([\App\Http\Controllers\TransactionPaymentController::class, 'addPayment'], [$transaction->id]) }}">
                                    <button type="button" class="btn btn-success btn-flat no-print pos-express-finalize"
                                        data-pay_method="suspend" title="Suspendre les ventes (pause)">
                                        <i class="fas fa-pause" aria-hidden="true"></i>
                                        Ajouter Un Paiement Manuel </button></a>
                            @endif

                            {{--
                            @can('sell.delete')
                                <a href="{{ action([\App\Http\Controllers\SellPosController::class, 'destroy'], [$transaction->id]) }}"
                                    class="delete-sale">
                                    <button type="button" class="btn bg-red btn-default btn-flat no-print pos-express-finalize"
                                        data-pay_method="suspend" title="{{ __('lang_v1.click_to_delete') }}">
                                        <i class="fa fa-trash"></i>
                                        Supprimer l'opération </button></a>
                            @endcan
                            --}}


<a class="" href="{{ action([\App\Http\Controllers\SellPosController::class, 'create']) }}">
    <button type="button" class="btn bg-azerty btn-default btn-flat no-print pos-express-finalize"
        data-pay_method="suspend" title="Suspendre les ventes (pause)">
        <i class="fas fa-pause" aria-hidden="true"></i>
        Retour </button></a>
</div>
</div>
</div>
</div>

</div>
</div>

<div class="modal fade" id="custom_amount_modificator_modal" tabindex="-1" role="dialog" aria-labelledby="modalTitle"
    style="z-index: 789879897">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">×</span></button>
                <h4 class="modal-title" id="myModalLabel">Options avancées du paiement</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="form-group col-xs-12 col-sm-12 ">
                        <label>Total prêt à payer</label>
                        <div class="range-wrap" style="width: 100%;">
                            <input type="range" class="range" min="1" max="100" step="1"
                                value="100">
                            <output class="bubble"></output>
                        </div>
                    </div>
                    <div class="form-group col-xs-12">
                        <label>La description</label>
                        <textarea class="form-control" name="custom_description" rows="3" maxlength="90"></textarea>
                        <p class="help-block">Ajouter une note de paiement.</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>
@endsection





<!-- /.content -->
<div class="modal fade payment_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
</div>

<div class="modal fade edit_payment_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
</div>

<!-- This will be printed -->
<!-- <section class="invoice print_section" id="receipt_section">
</section> -->


@section('javascript')


<script type="text/javascript">
    var mobilemoney_phone = "{{ app('request')->input('mobilemoney_phone') }}";
    var code = "{{ app('request')->input('code') }}";
    if (code !== undefined || code == null) {
        code = "{{ $business_details->currency_code }}";
    }




    $(document).ready(function() {
        $('button').removeAttr("disabled"); // à ajouter partout

        //alert($('#submit_mobilemoney_'+code).trigger('click'));
        if (code !== undefined || code == null) {
            $('#submit_mobilemoney_' + code).trigger('click');
            $('.swal2-confirm').trigger('click');
        }






        //Date range as a button
        $('#sell_list_filter_date_range').daterangepicker(
            dateRangeSettings,
            function(start, end) {
                $('#sell_list_filter_date_range').val(start.format(moment_date_format) + ' ~ ' + end.format(
                    moment_date_format));
                sell_table.ajax.reload();
            }
        );
        $('#sell_list_filter_date_range').on('cancel.daterangepicker', function(ev, picker) {
            $('#sell_list_filter_date_range').val('');
            sell_table.ajax.reload();
        });

        sell_table = $('#sell_table').DataTable({
            processing: true,
            serverSide: true,
            aaSorting: [
                [1, 'desc']
            ],
            "ajax": {
                "url": "/sells",
                "data": function(d) {
                    if ($('#sell_list_filter_date_range').val()) {
                        var start = $('#sell_list_filter_date_range').data('daterangepicker')
                            .startDate.format('YYYY-MM-DD');
                        var end = $('#sell_list_filter_date_range').data('daterangepicker').endDate
                            .format('YYYY-MM-DD');
                        d.start_date = start;
                        d.end_date = end;
                    }
                    d.is_direct_sale = 1;

                    d.location_id = $('#sell_list_filter_location_id').val();
                    d.customer_id = $('#sell_list_filter_customer_id').val();
                    d.payment_status = $('#sell_list_filter_payment_status').val();
                    d.created_by = $('#created_by').val();
                    d.sales_cmsn_agnt = $('#sales_cmsn_agnt').val();
                    d.service_staffs = $('#service_staffs').val();

                    if ($('#shipping_status').length) {
                        d.shipping_status = $('#shipping_status').val();
                    }

                    if ($('#sell_list_filter_source').length) {
                        d.source = $('#sell_list_filter_source').val();
                    }

                    if ($('#only_subscriptions').is(':checked')) {
                        d.only_subscriptions = 1;
                    }

                    d = __datatable_ajax_callback(d);
                }
            },
            scrollY: "75vh",
            scrollX: true,
            scrollCollapse: true,
            columns: [{
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    "searchable": false
                },
                {
                    data: 'transaction_date',
                    name: 'transaction_date'
                },
                {
                    data: 'invoice_no',
                    name: 'invoice_no'
                },
                {
                    data: 'conatct_name',
                    name: 'conatct_name'
                },
                {
                    data: 'mobile',
                    name: 'contacts.mobile'
                },
                {
                    data: 'business_location',
                    name: 'bl.name'
                },
                {
                    data: 'payment_status',
                    name: 'payment_status'
                },
                {
                    data: 'payment_methods',
                    orderable: false,
                    "searchable": false
                },
                {
                    data: 'final_total',
                    name: 'final_total'
                },
                {
                    data: 'total_paid',
                    name: 'total_paid',
                    "searchable": false
                },
                {
                    data: 'total_remaining',
                    name: 'total_remaining'
                },
                {
                    data: 'return_due',
                    orderable: false,
                    "searchable": false
                },
                {
                    data: 'shipping_status',
                    name: 'shipping_status'
                },
                {
                    data: 'total_items',
                    name: 'total_items',
                    "searchable": false
                },
                {
                    data: 'types_of_service_name',
                    name: 'tos.name',
                    @if (empty($is_types_service_enabled))
                        visible: false
                    @endif
                },
                {
                    data: 'service_custom_field_1',
                    name: 'service_custom_field_1',
                    @if (empty($is_types_service_enabled))
                        visible: false
                    @endif
                },
                {
                    data: 'custom_field_1',
                    name: 'transactions.custom_field_1',
                    @if (empty($custom_labels['sell']['custom_field_1']))
                        visible: false
                    @endif
                },
                {
                    data: 'custom_field_2',
                    name: 'transactions.custom_field_2',
                    @if (empty($custom_labels['sell']['custom_field_2']))
                        visible: false
                    @endif
                },
                {
                    data: 'custom_field_3',
                    name: 'transactions.custom_field_3',
                    @if (empty($custom_labels['sell']['custom_field_3']))
                        visible: false
                    @endif
                },
                {
                    data: 'custom_field_4',
                    name: 'transactions.custom_field_4',
                    @if (empty($custom_labels['sell']['custom_field_4']))
                        visible: false
                    @endif
                },
                {
                    data: 'added_by',
                    name: 'u.first_name'
                },
                {
                    data: 'additional_notes',
                    name: 'additional_notes'
                },
                {
                    data: 'staff_note',
                    name: 'staff_note'
                },
                {
                    data: 'shipping_details',
                    name: 'shipping_details'
                },
                {
                    data: 'table_name',
                    name: 'tables.name',
                    @if (empty($is_tables_enabled))
                        visible: false
                    @endif
                },
                {
                    data: 'waiter',
                    name: 'ss.first_name',
                    @if (empty($is_service_staff_enabled))
                        visible: false
                    @endif
                },
            ],
            "fnDrawCallback": function(oSettings) {
                __currency_convert_recursively($('#sell_table'));
            },
            "footerCallback": function(row, data, start, end, display) {
                var footer_sale_total = 0;
                var footer_total_paid = 0;
                var footer_total_remaining = 0;
                var footer_total_sell_return_due = 0;
                for (var r in data) {
                    footer_sale_total += $(data[r].final_total).data('orig-value') ? parseFloat($(
                        data[r].final_total).data('orig-value')) : 0;
                    footer_total_paid += $(data[r].total_paid).data('orig-value') ? parseFloat($(
                        data[r].total_paid).data('orig-value')) : 0;
                    footer_total_remaining += $(data[r].total_remaining).data('orig-value') ?
                        parseFloat($(data[r].total_remaining).data('orig-value')) : 0;
                    footer_total_sell_return_due += $(data[r].return_due).find('.sell_return_due')
                        .data('orig-value') ? parseFloat($(data[r].return_due).find(
                            '.sell_return_due').data('orig-value')) : 0;
                }

                $('.footer_total_sell_return_due').html(__currency_trans_from_en(
                    footer_total_sell_return_due));
                $('.footer_total_remaining').html(__currency_trans_from_en(footer_total_remaining));
                $('.footer_total_paid').html(__currency_trans_from_en(footer_total_paid));
                $('.footer_sale_total').html(__currency_trans_from_en(footer_sale_total));

                $('.footer_payment_status_count').html(__count_status(data, 'payment_status'));
                $('.service_type_count').html(__count_status(data, 'types_of_service_name'));
                $('.payment_method_count').html(__count_status(data, 'payment_methods'));
            },
            createdRow: function(row, data, dataIndex) {
                $(row).find('td:eq(6)').attr('class', 'clickable_td');
            }
        });

        $(document).on('change',
            '#sell_list_filter_location_id, #sell_list_filter_customer_id, #sell_list_filter_payment_status, #created_by, #sales_cmsn_agnt, #service_staffs, #shipping_status, #sell_list_filter_source',
            function() {
                sell_table.ajax.reload();
            });

        $('#only_subscriptions').on('ifChanged', function(event) {
            sell_table.ajax.reload();
        });
    });
</script>
<script src="{{ asset('js/payment.js?v=' . $asset_v) }}"></script>
@endsection


@section('mycustom_js')

<script>
    var currency_code = '{{ $business_details->currency_code }}';
    var currency_rate = 1;
    var currency_id = {{ $business_details->currency_id }};

    var total_payable = Number({{ $total_payable }});
    var custom_amount = total_payable;
    //alert(total_payable_formatted);


    range_value = 100;
    var custom_amount_info = '';
    const allRanges = document.querySelectorAll(".range-wrap");
    allRanges.forEach(wrap => {
        const range = wrap.querySelector(".range");
        const bubble = wrap.querySelector(".bubble");

        range.addEventListener("input", () => {
            console.log(range.value);
            setBubble(range, bubble);
        });
        setBubble(range, bubble);
    });


    function trigger_range() {

        var range = document.getElementsByClassName("range");
        const bubble = document.getElementsByClassName("bubble");

        range.value = range_value;
        custom_amount_info = (currency_rate * total_payable * range_value / 100).toFixed(
            {{ session('business.currency_precision', 2) }}) + ` ${currency_code}`;


        const val = range.value ?? 100;
        $(bubble).html((currency_rate * total_payable * val / 100).toFixed(
            {{ session('business.currency_precision', 2) }}) + ` ${currency_code}`);


    }

    function setBubble(range, bubble) {
        const val = range.value;
        const min = range.min ? range.min : 0;
        const max = range.max ? range.max : 100;
        const newVal = Number(((val - min) * 100) / (max - min));
        var text = (currency_rate * total_payable * val / 100).toFixed(
            {{ session('business.currency_precision', 2) }}) + ` ${currency_code}`;
        bubble.innerHTML = text;
        custom_amount = val;

        $('#custom_amount_info').html(text);

        // Sorta magic numbers based on size of the native UI thumb
        bubble.style.left = `calc(${newVal}% + (${8 - newVal * 0.15}px))`;
        range_value = val;
    }
</script>

<script>
    currency_code = '';
    i = 1;
    $("#flexpay_form").submit(function(e) {
        if (i == 0) {
            //$(this).find('[type="submit"]').click();
            return;
        }
        //alert(currency_code);
        //return 222;
        e.preventDefault();
        inputValue = "";
        Swal.fire({
            allowOutsideClick: false,
            title: '<strong>PAIEMENT <u>MOBILE MONEY</u></strong><br><span id="custom_amount_info">' +
                custom_amount_info + '</span>',
            html: '<img src="{{ asset('uploads/custom/mobilemoney/africell.png') }}" width="75" />' +
                '<img src="{{ asset('uploads/custom/mobilemoney/airtel.png') }}" width="75" />' +
                '<img src="{{ asset('uploads/custom/mobilemoney/orange.png') }}" width="75" />' +
                '<img src="{{ asset('uploads/custom/mobilemoney/vodacom.png') }}" width="75" />' +
                    '<br><br><span class="text-link text-info cursor-pointer" data-toggle="modal" data-target="#custom_amount_modificator_modal">Plus d\'options <i class="fa fa-info-circle"></i></span>',
            input: 'number',
            inputLabel: 'Entrez le numéro Mobile money au format (243800000000)',
            inputValue: mobilemoney_phone,
            inputPlaceholder: "243812558314",
            inputAttributes: {
                maxlength: 12
            },
            //inputRequired: true,
            showCancelButton: true,
            inputValidator: (value) => {

                if (!value) {
                    return 'Vous devez inserrer le numéro de téléphone!'
                    e.preventDefault();
                }

                $(this).find('[name="phone"]').val(value);
                custom_data_string =
                    "custom_amount_modificator_type=percentage&custom_description=" + $(
                        '[name="custom_description"]').val() + "&custom_amount=" + custom_amount;


                let timerInterval2
                Swal.fire({
                    allowOutsideClick: false,
                    title: 'En attente du serveur...',
                    timer: 30000,
                    timerProgressBar: true,
                    didOpen: () => {
                        Swal.showLoading()
                        const b = Swal.getHtmlContainer().querySelector('b')
                        timerInterval2 = setInterval(() => {
                            //b.textContent = Swal.getTimerLeft()
                        }, 100)
                    },
                    willClose: () => {
                        clearInterval(timerInterval2)
                        Swal.fire(
                            'Oops',
                            'Délai dépassé',
                            'error'
                        );
                    }
                }).then((result) => {})



                $.ajax({
                    url: '{{ route('trigger_payment', ['id' => $transaction->id]) }}?currency_code=' +
                        currency_code + '&currency_id=' + currency_id +
                        "&" + custom_data_string,
                    type: "POST",
                    data: $(this).serialize(),
                    //contentType: "application/json",
                    //dataType: "json",
                    //container: '.modal-content',
                    //messagePosition: "inline",
                    //disableButton: true,
                    //buttonSelector: "#mobilemoney-button",
                    success: function(response, status, xhr) {
                        //alert('ok');
                        console.log(xhr.status);
                        console.log("status", status);
                        console.log('aaa', response);
                        //alert(response.data.message);

                        //return ;
                        if (response.data.code == 0) {





                            let timerInterval
                            Swal.fire({
                                allowOutsideClick: false,
                                title: 'Paiement initié',
                                html: response.data.message +
                                    '<br><img src="{{ asset('uploads/custom/mobilemoney/load.gif') }}" width="150" />',
                                timer: 150000,
                                timerProgressBar: true,
                                didOpen: () => {
                                    Swal.showLoading()
                                    const b = Swal.getHtmlContainer()
                                        .querySelector('b')
                                    timerInterval = setInterval(() => {
                                        //b.textContent = Swal.getTimerLeft()
                                    }, 100)
                                },
                                willClose: () => {
                                    clearInterval(timerInterval);
                                    Swal.fire(
                                        'Oops',
                                        'session expirée',
                                        'error'
                                    );
                                }
                            }).then((result) => {})



                            setInterval(checkMobileMoneyWebhook(response.data
                                .orderNumber), 2000);
                        } else {




                            if (response.data.msg) {
                                Swal.fire(
                                    'Oops',
                                    response.data.msg,
                                    'error'
                                ).then(
                                    function() {
                                        window.location = window.location.pathname;
                                    });
                                return false;
                            } else if (response.data) {
                                Swal.fire(
                                    'Oops',
                                    response.data,
                                    'error'
                                ).then(
                                    function() {
                                        window.location = window.location.pathname;
                                    });
                                return false;
                            }
                            sweetAlert("Oops!",
                                "Une erreur nconnue est survenue , nous vous recommandons de réessayer dans quelques minutes",
                                "error");
                            return false;
                        }
                    }
                }).catch(
                    error => {
                        console.log(error.responseJSON);
                        Swal.fire(
                            'Oops',
                            "L'opération n'a pas abouti, veuillez verifier votre connexion, ou surtout le numéro mobile money du client si c'est bien mentionné",
                            'error'
                        ).then(
                            function() {
                                window.location = window.location.pathname;
                            });


                    }
                );



                i = 0;
                //$(this).submit();
                //obj.preventDefault();
            }
        })
    });

    function checkMobileMoneyWebhook(ordernumber) {
        //alert(1111111);
        //return;
        //$(this).find('[name="phone"]').val(value);
        $.ajax({
            url: "{{ route('trigger_payment', ['id' => $transaction->id]) }}",
            type: "POST",
            data: {
                ordernumber: ordernumber,
                'gateway': 'checkflexpay',
                'phone': $('#flexpay_form').find('[name="phone"]').val(),
                '_token': '{{ csrf_token() }}'
            },
            container: '.modal-content',
            success: function(response) {
                console.log(response);
                try {
                    if (response == "0") {
                        return;
                    } //on fait rien;
                    /*
                    if (response.status == 'success' && response.webhook) {

                    }
                    */


                    if (response.data.code == "0") {
                        if (response.data.transaction) {
                            if (response.data.transaction.status == 0) {

                                Swal.fire('Reussi!', response.msg, 'success').then(
                                    function() {
                                        window.location = window.location.pathname;
                                    });
                                return;
                            } else if (response.data.transaction.status == 2) {
                                //on fait rien le paiement est en attente
                            } else {

                                try {
                                    Swal.fire('Oops!', response.msg, 'error').then(
                                        function() {
                                            window.location = window.location.pathname;;
                                        });
                                } catch (error) {
                                    Swal.fire('Oops!', 'Désolé le paiement n\'a pas abouti', 'error').then(
                                        function() {
                                            window.location = window.location.pathname;;
                                        });
                                }

                                return;
                            }
                        }
                        //return;
                    }

                    if (response.data.status == "404") {
                        //on fait rien
                        //swal("Oops...", "Opération non reconnu", "error");
                        //return;
                    }
                    checkMobileMoneyWebhook(ordernumber);
                } catch (error) {
                    checkMobileMoneyWebhook(ordernumber);
                }
            }

        }).catch(
            error => {
                if (typeof myVariable === "undefined") {
                    checkMobileMoneyWebhook(ordernumber);
                    return;
                }

                console.log(error.responseJSON);
                response = error.responseJSON;
                if (response.data.transaction.status == 0) {
                    //reussi , il sera geré au niveau de la fonction success d'ajax
                    return;
                } else if (response.data.transaction.status == 2) {
                    //on fait rien le paiement est en attente
                    checkMobileMoneyWebhook(ordernumber);
                    return;
                } else {
                    Swal.fire('Oops!', error.responseJSON.msg, 'error').then(
                        function() {
                            window.location = window.location.pathname;;
                        });
                    return;
                }



            }
        );
    }
</script>
@endsection

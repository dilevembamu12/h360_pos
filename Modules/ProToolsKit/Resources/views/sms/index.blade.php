@php
    use App\Business;
    use App\System;

    $system_currency = System::getCurrency();
    $code = strtolower($system_currency->code);

    //dd(request()->session()->get('sms'));
@endphp
@php
    //dd(request()->session()->get('sms')->gateway_credentials->sms_gateways->sender_id);
    //      dd($userdashboard);
@endphp
@extends('layouts.app')
@section('title', __('protoolskit::ptk.sms'))

@section('content')
    @include('protoolskit::layouts.nav')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>@lang('protoolskit::ptk.sms')</h1>
    </section>

    <!-- Main content -->
    <section class="content">
        @component('components.widget', ['class' => 'box-solid'])
            @can('protoolskit.add_sms')
            @endcan

            <div class="col-md-6 col-sm-6 col-xs-12 col-custom">

                <div class="col-md-12">
                    <div class="box box-success hvr-grow-shadow">

                        <div class="box-body text-center">
                            <i class="fas fa-tags font-30"></i>
                            <h3 class="text-center">
                                "<strong>{{ request()->session()->get('sms')->gateway_credentials->sms_gateways->sender_id }}</strong>"
                            </h3>
                            <p class="text-center">L'ID d'ENVOI ne peut contenir des termes ci-après:<br> {{ENV('SMS_SPAM_WORDS')}}</p>
                            <form
                                action="{{ action([\Modules\ProToolsKit\Http\Controllers\SMSController::class, 'updateSmsSenderId']) }}"
                                method="POST" id="create_form">
                                {{ csrf_field() }}
                                <div class="col-md-4">
                                    {!! Form::text('sender_id', request()->session()->get('sms')->gateway_credentials->sms_gateways->sender_id, [
                                        'class' => 'form-control',
                                        'placeholder' => __('aiassistance::lang.brandproduct_name'),
                                        'required',
                                        'autofocus',
                                    ]) !!}
                                </div>
                                <div class="col-md-3">
                                    <button type="submit" class="btn btn-success pull-right ladda-button"
                                        id="submit_btn">Changer la denomination</button>

                                </div>

                            </form>
                            <div class="col-md-5">
                                <a href="{{action([\Modules\ProToolsKit\Http\Controllers\SMSController::class, 'buycredit'])}}"
                                    class="btn btn-success">Payer plus de credit SMS</a>
                            </div>
                        </div>

                    </div>

                </div>


                <div class="col-md-12 col-sm-12 col-xs-12 col-custom">
                    <div class="info-box info-box-new-style">
                        <span class="info-box-icon bg-aqua"><i class="ion ion-ios-cart-outline"></i></span>

                        <div class="info-box-content">
                            <span class="info-box-text">**Crédit SMS</span>
                            <span class="info-box-number ">{{ $userdashboard['user']->credit }}</span>
                        </div>
                        <!-- /.info-box-content -->
                    </div>
                    <!-- /.info-box -->
                </div>

                <!-- /.col -->
                <div class="col-md-6 col-sm-6 col-xs-12 col-custom">
                    <div class="info-box info-box-new-style">
                        <span class="info-box-icon bg-aqua"><i class="ion ion-ios-cart-outline"></i></span>

                        <div class="info-box-content">
                            <span class="info-box-text">**SMS (Non utilisés / Utilisés)</span>
                            <span
                                class="info-box-number ">{{ $userdashboard['user']->credit }}/{{ $userdashboard['logs']->sms->all }}</span>
                        </div>
                        <!-- /.info-box-content -->
                    </div>
                    <!-- /.info-box -->
                </div>
                <div class="col-md-6 col-sm-6 col-xs-12 col-custom">
                    <div class="info-box info-box-new-style">
                        <span class="info-box-icon bg-green">
                            <i class="ion ion-ios-paper-outline"></i>

                        </span>

                        <div class="info-box-content">
                            <span class="info-box-text">**SMS Reussis</span>
                            <span class="info-box-number ">{{ $userdashboard['logs']->sms->success }}</span>
                        </div>
                        <!-- /.info-box-content -->
                    </div>
                    <!-- /.info-box -->
                </div>
                <div class="col-md-6 col-sm-6 col-xs-12 col-custom">
                    <div class="info-box info-box-new-style">
                        <span class="info-box-icon bg-yellow">
                            <i class="ion ion-ios-paper-outline"></i>
                            <i class="fa fa-exclamation"></i>
                        </span>

                        <div class="info-box-content">
                            <span class="info-box-text">**SMS en attente</span>
                            <span class="info-box-number ">{{ $userdashboard['logs']->sms->pending }}</span>
                        </div>
                        <!-- /.info-box-content -->
                    </div>
                    <!-- /.info-box -->
                </div>

                <div class="col-md-6 col-sm-6 col-xs-12 col-custom">
                    <div class="info-box info-box-new-style">
                        <span class="info-box-icon bg-red text-white">
                            <i class="fas fa-exchange-alt"></i>
                        </span>

                        <div class="info-box-content">
                            <span class="info-box-text">**SMS Echoués</span>
                            <span class="info-box-number ">{{ $userdashboard['logs']->sms->failed }}</span>
                        </div>
                    </div>
                    <!-- /.info-box -->
                </div>
                <!-- /.col -->
            </div>
            <div class="col-md-6 col-sm-6 col-xs-12 col-custom">
                {!! Form::open([
                    'url' => action([\Modules\ProToolsKit\Http\Controllers\SMSController::class, 'manualSendSms']),
                    'method' => 'post',
                    'id' => 'campaign_form',
                ]) !!}

                <div class="col-md-12">
                    <div class="box box-success box-solid">
                        <div class="box-header with-border">
                            <div class="box-tools pull-right">
                                <i class=""></i>
                            </div>
                            <p>ENVOYER DES SMS PROFESSIONNELS</p>
                        </div>

                        <div class="box-body">
                            <div class="row" style="display: none">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        {!! Form::label('campaign_type', __('crm::lang.campaign_type') . ':*') !!}
                                        {!! Form::select('campaign_type', ['sms' => __('crm::lang.sms')], 'sms', [
                                            'class' => 'form-control select2',
                                            'placeholder' => __('messages.please_select'),
                                            '',
                                            'style' => 'display: none;',
                                        ]) !!}
                                    </div>
                                </div>
                            </div>
                            @if (!empty($contact_ids))
                                @php
                                    $default_value = explode(',', $contact_ids);
                                    $to = 'contact';
                                @endphp
                            @else
                                @php
                                    $default_value = null;
                                    $to = null;
                                @endphp
                            @endif
                            <div class="row">
                                <div class="col-md-4"  style="display: none;">
                                    <div class="form-group">
                                        {!! Form::label('to', __('crm::lang.to') . ':*') !!}
                                        {!! Form::select(
                                            'to',
                                            [
                                                'customer' => __('lang_v1.customers'),
                                                'lead' => __('crm::lang.leads'),
                                                'transaction_activity' => __('crm::lang.transaction_activity'),
                                                'contact' => __('contact.contact'),
                                            ],
                                            $to,
                                            [
                                                'class' => 'form-control select2',
                                                'placeholder' => __('messages.please_select'),
                                                '',
                                                'style' => 'width: 100%;',
                                            ],
                                        ) !!}
                                    </div>
                                </div>
                                <div class="col-md-8 customer_div" style="display: none;">
                                    <div class="form-group">
                                        {!! Form::label('contact_id', __('lang_v1.customers') . ':*') !!}
                                        <button type="button" class="btn btn-primary btn-xs select-all">
                                            @lang('lang_v1.select_all')
                                        </button>
                                        <button type="button" class="btn btn-primary btn-xs deselect-all">
                                            @lang('lang_v1.deselect_all')
                                        </button>
                                        {!! Form::select('contact_id[]', $customers, null, [
                                            'class' => 'form-control select2',
                                            'multiple',
                                            'id' => 'contact_id',
                                            'style' => 'width: 100%;',
                                        ]) !!}
                                    </div>
                                </div>
                                <div class="col-md-8 lead_div" style="display: none;">
                                    <div class="form-group">
                                        {!! Form::label('lead_id', __('crm::lang.leads') . ':*') !!}
                                        <button type="button" class="btn btn-primary btn-xs select-all">
                                            @lang('lang_v1.select_all')
                                        </button>
                                        <button type="button" class="btn btn-primary btn-xs deselect-all">
                                            @lang('lang_v1.deselect_all')
                                        </button>
                                        {!! Form::select('lead_id[]', $leads, null, [
                                            'class' => 'form-control select2',
                                            'multiple',
                                            'id' => 'lead_id',
                                            'style' => 'width: 100%;',
                                        ]) !!}
                                    </div>
                                </div>
                                <div class="col-md-8 contact_div" style="display: none;">
                                    <div class="form-group">
                                        {!! Form::label('contact', __('contact.contact') . ':*') !!}
                                        <button type="button" class="btn btn-primary btn-xs select-all">
                                            @lang('lang_v1.select_all')
                                        </button>
                                        <button type="button" class="btn btn-primary btn-xs deselect-all">
                                            @lang('lang_v1.deselect_all')
                                        </button>
                                        {!! Form::select('contact[]', $contacts, $default_value, [
                                            'class' => 'form-control select2',
                                            'multiple',
                                            'id' => 'contact',
                                            'style' => 'width: 100%;',
                                        ]) !!}
                                    </div>
                                </div>
                                <div class="col-md-4 transaction_activity_div" style="display: none;">
                                    <div class="form-group">
                                        {!! Form::label('trans_activity', __('crm::lang.transaction_activity') . ':*') !!}
                                        {!! Form::select(
                                            'trans_activity',
                                            [
                                                'has_transactions' => __('crm::lang.has_transactions'),
                                                'has_no_transactions' => __('crm::lang.has_no_transactions'),
                                            ],
                                            null,
                                            ['class' => 'form-control select2', 'required', 'style' => 'width: 100%;'],
                                        ) !!}
                                    </div>
                                </div>
                                <div class="col-md-4 transaction_activity_div" style="display: none;">
                                    <div class="form-group">
                                        <label for="in_days">{{ __('crm::lang.in_days') }}:*</label>
                                        <div class="input-group">
                                            <div class="input-group-addon">{{ __('crm::lang.in') }}</div>
                                            <input type="text" class="form-control input_number" id="in_days"
                                                placeholder="0" name="in_days" >
                                            <div class="input-group-addon">{{ __('lang_v1.days') }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        {!! Form::label('manual_to', 'Message A:*(+243XXXXXXX) ') !!}
                                        <span style="color: red"><br>pour les messages en masse,  veuillez séparer le numero des destinateur pas une virgule</span>
                                        {!! Form::text('manual_to', null, [
                                            'class' => 'form-control',
                                            'placeholder' => "Correspondant, commencant par l'indicateur du pays",
                                            '',
                                            'autofocus',
                                        ]) !!}
                                    </div>
                                </div>
                            </div>

                            <div class="row sms_div">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        {!! Form::label('sms_body', __('crm::lang.sms_body') . ':') !!}
                                        {!! Form::textarea('sms_body', null, [
                                            'class' => 'form-control ',
                                            'id' => 'sms_body',
                                            'rows' => '6',
                                            'required',
                                        ]) !!}
                                    </div>
                                </div>
                            </div>
                            {{-- 
                            <strong>@lang('lang_v1.available_tags'):</strong>
                            <p class="help-block">
                                {{ implode(', ', $tags) }}
                            </p>
                            --}}



                            <button type="submit" class="btn btn-warning btn-sm pull-right submit-button notif m-5"
                                name="send_notification" value="1" data-style="expand-right">
                                <span class="ladda-label">
                                    <i class="fas fa-envelope-square"></i>
                                    @lang('crm::lang.send_notification')
                                </span>
                            </button>
                        </div>
                    </div>
                </div>

                {!! Form::close() !!}
            </div>
        @endcomponent

        @component('components.widget', ['class' => 'box-solid'])
            @can('protoolskit.add_sms')
            @endcan

            <div class="row">
                <!-- /.col -->
                <div class="col-md-6 col-sm-6 col-xs-12 col-custom">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="superadmin_sms_user_table">
                            <thead>
                                <tr>
                                <tr>
                                    <th>**Date</th>
                                    <th>**Trx Number</th>
                                    <th>**Montant</th>
                                    <th>**Detail</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($userdashboard['transactions'] as $creditdata)
                                    <tr class="@if ($loop->even)  @endif">
                                        <td data-label="**Date">
                                            <span>{{ \Carbon\Carbon::parse(strtotime($creditdata->created_at))->diffForHumans() }}</span><br>
                                            {{ \Carbon\Carbon::parse(strtotime($creditdata->created_at)) }}
                                        </td>

                                        <td data-label="**Trx Number">
                                            {{ $creditdata->transaction_number }}
                                        </td>

                                        <td data-label="**Credit">
                                            <span>
                                            {{ number_format($creditdata->amount, session('business.currency_precision', 2), session('currency')['decimal_separator'], session('currency')['thousand_separator']) }}$
                                            </span>
                                            
                                        </td>

                                        <td data-label="">
                                            {{ $creditdata->details }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-muted text-center" colspan="100%">**Aucune donnée trouvée</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- /.col -->
                <!-- /.col -->
                <div class="col-md-6 col-sm-6 col-xs-12 col-custom">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="superadmin_sms_user_table">
                            <thead>
                                <tr>
                                <tr>
                                    <th>**Date</th>
                                    <th>**Trx Number</th>
                                    <th>**Credit</th>
                                    <th>**Credit restant</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($userdashboard['credits'] as $creditdata)
                                    <tr class="@if ($loop->even)  @endif">
                                        <td data-label="**Date">
                                            <span>{{ \Carbon\Carbon::parse(strtotime($creditdata->created_at))->diffForHumans() }}</span><br>
                                            {{ $creditdata->created_at }}
                                        </td>

                                        <td data-label="**Trx Number">
                                            {{ $creditdata->trx_number }}
                                        </td>

                                        <td data-label="**Credit">
                                            <span
                                                class="@if ($creditdata->credit_type == '+') text--success @else text--danger @endif">{{ $creditdata->credit_type }}
                                                {{ $creditdata->credit }}
                                            </span>**Credit
                                        </td>

                                        <td data-label="**Credit restant">
                                            {{ $creditdata->post_credit }} **Credit
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-muted text-center" colspan="100%">**Aucune donnée trouvée</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- /.col -->
            </div>
        @endcomponent
    </section>
    <!-- /.content -->
    <div class="modal fade" id="sms_modal" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>
@stop
@section('javascript')
    <script src="{{ asset('modules/crm/js/crm.js?v=' . $asset_v) }}"></script>
    <script type="text/javascript">
        $(function() {

            $('select#to').change(function() {
                toggleFieldBasedOnTo($(this).val());
            });

            function toggleFieldBasedOnTo(to) {
                if (to == 'customer') {
                    $('div.customer_div').show();
                    $('div.lead_div').hide();
                    $('div.transaction_activity_div').hide();
                    $('div.contact_div').hide();
                } else if (to == 'lead') {
                    $('div.lead_div').show();
                    $('div.customer_div').hide();
                    $('div.transaction_activity_div').hide();
                    $('div.contact_div').hide();
                } else if (to == 'transaction_activity') {
                    $('div.transaction_activity_div').show();
                    $('div.customer_div').hide();
                    $('div.lead_div').hide();
                    $('div.contact_div').hide();
                } else if (to == 'contact') {
                    $('div.contact_div').show();
                    $('div.transaction_activity_div').hide();
                    $('div.customer_div').hide();
                    $('div.lead_div').hide();
                } else {
                    $('div.transaction_activity_div, div.customer_div, div.lead_div, div.contact_div').hide();
                }
            }

            toggleFieldBasedOnTo($('select#to').val());
        });
    </script>
@endsection

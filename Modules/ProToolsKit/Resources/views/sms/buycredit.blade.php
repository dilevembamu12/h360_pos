@php
    use App\Business;
    use App\System;

    $system_currency = System::getCurrency();
    $code = strtolower($system_currency->code);
@endphp
@php
    //dd($data);
    $plans = $data['plans'];
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

            <div class="row">
                <!-- /.col -->

                @foreach ($plans as $item)
                    <div class="col-md-4">
                        <div class="box box-success hvr-grow-shadow">
                            <div class="box-header with-border text-center">
                                <h2 class="box-title">
                                    {{ $item->name }}
                                </h2>
                                @if ($item->recommended_status == 1)
                                    <div class="pull-right">
                                        <span class="badge bg-green">
                                            @lang('superadmin::lang.popular')
                                        </span>
                                    </div>
                                @endif
                            </div>

                            <!-- /.box-header -->
                            <div class="box-body text-center">


                                @if (!empty($item->sms->is_allowed))
                                    <i class="fa fa-check text-success"></i>
                                    {{ $item->sms->credits }} SMS Credit
                                    <br /><br />
                                @endif


                                @if (!empty($item->whatsapp->is_allowed))
                                    <i class="fa fa-check text-success"></i>
                                    {{ $item->whatsapp->credits }} Whatsapp Credit
                                    <br /><br />
                                @endif


                                @if (!empty($item->email->is_allowed))
                                    <i class="fa fa-check text-success"></i>
                                    {{ $item->email->credits }} Email Credit
                                    <br /><br />
                                @endif
                                <hr>
                                <i class="fa fa-check text-success"></i>
                                1 Credit for 160 plain word<br /><br />
                                <i class="fa fa-check text-success"></i>
                                1 Credit for 70 unicode word<br /><br />
                                <i class="fa fa-check text-success"></i>
                                1 Credit for 320 word<br /><br />
                                <i class="fa fa-check text-success"></i>
                                1 Credit for per Email<br /><br />



                                <h3 class="text-center">
                                    {{ number_format($item->amount, session('business.currency_precision', 2), session('currency')['decimal_separator'], session('currency')['thousand_separator']) }}$
                                    / {{ $item->duration }}Jours
                                </h3>
                            </div>
                            <!-- /.box-body -->

                            <div class="box-footer bg-gray disabled text-center">
                                <a href="{{ action([\Modules\ProToolsKit\Http\Controllers\SMSController::class, 'paycredit'],[$item->id]) }}"
                                    class="btn btn-block btn-success">
                                    @lang('superadmin::lang.pay_and_subscribe')
                                </a>
                                {{ $item->description }}
                            </div>
                        </div>
                        <!-- /.box -->
                    </div>
                @endforeach
                {{--
                @if ($count % 3 == 0)
                    <div class="clearfix"></div>
                @endif
                --}}


                <!-- /.col -->

            </div>
        @endcomponent
    </section>
    <!-- /.content -->

@stop

@section('javascript')

    <script type="text/javascript">
        $(document).ready(function() {
            protoolskit_sms_history_table = $('#protoolskit_sms_history_table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ action([\Modules\ProToolsKit\Http\Controllers\SMSController::class, 'history']) }}",
                    data: function(d) {},
                },
                aaSorting: [
                    [0, 'desc']
                ],
                columns: [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'to',
                        name: 'to'
                    },
                    {
                        data: 'sms_gateway.sms_gateways.sender_id',
                        name: 'sms_gateway.sms_gateways.sender_id',
                        searchable: false
                    },
                    {
                        data: 'credit',
                        name: 'credit'
                    },
                    {
                        data: 'updated_at',
                        name: 'updated_at'
                    },
                    {
                        data: 'initiated_time',
                        name: 'initiated_time'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'message',
                        name: 'message'
                    },
                ]
            });

            $('#package_id, #subscription_status, #is_active, #last_transaction_date, #no_transaction_since')
                .change(function() {
                    protoolskit_sms_history_table.ajax.reload();
                });
        });
        $(document).on('click', 'a.delete_business_confirmation', function(e) {
            e.preventDefault();
            swal({
                title: LANG.sure,
                text: "Once deleted, you will not be able to recover this business!",
                icon: "warning",
                buttons: true,
                dangerMode: true,
            }).then((confirmed) => {
                if (confirmed) {
                    window.location.href = $(this).attr('href');
                }
            });
        });
    </script>

@endsection

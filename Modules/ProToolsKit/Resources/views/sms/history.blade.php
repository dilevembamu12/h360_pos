@php
    //    dd($userdashboard);
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
                <div class="col-md-12 col-sm-12 col-xs-12 col-custom">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="protoolskit_sms_history_table">
                            <thead>
                                <tr>
                                <tr>
                                    <th>#</th>
                                    <th>**A</th>
                                    <th>**Pro name</th>
                                    <th>**Credit</th>
                                    <th>**Initié le</th>
                                    <th>**Planifié pour</th>
                                    <th>**Statut</th>
                                    <th>**Message</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
                <!-- /.col -->

            </div>
        @endcomponent
    </section>
    <!-- /.content -->

@stop

@section('javascript')

<script type="text/javascript">
    $(document).ready( function(){
        protoolskit_sms_history_table = $('#protoolskit_sms_history_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{action([\Modules\ProToolsKit\Http\Controllers\SMSController::class, 'history'])}}",
                data: function(d) { },
            },
            aaSorting: [[0, 'desc']],
            columns: [
                { data: 'id', name: 'id' },
                { data: 'to', name: 'to' },
                { data: 'sms_gateway.sms_gateways.sender_id', name: 'sms_gateway.sms_gateways.sender_id', searchable: false},
                { data: 'credit', name: 'credit' },
                { data: 'updated_at', name: 'updated_at' },
                { data: 'initiated_time', name: 'initiated_time' },
                { data: 'status', name: 'status' },
                { data: 'message', name: 'message'},
            ]
        });

        $('#package_id, #subscription_status, #is_active, #last_transaction_date, #no_transaction_since').change( function(){
            protoolskit_sms_history_table.ajax.reload();
        });
    });
    $(document).on('click', 'a.delete_business_confirmation', function(e){
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

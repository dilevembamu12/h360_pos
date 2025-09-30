@php
	//dd($sms_users);
@endphp

@extends('layouts.app')
@section('title', __('superadmin::lang.superadmin') . ' | ' . __('superadmin::lang.packages'))

@section('content')
	@include('superadmin::layouts.sms_nav')
	<section class="content-header">
		<h1>
			@lang('superadmin::lang.welcome_superadmin')
		</h1>
	</section>


	<!-- Main content -->
<section class="content">

	<div class="box box-solid">
        <div class="box-header">
            <h3 class="box-title">&nbsp;</h3>
        	<div class="box-tools">
                <a href="{{action([\Modules\Superadmin\Http\Controllers\BusinessController::class, 'create'])}}" 
                    class="btn btn-block btn-primary">
                	<i class="fa fa-plus"></i> @lang( 'messages.add' )</a>
            </div>
        </div>

        <div class="box-body">
            @can('superadmin')
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="superadmin_sms_user_table">
                        <thead>
                            <tr>
                                <th>**
                                    Business ID
                                </th>
                                <th>**Business Name</th>
                                <th>**SMS</th>
                                <th>**MAIL</th>
                                <th>**WHATSAPP</th>
                                <th>**API KEY</th>
                                <th>**SMS GATEWAY</th>
                                <th>**SENDER ID</th>
                                <th>@lang( 'superadmin::lang.action' )</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            @endcan
        </div>
    </div>

</section>
<!-- /.content -->



@endsection

@section('javascript')

<script type="text/javascript">
    $(document).ready( function(){
        superadmin_sms_user_table = $('#superadmin_sms_user_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{action([\Modules\Superadmin\Http\Controllers\SMSController::class, 'index'])}}",
                data: function(d) {
                    d.package_id = $('#package_id').val();
                    d.subscription_status = $('#subscription_status').val();
                    d.is_active = $('#is_active').val();
                    d.last_transaction_date = $('#last_transaction_date').val();
                    d.no_transaction_since = $('#no_transaction_since').val();
                },
            },
            aaSorting: [[0, 'desc']],
            columns: [
                { data: 'id', name: 'id' },
                { data: 'name', name: 'name' },
                { data: 'credit', name: 'credit', searchable: false},
                { data: 'email_credit', name: 'email_credit' },
                { data: 'whatsapp_credit', name: 'whatsapp_credit' },
				{ data: 'api_key', name: 'api_key', searchable: false },
                { data: 'sms_gateway', name: 'sms_gateway' },
                { data: 'gateway_credentials', name: 'gateway_credentials' },
                { data: 'action', name: 'action', orderable: false, searchable: false },
            ]
        });

        $('#package_id, #subscription_status, #is_active, #last_transaction_date, #no_transaction_since').change( function(){
            superadmin_sms_user_table.ajax.reload();
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
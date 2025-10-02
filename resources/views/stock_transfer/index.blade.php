{{--- custom_code 18062024 ---}}
@php
    //$business_locations=[];
    $suppliers=[];
    $orderStatuses=[];

    //dd($statuses);
@endphp
{{----------------------------}}

@extends('layouts.app')
@section('title', __('lang_v1.stock_transfers'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header no-print">
    <h1>@lang('lang_v1.stock_transfers')
    </h1>
</section>

{{--- custom_code 18062024 ---}}
<section class="content no-print">
    @component('components.filters', ['title' => __('report.filters')])
    
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('stock_transfer_list_filter_location_id_from',  __('lang_v1.location_from') . ':') !!}
                {!! Form::select('stock_transfer_list_filter_location_id_from', $business_locations, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('stock_transfer_list_filter_location_id_to',  __('lang_v1.location_to') . ':') !!}
                {!! Form::select('stock_transfer_list_filter_location_id_to', $business_locations, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
            </div>
        </div>



        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('stock_transfer_list_filter_status',  "**Statut" . ':') !!}
                {!! Form::select('stock_transfer_list_filter_status', $statuses, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('stock_transfer_list_filter_date_range', __('report.date_range') . ':') !!}
                {!! Form::text('stock_transfer_list_filter_date_range', null, ['placeholder' => __('lang_v1.select_a_date_range'), 'class' => 'form-control', 'readonly']); !!}
            </div>
        </div>

    @endcomponent
</section>
{{----------------------------}}


<!-- Main content -->
<section class="content no-print">
    @component('components.widget', ['class' => 'box-primary', 'title' => __('lang_v1.all_stock_transfers')])
        @slot('tool')
            <div class="box-tools">
                <a class="btn btn-block btn-primary" href="{{action([\App\Http\Controllers\StockTransferController::class, 'create'])}}">
                <i class="fa fa-plus"></i> @lang('messages.add')</a>
            </div>
        @endslot
        <div class="table-responsive">
            <table class="table table-bordered table-striped ajax_view" id="stock_transfer_table">
                <thead>
                    <tr>
                        <th>@lang('messages.date')</th>
                        <th>@lang('purchase.ref_no')</th>
                        <th>@lang('lang_v1.location_from')</th>
                        <th>@lang('lang_v1.location_to')</th>
                        <th>@lang('sale.status')</th>
                        <th>@lang('lang_v1.shipping_charges')</th>
                        <th>@lang('stock_adjustment.total_amount')</th>
                        <th>@lang('purchase.additional_notes')</th>
                        <th>@lang('messages.action')</th>
                    </tr>
                </thead>
            </table>
        </div>
    @endcomponent
</section>

@include('stock_transfer.partials.update_status_modal')

<section id="receipt_section" class="print_section"></section>

<!-- /.content -->
@stop
@section('javascript')
	<script src="{{ asset('js/stock_transfer.js?v=' . $asset_v) }}"></script>

    <script>
        //Date range as a button
        $('#stock_transfer_list_filter_date_range').daterangepicker(
            dateRangeSettings,
            function (start, end) {
                $('#stock_transfer_list_filter_date_range').val(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));
               stock_transfer_table.ajax.reload();
            }
        );
        
    
        $(document).on('submit', '#update_stock_transfer_status_form', function(e){
            e.preventDefault();
            aelrt(111);
            var form = $(this);
            var data = form.serialize();
    
            $.ajax({
                method: 'POST',
                url: $(this).attr('action'),
                dataType: 'json',
                data: data,
                beforeSend: function(xhr) {
                    __disable_submit_button(form.find('button[type="submit"]'));
                },
                success: function(result) {
                    if (result.success == true) {
                        $('#update_stock_transfer_status_modal').modal('hide');
                        toastr.success(result.msg);
                        stock_transfer_table.ajax.reload();
                        $('#update_stock_transfer_status_form')
                            .find('button[type="submit"]')
                            .attr('disabled', false);
                    } else {
                        toastr.error(result.msg);
                    }
                },
            });
        });
    </script>
@endsection
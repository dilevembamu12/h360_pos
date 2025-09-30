@php
//dd(1111);
@endphp

@extends('layouts.app')
@section('title', 'Rapport de VENTE')

@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header no-print">
        <h1>Rapport de VENTE</h1>
    </section>

    <!-- Main content -->
    <section class="content no-print">
        <div class="row">
            <div class="col-md-12">
                @component('components.filters', ['title' => __('report.filters')])
                    {!! Form::open([
                        'url' =>
                            action([\App\Http\Controllers\ReportController::class, 'getManuelreport2']) .
                            '?view_mode=' .
                            Request::get('view_mode'),
                        'method' => 'get',
                        'id' => 'filter_form',
                    ]) !!}

                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('customer_id', __('contact.customer') . ':') !!}
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-user"></i>
                                </span>
                                {!! Form::select('customer_id', $customers, null, [
                                    'class' => 'form-control select2',
                                    'placeholder' => __('messages.please_select'),
                                    'required',
                                ]) !!}
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3" id="location_filter">
                        <div class="form-group">
                            {!! Form::label('location_id', __('purchase.business_location') . ':') !!}
                            {!! Form::select('location_id', $business_locations, null, [
                                'class' => 'form-control select2',
                                'style' => 'width:100%',
                                'placeholder' => __('lang_v1.all'),
                            ]) !!}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('category_id', __('product.category') . ':') !!}
                            {!! Form::select('category_id', $categories, null, [
                                'class' => 'form-control select2',
                                'style' => 'width:100%',
                                'id' => 'psr_filter_category_id',
                                'placeholder' => __('lang_v1.all'),
                            ]) !!}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('brand_id', __('product.brand') . ':') !!}
                            {!! Form::select('brand_id', $brands, null, [
                                'class' => 'form-control select2',
                                'style' => 'width:100%',
                                'id' => 'psr_filter_brand_id',
                                'placeholder' => __('lang_v1.all'),
                            ]) !!}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('product_sr_date_filter', __('report.date_range') . ':') !!}
                            @php
                                $startDay = Carbon::now()->startOfDay();
                                $endDay = $startDay->copy()->endOfDay();
                            @endphp
                            {!! Form::text('date_range', null, [
                                'placeholder' => __('lang_v1.select_a_date_range'),
                                'class' => 'form-control',
                                'id' => 'product_sr_date_filter',
                                'readonly',
                            ]) !!}
                        </div>
                    </div>
                    <div class="col-md-3">
                        {!! Form::label('product_sr_start_time', __('lang_v1.time_range') . ':') !!}
                        @php
                            $startDay = Carbon::now()->startOfDay();
                            $endDay = $startDay->copy()->endOfDay();
                        @endphp
                        <div class="form-group">
                            {!! Form::text('start_time', @format_time($startDay), [
                                'style' => __('lang_v1.select_a_date_range'),
                                'class' => 'form-control width-50 f-left',
                                'id' => 'product_sr_start_time',
                            ]) !!}
                            {!! Form::text('end_time', @format_time($endDay), [
                                'class' => 'form-control width-50 f-left',
                                'id' => 'product_sr_end_time',
                            ]) !!}
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <br>
                            @php
                                $checked = '';
                            @endphp
                            @if (Request::get('show_by_box') == 1)
                                @php $checked='checked' @endphp
                            @endif
                            <label>
                                {!! Form::checkbox('show_by_box', 1, false, ['class' => 'input-icheck', 'id' => 'show_by_box', $checked]) !!} <strong>Voir en carton</strong>
                            </label>
                        </div>
                    </div>



                    <div class="col-sm-12">
                        <button type="submit" class="btn btn-primary pull-right">@lang('report.apply_filters')</button>
                    </div>
                    {!! Form::close() !!}
                @endcomponent
            </div>
        </div>
        @include('manuel_report2.partials.stock_report_table')

    </section>
    <!-- /.content -->
    <div class="modal fade view_register" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>

@endsection

@section('javascript')
    
    <script src="{{ asset('js/report.js?v=' . $asset_v) }}"></script>

    <script type="text/javascript">
        $(function() {
            $("#product_sr_date_filter").daterangepicker({
                ranges: ranges,
                startDate: ranges[LANG.yesterday][0],
                endDate: ranges[LANG.yesterday][1],
                locale: {
                    cancelLabel: LANG.clear,
                    applyLabel: LANG.apply,
                    customRangeLabel: LANG.custom_range,
                    format: moment_date_format,
                    toLabel: '~',
                },
            });
        });
    </script>


    <script type="text/javascript">
        $('#filter_form').on('submit', function(eventObj) {
            var start = '';
            var end = '';
            var start_time = $('#product_sr_start_time').val();
            var end_time = $('#product_sr_end_time').val();
            if ($('#product_sr_date_filter').val()) {
                start = $('input#product_sr_date_filter')
                    .data('daterangepicker')
                    .startDate.format('YYYY-MM-DD');
                end = $('input#product_sr_date_filter')
                    .data('daterangepicker')
                    .endDate.format('YYYY-MM-DD');

                start = moment(start + " " + start_time, "YYYY-MM-DD" +
                    " " + moment_time_format).format(
                    'YYYY-MM-DD HH:mm');
                end = moment(end + " " + end_time, "YYYY-MM-DD" + " " +
                    moment_time_format).format('YYYY-MM-DD HH:mm');
            }

            $("<input />").attr("type", "hidden")
                .attr("name", "start_date")
                .attr("value", start)
                .appendTo("#filter_form");
            $("<input />").attr("type", "hidden")
                .attr("name", "end_date")
                .attr("value", end)
                .appendTo("#filter_form");
            return true;

        });
        $(
            '#product_sell_report_form #location_id, #product_sell_report_form #customer_id, #psr_filter_brand_id, #psr_filter_category_id, #psr_customer_group_id'
        ).change(function() {
            $('.nav-tabs li.active').find('a[data-toggle="tab"]').trigger('shown.bs.tab');
        });
        $(document).ready(function() {
            $('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
                var target = $(e.target).attr('href');
                if (target == '#psr_by_cat_tab') {
                    if (typeof product_sell_report_by_category_datatable == 'undefined') {
                        product_sell_report_by_category_datatable = $(
                            'table#product_sell_report_by_category').DataTable({
                            processing: true,
                            serverSide: true,
                            ajax: {
                                url: '/reports/product-sell-grouped-by',
                                data: function(d) {
                                    var start = '';
                                    var end = '';
                                    var start_time = $('#product_sr_start_time').val();
                                    var end_time = $('#product_sr_end_time').val();
                                    if ($('#product_sr_date_filter').val()) {
                                        start = $('input#product_sr_date_filter')
                                            .data('daterangepicker')
                                            .startDate.format('YYYY-MM-DD');
                                        end = $('input#product_sr_date_filter')
                                            .data('daterangepicker')
                                            .endDate.format('YYYY-MM-DD');

                                        start = moment(start + " " + start_time, "YYYY-MM-DD" +
                                            " " + moment_time_format).format(
                                            'YYYY-MM-DD HH:mm');
                                        end = moment(end + " " + end_time, "YYYY-MM-DD" + " " +
                                            moment_time_format).format('YYYY-MM-DD HH:mm');
                                    }
                                    d.start_date = start;
                                    d.end_date = end;
                                    d.group_by = 'category';
                                    d.category_id = $('select#psr_filter_category_id').val();
                                    d.brand_id = $('select#psr_filter_brand_id').val();
                                    d.customer_id = $('select#customer_id').val();
                                    d.location_id = $('select#location_id').val();
                                    d.customer_group_id = $('#psr_customer_group_id').val();
                                },
                            },
                            columns: [{
                                    data: 'category_name',
                                    name: 'cat.name'
                                },
                                {
                                    data: 'current_stock',
                                    name: 'current_stock',
                                    searchable: false,
                                    orderable: false
                                },
                                {
                                    data: 'total_qty_sold',
                                    name: 'total_qty_sold',
                                    searchable: false
                                },
                                {
                                    data: 'subtotal',
                                    name: 'subtotal',
                                    searchable: false
                                },
                            ],
                            fnDrawCallback: function(oSettings) {
                                $('#footer_psr_by_cat_total_sell').text(
                                    sum_table_col($('#product_sell_report_by_category'),
                                        'row_subtotal')
                                );
                                $('#footer_psr_by_cat_total_sold').html(
                                    __sum_stock($('#product_sell_report_by_category'),
                                        'sell_qty')
                                );

                                $('#footer_psr_by_cat_total_stock').html(
                                    __sum_stock($('#product_sell_report_by_category'),
                                        'current_stock')
                                );
                                __currency_convert_recursively($(
                                    '#product_sell_report_by_category'));
                            },
                        });
                    } else {
                        product_sell_report_by_category_datatable.ajax.reload();
                    }
                } else if (target == '#psr_by_brand_tab') {
                    if (typeof product_sell_report_by_brand_datatable == 'undefined') {
                        product_sell_report_by_brand_datatable = $('table#product_sell_report_by_brand')
                            .DataTable({
                                processing: true,
                                serverSide: true,
                                ajax: {
                                    url: '/reports/product-sell-grouped-by',
                                    data: function(d) {
                                        var start = '';
                                        var end = '';
                                        var start_time = $('#product_sr_start_time').val();
                                        var end_time = $('#product_sr_end_time').val();
                                        if ($('#product_sr_date_filter').val()) {
                                            start = $('input#product_sr_date_filter')
                                                .data('daterangepicker')
                                                .startDate.format('YYYY-MM-DD');
                                            end = $('input#product_sr_date_filter')
                                                .data('daterangepicker')
                                                .endDate.format('YYYY-MM-DD');

                                            start = moment(start + " " + start_time, "YYYY-MM-DD" +
                                                " " + moment_time_format).format(
                                                'YYYY-MM-DD HH:mm');
                                            end = moment(end + " " + end_time, "YYYY-MM-DD" + " " +
                                                moment_time_format).format('YYYY-MM-DD HH:mm');
                                        }
                                        d.start_date = start;
                                        d.end_date = end;
                                        d.group_by = 'brand';
                                        d.category_id = $('select#psr_filter_category_id').val();
                                        d.brand_id = $('select#psr_filter_brand_id').val();
                                        d.customer_id = $('select#customer_id').val();
                                        d.location_id = $('select#location_id').val();
                                        d.customer_group_id = $('#psr_customer_group_id').val();
                                    },
                                },
                                columns: [{
                                        data: 'brand_name',
                                        name: 'b.name'
                                    },
                                    {
                                        data: 'current_stock',
                                        name: 'current_stock',
                                        searchable: false,
                                        orderable: false
                                    },
                                    {
                                        data: 'total_qty_sold',
                                        name: 'total_qty_sold',
                                        searchable: false
                                    },
                                    {
                                        data: 'subtotal',
                                        name: 'subtotal',
                                        searchable: false
                                    },
                                ],
                                fnDrawCallback: function(oSettings) {
                                    $('#footer_psr_by_brand_total_sell').text(
                                        sum_table_col($('#product_sell_report_by_brand'),
                                            'row_subtotal')
                                    );
                                    $('#footer_psr_by_brand_total_sold').html(
                                        __sum_stock($('#product_sell_report_by_brand'),
                                            'sell_qty')
                                    );

                                    $('#footer_psr_by_cat_total_stock').html(
                                        __sum_stock($('#product_sell_report_by_brand'),
                                            'current_stock')
                                    );
                                    __currency_convert_recursively($(
                                        '#product_sell_report_by_brand'));
                                },
                            });
                    } else {
                        product_sell_report_by_brand_datatable.ajax.reload();
                    }
                }
            });
        });
    </script>
@endsection

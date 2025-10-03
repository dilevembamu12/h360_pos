@php
//dd($data);
@endphp




@extends('layouts.app')
@section('title', __('report.stock_report'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>{{ __('report.stock_report')}}</h1>
</section>

<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
            @component('components.filters', ['title' => __('report.filters')])
              {!! Form::open(['url' => action('ReportController@getManuelreport'), 'method' => 'get' ]) !!}
              <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('location_id',  __('purchase.business_location') . ':') !!}
                    {!! Form::select('location_id', $business_locations, null, ['class' => 'form-control select2', 'style' => 'width:100%']); !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('category_id', __('category.category') . ':') !!}
                    {!! Form::select('category', $categories, null, ['placeholder' => __('messages.all'), 'class' => 'form-control select2', 'style' => 'width:100%', 'id' => 'category_id']); !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('sub_category_id', __('product.sub_category') . ':') !!}
                    {!! Form::select('sub_category', array(), null, ['placeholder' => __('messages.all'), 'class' => 'form-control select2', 'style' => 'width:100%', 'id' => 'sub_category_id']); !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('brand', __('product.brand') . ':') !!}
                    {!! Form::select('brand', $brands, null, ['placeholder' => __('messages.all'), 'class' => 'form-control select2', 'style' => 'width:100%']); !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('unit',__('product.unit') . ':') !!}
                    {!! Form::select('unit', $units, null, ['placeholder' => __('messages.all'), 'class' => 'form-control select2', 'style' => 'width:100%']); !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                  <br>
                  <label>
                    {!! Form::checkbox('show_by_box', 1, false, ['class' => 'input-icheck', 'id' => 'show_by_box']); !!} <strong>Voir en carton</strong>
                  </label>
                </div>
              </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('trending_product_date_range', __('report.date_range') . ':') !!}
                        {!! Form::text('date_range', null , ['placeholder' => __('lang_v1.select_a_date_range'), 'class' => 'form-control', 'id' => 'trending_product_date_range', 'readonly']); !!}
                    </div>
                </div>


                <div class="col-sm-12">
                  <button type="submit" class="btn btn-primary pull-right">@lang('report.apply_filters')</button>
                </div> 
                {!! Form::close() !!}
            @endcomponent

 
        </div>
    </div>
    
    @include('manuel_report.partials.stock_report_table')
</section>
<!-- /.content -->

@endsection

@section('javascript')
    <script src="{{ asset('js/report.js?v=' . $asset_v) }}"></script>
@endsection
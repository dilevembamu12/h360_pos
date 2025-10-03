@php
//dd($data);
@endphp



@extends('layouts.app')
@section('title', 'Rapport de STOCK')

@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header no-print">
        <h1>Rapport de STOCK</h1>
    </section>


    <!-- Main content -->
    <section class="content no-print">
        <div class="row">
            <div class="col-md-12">
                @component('components.filters', ['title' => __('report.filters')])
                    {!! Form::open([
                        'url' => action([\App\Http\Controllers\ReportController::class, 'getManuelreport3']),
                        'method' => 'get',
                        'id' => 'filter_form',
                    ]) !!}



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


        @include('manuel_report3.partials.stock_report_table')
    </section>
    <!-- /.content -->
    <div class="modal fade view_register" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>

@endsection

@section('javascript')
    @php 
    //$asset_v = env('APP_VERSION'); 
    @endphp
    <script src="{{ asset('js/report.js?v=' . $asset_v) }}"></script>
    
@endsection

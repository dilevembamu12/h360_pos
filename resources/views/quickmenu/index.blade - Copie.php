@extends('layouts.app')
@section('title', __('home.home'))

@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header content-header-custom">
        <h1>{{ __('home.welcome_message', ['name' => Session::get('user.first_name')]) }}
        </h1>
    </section>
    <!-- Main content -->
    <section class="content content-custom no-print">
        <br>
        @if (auth()->user()->can('dashboard.data'))
            @if ($is_admin)
                <div class="row">
                    <div class="col-md-4 col-xs-12">
                        @if (count($all_locations) > 1)
                            {!! Form::select('dashboard_location', $all_locations, null, [
                                'class' => 'form-control select2',
                                'placeholder' => __('lang_v1.select_location'),
                                'id' => 'dashboard_location',
                            ]) !!}
                        @endif
                    </div>
                    <div class="col-md-8 col-xs-12">
                        <div class="form-group pull-right">
                            <div class="input-group">
                                <button type="button" class="btn btn-primary" id="dashboard_date_filter">
                                    <span>
                                        <i class="fa fa-calendar"></i> {{ __('messages.filter_by_date') }}
                                    </span>
                                    <i class="fa fa-caret-down"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <br>
                <div class="row no-print">
                    <div class="col-md-12">
                        <!-- Custom Tabs -->
                        <div class="nav-tabs-custom">
                            <ul class="nav nav-tabs">
                                <li class="active">
                                    <a href="#report_stock" data-toggle="tab" aria-expanded="true"><i
                                            class="fa fa-cubes" aria-hidden="true"></i> Rapport Stock</a>
                                </li>

                                <li>
                                    <a href="#profit_by_categories" data-toggle="tab" aria-expanded="true"><i
                                            class="fa fa-tags" aria-hidden="true"></i> Rapport Chauffeur/DR/Grossiste</a>
                                </li>

                                <li>
                                    <a href="#profit_by_brands" data-toggle="tab" aria-expanded="true"><i
                                            class="fa fa-diamond" aria-hidden="true"></i> Rapport Depenses</a>
                                </li>

                                <li>
                                    <a href="#profit_by_locations" data-toggle="tab" aria-expanded="true"><i
                                            class="fa fa-map-marker" aria-hidden="true"></i> Calcul de Profit</a>
                                </li>

                            </ul>

                            <div class="tab-content">
                                <div class="tab-pane active" id="report_stock">
                                    @include('home2.stock_report')
                                </div>

                                <div class="tab-pane" id="profit_by_categories">
                                    @include('report.partials.profit_by_categories')
                                </div>

                                <div class="tab-pane" id="profit_by_brands">
                                    @include('report.partials.profit_by_brands')
                                </div>

                                <div class="tab-pane" id="profit_by_locations">
                                    @include('report.partials.profit_by_locations')
                                </div>

                                <div class="tab-pane" id="profit_by_invoice">
                                    @include('report.partials.profit_by_invoice')
                                </div>

                                <div class="tab-pane" id="profit_by_date">
                                    @include('report.partials.profit_by_date')
                                </div>

                                <div class="tab-pane" id="profit_by_customer">
                                    @include('report.partials.profit_by_customer')
                                </div>

                                <div class="tab-pane" id="profit_by_day">

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif


            <iframe url="http://127.0.0.1:8000/home2">

            </iframe>

            <div>
                bbbbbb
            </div>

            <div>
                cccccc
            </div>


        @endif
        <!-- can('dashboard.data') end -->
    </section>
@stop
@section('javascript')
    <script src="{{ asset('js/home.js?v=' . $asset_v) }}"></script>
@endsection

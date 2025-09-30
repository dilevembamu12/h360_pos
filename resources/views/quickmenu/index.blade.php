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
            
            <br>
            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12 col-custom">
                    <a href="#!" target="popup"
                        onclick="  window.open('{{ action([\App\Http\Controllers\AccountController::class, 'index2'])}}?core_layout=none','popup','width=600,height=600'); return false;">

                        <div class="info-box info-box-new-style">
                            <span class="info-box-icon bg-red text-white">
                                <i class="fas fa-exchange-alt"></i>
                            </span>

                            <div class="info-box-content">
                                <span class="info-box-text">Rapport de CASH</span>
                            </div>
                            <!-- /.info-box-content -->
                            <p class="mb-0 text-muted fs-10 mt-5" style="margin-left: 75px">Rapport Stock: <span
                                    class="total_sr"></span><br>
                                {{ __('lang_v1.total_sell_return_paid') }}<span class="total_srp"></span></p>
                        </div>
                    </a>





                    <!-- /.info-box -->
                </div>
            </div>
            
            <div class="row">

                <div class="col-md-6 col-sm-6 col-xs-12 col-custom">
                    <a href="#!" target="popup"
                        onclick="window.open('{{ action([\App\Http\Controllers\ReportController::class, 'getManuelreport2']) }}?core_layout=none&view_mode=1&show_by_box=1','popup','width=600,height=600'); return false;">

                        <div class="info-box info-box-new-style">
                            <span class="info-box-icon bg-red text-white">
                                <i class="fas fa-exchange-alt"></i>
                            </span>

                            <div class="info-box-content">

                                <span class="info-box-text">Rapport VENTE (TABLEAU)</span>
                            </div>
                            <!-- /.info-box-content -->
                            <p class="mb-0 text-muted fs-10 mt-5" style="margin-left: 75px">Rapport Stock: <span
                                    class="total_sr"></span><br>
                                Stock vendu dans la societé<span class="total_srp"></span></p>
                        </div>
                    </a>
                    <a href="#!" target="popup"
                        onclick="window.open('{{ action([\App\Http\Controllers\ReportController::class, 'getManuelreport2']) }}?core_layout=none&show_by_box=1','popup','width=600,height=600'); return false;">

                        <div class="info-box info-box-new-style">
                            <span class="info-box-icon bg-red text-white">
                                <i class="fas fa-exchange-alt"></i>
                            </span>

                            <div class="info-box-content">

                                <span class="info-box-text">Rapport VENTE (TABLEAU + GRILLE)</span>
                            </div>
                            <!-- /.info-box-content -->
                            <p class="mb-0 text-muted fs-10 mt-5" style="margin-left: 75px">Rapport Stock: <span
                                    class="total_sr"></span><br>
                                Stock vendu dans la societé, format tableau et bloc de facture<span
                                    class="total_srp"></span></p>
                        </div>
                    </a>

                    <!-- /.info-box -->
                </div>

                <div class="col-md-6 col-sm-6 col-xs-12 col-custom">
                    <a href="#!" target="popup"
                        onclick="window.open('{{ action([\App\Http\Controllers\ReportController::class, 'getManuelreport3']) }}?core_layout=none&view_mode=2&show_by_box=1','popup','width=600,height=600'); return false;">
                        <div class="info-box info-box-new-style">
                            <span class="info-box-icon bg-red text-white">
                                <i class="fas fa-exchange-alt"></i>
                            </span>

                            <div class="info-box-content">
                                <span class="info-box-text">Rapport Stock (TABLEAU)</span>
                            </div>
                            <!-- /.info-box-content -->
                            <p class="mb-0 text-muted fs-10 mt-5" style="margin-left: 75px">Rapport Stock: <span
                                    class="total_sr"></span><br>
                                Le stock restant dans la societé<span class="total_srp"></span></p>
                        </div>
                    </a>


                    <!-- /.info-box -->
                </div>

            </div>

            
            {{--
            

            <div class="row" style="display: none">

                <div class="col-md-3 col-sm-6 col-xs-12 col-custom">
                    <a href="#!" target="popup"
                        onclick="window.open('{{ action('SellReturnController@index') }}?core_layout=none','popup','width=600,height=600'); return false;">
                        <div class="info-box info-box-new-style">
                            <span class="info-box-icon bg-red text-white">
                                <i class="fas fa-exchange-alt"></i>
                            </span>

                            <div class="info-box-content">
                                <span class="info-box-text">Rapport Stock</span>
                            </div>
                            <!-- /.info-box-content -->
                            <p class="mb-0 text-muted fs-10 mt-5" style="margin-left: 75px">Rapport Stock: <span
                                    class="total_sr"></span><br>
                                {{ __('lang_v1.total_sell_return_paid') }}<span class="total_srp"></span></p>
                        </div>
                    </a>

                    <!-- /.info-box -->
                </div>

                <div class="col-md-3 col-sm-6 col-xs-12 col-custom">
                    <a href="#!" target="popup"
                        onclick="window.open('{{ action('ContactController@index') }}?core_layout=none&type=customer','popup','width=600,height=600'); return false;">

                        <div class="info-box info-box-new-style">
                            <span class="info-box-icon bg-red text-white">
                                <i class="fas fa-exchange-alt"></i>
                            </span>

                            <div class="info-box-content">
                                <span class="info-box-text">Rapport de DR/Chauffeur</span>
                            </div>
                            <!-- /.info-box-content -->
                            <p class="mb-0 text-muted fs-10 mt-5" style="margin-left: 75px">Rapport Stock: <span
                                    class="total_sr"></span><br>
                                {{ __('lang_v1.total_sell_return_paid') }}<span class="total_srp"></span></p>
                        </div>
                    </a>

                    <!-- /.info-box -->
                </div>

                <div class="col-md-3 col-sm-6 col-xs-12 col-custom">
                    <a href="#!" target="popup"
                        onclick="window.open('{{ action('ReportController@getCustomerGroup') }}?core_layout=none&type=customer','popup','width=600,height=600'); return false;">

                        <div class="info-box info-box-new-style">
                            <span class="info-box-icon bg-red text-white">
                                <i class="fas fa-exchange-alt"></i>
                            </span>

                            <div class="info-box-content">
                                <span class="info-box-text">Vente par Groupes de DR</span>
                            </div>
                            <!-- /.info-box-content -->
                            <p class="mb-0 text-muted fs-10 mt-5" style="margin-left: 75px">Rapport Stock: <span
                                    class="total_sr"></span><br>
                                {{ __('lang_v1.total_sell_return_paid') }}<span class="total_srp"></span></p>
                        </div>
                    </a>

                    <!-- /.info-box -->
                </div>

                <div class="col-md-3 col-sm-6 col-xs-12 col-custom">
                    <a href="#!" target="popup"
                        onclick="window.open('{{ action('ReportController@getProfitLoss') }}?core_layout=none&no_display=pl_data_div','popup','width=600,height=600'); return false;">
                        <div class="info-box info-box-new-style">
                            <span class="info-box-icon bg-red text-white">
                                <i class="fas fa-exchange-alt"></i>
                            </span>

                            <div class="info-box-content">
                                <span class="info-box-text">Rapport de Bénéfices</span>
                            </div>
                            <!-- /.info-box-content -->
                            <p class="mb-0 text-muted fs-10 mt-5" style="margin-left: 75px">Rapport Stock: <span
                                    class="total_sr"></span><br>
                                {{ __('lang_v1.total_sell_return_paid') }}<span class="total_srp"></span></p>
                        </div>
                    </a>

                    <!-- /.info-box -->
                </div>
            </div>



            <div class="row" style="display: none">
                <div class="col-md-3 col-sm-6 col-xs-12 col-custom">
                    <a href="#" target="popup"
                        onclick="window.open('{{ action('ContactController@index') }}?core_layout=none&type=supplier','popup','width=600,height=600'); return false;">

                        <div class="info-box info-box-new-style">
                            <span class="info-box-icon bg-red text-white">
                                <i class="fas fa-exchange-alt"></i>
                            </span>

                            <div class="info-box-content">
                                <span class="info-box-text">Cashbook Fournisseur</span>
                            </div>
                            <!-- /.info-box-content -->
                            <p class="mb-0 text-muted fs-10 mt-5" style="margin-left: 75px">Rapport Stock: <span
                                    class="total_sr"></span><br>
                                {{ __('lang_v1.total_sell_return_paid') }}<span class="total_srp"></span></p>
                        </div>
                    </a>

                    <!-- /.info-box -->
                </div>

                <div class="col-md-3 col-sm-6 col-xs-12 col-custom">
                    <a href="#!" target="popup"
                        onclick="window.open('{{ action('ContactController@index') }}?core_layout=none&type=customer','popup','width=600,height=600'); return false;">

                        <div class="info-box info-box-new-style">
                            <span class="info-box-icon bg-red text-white">
                                <i class="fas fa-exchange-alt"></i>
                            </span>

                            <div class="info-box-content">
                                <span class="info-box-text">Cashbook DR/Chauffeur</span>
                            </div>
                            <!-- /.info-box-content -->
                            <p class="mb-0 text-muted fs-10 mt-5" style="margin-left: 75px">Rapport Stock: <span
                                    class="total_sr"></span><br>
                                {{ __('lang_v1.total_sell_return_paid') }}<span class="total_srp"></span></p>
                        </div>
                    </a>

                    <!-- /.info-box -->
                </div>


                <div class="col-md-3 col-sm-6 col-xs-12 col-custom">
                    <a href="#!" target="popup"
                        onclick="window.open('{{ action('ReportController@getRegisterReport') }}?core_layout=none','popup','width=600,height=600'); return false;">

                        <div class="info-box info-box-new-style">
                            <span class="info-box-icon bg-red text-white">
                                <i class="fas fa-exchange-alt"></i>
                            </span>

                            <div class="info-box-content">
                                <span class="info-box-text">Rapport Caisse</span>
                            </div>
                            <!-- /.info-box-content -->
                            <p class="mb-0 text-muted fs-10 mt-5" style="margin-left: 75px">Rapport Stock: <span
                                    class="total_sr"></span><br>
                                {{ __('lang_v1.total_sell_return_paid') }}<span class="total_srp"></span></p>
                        </div>
                    </a>

                    <!-- /.info-box -->
                </div>

                <div class="col-md-3 col-sm-6 col-xs-12 col-custom">
                    <a href="#!" target="popup"
                        onclick="window.open('{{ action('ReportController@getExpenseReport') }}?core_layout=none','popup','width=600,height=600'); return false;">

                        <div class="info-box info-box-new-style">
                            <span class="info-box-icon bg-red text-white">
                                <i class="fas fa-exchange-alt"></i>
                            </span>

                            <div class="info-box-content">
                                <span class="info-box-text">Depense en categorie</span>
                            </div>
                            <!-- /.info-box-content -->
                            <p class="mb-0 text-muted fs-10 mt-5" style="margin-left: 75px">Rapport Stock: <span
                                    class="total_sr"></span><br>
                                {{ __('lang_v1.total_sell_return_paid') }}<span class="total_srp"></span></p>
                        </div>
                    </a>

                    <!-- /.info-box -->
                </div>

            </div>

            <div class="row" style="display: none">
                <div class="col-md-3 col-sm-6 col-xs-12 col-custom">
                    <a href="#!" target="popup"
                        onclick="window.open('{{ action('ExpenseController@index') }}?core_layout=none','popup','width=600,height=600'); return false;">

                        <div class="info-box info-box-new-style">
                            <span class="info-box-icon bg-red text-white">
                                <i class="fas fa-exchange-alt"></i>
                            </span>

                            <div class="info-box-content">
                                <span class="info-box-text">Liste de Depenses</span>
                            </div>
                            <!-- /.info-box-content -->
                            <p class="mb-0 text-muted fs-10 mt-5" style="margin-left: 75px">Rapport Stock: <span
                                    class="total_sr"></span><br>
                                {{ __('lang_v1.total_sell_return_paid') }}<span class="total_srp"></span></p>
                        </div>
                    </a>

                    <!-- /.info-box -->
                </div>


                <div class="col-md-3 col-sm-6 col-xs-12 col-custom">
                    <a href="#!" target="popup"
                        onclick="window.open('{{ action('ReportController@getproductSellReport') }}?core_layout=none','popup','width=600,height=600'); return false;">

                        <div class="info-box info-box-new-style">
                            <span class="info-box-icon bg-red text-white">
                                <i class="fas fa-exchange-alt"></i>
                            </span>

                            <div class="info-box-content">
                                <span class="info-box-text">Rapport de ventes</span>
                            </div>
                            <!-- /.info-box-content -->
                            <p class="mb-0 text-muted fs-10 mt-5" style="margin-left: 75px">Rapport Stock: <span
                                    class="total_sr"></span><br>
                                {{ __('lang_v1.total_sell_return_paid') }}<span class="total_srp"></span></p>
                        </div>
                    </a>

                    <!-- /.info-box -->
                </div>






            </div>
            @if ($is_admin)





                @if (!empty($widgets['after_sale_purchase_totals']))
                    @foreach ($widgets['after_sale_purchase_totals'] as $widget)
                        {!! $widget !!}
                    @endforeach
                @endif
            @endif
            <!-- end is_admin check -->
            @if (auth()->user()->can('sell.view') ||
                    auth()->user()->can('direct_sell.view'))
            @endif

            --}}


        @endif
        <!-- can('dashboard.data') end -->
    </section>
@stop


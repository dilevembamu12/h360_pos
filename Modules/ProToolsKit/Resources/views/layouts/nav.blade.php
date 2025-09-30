<section class="no-print">
    <nav class="navbar navbar-default bg-white m-4">
        <div class="container-fluid">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="{{action([\Modules\ProToolsKit\Http\Controllers\SMSController::class, 'index'])}}"><i class="fas fa-industry"></i> {{__('protoolskit::ptk.sms')}}</a>
            </div>

            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav">
                    @can('manufacturing.access_production')
                    <li @if(request()->segment(1) == 'protoolskit' && empty(request()->segment(2))) class="active" @endif><a href="{{action([\Modules\ProToolsKit\Http\Controllers\SMSController::class, 'index'])}}">**SMS</a></li>
                        {{-- 
                        <li @if(request()->segment(2) == 'production') class="active" @endif><a href="{{action([\Modules\ProToolsKit\Http\Controllers\SMSController::class, 'index'])}}">**Boite de reception</a></li>

                        <li @if(request()->segment(1) == 'manufacturing' && request()->segment(2) == 'settings') class="active" @endif><a href="{{action([\Modules\ProToolsKit\Http\Controllers\SMSController::class, 'showSendSms'])}}">**Boite d'envoi</a></li>
                        --}}
                        <li @if(request()->segment(2) == 'history') class="active" @endif><a href="{{action([\Modules\ProToolsKit\Http\Controllers\SMSController::class, 'history'])}}">**Historique d'envoi</a></li>

                        <li @if(request()->segment(2) == 'buycredit') class="active" @endif><a href="{{action([\Modules\ProToolsKit\Http\Controllers\SMSController::class, 'buycredit'])}}">**Achetez plus de crédit</a></li>
                    @endcan
                </ul>

            </div><!-- /.navbar-collapse -->
        </div><!-- /.container-fluid -->
    </nav>
</section>
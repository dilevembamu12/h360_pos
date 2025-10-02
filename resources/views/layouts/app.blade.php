@inject('request', 'Illuminate\Http\Request')
{{-- -----------PERSONNALIZE CUSTOMER CODE 05102024--- --}}
{{-- ----------- CLOSE WINDOWS IF close_current_windows=1 ==>used for auto close after paiement ----- --}}

@if (request()->get('close_current_windows') == 1)
    <script type="text/javascript">
        self.close();
    </script>
    @php die("Processus de paiement terminé, veuillez fermer cette fenêtre"); @endphp
@endif


{{-- ------------------------------------------------------------------------------------------------ --}}

@if (
    $request->segment(1) == 'pos' &&
        ($request->segment(2) == 'create' || $request->segment(3) == 'edit' || $request->segment(2) == 'payment'))
    @php
        $pos_layout = true;
    @endphp
@else
    @php
        $pos_layout = false;
    @endphp
@endif

@php
    $whitelist = ['127.0.0.1', '::1'];
@endphp

@php

    use App\BusinessLocation;
    use App\Business;

    use Spatie\Permission\Models\Permission;
    use Spatie\Permission\Models\Role;
    use App\Utils\ModuleUtil;

    use App\MobilemoneyPayLine;
    use Illuminate\Http\Request;


    //personnalize custom code 25042024-MOBILEMONEY -- 25042024
    //retrouver tous les paiementts mobbiles money en attentte afin de les confirmer
    $_request = new Request();
    $_request->setMethod('POST');
    
    $mmp = MobilemoneyPayLine::where('business_id', session()->get('user.business_id'))
        ->whereIn('status', ['pending', 'draft'])
        ->get();
    //dd($mmp);
    foreach ($mmp as $key => $value) {
        $action=explode('-',$value->payment_ref);
        
        $_request->replace([
            '_token' => csrf_token(),
            'ordernumber' => $value->order_number,
            'gateway' => 'check' . $value->method,
            'phone' => $value->mobile,
        ]);
        if($action[0]=="smscredit"){
            $result = app(Modules\ProToolsKit\Http\Controllers\SMSController::class)->confirm_flexpayBuycredit($_request);
        }else if($action[0]=="subscription"){
            $result = app(Modules\Superadmin\Http\Controllers\SubscriptionController::class)->confirm_flexpaySubscription($_request);
        }else{
            $result = app(App\Http\Controllers\SellPosController::class)->triggerPayment($value->transaction_id, $_request);
        }
        //dd($action);
        //dd($_request);
        
        //dd($result);
    }
    

    //je fais aussi le trigger paiement pour tous les achats d'sms

    /*
    //si il y a eu des trasanction à confirmer , on sleep le code pendan 1.5 seconde pour pendre en compte les modifications 
    if(!empty($mmp)){
        usleep( 1.5 * 1000 );
    }
    */

    //----------------------- END PERSONNALIZE CUSTOM CODE-----------------------------------//////


// personnalize custom code 23032024-MULTIDEVISE030 -- 24032024
//==>mettttrre la devvise du systeme  la devvise de locattion parr deffautt si la locattion n''a jamais eu des devvises initttialisé'
//$location_details = BusinessLocation::find($input['location_id']);
$business_id = session()->get('user.business_id');
$business_details  = Business::leftjoin('currencies AS cur', 'business.currency_id', 'cur.id')
    ->select('cur.id as currency_id')
    ->where('business.id', $business_id)
    ->first();
$locations = BusinessLocation::where('business_id', $business_id)->get();
foreach ($locations as $location) {
    if (empty($location->second_currency)) {
        $location->second_currency = $business_details->currency_id;
        $location->second_currency_rate = 1;
        $location->save();
    }
}
/*********************************************************/

/*** personnalize custom code 27032024-UPDATE_ADMIN_ROLE001 ***/
//donnez la possibiliter pour les Admin de tous les business d'utiliser la fonctionnalité CRM
$module_util = new ModuleUtil();
    if (
        !auth()->user()->can('superadmin') &&
        (auth()->user()->can('admin') && $module_util->hasThePermissionInSubscription($business_id, 'crm_module'))
    ) {
        //dd(111);
        $role = Role::where([['business_id', '=', $business_id], ['is_default', '=', 1]])->first();
        //je recuper tous les roles pour ce business
        $crm_permissions = [
            'crm.access_all_schedule',
            'crm.access_all_leads',
            'crm.access_all_campaigns',
            'crm.access_contact_login',
            'crm.access_sources',
            'crm.access_life_stage',
            'crm.access_proposal',
            'crm.access_b2b_marketplace',
        ];

        $exising_permissions = Permission::whereIn('name', $crm_permissions)->pluck('name')->toArray();
        $non_existing_permissions = array_diff($crm_permissions, $exising_permissions);
        if (!empty($non_existing_permissions)) {
            foreach ($non_existing_permissions as $new_permission) {
                $time_stamp = \Carbon::now()->toDateTimeString();
                Permission::create([
                    'name' => $new_permission,
                    'guard_name' => 'web',
                ]);
            }
        }
        if (!empty($crm_permissions)) {
            $role->syncPermissions($crm_permissions);
        }
    }

    /******* endcustomcode ******/

@endphp

<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}"
    dir="{{ in_array(session()->get('user.language', config('app.locale')), config('constants.langs_rtl')) ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title') - {{ Session::get('business.name') }}</title>

    @include('layouts.partials.css')

    @yield('css')


    <link rel="stylesheet" href="{{ asset('modules/help/css/help.css') }}">
    <link rel="stylesheet" href="{{ asset('modules/h360copilot/css/copilot.css') }}">
</head>

<body
    class="@if ($pos_layout) hold-transition lockscreen @else hold-transition skin-@if (!empty(session('business.theme_color'))){{ session('business.theme_color') }}@else{{ 'blue-light' }} @endif sidebar-mini @endif">
    <div class="wrapper thetop">
        <script type="text/javascript">
            if (localStorage.getItem("upos_sidebar_collapse") == 'true') {
                var body = document.getElementsByTagName("body")[0];
                body.className += " sidebar-collapse";
            }
        </script>
        @if (!$pos_layout)
            @include('layouts.partials.header')
            @include('layouts.partials.sidebar')
        @else
            @include('layouts.partials.header-pos')
        @endif

        @if (in_array($_SERVER['REMOTE_ADDR'], $whitelist))
            <input type="hidden" id="__is_localhost" value="true">
        @endif

        <!-- Content Wrapper. Contains page content -->
        <div class="@if (!$pos_layout) content-wrapper @endif">
            <!-- empty div for vuejs -->
            <div id="app">
                @yield('vue')
            </div>

            {{--  personnalize custom code 06022024-MULTIDEVISE030 --}}
            {{--  si c'est l'ecran de vente , on met la devise secondaire assigné a cette location --}}
            @if (!$pos_layout)
                @php
                    //dd(11);
                @endphp
            @else
                @php
                    //dd(session()->all());
                @endphp
            @endif
            {{--  *********************************************** --}}

            <!-- Add currency related field-->
            <input type="hidden" id="__business_id" value="{{ session('user.business_id') }}">
            
            <input type="hidden" id="__code" value="{{ session('currency')['code'] }}">
            <input type="hidden" id="__symbol" value="{{ session('currency')['symbol'] }}">
            <input type="hidden" id="__thousand" value="{{ session('currency')['thousand_separator'] }}">
            <input type="hidden" id="__decimal" value="{{ session('currency')['decimal_separator'] }}">
            <input type="hidden" id="__symbol_placement" value="{{ session('business.currency_symbol_placement') }}">
            <input type="hidden" id="__precision" value="{{ session('business.currency_precision', 2) }}">
            <input type="hidden" id="__quantity_precision" value="{{ session('business.quantity_precision', 2) }}">
            <!-- End of currency related field-->
            @can('view_export_buttons')
                <input type="hidden" id="view_export_buttons">
            @endcan
            @if (isMobile())
                <input type="hidden" id="__is_mobile">
            @endif
            @if (session('status'))
                <input type="hidden" id="status_span" data-status="{{ session('status.success') }}"
                    data-msg="{{ session('status.msg') }}">
            @endif
            @yield('content')

            <div class='scrolltop no-print'>
                <div class='scroll icon'><i class="fas fa-angle-up"></i></div>
            </div>

            @if (config('constants.iraqi_selling_price_adjustment'))
                <input type="hidden" id="iraqi_selling_price_adjustment">
            @endif

            <!-- This will be printed -->
            <section class="invoice print_section" id="receipt_section">
            </section>

        </div>
        @include('home.todays_profit_modal')
        <!-- /.content-wrapper -->

        @if (!$pos_layout)
            @include('layouts.partials.footer')
        @else
            @include('layouts.partials.footer_pos')
        @endif

        <audio id="success-audio">
            <source src="{{ asset('/audio/success.ogg?v=' . $asset_v) }}" type="audio/ogg">
            <source src="{{ asset('/audio/success.mp3?v=' . $asset_v) }}" type="audio/mpeg">
        </audio>
        <audio id="error-audio">
            <source src="{{ asset('/audio/error.ogg?v=' . $asset_v) }}" type="audio/ogg">
            <source src="{{ asset('/audio/error.mp3?v=' . $asset_v) }}" type="audio/mpeg">
        </audio>
        <audio id="warning-audio">
            <source src="{{ asset('/audio/warning.ogg?v=' . $asset_v) }}" type="audio/ogg">
            <source src="{{ asset('/audio/warning.mp3?v=' . $asset_v) }}" type="audio/mpeg">
        </audio>
    </div>

    @if (!empty($__additional_html))
        {!! $__additional_html !!}
    @endif

    @include('layouts.partials.javascripts')

    <div class="modal fade view_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel"></div>

    @if (!empty($__additional_views) && is_array($__additional_views))
        @foreach ($__additional_views as $additional_view)
            @includeIf($additional_view)
        @endforeach
    @endif

    @yield('mycustom_js')
    
    
    {{--    
    auth()->user()
    session()->get('user.business_id')

    <script src='https://ai.h360.cd/Modules/Chatbot/Resources/assets/js/chatbot-widget.min.js'  data-iframe-src="https://ai.h360.cd/chatbot/embed/chatbot_code=711535ffedd14e4/welcome" data-iframe-height="532" data-iframe-width="400"></script>
    

     <script src='https://ai.h360.cd/Modules/Chatbot/Resources/assets/js/chatbot-widget.min.js'  data-iframe-src="https://ai.h360.cd/chatbot/embed/chatbot_code=bot_{{auth()->user()->business_id}}_{{auth()->user()->id}}/welcome" data-iframe-height="532" data-iframe-width="400"></script>
     --}}


    {{-- =================================================================== --}}
    {{-- ============== SECTION AIDE & H360COPILOT ========================= --}}
    {{-- =================================================================== --}}

    {{-- Chargement du HTML pour le bouton flottant (déjà présent) --}}
    @include('help::partials.floating_help_button')

    {{-- **LIGNE MANQUANTE** : Chargement du HTML pour la fenêtre du chatbot --}}
    @include('h360copilot::partials.chatbot')

    {{-- Déclaration des variables pour les scripts --}}
    <script>
        var fetch_videos_url = "{{ route('help.fetchVideos') }}";
        var copilot_ask_url = "{{ route('h360_copilot.ask') }}";
    </script>

    {{-- Chargement des scripts des modules --}}
    <script src="{{ asset('modules/help/js/help.js') }}"></script>
    <script src="{{ asset('modules/h360copilot/js/copilot.js') }}"></script>

    {{-- =================================================================== --}}


     
    </body>

</html>
   
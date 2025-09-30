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
    
    
    <script>
      window.USERTOURJS_ENV_VARS = {
        // WebSocket connection URL
        WS_URI: "https://usertour.scaling.h360.cd/",
        
        // Base URL for SDK assets
        ASSETS_URI: "https://usertour.scaling.h360.cd/sdk/",
        
        // Modern JavaScript bundle (ES2020)
        USERTOURJS_ES2020_URL: 'https://usertour.scaling.h360.cd/sdk/es2020/usertour.js',
        
        // Legacy JavaScript bundle (IIFE)
        USERTOURJS_LEGACY_URL: "https://usertour.scaling.h360.cd/sdk/legacy/usertour.iife.js"
      };
    </script>
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
    
    {{-- personnalize custom code 12042025-TUTOSECTION -- 12042025 --}}
    {{-- BODY CSS POUR SECTION TUTO --}}
    <div class="tutorial-container no-print">
        <!-- Bouton Flottant d'Apprentissage -->
        <button id="tutorial-toggle" class="btn btn-primary tutorial-button">
            <i class="fas fa-video"></i>
        </button>

        <!-- Section Tutoriels Vidéo (initialement cachée) -->
        <section id="tutoriels-video" class="tutorial-section container py-5">
            <h2>Besoin d'aide ? Découvrez nos tutoriels vidéo</h2>
            @yield('tutoriels-video')
            <div class="text-center">
                <a href="https://academy.h360.cd" class="btn btn-primary" target="_blank">Voir tous les tutoriels</a>
            </div>
        </section>
    </div>
    {{-- **************END***************************************** --}}

    @if (!empty($__additional_views) && is_array($__additional_views))
        @foreach ($__additional_views as $additional_view)
            @includeIf($additional_view)
        @endforeach
    @endif

    @yield('mycustom_js')
    
    
    {{-- personnalize custom code 12042025-TUTOSECTION -- 12042025 --}}
    {{-- BODY CSS POUR SECTION TUTO --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
  const tutorialToggle = document.getElementById('tutorial-toggle');
  const tutorialSection = document.getElementById('tutoriels-video');

  tutorialToggle.addEventListener('click', function() {
    tutorialSection.classList.toggle('active');

    // Modification : Changer le texte et l'icône du bouton
    if (tutorialSection.classList.contains('active')) {
      tutorialToggle.innerHTML = '<i class="fas fa-times-circle"></i>';
      tutorialToggle.classList.remove('transparent'); // S'assurer qu'il est visible
    } else {
      tutorialToggle.innerHTML = '<i class="fas fa-video"></i>';
      setTimeout(function() {
        tutorialToggle.classList.add('transparent');
      }, 10000); // Réappliquer la transparence après 10 secondes
    }
  });

  // Ajout de la classe "transparent" après 10 secondes (initialement)
  setTimeout(function() {
    tutorialToggle.classList.add('transparent');
  }, 10000);
});
    </script>
    {{-- **************END***************************************** --}}
    
    
    {{--
    <script src='https://ai.h360.cd/Modules/Chatbot/Resources/assets/js/chatbot-widget.min.js'  data-iframe-src="https://ai.h360.cd/chatbot/embed/chatbot_code=bot_{{auth()->user()->business_id}}_{{auth()->user()->id}}/welcome" data-iframe-height="532" data-iframe-width="400"></script>
    --}}
    
    {{-- 
     <script src='https://ai.h360.cd/Modules/Chatbot/Resources/assets/js/chatbot-widget.min.js'  data-iframe-src="https://ai.h360.cd/chatbot/embed/chatbot_code={{env("ADMINISTRATOR_CHATBOT_CODE")}}/welcome" data-iframe-height="532" data-iframe-width="400"></script>
     --}}
     
     
     <script>
        !function(){var e="undefined"==typeof window?{}:window,r=e.usertour;if(console.log("enter npm backage, usertour:",r),!r){var t="https://js.usertour.io/";console.log("enter npm backage: ",t);var n=null;r=e.usertour={_stubbed:!0,load:function(){return n||(n=new Promise((function(r,o){var s=document.createElement("script");s.async=!0;var a=e.USERTOURJS_ENV_VARS||{};"es2020"===(a.USERTOURJS_BROWSER_TARGET||function(e){for(var r=[[/Edg\//,/Edg\/(\d+)/,80],[/OPR\//,/OPR\/(\d+)/,67],[/Chrome\//,/Chrome\/(\d+)/,80],[/CriOS\//,/CriOS\/(\d+)/,100],[/Safari\//,/Version\/(\d+)/,14],[/Firefox\//,/Firefox\/(\d+)/,74]],t=0;t<r.length;t++){var n=r[t],o=n[0],s=n[1],a=n[2];if(e.match(o)){var i=e.match(new RegExp(s));if(i&&parseInt(i[1],10)>=a)return"es2020";break}}return"legacy"}(navigator.userAgent))?(s.type="module",s.src=a.USERTOURJS_ES2020_URL||t+"es2020/usertour.js"):s.src=a.USERTOURJS_LEGACY_URL||t+"legacy/usertour.iife.js",s.onload=function(){r()},s.onerror=function(){document.head.removeChild(s),n=null;var e=new Error("Could not load Usertour.js");console.warn(e.message),o(e)},document.head.appendChild(s)}))),n}};var o=e.USERTOURJS_QUEUE=e.USERTOURJS_QUEUE||[],s=function(e){r[e]=function(){var t=Array.prototype.slice.call(arguments);r.load(),o.push([e,null,t])}},a=function(e){r[e]=function(){var t,n=Array.prototype.slice.call(arguments);r.load();var s=new Promise((function(e,r){t={resolve:e,reject:r}}));return o.push([e,t,n]),s}},i=function(e,t){r[e]=function(){return t}};s("init"),s("off"),s("on"),s("reset"),s("setBaseZIndex"),s("setSessionTimeout"),s("setTargetMissingSeconds"),s("setCustomInputSelector"),s("setCustomNavigate"),s("setCustomScrollIntoView"),s("setInferenceAttributeFilter"),s("setInferenceAttributeNames"),s("setInferenceClassNameFilter"),s("setScrollPadding"),s("setServerEndpoint"),s("setShadowDomEnabled"),s("setPageTrackingDisabled"),s("setUrlFilter"),s("setLinkUrlDecorator"),a("endAll"),a("group"),a("identify"),a("identifyAnonymous"),a("start"),a("track"),a("updateGroup"),a("updateUser"),i("isIdentified",!1),i("isStarted",!1)}}();

            // Initialize the Usertour SDK with your environment token
            usertour.init('cmfzhzq8600063y2cw5kkbp3c');
        
            // Identify the current user with their attributes
            @if(config('app.env') === 'local' && request()->has('test_usertour'))
        
                // --- MODE FAKER POUR LES TESTS ---
                // Ce bloc simule différents types d'utilisateurs de manière aléatoire.
        
                const fakeUsers = [
                    { id: 'fake_usr_starter', name: 'Sophie Dubois (Test)', email: 'sophie.test@email.com', signed_up_at: new Date(Date.now() - 2 * 24 * 60 * 60 * 1000).toISOString(), plan: 'Starter', business_type: 'Boutique' },
                    { id: 'fake_usr_business', name: 'Antoine Lefevre (Test)', email: 'antoine.test@email.com', signed_up_at: new Date(Date.now() - 150 * 24 * 60 * 60 * 1000).toISOString(), plan: 'Business', business_type: 'Restaurant', user_role: 'admin' },
                    { id: 'fake_usr_cashier', name: 'Marie Ngoma (Test)', email: 'marie.test@email.com', signed_up_at: new Date(Date.now() - 30 * 24 * 60 * 60 * 1000).toISOString(), plan: 'Business', user_role: 'cashier' },
                    { id: 'fake_usr_free', name: 'Jean-Pierre Lema (Test)', email: 'jp.test@email.com', signed_up_at: new Date(Date.now() - 1 * 24 * 60 * 60 * 1000).toISOString(), plan: 'Essentiel' }
                ];
        
                // Sélectionne un utilisateur aléatoire dans la liste à chaque rechargement de page
                const randomUser = fakeUsers[Math.floor(Math.random() * fakeUsers.length)];
        
                // Affiche un message dans la console pour savoir quel utilisateur est simulé
                console.log('%cUSERTOUR FAKER MODE:', 'color: orange; font-weight: bold;', 'Simulating user:', randomUser);
        
                usertour.identify(randomUser.id, {
                    name: randomUser.name,
                    email: randomUser.email,
                    signed_up_at: randomUser.signed_up_at, // Usertour attend 'signed_up_at'
                    plan: randomUser.plan,
                    business_type: randomUser.business_type,
                    user_role: randomUser.user_role
                });
        
            @else
        
                // --- MODE PRODUCTION ---
                // Utilise les vraies données de l'utilisateur authentifié.
                
                usertour.identify("{{ Auth::user()->id }}", {
                    name: "{{ Auth::user()->username }}",
                    email: "{{ Auth::user()->email }}",
                    signed_up_at: "{{ Auth::user()->created_at->toIso8601String() }}", // Conversion au format ISO 8601
                    
                    // Exemple d'attribut personnalisé que vous pourriez avoir
                    // plan: "{{ Auth::user()->business->subscription->package->name ?? 'Essentiel' }}"
                });
        
            @endif
    </script>
</body>

</html>

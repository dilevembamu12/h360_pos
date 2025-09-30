<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title') - {{ config('app.name', 'POS') }}</title> 

    @include('layouts.partials.css')

    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->


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

 <script>
    /*
!function(){var e,r="undefined"==typeof window?{}:window,t=r.usertour;if(console.log("enter npm backage, usertour:",t),!t){var n="https://usertour.scaling.h360.cd/";console.log("enter npm backage: ",n);var o=null;t=r.usertour={_stubbed:!0,load:function(){return o||(o=new Promise((function(e,t){var s=document.createElement("script");s.async=!0;var a=r.USERTOURJS_ENV_VARS||{};"es2020"===(a.USERTOURJS_BROWSER_TARGET||function(e){for(var r=[[/Edg\//,/Edg\/(\d+)/,80],[/OPR\//,/OPR\/(\d+)/,67],[/Chrome\//,/Chrome\/(\d+)/,80],[/CriOS\//,/CriOS\/(\d+)/,100],[/Safari\//,/Version\/(\d+)/,14],[/Firefox\//,/Firefox\/(\d+)/,74]],t=0;t<r.length;t++){var n=r[t],o=n[0],s=n[1],a=n[2];if(e.match(o)){var i=e.match(new RegExp(s));if(i&&parseInt(i[1],10)>=a)return"es2020";break}}return"legacy"}(navigator.userAgent))?(s.type="module",s.src=a.USERTOURJS_ES2020_URL||n+"es2020/usertour.js"):s.src=a.USERTOURJS_LEGACY_URL||n+"legacy/usertour.iife.js",s.onload=function(){e()},s.onerror=function(){document.head.removeChild(s),o=null;var e=new Error("Could not load Usertour.js");console.warn(e.message),t(e)},document.head.appendChild(s)}))),o}};var s=r.USERTOURJS_QUEUE=r.USERTOURJS_QUEUE||[],a=function(e){t[e]=function(){var r=Array.prototype.slice.call(arguments);t.load(),s.push([e,null,r])}},i=function(e){t[e]=function(){var r,n=Array.prototype.slice.call(arguments);t.load();var o=new Promise((function(e,t){r={resolve:e,reject:t}}));return s.push([e,r,n]),o}};a("init"),a("off"),a("on"),a("reset"),a("setBaseZIndex"),a("setSessionTimeout"),a("setTargetMissingSeconds"),a("setCustomInputSelector"),a("setCustomNavigate"),a("setCustomScrollIntoView"),a("setInferenceAttributeFilter"),a("setInferenceAttributeNames"),a("setInferenceClassNameFilter"),a("setScrollPadding"),a("setServerEndpoint"),a("setShadowDomEnabled"),a("setPageTrackingDisabled"),a("setUrlFilter"),a("setLinkUrlDecorator"),i("endAll"),i("group"),i("identify"),i("identifyAnonymous"),i("start"),i("track"),i("updateGroup"),i("updateUser"),e=!1,t["isIdentified"]=function(){return e}}}();

  
  usertour.init('cmfzhzq8600063y2cw5kkbp3c');
  //usertour.init('cmfyqaues0nz87bubh4q4we6p');

  
  */
</script>

</head>

<body>
    @inject('request', 'Illuminate\Http\Request')
    @if (session('status') && session('status.success'))
        <input type="hidden" id="status_span" data-status="{{ session('status.success') }}" data-msg="{{ session('status.msg') }}">
    @endif
    <div class="container-fluid">
        <div class="row eq-height-row">
            <div class="col-md-5 col-sm-5 hidden-xs left-col eq-height-col" >
                <div class="left-col-content login-header"> 
                    <div style="margin-top: 50%;">
                    <a href="/">
                    @if(file_exists(public_path('uploads/logo.png')))
                        <img src="/uploads/logo.png" class="img-rounded" alt="Logo" width="150">
                    @else
                       {{ config('app.name', 'ultimatePOS') }}
                    @endif 
                    </a>
                    <br/>
                    @if(!empty(config('constants.app_title')))
                        <small>{{config('constants.app_title')}}</small>
                    @endif
                    </div>
                </div>
            </div>
            <div class="col-md-7 col-sm-7 col-xs-12 right-col eq-height-col">
                <div class="row">
                <div class="col-md-3 col-xs-4" style="text-align: left;">
                    <select class="form-control input-sm" id="change_lang" style="margin: 10px;">
                    @foreach(config('constants.langs') as $key => $val)
                        <option value="{{$key}}" 
                            @if( (empty(request()->lang) && config('app.locale') == $key) 
                            || request()->lang == $key) 
                                selected 
                            @endif
                        >
                            {{$val['full_name']}}
                        </option>
                    @endforeach
                    </select>
                </div>
                <div class="col-md-9 col-xs-8" style="text-align: right;padding-top: 10px;">
                    @if(!($request->segment(1) == 'business' && $request->segment(2) == 'register'))
                        <!-- Register Url -->
                        @if(config('constants.allow_registration'))
                            <a href="{{ route('business.getRegister') }}@if(!empty(request()->lang)){{'?lang=' . request()->lang}} @endif" class="btn bg-maroon btn-flat" ><b>{{ __('business.not_yet_registered')}}</b> {{ __('business.register_now') }}</a>
                            <!-- pricing url -->
                            @if(Route::has('pricing') && config('app.env') != 'demo' && $request->segment(1) != 'pricing')
                                &nbsp; <a href="{{ action([\Modules\Superadmin\Http\Controllers\PricingController::class, 'index']) }}">@lang('superadmin::lang.pricing')</a>
                            @endif
                        @endif
                    @endif
                    @if($request->segment(1) != 'login')
                        &nbsp; &nbsp;<span class="text-white">{{ __('business.already_registered')}} </span><a href="{{ action([\App\Http\Controllers\Auth\LoginController::class, 'login']) }}@if(!empty(request()->lang)){{'?lang=' . request()->lang}} @endif">{{ __('business.sign_in') }}</a>
                    @endif
                </div>
                
                @yield('content')
                </div>
            </div>
        </div>
    </div>

    
    @include('layouts.partials.javascripts')
    
    <!-- Scripts -->
    <script src="{{ asset('js/login.js?v=' . $asset_v) }}"></script>
    
    @yield('javascript')

    <script type="text/javascript">
        $(document).ready(function(){
            $('.select2_register').select2();

            $('input').iCheck({
                checkboxClass: 'icheckbox_square-blue',
                radioClass: 'iradio_square-blue',
                increaseArea: '20%' // optional
            });
        });
    </script>
    
    
    <script src='https://ai.h360.cd/Modules/Chatbot/Resources/assets/js/chatbot-widget.min.js'  data-iframe-src="https://ai.h360.cd/chatbot/embed/chatbot_code={{env("ADMINISTRATOR_CHATBOT_CODE")}}/welcome" data-iframe-height="532" data-iframe-width="400"></script>
    
    
    
    
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




    


<script>
        // Récupérer les données de l'utilisateur depuis votre backend
        /*
        const currentUser = {
            id: 1233,
            email: "dilevembongo@h360.pos",
            name: "Dileve Mbamu",
            signupDate: "2025-09-01T10:00:00Z",
            plan: "Business"
        };

        // Identification de l'utilisateur auprès de Usertour
        usertour("identify", currentUser.id, {
          email: currentUser.email,
          name: currentUser.name,
          created_at: currentUser.signupDate,
          plan: currentUser.plan
        });
        */
    
    /*    
        usertour.identify('123', {
    name: 'h360111222',
    email: 'h360@h360.cd',
    signed_up_at: '2025-09-01T10:00:00Z',
  });
  */
       // usertour.identifyAnonymous()
    </script>
    
    <script>
        !function(){var e="undefined"==typeof window?{}:window,r=e.usertour;if(console.log("enter npm backage, usertour:",r),!r){var t="https://js.usertour.io/";console.log("enter npm backage: ",t);var n=null;r=e.usertour={_stubbed:!0,load:function(){return n||(n=new Promise((function(r,o){var s=document.createElement("script");s.async=!0;var a=e.USERTOURJS_ENV_VARS||{};"es2020"===(a.USERTOURJS_BROWSER_TARGET||function(e){for(var r=[[/Edg\//,/Edg\/(\d+)/,80],[/OPR\//,/OPR\/(\d+)/,67],[/Chrome\//,/Chrome\/(\d+)/,80],[/CriOS\//,/CriOS\/(\d+)/,100],[/Safari\//,/Version\/(\d+)/,14],[/Firefox\//,/Firefox\/(\d+)/,74]],t=0;t<r.length;t++){var n=r[t],o=n[0],s=n[1],a=n[2];if(e.match(o)){var i=e.match(new RegExp(s));if(i&&parseInt(i[1],10)>=a)return"es2020";break}}return"legacy"}(navigator.userAgent))?(s.type="module",s.src=a.USERTOURJS_ES2020_URL||t+"es2020/usertour.js"):s.src=a.USERTOURJS_LEGACY_URL||t+"legacy/usertour.iife.js",s.onload=function(){r()},s.onerror=function(){document.head.removeChild(s),n=null;var e=new Error("Could not load Usertour.js");console.warn(e.message),o(e)},document.head.appendChild(s)}))),n}};var o=e.USERTOURJS_QUEUE=e.USERTOURJS_QUEUE||[],s=function(e){r[e]=function(){var t=Array.prototype.slice.call(arguments);r.load(),o.push([e,null,t])}},a=function(e){r[e]=function(){var t,n=Array.prototype.slice.call(arguments);r.load();var s=new Promise((function(e,r){t={resolve:e,reject:r}}));return o.push([e,t,n]),s}},i=function(e,t){r[e]=function(){return t}};s("init"),s("off"),s("on"),s("reset"),s("setBaseZIndex"),s("setSessionTimeout"),s("setTargetMissingSeconds"),s("setCustomInputSelector"),s("setCustomNavigate"),s("setCustomScrollIntoView"),s("setInferenceAttributeFilter"),s("setInferenceAttributeNames"),s("setInferenceClassNameFilter"),s("setScrollPadding"),s("setServerEndpoint"),s("setShadowDomEnabled"),s("setPageTrackingDisabled"),s("setUrlFilter"),s("setLinkUrlDecorator"),a("endAll"),a("group"),a("identify"),a("identifyAnonymous"),a("start"),a("track"),a("updateGroup"),a("updateUser"),i("isIdentified",!1),i("isStarted",!1)}}();

// Initialize the Usertour SDK with your environment token
usertour.init('cmfzhzq8600063y2cw5kkbp3c');

// Identify the current user with their attributes
@if(config('app.env') !== 'local' && !request()->has('test_usertour'))

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
            name: "{{ Auth::user()->name }}",
            email: "{{ Auth::user()->email }}",
            signed_up_at: "{{ Auth::user()->created_at->toIso8601String() }}", // Conversion au format ISO 8601
            
            // Exemple d'attribut personnalisé que vous pourriez avoir
            // plan: "{{ Auth::user()->business->subscription->package->name ?? 'Essentiel' }}"
        });

    @endif
    </script>
</body>

</html>
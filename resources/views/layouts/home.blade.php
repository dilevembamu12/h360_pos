<!doctype html>
<html lang="{{ config('app.locale') }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title')</title>

        <!-- Fonts -->
        <!-- <link href="https://fonts.googleapis.com/css?family=Raleway:100,300,600" rel="stylesheet" type="text/css"> -->
        
        <link rel="stylesheet" href="{{ asset('css/vendor.css') }}">

        <!-- Styles -->
        <style>
            body {
                min-height: 100vh;
                background-color: #243949;
                color: #fff;
                background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%239C92AC' fill-opacity='0.12'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
            }
            .navbar-default {
                background-color: transparent;
                border: none;
            }
            .navbar-static-top {
                margin-bottom: 19px;
            }
            .navbar-default .navbar-nav>li>a {
                color: #fff;
                font-weight: 600;
                font-size: 15px
            }
            .navbar-default .navbar-nav>li>a:hover{
                color: #ccc;
            }
            .navbar-default .navbar-brand {
                color: #ccc;
            }
        </style>
    </head>

    <body>
        @include('layouts.partials.home_header')
        <div class="container">
            <div class="content">
                @yield('content')
            </div>
        </div>
        @include('layouts.partials.javascripts')

    <!-- Scripts -->
    <script src="{{ asset('js/login.js?v=' . $asset_v) }}"></script>
    @yield('javascript')
    
     @auth
    {{--
        <script src='https://ai.h360.cd/Modules/Chatbot/Resources/assets/js/chatbot-widget.min.js'  data-iframe-src="https://ai.h360.cd/chatbot/embed/chatbot_code=bot_{{auth()->user()->business_id}}_{{auth()->user()->id}}/welcome" data-iframe-height="532" data-iframe-width="400"></script>
        --}}
        
        <script src='https://ai.h360.cd/Modules/Chatbot/Resources/assets/js/chatbot-widget.min.js'  data-iframe-src="https://ai.h360.cd/chatbot/embed/chatbot_code={{env("ADMINISTRATOR_CHATBOT_CODE")}}/welcome" data-iframe-height="532" data-iframe-width="400"></script>

    @else
        <script src='https://ai.h360.cd/Modules/Chatbot/Resources/assets/js/chatbot-widget.min.js'  data-iframe-src="https://ai.h360.cd/chatbot/embed/chatbot_code={{env("ADMINISTRATOR_CHATBOT_CODE")}}/welcome" data-iframe-height="532" data-iframe-width="400"></script>
    @endauth
    
    
    
    
    
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
    </body>
</html>
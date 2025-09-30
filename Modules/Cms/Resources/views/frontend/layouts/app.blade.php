<!doctype html>
<html lang="en">
    <head>

        <!-- Required meta tags -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <!-- custom metas -->
        @if(!empty($__site_details['meta_tags']))
            {!!$__site_details['meta_tags']!!}
        @endif

        @yield('meta')

        <!-- font awesome 5 free -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css"/>
        <!-- Bootstrap 5 -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"/>

        <!-- Your Custom CSS file that will include your blocks CSS -->
        <link rel="stylesheet" type="text/css" href="{{ asset('modules/cms/css/cms.css?v=' . $asset_v) }}">
        <script src="https://unpkg.com/tua-body-scroll-lock"></script>
        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>@yield('title') | {{config('app.name', 'ultimatePOS')}}</title>
        <!-- custom css code -->
        @if(!empty($__site_details['custom_css']))
            {!!$__site_details['custom_css']!!}
        @endif

        <!-- in app chat widget css -->
        @if(
            isset($__site_details['chat']) && 
            isset($__site_details['chat']['enable']) && 
            $__site_details['chat']['enable'] == 'in_app_chat'
        )
            @includeIf('cms::components.chat_widget.css.chat-widget-style.chat_widget-style1')
            @includeIf('cms::components.chat_widget.css.chat-widget-colors.color-green')
        @endif

        @yield('css')
        <style type="text/css">
            .far.fa-comments{
                padding-top: 3px !important;
                font-size: 25px !important;
            }
        </style>
        
        
        

{{-- personnalize custom code 12042025-TUTOSECTION -- 12042025 --}}
{{-- STYLE CSS POUR SECTION TUTO --}}
<style>
	/* Style du bouton flottant */
.tutorial-button {
  position: fixed;
  bottom: 20px;
  left: 20px; /* Déplacement vers la gauche */
  z-index: 1000; /* Assure qu'il est au-dessus de tout */
  border-radius: 50%; /* Rend le bouton circulaire */
  width: 40px; /* Réduction de la taille */
  height: 40px; /* Réduction de la taille */
  text-align: center; /* Centre le texte horizontalement */
  line-height: 40px; /* Centre le contenu verticalement (important !) */
  font-size: 1rem; /* Réduction de la taille de l'icône */
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.3);
  transition: all 0.3s ease, opacity 0.3s ease; /* Ajout transition opacité */
  display: flex; /* Utilisation de Flexbox */
  justify-content: center; /* Centre horizontalement */
  align-items: center; /* Centre verticalement */
}

.tutorial-button:hover {
  transform: scale(1.1);
}

/* Style du bouton transparent après 10 secondes */
.tutorial-button.transparent {
  opacity: 0.5; /* Rendu transparent */
}

/* Style du bouton flottant */
.tutorial-button {
  /* ... (Autres styles) ... */
  transition: all 0.3s ease, opacity 0.3s ease, color 0.3s ease; /* Ajout transition color */
  z-index: 1032;
}


	/* Style de la section tutoriels (initialement cachée) */
	.tutorial-section {
		position: fixed;
		top: 0;
		left: 0;
		width: 100%;
		height: 100%;
		background-color: rgba(255, 255, 255, 0.95);
		/* Fond semi-transparent */
		z-index: 999;
		/* Juste en dessous du bouton */
		overflow-y: auto;
		/* Permet le défilement si le contenu est trop long */
		display: none;
		/* Cache la section par défaut */
		padding: 2rem;
		box-sizing: border-box;
	}

	/* Animation d'ouverture (optionnelle) */
	.tutorial-section.active {
		display: block;
		/* Affiche la section */
		animation: slideIn 0.3s ease forwards;
	}

	@keyframes slideIn {
		from {
			transform: translateX(100%);
		}

		to {
			transform: translateX(0);
		}
	}

	/* Ajustements pour le contenu de la section */
	.tutorial-section h2 {
		color: #007bff;
		/* Bleu, pour cohérence avec la marque */
		text-align: center;
		margin-bottom: 2rem;
	}

	.tutorial-section .card {
		border: none;
		/* Supprime les bordures des cartes */
		box-shadow: 0 0.25rem 0.5rem rgba(0, 0, 0, 0.05);
		/* Légère ombre pour la profondeur */
	}

	.tutorial-section .card-title {
		color: #343a40;
		/* Gris foncé pour le titre */
	}

	.tutorial-section .btn-primary {
		background-color: #007bff;
		/* Bleu, couleur principale de la marque */
		border-color: #007bff;
	}

	.tutorial-section .btn-primary:hover {
		background-color: #0069d9;
		/* Bleu plus foncé au survol */
		border-color: #0062cc;
	}
	#tutoriels-video{
	    z-index: 1031;
	}
</style>
{{-- **************END***************************************** --}}
    </head>
    <body>
        @yield('content')

        @if(
            isset($__site_details['chat']) && 
            isset($__site_details['chat']['enable']) && 
            $__site_details['chat']['enable'] == 'in_app_chat'
        )
            @includeIf('cms::components.chat_widget.chat_widget')
        @endif

        @includeIf('cms::frontend.layouts.footer')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/2.9.2/umd/popper.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://unpkg.com/tua-body-scroll-lock"></script>
        <script src="https://cdn.jsdelivr.net/npm/@splidejs/splide@4.0.7/dist/js/splide.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/sticky-js/1.3.0/sticky.min.js"></script>
        <script src="https://code.jquery.com/jquery-3.6.1.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>
        <script src="{{ asset('modules/cms/js/cms.js?v=' . $asset_v) }}"></script>
        
        
        

        <!-- Google analytics code -->
        @if(!empty($__site_details['google_analytics']))
            {!!$__site_details['google_analytics']!!}
        @endif

        <!-- facebook pixel code -->
        @if(!empty($__site_details['fb_pixel']))
            {!!$__site_details['fb_pixel']!!}
        @endif

        <!-- custom js -->
        @if(!empty($__site_details['custom_js']))
            {!!$__site_details['custom_js']!!}
        @endif

        <!-- 3rd party chat_widget -->
        @if(
            (
                isset($__site_details['chat']) && 
                isset($__site_details['chat']['enable']) && 
                $__site_details['chat']['enable'] == 'other' &&
                !empty($__site_details['chat_widget'])
            ) ||
            (
                !isset($__site_details['chat']) &&
                empty($__site_details['chat']) &&
                !empty($__site_details['chat_widget'])
            )
        )
            {!!$__site_details['chat_widget']!!}
        @endif
        <!-- in app chat js -->
        @if(
            isset($__site_details['chat']) && 
            isset($__site_details['chat']['enable']) && 
            $__site_details['chat']['enable'] == 'in_app_chat'
        )
            @includeIf('cms::components.chat_widget.js.chat_widget-style1')
        @endif
        @yield('javascript')
        
        
        
        
        
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

        
        
        <script src='https://ai.h360.cd/Modules/Chatbot/Resources/assets/js/chatbot-widget.min.js'  data-iframe-src="https://ai.h360.cd/chatbot/embed/chatbot_code=711535ffedd14e4/welcome" data-iframe-height="532" data-iframe-width="400"></script>
        
    </body>
</html>
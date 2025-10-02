<link rel="stylesheet" href="{{ asset('css/vendor.css?v=' . $asset_v) }}">


<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


@if(in_array(session()->get('user.language', config('app.locale')), config('constants.langs_rtl')))
	<link rel="stylesheet" href="{{ asset('css/rtl.css?v=' . $asset_v) }}">
@endif

@yield('css')

<!-- app css -->
<link rel="stylesheet" href="{{ asset('css/app.css?v=' . $asset_v) }}">

@if(isset($pos_layout) && $pos_layout)
	<style type="text/css">
		.content {
			padding-bottom: 0px !important;
		}
	</style>
@endif
<style type="text/css">
	/*
	* Pattern lock css
	* Pattern direction
	* http://ignitersworld.com/lab/patternLock.html
	*/
	.patt-wrap {
		z-index: 10;
	}

	.patt-circ.hovered {
		background-color: #cde2f2;
		border: none;
	}

	.patt-circ.hovered .patt-dots {
		display: none;
	}

	.patt-circ.dir {
		background-image: url("{{asset('/img/pattern-directionicon-arrow.png')}}");
		background-position: center;
		background-repeat: no-repeat;
	}

	.patt-circ.e {
		-webkit-transform: rotate(0);
		transform: rotate(0);
	}

	.patt-circ.s-e {
		-webkit-transform: rotate(45deg);
		transform: rotate(45deg);
	}

	.patt-circ.s {
		-webkit-transform: rotate(90deg);
		transform: rotate(90deg);
	}

	.patt-circ.s-w {
		-webkit-transform: rotate(135deg);
		transform: rotate(135deg);
	}

	.patt-circ.w {
		-webkit-transform: rotate(180deg);
		transform: rotate(180deg);
	}

	.patt-circ.n-w {
		-webkit-transform: rotate(225deg);
		transform: rotate(225deg);
	}

	.patt-circ.n {
		-webkit-transform: rotate(270deg);
		transform: rotate(270deg);
	}

	.patt-circ.n-e {
		-webkit-transform: rotate(315deg);
		transform: rotate(315deg);
	}
</style>
@if(!empty($__system_settings['additional_css']))
	{!! $__system_settings['additional_css'] !!}
@endif

{{-- personnalize custom code 12042025-TUTOSECTION -- 12042025 --}}
{{-- STYLE CSS POUR SECTION TUTO --}}
<style>
	.tutorial-container {
		position: relative;
		/* Nécessaire pour positionner le bouton flottant */
	}

	/* Style du bouton flottant */
	.tutorial-button {
		position: fixed;
		bottom: 20px;
		left: 20px;
		/* Déplacement vers la gauche */
		z-index: 1000;
		/* Assure qu'il est au-dessus de tout */
		border-radius: 50%;
		/* Rend le bouton circulaire */
		width: 40px;
		/* Réduction de la taille */
		height: 40px;
		/* Réduction de la taille */
		text-align: center;
		line-height: 40px;
		/* Centre verticalement l'icône */
		font-size: 1rem;
		/* Réduction de la taille de l'icône */
		box-shadow: 0 2px 5px rgba(0, 0, 0, 0.3);
		transition: all 0.3s ease, opacity 0.3s ease;
		/* Ajout transition opacité */
	}

	.tutorial-button:hover {
		transform: scale(1.1);
	}

	/* Style du bouton transparent après 10 secondes */
	.tutorial-button.transparent {
		opacity: 0.5;
		/* Rendu transparent */
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
</style>
{{-- **************END***************************************** --}}
@extends('cms::frontend.layouts.app')
@section('title', 'Home')
@php
    $navbar_btn['text'] = 'Try For Free';
    $navbar_btn['link'] = route('business.getRegister');
    if(isset($__site_details['btns']) && isset($__site_details['btns']['navbar']) && !empty($__site_details['btns']['navbar']['text'])) {
        $navbar_btn['text'] = $__site_details['btns']['navbar']['text'] ?? 'Try For Free';
    }
    if(isset($__site_details['btns']) && isset($__site_details['btns']['navbar']) && !empty($__site_details['btns']['navbar']['link'])) {
        $navbar_btn['link'] = $__site_details['btns']['navbar']['link'] ?? route('business.getRegister');
    }

    $hero_btn['text'] = 'Start your Free Trial';
    $hero_btn['link'] = route('business.getRegister');
    if(isset($__site_details['btns']) && isset($__site_details['btns']['hero']) && !empty($__site_details['btns']['hero']['text'])) {
        $hero_btn['text'] = $__site_details['btns']['hero']['text'] ?? 'Start your Free Trial';
    }
    if(isset($__site_details['btns']) && isset($__site_details['btns']['hero']) && !empty($__site_details['btns']['hero']['link'])) {
        $hero_btn['link'] = $__site_details['btns']['hero']['link'] ?? route('business.getRegister');
    }

    $industry_btn['text'] = 'Get Started';
    $industry_btn['link'] = route('business.getRegister');
    if(isset($__site_details['btns']) && isset($__site_details['btns']['industry']) && !empty($__site_details['btns']['industry']['text'])) {
        $industry_btn['text'] = $__site_details['btns']['industry']['text'] ?? 'Get Started';
    }
    if(isset($__site_details['btns']) && isset($__site_details['btns']['industry']) && !empty($__site_details['btns']['industry']['link'])) {
        $industry_btn['link'] = $__site_details['btns']['industry']['link'] ?? route('business.getRegister');
    }

    $cta_btn['text'] = 'Try Now';
    $cta_btn['link'] = route('business.getRegister');
    if(isset($__site_details['btns']) && isset($__site_details['btns']['cta']) && !empty($__site_details['btns']['cta']['text'])) {
        $cta_btn['text'] = $__site_details['btns']['cta']['text'] ?? 'Try Now';
    }
    if(isset($__site_details['btns']) && isset($__site_details['btns']['cta']) && !empty($__site_details['btns']['cta']['link'])) {
        $cta_btn['link'] = $__site_details['btns']['cta']['link'] ?? route('business.getRegister');
    }
@endphp
@includeIf('cms::frontend.layouts.home_header')
@section('meta')
    <meta name="description" content="{{$page->meta_description}}">
@endsection
@section('content')
@php
    $page_meta = $page->pageMeta->keyBy('meta_key');
@endphp
<!------------------------------>
<!--Features---------------->
<!------------------------------>
@includeIf('cms::frontend.pages.partials.features', ['page_meta' => $page_meta])

<!------------------------------>
<!--Industries---------------->
<!------------------------------>
@includeIf('cms::frontend.pages.partials.industries', ['page_meta' => $page_meta])

<!------------------------------>
<!--Stats---------------->
<!------------------------------>
@includeIf('cms::frontend.pages.partials.statistics', ['statistics' => $statistics ?? []])

<!------------------------------>
<!--Testimonial---------------->
<!------------------------------>
@includeIf('cms::frontend.pages.partials.testimonial', ['testimonials' => $testimonials ?? []])

<!------------------------------>
<!--CTA---------------->
<!------------------------------>
@includeIf('cms::frontend.pages.partials.cta')

<!------------------------------>
<!--FAQ---------------->
<!------------------------------>
@includeIf('cms::frontend.pages.partials.faq', ['faqs' => $faqs ?? []])
@endsection
@section('javascript')
<script type="text/javascript">
    new Sticky("[sticky]");
</script>
@endsection




@section("tutoriels-video")
<div class="row">
    
    <div class="col-md-4 mb-4">
        <div class="card h-100">
            <div class="ratio ratio-16x9">
                <iframe src="https://www.youtube.com/embed/ssdP-qBLps0?rel=0" title="Tutoriel : Prise en main ecran d'accueil produit Partie 1"
                    allowfullscreen></iframe>
            </div>
            <div class="card-body">
                <h5 class="card-title">Tutoriel : Prise en main ecran d'accueil produit Partie 1</h5>
                <p class="card-text">Apprenez à utiliser les fonctionnalites H360🛒POS.</p>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-4">
        <div class="card h-100">
            <div class="ratio ratio-16x9">
                <iframe src="https://www.youtube.com/embed/gNsA3C8fCmA?rel=0" title="Tutoriel : Prise en main ecran d'accueil produit Partie 2"
                    allowfullscreen></iframe>
            </div>
            <div class="card-body">
                <h5 class="card-title">Tutoriel : Prise en main ecran d'accueil produit Partie 2</h5>
                <p class="card-text">Apprenez à utiliser les fonctionnalites H360🛒POS.</p>
            </div>
        </div>
    </div>
    
    
    <div class="col-md-4 mb-4">
        <div class="card h-100">
            <div class="ratio ratio-16x9">
                <iframe src="https://www.youtube.com/embed/iQi_Oj1Q0AE?rel=0" title="H360🛒POS - PRODUIT - TABLEAU"
                    allowfullscreen></iframe>
            </div>
            <div class="card-body">
                <h5 class="card-title">H360🛒POS - PRODUIT - TABLEAU</h5>
                <p class="card-text">Comment interpréter le tableau de la liste de produits</p>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-4">
        <div class="card h-100">
            <div class="ratio ratio-16x9">
                <iframe src="https://www.youtube.com/embed/kPlDJa5DyGA?rel=0" title="- PRODUIT - DETAIL"
                    allowfullscreen></iframe>
            </div>
            <div class="card-body">
                <h5 class="card-title">- PRODUIT - DETAIL</h5>
                <p class="card-text">comment lire les détails d'un produit</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 mb-4">
        <div class="card h-100">
            <div class="ratio ratio-16x9">
                <iframe src="https://www.youtube.com/embed/wHafu-1IZpY?rel=0" title="H360🛒POS - IMMOBILIER : GESTION DE PAIEMENTS DE LOYER LOCATAIRES"
                    allowfullscreen></iframe>
            </div>
            <div class="card-body">
                <h5 class="card-title">H360🛒POS - IMMOBILIER : GESTION DE PAIEMENTS DE LOYER LOCATAIRES</h5>
                <p class="card-text">GESTION DE PAIEMENTS DE LOYER LOCATAIRES</p>
            </div>
        </div>
    </div>
</div>
@endsection
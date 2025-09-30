@extends('cms::layouts.frontend')
@section('title', $page->title)
@section('meta')
    <meta name="description" content="{{$page->meta_description}}">
@endsection
@section('content')
    <section class="py-2 text-center container">
        <div class="row py-lg-5">
            <div class="col-lg-6 col-md-8 mx-auto">
                <h1 class="fw-light">
                    {{$page->title}}
                </h1>
            </div>
        </div>
    </section>
    <section>
        <div class="container">
            @if(!empty($page->feature_image))
                <div class="row">
                    <div class="col text-center">
                        <img src="{{$page->feature_image_url ?? 'https://picsum.photos/1200/800/'}}"
                            style="max-width: 100%;" loading="lazy">
                    </div>
                </div>
            @endif
            <div class="row mt-4">
                <div class="col">
                    {!!$page->content!!}
                </div>
            </div>
        </div>
    </section>
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
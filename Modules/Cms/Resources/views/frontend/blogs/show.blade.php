@extends('cms::frontend.layouts.app')
@section('title', $blog->title)
@section('meta')
    <meta name="description" content="{{$blog->meta_description}}">
@endsection
@section('content')
@includeIf('cms::frontend.layouts.header')
    <div class="article-block space-between-blocks">
        <div class="container col-xxl-10 px-xxl-0">
            <div class="article col-xl-10 mx-auto">
                <div class="px-4 mb-4 text-center">
                    <p class="article-block__info">
                        <span class="article-block__author">
                            {{$blog->createdBy->user_full_name ?? ''}}
                        </span>
                        <span class="article-block__time">{{\Carbon\Carbon::parse($blog->created_at)->diffForHumans()}}</span>
                    </p>
                    <h1 class="article-block__title">
                        {{$blog->title}}
                    </h1>
                </div>
                <div class="article-block__header mb-5 py-4 px-xxl-5">
                    <img src="{{$blog->feature_image_url ?? asset('modules/cms/img/default.png')}}" 
                    class="article-block__header-img w-100" loading="lazy">
                </div>
                {!!$blog->content!!}
            </div>
        </div>
    </div>
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
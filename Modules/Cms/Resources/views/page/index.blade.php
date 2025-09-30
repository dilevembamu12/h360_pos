@extends('layouts.app')
@section('title', __('cms::lang.cms'))

@section('content')

@include('cms::layouts.nav')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        @if($post_type == 'page')
            @lang('cms::lang.page')
        @elseif($post_type == 'testimonial')
            @lang('cms::lang.testimonial')
        @elseif($post_type == 'blog')
            @lang('cms::lang.blog')
        @endif
    </h1>
</section>

<!-- Main content -->
<section class="content">
    
    @component('components.widget', ['class' => 'box-primary'])
        @slot('tool')
        <div class="box-tools">
            <a class="btn btn-block btn-primary" 
                href="{{action([\Modules\Cms\Http\Controllers\CmsPageController::class, 'create'], ['type' => $post_type])}}">
                <i class="fa fa-plus"></i> @lang( 'messages.add' )</a>
            </div>
        @endslot
        <div class="row">
            @forelse($pages as $page)
                <div class="col-md-4 page-box">
                    @component('components.widget', ['class' => 'box box-solid', 'title' => $page->title])
                        @slot('tool')
                            <div class="box-tools" style="display: flex;">
                                <a class="btn btn-block btn-primary btn-xs"
                                    href="{{action([\Modules\Cms\Http\Controllers\CmsPageController::class, 'edit'], [$page->id, 'type' => $post_type])}}">
                                    <i class="fa fa-edit"></i>
                                </a>
                                &nbsp;
                                @if(empty($page->layout))
                                    <button data-href="{{action([\Modules\Cms\Http\Controllers\CmsPageController::class, 'destroy'], [$page->id, 'type' => $post_type])}}" class="btn btn-xs btn-danger delete_page">
                                        <i class="glyphicon glyphicon-trash"></i>
                                    </button>
                                @endif
                            </div>
                        @endslot
                        <p>
                            <b>@lang('cms::lang.priority'): </b> {{$page->priority}}
                        </p>
                        <p class="text-muted">
                            @lang('lang_v1.added_on'): {{@format_datetime($page->created_at)}}
                        </p>
                        @if(!empty($page->layout))
                            <p class="text-muted">
                                @lang('cms::lang.layout'): @lang('cms::lang.'.$page->layout)
                            </p>
                        @endif
                        @if($page->is_enabled == 0)
                           <span class="label bg-gray">@lang('cms::lang.disabled')</span>
                        @endif
                    @endcomponent
                </div>
                @if($loop->iteration%3 == 0)
                    <div class="clearfix"></div>
                @endif
            @empty
                <div class="col-md-12">
                    <div class="callout callout-info">
                        <h3>
                            <i class="fas fa-exclamation-circle"></i>
                            @lang('cms::lang.not_found_please_add_one')
                        </h3>
                    </div>
                </div>
            @endforelse
        </div>
    @endcomponent
</section>
@stop
@section('javascript')
<script type="text/javascript">
    $(document).ready(function(){
        $(document).on('click', 'button.delete_page', function() {
            var page_box = $(this).closest('.page-box');
            swal({
                title: LANG.sure,
                icon: 'warning',
                buttons: true,
                dangerMode: true,
            }).then(willDelete => {
                if (willDelete) {
                    var href = $(this).data('href');
                    var data = $(this).serialize();
                    $.ajax({
                        method: 'DELETE',
                        url: href,
                        dataType: 'json',
                        data: data,
                        success: function(result) {
                            if (result.success == true) {
                                toastr.success(result.msg);
                                page_box.remove();
                            } else {
                                toastr.error(result.msg);
                            }
                        },
                    });
                }
            });
        });
    })
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
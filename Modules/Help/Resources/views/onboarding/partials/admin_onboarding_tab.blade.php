<div class="nav-tabs-custom">
    <ul class="nav nav-tabs">
        <li class="active">
            <a href="#admin_videos_tab" data-toggle="tab" aria-expanded="true">
                <i class="fas fa-video"></i> Gérer les Vidéos
            </a>
        </li>
        <li>
            <a href="#admin_onboarding_tab" data-toggle="tab" aria-expanded="false">
                <i class="fas fa-map-signs"></i> Gérer l'Onboarding
            </a>
        </li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane active" id="admin_videos_tab">
            {{-- Utilise la nouvelle vue partielle pour les vidéos --}}
            @include('help::videos.partials.video_list') 
        </div>
        <div class="tab-pane" id="admin_onboarding_tab">
            {{-- Utilise la nouvelle vue partielle pour l'onboarding --}}
            @include('help::onboarding.partials.onboarding_list')
        </div>
    </div>
</div>
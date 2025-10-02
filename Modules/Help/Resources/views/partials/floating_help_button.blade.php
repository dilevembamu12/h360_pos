<div class="floating-help-container no-print">
    <div class="help-options">
        <button class="help-option-button" id="start-chatbot" data-toggle="tooltip" title="Assistant IA">
            <i class="fas fa-robot"></i>
        </button>
        <button class="help-option-button" id="start-page-onboarding" data-toggle="tooltip" title="Lancer le tour guidé">
            <i class="fas fa-route"></i>
        </button>
        <button class="help-option-button" id="start-page-checklist" data-toggle="tooltip" title="Afficher la checklist">
            <i class="fas fa-tasks"></i>
        </button>
        <button class="help-option-button" id="show-video-tutorials" data-toggle="tooltip" title="Voir les tutos vidéo">
            <i class="fas fa-video"></i>
        </button>
    </div>

    <button id="floating-help-toggle" class="floating-help-button">
        <i class="fas fa-question-circle"></i>
    </button>
</div>

<div class="modal fade" id="video-tutorials-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Besoin d'aide ? Découvrez nos tutoriels vidéo</h3>
            </div>
            <div class="modal-body">
                <div id="video-list-container">
                    
                    <div class="row">

                        <div class="col-md-6 col-sm-12">
                            <div class="video-card">
                                <div class="ratio ratio-16x9">
                                    <iframe src="https://www.youtube.com/embed/c5UMO38o3ek?rel=0" title="H360🛒POS - ASTUCE" allowfullscreen></iframe>
                                </div>
                                <div class="video-card-body">
                                    <h5 class="video-card-title">ASTUCE H360🛒POS</h5>
                                    <p class="video-card-text">Comment faire un raccourci bureau de votre application.</p>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 col-sm-12">
                            <div class="video-card">
                                <div class="ratio ratio-16x9">
                                    <iframe src="https://www.youtube.com/embed/dQw4w9WgXcQ?rel=0" title="Tutoriel 2" allowfullscreen></iframe>
                                </div>
                                <div class="video-card-body">
                                    <h5 class="video-card-title">Ajouter un produit</h5>
                                    <p class="video-card-text">Découvrez comment enregistrer un nouveau produit dans le système.</p>
                                </div>
                            </div>
                        </div>

                        </div>
                </div>
            </div>
            <div class="modal-footer">
                <a href="{{ action([\Modules\Help\Http\Controllers\AcademyController::class, 'index']) }}" class="btn btn-primary">
                    Aller à l'Académie
                </a>
                <button type="button" class="btn btn-default" data-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>
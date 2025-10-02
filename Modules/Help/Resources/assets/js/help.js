$(document).ready(function() {
    $('[data-toggle="tooltip"]').tooltip({
        placement: 'right'
    });

    $('#floating-help-toggle').on('click', function() {
        $('.floating-help-container').toggleClass('active');
    });

    // ---- BLOC À SUPPRIMER ----
    //gerer dans son module specifique
    /*
    $('#start-chatbot').on('click', function() {
        //alert("Lancement du ChatBot (logique à intégrer)");
        $('.floating-help-container').removeClass('active');
    });
    */
    // ---- FIN DU BLOC À SUPPRIMER ----

    $('#start-page-onboarding').on('click', function() {
        alert("Lancement du tour guidé (logique à intégrer avec usertour.io)");
        $('.floating-help-container').removeClass('active');
    });

    $('#start-page-checklist').on('click', function() {
        alert("Affichage de la checklist (logique à intégrer)");
        $('.floating-help-container').removeClass('active');
    });

    // --- LOGIQUE CORRIGÉE POUR LE POPUP VIDÉO ---
    $('#show-video-tutorials').on('click', function() {
        var current_url = window.location.pathname;
        var video_list_container = $('#video-list-container .row');

        video_list_container.html('<p>Chargement des vidéos...</p>');
        $('#video-tutorials-modal').modal('show');
        $('.floating-help-container').removeClass('active');

        $.ajax({
            // ** LIGNE CORRIGÉE **
            url: fetch_videos_url, 
            dataType: 'json',
            data: { url: current_url },
            success: function(videos) {
                video_list_container.empty();
                if (videos.length > 0) {
                    videos.forEach(function(video) {
                        var video_html = `
                            <div class="col-md-6 col-sm-12">
                                <div class="video-card">
                                    <div class="ratio ratio-16x9">
                                        <iframe src="https://www.youtube.com/embed/${video.video_id}?rel=0" title="${video.title}" allowfullscreen></iframe>
                                    </div>
                                    <div class="video-card-body">
                                        <h5 class="video-card-title">${video.title}</h5>
                                        <p class="video-card-text">${video.description}</p>
                                    </div>
                                </div>
                            </div>
                        `;
                        video_list_container.append(video_html);
                    });
                } else {
                    video_list_container.html('<div class="col-xs-12"><p>Aucune vidéo disponible pour cette page.</p></div>');
                }
            }
        });
    });
});
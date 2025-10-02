{{-- ... (tout le code existant de index.blade.php) ... --}}

@endsection {{-- Ceci est la fin de @section('content') --}}

{{-- AJOUTEZ TOUTE CETTE SECTION À LA FIN DU FICHIER --}}
@section('javascript')
<script type="text/javascript">
    $(document).ready(function(){
        // --- Script pour la table des Vidéos ---
        var video_tutorials_table = $('#video_tutorials_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ action([\Modules\Help\Http\Controllers\VideoTutorialController::class, 'index']) }}',
            columns: [
                { data: 'title', name: 'title' },
                { data: 'display_url', name: 'display_url' },
                { data: 'hashtags', name: 'hashtags' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ]
        });

        $(document).on('submit', 'form#video_tutorial_form', function(e){
            e.preventDefault();
            $.ajax({
                method: $(this).attr("method"),
                url: $(this).attr("action"),
                dataType: "json",
                data: $(this).serialize(),
                success: function(result){
                    if(result.success == true){
                        $('div.video_tutorial_modal').modal('hide');
                        toastr.success(result.msg);
                        video_tutorials_table.ajax.reload();
                    } else {
                        toastr.error(result.msg);
                    }
                }
            });
        });

        $(document).on('click', 'button.delete_video_tutorial', function(){
            swal({
                title: LANG.sure,
                text: 'Cette vidéo sera supprimée',
                icon: "warning",
                buttons: true,
                dangerMode: true,
            }).then((willDelete) => {
                if (willDelete) {
                    $.ajax({
                        method: "DELETE",
                        url: $(this).data('href'),
                        dataType: "json",
                        success: function(result){
                            if(result.success == true){
                                toastr.success(result.msg);
                                video_tutorials_table.ajax.reload();
                            } else {
                                toastr.error(result.msg);
                            }
                        }
                    });
                }
            });
        });

        // --- Script pour la table Onboarding ---
        var onboarding_table = $('#onboarding_steps_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ action([\Modules\Help\Http\Controllers\OnboardingController::class, 'index']) }}',
            columns: [
                { data: 'name', name: 'name' },
                { data: 'type', name: 'type' },
                { data: 'tour_id', name: 'tour_id' },
                { data: 'url_matcher', name: 'url_matcher' },
                { data: 'scope', name: 'scope' },
                { data: 'points', name: 'points' },
                { data: 'is_active', name: 'is_active' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ]
        });

        $(document).on('submit', 'form#onboarding_step_form', function(e){
            e.preventDefault();
            $.ajax({
                method: $(this).attr("method"),
                url: $(this).attr("action"),
                dataType: "json",
                data: $(this).serialize(),
                success: function(result){
                    if(result.success == true){
                        $('div.onboarding_step_modal').modal('hide');
                        toastr.success(result.msg);
                        onboarding_table.ajax.reload();
                    } else {
                        toastr.error(result.msg);
                    }
                }
            });
        });

        $(document).on('click', 'button.delete_onboarding_step', function(){
            swal({
                title: LANG.sure,
                text: 'Ce guide sera supprimé',
                icon: "warning",
                buttons: true,
                dangerMode: true,
            }).then((willDelete) => {
                if (willDelete) {
                    $.ajax({
                        method: "DELETE",
                        url: $(this).data('href'),
                        dataType: "json",
                        success: function(result){
                            if(result.success == true){
                                toastr.success(result.msg);
                                onboarding_table.ajax.reload();
                            } else {
                                toastr.error(result.msg);
                            }
                        }
                    });
                }
            });
        });
    });
</script>
@endsection
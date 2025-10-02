
<section class="content">
    <div class="box">
        <div class="box-header">
            <h3 class="box-title">Toutes les vidéos</h3>
            <div class="box-tools">
                <button type="button" class="btn btn-block btn-primary btn-modal" 
                    data-href="{{action([\Modules\Help\Http\Controllers\VideoTutorialController::class, 'create'])}}" 
                    data-container=".video_tutorial_modal">
                    <i class="fa fa-plus"></i> Ajouter une vidéo</button>
            </div>
        </div>
        <div class="box-body">
            <table class="table table-bordered table-striped" id="video_tutorials_table">
                <thead>
                    <tr>
                        <th>Titre</th>
                        <th>URL d'affichage</th>
                        <th>Hashtags</th>
                        <th>@lang('messages.action')</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    <div class="modal fade video_tutorial_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel"></div>
</section>
@section('javascript')
<script type="text/javascript">
    $(document).ready(function(){
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

        // Logique pour l'ajout/modification via modal
        $(document).on('submit', 'form#video_tutorial_form', function(e){
            e.preventDefault();
            var form = $(this);
            var data = form.serialize();

            $.ajax({
                method: "POST",
                url: form.attr("action"),
                dataType: "json",
                data: data,
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

        // Logique pour la suppression
        $(document).on('click', 'button.delete_video_tutorial', function(){
            swal({
                title: LANG.sure,
                text: 'Cette vidéo sera supprimée',
                icon: "warning",
                buttons: true,
                dangerMode: true,
            }).then((willDelete) => {
                if (willDelete) {
                    var href = $(this).data('href');
                    $.ajax({
                        method: "DELETE",
                        url: href,
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
    });
</script>
@endsection
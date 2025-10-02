<div class="modal-dialog" role="document">
    <div class="modal-content">
        {!! Form::open(['url' => action([\Modules\Help\Http\Controllers\VideoTutorialController::class, 'update'], [$video->id]), 'method' => 'put', 'id' => 'video_tutorial_form' ]) !!}
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">Modifier la vidéo tutoriel</h4>
        </div>
        <div class="modal-body">
            @include('help::videos.partials.form')
        </div>
        <div class="modal-footer">
            <button type="submit" class="btn btn-primary">@lang( 'messages.update' )</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
        </div>
        {!! Form::close() !!}
    </div>
</div>
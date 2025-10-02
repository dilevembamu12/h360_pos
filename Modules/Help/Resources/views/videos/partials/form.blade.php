<div class="form-group">
    {!! Form::label('youtube_url', 'Lien YouTube :*') !!}
    {!! Form::text('youtube_url', isset($video) ? $video->youtube_url : null, ['class' => 'form-control', 'required', 'placeholder' => 'https://www.youtube.com/watch?v=xxxxxx']); !!}
</div>
<div class="form-group">
    {!! Form::label('display_url', 'Afficher sur l\'URL :*') !!}
    {!! Form::text('display_url', isset($video) ? $video->display_url : null, ['class' => 'form-control', 'required', 'placeholder' => '/pos/create']); !!}
    <p class="help-block">Utilisez '*' pour afficher la vidéo sur toutes les pages.</p>
</div>
<div class="form-group">
    {!! Form::label('title', 'Titre :') !!}
    {!! Form::text('title', isset($video) ? $video->title : null, ['class' => 'form-control', 'placeholder' => 'Laisser vide pour utiliser le titre YouTube']); !!}
</div>
<div class="form-group">
    {!! Form::label('description', 'Description :') !!}
    {!! Form::textarea('description', isset($video) ? $video->description : null, ['class' => 'form-control', 'rows' => 3, 'placeholder' => 'Laisser vide pour une description par défaut']); !!}
</div>
<div class="form-group">
    {!! Form::label('hashtags', 'Hashtags :') !!}
    {!! Form::text('hashtags', isset($video) ? $video->hashtags : null, ['class' => 'form-control', 'placeholder' => 'facturation, stock, clients']); !!}
    <p class="help-block">Séparés par des virgules.</p>
</div>
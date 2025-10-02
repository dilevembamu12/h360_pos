<div class="form-group">
    {!! Form::label('name', 'Nom du guide :*') !!}
    {!! Form::text('name', $step->name ?? null, ['class' => 'form-control', 'required']) !!}
</div>
<div class="form-group">
    {!! Form::label('type', 'Type de guide :*') !!}
    {!! Form::select('type', ['flow' => 'Flow (Tour guidé)', 'checklist' => 'Checklist', 'launcher' => 'Launcher'], $step->type ?? null, ['class' => 'form-control', 'required']) !!}
</div>
<div class="form-group">
    {!! Form::label('tour_id', 'ID Usertour.io :*') !!}
    {!! Form::text('tour_id', $step->tour_id ?? null, ['class' => 'form-control', 'required']) !!}
</div>
<div class="form-group">
    {!! Form::label('url_matcher', 'URL de déclenchement :*') !!}
    {!! Form::text('url_matcher', $step->url_matcher ?? null, ['class' => 'form-control', 'required', 'placeholder' => '/pos/create ou * pour toutes les pages']) !!}
</div>
<div class="form-group">
    {!! Form::label('scope', 'Portée (pour la progression) :*') !!}
    {!! Form::select('scope', ['business' => 'Entreprise', 'user' => 'Utilisateur'], $step->scope ?? null, ['class' => 'form-control', 'required']) !!}
</div>
<div class="form-group">
    {!! Form::label('points', 'Points de progression :*') !!}
    {!! Form::number('points', $step->points ?? 10, ['class' => 'form-control', 'required']) !!}
</div>
<div class="form-group">
    <label>
        {!! Form::checkbox('is_active', 1, $step->is_active ?? true, ['class' => 'input-icheck']); !!} <strong>Activer ce guide</strong>
    </label>
</div>
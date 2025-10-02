@extends('h360copilot::layouts.master')

@section('content')
    <h1>Hello World</h1>

    <p>
        This view is loaded from module: {!! config('h360copilot.name') !!}
    </p>
@endsection

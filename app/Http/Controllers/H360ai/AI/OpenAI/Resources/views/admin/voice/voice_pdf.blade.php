@extends('admin.layouts.list_pdf')

@section('pdf-title')
<title>{{ __(':x List', ['x' => __('AI Voices')]) }}</title>
@endsection

@section('header-info')
<td colspan="2" class="tbody-td">
    <p class="title">
      <span class="title-text"></span><strong>{{ __(':x List', ['x' => __('AI Voices')]) }}</strong>
    </p>
    <p class="title">
      <span class="title-text">{{ __('Print Date') }}: </span> {{ formatDate(date('d-m-Y')) }}
    </p>
</td>
@endsection

@section('list-table')
<table class="list-table">
    <thead class="list-head">
        <tr>
            <td class="text-center list-th"> {{ __('Name') }} </td>
            <td class="text-center list-th"> {{ __('Gender') }} </td>
            <td class="text-center list-th"> {{ __('Status') }} </td>
            <td class="text-center list-th"> {{ __('Created At') }} </td>
        </tr>
    </thead>
    @foreach ($AiVoices as $key => $voice)
        <tr>
            <td class="text-center list-td"> {!! ucfirst($voice->name) !!} </td>
            <td class="text-center list-td"> {{ $voice->gender }} </td>
            <td class="text-center list-td"> {{ lcfirst($voice->status) }} </td>
            <td class="text-center list-td"> {{ timeZoneFormatDate($voice->created_at) }} </td>
        </tr>
    @endforeach
</table>
@endsection

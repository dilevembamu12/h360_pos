@extends('admin.layouts.list_pdf')

@section('pdf-title')
<title>{{ __(':x List', ['x' => __('Voiceover')]) }}</title>
@endsection

@section('header-info')
<td colspan="2" class="tbody-td">
    <p class="title">
      <span class="title-text"></span><strong>{{ __(':x List', ['x' => __('Voiceover')]) }}</strong>
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
            <td class="text-center list-th"> {{ __('Promt') }} </td>
            <td class="text-center list-th"> {{ __('Language') }} </td>
            <td class="text-center list-th"> {{ __('Gender') }} </td>
            <td class="text-center list-th"> {{ __('Creator') }} </td>
            <td class="text-center list-th"> {{ __('Created At') }} </td>
        </tr>
    </thead>
    @foreach ($textToSpeechs as $key => $audio)
        <tr>
            <td class="text-center list-td"> {{ trimWords(ucfirst($audio->prompt), 60) }} </td>
            <td class="text-center list-td"> {{ $audio->language }} </td>
            <td class="text-center list-td"> {{ $audio->gender }} </td>
            <td class="text-center list-td"> {{ optional($audio->user)->name }} </td>
            <td class="text-center list-td"> {{ timeZoneFormatDate($audio->created_at) }} </td>
        </tr>
    @endforeach
</table>
@endsection

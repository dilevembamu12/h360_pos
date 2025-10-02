@extends('admin.layouts.list_pdf')

@section('pdf-title')
<title>{{ __(':x List', ['x' => __('Speeches')]) }}</title>
@endsection

@section('header-info')
<td colspan="2" class="tbody-td">
    <p class="title">
      <span class="title-text"></span><strong>{{ __(':x List', ['x' => __('Speeches')]) }}</strong>
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
            <td class="text-center list-th"> {{ __('Content') }} </td>
            <td class="text-center list-th"> {{ __('Creator') }} </td>
            <td class="text-center list-th"> {{ __('Language') }} </td>
            <td class="text-center list-th"> {{ __('Duration') }} </td>
            <td class="text-center list-th"> {{ __('Created At') }} </td>
        </tr>
    </thead>
    @foreach ($speechToTexts as $key => $speeches)
        <tr>
            <td class="text-center list-td"> {!! trimWords($speeches->content, 60) !!} </td>
            <td class="text-center list-td"> {{ wrapIt(optional($speeches->speechToTextCreator)->name, 10) }} </td>
            <td class="text-center list-td"> {{ ucfirst($speeches->language) }} </td>
            <td class="text-center list-td"> {{ gmdate('H:i:s', ($speeches->duration * 60)) }} </td>
            <td class="text-center list-td"> {{ timeZoneFormatDate($speeches->created_at) }} </td>
        </tr>
    @endforeach
</table>
@endsection

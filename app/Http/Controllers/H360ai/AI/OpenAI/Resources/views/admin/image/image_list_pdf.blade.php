@extends('admin.layouts.list_pdf')

@section('pdf-title')
<title>{{ __(':x List', ['x' => __('Images')]) }}</title>
@endsection

@section('header-info')
<td colspan="2" class="tbody-td">
    <p class="title">
      <span class="title-text"></span><strong>{{ __(':x List', ['x' => __('Images')]) }}</strong>
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
            <td class="text-center list-th"> {{ __('Creator') }} </td>
            <td class="text-center list-th"> {{ __('Size') }} </td>
            <td class="text-center list-th"> {{ __('Created At') }} </td>
        </tr>
    </thead>
    @foreach ($images as $key => $image)
        <tr>
            <td class="text-center list-td"> {{ trimWords($image->name, 40) }} </td>
            <td class="text-center list-td"> {{ wrapIt(optional($image->user)->name, 10) }} </td>
            <td class="text-center list-td"> {{ $image->size }} </td>
            <td class="text-center list-td"> {{ timeZoneFormatDate($image->created_at) }} </td>
        </tr>
    @endforeach
</table>
@endsection

@extends('admin.layouts.list_pdf')

@section('pdf-title')
<title>{{ __(':x List', ['x' => __('Use Case Categories')]) }}</title>
@endsection

@section('header-info')
<td colspan="2" class="tbody-td">
    <p class="title">
      <span class="title-text"></span><strong>{{ __(':x List', ['x' => __('Use Case Categories')]) }}</strong>
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
            <td class="text-center list-th"> {{ __('Description') }} </td>
            <td class="text-center list-th"> {{ __('Created At') }} </td>
        </tr>
    </thead>
    @foreach ($useCaseCategories as $key => $useCaseCategory)
        <tr>
            <td class="text-center list-td"> {!! wrapIt($useCaseCategory->name, 10, ['columns' => 2]) !!} </td>
            <td class="text-center list-td"> {!! wrapIt($useCaseCategory->description, 10, ['columns' => 2]) !!} </td>
            <td class="text-center list-td"> {{ timeZoneFormatDate($useCaseCategory->created_at) }} </td>
        </tr>
    @endforeach
</table>
@endsection

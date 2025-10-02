@extends('admin.layouts.list_pdf')

@section('pdf-title')
<title>{{ __(':x List', ['x' => __('Use Cases')]) }}</title>
@endsection

@section('header-info')
<td colspan="2" class="tbody-td">
    <p class="title">
      <span class="title-text"></span><strong>{{ __(':x List', ['x' => __('Use Cases')]) }}</strong>
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
            <td class="text-center list-th"> {{ __('Creator') }} </td>
            <td class="text-center list-th"> {{ __('Status') }} </td>
            <td class="text-center list-th"> {{ __('Created At') }} </td>
        </tr>
    </thead>
    @foreach ($useCases as $key => $useCase)
        <tr>
            <td class="text-center list-td"> {!! wrapIt($useCase->name, 10, ['columns' => 2]) !!} </td>
            <td class="text-center list-td"> {!! wrapIt($useCase->description, 10, ['columns' => 2]) !!} </td>
            <td class="text-center list-td"> {{ ucfirst(optional($useCase->user)->name) }} </td>
            <td class="text-center list-td"> {{ ucfirst($useCase->status) }} </td>
            <td class="text-center list-td"> {{ timeZoneFormatDate($useCase->created_at) }} </td>
        </tr>
    @endforeach
</table>
@endsection

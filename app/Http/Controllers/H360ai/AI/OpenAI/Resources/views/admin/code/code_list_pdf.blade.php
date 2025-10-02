@extends('admin.layouts.list_pdf')

@section('pdf-title')
<title>{{ __(':x List', ['x' => __('Code')]) }}</title>
@endsection

@section('header-info')
<td colspan="2" class="tbody-td">
    <p class="title">
      <span class="title-text"></span><strong>{{ __(':x List', ['x' => __('Code')]) }}</strong>
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
            <td class="text-center list-th"> {{ __('Code') }} </td>
            <td class="text-center list-th"> {{ __('Creator') }} </td>
            <td class="text-center list-th"> {{ __('Created At') }} </td>
        </tr>
    </thead>
    @foreach ($codes as $key => $code)
        <tr>
            <td class="text-center list-td"> {!! trimWords(ucfirst($code->code_title), 60) !!} </td>
            <td class="text-center list-td"> {!! trimWords(implode('', json_decode($code->formated_code)), 60) !!} </td>
            <td class="text-center list-td"> {!! optional($code->codeCreator)->name !!} </td>
            <td class="text-center list-td"> {{ timeZoneFormatDate($code->created_at) }} </td>
        </tr>
    @endforeach
</table>
@endsection

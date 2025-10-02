@extends('admin.layouts.list_pdf')

@section('pdf-title')
<title>{{ __(':x List', ['x' => __('Contents')]) }}</title>
@endsection

@section('header-info')
<td colspan="2" class="tbody-td">
    <p class="title">
      <span class="title-text"></span><strong>{{ __(':x List', ['x' => __('Contents')]) }}</strong>
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
            <td class="text-center list-th"> {{ __('Template') }} </td>
            <td class="text-center list-th"> {{ __('Description') }} </td>
            <td class="text-center list-th"> {{ __('Creator') }} </td>
            <td class="text-center list-th"> {{ __('Model') }} </td>
            <td class="text-center list-th"> {{ __('Language') }} </td>
            <td class="text-center list-th"> {{ __('Created At') }} </td>
        </tr>
    </thead>
    @foreach ($contents as $key => $content)
        <tr>
            <td class="text-center list-td"> {{ trimWords(optional($content->useCase)->name, 60) }} </td>
            <td class="text-center list-td"> {{ trimWords($content->content, 60) }} </td>
            <td class="text-center list-td"> {{ wrapIt(optional($content->templateCreator)->name, 10) }} </td>
            <td class="text-center list-td"> {{ ucfirst($content->template_model) }} </td>
            <td class="text-center list-td"> {{ ucfirst($content->template_language) }} </td>
            <td class="text-center list-td"> {{ timeZoneFormatDate($content->created_at) }} </td>

        </tr>
    @endforeach
</table>
@endsection

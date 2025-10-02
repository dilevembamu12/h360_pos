@extends('admin.layouts.list_pdf')

@section('pdf-title')
<title>{{ __(':x List', ['x' => __('Long Article')]) }}</title>
@endsection

@section('header-info')
<td colspan="2" class="tbody-td">
    <p class="title">
      <span class="title-text"></span><strong>{{ __(':x List', ['x' => __('Long Article')]) }}</strong>
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
            <td class="text-center list-th"> {{ __('Title') }} </td>
            <td class="text-center list-th"> {{ __('Content') }} </td>
            <td class="text-center list-th"> {{ __('Generator') }} </td>
            <td class="text-center list-th"> {{ __('Provider') }} </td>
            <td class="text-center list-th"> {{ __('Model') }} </td>
            <td class="text-center list-th"> {{ __('Created At') }} </td>
        </tr>
    </thead>
    @foreach ($longArticles as $longArticle)
        <tr>
            <td class="text-center list-td"> {{ trimWords($longArticle->title, 10) }} </td>
            <td class="text-center list-td"> {{ trimWords(strip_tags($longArticle->filtered_content ?? str_replace('**', '', $longArticle->content)), 35) }} </td>
            <td class="text-center list-td"> {{ $longArticle->user?->name }} </td>
            <td class="text-center list-td"> {{ $longArticle->provider }} </td>
            <td class="text-center list-td"> {{ $longArticle->generation_options['model'] ?? '' }} </td>
            <td class="text-center list-td"> {{ timeZoneFormatDate($longArticle->created_at) }} {{ timeZoneGetTime($longArticle->created_at) }} </td>
        </tr>
    @endforeach
</table>
@endsection

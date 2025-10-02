@extends('admin.layouts.list_pdf')

@section('pdf-title')
<title>{{ __(':x List', ['x' => __('Chat Assistants')]) }}</title>
@endsection

@section('header-info')
<td colspan="2" class="tbody-td">
    <p class="title">
      <span class="title-text"></span><strong>{{ __(':x List', ['x' => __('Chat Assistants')]) }}</strong>
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
            <td class="text-center list-th"> {{ __('Category Name') }} </td>
            <td class="text-center list-th"> {{ __('Name') }} </td>
            <td class="text-center list-th"> {{ __('Message') }} </td>
            <td class="text-center list-th"> {{ __('Role') }} </td>
            <td class="text-center list-th"> {{ __('Status') }} </td>
            <td class="text-center list-th"> {{ __('Default') }} </td>
            <td class="text-center list-th"> {{ __('Created At') }} </td>
        </tr>
    </thead>
    @foreach ($chatAssistants as $key => $chatAssistant)
        <tr>
            <td class="text-center list-td"> {{ trimWords(ucfirst($chatAssistant->chatCategory?->name), 60) }} </td>
            <td class="text-center list-td"> {{ ucfirst($chatAssistant->name) }} </td>
            <td class="text-center list-td"> {{ trimWords($chatAssistant->message, 60) }} </td>
            <td class="text-center list-td"> {{ ucfirst($chatAssistant->role) }} </td>
            <td class="text-center list-td"> {{ lcfirst($chatAssistant->status) }} </td>
            <td class="text-center list-td"> {{ $chatAssistant->is_default == 1 ?  __("Yes") : __("No") }} </td>
            <td class="text-center list-td"> {{ timeZoneFormatDate($chatAssistant->created_at) }} </td>
        </tr>
    @endforeach
</table>
@endsection

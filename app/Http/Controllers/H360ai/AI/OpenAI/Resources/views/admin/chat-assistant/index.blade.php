@extends('admin.layouts.app')
@section('page_title', __('Chat Assistants'))
@section('css')
@endsection

@section('content')
<!-- Main content -->
<div class="col-sm-12 list-container" id="chat-assistant-category-list-container">
    <div class="card">
        <div class="card-header bb-none pb-0">
            <h5>{{ __('Chat Assistants') }}</h5>
            <x-backend.group-filters :groups="$groups" :column="'status'" />
            <div class="card-header-right my-2 mx-md-0 mx-sm-4">
                <x-backend.button.batch-delete class="me-1" />
                @if (in_array('Modules\OpenAI\Http\Controllers\Admin\ChatAssistantsController@create', $prms))
                    <x-backend.button.add-new label="{{ __('Add :x', ['x' => __('Chat Assistant')]) }}" class="me-1" href="{{ route('admin.chat.assistant.create') }}" />
                @endif
                <x-backend.button.export  class="me-1"/>
                <x-backend.button.filter class="me-0" />
            </div>
        </div>
        <x-backend.datatable.filter-panel class="mx-1">
            <div class="col-md-6">
                <x-backend.datatable.input-search />
            </div>
            <div class="col-md-3">
                <select class="select2 filter" name="chat_category">
                    <option value="">{{ __('All Category') }}</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select class="select2-hide-search filter" name="status">
                    <option value="">{{ __('All Status') }}</option>
                    <option value="Active">{{ __('Active') }}</option>
                    <option value="Inactive">{{ __('Inactive') }}</option>
                </select>
            </div> 
        </x-backend.datatable.filter-panel>
        <x-backend.datatable.table-wrapper class="user-list-wallet user-list-processing-message need-batch-operation"
            data-namespace="\Modules\OpenAI\Entities\ChatBot" data-column="id">
            @include('admin.layouts.includes.yajra-data-table')
        </x-backend.datatable.table-wrapper>
        @include('admin.layouts.includes.delete-modal')
    </div>
</div>
@endsection

@section('js')
<script type="text/javascript">
    'use strict';
    var pdf = "{{ in_array('Modules\OpenAI\Http\Controllers\Admin\ChatAssistantsController@pdf', $prms) ? '1' : '0' }}";
    var csv = "{{ in_array('Modules\OpenAI\Http\Controllers\Admin\ChatAssistantsController@csv', $prms) ? '1' : '0' }}";
    var listContaner = "chat-assistant-category-list-container";
    var endRoute = "/chat-assistant/";
</script>

<script src="{{ asset('public/dist/js/custom/permission.min.js') }}"></script>
<script src="{{ asset('public/dist/js/custom/document-list.min.js') }}"></script>
@endsection

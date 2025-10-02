@extends('admin.layouts.app')
@section('page_title', __('Voiceover'))

@section('content')
<!-- Main content -->
<div class="col-sm-12 list-container" id="text-to-speech-list-container">
    <div class="card">
        <div class="card-header bb-none pb-0 mb-2">
            <h5>{{ __('Voiceover') }}</h5>
            <div class="card-header-right my-2 mx-md-0 mx-sm-4">
                <x-backend.button.batch-delete class="me-1" />
                <x-backend.button.export  class="me-1"/>
                <x-backend.button.filter class="me-0" />
            </div>
        </div>
        <x-backend.datatable.filter-panel class="mx-1">
            <div class="col-md-6">
                <x-backend.datatable.input-search />
            </div>
            <div class="col-md-3">
                <select class="select2 filter" name="user_id">
                    <option value="">{{ __('All Users') }}</option>
                    @foreach($users as $user)
                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select class="select2 filter" name="language">
                    <option value="">{{ __('All Language') }}</option>
                    @foreach($languages as $language)
                        @if ( !array_key_exists($language->name, $omitLanguages) )
                            <option value="{{ $language->name }}">{{ $language->name }}</option>
                        @endif
                    @endforeach
                </select>
            </div>
        </x-backend.datatable.filter-panel>
        <x-backend.datatable.table-wrapper class="user-list-wallet user-list-processing-message need-batch-operation"
            data-namespace="\Modules\OpenAI\Entities\Audio" data-column="id">
            @include('admin.layouts.includes.yajra-data-table')
        </x-backend.datatable.table-wrapper>
        @include('admin.layouts.includes.delete-modal')
    </div>
</div>
@endsection

@section('js')
<script type="text/javascript">
    'use strict';
    var pdf = "{{ in_array('Modules\OpenAI\Http\Controllers\Admin\AdminTextToSpeechController@pdf', $prms) ? '1' : '0' }}";
    var csv = "{{ in_array('Modules\OpenAI\Http\Controllers\Admin\AdminTextToSpeechController@csv', $prms) ? '1' : '0' }}";
    var listContaner = "text-to-speech-list-container";
    var endRoute = "/text-to-speech/";
</script>
<script src="{{ asset('public/dist/js/custom/permission.min.js') }}"></script>
<script src="{{ asset('public/dist/js/custom/document-list.min.js') }}"></script>
@endsection

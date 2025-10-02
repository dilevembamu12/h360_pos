@extends('admin.layouts.app')
@section('page_title', __('Voice Verse'))

@section('content')
<!-- Main content -->
<div class="col-sm-12 list-container" id="voice-verse-list-container">
    <div class="card">
        <div class="card-header bb-none pb-0">
            <h5>{{ __('AI Voices') }}</h5>
            <x-backend.group-filters :groups="$groups" :column="'status'" />
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
            <div class="col-md-3 mb-2 mb-md-0">
                <select class="select2 filter" name="gender">
                    <option value="">{{ __('Gender') }}</option>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                </select>
            </div>
            <div class="col-md-3 mb-2 mb-md-0">
                <select class="select2-hide-search filter" name="language">
                    <option value="">{{ __('All Languages') }}</option>
                    @foreach($languages as $language)
                        @if ( !array_key_exists($language->name, $omitLanguages) )
                        <option value="{{ $language->short_name == 'zh' ? 'yue' : $language->short_name }}">{{ $language->name }}</option>
                        @endif
                    @endforeach
                </select>
            </div> 
        </x-backend.datatable.filter-panel>
        <x-backend.datatable.table-wrapper class="user-list-wallet user-list-processing-message need-batch-operation">
            @include('admin.layouts.includes.yajra-data-table')
        </x-backend.datatable.table-wrapper>
        @include('admin.layouts.includes.delete-modal')
    </div>
</div>
@endsection

@section('js')
<script type="text/javascript">
    'use strict';
    var pdf = "{{ in_array('Modules\OpenAI\Http\Controllers\Admin\TextToSpeechController@voicePdf', $prms) ? '1' : '0' }}";
    var csv = "{{ in_array('Modules\OpenAI\Http\Controllers\Admin\TextToSpeechController@voiceCsv', $prms) ? '1' : '0' }}";
    var listContaner = "voice-verse-list-container";
    var endRoute = "/voice/";
</script>
<script src="{{ asset('public/dist/js/custom/permission.min.js') }}"></script>
<script src="{{ asset('public/dist/js/custom/document-list.min.js') }}"></script>
@endsection

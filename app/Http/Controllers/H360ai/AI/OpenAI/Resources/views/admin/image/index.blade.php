@extends('admin.layouts.app')
@section('page_title', __('Images'))

@section('css')
@endsection

@section('content')
<!-- Main content -->
<div class="col-sm-12 list-container" id="image-list-container">
    <div class="card">
        <div class="card-header bb-none pb-0 mb-2">
            <h5>{{ __('Images') }}</h5>
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
                <select class="select2-hide-search filter" name="user_id">
                    <option value="">{{ __('All Users') }}</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select class="select2-hide-search filter" name="size">
                    <option value="">{{ __('All Sizes') }}</option>
                    @foreach( $sizes as $size )
                        <option value="{{ $size }}">{{ $size }}</option>
                    @endforeach
                </select>
            </div> 
        </x-backend.datatable.filter-panel>
        <x-backend.datatable.table-wrapper class="user-list-wallet user-list-processing-message need-batch-operation"
            data-namespace="\Modules\OpenAI\Entities\Image" data-column="id">
            @include('admin.layouts.includes.yajra-data-table')
        </x-backend.datatable.table-wrapper>
        @include('admin.layouts.includes.delete-modal')
    </div>
</div>
@endsection

@section('js')
<script type="text/javascript">
    'use strict';
    var pdf = "{{ in_array('Modules\OpenAI\Http\Controllers\Admin\ImageController@pdf', $prms) ? '1' : '0' }}";
    var csv = "{{ in_array('Modules\OpenAI\Http\Controllers\Admin\ImageController@csv', $prms) ? '1' : '0' }}";
    var listContaner = "image-list-container";
    var endRoute = "/image/";
</script>
<script src="{{ asset('public/dist/js/custom/permission.min.js') }}"></script>
<script src="{{ asset('public/dist/js/custom/document-list.min.js') }}"></script>

@endsection

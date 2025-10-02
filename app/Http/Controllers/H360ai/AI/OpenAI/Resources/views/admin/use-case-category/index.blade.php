@extends('admin.layouts.app')
@section('page_title', __('Use Cases Categories'))
@section('css')
@endsection

@section('content')
<!-- Main content -->
<div class="col-sm-12 list-container" id="use-case-category-list-container">
    <div class="card">
        <div class="card-header bb-none pb-0 mb-2">
            <h5>{{ __('Use Case Categories') }}</h5>
            <div class="card-header-right my-2 mx-md-0 mx-sm-4">
                <x-backend.button.batch-delete class="me-1" />
                @if (in_array('Modules\OpenAI\Http\Controllers\Admin\UseCaseCategoriesController@create', $prms))
                    <x-backend.button.add-new label="{{ __('Add Category') }}" class="me-1" href="{{ route('admin.use_case.category.create') }}" />
                @endif
                <x-backend.button.export  class="me-1"/>
                <x-backend.button.filter class="me-0" />
            </div>
        </div>
        <x-backend.datatable.filter-panel class="mx-1">
            <div class="col-md-12">
                <x-backend.datatable.input-search />
            </div>
        </x-backend.datatable.filter-panel>
        <x-backend.datatable.table-wrapper class="user-list-wallet user-list-processing-message need-batch-operation"
            data-namespace="\Modules\OpenAI\Entities\UseCaseCategory" data-column="id">
            @include('admin.layouts.includes.yajra-data-table')
        </x-backend.datatable.table-wrapper>
        @include('admin.layouts.includes.delete-modal')
    </div>
</div>
@endsection

@section('js')
<script type="text/javascript">
    'use strict';
    var pdf = "{{ in_array('Modules\OpenAI\Http\Controllers\Admin\UseCaseCategoriesController@pdf', $prms) ? '1' : '0' }}";
    var csv = "{{ in_array('Modules\OpenAI\Http\Controllers\Admin\UseCaseCategoriesController@csv', $prms) ? '1' : '0' }}";
    var listContaner = "use-case-category-list-container";
    var endRoute = "/use-case-categories/";
</script>
<script src="{{ asset('public/dist/js/custom/permission.min.js') }}"></script>
<script src="{{ asset('public/dist/js/custom/document-list.min.js') }}"></script>
@endsection

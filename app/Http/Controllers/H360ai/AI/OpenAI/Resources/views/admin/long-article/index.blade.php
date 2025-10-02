@extends('admin.layouts.app')
@section('page_title', __('Articles'))
@section('content')
    <!-- Main content -->
    <div class="col-sm-12 list-container" id="long_article-list-container">
        <div class="card">
            <div class="card-header bb-none pb-0">
                <h5>{{ __('Articles') }}</h5>
                <x-backend.group-filters :groups="$groups" :column="'status'" />
                <div class="card-header-right my-2 mx-md-0 mx-sm-4">
                    <x-backend.button.batch-delete class="me-1" />
                    <x-backend.button.export  class="me-1"/>
                    <x-backend.button.filter class="me-0" />
                </div>
            </div>

            <x-backend.datatable.filter-panel class="mx-1">
                <div class="col-md-3">
                    <x-backend.datatable.input-search />
                </div>
                <div class="col-md-3">
                    <select class="select2 filter" name="user">
                        <option value="">{{ __('All Users') }}</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="select2 filter" name="provider">
                        <option value="">{{ __('All Providers') }}</option>
                        @foreach ($longArticleProviders as $provider)
                            <option value="{{ $provider }}">{{ ucfirst($provider) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="select2 filter" name="model">
                        <option value="">{{ __('All Models') }}</option>
                        @foreach ($longArticleModels as $model)
                            <option value="{{ $model }}">{{ $model }}</option>
                        @endforeach
                    </select>
                </div>
            </x-backend.datatable.filter-panel>

            <x-backend.datatable.table-wrapper class="user-list-wallet user-list-processing-message need-batch-operation"
                data-namespace="\Modules\OpenAI\Entities\Archive" data-column="id">
                @include('admin.layouts.includes.yajra-data-table')
            </x-backend.datatable.table-wrapper>

            @include('admin.layouts.includes.delete-modal')
        </div>
    </div>
@endsection

@section('js')
    <script type="text/javascript">
        'use strict';
        var pdf = "{{ in_array('Modules\\OpenAI\\Http\\Controllers\\Admin\\LongArticleController@pdf', $prms) ? '1' : '0' }}";
        var csv = "{{ in_array('Modules\\OpenAI\\Http\\Controllers\\Admin\\LongArticleController@csv', $prms) ? '1' : '0' }}";
    </script>

    <script src="{{ asset('public/dist/js/custom/permission.min.js') }}"></script>
    <script src="{{ asset('public/dist/js/custom/long-article.min.js') }}"></script>
@endsection

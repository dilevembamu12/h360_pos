@extends('admin.layouts.app')
@section('page_title', __('Edit :x', ['x' => __('Ai Chatbot')]))

@section('css')
<link rel="stylesheet" href="{{ asset('Modules/MediaManager/Resources/assets/css/media-manager.min.css') }}">
@endsection

@section('content')
<!-- Main content -->
<div class="col-sm-12">
    <div class="card">
        <div class="card-header">
            <h5>
                <a class="pe-1" href="{{ route('admin.features.ai_chatbot.index') }}">{{ __('Ai Chatbots') }}</a>>>
                <span class="ps-1">{{ __("Edit :x informations", ['x' => $chatBot->name]) }}</span>
            </h5>
        </div>

        <div class="card-body px-3" id="no_shadow_on_card">
            <div class="col-sm-12 m-t-20 form-tabs">
                <ul class="nav nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link font-bold active text-uppercase" id="home-tab" data-bs-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true">{{ __(':x Information', ['x' => $chatBot->name]) }}</a>
                    </li>
                </ul>

                <div class="card-block table-border-style tab-content">
                    <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                        <div class="col-sm-12">
                            <form action="{{ route('admin.features.ai_chatbot.update', [ 'id' => $chatBot->id]) }}" method="post" class="form-horizontal" enctype="multipart/form-data" id="form" onsubmit="return formValidation()" novalidate>
                                <input type="hidden" value="{{ csrf_token() }}" name="_token" id="token">
                                
                                <div class="form-group row">
                                    <label for="name" class="col-sm-2 col-form-label require">{{ __('Name') }}</label>
                                    <div class="col-sm-10">
                                        <input type="text" placeholder="{{ __('Name') }}" class="form-control inputFieldDesign" id="name" name="name" value="{{ $chatBot->name }}" maxlength="191" oninvalid="this.setCustomValidity({{ __('This field is required.') }})" required>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="description" class="col-sm-2 col-form-label require">{{ __('Description') }}</label>
                                    <div class="col-sm-10">
                                        <textarea placeholder="{{ __('Description') }}" id="description" class="form-control" name="description" oninvalid="this.setCustomValidity('{{ __('This field is required.') }}')" required>{{ $chatBot->description }}</textarea>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="first_name"
                                        class="col-sm-2 col-form-label require pr-0">{{ __('Script Code') }}
                                    </label>
                                    <div class="col-sm-10">
                                        <textarea placeholder="{{ __('Script Code') }}" readonly class="form-control" id="script" rows="2" name="script"> {{ $chatBot->script_code }} </textarea>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="first_name" class="col-sm-2 require col-form-label pr-0">{{ __('Language') }}
                                    </label>
                                    <div class="col-sm-10">
                                        <select class="form-control sl_common_bx select2-hide-search" id="language" name="language">
                                            <option value="">{{ __('Select One') }}</option>
                                            @foreach ($languages as $language)
                                                <option value="{{ $language->name }}" {{ $language->name == $chatBot->language ? 'selected' : '' }}>
                                                    {{ $language->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="Status" class="col-md-2 col-3 col-form-label require">{{ __('Status') }}</label>
                                    <div class="col-sm-10">
                                        <select name="status" class="form-control sl_common_bx select2-hide-search" id="status">
                                            <option value="">{{ __('Select One') }} </option>
                                            <option value="Active" {{ $chatBot->status === 'Active' ? 'selected' : '' }}>{{ __('Active') }} </option>
                                            <option value="Inactive" {{ $chatBot->status === 'Inactive' ? 'selected' : '' }} >{{ __('Inactive') }}</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="d-flex justify-items-start mt-4 flex-wrap">
                                        <a href="{{ route('admin.features.ai_chatbot.index') }}" class="btn custom-btn-cancel all-cancel-btn">{{ __('Cancel') }}</a>
                                        <button class="btn custom-btn-submit" type="submit" id="btnUpdate">
                                            <i class="comment_spinner spinner fa fa-spinner fa-spin custom-btn-small display_none"></i>
                                            <span id="spinnerText">{{ __('Update') }}</span>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                @include('mediamanager::image.modal_image')
            </div>
        </div>
    </div>
</div>

@endsection

@section('js')
<script>
    'use strict';
    var currentUrl = "{!! url()->full() !!}";
    var loginNeeded = "{!! session('loginRequired') ? 1 : 0 !!}";
    var slug = false;
</script>
<script src="{{ asset('public/dist/js/custom/validation.min.js') }}"></script>
<script src="{{ asset('Modules/OpenAI/Resources/assets/js/admin/ai-chatbot.min.js') }}"></script>
@endsection

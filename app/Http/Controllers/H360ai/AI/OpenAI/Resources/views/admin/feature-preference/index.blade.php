@extends('admin.layouts.app')
@section('page_title', __('Edit :x', ['x' => __('Feature Preferences')]))
@section('css')
    <link rel="stylesheet" href="{{ asset('Modules/MediaManager/Resources/assets/css/media-manager.min.css') }}">
    <link rel="stylesheet" href="{{ asset('Modules/OpenAI/Resources/assets/css/feature_preference.min.css') }}">
@endsection

@section('content')
    <!-- Main content -->
    <div class="col-sm-12" id="preference-container">
        <div class="card">
            <div class="card-body row" id="preference-container">
                <div class="col-lg-3 col-12 z-index-10 pe-0 ps-0 ps-md-3" aria-labelledby="navbarDropdown">
                    <div class="card card-info shadow-none" id="nav">
                        <div class="card-header pt-4 border-bottom text-nowrap">
                            <h5 id="general-settings">{{ __('Features') }}</h5>
                        </div>
                        <ul class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">

                            <!-- Ai Doc Chat -->
                            <li>
                                <a class="nav-link text-left tab-name active" id="v-pills-ai-doc-chat-tab" data-bs-toggle="pill"
                                    href="#v-pills-ai-doc-chat" role="tab" aria-controls="v-pills-ai-doc-chat"
                                    aria-selected="true" data-id="{{ __('Ai Doc Chat') }}">{{ __('Ai Doc Chat') }}</a>
                            </li>

                            <!-- Chatbot -->
                            <li>
                                <a class="accordion-heading position-relative text-start" data-bs-toggle="collapse"
                                    data-bs-target="#chatbot-main-v-pills-tab"> {{ __('Chatbot') }}
                                    <span class="pull-right"><b class="caret"></b></span>
                                    <span><i
                                            class="fa fa-angle-down position-absolute arrow-icon end-0 me-2 top-0 fs-6"></i></span>
                                </a>
                                <ul class="nav nav-list flex-column flex-nowrap collapse ml-2 vertical-class side-nav"
                                    id="chatbot-main-v-pills-tab" role="tablist" aria-orientation="vertical">
                                    <li>
                                        <a class="nav-link text-left tab-name" id="v-pills-general-chatbot-tab"
                                            data-bs-toggle="pill" href="#v-pills-general-chatbot" role="tab"
                                            aria-controls="v-pills-general-chatbot" aria-selected="true"
                                            data-id="{{ __('Chatbot') }} >> {{ __('General') }}">{{ __('General Options') }}</a>
                                    </li>

                                    <li>
                                        <a class="nav-link text-left tab-name" id="v-pills-theme-chatbot-tab"
                                            data-bs-toggle="pill" href="#v-pills-theme-chatbot" role="tab"
                                            aria-controls="v-pills-theme-chatbot" aria-selected="true"
                                            data-id="{{ __('Chatbot') }} >> {{ __('Theme') }}">{{ __('Theme') }}</a>
                                    </li>

                                    <li>
                                        <a class="nav-link text-left tab-name" id="v-pills-settings-chatbot-tab"
                                            data-bs-toggle="pill" href="#v-pills-settings-chatbot" role="tab"
                                            aria-controls="v-pills-restriction-chatbot" aria-selected="true"
                                            data-id="{{ __('Chatbot') }} >> {{ __('Settings') }}">{{ __('Settings') }}</a>
                                    </li>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-9 col-12 ps-0">
                    <div class="card card-info shadow-none">
                        <div class="card-header pt-4 border-bottom">
                            <h5><span id="theme-title">{{ __('Features') }}</span></h5>
                        </div>
                        <div class="card-body">
                            <form method="post" action="{{ route('admin.features.feature_preference.options') }}" id="aiSettings">
                                @csrf

                                <div class="tab-content p-0 box-shadow-unset" id="topNav-v-pills-tabContent">
                                    <!-- Ai Doc Chat -->
                                    <div class="tab-pane fade active show" id="v-pills-ai-doc-chat" role="tabpanel" aria-labelledby="v-pills-ai-doc-chat-tab">
                                        <div class="row">
                                            <div class="col-sm-12">

                                                <div class="d-flex justify-content-start alert alert-warning">
                                                    <b>{{ __("If neither option is enabled, the system will automatically pick the first available provider and model for users, so they won't have to choose anything themselves.") }}</b>
                                                </div>

                                                <!-- User Access -->
                                                <div class="form-group row">
                                                    <label for="rating"
                                                        class="col-sm-2 control-label text-left require">{{ __("Provider-Model Access") }}</label>
                                                    <div class="col-9 d-flex mt-neg-2">
                                                        <div class="ltr:me-3 rtl:ms-3">
                                                            <div class="switch switch-bg d-inline m-r-10">
                                                                <input type="checkbox" name="aiDocChat[provider_model_access]"
                                                                    class="checkActivity" id="user_access_id"
                                                                    {{ $aiDocChat['provider_model_access'] == 'on' ? 'checked' : '' }} >
                                                                <label for="user_access_id" class="cr"></label>
                                                            </div>
                                                        </div>
                                                        <div class="mt-2">
                                                            <span>{{ __('Enable provider-model access, allowing users to choose their preferred provider and model.') }}</span>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- All Active Providers -->
                                                <div class="form-group row">
                                                    <label for="rating"
                                                        class="col-sm-2 control-label text-left require">{{ __("All Providers & Models") }}</label>
                                                    <div class="col-9 d-flex mt-neg-2">
                                                        <div class="ltr:me-3 rtl:ms-3">
                                                            <div class="switch switch-bg d-inline m-r-10">
                                                                <input type="checkbox" name="aiDocChat[all_providers]"
                                                                    class="checkActivity" id="all_provider_id"
                                                                    {{ $aiDocChat['all_providers'] == 'on' ? 'checked' : '' }} >
                                                                <label for="all_provider_id" class="cr"></label>
                                                            </div>
                                                        </div>
                                                        <div class="mt-2">
                                                            <span>{{ __("Allow users to automatically utilize all active providers and have the first available model option selected for them, without requiring manual selection.") }}</span>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                    <!-- General Options -->
                                    <div class="tab-pane fade" id="v-pills-general-chatbot" role="tabpanel" aria-labelledby="v-pills-general-chatbot-tab">
                                        <div class="row">
                                            <div class="col-sm-12">

                                                <!-- Language -->
                                                <div class="form-group row">
                                                    <div class="col-12">
                                                        <label for="default-category" class="control-label require">{{ __('Select Languages') }}</label>
                                                        <select class="form-control select2 inputFieldDesign sl_common_bx"
                                                            name="general_options[languages][]" multiple required>
                                                            @foreach ($allLanguages as $language)
                                                                <option value="{{ $language->name }}"
                                                                    {{ in_array($language->name, $general_options['languages']) ? 'selected' : '' }}> {{ $language->name }} </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>

                                                <!-- Default Avatar -->
                                                <div class="form-group row preview-parent">
                                                    <label for="training_options"
                                                        class="control-label require">{{ __('Default Avatar') }}</label>
                                                    <div class="col-sm-12">
                                                        <div class="custom-file media-manager-img" data-val="single"
                                                            data-returntype="ids" id="image-status"
                                                            data-type="{{ implode(',', getFileExtensions(3)) }}">
                                                            <input type="hidden"
                                                                class="custom-file-input is-image form-control form-height"
                                                                name="general_options[default_avatar]">
                                                            <label class="custom-file-label overflow_hidden position-relative d-flex align-items-center" for="validatedCustomFile">{{ __('Upload image') }}</label>
                                                        </div>
                                                        <div class="preview-image">
                                                            <!-- img will be shown here -->
                                                            <div class="d-flex flex-wrap mt-2">
                                                                <div class="position-relative border boder-1 p-1 mr-2 rounded mt-2">
                                                                    <img width="80" class="p-1" src="{{ $general_options['default_avatar'] }}">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="py-1" id="note_txt_1">
                                                            <div class="d-flex mt-1 mb-3">
                                                                <span class="badge badge-danger h-100 mt-1">{{ __('Note') }}!</span>
                                                                <ul class="list-unstyled ml-3">
                                                                    <li>{{ __('Allowed File Extensions: :y and Maximum File Size :x', ['x' => preference('file_size') . 'MB.', 'y' => implode(',', getFileExtensions(3))]) }}
                                                                    </li>
                                                                </ul>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Theme Options -->
                                    <div class="tab-pane fade" id="v-pills-theme-chatbot" role="tabpanel" aria-labelledby="v-pills-theme-chatbot-tab">
                                        <div class="row">
                                            <div class="col-sm-12">

                                                <!-- Theme Color -->
                                                <div class="form-group row">
                                                    <label for="theme-color" class="control-label require">{{ __('Select theme') }}</label>
                                                    <div class="col-sm-12">
                                                        <div class="theme-container mw-xl" id="theme-color">
                                                            <div class="d-flex gap-4 flex-wrap">
                                                                @php
                                                                    $colors = !empty($theme_options) ? $theme_options['color'] : ['#9163DD', '#E22861', '#FCCA19', '#FF1493', '#2c2c2c', '#5AF457', '#5707CF', '#F2EC36'];
                                                                @endphp
                                                                @foreach ( $colors as $color)
                                                                    <div class="color-input-container">
                                                                        <div class="color-container themes">
                                                                            <input type="color" name="theme_options[color][]" value="{{ $color }}" />
                                                                        </div>
                                                                        <span class="color-code drawer">{{ $color }}</span>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                     <!-- Settings Options -->
                                    <div class="tab-pane fade" id="v-pills-settings-chatbot" role="tabpanel" aria-labelledby="v-pills-settings-chatbot-tab">
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <!-- Conversation -->
                                                <div class="form-group row">
                                                    <label for="rating"
                                                        class="col-sm-3 control-label text-left require">{{ __("Conversation") }}</label>
                                                    <div class="col-9 d-flex mt-neg-2">
                                                        <div class="ltr:me-3 rtl:ms-3">
                                                            <div class="switch switch-bg d-inline m-r-10">
                                                                <input type="hidden" name="settings[conversation]" value="off">
                                                                <input type="checkbox" name="settings[conversation]" class="checkActivity" id="conversation_id"
                                                                    {{ (isset($settings['conversation']) && $settings['conversation']== 'on') ? 'checked' : '' }} >
                                                                <label for="conversation_id" class="cr"></label>
                                                            </div>
                                                        </div>
                                                        <div class="mt-2">
                                                            <span>{{ __('Enable conversation so users can view their past conversations.') }}</span>
                                                        </div>
                                                    </div>
                                                
                                                </div>

                                                <!-- Max File -->
                                                <div class="form-group row">
                                                    <label class="col-sm-3 control-label text-left require" for="file_size">{{ __('Max File Size') }}</label>
                                                    <div class="col-sm-6 flex-wrap">
                                                        <div class="input-group">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text rounded-0 rounded-start">{{ __('MB') }}</span>
                                                            </div>
                                                            <input class="form-control" type="number" name="settings[file_size]" id="file_size" value="{{ isset($settings['file_size']) ? $settings['file_size'] : '' }}" min="1" max="20" required oninvalid="this.setCustomValidity('{{ __('This field is required.') }}')" data-min="{{ __('The value must be :x than or equal to :y', ['x' => __('greater'), 'y' => 1]) }}" data-max="{{ __('The value must be :x than or equal to :y', ['x' => __('less'), 'y' => 20]) }}">
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- File Limit -->
                                                <div class="form-group row">
                                                    <label class="col-sm-3 control-label text-left require" for="file_limit">{{ __('Max Upload Files') }}</label>
                                                    <div class="col-sm-6">
                                                        <input class="form-control" type="number" name="settings[file_limit]" id="file_limit" value="{{ isset($settings['file_limit']) ? $settings['file_limit'] : '' }}" min="1" data-min="{{ __('The value must be :x than or equal to :y', ['x' => __('greater'), 'y' => 1]) }}" required oninvalid="this.setCustomValidity('{{ __('This field is required.') }}') }}">
                                                    </div>
                                                </div>
                                                
                                                <!-- Training Options -->
                                                <div class="form-group row">
                                                    <label for="" class="control-label require">{{ __('Training Options') }}</label>
                                                    <div class="col-sm-12">
                                                        <div class="training-option-container mt-4">
                                                            <div class="row gap-lg-3">
                                                                <div class="col-xxl-2 col-xl-3 col-lg-4 col-md-6 col-12 mb-0 px-lg-0  ">
                                                                <div class="card">
                                                                        <div class="card-body p-2 px-4">
                                                                            <div class="switch switch-bg d-flex ms-auto justify-content-end">
                                                                                    <input type="hidden" name="settings[training_options][file_upload]" value="off">
                                                                                    <input type="checkbox" name="settings[training_options][file_upload]"
                                                                                        id="show-settings-one" {{ isset($settings['training_options']['file_upload']) && $settings['training_options']['file_upload'] == 'on' ? 'checked' : '' }}>
                                                                                    <label for="show-settings-one" class="cr"></label>
                                                                            </div>
                                                                            <span>
                                                                                <svg class="p-2 border border-1 w-40p h-40p rounded-2 bg-color-f3" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                                <g clip-path="url(#clip0_3040_2070)">
                                                                                <path d="M11.1641 1H5.375C4.16688 1 3.1875 1.97938 3.1875 3.1875V16.3125C3.1875 17.5206 4.16688 18.5 5.375 18.5H14.125C15.3331 18.5 16.3125 17.5206 16.3125 16.3125V6.1484C16.3125 5.85832 16.1973 5.58012 15.9921 5.375L11.9375 1.32035C11.7324 1.11523 11.4542 1 11.1641 1ZM11.3906 4.82812V2.64062L14.6719 5.92188H12.4844C11.8803 5.92188 11.3906 5.43219 11.3906 4.82812ZM5.92188 10.8438C5.61984 10.8438 5.375 10.5989 5.375 10.2969C5.375 9.99484 5.61984 9.75 5.92188 9.75H13.5781C13.8802 9.75 14.125 9.99484 14.125 10.2969C14.125 10.5989 13.8802 10.8438 13.5781 10.8438H5.92188ZM5.375 12.4844C5.375 12.1823 5.61984 11.9375 5.92188 11.9375H13.5781C13.8802 11.9375 14.125 12.1823 14.125 12.4844C14.125 12.7864 13.8802 13.0312 13.5781 13.0312H5.92188C5.61984 13.0312 5.375 12.7864 5.375 12.4844ZM5.92188 15.2188C5.61984 15.2188 5.375 14.9739 5.375 14.6719C5.375 14.3698 5.61984 14.125 5.92188 14.125H10.2969C10.5989 14.125 10.8438 14.3698 10.8438 14.6719C10.8438 14.9739 10.5989 15.2188 10.2969 15.2188H5.92188Z" fill="#141414"/>
                                                                                </g>
                                                                                <defs>
                                                                                <clipPath id="clip0_3040_2070">
                                                                                <rect width="20" height="20" fill="white"/>
                                                                                </clipPath>
                                                                                </defs>
                                                                                </svg>
                                                                            </span>
                                                                            <h5 class="training-card-title mt-3">{{ __('File Upload') }}</h5>
                                                                        </div>
                                                                </div>
                                                                </div>
                                                                <div class="col-xxl-2 col-xl-3 col-lg-4 col-md-6 col-12 mb-0 px-lg-0   ">
                                                                    <div class="card">
                                                                        <div class="card-body p-2 px-4">
                                                                            <div class="switch switch-bg d-flex ms-auto justify-content-end">
                                                                                    <input type="hidden" name="settings[training_options][website_url]" value="off">
                                                                                    <input type="checkbox" name="settings[training_options][website_url]"
                                                                                        id="show-settings-two" {{ isset($settings['training_options']['website_url']) && $settings['training_options']['website_url'] == 'on' ? 'checked' : '' }}>
                                                                                    <label for="show-settings-two" class="cr"></label>
                                                                            </div>
                                                                            <span>
                                                                                <svg class="p-2 border border-1 w-40p h-40p rounded-2 bg-color-f3" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                                <g clip-path="url(#clip0_3040_2070)">
                                                                                <path d="M11.1641 1H5.375C4.16688 1 3.1875 1.97938 3.1875 3.1875V16.3125C3.1875 17.5206 4.16688 18.5 5.375 18.5H14.125C15.3331 18.5 16.3125 17.5206 16.3125 16.3125V6.1484C16.3125 5.85832 16.1973 5.58012 15.9921 5.375L11.9375 1.32035C11.7324 1.11523 11.4542 1 11.1641 1ZM11.3906 4.82812V2.64062L14.6719 5.92188H12.4844C11.8803 5.92188 11.3906 5.43219 11.3906 4.82812ZM5.92188 10.8438C5.61984 10.8438 5.375 10.5989 5.375 10.2969C5.375 9.99484 5.61984 9.75 5.92188 9.75H13.5781C13.8802 9.75 14.125 9.99484 14.125 10.2969C14.125 10.5989 13.8802 10.8438 13.5781 10.8438H5.92188ZM5.375 12.4844C5.375 12.1823 5.61984 11.9375 5.92188 11.9375H13.5781C13.8802 11.9375 14.125 12.1823 14.125 12.4844C14.125 12.7864 13.8802 13.0312 13.5781 13.0312H5.92188C5.61984 13.0312 5.375 12.7864 5.375 12.4844ZM5.92188 15.2188C5.61984 15.2188 5.375 14.9739 5.375 14.6719C5.375 14.3698 5.61984 14.125 5.92188 14.125H10.2969C10.5989 14.125 10.8438 14.3698 10.8438 14.6719C10.8438 14.9739 10.5989 15.2188 10.2969 15.2188H5.92188Z" fill="#141414"/>
                                                                                </g>
                                                                                <defs>
                                                                                <clipPath id="clip0_3040_2070">
                                                                                <rect width="20" height="20" fill="white"/>
                                                                                </clipPath>
                                                                                </defs>
                                                                                </svg>
                                                                            </span>
                                                                            <h5 class="training-card-title mt-3">{{ __('Website URL') }}</h5>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-xxl-2 col-xl-3 col-lg-4 col-md-6 col-12 mb-0 px-lg-0   ">
                                                                    <div class="card">
                                                                        <div class="card-body p-2 px-4">
                                                                            <div class="switch switch-bg d-flex ms-auto justify-content-end">
                                                                                <input type="hidden" name="settings[training_options][pure_text]" value="off">
                                                                                    <input type="checkbox" name="settings[training_options][pure_text]"
                                                                                        id="show-settings-three" {{ isset($settings['training_options']['pure_text']) && $settings['training_options']['pure_text'] == 'on' ? 'checked' : '' }}> 
                                                                                    <label for="show-settings-three" class="cr"></label>
                                                                            </div>
                                                                            <span>
                                                                                <svg class="p-2 border border-1 w-40p h-40p rounded-2 bg-color-f3" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                                <g clip-path="url(#clip0_3040_2070)">
                                                                                <path d="M11.1641 1H5.375C4.16688 1 3.1875 1.97938 3.1875 3.1875V16.3125C3.1875 17.5206 4.16688 18.5 5.375 18.5H14.125C15.3331 18.5 16.3125 17.5206 16.3125 16.3125V6.1484C16.3125 5.85832 16.1973 5.58012 15.9921 5.375L11.9375 1.32035C11.7324 1.11523 11.4542 1 11.1641 1ZM11.3906 4.82812V2.64062L14.6719 5.92188H12.4844C11.8803 5.92188 11.3906 5.43219 11.3906 4.82812ZM5.92188 10.8438C5.61984 10.8438 5.375 10.5989 5.375 10.2969C5.375 9.99484 5.61984 9.75 5.92188 9.75H13.5781C13.8802 9.75 14.125 9.99484 14.125 10.2969C14.125 10.5989 13.8802 10.8438 13.5781 10.8438H5.92188ZM5.375 12.4844C5.375 12.1823 5.61984 11.9375 5.92188 11.9375H13.5781C13.8802 11.9375 14.125 12.1823 14.125 12.4844C14.125 12.7864 13.8802 13.0312 13.5781 13.0312H5.92188C5.61984 13.0312 5.375 12.7864 5.375 12.4844ZM5.92188 15.2188C5.61984 15.2188 5.375 14.9739 5.375 14.6719C5.375 14.3698 5.61984 14.125 5.92188 14.125H10.2969C10.5989 14.125 10.8438 14.3698 10.8438 14.6719C10.8438 14.9739 10.5989 15.2188 10.2969 15.2188H5.92188Z" fill="#141414"/>
                                                                                </g>
                                                                                <defs>
                                                                                <clipPath id="clip0_3040_2070">
                                                                                <rect width="20" height="20" fill="white"/>
                                                                                </clipPath>
                                                                                </defs>
                                                                                </svg>
                                                                            </span>
                                                                            <h5 class="training-card-title mt-3">{{ __('Plain Text') }}</h5>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>

                                </div>
                                <div class="footer py-0">
                                    <div class="form-group row">
                                        <label for="btn_save" class="col-sm-3 control-label"></label>
                                        <div class="m-auto">
                                            <button type="submit"
                                                class="btn form-submit custom-btn-submit float-right feature-preference-submit-button"
                                                id="footer-btn">{{ __('Save') }}</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('mediamanager::image.modal_image')
@endsection

@section('js')
    <script>
        var dynamic_page = ['ai-doc-chat','general-chatbot'];
    </script>
    <script src="{{ asset('Modules/OpenAI/Resources/assets/js/admin/feature_preference.min.js') }}"></script>
    <script src="{{ asset('public/dist/js/custom/validation.min.js') }}"></script>

@endsection

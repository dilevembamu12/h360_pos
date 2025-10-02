
@extends('admin.layouts.app')
@section('page_title', __('Edit :x', ['x' => __('Feature Preferences')]))
@section('css')
    <link rel="stylesheet" href="{{ asset('Modules/MediaManager/Resources/assets/css/media-manager.min.css') }}">
    <link rel="stylesheet" href="{{ asset('Modules/OpenAI/Resources/assets/css/feature_preference.min.css') }}">
@endsection

@section('content')
    <!-- Main content -->
    <div class="col-sm-12" id="company-settings-container">
        <div class="card">
            <div class="card-body row">
                <div class="col-lg-3 col-12 z-index-10 pe-0 ps-0 ps-md-3" aria-labelledby="navbarDropdown">
                    @include('admin.layouts.includes.feature_preference.feature_menu')
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
                                    @foreach ( $features as $featureName => $options)

                                        @foreach ($options as $optionName => $option)

                                            <div class="tab-pane fade active show" id="v-pills-{{ $optionName }}_{{ $featureName }}" role="tabpanel" aria-labelledby="v-pills-{{ $optionName }}_{{ $featureName }}-tab">
                                                <div class="row">
                                                    @if ($featureName == 'chatbot')

                                                        @if ($optionName == 'general_options')
                                                            <div class="col-sm-12">
                                                                <!-- Language -->
                                                                <div class="form-group row">
                                                                    <div class="col-12">
                                                                        <label for="default-category" class="control-label require">{{ __('Select Languages') }}</label>
                                                                        
                                                                        <select class="form-control select2 inputFieldDesign sl_common_bx language-tags"
                                                                            name="{{$featureName}}[general_options][languages][]" multiple required>
                                                                            @foreach ($option['languages'] as $language)
                                                                                <option value="{{ $language }}" selected >
                                                                                    {{ $language }}
                                                                                </option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                </div>

                                                                <div class="form-group row">
                                                                    @php
                                                                        $uploads = [
                                                                            'default_avatar' => __('Default Avatar'),
                                                                            'default_floating_image' => __('Default Floating Image'),
                                                                        ];

                                                                        $defaultImage = [
                                                                            'default_avatar' => defaultImage('chatbots'),
                                                                            'default_floating_image' => defaultImage('chatbot_floating_image')
                                                                        ];

                                                                    @endphp

                                                                    @foreach ($uploads as $name => $label)
                                                                        <div class="col-md-6">
                                                                            <div class="form-group row preview-parent">
                                                                                <label for="{{ $name }}" class="control-label require">{{ $label }}</label>
                                                                                <div class="col-sm-12">
                                                                                    <div class="custom-file media-manager-img" data-val="single"
                                                                                        data-returntype="ids" id="image-status"
                                                                                        data-type="{{ implode(',', getFileExtensions(3)) }}">
                                                                                        <input type="hidden"
                                                                                            class="custom-file-input is-image form-control form-height"
                                                                                            name="{{ $featureName }}[general_options][{{ $name }}]" value={{ $option[$name] }}>
                                                                                        <label class="custom-file-label overflow_hidden position-relative d-flex align-items-center" 
                                                                                               for="{{ $name }}">{{ __('Upload image') }}</label>
                                                                                    </div>
                                                                                    @php
                                                                                        $file = \App\Models\File::where('id', $option[$name])->first();
                                                                                        $imageSrc = objectStorage()->url($defaultImage[$name]);
                                                                                        if ($file) {
                                                                                            $imageSrc = objectStorage()->url('public\\uploads\\' . $file->file_name);
                                                                                        }
                                                                                    @endphp
                                                                                    <div class="preview-image">
                                                                                        <div class="d-flex flex-wrap mt-2">
                                                                                            <div class="position-relative border border-1 p-1 mr-2 rounded mt-2">
                                                                                                <img width="80" class="p-1" src="{{ $imageSrc }}">
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                
                                                                                    <div class="py-1" id="note_txt_{{ $name }}">
                                                                                        <div class="d-flex mt-1 mb-3">
                                                                                            <span class="badge badge-danger h-100 mt-1">{{ __('Note') }}!</span>
                                                                                            <ul class="list-unstyled ml-3">
                                                                                                <li>{{ __('Allowed File Extensions: :y and Maximum File Size :x', [
                                                                                                    'x' => preference('file_size') . 'MB.', 
                                                                                                    'y' => implode(',', getFileExtensions(3))
                                                                                                ]) }}</li>
                                                                                            </ul>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    @endforeach
                                                                </div>
                                                            </div>
                                                        @endif

                                                        @if ($optionName == 'theme_options')
                                                            <div class="col-sm-12">

                                                                <!-- Theme Color -->
                                                                <div class="form-group row">
                                                                    <label for="theme-color" class="control-label require">{{ __('Select theme colors') }}</label>
                                                                    <div class="col-sm-12">
                                                                        <div class="theme-container mw-xl" id="theme-color">
                                                                            <div class="d-flex gap-4 flex-wrap">
                                                                                @php
                                                                                    $colors = !empty($options['theme_options']['color']) ? $options['theme_options']['color'] : ['#9163DD', '#E22861', '#FCCA19', '#FF1493', '#2c2c2c', '#5AF457', '#5707CF', '#F2EC36'];
                                                                                @endphp
                                                                                @foreach ( $colors as $color)
                                                                                    <div class="color-input-container">
                                                                                        <div class="color-container themes">
                                                                                            <input type="color" name="{{ $featureName }}[theme_options][color][]" value="{{ $color }}" />
                                                                                        </div>
                                                                                        <span class="color-code drawer">{{ $color }}</span>
                                                                                    </div>
                                                                                @endforeach
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endif

                                                        @if ($optionName == 'settings')
                                                            <div class="col-sm-12">

                                                                <!-- Max File -->
                                                                <div class="form-group row">
                                                                    <label class="col-sm-3 control-label" for="file_size">
                                                                        {{ __('Maximum File Size') }}
                                                                        <div
                                                                            class="tooltips cursor-pointer neg-transition-scale ms-2">
                                                                            <svg width="12" height="12" viewBox="0 0 12 12"
                                                                                fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                                <path fill-rule="evenodd" clip-rule="evenodd"
                                                                                    d="M12 6C12 9.31371 9.31371 12 6 12C2.68629 12 0 9.31371 0 6C0 2.68629 2.68629 0 6 0C9.31371 0 12 2.68629 12 6ZM6.66667 10C6.66667 10.3682 6.36819 10.6667 6 10.6667C5.63181 10.6667 5.33333 10.3682 5.33333 10C5.33333 9.63181 5.63181 9.33333 6 9.33333C6.36819 9.33333 6.66667 9.63181 6.66667 10ZM6 1.33333C4.52724 1.33333 3.33333 2.52724 3.33333 4H4.66667C4.66667 3.26362 5.26362 2.66667 6 2.66667H6.06287C6.76453 2.66667 7.33333 3.23547 7.33333 3.93713V4.27924C7.33333 4.62178 7.11414 4.92589 6.78918 5.03421C5.91976 5.32402 5.33333 6.13765 5.33333 7.05409V8.66667H6.66667V7.05409C6.66667 6.71155 6.88586 6.40744 7.21082 6.29912C8.08024 6.00932 8.66667 5.19569 8.66667 4.27924V3.93713C8.66667 2.49909 7.50091 1.33333 6.06287 1.33333H6Z"
                                                                                    fill="#898989" />
                                                                            </svg>
                                                                            <span
                                                                                class="tooltiptexts">{{ __('The maximum file size that a user can upload.') }}</span>
                                                                        </div>
                                                                    </label>
                                                                    <div class="col-sm-6">
                                                                        <div class="input-group">
                                                                            <div class="input-group-prepend">
                                                                                <span class="input-group-text rounded-0 rounded-start">{{ __('MB') }}</span>
                                                                            </div>
                                                                            <input class="form-control" type="number" name="{{ $featureName }}[settings][file_size]" id="file_size" value="{{ isset($option['file_size']) ? $option['file_size'] : '' }}" min="1" max="20" required oninvalid="this.setCustomValidity('{{ __('This field is required.') }}')" data-min="{{ __('The value must be :x than or equal to :y', ['x' => __('greater'), 'y' => 1]) }}" data-max="{{ __('The value must be :x than or equal to :y', ['x' => __('less'), 'y' => 20]) }}">
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <!-- File Limit -->
                                                                <div class="form-group row">
                                                                    <label class="col-sm-3 control-label" for="file_limit">
                                                                        {{ __('Maximum Upload Files') }}
                                                                        <div
                                                                            class="tooltips cursor-pointer neg-transition-scale ms-2">
                                                                            <svg width="12" height="12" viewBox="0 0 12 12"
                                                                                fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                                <path fill-rule="evenodd" clip-rule="evenodd"
                                                                                    d="M12 6C12 9.31371 9.31371 12 6 12C2.68629 12 0 9.31371 0 6C0 2.68629 2.68629 0 6 0C9.31371 0 12 2.68629 12 6ZM6.66667 10C6.66667 10.3682 6.36819 10.6667 6 10.6667C5.63181 10.6667 5.33333 10.3682 5.33333 10C5.33333 9.63181 5.63181 9.33333 6 9.33333C6.36819 9.33333 6.66667 9.63181 6.66667 10ZM6 1.33333C4.52724 1.33333 3.33333 2.52724 3.33333 4H4.66667C4.66667 3.26362 5.26362 2.66667 6 2.66667H6.06287C6.76453 2.66667 7.33333 3.23547 7.33333 3.93713V4.27924C7.33333 4.62178 7.11414 4.92589 6.78918 5.03421C5.91976 5.32402 5.33333 6.13765 5.33333 7.05409V8.66667H6.66667V7.05409C6.66667 6.71155 6.88586 6.40744 7.21082 6.29912C8.08024 6.00932 8.66667 5.19569 8.66667 4.27924V3.93713C8.66667 2.49909 7.50091 1.33333 6.06287 1.33333H6Z"
                                                                                    fill="#898989" />
                                                                            </svg>
                                                                            <span
                                                                                class="tooltiptexts">{{ __('How many files can a user upload for materials.') }}</span>
                                                                        </div>
                                                                    </label>
                                                                    <div class="col-sm-6">
                                                                        <input class="form-control positive-int-number" id="file_limit" type="number" name="{{ $featureName }}[settings][file_limit]" id="file_limit" value="{{ isset($option['file_limit']) ? $option['file_limit'] : '' }}" min="1" data-min="{{ __('The value must be :x than or equal to :y', ['x' => __('greater'), 'y' => 1]) }}" required oninvalid="this.setCustomValidity('{{ __('This field is required.') }}') }}">
                                                                    </div>
                                                                </div>

                                                                <!-- URL Limit -->
                                                                <div class="form-group row">
                                                                    <label class="col-sm-3 control-label" for="url_limit">
                                                                        {{ __('Maximum URLs') }}
                                                                        <div
                                                                            class="tooltips cursor-pointer neg-transition-scale ms-2">
                                                                            <svg width="12" height="12" viewBox="0 0 12 12"
                                                                                fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                                <path fill-rule="evenodd" clip-rule="evenodd"
                                                                                    d="M12 6C12 9.31371 9.31371 12 6 12C2.68629 12 0 9.31371 0 6C0 2.68629 2.68629 0 6 0C9.31371 0 12 2.68629 12 6ZM6.66667 10C6.66667 10.3682 6.36819 10.6667 6 10.6667C5.63181 10.6667 5.33333 10.3682 5.33333 10C5.33333 9.63181 5.63181 9.33333 6 9.33333C6.36819 9.33333 6.66667 9.63181 6.66667 10ZM6 1.33333C4.52724 1.33333 3.33333 2.52724 3.33333 4H4.66667C4.66667 3.26362 5.26362 2.66667 6 2.66667H6.06287C6.76453 2.66667 7.33333 3.23547 7.33333 3.93713V4.27924C7.33333 4.62178 7.11414 4.92589 6.78918 5.03421C5.91976 5.32402 5.33333 6.13765 5.33333 7.05409V8.66667H6.66667V7.05409C6.66667 6.71155 6.88586 6.40744 7.21082 6.29912C8.08024 6.00932 8.66667 5.19569 8.66667 4.27924V3.93713C8.66667 2.49909 7.50091 1.33333 6.06287 1.33333H6Z"
                                                                                    fill="#898989" />
                                                                            </svg>
                                                                            <span
                                                                                class="tooltiptexts">{{ __('How many URLs can a user select for materials.') }}</span>
                                                                        </div>
                                                                    </label>
                                                                    <div class="col-sm-6">
                                                                        <input class="form-control positive-int-number" id="url_limit" type="number" name="{{ $featureName }}[settings][url_limit]" id="url_limit" value="{{ isset($option['url_limit']) ? $option['url_limit'] : '' }}" min="1" data-min="{{ __('The value must be :x than or equal to :y', ['x' => __('greater'), 'y' => 1]) }}" required oninvalid="this.setCustomValidity('{{ __('This field is required.') }}') }}">
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
                                                                                                    <input type="hidden" name="{{ $featureName }}[settings][training_options][file_upload]" value="off">
                                                                                                    <input type="checkbox" name="{{ $featureName }}[settings][training_options][file_upload]"
                                                                                                        id="show-settings-one" {{ isset($option['training_options']['file_upload']) && $option['training_options']['file_upload'] == 'on' ? 'checked' : '' }}>
                                                                                                    <label for="show-settings-one" class="cr"></label>
                                                                                            </div>
                                                                                            <span>
                                                                                            <svg width="48" height="48" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                                                <rect x="0.5" y="0.5" width="47" height="47" rx="7.5" fill="#F6F3F2" stroke="#DFDFDF"/>
                                                                                                <path d="M15.5 33.9016V11.5H28.1787L33.4606 16.7819V33.9016H15.5Z" fill="white" stroke="#474746"/>
                                                                                                <path d="M28.3867 16.5748H33.9615L28.3867 11V16.5748Z" fill="#474746"/>
                                                                                                <path d="M30.9051 18.874H18.0547V19.7874H30.9051V18.874Z" fill="#474746"/>
                                                                                                <path d="M30.9051 22.6851H18.0547V23.5984H30.9051V22.6851Z" fill="#474746"/>
                                                                                                <path d="M30.9051 26.5273H18.0547V27.4407H30.9051V26.5273Z" fill="#474746"/>
                                                                                                <path d="M30.9051 30.3384H18.0547V31.2518H30.9051V30.3384Z" fill="#474746"/>
                                                                                                <path d="M21.7401 31.0316V37.2678H27.2204V31.0316H29.4567L24.4803 26.0552L19.5039 31.0316H21.7401Z" fill="#FF774B"/>
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
                                                                                                    <input type="hidden" name="{{ $featureName }}[settings][training_options][website_url]" value="off">
                                                                                                    <input type="checkbox" name="{{ $featureName }}[settings][training_options][website_url]"
                                                                                                        id="show-settings-two" {{ isset($option['training_options']['website_url']) && $option['training_options']['website_url'] == 'on' ? 'checked' : '' }}>
                                                                                                    <label for="show-settings-two" class="cr"></label>
                                                                                            </div>
                                                                                            <span>
                                                                                            <svg width="48" height="48" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                                                <rect x="0.5" y="0.5" width="47" height="47" rx="7.5" fill="#F6F3F2" stroke="#DFDFDF"/>
                                                                                                <path d="M24.7703 30.0864H23.668V34.2754H24.7703V30.0864Z" fill="#474746"/>
                                                                                                <path d="M33.3695 34.1182H15.0703V35.2205H33.3695V34.1182Z" fill="#474746"/>
                                                                                                <path d="M24.202 36.008C24.95 36.008 25.5563 35.4016 25.5563 34.6536C25.5563 33.9057 24.95 33.2993 24.202 33.2993C23.454 33.2993 22.8477 33.9057 22.8477 34.6536C22.8477 35.4016 23.454 36.008 24.202 36.008Z" fill="#FF774B"/>
                                                                                                <path d="M24.2013 30.9685C29.5937 30.9685 33.9651 26.5971 33.9651 21.2047C33.9651 15.8123 29.5937 11.4409 24.2013 11.4409C18.8089 11.4409 14.4375 15.8123 14.4375 21.2047C14.4375 26.5971 18.8089 30.9685 24.2013 30.9685Z" fill="white"/>
                                                                                                <path d="M24.2047 11C18.5669 11 14 15.5984 14 21.2047C14 26.8425 18.5984 31.4095 24.2047 31.4095C29.8425 31.4095 34.4095 26.811 34.4095 21.2047C34.4095 15.5984 29.8425 11 24.2047 11ZM26.9449 25.3622C27.0709 24.1969 27.1654 22.9055 27.1654 21.6457H30.0315C29.9685 23.3465 29.6535 24.9213 29.1181 26.2441C28.4252 25.8976 27.7008 25.5827 26.9449 25.3622ZM28.7402 27.063C27.9528 28.6063 26.8819 29.7402 25.6535 30.2756C26.1575 29.3307 26.5669 27.9134 26.8189 26.2756C27.5118 26.4646 28.1417 26.7165 28.7402 27.063ZM19.2913 26.2756C18.7559 24.9213 18.4094 23.3465 18.378 21.6772H21.2441C21.2441 22.937 21.3386 24.2283 21.4646 25.3937C20.7087 25.5827 19.9843 25.8976 19.2913 26.2756ZM21.5906 26.2441C21.8425 27.9134 22.2205 29.3307 22.7559 30.2441C21.5276 29.7402 20.4252 28.5748 19.6693 27.0315C20.2677 26.7165 20.8976 26.4646 21.5906 26.2441ZM21.4646 17.0787C21.3386 18.2441 21.2441 19.5354 21.2441 20.7953H18.378C18.4409 19.0945 18.7559 17.5197 19.2913 16.1969C19.9843 16.5433 20.7087 16.8583 21.4646 17.0787ZM19.6693 15.378C20.4567 13.8346 21.5276 12.7008 22.7559 12.1654C22.252 13.1102 21.8425 14.5276 21.5906 16.1654C20.8976 15.9764 20.2677 15.7244 19.6693 15.378ZM22.3465 17.2677C22.9449 17.3937 23.5748 17.4567 24.2047 17.4567C24.8346 17.4567 25.4646 17.3937 26.063 17.2677C26.189 18.3071 26.252 19.5039 26.2835 20.7953H22.126C22.126 19.5039 22.2205 18.3071 22.3465 17.2677ZM22.126 21.6457H26.315C26.315 22.937 26.2205 24.1024 26.0945 25.1732C25.4646 25.0787 24.8346 25.0157 24.2047 25.0157C23.5748 25.0157 22.9449 25.0787 22.3465 25.2047C22.2205 24.1339 22.126 22.937 22.126 21.6457ZM27.1654 20.7953C27.1654 19.5354 27.0709 18.2441 26.9449 17.0787C27.7008 16.8583 28.4567 16.5748 29.1181 16.1969C29.6535 17.5512 30 19.126 30.0315 20.7953H27.1654ZM26.8189 16.1969C26.5669 14.5276 26.189 13.1102 25.6535 12.1969C26.8819 12.7008 27.9843 13.8346 28.7402 15.4094C28.1417 15.7244 27.5118 15.9764 26.8189 16.1969ZM27.7953 12.6063C28.7402 13.0157 29.622 13.5512 30.378 14.2126C30.0945 14.4646 29.811 14.685 29.4961 14.9055C29.0236 14.0236 28.4567 13.2362 27.7953 12.6063ZM25.9685 16.3858C25.4016 16.5118 24.8032 16.5748 24.2047 16.5748C23.6063 16.5748 23.0079 16.5118 22.4409 16.4173C22.8819 13.5827 23.6063 11.9134 24.2047 11.9134C24.7717 11.8819 25.5276 13.5512 25.9685 16.3858ZM18.9134 14.9055C18.5984 14.685 18.315 14.4646 18.0315 14.2126C18.7874 13.5512 19.6693 12.9843 20.6142 12.6063C19.9528 13.2362 19.3858 14.0236 18.9134 14.9055ZM18.5354 15.7244C17.9055 17.2047 17.5276 18.937 17.4961 20.7953H14.8819C14.9764 18.4961 15.9213 16.4173 17.4016 14.8425C17.748 15.1575 18.1575 15.4409 18.5354 15.7244ZM17.4961 21.6457C17.5591 23.5039 17.937 25.2362 18.5354 26.7165C18.126 27 17.748 27.2835 17.4016 27.5984C15.9213 26.0236 14.9764 23.9449 14.8819 21.6457H17.4961ZM18.9134 27.5354C19.3858 28.4488 19.9528 29.2362 20.5827 29.8661C19.6378 29.4567 18.7559 28.9213 18 28.2598C18.315 27.9764 18.5984 27.7244 18.9134 27.5354ZM22.4409 26.0551C23.0079 25.9606 23.6063 25.8976 24.2047 25.8976C24.8032 25.8976 25.4016 25.9606 25.9685 26.0551C25.5276 28.8898 24.8032 30.5591 24.2047 30.5591C23.6378 30.5591 22.8819 28.8898 22.4409 26.0551ZM29.4961 27.5354C29.811 27.7559 30.0945 27.9764 30.378 28.2283C29.622 28.8898 28.7402 29.4567 27.7953 29.8346C28.4567 29.2047 29.0236 28.4173 29.4961 27.5354ZM29.874 26.7165C30.5039 25.2362 30.8819 23.5039 30.9134 21.6457H33.5276C33.4331 23.9449 32.4882 26.0236 31.0079 27.5984C30.6614 27.2835 30.252 27 29.874 26.7165ZM33.5276 20.7953H30.9134C30.8504 18.937 30.4724 17.2047 29.874 15.7244C30.2835 15.4409 30.6614 15.1575 31.0079 14.8425C32.4882 16.4173 33.4331 18.4961 33.5276 20.7953Z" fill="#474746"/>
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
                                                                                                <input type="hidden" name="{{ $featureName }}[settings][training_options][pure_text]" value="off">
                                                                                                    <input type="checkbox" name="{{ $featureName }}[settings][training_options][pure_text]"
                                                                                                        id="show-settings-three" {{ isset($option['training_options']['pure_text']) && $option['training_options']['pure_text'] == 'on' ? 'checked' : '' }}> 
                                                                                                    <label for="show-settings-three" class="cr"></label>
                                                                                            </div>
                                                                                            <span>
                                                                                            <svg width="48" height="48" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                                                <rect x="0.5" y="0.5" width="47" height="47" rx="7.5" fill="#F6F3F2" stroke="#DFDFDF"/>
                                                                                                <path d="M18.1851 20.9092C18.6601 20.9092 19.0487 20.5206 19.0487 20.0456V15.7273H20.776C21.2511 15.7273 21.6397 15.3387 21.6397 14.8637C21.6397 14.3886 21.2511 14 20.776 14H15.5941C15.1191 14 14.7305 14.3886 14.7305 14.8637C14.7305 15.3387 15.1191 15.7273 15.5941 15.7273H17.3214V20.0456C17.3214 20.5206 17.7101 20.9092 18.1851 20.9092Z" fill="#FF774B"/>
                                                                                                <path d="M34.59 14H25.9535C25.4785 14 25.0898 14.3886 25.0898 14.8637C25.0898 15.3387 25.4785 15.7273 25.9535 15.7273H34.59C35.065 15.7273 35.4537 15.3387 35.4537 14.8637C35.4537 14.3886 35.065 14 34.59 14Z" fill="#474746"/>
                                                                                                <path d="M34.59 18.3184H25.9535C25.4785 18.3184 25.0898 18.707 25.0898 19.182C25.0898 19.657 25.4785 20.0457 25.9535 20.0457H34.59C35.065 20.0457 35.4537 19.657 35.4537 19.182C35.4537 18.707 35.065 18.3184 34.59 18.3184Z" fill="#474746"/>
                                                                                                <path d="M34.5913 22.6372H13.8637C13.3886 22.6372 13 23.0259 13 23.5009C13 23.9759 13.3886 24.3645 13.8637 24.3645H34.5913C35.0663 24.3645 35.455 23.9759 35.455 23.5009C35.455 23.0259 35.0663 22.6372 34.5913 22.6372Z" fill="#474746"/>
                                                                                                <path d="M34.5913 26.9551H13.8637C13.3886 26.9551 13 27.3437 13 27.8187C13 28.2937 13.3886 28.6824 13.8637 28.6824H34.5913C35.0663 28.6824 35.455 28.2937 35.455 27.8187C35.455 27.3437 35.0663 26.9551 34.5913 26.9551Z" fill="#474746"/>
                                                                                                <path d="M34.5913 31.2725H13.8637C13.3886 31.2725 13 31.6611 13 32.1361C13 32.6111 13.3886 32.9998 13.8637 32.9998H34.5913C35.0663 32.9998 35.455 32.6111 35.455 32.1361C35.455 31.6611 35.0663 31.2725 34.5913 31.2725Z" fill="#474746"/>
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
                                                        @endif
                                                    @endif

                                                    @if ($featureName == 'ai_doc_chat') 
                                                        <div class="col-sm-12">

                                                            <!-- User Access -->
                                                            <div class="form-group row">
                                                                <label for="rating"
                                                                    class="col-sm-3 control-label text-left require">{{ __("User Access Disabled (Provider-Model)") }}</label>
                                                                <div class="col-9 d-flex mt-neg-2">
                                                                    <div class="ltr:me-3 rtl:ms-3">
                                                                        <div class="switch switch-bg d-inline m-r-10">
                                                                            <input type="hidden" name="{{ $featureName }}[general_options][user_access_disable]" value="off">
                                                                            <input type="checkbox" name="{{ $featureName }}[general_options][user_access_disable]"
                                                                                class="checkActivity" id="show-user-access"
                                                                                {{ isset($option['user_access_disable']) && $option['user_access_disable'] == 'on' ? 'checked' : '' }} >
                                                                            <label for="show-user-access" class="cr"></label>
                                                                        </div>
                                                                    </div>
                                                                    <div class="ml-2 mt-2">
                                                                        <span>{{ __('Enable user access to allow users to select their preferred provider and model. If the option is disabled, the administration will have the authority to choose the preferred provider and model on behalf of the users.') }}</span>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="form-group row conditional" data-if="#show-user-access">
                                                                <div class="col-12">
                                                                    <label for="provider-select" class="control-label require">{{ __('Select Provider') }}</label>
                                                                    <select id="provider-select" class="form-control select2 inputFieldDesign sl_common_bx"
                                                                        name="{{ $featureName }}[general_options][provider]" required>
                                                                        <option value="">{{ __('Select a Provider') }}</option>
                                                                        @foreach ($option['providerModels'] as $key => $model)
                                                                            <option value="{{ $key }}" {{ isset($option['provider']) && $option['provider'] == $key ? 'selected' : '' }} >{{ ucfirst($key) }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            
                                                            <div class="form-group row conditional" data-if="#show-user-access">
                                                                <div class="col-12">
                                                                    <label for="model-select" class="control-label require">{{ __('Select Model') }}</label>
                                                                    <select id="model-select" class="form-control select2 inputFieldDesign sl_common_bx"
                                                                        name="{{ $featureName }}[general_options][model]" required>
                                                                        
                                                                        @if (isset($option['model']))
                                                                            <option value="{{ $option['model'] }}" selected>{{ $option['model'] }}</option>
                                                                        @else 
                                                                            <option value="">{{ __('Select a Model') }}</option>
                                                                        @endif

                                                                    </select>
                                                                </div>
                                                            </div>

                                                        </div>
                                                    @endif

                                                    @if ($featureName == 'ai_detector') 
                                                    <div class="col-sm-12">

                                                        <!-- Max File -->
                                                        <div class="form-group row">
                                                            <label class="col-sm-3 control-label" for="file_size">
                                                                {{ __('Maximum File Size') }}
                                                                <div
                                                                    class="tooltips cursor-pointer neg-transition-scale ms-2">
                                                                    <svg width="12" height="12" viewBox="0 0 12 12"
                                                                        fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                                            d="M12 6C12 9.31371 9.31371 12 6 12C2.68629 12 0 9.31371 0 6C0 2.68629 2.68629 0 6 0C9.31371 0 12 2.68629 12 6ZM6.66667 10C6.66667 10.3682 6.36819 10.6667 6 10.6667C5.63181 10.6667 5.33333 10.3682 5.33333 10C5.33333 9.63181 5.63181 9.33333 6 9.33333C6.36819 9.33333 6.66667 9.63181 6.66667 10ZM6 1.33333C4.52724 1.33333 3.33333 2.52724 3.33333 4H4.66667C4.66667 3.26362 5.26362 2.66667 6 2.66667H6.06287C6.76453 2.66667 7.33333 3.23547 7.33333 3.93713V4.27924C7.33333 4.62178 7.11414 4.92589 6.78918 5.03421C5.91976 5.32402 5.33333 6.13765 5.33333 7.05409V8.66667H6.66667V7.05409C6.66667 6.71155 6.88586 6.40744 7.21082 6.29912C8.08024 6.00932 8.66667 5.19569 8.66667 4.27924V3.93713C8.66667 2.49909 7.50091 1.33333 6.06287 1.33333H6Z"
                                                                            fill="#898989" />
                                                                    </svg>
                                                                    <span
                                                                        class="tooltiptexts">{{ __('The maximum file size that a user can upload.') }}</span>
                                                                </div>
                                                            </label>
                                                            <div class="col-sm-6">
                                                                <div class="input-group">
                                                                    <div class="input-group-prepend">
                                                                        <span class="input-group-text rounded-0 rounded-start">{{ __('MB') }}</span>
                                                                    </div>
                                                                    <input class="form-control" type="number" name="{{ $featureName }}[settings][file_size]" id="file_size" value="{{ isset($option['file_size']) ? $option['file_size'] : '' }}" min="1" max="20" required oninvalid="this.setCustomValidity('{{ __('This field is required.') }}')" data-min="{{ __('The value must be :x than or equal to :y', ['x' => __('greater'), 'y' => 1]) }}" data-max="{{ __('The value must be :x than or equal to :y', ['x' => __('less'), 'y' => 20]) }}">
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <!-- Training Options -->
                                                        <div class="form-group row">
                                                            <label for="" class="control-label require">{{ __('Feature Options') }}</label>
                                                            <div class="col-sm-12">
                                                                <div class="training-option-container mt-4">
                                                                    <div class="row gap-lg-3">
                                                                        <div class="col-xxl-2 col-xl-3 col-lg-4 col-md-6 col-12 mb-0 px-lg-0  ">
                                                                            <div class="card">
                                                                                <div class="card-body p-2 px-4">
                                                                                    <div class="switch switch-bg d-flex ms-auto justify-content-end">
                                                                                            <input type="hidden" name="{{ $featureName }}[settings][feature_options][file_upload]" value="off">
                                                                                            <input type="checkbox" name="{{ $featureName }}[settings][feature_options][file_upload]"
                                                                                                id="show-settings-for-detector-one" {{ isset($option['feature_options']['file_upload']) && $option['feature_options']['file_upload'] == 'on' ? 'checked' : '' }}>
                                                                                            <label for="show-settings-for-detector-one" class="cr"></label>
                                                                                    </div>
                                                                                    <span>
                                                                                    <svg width="48" height="48" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                                        <rect x="0.5" y="0.5" width="47" height="47" rx="7.5" fill="#F6F3F2" stroke="#DFDFDF"/>
                                                                                        <path d="M15.5 33.9016V11.5H28.1787L33.4606 16.7819V33.9016H15.5Z" fill="white" stroke="#474746"/>
                                                                                        <path d="M28.3867 16.5748H33.9615L28.3867 11V16.5748Z" fill="#474746"/>
                                                                                        <path d="M30.9051 18.874H18.0547V19.7874H30.9051V18.874Z" fill="#474746"/>
                                                                                        <path d="M30.9051 22.6851H18.0547V23.5984H30.9051V22.6851Z" fill="#474746"/>
                                                                                        <path d="M30.9051 26.5273H18.0547V27.4407H30.9051V26.5273Z" fill="#474746"/>
                                                                                        <path d="M30.9051 30.3384H18.0547V31.2518H30.9051V30.3384Z" fill="#474746"/>
                                                                                        <path d="M21.7401 31.0316V37.2678H27.2204V31.0316H29.4567L24.4803 26.0552L19.5039 31.0316H21.7401Z" fill="#FF774B"/>
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
                                                                                        <input type="hidden" name="{{ $featureName }}[settings][feature_options][content_description]" value="off">
                                                                                            <input type="checkbox" name="{{ $featureName }}[settings][feature_options][content_description]"
                                                                                                id="show-settings-for-detector" {{ isset($option['feature_options']['content_description']) && $option['feature_options']['content_description'] == 'on' ? 'checked' : '' }}> 
                                                                                            <label for="show-settings-for-detector" class="cr"></label>
                                                                                    </div>
                                                                                    <span>
                                                                                    <svg width="48" height="48" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                                        <rect x="0.5" y="0.5" width="47" height="47" rx="7.5" fill="#F6F3F2" stroke="#DFDFDF"/>
                                                                                        <path d="M18.1851 20.9092C18.6601 20.9092 19.0487 20.5206 19.0487 20.0456V15.7273H20.776C21.2511 15.7273 21.6397 15.3387 21.6397 14.8637C21.6397 14.3886 21.2511 14 20.776 14H15.5941C15.1191 14 14.7305 14.3886 14.7305 14.8637C14.7305 15.3387 15.1191 15.7273 15.5941 15.7273H17.3214V20.0456C17.3214 20.5206 17.7101 20.9092 18.1851 20.9092Z" fill="#FF774B"/>
                                                                                        <path d="M34.59 14H25.9535C25.4785 14 25.0898 14.3886 25.0898 14.8637C25.0898 15.3387 25.4785 15.7273 25.9535 15.7273H34.59C35.065 15.7273 35.4537 15.3387 35.4537 14.8637C35.4537 14.3886 35.065 14 34.59 14Z" fill="#474746"/>
                                                                                        <path d="M34.59 18.3184H25.9535C25.4785 18.3184 25.0898 18.707 25.0898 19.182C25.0898 19.657 25.4785 20.0457 25.9535 20.0457H34.59C35.065 20.0457 35.4537 19.657 35.4537 19.182C35.4537 18.707 35.065 18.3184 34.59 18.3184Z" fill="#474746"/>
                                                                                        <path d="M34.5913 22.6372H13.8637C13.3886 22.6372 13 23.0259 13 23.5009C13 23.9759 13.3886 24.3645 13.8637 24.3645H34.5913C35.0663 24.3645 35.455 23.9759 35.455 23.5009C35.455 23.0259 35.0663 22.6372 34.5913 22.6372Z" fill="#474746"/>
                                                                                        <path d="M34.5913 26.9551H13.8637C13.3886 26.9551 13 27.3437 13 27.8187C13 28.2937 13.3886 28.6824 13.8637 28.6824H34.5913C35.0663 28.6824 35.455 28.2937 35.455 27.8187C35.455 27.3437 35.0663 26.9551 34.5913 26.9551Z" fill="#474746"/>
                                                                                        <path d="M34.5913 31.2725H13.8637C13.3886 31.2725 13 31.6611 13 32.1361C13 32.6111 13.3886 32.9998 13.8637 32.9998H34.5913C35.0663 32.9998 35.455 32.6111 35.455 32.1361C35.455 31.6611 35.0663 31.2725 34.5913 31.2725Z" fill="#474746"/>
                                                                                    </svg>
                                                                                    </span>
                                                                                    <h5 class="training-card-title mt-3">{{ __('Description') }}</h5>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
        
                                                    </div>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                        
                                    @endforeach
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
        var dynamic_page = ['general_options_ai-doc-chat','general_options_chatbot'];
        var providerModels = {!! json_encode($features['ai_doc_chat']['general_options']['providerModels'] ?? []) !!};
    </script>
    <script src="{{ asset('Modules/OpenAI/Resources/assets/js/admin/feature_preference.min.js') }}"></script>
    <script src="{{ asset('public/dist/js/custom/validation.min.js') }}"></script>
    <script src="{{ asset('public/dist/js/condition.min.js') }}"></script>
    <script src="{{ asset('Modules/OpenAI/Resources/assets/js/feature-options.min.js') }}"></script>
@endsection


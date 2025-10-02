@extends('admin.layouts.app')
@section('page_title', __('Edit :x', ['x' => __('AI Preferences')]))

@section('content')
    <!-- Main content -->
    <div class="col-sm-12" id="preference-container">
        <div class="card">
            <div class="card-body row" id="preference-container">
                <div class="col-lg-3 col-12 z-index-10 pe-0 ps-0 ps-md-3" aria-labelledby="navbarDropdown">
                    <div class="card card-info shadow-none" id="nav">
                        <div class="card-header pt-4 border-bottom text-nowrap">
                            <h5 id="general-settings">{{ __('Content Types') }}</h5>
                        </div>
                        <ul class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                            <li><a class="nav-link text-left tab-name active" id="v-pills-setup-tab" data-bs-toggle="pill"
                                href="#v-pills-setup" role="tab" aria-controls="v-pills-setup"
                                aria-selected="true" data-id="{{ __('AI Setup') }}">{{ __('AI Setup') }}</a></li>
                            <li><a class="nav-link text-left tab-name" id="v-pills-voiceover-tab" data-bs-toggle="pill"
                                    href="#v-pills-voiceover" role="tab" aria-controls="v-pills-voiceover"
                                    aria-selected="true" data-id="{{ __('Voiceover') }}">{{ __('Voiceover') }}</a></li>
                            <li><a class="nav-link text-left tab-name" id="v-pills-bad-word-tab" data-bs-toggle="pill"
                                href="#v-pills-bad-word" role="tab" aria-controls="v-pills--bad-word"
                                aria-selected="true" data-id="{{ __('Bad Words') }}">{{ __('Bad Words') }}</a></li>
                            <li><a class="nav-link text-left tab-name" id="v-pills-access-tab" data-bs-toggle="pill"
                                href="#v-pills-access" role="tab" aria-controls="v-pills-access"
                                aria-selected="true" data-id="{{ __('User Access') }}">{{ __('User Assess') }}</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-9 col-12 ps-0">
                    <div class="card card-info shadow-none">
                        <div class="card-header pt-4 border-bottom">
                            <h5><span id="theme-title">{{ __('Document') }}</span></h5>
                        </div>
                        <div class="card-body">
                            <form method="post" action="{{ route('admin.features.preferences.create') }}" id="aiSettings">
                                @csrf

                                <div class="tab-content p-0 box-shadow-unset" id="topNav-v-pills-tabContent">
                                    {{-- OpenAI Setup --}}
                                    <div class="tab-pane fade active show" id="v-pills-setup" role="tabpanel" aria-labelledby="v-pills-setup-tab">
                                        <div class="row">
                                            <div class="d-flex justify-content-between mt-16p">
                                                <div id="#headingOne">
                                                    <h5 class="text-btn">{{ __('Ai Key') }}</h5>
                                                </div>
                                                <div class="mr-3"></div>
                                            </div>
                                            <div class="card-body p-l-15">
                                                <input type="hidden" value="{{ csrf_token() }}" name="_token" id="token">
                                                <div class="form-group row">
                                                    <label class="col-sm-3 control-label text-left require">{{ __('OpenAi Key') }}</label>
                                                    <div class="col-sm-8">
                                                        <input type="text"
                                                            value="{{ config('openAI.is_demo') ? 'sk-xxxxxxxxxxxxxxx' : config('aiKeys.OPENAI.API_KEY') }}"
                                                            class="form-control inputFieldDesign" name="apiKey[openai]" id="openai_key">
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col-sm-3 control-label text-left">{{ __('Stable Diffusion Key') }}</label>
                                                    <div class="col-sm-8">
                                                        <input type="text"
                                                            value="{{ config('openAI.is_demo') ? 'sk-xxxxxxxxxxxxxxx' : (config('aiKeys.STABLEDIFFUSION.API_KEY') ? config('aiKeys.STABLEDIFFUSION.API_KEY') : ($openai['stablediffusion'] ?? '' )) }}"
                                                            class="form-control inputFieldDesign" name="apiKey[stablediffusion]" id="stable_diffusion_key">
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col-sm-3 control-label text-left">{{ __('Google API Key') }}</label>
                                                    <div class="col-sm-8">
                                                        <input type="text"
                                                            value="{{ config('openAI.is_demo') ? 'sk-xxxxxxxxxxxxxxx' : (config('aiKeys.GOOGLE.API_KEY') ? config('aiKeys.GOOGLE.API_KEY') : ($openai['google_api'] ?? '' )) }}"
                                                            class="form-control inputFieldDesign" name="apiKey[google]" id="googleApi_key">
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col-sm-3 control-label text-left">{{ __('Clipdrop API Key') }}</label>
                                                    <div class="col-sm-8">
                                                        <input type="text"
                                                            value="{{ config('openAI.is_demo') ? 'sk-xxxxxxxxxxxxxxx' : (config('aiKeys.CLIPDROP.API_KEY') ? config('aiKeys.CLIPDROP.API_KEY') : ($openai['clipdrop_api'] ?? '')) }}"
                                                            class="form-control inputFieldDesign" name="apiKey[clipdrop]" id="clipdropApi_key">
                                                    </div>
                                                </div>
                                                @php
                                                    $addon = \Modules\Addons\Entities\Addon::find('anthropic');
                                                @endphp

                                                @if ($addon && $addon->isEnabled())
                                                    <div class="form-group row">
                                                        <label class="col-sm-3 control-label text-left">{{ __('Anthropic API Key') }}</label>
                                                        <div class="col-sm-8">
                                                            <input type="text"
                                                                value="{{ config('openAI.is_demo') ? 'sk-xxxxxxxxxxxxxxx' : config('aiKeys.ANTHROPIC.API_KEY') }}"
                                                                class="form-control inputFieldDesign" name="apiKey[anthropic]" id="anthropicApi_key">
                                                        </div>
                                                    </div>
                                                @endif

                                                @doAction('before_provider_api_key_section_retrived')

                                                <div class="form-group row">
                                                    <label class="col-sm-3 control-label text-left require">{{ __('Max Length for Short Description') }}</label>
                                                    <div class="col-sm-8">
                                                        <input type="text"
                                                            value="{{ $openai['short_desc_length'] ?? '' }}"
                                                            class="form-control inputFieldDesign positive-int-number" name="short_desc_length" required pattern="^(?:[1-9]|[1-9][0-9]{1,2}|1000)$"
                                                            oninvalid="this.setCustomValidity('{{ __('This field is required.') }}')" data-pattern="{{ __('Value exceeds the maximum limit of 1000.') }}">
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col-sm-3 control-label text-left require">{{ __('Max Length for Long Description ') }}</label>
                
                                                    <div class="col-sm-8">
                                                        <input type="text"
                                                            value="{{ $openai['long_desc_length'] ?? '' }}"
                                                            class="form-control inputFieldDesign positive-int-number" name="long_desc_length" required  pattern="^(?:[1-9]|[1-9][0-9]{1,2}|1000)$"
                                                            oninvalid="this.setCustomValidity('{{ __('This field is required.') }}')" data-pattern="{{ __('Value exceeds the maximum limit of 1000.') }}">
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col-sm-3 text-left control-label">{{ __('Word Count method based on') }}</label>
                                                    <div class="col-sm-8">
                                                        <select class="form-control select2-hide-search inputFieldDesign" name="word_count_method">
                                                            <option value="token" {{ $openai['word_count_method'] == 'token' ? 'selected="selected"' : '' }}>{{ __('Token') }}</option>
                                                            <option value="count_word_function" {{ $openai['word_count_method'] == 'count_word_function' ? 'selected="selected"' : '' }}>{{ __('Word Counter') }}</option>
                                                        </select>
                                                        <div class="py-1" id="note_txt_1">
                                                            <p><span class="badge badge-danger h-100 mt-1"> {{__('Note') }}!</span> {!! __('Token counting is performed in accordance with OpenAI\'s token counting guidelines, as outlined in their official :x. Meanwhile, word counting is based on the conventional method', ['x' => '
                                                            <a href="https://help.openai.com/en/articles/4936856-what-are-tokens-and-how-to-count-them" target="_blank">' . __('documentation') . '</a>']) !!} </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="d-flex justify-content-between pt-3">
                                                <div id="#headingOne">
                                                    <h5 class="text-btn">{{ __('Live Mode') }}</h5>
                                                </div>
                                                <div class="mr-3"></div>
                                            </div>
                                            <div class="card-body p-l-15">
                                                <input type="hidden" value="{{ csrf_token() }}" name="_token" id="token">
                                                <div class="form-group row">
                                                    <label class="col-sm-3 text-left control-label">{{ __('OpenAI Model') }}</label>
                                                    <div class="col-sm-8">
                                                        <select class="form-control select2-hide-search inputFieldDesign" name="ai_model">
                                                            @foreach($aiModels as $key => $aiModel)
                                                            <option value="{{ $key }}"
                                                                {{ $key == $openai['ai_model'] ? 'selected="selected"' : '' }}>
                                                                {{ $aiModel }} ({{ $aiModelDescription[$key] }})</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <!--smtp form start here-->
                                                <span id="smtp_form">
                                                    <div class="form-group row">
                                                        <label class="col-sm-3 control-label text-left require">{{ __('Max Result Length (Token)') }}</label>
                                                        <div class="col-sm-8">
                                                            <input type="text"
                                                                value="{{ $openai['max_token_length'] ?? $openai['max_token_length'] }}"
                                                                class="form-control inputFieldDesign positive-int-number" name="max_token_length" required
                                                                oninvalid="this.setCustomValidity('{{ __('This field is required.') }}')">
                                                        </div>
                                                    </div>
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Voiceover --}}
                                    <div class="tab-pane fade" id="v-pills-voiceover" role="tabpanel" aria-labelledby="v-pills-voiceover-tab">
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <div class="row">
                                                    <div class="col-12">
                                                        <label for="default-category" class="control-label require">{{ __('Maximum Text Blocks Limit') }}</label>
                                                        <div>
                                                            <input type="number" class="form-control" name="conversation_limit" value="{{ $openai['conversation_limit'] ?? 1 }}" min="1" data-min="{{ __('This value must be greater than :x.', ['x' => '0']) }}" required />
                                                        </div>
                                                        <div class="d-flex py-1" id="note_txt_1">
                                                            <span class="badge badge-danger h-100 mt-1"> {{__('Note') }}!</span>
                                                            <ul class="list-unstyled ml-3">
                                                                <li>{{ __('If you increase the value, it will take longer to generate. Please note that the minimum value must be equal to or greater than 1.') }} </li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <div class="form-group row">
                                                    <div class="col-12">
                                                        <label for="default-category" class="control-label require">{{ __('Select Languages') }}</label>
                                                        <select class="form-control select2 inputFieldDesign sl_common_bx"
                                                            name="meta[text_to_speech][language][]" multiple required>
                                                            @foreach ($languages as $language)
                                                                @if ( !array_key_exists($language->name, $omitTextToSpeechLanguages) )
                                                                    <option value="{{ $language->name }}"
                                                                        {{ in_array($language->name, processPreferenceData($meta[4]->language) ) ? 'selected' : '' }}> {{ $language->name }} 
                                                                    </option>
                                                                @endif
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <div class="col-12">
                                                        <label for="default-category" class="control-label require">{{ __('Select Volumes') }}</label>
                                                        <select class="form-control select2 inputFieldDesign sl_common_bx"
                                                            name="meta[text_to_speech][volume][]" multiple required>
                                                            @foreach ($preferences['text_to_speech']['volume'] as $key => $volume)
                                                                <option value="{{ $key }}" {{ in_array($volume, processPreferenceData($meta[4]->volume)) ? 'selected' : '' }} > {{ $volume }} </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="form-group row">
                                                    <div class="col-12">
                                                        <label for="default-category" class="control-label require">{{ __('Pitch') }}</label>
                                                        <select class="form-control select2 inputFieldDesign sl_common_bx"
                                                            name="meta[text_to_speech][pitch][]" multiple required>
                                                            @foreach ($preferences['text_to_speech']['pitch'] as $key => $pitch)
                                                                <option value="{{ $key }}" {{ in_array($pitch, processPreferenceData($meta[4]->pitch)) ? 'selected' : '' }} > {{ $pitch }} </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="form-group row">
                                                    <div class="col-12">
                                                        <label for="default-category" class="control-label require">{{ __('Speed') }}</label>
                                                        <select class="form-control select2 inputFieldDesign sl_common_bx"
                                                            name="meta[text_to_speech][speed][]" multiple required>
                                                            @foreach ($preferences['text_to_speech']['speed'] as $key => $speed)
                                                                <option value="{{ $key }}" {{ in_array($speed, processPreferenceData($meta[4]->speed)) ? 'selected' : '' }} > {{ $speed }} </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="form-group row">
                                                    <div class="col-12">
                                                        <label for="default-category" class="control-label require">{{ __('Pause') }}</label>
                                                        <select class="form-control select2 inputFieldDesign sl_common_bx"
                                                            name="meta[text_to_speech][pause][]" multiple required>
                                                            @foreach ($preferences['text_to_speech']['pause'] as $key => $pause)
                                                                <option value="{{ $key }}" {{ in_array($pause, processPreferenceData($meta[4]->pause)) ? 'selected' : '' }} > {{ $pause }} </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="form-group row">
                                                    <div class="col-12">
                                                        <label for="default-category" class="control-label require">{{ __('Audio Effect') }}</label>
                                                        <select class="form-control select2 inputFieldDesign sl_common_bx"
                                                            name="meta[text_to_speech][audio_effect][]" multiple required>
                                                            @foreach ($preferences['text_to_speech']['audio_effect'] as $key => $audio_effect)
                                                                <option value="{{ $key }}" {{ in_array($audio_effect, processPreferenceData($meta[4]->audio_effect)) ? 'selected' : '' }} > {{ $audio_effect }} </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="form-group row">
                                                    <div class="col-12">
                                                        <label for="default-category" class="control-label require">{{ __('Converted To') }}</label>
                                                        <select class="form-control select2 inputFieldDesign sl_common_bx"
                                                            name="meta[text_to_speech][target_format][]" multiple required>
                                                            @foreach ($preferences['text_to_speech']['target_format'] as $key => $target_format)
                                                                <option value="{{ $key }}" {{ in_array($target_format, processPreferenceData($meta[4]->target_format)) ? 'selected' : '' }} > {{ $target_format }} </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>

                                    {{-- Bad Words --}}
                                    <div class="tab-pane fade" id="v-pills-bad-word" role="tabpanel" aria-labelledby="v-pills-bad-word-tab">
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <div class="form-group row">
                                                    <div class="col-12">
                                                        <label for="default-category" class="control-label">{{ __('Words') }}</label>
                                                        <div class="col-sm-12">
                                                            <textarea class="form-control" rows="5" name="bad_words">{{ $openai['bad_words'] ?? '' }}</textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="py-1" id="note_txt_1">
                                                    <div class="d-flex mt-1 mb-3">
                                                        <span class="badge badge-danger h-100 mt-1"> {{__('Note') }}!</span>
                                                        <ul class="list-unstyled ml-3">
                                                            <li>{{ __('After using each bad word, please differentiate them using a comma (,).') }} </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- User Access --}}
                                    <div class="tab-pane fade" id="v-pills-access" role="tabpanel" aria-labelledby="v-pills-access-tab">
                                        <div class="row">
                                            @php
                                                $features = [
                                                    'hide_template' => [
                                                        'label' => __('Disable Template'),
                                                        'description' => __('Toggle to enable or disable the template features.'),
                                                    ],
                                                    'hide_image' => [
                                                        'label' => __('Disable Image'),
                                                        'description' => __('Toggle to enable or disable image features.'),
                                                    ],
                                                    'hide_code' => [
                                                        'label' => __('Disable Code'),
                                                        'description' => __('Toggle to enable or disable code features.'),
                                                    ],
                                                    'hide_speech_to_text' => [
                                                        'label' => __('Disable Speech to Text'),
                                                        'description' => __('Toggle to enable or disable speech-to-text features.'),
                                                    ],
                                                    'hide_text_to_speech' => [
                                                        'label' => __('Disable Voiceover'),
                                                        'description' => __('Toggle to enable or disable voiceover features.'),
                                                    ],
                                                    'hide_long_article' => [
                                                        'label' => __('Disable Long Article'),
                                                        'description' => __('Toggle to enable or disable support for long articles.'),
                                                    ],
                                                    'hide_chat' => [
                                                        'label' => __('Disable Chat'),
                                                        'description' => __('Toggle to enable or disable chat features.'),
                                                    ],
                                                    'hide_plagiarism' => [
                                                        'label' => __('Disable Plagiarism'),
                                                        'description' => __('Toggle to enable or disable plagiarism checking features.'),
                                                    ],
                                                    'hide_aichatbot' => [
                                                        'label' => __('Disable Ai Chatbot'),
                                                        'description' => __('Toggle to enable or disable the AI chatbot feature.'),
                                                    ],
                                                    'hide_ai_detector' => [
                                                        'label' => __('Disable Ai Detector'),
                                                        'description' => __('Toggle to enable or disable the AI Detector feature.'),
                                                    ],
                                                    'hide_video' => [
                                                        'label' => __('Disable Ai Video'),
                                                        'description' => __('Toggle to enable or disable the AI Video feature.'),
                                                    ]
                                                ];
                                            @endphp
                                            <div class="col-sm-12">
                                                <div class="card-body px-2 ">
                                                    @foreach ($features as $name => $feature)
                                                        <div class="row p-l-15">
                                                            <div class="form-group row">
                                                                <div class="col-lg-8 col-md-8 col-sm-12">
                                                                    <label for="{{ $name }}" class="control-label"><b>{{ $feature['label'] }}</b></label>
                                                                    <span>{{ $feature['description'] }}</span>
                                                                </div>
                                                                <div class="col-lg-4 col-md-4 col-sm-12 d-flex justify-content-end align-self-center p-r-0 m-r-0">
                                                                    <div class="ltr:me-3 rtl:ms-3">
                                                                        <input type="hidden" name="user_access[{{ $name }}]" value="0">
                                                                        <div class="switch switch-bg d-inline">
                                                                            <input type="checkbox" name="user_access[{{ $name }}]" class="checkActivity" id="{{ $name }}" value="1"  
                                                                            {{ isset($userPermission->$name) && ($userPermission->$name == 1)  ? 'checked' : '' }}
                                                                            >
                                                                            <label for="{{ $name }}" class="cr"></label>
                                                                        </div>

                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
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
                                                class="btn form-submit custom-btn-submit float-right package-submit-button"
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
        var openai_key = "{{ $openai['openai'] ?? '' }}";
        var stable_diffusion_key = "{{ $openai['stablediffusion'] ?? '' }}";
        var googleApi_key = "{{ $openai['google_api'] ?? '' }}";
        var clipdropApi_key = "{{ $openai['clipdrop_api'] ?? '' }}";
        const openAI = @json(config('openAI'));
    </script>
    <script src="{{ asset('public/dist/js/custom/openai-settings.min.js') }}"></script>
    <script src="{{ asset('public/dist/js/custom/validation.min.js') }}"></script>
    <script src="{{ asset('Modules/OpenAI/Resources/assets/js/admin/preference.min.js') }}"></script>
@endsection


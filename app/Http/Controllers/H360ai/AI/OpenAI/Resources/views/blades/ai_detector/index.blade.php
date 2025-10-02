@extends('layouts.user_master')
@section('page_title', __('AI Detector'))
@section('css')
    <link rel="stylesheet" media="all" href="{{ asset('Modules/OpenAI/Resources/assets/css/dark.min.css') }}">

@endsection
@section('content')

@php
    $pageLeft = 0;
    $pageLimit = 0;
    if ($userSubscription && in_array($userSubscription->status, ['Active', 'Cancel'])) {
        $pageLeft = $featureLimit['page']['remain'];
        $pageLimit = $featureLimit['page']['limit'];
    }
@endphp
    <div
        class="w-[68.9%] 5xl:w-[85.9%] bg-[#F6F3F2] dark:bg-[#292929] flex flex-col flex-1 border-l dark:border-[#474746] border-color-DF">
        <div class="px-5 6xl:px-[100px] 7xl:px-[140px] 8xl:px-[200px] 9xl:px-[261px]  pt-[76px] lg:pt-[80px] pb-5">
            <p class="text-color-14 text-24 font-semibold font-RedHat dark:text-white">
                {{ __('AI-Powered Content Authenticity Checker') }}</p>

            <p class="text-color-89 text-[13px] leading-5 font-medium font-Figtree mt-2">
                {{ __('Detect AI-Generated Content with Advanced AI Analysis and Real-Time Accuracy.') }}
            </p>
            <div class="subscription-main grid gap-6 xl:grid-cols-2 grid-cols-1">
                <form class="mt-5" id="detector-form">
                    @if($pageLeft)
                        <div class="bg-white dark:bg-[#474746] p-3 rounded-xl flex items-center justify-start mt-6 gap-2.5">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18"
                                fill="none">
                                <g clip-path="url(#clip0_4514_3509)">
                                    <path
                                        d="M13.9714 7.00665C13.8679 6.84578 13.6901 6.75015 13.5 6.75015H9.56255V0.562738C9.56255 0.297241 9.37693 0.0677446 9.11706 0.0126204C8.85269 -0.0436289 8.59394 0.0924942 8.48594 0.334366L3.986 10.4592C3.90838 10.6325 3.92525 10.835 4.02875 10.9936C4.13225 11.1533 4.31 11.2501 4.50012 11.2501H8.43757V17.4375C8.43757 17.703 8.62319 17.9325 8.88306 17.9876C8.92244 17.9955 8.96181 18 9.00006 18C9.21831 18 9.42193 17.8729 9.51418 17.6659L14.0141 7.54102C14.0906 7.36664 14.076 7.1664 13.9714 7.00665Z"
                                        fill="url(#paint0_linear_4514_3509)"></path>
                                </g>
                                <defs>
                                    <linearGradient id="paint0_linear_4514_3509" x1="10.5204" y1="15.7845" x2="2.32033"
                                        y2="5.3758" gradientUnits="userSpaceOnUse">
                                        <stop offset="0" stop-color="#E60C84"></stop>
                                        <stop offset="1" stop-color="#FFCF4B"></stop>
                                    </linearGradient>
                                    <clipPath id="clip0_4514_3509">
                                        <rect width="18" height="18" fill="white"></rect>
                                    </clipPath>
                                </defs>
                            </svg>
                            <p class="text-color-14 dark:text-white font-Figtree font-normal">
                                {!! __('Credits Balance: :x pages left', ['x' => "<span class='total-page-left font-semibold dark:text-[#FCCA19]'>" . ($pageLimit == -1 ? __('Unlimited') : ($pageLeft < 0 ? 0 : $pageLeft)) . "</span>"]) !!}      
                        </div>
                    @endif

                    <div class="bg-white dark:bg-[#3A3A39] p-6 rounded-xl mt-5">
                        <div class="custom-dropdown-arrow font-normal text-14 text-[#141414] dark:text-white {{ count($aiProviders) <= 1 ? 'hidden' : '' }}">
                            <label>{{ __('Provider') }}</label>
                            <select class="select block w-full mt-[3px] text-base leading-6 font-medium text-color-FFR bg-white bg-clip-padding bg-no-repeat dark:bg-[#333332] rounded-xl dark:rounded-2xl focus:text-color-2C focus:bg-white focus:border-color-89 focus:outline-none form-control" name="provider" id="provider">
                                @foreach ($aiProviders as $provider => $value)
                                    @php
                                        $providerName = str_replace('aidetector_', '', $provider);
                                    @endphp
                                        <option value="{{ $providerName }}"> {{ ucwords($providerName) }} </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="flex flex-col">
                            @if ($featurePreferecnes['feature_options']['file_upload'] == 'on')
                                <div class="flex flex-col">
                                    <label class="font-normal text-14 text-color-2C dark:text-white" for="">{{ __('File') }}</label>
                                    <input required class="w-full cursor-pointer rounded-xl border border-color-89 dark:border-color-47 px-3 file:-mx-3 file:cursor-pointer file:overflow-hidden file:rounded-none file:border-0 file:border-solid file:border-inherit dark:file:!bg-[#474746] file:bg-color-DF file:px-3 file:py-2 bg-white dark:bg-[#333332] file:[border-inline-end-width:1px] file:[margin-inline-end:0.75rem] file:text-color-14 dark:file:text-white form-control text-color-14 dark:text-white file:transition-none focus:outline-none focus:dark:!border-color-47" name="file" accept="file/*" id="file_input" type="file">
                                </div>
                            @endif
                            
                            @if ($featurePreferecnes['feature_options']['content_description'] == 'on')
                                <label class="require text-color-14 dark:text-white font-Figtree text-14 font-normal mb-1.5 mt-2"
                                    for="">{{ __('Content Description') }}</label>
                                <textarea
                                    class=" questions dynamic-input peer py-1.5 mt-1.5 text-base overflow-y-scroll middle-sidebar-scroll leading-6 font-light !text-color-14 dark:!text-white bg-white dark:bg-[#333332] bg-clip-padding bg-no-repeat border border-solid border-color-89 dark:!border-color-47 rounded-xl m-0 focus:text-color-14 focus:bg-white focus:border-color-89 focus:dark:!border-color-47 focus:outline-none w-full px-4 outline-none form-control"
                                    id="detector_description"
                                    placeholder="{{ __('Briefly write down the description of the content you want to check..') }}" minlength="80"
                                    rows="20"
                                    data-min-length="{{ __('Description should be at least 80 characters.') }}"
                                    required oninvalid="this.setCustomValidity('{{ __('This field is required.') }}')"
                                    name="detector_description"></textarea>
                            @endif
                        </div>

                        @if (count($aiProviders) && count($aiProviders) > 1)
                            <p class="mt-6 cursor-pointer AdavanceOption dark:text-white">{{ __('Advance Options') }}</p>
                        @endif

                        @if(count($aiProviders))
                            <div id="ProviderOptionDiv" class="hidden">
                                @foreach ($aiProviders as $provider => $providerOptions)

                                    @if (!empty($providerOptions))
                                        @php
                                            $providerName = str_replace('aidetector_', '', $provider);
                                            $fields = $providerOptions;
                                        @endphp
                                        <div class="gap-6 pt-3 grid grid-cols-2 ProviderOptions {{ $providerName . '_div' }}">
                                            @foreach ($fields as $field)
                                                @if ($field['type'] == 'dropdown')
                                                    <div class="custom-dropdown-arrow font-normal text-14 text-[#141414] dark:text-white  {{ count($field['value']) == 0 ? 'hidden' : '' }}">
                                                        <label>{{ $field['label'] }}</label>
                                                        <select class="select block w-full mt-[3px] text-base leading-6 font-medium text-color-FFR bg-white bg-clip-padding bg-no-repeat dark:bg-[#333332] rounded-xl dark:rounded-2xl focus:text-color-2C focus:bg-white focus:border-color-89 focus:outline-none form-control" name="{{ $providerName . '[' . $field['name'] . ']' }}" id="{{ $field['name'] }}">
                                                            @foreach ($field['value'] as $value)
                                                                <option value="{{ $value }}" {{ isset($field['default_value']) && $field['default_value'] == $value ? 'selected' : '' }}> {{ $value }} </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                @endif
                                            @endforeach
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        @endif

                        <div class="mt-6">
                            <button
                                class="magic-bg w-full rounded-xl text-16 text-white font-semibold py-3 flex justify-center items-center gap-3"
                                id="detector-creation">
                                <span>
                                    {{ __('Scan the detector') }}
                                </span>
                                <svg class="loader animate-spin h-5 w-5 hidden" xmlns="http://www.w3.org/2000/svg"
                                    width="72" height="72" viewBox="0 0 72 72" fill="none">
                                    <mask id="path-1-inside-1_1032_3036" fill="white">
                                        <path
                                            d="M67 36C69.7614 36 72.0357 38.2493 71.6534 40.9841C70.685 47.9121 67.7119 54.4473 63.048 59.7573C57.2779 66.3265 49.3144 70.5713 40.644 71.6992C31.9736 72.8271 23.1891 70.761 15.9304 65.8866C8.67173 61.0123 3.4351 53.6628 1.19814 45.2104C-1.03881 36.7579 -0.123172 27.7803 3.77411 19.9534C7.67139 12.1266 14.2839 5.98568 22.3772 2.67706C30.4704 -0.631565 39.4912 -0.881694 47.7554 1.97337C54.4353 4.28114 60.2519 8.49021 64.5205 14.0322C66.2056 16.2199 65.3417 19.2997 62.9417 20.6656L60.8567 21.8524C58.4567 23.2183 55.4379 22.3325 53.5977 20.2735C50.9338 17.2927 47.5367 15.0161 43.7066 13.6929C38.2888 11.8211 32.3749 11.9851 27.0692 14.1542C21.7634 16.3232 17.4284 20.3491 14.8734 25.4802C12.3184 30.6113 11.7181 36.4969 13.1846 42.0381C14.6511 47.5794 18.0842 52.3975 22.8428 55.5931C27.6014 58.7886 33.3604 60.1431 39.0445 59.4037C44.7286 58.6642 49.9494 55.8814 53.7321 51.5748C56.4062 48.5302 58.2325 44.8712 59.0732 40.9628C59.6539 38.2632 61.8394 36 64.6008 36H67Z" />
                                    </mask>
                                    <path
                                        d="M67 36C69.7614 36 72.0357 38.2493 71.6534 40.9841C70.685 47.9121 67.7119 54.4473 63.048 59.7573C57.2779 66.3265 49.3144 70.5713 40.644 71.6992C31.9736 72.8271 23.1891 70.761 15.9304 65.8866C8.67173 61.0123 3.4351 53.6628 1.19814 45.2104C-1.03881 36.7579 -0.123172 27.7803 3.77411 19.9534C7.67139 12.1266 14.2839 5.98568 22.3772 2.67706C30.4704 -0.631565 39.4912 -0.881694 47.7554 1.97337C54.4353 4.28114 60.2519 8.49021 64.5205 14.0322C66.2056 16.2199 65.3417 19.2997 62.9417 20.6656L60.8567 21.8524C58.4567 23.2183 55.4379 22.3325 53.5977 20.2735C50.9338 17.2927 47.5367 15.0161 43.7066 13.6929C38.2888 11.8211 32.3749 11.9851 27.0692 14.1542C21.7634 16.3232 17.4284 20.3491 14.8734 25.4802C12.3184 30.6113 11.7181 36.4969 13.1846 42.0381C14.6511 47.5794 18.0842 52.3975 22.8428 55.5931C27.6014 58.7886 33.3604 60.1431 39.0445 59.4037C44.7286 58.6642 49.9494 55.8814 53.7321 51.5748C56.4062 48.5302 58.2325 44.8712 59.0732 40.9628C59.6539 38.2632 61.8394 36 64.6008 36H67Z"
                                        stroke="url(#paint0_linear_1032_3036)" stroke-width="24"
                                        mask="url(#path-1-inside-1_1032_3036)" />
                                    <defs>
                                        <linearGradient id="paint0_linear_1032_3036" x1="46.8123" y1="63.1382"
                                            x2="21.8195" y2="6.73779" gradientUnits="userSpaceOnUse">
                                            <stop offset="0" stop-color="#E60C84" />
                                            <stop offset="1" stop-color="#FFCF4B" />
                                        </linearGradient>
                                    </defs>
                                </svg>
                            </button>
                        </div>
                    </div>
                </form>
                <div class="bg-white dark:bg-[#3A3A39] p-6 rounded-xl xl:mt-11">
                    <p class="text-color-14 font-Figtree text-20 font-semibold dark:text-white text-center mb-3">
                        {{ __("AI Content Report") }}</p>
                    <div class="ai-detector-graph flex justify-center items-center">
                        <div class="bar-overflow-background relative">
                                <div class="percentage-wrapper absolute left-1/2 -translate-x-1/2 top-1/2 -translate-y-1/2 text-color-2C dark:text-white font-Figtree text-lg font-bold">
                                    <span class="percentage">0</span>%
                                </div>
                            <div class="bar-background"></div>
                            <div class="flex justify-center items-center gap-2 mt-[120px] flex-wrap">
                                <p class="text-center text-color-89 text-[13px] leading-5 font-medium font-Figtree flex justify-center items-center gap-2">
                                    <svg class="w-3 h-3" width="34" height="33" viewBox="0 0 34 33" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <defs>
                                          <linearGradient id="gradient1" x1="0%" y1="0%" x2="100%" y2="0%">
                                            <stop offset="0%" stop-color="#FFCCCC" />
                                            <stop offset="25%" stop-color="#FF6666" />
                                            <stop offset="50%" stop-color="#FF3333" />
                                            <stop offset="75%" stop-color="#FF0000" />
                                            <stop offset="100%" stop-color="#B80000" />
                                          </linearGradient>
                                        </defs>
                                        <path
                                          d="M33.4414 16.3033C33.4414 25.2604 26.1702 32.5316 17.2131 32.5316C8.25599 32.5316 0.814453 25.2604 0.814453 16.3033C0.814453 7.34618 8.0857 0.0749359 17.0428 0.0749359C25.9999 0.0749359 33.2711 7.34618 33.2711 16.3033H33.4414Z"
                                          fill="url(#gradient1)" />
                                    </svg>
                                    {{ __("AI Generated") }}
                                </p>
                                <p class="text-center text-color-89 text-[13px] leading-5 font-medium font-Figtree flex justify-center items-center gap-2">
                                    <svg class="w-3 h-3" width="34" height="33" viewBox="0 0 34 33" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <defs>
                                          <linearGradient id="gradient2" x1="0%" y1="0%" x2="100%" y2="0%">
                                            <stop offset="0%" stop-color="#ADFF2F" />
                                            <stop offset="25%" stop-color="#7CFC00" />
                                            <stop offset="50%" stop-color="#32CD32" />
                                            <stop offset="75%" stop-color="#008000" />
                                            <stop offset="100%" stop-color="#006400" />
                                          </linearGradient>
                                        </defs>
                                        <path
                                          d="M33.4414 16.3033C33.4414 25.2604 26.1702 32.5316 17.2131 32.5316C8.25599 32.5316 0.814453 25.2604 0.814453 16.3033C0.814453 7.34618 8.0857 0.0749359 17.0428 0.0749359C25.9999 0.0749359 33.2711 7.34618 33.2711 16.3033H33.4414Z"
                                          fill="url(#gradient2)" />
                                    </svg>
                                    {{ __("Human") }}
                                </p>
                                <div class="bar-overflow">
                                    <div class="progress-bars">
                                        <div class="bar-inner">
                                            
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <p
                        class="text-color-14 font-Figtree text-18 font-semibold dark:text-white text-left mb-1.5 p-3 border border-color-89 dark:!border-color-47 mt-6 rounded-xl">
                        {{ __("Report Details") }}
                    </p>

                    <div class="data-append text-color-14 font-Figtree text-14 dark:text-white text-left mb-1.5 p-3 max-h-[295px] overflow-auto sidebar-scrollbar"></div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script> 
        var PROMT_URL = "{{ !empty($promtUrl) ? $promtUrl : ''  }}";  
    </script>
    <script src="{{ asset('Modules/OpenAI/Resources/assets/js/customer/content-detector.min.js') }}"></script>
@endsection

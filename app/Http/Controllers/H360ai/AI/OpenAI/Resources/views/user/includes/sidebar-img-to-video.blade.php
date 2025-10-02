@section('css')
    <link rel="stylesheet" href="{{ asset('public/dist/css/site_custom.min.css') }}">
@endsection
@php
    $videoLeft = null;
    if ($userSubscription && in_array($userSubscription->status, ['Active', 'Cancel'])) {
        $videoLeft = $featureLimit['video']['remain'];
        $videoLimit = $featureLimit['video']['limit'];
    }
@endphp
<form class="image-to-video">
    <div class="px-5 py-[22px] sm:py-8 xl:px-6 xl:pb-[56px] pt-10 font-Figtree">
        <div class="flex items-center justify-start gap-2.5">
            @if( !is_null($videoLeft) &&  auth()->id() == $userId)
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18" fill="none">
                <g clip-path="url(#clip0_4514_3509)">
                    <path
                        d="M13.9714 7.00665C13.8679 6.84578 13.6901 6.75015 13.5 6.75015H9.56255V0.562738C9.56255 0.297241 9.37693 0.0677446 9.11706 0.0126204C8.85269 -0.0436289 8.59394 0.0924942 8.48594 0.334366L3.986 10.4592C3.90838 10.6325 3.92525 10.835 4.02875 10.9936C4.13225 11.1533 4.31 11.2501 4.50012 11.2501H8.43757V17.4375C8.43757 17.703 8.62319 17.9325 8.88306 17.9876C8.92244 17.9955 8.96181 18 9.00006 18C9.21831 18 9.42193 17.8729 9.51418 17.6659L14.0141 7.54102C14.0906 7.36664 14.076 7.1664 13.9714 7.00665Z"
                        fill="url(#paint0_linear_4514_3509)" />
                </g>
                <defs>
                    <linearGradient id="paint0_linear_4514_3509" x1="10.5204" y1="15.7845" x2="2.32033"
                        y2="5.3758" gradientUnits="userSpaceOnUse">
                        <stop offset="0" stop-color="#E60C84" />
                        <stop offset="1" stop-color="#FFCF4B" />
                    </linearGradient>
                    <clipPath id="clip0_4514_3509">
                        <rect width="18" height="18" fill="white" />
                    </clipPath>
                </defs>
            </svg>
           
            {!! __('Credits Balance: :x videos left', ['x' => "<span class='total-page-left font-semibold dark:text-[#FCCA19]'>" . ($videoLimit == -1 ? __('Unlimited') : ($videoLeft < 0 ? 0 : $videoLeft)) . "</span>"]) !!}      
            @endif
        </div>


        <div class="bg-white dark:bg-[#474746] py-7 px-6 rounded-xl mt-5">
            <p class="text-color-14 text-24 font-semibold font-RedHat dark:text-white">
                {{ __('Static images to live videos') }}</p>
            <p class="text-color-89 text-13 font-medium font-Figtree mt-2">
                {{ __('Bring your static images to life and create visually compelling videos.') }}
            </p>
            <div class="custom-dropdown-arrow font-normal text-14 text-[#141414] dark:text-white {{ count($aiProviders) <= 1 ? 'hidden' : '' }}">
                <label>{{ __('Provider') }}</label>
                <select class="select block w-full mt-[3px] text-base leading-6 font-medium text-color-FFR bg-white bg-clip-padding bg-no-repeat dark:bg-[#333332] rounded-xl dark:rounded-2xl focus:text-color-2C focus:bg-white focus:border-color-89 focus:outline-none form-control" name="provider" id="provider">
                    @foreach ($aiProviders as $provider => $value)
                        @php
                            $providerName = str_replace('videomaker_', '', $provider);
                        @endphp
                            <option value="{{ $providerName }}"> {{ ucwords($providerName) }} </option>
                    @endforeach
                </select>
            </div>
            <div class="w-full gender-container">
                <p class="text-color-14 dark:text-white font-Figtree text-14 font-normal mt-6">{{ __('Upload Image') }}
                </p>
                <div class="drop-zone"
                    id="file-upload-container">
                    <div
                        class="border border-dashed border-color-89 rounded-xl bg-color-F3 dark:bg-color-33 dark:border-color-47 mt-[7px] cursor-pointer text-[13px] leading-[18px] font-normal font-Figtree text-colo-14 wrap-anywhere text-center py-[37px] px-4 file-info-container">
                        <div
                            class="file-info-text justify-center items-center flex gap-2 text-color-14 dark:text-color-89">
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M7.99935 2.6665C8.36754 2.6665 8.66602 2.96498 8.66602 3.33317V7.33317H12.666C13.0342 7.33317 13.3327 7.63165 13.3327 7.99984C13.3327 8.36803 13.0342 8.6665 12.666 8.6665H8.66602V12.6665C8.66602 13.0347 8.36754 13.3332 7.99935 13.3332C7.63116 13.3332 7.33268 13.0347 7.33268 12.6665V8.6665H3.33268C2.96449 8.6665 2.66602 8.36803 2.66602 7.99984C2.66602 7.63165 2.96449 7.33317 3.33268 7.33317H7.33268V3.33317C7.33268 2.96498 7.63116 2.6665 7.99935 2.6665Z"
                                    fill="currentColor" />
                            </svg>
                            <p>{{ __('Click or drag an image here') }}</p>
                        </div>
                    </div>
                    <div>
                        <input type="file" id="file_input" name="image" required class="form-control drop-zone__input hidden" value="">
                    </div>
                </div>
                <div id="imgFile-container" class="flex justify-between items-center gap-[11px] gap-y-1 flex-wrap">
                </div>
                <div id="error-message"
                    class="error-message hidden font-Figtree text-[11px] text-[#FF4500] font-medium">
                    {{ __('invalid files') }}</div>
                <p class="text-color-89 text-[13px] leading-5 font-medium font-Figtree mt-1.5">
                    {{ __('Dimensions: 1024x576, 576x1024 or 768x768') }}</p>
                <p class="text-color-89 text-[13px] leading-5 font-medium font-Figtree mt-0.5">
                    {{ __('Formats: jpeg, png') }}</p>
            </div>
            <div class="image-input-loader mx-auto mt-12 hidden">
                <svg class="animate-spin h-7 w-7 m-auto" width="80" height="80" viewBox="0 0 80 80"
                    fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle class="loading-circle-large" cx="40" cy="40" r="36" stroke="#E60C84"
                        stroke-width="8" />
                </svg>
                <p class="text-center text-color-14 dark:text-white text-12 mt-2 font-normal font-Figtree ">
                    {{ __('Processing..') }}</p>
            </div>
            @if (count($aiProviders))
            <p class="mt-6 cursor-pointer AdavanceOption dark:text-white">{{ __('Advance Options') }}</p>
            @endif
    
            @if(count($aiProviders))
                <div id="ProviderOptionDiv" class="hidden">
    
                    @foreach ($aiProviders as $provider => $providerOptions)
    
                    @if (!empty($providerOptions))
                    @php
                                $providerName = str_replace('videomaker_', '', $provider);
                                $fields = $providerOptions;
                                @endphp
                            <div class="gap-6 pt-3 grid grid-cols-2 ProviderOptions {{ $providerName . '_div' }}">
                                @foreach ($fields as $field)
                                    @if ($field['type'] == 'number' && !is_null($field['value']))
                                        <div>
                                            <div class="font-normal text-14 text-color-2C dark:text-white">
                                                <div class="flex gap-2 justify-start items-center">
                                                    <label class="">{{ __($field['label']) }}</label>
                                                    <a class="tooltip-info relative"
                                                        title ="{{ __($field['tooltip']) }}"
                                                        href="javascript: void(0)">
                                                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none"
                                                            xmlns="http://www.w3.org/2000/svg">
                                                            <g clip-path="url(#clip0_18565_11277)">
                                                                <path fill-rule="evenodd" clip-rule="evenodd"
                                                                    d="M7.99935 2.00033C4.68564 2.00033 1.99935 4.68662 1.99935 8.00033C1.99935 11.314 4.68564 14.0003 7.99935 14.0003C11.3131 14.0003 13.9993 11.314 13.9993 8.00033C13.9993 4.68662 11.3131 2.00033 7.99935 2.00033ZM0.666016 8.00033C0.666016 3.95024 3.94926 0.666992 7.99935 0.666992C12.0494 0.666992 15.3327 3.95024 15.3327 8.00033C15.3327 12.0504 12.0494 15.3337 7.99935 15.3337C3.94926 15.3337 0.666016 12.0504 0.666016 8.00033ZM7.33268 5.33366C7.33268 4.96547 7.63116 4.66699 7.99935 4.66699H8.00602C8.37421 4.66699 8.67268 4.96547 8.67268 5.33366C8.67268 5.70185 8.37421 6.00033 8.00602 6.00033H7.99935C7.63116 6.00033 7.33268 5.70185 7.33268 5.33366ZM7.99935 7.33366C8.36754 7.33366 8.66602 7.63214 8.66602 8.00033V10.667C8.66602 11.0352 8.36754 11.3337 7.99935 11.3337C7.63116 11.3337 7.33268 11.0352 7.33268 10.667V8.00033C7.33268 7.63214 7.63116 7.33366 7.99935 7.33366Z"
                                                                    fill="currentColor" />
                                                            </g>
                                                            <defs>
                                                                <clipPath id="clip0_18565_11277">
                                                                    <rect width="16" height="16" fill="white" />
                                                                </clipPath>
                                                            </defs>
                                                        </svg>
                                                    </a>
                                                </div>
                                                <input
                                                    class="w-full px-4 h-12 py-1.5 text-base mt-[3px] leading-6 font-light text-color-14 dark:!text-white bg-white dark:bg-[#333332] bg-clip-padding bg-no-repeat border border-solid border-color-89 dark:!border-color-47 rounded-xl focus:text-color-14 focus:dark:!text-white focus:bg-white focus:border-color-89 focus:outline-none form-control"
                                                    @if(isset($field['required']) &&  $field['required']) 
                                                        required oninvalid="this.setCustomValidity('{{ __('This field is required.') }}')"
                                                    @endif
                                                    value="{{ isset($field['value']) ? $field['value'] : ''}}"
                                                    min="{{ $field['min'] }}"
                                                    max="{{ $field['max'] }}"
                                                    type="number" name="{{ $field['name'] }}">
                                            </div>
                                        </div>
                                    @endif
                                    @if ($field['type'] == 'slider' && !is_null($field['value']))
                                        <div class="col-span-2">
                                           <div class="flex gap-2 justify-between items-center font-normal text-14 text-color-2C dark:text-white">
                                            <div class="flex gap-2 justify-start items-center ">
                                                <label class="">{{ __('Image Strength') }}</label>
                                                <a class="tooltip-info relative"
                                                    title ="A specific value from 0 to 10 to express how strongly the video sticks to the original image."
                                                    href="javascript: void(0)">
                                                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <g clip-path="url(#clip0_18565_11277)">
                                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                                d="M7.99935 2.00033C4.68564 2.00033 1.99935 4.68662 1.99935 8.00033C1.99935 11.314 4.68564 14.0003 7.99935 14.0003C11.3131 14.0003 13.9993 11.314 13.9993 8.00033C13.9993 4.68662 11.3131 2.00033 7.99935 2.00033ZM0.666016 8.00033C0.666016 3.95024 3.94926 0.666992 7.99935 0.666992C12.0494 0.666992 15.3327 3.95024 15.3327 8.00033C15.3327 12.0504 12.0494 15.3337 7.99935 15.3337C3.94926 15.3337 0.666016 12.0504 0.666016 8.00033ZM7.33268 5.33366C7.33268 4.96547 7.63116 4.66699 7.99935 4.66699H8.00602C8.37421 4.66699 8.67268 4.96547 8.67268 5.33366C8.67268 5.70185 8.37421 6.00033 8.00602 6.00033H7.99935C7.63116 6.00033 7.33268 5.70185 7.33268 5.33366ZM7.99935 7.33366C8.36754 7.33366 8.66602 7.63214 8.66602 8.00033V10.667C8.66602 11.0352 8.36754 11.3337 7.99935 11.3337C7.63116 11.3337 7.33268 11.0352 7.33268 10.667V8.00033C7.33268 7.63214 7.63116 7.33366 7.99935 7.33366Z"
                                                                fill="currentColor" />
                                                        </g>
                                                        <defs>
                                                            <clipPath id="clip0_18565_11277">
                                                                <rect width="16" height="16" fill="white" />
                                                            </clipPath>
                                                        </defs>
                                                    </svg>
                                                </a>
                                            </div>
                                            <span class="img-strength">1.8</span>
                                           </div>

                                        <input dir="ltr" class="range progress-bar w-full progress-input inputProgress" id="progress-input"
                                            min="{{ $field['min'] }}" max="{{ $field['max'] }}" type="range" name="{{ $field['name'] }}" 
                                            value="{{ $field['value'] }}" step="0.1" />
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        @endif
                    @endforeach
                </div>
            @endif
            <div class="mt-6 xl:my-6">
                <button
                    class="magic-bg w-full rounded-xl text-16 text-white font-semibold py-3 flex justify-center items-center gap-3 relative"
                    id="video-creation">
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M10.5002 15C10.1208 16.1837 9.18382 17.1207 8 17.5C9.18382 17.8793 10.1208 18.8163 10.5002 20C10.8795 18.8163 11.8162 17.8793 13 17.5C11.8162 17.1207 10.8795 16.1841 10.5002 15Z"
                            fill="white" />
                        <path
                            d="M13.3909 2C12.8792 4.84754 10.6284 7.09858 7.78125 7.61052C10.6284 8.12224 12.8792 10.3735 13.3909 13.2208C13.9026 10.3733 16.1534 8.12224 19.0005 7.61052C16.1534 7.09858 13.9026 4.84754 13.3909 2Z"
                            fill="white" />
                        <path
                            d="M3.5 9C3.08663 10.7255 1.72561 12.0867 0 12.5C1.72561 12.9133 3.08689 14.2745 3.5 16C3.91337 14.2745 5.27439 12.9133 7 12.5C5.27439 12.0867 3.91337 10.7255 3.5 9Z"
                            fill="white" />
                    </svg> <span>

                        {{ __('Show the Magic') }}
                    </span>
                    <svg class="loader-video animate-spin h-5 w-5 hidden" xmlns="http://www.w3.org/2000/svg"
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
    </div>
</form>

<div class="SingleForm Active">

    <p class="text-color-14 text-18 font-semibold font-Figtree dark:text-white wrap-anywhere py-6">{{ __('Titles & Keywords') }}</p>

    <form action="#" class="TitleForm">
        @csrf 
        <input type="hidden" class="long_article_id" name="long_article_id" value="">

        <!-- Topic -->
        <label class="text-color-14 dark:text-white font-Figtree text-14 font-normal mt-6">{{ __('Topic') }}</label>
        <input class="questions dynamic-input w-full px-4 h-12 py-1.5 text-base mt-[3px] leading-6 font-normal text-color-14 bg-white dark:bg-[#333332] bg-clip-padding bg-no-repeat border border-solid border-color-89 dark:!border-color-47 rounded-xl dark:!text-white focus:bg-white focus:border-color-89 focus:outline-none form-control"
        type="text" id="TitleTopic" name="topic" value="">

        <!-- Number of Titles -->
        <div class="mt-6">
            <label class="text-color-14 dark:text-white font-Figtree text-14 font-normal mt-6">{{ __('Number of Titles') }}</label>
            <input class="questions dynamic-input w-full px-4 h-12 py-1.5 text-base mt-[3px] leading-6 font-normal text-color-14 bg-white dark:bg-[#333332] bg-clip-padding bg-no-repeat border border-solid border-color-89 dark:!border-color-47 rounded-xl dark:!text-white focus:bg-white focus:border-color-89 focus:outline-none form-control" type="number" name="number_of_title" value="{{ isset(session('longarticle')['title_number']) ? session('longarticle')['title_number'] : 1 }}" min="1">
        </div>

        <!-- Keywords --> 
        <div class="my-6">
            <label class="text-color-14 dark:text-white font-Figtree text-14 font-normal mt-6">{{ __('Keywords') }}</label>
            <input class="questions dynamic-input w-full px-4 h-12 py-1.5 text-base mt-[3px] leading-6 font-normal text-color-14 bg-white dark:bg-[#333332] bg-clip-padding bg-no-repeat border border-solid border-color-89 dark:!border-color-47 rounded-xl dark:!text-white focus:bg-white focus:border-color-89 focus:outline-none form-control"
            type="text" name="keywords" id="TitleKeywords">
            <p class="text-color-89 text-[13px] leading-5 font-medium font-Figtree mt-1"></span>{{ __('Insert keywords as comma separated value') }}</p>
        </div>

        <!-- Provider -->
        <div class="custom-dropdown-arrow font-normal text-14 text-[#141414] dark:text-white {{ count($aiProviders) <= 1 ? 'hidden' : '' }}">
            <label>{{ __('Provider') }}</label>
            <select class="select block w-full mt-[3px] text-base leading-6 font-medium text-color-FFR bg-white bg-clip-padding bg-no-repeat dark:bg-[#333332] rounded-xl dark:rounded-2xl focus:text-color-2C focus:bg-white focus:border-color-89 focus:outline-none form-control" name="provider" id="provider">
                @foreach ($aiProviders as $provider => $value)
                    @php
                        $providerName = str_replace('longarticle_', '', $provider);
                    @endphp
                        <option value="{{ $providerName }}"  {{ isset(session('longarticle')['provider']) && session('longarticle')['provider'] == $providerName ? 'selected' :  '' }}> {{ ucwords($providerName) }} </option>
                @endforeach
            </select>
        </div>

        @if (count($aiProviders))
        <p class="mt-6 cursor-pointer AdavanceOption dark:text-white">{{ __('Advance Options') }}</p>
        @endif

        @if(count($aiProviders))
            <div id="ProviderOptionDiv" class="hidden">

                @foreach ($aiProviders as $provider => $providerOptions)

                    @if (!empty($providerOptions))
                        @php
                            $providerName = str_replace('longarticle_', '', $provider);
                            $fields = $providerOptions;
                        @endphp
                        <div class="gap-6 pt-3 grid grid-cols-2 ProviderOptions {{ $providerName . '_div' }}">
                            @foreach ($fields as $field)
                                @if ($field['type'] == 'dropdown')
                                    <div class="custom-dropdown-arrow font-normal text-14 text-[#141414] dark:text-white  {{ count($field['value']) <= 1 ? 'hidden' : '' }}">
                                        <label>{{ $field['label'] }}</label>
                                        <select class="select block w-full mt-[3px] text-base leading-6 font-medium text-color-FFR bg-white bg-clip-padding bg-no-repeat dark:bg-[#333332] rounded-xl dark:rounded-2xl focus:text-color-2C focus:bg-white focus:border-color-89 focus:outline-none form-control" name="{{ $providerName . '[' . $field['name'] . ']' }}">
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
            <button class="bg-[#333332] dark:bg-[#141414] w-full rounded-xl text-16 text-white font-semibold py-3 flex justify-center items-center gap-3" id="TitleGenerateButton">
                <span>
                    {{ __('Generate Titles') }}
                </span>
                <svg class="loader animate-spin h-5 w-5 hidden" xmlns="http://www.w3.org/2000/svg" width="72" height="72" viewBox="0 0 72 72" fill="none">
                    <mask id="path-1-inside-1_1032_3036" fill="white">
                        <path d="M67 36C69.7614 36 72.0357 38.2493 71.6534 40.9841C70.685 47.9121 67.7119 54.4473 63.048 59.7573C57.2779 66.3265 49.3144 70.5713 40.644 71.6992C31.9736 72.8271 23.1891 70.761 15.9304 65.8866C8.67173 61.0123 3.4351 53.6628 1.19814 45.2104C-1.03881 36.7579 -0.123172 27.7803 3.77411 19.9534C7.67139 12.1266 14.2839 5.98568 22.3772 2.67706C30.4704 -0.631565 39.4912 -0.881694 47.7554 1.97337C54.4353 4.28114 60.2519 8.49021 64.5205 14.0322C66.2056 16.2199 65.3417 19.2997 62.9417 20.6656L60.8567 21.8524C58.4567 23.2183 55.4379 22.3325 53.5977 20.2735C50.9338 17.2927 47.5367 15.0161 43.7066 13.6929C38.2888 11.8211 32.3749 11.9851 27.0692 14.1542C21.7634 16.3232 17.4284 20.3491 14.8734 25.4802C12.3184 30.6113 11.7181 36.4969 13.1846 42.0381C14.6511 47.5794 18.0842 52.3975 22.8428 55.5931C27.6014 58.7886 33.3604 60.1431 39.0445 59.4037C44.7286 58.6642 49.9494 55.8814 53.7321 51.5748C56.4062 48.5302 58.2325 44.8712 59.0732 40.9628C59.6539 38.2632 61.8394 36 64.6008 36H67Z"></path>
                    </mask>
                    <path d="M67 36C69.7614 36 72.0357 38.2493 71.6534 40.9841C70.685 47.9121 67.7119 54.4473 63.048 59.7573C57.2779 66.3265 49.3144 70.5713 40.644 71.6992C31.9736 72.8271 23.1891 70.761 15.9304 65.8866C8.67173 61.0123 3.4351 53.6628 1.19814 45.2104C-1.03881 36.7579 -0.123172 27.7803 3.77411 19.9534C7.67139 12.1266 14.2839 5.98568 22.3772 2.67706C30.4704 -0.631565 39.4912 -0.881694 47.7554 1.97337C54.4353 4.28114 60.2519 8.49021 64.5205 14.0322C66.2056 16.2199 65.3417 19.2997 62.9417 20.6656L60.8567 21.8524C58.4567 23.2183 55.4379 22.3325 53.5977 20.2735C50.9338 17.2927 47.5367 15.0161 43.7066 13.6929C38.2888 11.8211 32.3749 11.9851 27.0692 14.1542C21.7634 16.3232 17.4284 20.3491 14.8734 25.4802C12.3184 30.6113 11.7181 36.4969 13.1846 42.0381C14.6511 47.5794 18.0842 52.3975 22.8428 55.5931C27.6014 58.7886 33.3604 60.1431 39.0445 59.4037C44.7286 58.6642 49.9494 55.8814 53.7321 51.5748C56.4062 48.5302 58.2325 44.8712 59.0732 40.9628C59.6539 38.2632 61.8394 36 64.6008 36H67Z" stroke="url(#paint0_linear_1032_3036)" stroke-width="24" mask="url(#path-1-inside-1_1032_3036)"></path>
                    <defs>
                        <linearGradient id="paint0_linear_1032_3036" x1="46.8123" y1="63.1382" x2="21.8195" y2="6.73779" gradientUnits="userSpaceOnUse">
                            <stop offset="0" stop-color="#E60C84"></stop>
                            <stop offset="1" stop-color="#FFCF4B"></stop>
                        </linearGradient>
                    </defs>
                </svg>
            </button>
        </div>
    </form>
    @if (isset($longArticle))
    <div class="flex justify-center">
        <a href="{{ route('user.long_article.index') }}" class="flex justify-center gap-1 items-center mt-5">
            <span class="text-color-14 dark:text-white">
                <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M7.81694 9.83263C7.57286 10.0558 7.17714 10.0558 6.93306 9.83263L3.18306 6.40406C2.93898 6.1809 2.93898 5.8191 3.18306 5.59594L6.93306 2.16737C7.17714 1.94421 7.57286 1.94421 7.81694 2.16737C8.06102 2.39052 8.06102 2.75233 7.81694 2.97549L4.50888 6L7.81694 9.02451C8.06102 9.24767 8.06102 9.60948 7.81694 9.83263Z" fill="currentColor"/>
                </svg>
            </span>
            <span class="text-15 text-color-14 dark:text-white font-Figtree font-medium">
                {{ __('Back') }}
            </span>
        </a>
    </div>
    @endif
</div>
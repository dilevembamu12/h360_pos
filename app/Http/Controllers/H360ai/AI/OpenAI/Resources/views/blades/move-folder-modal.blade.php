<div id="move-folder-mod" class="relative bg-white dark:bg-color-3A py-5 w-[345px] xs:w-[388px] sm:w-[548px] rounded-xl white-popup mfp-with-anim mfp-hide">
    <form method="post" name="updateFolderItemForm" id="updateFolderItemForm">
        <div>
            <p class="px-5 font-Figtree text-color-14 dark:text-white font-medium text-20 rounded-xl move-content-title"></p>
            <div class="move-data"></div>
        </div>

        <div class="content-data"> </div>

        <div class="border-t border-t-color-DF dark:border-t-color-47">
            <div class="flex justify-between items-center mt-5 px-5">
                <div class="flex justify-center items-center gap-3">
                    <button type="submit" class="move-data-content font-Figtree text-white font-semibold text-15 py-2.5 px-[48.5px] bg-color-14 rounded-xl">
                        {{ __('Move') }}
                    </button>
                    <a href="javascript:void(0)" class="close-popup font-Figtree text-color-14 dark:text-white font-medium text-14 py-2.5 px-[31px] border border-color-89 dark:border-color-47 bg-color-F6 dark:bg-color-47 rounded-xl">
                        {{ __('Cancel') }}
                    </a>
                </div>
                <div>
                    <a href="javascript:void(0)">
                        <svg  class="generate-folder-modal modal-mest create-folder text-color-47 dark:text-white" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M13 19C13 19.34 13.04 19.67 13.09 20H4C3.46957 20 2.96086 19.7893 2.58579 19.4142C2.21071 19.0391 2 18.5304 2 18V6C2 4.89 2.89 4 4 4H10L12 6H20C20.5304 6 21.0391 6.21071 21.4142 6.58579C21.7893 6.96086 22 7.46957 22 8V13.81C21.39 13.46 20.72 13.22 20 13.09V8H4V18H13.09C13.04 18.33 13 18.66 13 19ZM20 18V15H18V18H15V20H18V23H20V20H23V18H20Z" fill="currentColor"/>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </form>
    <div class="loader-template mx-auto items-center dark:bg-color-3A absolute w-full h-full top-0 flex flex-col justify-center !bg-opacity-50 bg-white rounded-xl content-folder-loader">
        <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" width="72"
        height="72" viewBox="0 0 72 72" fill="none">
        <mask id="path-1-inside-1_1032_3036" fill="white">
            <path
                d="M67 36C69.7614 36 72.0357 38.2493 71.6534 40.9841C70.685 47.9121 67.7119 54.4473 63.048 59.7573C57.2779 66.3265 49.3144 70.5713 40.644 71.6992C31.9736 72.8271 23.1891 70.761 15.9304 65.8866C8.67173 61.0123 3.4351 53.6628 1.19814 45.2104C-1.03881 36.7579 -0.123172 27.7803 3.77411 19.9534C7.67139 12.1266 14.2839 5.98568 22.3772 2.67706C30.4704 -0.631565 39.4912 -0.881694 47.7554 1.97337C54.4353 4.28114 60.2519 8.49021 64.5205 14.0322C66.2056 16.2199 65.3417 19.2997 62.9417 20.6656L60.8567 21.8524C58.4567 23.2183 55.4379 22.3325 53.5977 20.2735C50.9338 17.2927 47.5367 15.0161 43.7066 13.6929C38.2888 11.8211 32.3749 11.9851 27.0692 14.1542C21.7634 16.3232 17.4284 20.3491 14.8734 25.4802C12.3184 30.6113 11.7181 36.4969 13.1846 42.0381C14.6511 47.5794 18.0842 52.3975 22.8428 55.5931C27.6014 58.7886 33.3604 60.1431 39.0445 59.4037C44.7286 58.6642 49.9494 55.8814 53.7321 51.5748C56.4062 48.5302 58.2325 44.8712 59.0732 40.9628C59.6539 38.2632 61.8394 36 64.6008 36H67Z" />
        </mask>
        <path
            d="M67 36C69.7614 36 72.0357 38.2493 71.6534 40.9841C70.685 47.9121 67.7119 54.4473 63.048 59.7573C57.2779 66.3265 49.3144 70.5713 40.644 71.6992C31.9736 72.8271 23.1891 70.761 15.9304 65.8866C8.67173 61.0123 3.4351 53.6628 1.19814 45.2104C-1.03881 36.7579 -0.123172 27.7803 3.77411 19.9534C7.67139 12.1266 14.2839 5.98568 22.3772 2.67706C30.4704 -0.631565 39.4912 -0.881694 47.7554 1.97337C54.4353 4.28114 60.2519 8.49021 64.5205 14.0322C66.2056 16.2199 65.3417 19.2997 62.9417 20.6656L60.8567 21.8524C58.4567 23.2183 55.4379 22.3325 53.5977 20.2735C50.9338 17.2927 47.5367 15.0161 43.7066 13.6929C38.2888 11.8211 32.3749 11.9851 27.0692 14.1542C21.7634 16.3232 17.4284 20.3491 14.8734 25.4802C12.3184 30.6113 11.7181 36.4969 13.1846 42.0381C14.6511 47.5794 18.0842 52.3975 22.8428 55.5931C27.6014 58.7886 33.3604 60.1431 39.0445 59.4037C44.7286 58.6642 49.9494 55.8814 53.7321 51.5748C56.4062 48.5302 58.2325 44.8712 59.0732 40.9628C59.6539 38.2632 61.8394 36 64.6008 36H67Z"
            stroke="url(#paint0_linear_1032_3036)" stroke-width="24"
            mask="url(#path-1-inside-1_1032_3036)" />
        <defs>
            <linearGradient id="paint0_linear_1032_3036" x1="46.8123" y1="63.1382" x2="21.8195"
                y2="6.73779" gradientUnits="userSpaceOnUse">
                <stop offset="0" stop-color="#E60C84" />
                <stop offset="1" stop-color="#FFCF4B" />
            </linearGradient>
        </defs>
        </svg>
    </div>

    <form class="hidden relative px-5" name="generateFolderOnModal" id="generateFolderOnModal">
        {!! csrf_field() !!}
        <div class="folder-show">
            <p class="font-Figtree text-color-14 dark:text-white text-20 font-semibold -mt-1">
                {{ __('Create Folder') }}
            </p>
            <p class="font-Figtree text-color-14 dark:text-white text-14 font-normal mt-3 pt-6 border-t border-t-color-DF dark:border-t-color-47">
                {{ __('Folder Name') }}
            </p>
            <div>
                <input class="form-control w-full px-4 h-12 py-1.5 text-base mt-1.5 font-normal font-Figtree text-color-14 dark:!text-white bg-white dark:bg-[#333332] border border-color-89 dark:!border-color-47 rounded-xl focus:text-color-14 focus:dark:!text-white focus:bg-white focus:border-color-89 focus:outline-none"
                    type="text" name="name"id="folder_name" required oninvalid="this.setCustomValidity('{{ __('This field is required.') }}')">
            </div>
            <input type="text" class="hidden" name="slug" id="slug" />
            <input type="int" class="hidden" name="userId" id="user_id" value ="{{ auth()->user()->id }}" />
            <div class="parent-data"></div>
            <div class="flex items-center mt-6 gap-[16px]">
                
                <button type="submit" class="flex items-center gap-2.5 font-Figtree bg-color-14 text-white font-semibold text-15 py-2.5 px-[43px] rounded-xl">
                    {{ __('Create') }}
                    <span class="loader-template folder-loader hidden">
                        <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" width="72"
                        height="72" viewBox="0 0 72 72" fill="none">
                        <mask id="path-1-inside-1_1032_303611" fill="white">
                            <path
                                d="M67 36C69.7614 36 72.0357 38.2493 71.6534 40.9841C70.685 47.9121 67.7119 54.4473 63.048 59.7573C57.2779 66.3265 49.3144 70.5713 40.644 71.6992C31.9736 72.8271 23.1891 70.761 15.9304 65.8866C8.67173 61.0123 3.4351 53.6628 1.19814 45.2104C-1.03881 36.7579 -0.123172 27.7803 3.77411 19.9534C7.67139 12.1266 14.2839 5.98568 22.3772 2.67706C30.4704 -0.631565 39.4912 -0.881694 47.7554 1.97337C54.4353 4.28114 60.2519 8.49021 64.5205 14.0322C66.2056 16.2199 65.3417 19.2997 62.9417 20.6656L60.8567 21.8524C58.4567 23.2183 55.4379 22.3325 53.5977 20.2735C50.9338 17.2927 47.5367 15.0161 43.7066 13.6929C38.2888 11.8211 32.3749 11.9851 27.0692 14.1542C21.7634 16.3232 17.4284 20.3491 14.8734 25.4802C12.3184 30.6113 11.7181 36.4969 13.1846 42.0381C14.6511 47.5794 18.0842 52.3975 22.8428 55.5931C27.6014 58.7886 33.3604 60.1431 39.0445 59.4037C44.7286 58.6642 49.9494 55.8814 53.7321 51.5748C56.4062 48.5302 58.2325 44.8712 59.0732 40.9628C59.6539 38.2632 61.8394 36 64.6008 36H67Z" />
                        </mask>
                        <path
                            d="M67 36C69.7614 36 72.0357 38.2493 71.6534 40.9841C70.685 47.9121 67.7119 54.4473 63.048 59.7573C57.2779 66.3265 49.3144 70.5713 40.644 71.6992C31.9736 72.8271 23.1891 70.761 15.9304 65.8866C8.67173 61.0123 3.4351 53.6628 1.19814 45.2104C-1.03881 36.7579 -0.123172 27.7803 3.77411 19.9534C7.67139 12.1266 14.2839 5.98568 22.3772 2.67706C30.4704 -0.631565 39.4912 -0.881694 47.7554 1.97337C54.4353 4.28114 60.2519 8.49021 64.5205 14.0322C66.2056 16.2199 65.3417 19.2997 62.9417 20.6656L60.8567 21.8524C58.4567 23.2183 55.4379 22.3325 53.5977 20.2735C50.9338 17.2927 47.5367 15.0161 43.7066 13.6929C38.2888 11.8211 32.3749 11.9851 27.0692 14.1542C21.7634 16.3232 17.4284 20.3491 14.8734 25.4802C12.3184 30.6113 11.7181 36.4969 13.1846 42.0381C14.6511 47.5794 18.0842 52.3975 22.8428 55.5931C27.6014 58.7886 33.3604 60.1431 39.0445 59.4037C44.7286 58.6642 49.9494 55.8814 53.7321 51.5748C56.4062 48.5302 58.2325 44.8712 59.0732 40.9628C59.6539 38.2632 61.8394 36 64.6008 36H67Z"
                            stroke="url(#paint0_linear_1032_303611)" stroke-width="24"
                            mask="url(#path-1-inside-1_1032_303611)" />
                        <defs>
                            <linearGradient id="paint0_linear_1032_303611" x1="46.8123" y1="63.1382" x2="21.8195"
                                y2="6.73779" gradientUnits="userSpaceOnUse">
                                <stop offset="0" stop-color="#E60C84" />
                                <stop offset="1" stop-color="#FFCF4B" />
                            </linearGradient>
                        </defs>
                        </svg>
                    </span>
                </button>
                <a href="javascript:void(0)" class="close-popup-mest font-Figtree text-color-14 dark:text-white font-semibold text-15 py-2.5 px-[42px] border border-color-89 dark:border-color-47 bg-color-F6 dark:bg-color-47 rounded-xl">
                    {{ __('Cancel') }}
                </a>
            </div>
        </div>
    </form>
</div>


@push('scripts')
    <script src="{{ asset('Modules/OpenAI/Resources/assets/js/customer/folders.min.js') }}"></script>
@endpush

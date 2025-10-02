@extends('layouts.user_master')
@section('page_title', __('Edit Article'))
@section('content')
{{-- main-content --}}
<div class="w-[68.9%] 5xl:w-[85.9%] dark:bg-[#292929] flex flex-col flex-1 border-l dark:border-[#474746] border-color-DF h-screen blog-parent">
    <div class="justify-between subscription-main flex xl:flex-row flex-col xl:h-full subscription-main md:overflow-auto sidebar-scrollbar md:h-screen overflow-x-hidden">
        <div class="w-full pt-[74px] dark:bg-[#292929] xl:overflow-auto sidebar-scrollbar h-screen">
            <div class="relative flex justify-between items-center mx-3 sm:mx-6 mb-4 gap-5">
                <div>
                    <div class="flex gap-1.5 leading-[22px] items-center font-medium text-color-2C dark:text-white text-[15px] edit-url">
                        <a href="{{ route('user.long_article.index') }}">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="12" viewBox="0 0 16 12"
                                fill="none">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M15.875 6C15.875 5.68934 15.6232 5.4375 15.3125 5.4375L2.0455 5.4375L5.58525 1.89775C5.80492 1.67808 5.80492 1.32192 5.58525 1.10225C5.36558 0.882582 5.00942 0.882582 4.78975 1.10225L0.289752 5.60225C0.0700827 5.82192 0.0700827 6.17808 0.289752 6.39775L4.78975 10.8977C5.00942 11.1174 5.36558 11.1174 5.58525 10.8977C5.80492 10.6781 5.80492 10.3219 5.58525 10.1023L2.0455 6.5625L15.3125 6.5625C15.6232 6.5625 15.875 6.31066 15.875 6Z" fill="currentColor" />
                            </svg>
                        </a>
                        <p class="content-name pl-3 line-clamp-single break-all">{{ $longArticle?->title }}</p>
                    </div>
                </div>
                <div class="flex justify-end lg:gap-8 gap-6 items-center">
                    <p class="hidden xl:block text-center word-counter text-sm font-medium text-color-14 dark:text-white font-Figtree whitespace-nowrap"></p>
                    <button class="flex justify-center items-center gap-1.5 text-color-14 dark:text-white font-Figtree text-15 font-normal save-article">
                        <svg class="save-icon" width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M15.75 5.25V14.25C15.75 14.6625 15.603 15.0158 15.309 15.3098C15.015 15.6038 14.662 15.7505 14.25 15.75H3.75C3.3375 15.75 2.98425 15.603 2.69025 15.309C2.39625 15.015 2.2495 14.662 2.25 14.25V3.75C2.25 3.3375 2.397 2.98425 2.691 2.69025C2.985 2.39625 3.338 2.2495 3.75 2.25H12.75L15.75 5.25ZM14.25 5.8875L12.1125 3.75H3.75V14.25H14.25V5.8875ZM9 13.5C9.625 13.5 10.1563 13.2813 10.5938 12.8438C11.0313 12.4063 11.25 11.875 11.25 11.25C11.25 10.625 11.0313 10.0938 10.5938 9.65625C10.1563 9.21875 9.625 9 9 9C8.375 9 7.84375 9.21875 7.40625 9.65625C6.96875 10.0938 6.75 10.625 6.75 11.25C6.75 11.875 6.96875 12.4063 7.40625 12.8438C7.84375 13.2813 8.375 13.5 9 13.5ZM4.5 7.5H11.25V4.5H4.5V7.5ZM3.75 5.8875V14.25V3.75V5.8875Z" fill="currentColor"/>
                        </svg>
                        <svg class="animate-spin h-5 w-5 m-auto loader-update hidden" xmlns="http://www.w3.org/2000/svg" width="72"
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
                        <span class="hidden lg:block save-article-text">{{ __('Save Article') }}</span>
                        
                    </button>
                    <div class="relative">
                        <div class="dropdown-click">
                            <a href="javascript:void(0)" class="flex justify-center items-center gap-1.5 text-color-14 dark:text-white font-Figtree text-15 py-1 font-normal">
                                <span class="hidden lg:block">{{ __('More Options') }}</span>

                                <span class="hidden lg:block " id="arrow-toggle">
                                    <svg class="up-arrow" width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M9.00156 11.2123C8.90156 11.2123 8.80781 11.1968 8.72031 11.1658C8.63281 11.1348 8.55156 11.0816 8.47656 11.0061L5.02656 7.55605C4.88906 7.41855 4.82031 7.24355 4.82031 7.03105C4.82031 6.81855 4.88906 6.64356 5.02656 6.50606C5.16406 6.36856 5.33906 6.2998 5.55156 6.2998C5.76406 6.2998 5.93906 6.36856 6.07656 6.50606L9.00156 9.43105L11.9266 6.50606C12.0641 6.36856 12.2391 6.2998 12.4516 6.2998C12.6641 6.2998 12.8391 6.36856 12.9766 6.50606C13.1141 6.64356 13.1828 6.81855 13.1828 7.03105C13.1828 7.24355 13.1141 7.41855 12.9766 7.55605L9.52656 11.0061C9.45156 11.0811 9.37031 11.1343 9.28281 11.1658C9.19531 11.1973 9.10156 11.2128 9.00156 11.2123Z" fill="currentColor"/>
                                    </svg>
                                    <svg class="hidden down-arrow" width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M9.00156 6.78769C8.90156 6.78769 8.80781 6.8032 8.72031 6.8342C8.63281 6.8652 8.55156 6.91845 8.47656 6.99395L5.02656 10.4439C4.88906 10.5814 4.82031 10.7564 4.82031 10.9689C4.82031 11.1814 4.88906 11.3564 5.02656 11.4939C5.16406 11.6314 5.33906 11.7002 5.55156 11.7002C5.76406 11.7002 5.93906 11.6314 6.07656 11.4939L9.00156 8.56895L11.9266 11.4939C12.0641 11.6314 12.2391 11.7002 12.4516 11.7002C12.6641 11.7002 12.8391 11.6314 12.9766 11.4939C13.1141 11.3564 13.1828 11.1814 13.1828 10.9689C13.1828 10.7564 13.1141 10.5814 12.9766 10.4439L9.52656 6.99395C9.45156 6.91894 9.37031 6.8657 9.28281 6.8342C9.19531 6.8027 9.10156 6.7872 9.00156 6.78769Z" fill="currentColor"/>
                                    </svg>
                                </span>
                                    
                                <svg class="lg:hidden" xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                    viewBox="0 0 18 18" fill="none">
                                    <path d="M10.6875 14.625C10.6875 15.557 9.93198 16.3125 9 16.3125C8.06802 16.3125 7.3125 15.557 7.3125 14.625C7.3125 13.693 8.06802 12.9375 9 12.9375C9.93198 12.9375 10.6875 13.693 10.6875 14.625ZM10.6875 9C10.6875 9.93198 9.93198 10.6875 9 10.6875C8.06802 10.6875 7.3125 9.93198 7.3125 9C7.3125 8.06802 8.06802 7.3125 9 7.3125C9.93198 7.3125 10.6875 8.06802 10.6875 9ZM10.6875 3.375C10.6875 4.30698 9.93198 5.0625 9 5.0625C8.06802 5.0625 7.3125 4.30698 7.3125 3.375C7.3125 2.44302 8.06802 1.6875 9 1.6875C9.93198 1.6875 10.6875 2.44302 10.6875 3.375Z"fill="currentColor" />
                                </svg>
                            </a>
                        </div>
                        <div class="hidden origin-top-right absolute right-1 mt-2 w-[201px] border border-color-89 dark:border-color-47 rounded-lg bg-white dark:bg-[#333332] z-[9999] drop-down dropdown-shadow">
                            <div>
                                <a href="javascript:void(0)" class="flex justify-start items-center gap-1.5 text-14 font-normal text-color-14 dark:text-white font-Figtree px-4 py-2 hover:bg-color-F6 dark:hover:bg-[#3A3A39] rounded-t-lg generate-pdf">
                                    <span class="w-4 h-4">
                                        <svg  xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                        viewBox="0 0 16 16" fill="none">
                                        <g clip-path="url(#clip0_1227_1376)">
                                            <path d="M14 6.23529H10.8571V2H6.14286V6.23529H3L8.5 11.1765L14 6.23529ZM3 12.5882V14H14V12.5882H3Z"
                                                fill="currentColor" />
                                        </g>
                                        <defs>
                                            <clipPath id="clip0_1227_1376">
                                                <rect width="16" height="16" fill="white" />
                                            </clipPath>
                                        </defs>
                                        </svg>
                                    </span>
                                    
                                    <p class="">{{ __('Download PDF') }}</p>
                                </a>
                                <a href="javascript:void(0)" class="flex justify-start items-center gap-1.5 text-14 font-normal text-color-14 dark:text-white font-Figtree px-4 py-2 hover:bg-color-F6 dark:hover:bg-[#3A3A39] generate-word">
                                    <span class="w-4 h-4">
                                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                        viewBox="0 0 16 16" fill="none">
                                        <g clip-path="url(#clip0_1227_1376)">
                                            <path d="M14 6.23529H10.8571V2H6.14286V6.23529H3L8.5 11.1765L14 6.23529ZM3 12.5882V14H14V12.5882H3Z"
                                                fill="currentColor" />
                                        </g>
                                        <defs>
                                            <clipPath id="clip0_1227_1376">
                                                <rect width="16" height="16" fill="white" />
                                            </clipPath>
                                        </defs>
                                        </svg>
                                    </span>
                                    
                                    <p>{{__('Download DOCX') }}
                                    </p>
                                </a>
                                <a href="javascript:void(0)" class="flex justify-start items-center gap-1.5 text-14 font-normal text-color-14 dark:text-white font-Figtree px-4 py-2 hover:bg-color-F6 dark:hover:bg-[#3A3A39] copy-text hover:rounded-b-lg">
                                    <span class="w-4 h-4">
                                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
                                        <g clip-path="url(#clip0_1227_1386)">
                                        <path d="M11.1053 2H4.15789C3.52105 2 3 2.49091 3 3.09091V10.7273H4.15789V3.09091H11.1053V2ZM10.5263 4.18182L14 7.45455V12.9091C14 13.5091 13.4789 14 12.8421 14H6.46789C5.83105 14 5.31579 13.5091 5.31579 12.9091L5.32158 5.27273C5.32158 4.67273 5.83684 4.18182 6.47368 4.18182H10.5263ZM9.94737 8H13.1316L9.94737 5V8Z" fill="currentColor"/>
                                        </g>
                                        <defs>
                                        <clipPath id="clip0_1227_1386">
                                        <rect width="16" height="16" fill="white"/>
                                        </clipPath>
                                        </defs>
                                        </svg>
                                    </span>
                                    
                                    <p>{{ __('Copy to clipboard') }}</p>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <textarea id="basic-example"  class="hidden bg-color-F6">
                @if ($longArticle['filtered_content'])
                    {!! $longArticle['filtered_content'] !!}
                @else 
                    {!! preg_replace('/\*\*(.*?)\*\*/', '<br><br><h1 class="text-color-14 text-24 font-semibold font-RedHat dark:text-white">$1</h1>', $longArticle['content']) !!}
                @endif 
            </textarea>
        </div>
    </div>
</div>
{{-- end main content --}}
@endsection
@section('js')
    <script type="text/javascript">
        'use script';
        var resetFlag = @json($resetFlag);
        var longArticleId = @json($longArticleId);
        var saveButtonText = '{{ __("Save Article") }}';
        var saveButtonUpdateText = '{{ __("Saving...") }}';
        var userId = {{ auth()->id() }};
    </script>
    <script src="{{ asset('public/assets/plugin/tinymce 6.3.1/js/tinymce/tinymce.min.js') }}"></script>
    <script src="{{ asset('Modules/OpenAI/Resources/assets/js/tiny_mce.min.js') }}"></script>
    <script src="{{ asset('Modules/OpenAI/Resources/assets/js/fileSaver.min.js') }}"></script>
    <script src="{{ asset('Modules/OpenAI/Resources/assets/js/jquery.wordexport.min.js') }}"></script>
    <script src="{{ asset('Modules/OpenAI/Resources/assets/js/html2pdf.min.js') }}"></script>
    <script src="{{ asset('Modules/OpenAI/Resources/assets/js/customer/long_article/edit.min.js') }}"></script>
@endsection

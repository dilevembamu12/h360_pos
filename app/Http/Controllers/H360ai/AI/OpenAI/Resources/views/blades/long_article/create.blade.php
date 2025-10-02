@extends('layouts.user_master')
@section('page_title', __('Long Article'))
@section('content')
@php
    $wordLeft = 0;
    if ($userSubscription && in_array($userSubscription->status, ['Active', 'Cancel'])) {
        $wordLeft = $featureLimit['word']['remain'];
        $wordLimit = $featureLimit['word']['limit'];
    }
@endphp
<div class="w-[68.9%] 5xl:w-[85.9%] bg-[#F6F3F2] dark:bg-[#292929] flex flex-col flex-1 border-l dark:border-[#474746] border-color-DF h-screen">
    <div class="md:overflow-auto sidebar-scrollbar md:h-screen overflow-x-hidden xl:pb-6 article-scroll">
        
        <!-- Header -->
        <div class="px-5 6xl:px-[100px] 7xl:px-[140px] 8xl:px-[200px] 9xl:px-[261px] pt-[76px] lg:pt-[80px] pb-5">
            <div class="flex justify-between items-center">
                <p class="text-color-14 text-24 font-semibold font-RedHat dark:text-white wrap-anywhere">{{ __('Long Article') }}</p>
                <button class="flex float-right justify-end gap-1 items-center" onclick="resetLongArticleFormData()">
                    <span class="text-color-14 dark:text-white">
                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <g clip-path="url(#clip0_10232_5958)">
                            <mask id="mask0_10232_5958" style="mask-type:luminance" maskUnits="userSpaceOnUse" x="0" y="0" width="16" height="16">
                            <path d="M0 0H16V16H0V0Z" fill="white"/>
                            </mask>
                            <g mask="url(#mask0_10232_5958)">
                            <path d="M7.99935 2.66699V0.666992L5.33268 3.33366L7.99935 6.00033V4.00033C10.206 4.00033 11.9993 5.79366 11.9993 8.00033C11.9993 8.67366 11.8327 9.31366 11.5327 9.86699L12.506 10.8403C13.026 10.0203 13.3327 9.04699 13.3327 8.00033C13.3327 5.05366 10.946 2.66699 7.99935 2.66699ZM7.99935 12.0003C5.79268 12.0003 3.99935 10.207 3.99935 8.00033C3.99935 7.32699 4.16602 6.68699 4.46602 6.13366L3.49268 5.16033C2.97268 5.98033 2.66602 6.95366 2.66602 8.00033C2.66602 10.947 5.05268 13.3337 7.99935 13.3337V15.3337L10.666 12.667L7.99935 10.0003V12.0003Z" fill="currentColor"/>
                            </g>
                            </g>
                            <defs>
                            <clipPath id="clip0_10232_5958">
                            <rect width="16" height="16" fill="white"/>
                            </clipPath>
                            </defs>
                        </svg>
                    </span>
                    <span class="text-15 text-color-14 dark:text-white font-Figtree font-normal">
                        {{ __('Reset') }}
                    </span>
                </button>
            </div>
            <div class="flex flex-col gap-5 lg:flex-row lg:justify-between lg:items-center mt-2 lg:mt-[7px]">
                <p class="text-color-89 text-[13px] leading-5 font-medium font-Figtree wrap-anywhere lg:w-1/2">
                    {{ __('Generate professional and SEO optimized blogs with the power of AI in seconds.') }}
                </p>
                <div class="flex items-center lg:justify-end gap-2.5 CreditBalance">
                    @if ($wordLeft && auth()->user()->id == $userId)
                        @include('openai::blades.long_article.credit_balance', ['wordLeft', $wordLeft])
                    @endif
                </div>
            </div>
        </div>
        <!-- Header -->

        <div class="subscription-main px-5 6xl:px-[100px] 7xl:px-[140px] 8xl:px-[200px] 9xl:px-[261px] flex gap-6 xl:flex-row flex-col">
            
            <!-- Left Side -->
            <div class="xl:w-1/2 5xl:w-[474px]">
                <div class="sticky top-[10%]">
                    <div class="bg-white dark:bg-[#3A3A39] px-6 pb-6 rounded-xl StepperFormWrapper">
                        
                        <div class="border-b border-[#DFDFDF] dark:border-[#474746] -mx-4 sm:-mx-6">
                            <div class="px-5 pt-1 pb-[30px]">
                                <div class="ml-6 sm:ml-[45px] mr-[11px] sm:mr-[29px] p-4">

                                    <!-- Stepper -->
                                    <div class="flex items-center StepperParent">
                                        <div class="Stepper">
                                            <div class="flex items-center text-gray-500 relative empty-circle">
                                                <div class="flex items-center justify-center rounded-full h-6 w-6 py-1 bg-gradient-to-r from-rose-400 via-fuchsia-500 to-indigo-500">
                                                    <p class="flex items-center justify-center text-11 text-[#474746] bg-white dark:bg-[#3A3A39] dark:text-white font-medium font-Figtree h-[22px] w-[22px] py-1 rounded-full">{{ __('1 ') }}</p>
                                                </div>
                                                <div class="absolute top-0 -ml-[52px] text-center mt-8 w-32 text-xs font-medium font-Figtree text-[#474746] dark:text-[#DFDFDF]">{{ __('Titles & Keywords') }}</div>
                                            </div>
                                            <div class="flex items-center relative full-circle hidden">
                                                <div class="flex items-center justify-center rounded-full h-6 w-6 py-1">
                                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <circle cx="12" cy="12" r="12" fill="url(#paint0_linear_10431_5608)"/>
                                                        <path d="M17.9398 7.91788C14.8844 10.7218 12.508 14.5819 11.7788 16.4428C11.7662 16.4931 11.7033 16.5434 11.6404 16.5434C11.6279 16.556 11.6279 16.556 11.6153 16.556C11.565 16.556 11.5147 16.5434 11.4896 16.5057L7.00077 11.9414C6.96305 11.9037 8.321 10.6967 8.38387 10.7469L10.8483 12.6833C11.8039 11.5517 14.1677 8.99921 17.4495 7C17.5123 7 17.9901 7.7167 17.9901 7.7167C18.0153 7.77957 17.9901 7.86758 17.9398 7.91788Z" fill="white"/>
                                                        <defs>
                                                        <linearGradient id="paint0_linear_10431_5608" x1="4.15256" y1="2.35886" x2="23.0334" y2="17.727" gradientUnits="userSpaceOnUse">
                                                        <stop stop-color="#FFF1BF"/>
                                                        <stop offset="0.2947" stop-color="#EC458D"/>
                                                        <stop offset="0.393" stop-color="#E14591"/>
                                                        <stop offset="0.561" stop-color="#C6469D"/>
                                                        <stop offset="0.7784" stop-color="#9A49B1"/>
                                                        <stop offset="1" stop-color="#664CC9"/>
                                                        </linearGradient>
                                                        </defs>
                                                    </svg>
                                                </div>
                                                <div class="absolute top-0 -ml-[52px] text-center mt-8 w-32 text-12 font-medium font-Figtree text-color-14 dark:text-white">{{ __('Titles & Keywords') }}</div>
                                            </div>
                                        </div>
                                        <div class="flex-auto h-px bg-gradient-to-r from-[#EC458D] via-[#664CC9] to-[#FFF1BF]"></div>
                                        
                                        <div class="Stepper">
                                            <div class="flex items-center text-gray-500 relative empty-circle">
                                                <div class="flex items-center justify-center rounded-full h-6 w-6 py-1 bg-gradient-to-r from-rose-400 via-fuchsia-500 to-indigo-500">
                                                    <p class="flex items-center justify-center text-11 text-[#474746] bg-white dark:bg-[#3A3A39] dark:text-white font-medium font-Figtree h-[22px] w-[22px] py-1 rounded-full">{{ __(' 2 ') }}</p>
                                                </div>
                                                <div class="absolute top-0 -ml-[52px] text-center mt-8 w-32 text-12 font-medium font-Figtree text-[#474746] dark:text-[#DFDFDF]">{{ __('Outlines') }}</div>
                                            </div>
                                            <div class="flex items-center relative full-circle hidden">
                                                <div class="flex items-center justify-center rounded-full h-6 w-6 py-1">
                                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <circle cx="12" cy="12" r="12" fill="url(#paint0_linear_10431_5608)"/>
                                                        <path d="M17.9398 7.91788C14.8844 10.7218 12.508 14.5819 11.7788 16.4428C11.7662 16.4931 11.7033 16.5434 11.6404 16.5434C11.6279 16.556 11.6279 16.556 11.6153 16.556C11.565 16.556 11.5147 16.5434 11.4896 16.5057L7.00077 11.9414C6.96305 11.9037 8.321 10.6967 8.38387 10.7469L10.8483 12.6833C11.8039 11.5517 14.1677 8.99921 17.4495 7C17.5123 7 17.9901 7.7167 17.9901 7.7167C18.0153 7.77957 17.9901 7.86758 17.9398 7.91788Z" fill="white"/>
                                                        <defs>
                                                        <linearGradient id="paint0_linear_10431_5608" x1="4.15256" y1="2.35886" x2="23.0334" y2="17.727" gradientUnits="userSpaceOnUse">
                                                        <stop stop-color="#FFF1BF"/>
                                                        <stop offset="0.2947" stop-color="#EC458D"/>
                                                        <stop offset="0.393" stop-color="#E14591"/>
                                                        <stop offset="0.561" stop-color="#C6469D"/>
                                                        <stop offset="0.7784" stop-color="#9A49B1"/>
                                                        <stop offset="1" stop-color="#664CC9"/>
                                                        </linearGradient>
                                                        </defs>
                                                    </svg>
                                                </div>
                                                <div class="absolute top-0 -ml-[52px] text-center mt-8 w-32 text-12 font-medium font-Figtree text-color-14 dark:text-white">{{ __('Outlines') }}</div>
                                            </div>
                                        </div>

                                        <div class="flex-auto h-px bg-gradient-to-r from-[#EC458D] via-[#664CC9] to-[#FFF1BF]"></div>
                                        
                                        <div class="Stepper">
                                            <div class="flex items-center text-gray-500 relative empty-circle">
                                                <div class="flex items-center justify-center rounded-full h-6 w-6 py-1 bg-gradient-to-r from-rose-400 via-fuchsia-500 to-indigo-500">
                                                    <p class="flex items-center justify-center text-11 text-[#474746] bg-white dark:bg-[#3A3A39] dark:text-white font-medium font-Figtree h-[22px] w-[22px] py-1 rounded-full">{{ __('3') }}</p>
                                                </div>
                                                <div class="absolute top-0 -ml-[52px] text-center mt-8 w-32 text-xs font-medium font-Figtree text-[#474746] dark:text-[#DFDFDF]">{{ __('Finalize') }}</div>
                                            </div>
                                            <div class="flex items-center relative full-circle hidden">
                                                <div class="flex items-center justify-center rounded-full h-6 w-6 py-1">
                                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <circle cx="12" cy="12" r="12" fill="url(#paint0_linear_10431_5608)"/>
                                                        <path d="M17.9398 7.91788C14.8844 10.7218 12.508 14.5819 11.7788 16.4428C11.7662 16.4931 11.7033 16.5434 11.6404 16.5434C11.6279 16.556 11.6279 16.556 11.6153 16.556C11.565 16.556 11.5147 16.5434 11.4896 16.5057L7.00077 11.9414C6.96305 11.9037 8.321 10.6967 8.38387 10.7469L10.8483 12.6833C11.8039 11.5517 14.1677 8.99921 17.4495 7C17.5123 7 17.9901 7.7167 17.9901 7.7167C18.0153 7.77957 17.9901 7.86758 17.9398 7.91788Z" fill="white"/>
                                                        <defs>
                                                        <linearGradient id="paint0_linear_10431_5608" x1="4.15256" y1="2.35886" x2="23.0334" y2="17.727" gradientUnits="userSpaceOnUse">
                                                        <stop stop-color="#FFF1BF"/>
                                                        <stop offset="0.2947" stop-color="#EC458D"/>
                                                        <stop offset="0.393" stop-color="#E14591"/>
                                                        <stop offset="0.561" stop-color="#C6469D"/>
                                                        <stop offset="0.7784" stop-color="#9A49B1"/>
                                                        <stop offset="1" stop-color="#664CC9"/>
                                                        </linearGradient>
                                                        </defs>
                                                    </svg>
                                                </div>
                                                <div class="absolute top-0 -ml-[52px] text-center mt-8 w-32 text-12 font-medium font-Figtree text-color-14 dark:text-white">{{ __('Finalize') }}</div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Stepper -->
                                </div>
                            </div>
                        </div>

                        <!-- Forms: Title, Outline, Article -->
                        @include('openai::blades.long_article.step_form.titles_form')
                        @include('openai::blades.long_article.step_form.outlines_form')
                        @include('openai::blades.long_article.step_form.article_form')
                        <!-- Forms: Title, Outline, Article -->

                        <div class="ContinueButtonDiv hidden mt-3">
                            <button class="magic-bg w-full rounded-xl text-16 text-white font-semibold py-3 flex justify-center items-center gap-3 ContinueButton">
                                <span class="flex gap-1 items-center">
                                    <span>
                                        {{ __('Continue') }}
                                    </span>
                                    <span>
                                        <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M4.87958 11.4712C5.16434 11.7316 5.62602 11.7316 5.91078 11.4712L10.2858 7.47124C10.5705 7.21089 10.5705 6.78878 10.2858 6.52843L5.91078 2.52843C5.62602 2.26808 5.16434 2.26808 4.87958 2.52843C4.59483 2.78878 4.59483 3.21089 4.87958 3.47124L8.73899 6.99984L4.87958 10.5284C4.59483 10.7888 4.59483 11.2109 4.87958 11.4712Z" fill="white"/>
                                        </svg>                                    
                                    </span>
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
                        
                    </div>
                    <p class="text-color-14 text-14 font-normal font-Figtree dark:text-white wrap-anywhere mt-4">
                        {{ __('Need extra credits?') }} <a class="underline font-medium" href="{{ route('user.subscription.smallPlan') }}">{{ __('Buy credit packages.') }}</a>
                    </p>
                </div>
            </div>
            <!-- Left Side -->

            <!-- Right Side -->
            <div class="grow xl:px-0 9xl:pb-[46px] dark:bg-[#292929] xl:overflow-initial sidebar-scrollbar xl:w-1/2 FormDataWrapper">
                
                
                <div class="pb-5">

                    <div class="bg-white dark:bg-[#3A3A39] px-5 8xl:px-[140px] py-[37px] rounded-xl mb-5 TitleSuggestionText">
                        <p class="text-color-89 text-[13px] text-center leading-5 font-normal font-Figtree wrap-anywhere">
                            {{ __('Title   Please fill in the inputs and select your preferences. Your generated titles will appear here.') }}
                        </p>
                    </div>
                    <!-- Title Data -->
                    <div class="FormData Active">
                        <div class="TitleData">
                            @include('openai::blades.long_article.step_data.title', ['titles' => []])
                        </div>
                    </div>
                    <!-- Title Data -->

                    <div class="bg-white dark:bg-[#3A3A39] px-5 8xl:px-[140px] py-[37px] rounded-xl mb-5 OutlineSuggestionText">
                        <p class="text-color-89 text-[13px] text-center leading-5 font-normal font-Figtree wrap-anywhere">
                            {{ __('Outline Please fill in the inputs and select your preferences. Your generated titles will appear here.') }}
                        </p>
                    </div>
                    <!-- Outline Data -->
                    <div class="FormData">
                        <div class="OutlineData">
                            @include('openai::blades.long_article.step_data.outline', ['outlinesData' => []])
                        </div>
                    </div>
                    <!-- Outline Data -->

                    <!-- Article Data -->
                    <div class="FormData">
                        <div class="bg-white dark:bg-[#3A3A39] px-5 8xl:px-[140px] py-[37px] h-[292px] rounded-xl mb-5 relative flex justify-center ArticleSuggestion hidden">
                            <p class="text-color-89 text-[13px] text-center leading-5 font-normal font-Figtree wrap-anywhere">
                                {{ __('Please fill in the inputs and select your preferences. Your generated titles will appear here.') }}
                            </p>
                            <img class="w-[177px] h-[251px] object-cover absolute mt-[77px] avatar-img" src="{{ asset('Modules/OpenAI/Resources/assets/image/img-robo-3.png') }}" alt="{{ __('Image') }}">
                        </div>
                        <div class="bg-white dark:bg-[#3A3A39] px-5 py-[37px] h- [292px] rounded-xl mb-5 relative ArticleSection">
                            
                            <div class="ArticleData dark:text-white">

                                @include('openai::blades.long_article.step_data.article', ['articleData' => null])
                            </div>

                            <div class="mt-3">
                                <a href="#" class="BlogEditButton hidden magic-bg rounded-lg text-[14px] text-white justify-center items-center font-medium py-[9px] flex text-center cursor-pointer font-Figtree w-max px-6 whitespace-nowrap gap-2">
                                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M16.181 2.58949L17.4135 3.82165C18.1955 4.60347 18.1955 5.87002 17.4135 6.65184L15.7837 8.28116L15.7524 8.24989L15.2519 7.74952L12.2489 4.74732L11.7171 4.21568L13.3469 2.58637C14.1289 1.80454 15.3958 1.80454 16.1779 2.58637L16.181 2.58949ZM9.53996 5.57918C9.24592 5.28522 8.77044 5.28522 8.47952 5.57918L5.2857 8.77214C4.99166 9.06611 4.51618 9.06611 4.22526 8.77214C3.93435 8.47818 3.93122 8.00283 4.22526 7.71199L7.41596 4.51903C8.29496 3.64026 9.72139 3.64026 10.6004 4.51903L11.0102 4.92871L11.542 5.46035L14.545 8.46254L15.0455 8.96291L15.0768 8.99418L14.545 9.52582L9.18023 14.886C7.67872 16.3871 5.7643 17.4128 3.68097 17.8288L2.89893 17.9851C2.65181 18.0352 2.39843 17.957 2.22013 17.7787C2.04182 17.6005 1.96675 17.3472 2.01367 17.1001L2.17008 16.3183C2.58612 14.2355 3.61215 12.3216 5.11365 10.8205L9.94975 5.98886L9.53996 5.57918Z" fill="url(#paint0_linear_10232_5981)"></path>
                                        <defs>
                                        <linearGradient id="paint0_linear_10232_5981" x1="12.4027" y1="16.0307" x2="6.84878" y2="3.49729" gradientUnits="userSpaceOnUse">
                                        <stop stop-color="#E60C84"></stop>
                                        <stop offset="1" stop-color="#FFCF4B"></stop>
                                        </linearGradient>
                                        </defs>
                                    </svg>
                                    <span> {{ __('Save & Edit Article') }}</span>
                                </a>
                            </div>
                        </div>
                    </div>
                    <!-- Article Data -->
                </div>     
            </div>
            <!-- Right Side -->
        </div>
    </div>
</div>
{{-- end main content --}}
@endsection
@section('js')
<script type="text/javascript">

    'use strict';
    var csrf = '{{ csrf_token() }}';
    var longArticleCreateUrl = '{{ route("user.long_article.create") }}';
    var titleGenerateUrl = '{{ route("user.long_article.generate_titles") }}';
    var outlineGenerateUrl = '{{ route("user.long_article.generate_outlines") }}';
    var initArticleGenerateUrl = '{{ route("user.long_article.init_article") }}';
    var articleGenerateUrl = '{{ route("user.long_article.generate_article") }}';
    var displayTitleDataUrl = '{{ route("user.long_article.display_title_data") }}';
    var displayOutlineDataUrl = '{{ route("user.long_article.display_outline_data") }}';
    var displayArticleBlogDataUrl = '{{ route("user.long_article.display_article_data") }}';
    var forgetSessionDataUrl = '{{ route("user.long_article.forget_session_data") }}';
    var userId = {{ auth()->id() }};

    var initLongArticleFormData = {
        activeStep: 1,
        longArticleId: '',
        
        titles: {
            for: 'titles',
            data: {
                topic: '',
                keywords: '',
                title: '',
                generatedTitles: []
            }
        },
        outlines: {
            for: 'outlines',
            data: {
                title: '',
                keywords: '',
                generatedOutlines: []
            }
        },
        article: {
            for: 'article',
            data: {
                title: '',
                keywords: '',
                outlines: [],
                generatedArticleBlogId: null
            }
        }
    };

    var oldLongArticle = @json($longArticle);

    var oldLongArticleTitle = @json($longArticleTitle);
    var oldGeneratedTitles = JSON.stringify(@json($generatedTitles));

    var oldLongArticleOutline = @json($longArticleOutline);
    var oldGeneratedOutlines = JSON.stringify(@json($generatedOutlines));

</script>
<script src="{{ asset('Modules/OpenAI/Resources/assets/js/customer/long_article/script.min.js') }}"></script>
@endsection

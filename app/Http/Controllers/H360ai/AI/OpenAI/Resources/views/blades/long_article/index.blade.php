@extends('layouts.user_master')
@section('page_title', __('Long Article List'))
@section('content')
{{-- main content --}}
<div class="dark:bg-[#292929] bg-[#F6F3F2] sidebar-scrollbar overflow-auto flex flex-col flex-1 font-Figtree border-l dark:border-[#474746] border-color-DF relative">
    @include('openai::blades.history-nav', ['menu' => 'long_article'])
    <div class="9xl:px-[185px] 7xl:px-[140px] px-5 9xl:pb-[22px] pb-28">
        <div class="flex justify-end items-center mt-2.5 mb-5 gap-3">
            <a href="{{ route('user.long_article.create') }}" class="justify-end items-center gap-2 text-color-14 dark:text-white inline-block text-right">
                <svg class="inline-block" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 14 14" fill="none">
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M7 1.75C7.24162 1.75 7.4375 1.94588 7.4375 2.1875V6.5625H11.8125C12.0541 6.5625 12.25 6.75838 12.25 7C12.25 7.24162 12.0541 7.4375 11.8125 7.4375H7.4375V11.8125C7.4375 12.0541 7.24162 12.25 7 12.25C6.75837 12.25 6.5625 12.0541 6.5625 11.8125V7.4375H2.1875C1.94588 7.4375 1.75 7.24162 1.75 7C1.75 6.75838 1.94588 6.5625 2.1875 6.5625H6.5625V2.1875C6.5625 1.94588 6.75838 1.75 7 1.75Z" fill="currentColor"/>
                </svg>
                <span class="text-[15px] leading-[22px] font-Figtree font-normal text-right">{{ __('Generate Article')}}</span>
            </a>
        </div>
        <div class="bg-white dark:bg-[#3A3A39] rounded-xl image-list-table">
            <div class="flex flex-col">
                <div class="xl:overflow-auto rounded-xl p-1.5">
                    <table class="min-w-full longArtileTable">
                        @if ($longArticles->isNotEmpty())
                        <thead class="bg-color-DF dark:bg-[#474746] rounded-xl longArticleTableHeader">
                            <tr class="rounded-lg">
                                <th class="md:pl-[34px] md:pr-6 px-3 py-[9px] font-Figtree text-left text-14 font-medium text-color-14 md:w-[200px] w-28 dark:text-white docs-table-row-one">
                                    {{ __('Title') }}
                                </th>
                                <th class="xs:px-6 py-[9px] text-left font-Figtree text-14 font-medium text-color-14 dark:text-white">
                                    {{ __('Generated') }}
                                </th>
                                <th
                                    class="xs:px-6 py-[9px] text-left font-Figtree text-14 font-medium text-color-14 dark:text-white hidden xl:table-cell">
                                    {{ __('Provider') }}
                                </th>
                                <th
                                    class="xs:px-6 py-[9px] text-left font-Figtree text-14 font-medium text-color-14 dark:text-white hidden xl:table-cell">
                                    {{ __('Model') }}
                                </th>
                                <th class="xs:px-6 py-[9px] text-left font-Figtree text-14 font-medium text-color-14 dark:text-white hidden xl:table-cell">
                                    {{ __('status') }}
                                </th>
                                <th class="md:pr-[34px] pr-3 py-[9px] text-right font-Figtree text-14 font-medium text-color-14 dark:text-white w-max action-rtl">
                                    {{ __('Actions') }}
                                </th>
                            </tr>
                        </thead>
                        @endif
                        <tbody>
                            @forelse ($longArticles as $longArticle)
                                @php
                                    $articleStatus = $longArticle->completed_step == 3 ? 'Completed' : 'Incomplete';
                                @endphp

                                <tr class="border-b dark:border-[#474746]" id="longArticle_{{ $longArticle->id }}">
                                    <td class="text-14 font-Figtree py-[18px] text-color-14 dark:text-white font-medium md:pl-[34px] md:pr-6 px-3  docs-table-row-one">
                                        <a href="{{ $articleStatus == 'Completed' ? route('user.long_article.edit', ['id' => $longArticle->id]) : route('user.long_article.create') }}?uuid={{ $longArticle->unique_identifier }}"
                                            class="flex items-center justify-start">
                                            <span class="w-[138px] xs:w-[170px] 4xl:w-[300px] lg:w-[200px] break-words line-clamp-double">
                                                {!! trimWords($longArticle->article_title ?? $longArticle->where('parent_id', $longArticle->id)->latest()->first()?->title_topic, 85) !!}
                                            </span>
                                        </a>
                                        <span class="text-[13px] font-Figtree text-color-89 font-medium mt-2 xl:hidden block break-words">{{ timeToGo($longArticle->reated_at, false, 'ago') }}</span>
                                        <span class="text-[13px] font-Figtree text-color-89 font-medium mt-2 xl:hidden block">{{ $longArticle->total_words }}</span>
                                        <span class="text-[13px] font-Figtree text-color-89 font-medium mt-2 xl:hidden block">{{ $articleStatus }}</span>
                                    </td>

                                     <!-- Generated Time -->
                                    <td  class="text-14 font-Figtree py-[18px] text-color-89 font-medium xs:px-6 lg:w-64 whitespace-nowrap align-top xl:align-middle">
                                        {{ timeToGo($longArticle->created_at, false, 'ago') }}
                                    </td>

                                     <!-- Provider -->
                                    <td class="text-14 font-Figtree py-[18px] text-color-89 font-medium px-6 w-64 whitespace-nowrap hidden xl:table-cell">
                                        {{ $longArticle->provider }}
                                    </td>
                                     <!-- Model -->
                                    <td class="text-14 font-Figtree py-[18px] text-color-89 font-medium px-6 w-64 whitespace-nowrap hidden xl:table-cell">
                                        {{ $longArticle->generation_options['model'] ?? '' }}
                                    </td>

                                    <!-- Status -->
                                    <td class="text-14 font-Figtree py-[18px] text-color-89 font-medium px-6 w-64 whitespace-nowrap hidden xl:table-cell">
                                        {{ $articleStatus }}
                                    </td>
                                    <td class="text-14 font-Figtree py-[18px] text-color-14 dark:text-white font-medium md:pr-[34px] pr-3 w-max align-top xl:align-middle text-right action-rtl">
                                        
                                        <!-- Desktop Menu -->
                                        <div class="gap-4 justify-end items-center hidden xl:flex">
                                            @if ($articleStatus == 'Completed')
                                                <div class="relative">
                                                    <a class="tooltip-edit flex items-center border border-color-89 dark:border-color-47 text-color-14 dark:text-white bg-white dark:bg-color-47 p-2 rounded-lg justify-center neg-transition-scale" title = "{{ __('Edit Article')}}"
                                                        href="{{ route('user.long_article.edit', ['id' => $longArticle->id]) }}">
                                                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
                                                            <path d="M2.73266 10.0443L2.01789 13.1291C1.99323 13.2419 1.99407 13.3587 2.02036 13.4711C2.04665 13.5835 2.09771 13.6886 2.16982 13.7787C2.24193 13.8689 2.33326 13.9418 2.43715 13.9921C2.54104 14.0424 2.65485 14.0689 2.77028 14.0696C2.82407 14.075 2.87826 14.075 2.93205 14.0696L6.03568 13.3548L11.9947 7.41841L8.66906 4.10034L2.73266 10.0443Z" fill="currentColor"></path>
                                                            <path d="M13.8682 4.44626L11.6486 2.22669C11.5027 2.0815 11.3052 2 11.0993 2C10.8935 2 10.696 2.0815 10.5501 2.22669L9.31616 3.46062L12.638 6.78245L13.8719 5.54852C13.9441 5.47594 14.0013 5.38984 14.0402 5.29514C14.0791 5.20043 14.099 5.09899 14.0986 4.99661C14.0983 4.89423 14.0777 4.79292 14.0382 4.69849C13.9986 4.60405 13.9409 4.51834 13.8682 4.44626Z" fill="currentColor"></path>
                                                        </svg>
                                                    </a>
                                                </div>
                                            @endif
                                            @if ($articleStatus == 'Incomplete')
                                            <div class="relative">
                                                <a class="tooltip-edit flex items-center border border-color-89 dark:border-color-47 text-color-14 dark:text-white bg-white dark:bg-color-47 p-2 rounded-lg justify-center neg-transition-scale" title = "{{ __('Keep Writing')}}"
                                                    href="{{ route('user.long_article.create') }}?uuid={{ $longArticle->unique_identifier }}">
                                                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M16.181 2.58949L17.4135 3.82165C18.1955 4.60347 18.1955 5.87002 17.4135 6.65184L15.7837 8.28116L15.7524 8.24989L15.2519 7.74952L12.2489 4.74732L11.7171 4.21568L13.3469 2.58637C14.1289 1.80454 15.3958 1.80454 16.1779 2.58637L16.181 2.58949ZM9.53996 5.57918C9.24592 5.28522 8.77044 5.28522 8.47952 5.57918L5.2857 8.77214C4.99166 9.06611 4.51618 9.06611 4.22526 8.77214C3.93435 8.47818 3.93122 8.00283 4.22526 7.71199L7.41596 4.51903C8.29496 3.64026 9.72139 3.64026 10.6004 4.51903L11.0102 4.92871L11.542 5.46035L14.545 8.46254L15.0455 8.96291L15.0768 8.99418L14.545 9.52582L9.18023 14.886C7.67872 16.3871 5.7643 17.4128 3.68097 17.8288L2.89893 17.9851C2.65181 18.0352 2.39843 17.957 2.22013 17.7787C2.04182 17.6005 1.96675 17.3472 2.01367 17.1001L2.17008 16.3183C2.58612 14.2355 3.61215 12.3216 5.11365 10.8205L9.94975 5.98886L9.53996 5.57918Z" fill="url(#paint0_linear_10232_5981)"></path>
                                                        <defs>
                                                        <linearGradient id="paint0_linear_10232_5981" x1="12.4027" y1="16.0307" x2="6.84878" y2="3.49729" gradientUnits="userSpaceOnUse">
                                                        <stop stop-color="#141414"></stop>
                                                        <stop offset="1" stop-color="#141414"></stop>
                                                        </linearGradient>
                                                        </defs>
                                                    </svg>
                                                </a>
                                            </div>
                                            @endif
                                            <a class="tooltip-delete relative flex items-center p-2 border border-color-89 dark:border-color-47 bg-white text-color-14 dark:text-white dark:bg-color-47 rounded-lg justify-center article-modal-toggle" id="{{ $longArticle->id }}" title ="{{ __('Delete Article')}}" href="javascript: void(0)" >
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
                                                    <path d="M3.84615 2.8C3.37884 2.8 3 3.15817 3 3.6V4.4C3 4.84183 3.37884 5.2 3.84615 5.2H4.26923V12.4C4.26923 13.2837 5.0269 14 5.96154 14H11.0385C11.9731 14 12.7308 13.2837 12.7308 12.4V5.2H13.1538C13.6212 5.2 14 4.84183 14 4.4V3.6C14 3.15817 13.6212 2.8 13.1538 2.8H10.1923C10.1923 2.35817 9.81347 2 9.34615 2H7.65385C7.18653 2 6.80769 2.35817 6.80769 2.8H3.84615ZM6.38462 6C6.61827 6 6.80769 6.17909 6.80769 6.4V12C6.80769 12.2209 6.61827 12.4 6.38462 12.4C6.15096 12.4 5.96154 12.2209 5.96154 12L5.96154 6.4C5.96154 6.17909 6.15096 6 6.38462 6ZM8.5 6C8.73366 6 8.92308 6.17909 8.92308 6.4V12C8.92308 12.2209 8.73366 12.4 8.5 12.4C8.26634 12.4 8.07692 12.2209 8.07692 12V6.4C8.07692 6.17909 8.26634 6 8.5 6ZM11.0385 6.4V12C11.0385 12.2209 10.849 12.4 10.6154 12.4C10.3817 12.4 10.1923 12.2209 10.1923 12V6.4C10.1923 6.17909 10.3817 6 10.6154 6C10.849 6 11.0385 6.17909 11.0385 6.4Z" fill="currentColor"/>
                                                </svg>
                                            </a>

                                        </div>

                                        <!-- Mobile Menu -->
                                        <div class="relative xl:hidden inline-block">
                                            <button class="table-dropdown-click">
                                                <a href="javascript: void(0)" class="cursor-pointer border p-2 border-color-89 rounded-lg flex justify-end">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18" fill="none">
                                                        <path d="M10.6875 14.625C10.6875 15.557 9.93198 16.3125 9 16.3125C8.06802 16.3125 7.3125 15.557 7.3125 14.625C7.3125 13.693 8.06802 12.9375 9 12.9375C9.93198 12.9375 10.6875 13.693 10.6875 14.625ZM10.6875 9C10.6875 9.93198 9.93198 10.6875 9 10.6875C8.06802 10.6875 7.3125 9.93198 7.3125 9C7.3125 8.06802 8.06802 7.3125 9 7.3125C9.93198 7.3125 10.6875 8.06802 10.6875 9ZM10.6875 3.375C10.6875 4.30698 9.93198 5.0625 9 5.0625C8.06802 5.0625 7.3125 4.30698 7.3125 3.375C7.3125 2.44302 8.06802 1.6875 9 1.6875C9.93198 1.6875 10.6875 2.44302 10.6875 3.375Z" fill="#898989"></path>
                                                    </svg>
                                                </a>
                                            </button>
                                            <div class="absolute ltr:right-0 rtl:left-0 mt-2 w-[201px] border border-color-89 dark:border-color-47 rounded-lg bg-white dark:bg-[#333332] z-50 table-drop-body dropdown-shadow table-dropdwon-rtl">
                                                <div>
                                                    @if ($articleStatus == 'Completed')
                                                        <a href="{{ route('user.long_article.edit', ['id' => $longArticle->id ]) }}" class="flex justify-start items-center gap-1.5 text-14 font-normal text-color-14 dark:text-white font-Figtree px-4 py-2 hover:bg-color-F6 dark:hover:bg-[#3A3A39] rounded-t-lg text-left">
                                                            <span class="w-4 h-4">
                                                                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
                                                                    <path d="M2.73266 10.0443L2.01789 13.1291C1.99323 13.2419 1.99407 13.3587 2.02036 13.4711C2.04665 13.5835 2.09771 13.6886 2.16982 13.7787C2.24193 13.8689 2.33326 13.9418 2.43715 13.9921C2.54104 14.0424 2.65485 14.0689 2.77028 14.0696C2.82407 14.075 2.87826 14.075 2.93205 14.0696L6.03568 13.3548L11.9947 7.41841L8.66906 4.10034L2.73266 10.0443Z" fill="currentColor"></path>
                                                                    <path d="M13.8682 4.44626L11.6486 2.22669C11.5027 2.0815 11.3052 2 11.0993 2C10.8935 2 10.696 2.0815 10.5501 2.22669L9.31616 3.46062L12.638 6.78245L13.8719 5.54852C13.9441 5.47594 14.0013 5.38984 14.0402 5.29514C14.0791 5.20043 14.099 5.09899 14.0986 4.99661C14.0983 4.89423 14.0777 4.79292 14.0382 4.69849C13.9986 4.60405 13.9409 4.51834 13.8682 4.44626Z" fill="currentColor"></path>
                                                                </svg>
                                                            </span>
                                                            
                                                            <p>{{ __('Edit Article')}}</p>
                                                        </a>
                                                    @endif
                                                    @if ($articleStatus == 'Incomplete')
                                                        <a href="{{ route('user.long_article.create') }}?uuid={{ $longArticle->unique_identifier }}" class="flex justify-start items-center gap-1.5 text-14 font-normal text-color-14 dark:text-white font-Figtree px-4 py-2 hover:bg-color-F6 dark:hover:bg-[#3A3A39] rounded-t-lg text-left">
                                                        <span class="w-4 h-4">
                                                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path d="M16.181 2.58949L17.4135 3.82165C18.1955 4.60347 18.1955 5.87002 17.4135 6.65184L15.7837 8.28116L15.7524 8.24989L15.2519 7.74952L12.2489 4.74732L11.7171 4.21568L13.3469 2.58637C14.1289 1.80454 15.3958 1.80454 16.1779 2.58637L16.181 2.58949ZM9.53996 5.57918C9.24592 5.28522 8.77044 5.28522 8.47952 5.57918L5.2857 8.77214C4.99166 9.06611 4.51618 9.06611 4.22526 8.77214C3.93435 8.47818 3.93122 8.00283 4.22526 7.71199L7.41596 4.51903C8.29496 3.64026 9.72139 3.64026 10.6004 4.51903L11.0102 4.92871L11.542 5.46035L14.545 8.46254L15.0455 8.96291L15.0768 8.99418L14.545 9.52582L9.18023 14.886C7.67872 16.3871 5.7643 17.4128 3.68097 17.8288L2.89893 17.9851C2.65181 18.0352 2.39843 17.957 2.22013 17.7787C2.04182 17.6005 1.96675 17.3472 2.01367 17.1001L2.17008 16.3183C2.58612 14.2355 3.61215 12.3216 5.11365 10.8205L9.94975 5.98886L9.53996 5.57918Z" fill="url(#paint0_linear_10232_5981)"></path>
                                                                <defs>
                                                                <linearGradient id="paint0_linear_10232_5981" x1="12.4027" y1="16.0307" x2="6.84878" y2="3.49729" gradientUnits="userSpaceOnUse">
                                                                <stop stop-color="#141414"></stop>
                                                                <stop offset="1" stop-color="#141414"></stop>
                                                                </linearGradient>
                                                                </defs>
                                                            </svg>
                                                        </span>
                                                        
                                                        <p>{{ __('Keep Writing')}}</p>
                                                    </a>
                                                    @endif
                                                    <a href="javascript: void(0)" id="{{ $longArticle->id }}" class="flex justify-start items-center gap-1.5 text-14 font-normal text-color-14 dark:text-white font-Figtree px-4 py-2 hover:bg-color-F6 dark:hover:bg-[#3A3A39] rounded-t-none rounded-b-lg  article-modal-toggle text-left">
                                                        <span class="w-4 h-3">
                                                            <svg class="w-3 h-3" width="11" height="12" viewBox="0 0 11 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path d="M0.846154 0.8C0.378836 0.8 0 1.15817 0 1.6V2.4C0 2.84183 0.378836 3.2 0.846154 3.2H1.26923V10.4C1.26923 11.2837 2.0269 12 2.96154 12H8.03846C8.9731 12 9.73077 11.2837 9.73077 10.4V3.2H10.1538C10.6212 3.2 11 2.84183 11 2.4V1.6C11 1.15817 10.6212 0.8 10.1538 0.8H7.19231C7.19231 0.358172 6.81347 0 6.34615 0H4.65385C4.18653 0 3.80769 0.358172 3.80769 0.8H0.846154ZM3.38462 4C3.61827 4 3.80769 4.17909 3.80769 4.4V10C3.80769 10.2209 3.61827 10.4 3.38462 10.4C3.15096 10.4 2.96154 10.2209 2.96154 10L2.96154 4.4C2.96154 4.17909 3.15096 4 3.38462 4ZM5.5 4C5.73366 4 5.92308 4.17909 5.92308 4.4V10C5.92308 10.2209 5.73366 10.4 5.5 10.4C5.26634 10.4 5.07692 10.2209 5.07692 10V4.4C5.07692 4.17909 5.26634 4 5.5 4ZM8.03846 4.4V10C8.03846 10.2209 7.84904 10.4 7.61538 10.4C7.38173 10.4 7.19231 10.2209 7.19231 10V4.4C7.19231 4.17909 7.38173 4 7.61538 4C7.84904 4 8.03846 4.17909 8.03846 4.4Z" fill="currentColor"/>
                                                            </svg>
                                                        </span>
                                                        
                                                        <p>{{ __('Remove from History')}}</p>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr class="empty-row">
                                    <td colspan="6">
                                        <svg class="mx-auto mt-10" xmlns="http://www.w3.org/2000/svg" width="44" height="44" viewBox="0 0 44 44" fill="none">
                                            <g clip-path="url(#clip0_2698_2638)">
                                            <path d="M38.6467 13.4583H5.35361C4.07374 13.4583 3.03613 14.4958 3.03613 15.7757V30.9319H40.9641V15.7757C40.9641 14.4959 39.9265 13.4583 38.6467 13.4583Z" fill="#FF9A00"/>
                                            <path d="M8.8972 0C7.26421 0 5.94043 1.32378 5.94043 2.95677V41.0432C5.94043 42.6762 7.26421 44 8.8972 44H35.1026C36.7356 44 38.0594 42.6762 38.0594 41.0432V9.11745C38.0594 8.52337 37.8234 7.95369 37.4034 7.53354L30.5258 0.655961C30.1057 0.235984 29.5359 0 28.9419 0L8.8972 0Z" fill="#F5F5F5"/>
                                            <path d="M37.4035 7.53367L32.8447 2.97485L32.8284 10.622C32.8274 11.102 33.2163 11.4918 33.6963 11.4918C34.1757 11.4918 34.5642 11.8804 34.5642 12.3597V41.0434C34.5642 42.6763 33.2404 44.0001 31.6074 44.0001H35.1027C36.7357 44.0001 38.0594 42.6763 38.0594 41.0434V12.1156V9.11749C38.0596 8.52341 37.8236 7.95373 37.4035 7.53367Z" fill="#EAEAEA"/>
                                            <path d="M37.4033 7.5335L30.5257 0.655926C30.3342 0.464457 30.1114 0.311746 29.8696 0.20166V5.23279C29.8696 6.86577 31.1934 8.18955 32.8264 8.18955H37.8575C37.7475 7.94772 37.5948 7.72497 37.4033 7.5335Z" fill="#A8D0D5"/>
                                            <path d="M16.3243 21.9575L15.7131 20.5742C15.3422 19.7349 14.511 19.1934 13.5934 19.1934H5.35361C4.07374 19.1934 3.03613 20.2309 3.03613 21.5108V41.6824C3.03613 42.9623 4.07366 43.9999 5.35361 43.9999H38.6467C39.9265 43.9999 40.9641 42.9624 40.9641 41.6824V25.6559C40.9641 24.376 39.9266 23.3384 38.6467 23.3384H18.4441C17.5264 23.3384 16.6952 22.7969 16.3243 21.9575Z" fill="#FFB541"/>
                                            <path d="M12.187 20.5742L12.7982 21.9575C13.1691 22.7968 14.0003 23.3383 14.918 23.3383H18.444C17.5263 23.3382 16.6952 22.7968 16.3243 21.9575L15.7131 20.5742C15.3422 19.7349 14.511 19.1934 13.5934 19.1934H10.0674C10.985 19.1934 11.8161 19.7349 12.187 20.5742Z" fill="#FFA812"/>
                                            <path d="M38.6468 23.3384H35.1209C36.4006 23.3385 37.4381 24.376 37.4381 25.6559V41.6825C37.4381 42.9624 36.4006 44 35.1206 44H38.6467C39.9266 44 40.9642 42.9625 40.9642 41.6825V25.6558C40.9643 24.3759 39.9267 23.3384 38.6468 23.3384Z" fill="#FFA812"/>
                                            <path d="M16.6176 4.86731H10.0499C9.68309 4.86731 9.38574 4.56997 9.38574 4.20319C9.38574 3.83641 9.68309 3.53906 10.0499 3.53906H16.6176C16.9844 3.53906 17.2818 3.83641 17.2818 4.20319C17.2818 4.56997 16.9845 4.86731 16.6176 4.86731Z" fill="#3693BD"/>
                                            <path d="M16.6176 8.86121H10.0499C9.68309 8.86121 9.38574 8.56387 9.38574 8.19708C9.38574 7.8303 9.68309 7.53296 10.0499 7.53296H16.6176C16.9844 7.53296 17.2818 7.8303 17.2818 8.19708C17.2818 8.56387 16.9845 8.86121 16.6176 8.86121Z" fill="#3693BD"/>
                                            </g>
                                            <defs>
                                            <clipPath id="clip0_2698_2638">
                                            <rect width="44" height="44" fill="white"/>
                                            </clipPath>
                                            </defs>
                                        </svg>
                                        <p class="text-color-14 dark:text-white text-center font-medium font-Figtree text-20 mt-6">{{ __('No article found')}}</p>
                                        <p class="text-center font-medium text-color-89 text-15 px-5 font-Figtree mt-3 md:w-[450px] mx-auto">{{ __('Looks like you did not generate any article yet.')}}</p>
                                        <a
                                        class="magic-bg w-max rounded-xl text-16 text-white font-semibold py-3 px-6 mx-auto flex text-center my-10 cursor-pointer" href="{{ route('user.long_article.create') }}">
                                            <span>
                                                {{ __('Generate Article') }}
                                            </span>
                                        </a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        {{-- pagination --}}
        {{ $longArticles->onEachSide(1)->links('site.layout.partials.pagination') }}
    </div>
</div>
{{-- modal --}}
<div class="index-modal modal absolute z-50 top-0 left-0 right-0 w-full h-full">
    <div class="modal-overlay fixed z-10 top-0 right-0 left-0 w-full h-full">
    </div>
    <div class="modal-wrapper modal-wrapper modal-transition fixed inset-0 z-10">
        <div class="modal-body flex h-full justify-center p-4 text-center items-center sm:p-0">
            <div class="modal-content modal-transition rounded-xl py-6 md:px-[54px] bg-white dark:bg-color-3A px-8">
                <p class="font-Figtree text-color-14 dark:text-white text-16 font-medium text-center">
                    {{ __('Are you sure you want to delete this article?') }}</p>
                <div class="flex justify-center items-center mt-7 gap-[16px]">
                    <a href="javascript: void(0)"
                        class="font-Figtree text-color-14 dark:text-white font-semibold text-15 py-[11px] px-[42px] border border-color-89 dark:border-color-47 bg-color-F6 dark:bg-color-47 rounded-xl article-modal-toggle">
                        {{ __('Cancel') }}</a>
                    <a href="javascript: void(0)" class="font-Figtree text-white font-semibold text-15 py-[11px] px-[30px] article-modal-toggle bg-color-DFF rounded-xl delete-article">
                        {{ __('Yes, Delete') }} </a>
                </div>
            </div>
        </div>
    </div>
</div>
{{-- end main content --}}
@endsection
@section('js')
<script>
    let noArticleFoundText = '{{ __("No article found.")}}';
    let articleHelpText = '{{ __("Looks like you did not generate any article yet.")}}';
    let articleGenerateUrl = '{{ route("user.long_article.create") }}';
    let articleGenerateButtonText = '{{ __("Generate Article") }}';
    let userId = {{ auth()->id() }};
</script>

<script src="{{ asset('Modules/OpenAI/Resources/assets/js/customer/long_article/list.min.js') }}"></script>
@endsection
@extends('layouts.user_master')
@section('page_title', __('Folders'))

@section('content')
    @php
        $currentUrl = url()->current();
        $url = str_contains($currentUrl, '/user/folder/');
        $name = '';
        $parentId = '';
        $breadCrumbs = [];

        if ($url) {
            $name = explode('/user/folder/', $currentUrl)[1];
            $breadCrumbs = Modules\OpenAI\Services\FolderService::getBreadcrumbTrail($name);
            $parentId = Modules\OpenAI\Services\FolderService::getFolderIdBySlug($name);
        }
    @endphp
<div class="dark:bg-[#292929] bg-[#F6F3F2] sidebar-scrollbar overflow-auto flex flex-col flex-1 font-Figtree border-l dark:border-[#474746] border-color-DF h-screen relative drive-sidebar">
    <div class="mt-1.5 border-b border-color-DF dark:border-[#474746] dark:bg-[#333332] bg-white 9xl:px-[185px] 7xl:px-[140px] px-5 pt-[66px] lg:pb-[15px] pb-[19px]">
        <div class="flex lg:flex-row flex-col justify-between lg:items-center gap-4 lg:gap-[60px]">
            <div class="flex justify-between items-center w-full">
                <p class="text-color-14 dark:text-white text-24 font-RedHat font-semibold mt-[3px]">{{ __('Your Drive') }}</p>
                <div class="flex sopbox hidden">
                    <div class="flex gap-[18px] items-center mt-[11px]">
                        <p class="text-color-14 dark:text-white text-15 font-Figtree font-medium counts" id="selectedCount">
                            {{-- selected coundt show here --}}
                        </p>
                        <div class="flex gap-6 text-color-47 dark:text-white">
                            {{-- Multiple Move --}}
                            <a href="javascript:void(0)" data-parent-id="{{ $parentId }}" id="multiple-drive-modal" class="modal-trigger tooltip-bottom" data-target="move-folder-mod" data-effect="mfp-zoom-in" data-tooltip="Move">
                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M13.332 11.9993H2.66536V5.33268H13.332V11.9993ZM7.9987 3.99935L6.66536 2.66602H2.66536C2.31174 2.66602 1.9726 2.80649 1.72256 3.05654C1.47251 3.30659 1.33203 3.64573 1.33203 3.99935V11.9993C1.33203 12.353 1.47251 12.6921 1.72256 12.9422C1.9726 13.1922 2.31174 13.3327 2.66536 13.3327H13.332C14.072 13.3327 14.6654 12.7393 14.6654 11.9993V5.33268C14.6654 4.97906 14.5249 4.63992 14.2748 4.38987C14.0248 4.13982 13.6857 3.99935 13.332 3.99935H7.9987ZM7.33203 9.33268V7.99935H9.9987V5.99935L12.6654 8.66602L9.9987 11.3327V9.33268H7.33203Z" fill="currentColor"/>
                                </svg>
                            </a>
                            {{-- Multiple delete --}}
                            <a href="javascript:void(0)" data-parent-id="{{ $parentId }}" class="modal-trigger tooltip-bottom" data-target="delete-popup" data-effect="mfp-zoom-in" data-tooltip="Delete">
                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M7.43967 0.666993H8.55773C8.90916 0.666981 9.21233 0.666971 9.46205 0.687373C9.72561 0.708907 9.98776 0.756451 10.24 0.88498C10.6163 1.07673 10.9223 1.38269 11.114 1.75901C11.2426 2.01127 11.2901 2.27341 11.3117 2.53697C11.3299 2.76083 11.3318 3.02764 11.332 3.33366H13.9987C14.3669 3.33366 14.6654 3.63214 14.6654 4.00033C14.6654 4.36852 14.3669 4.66699 13.9987 4.66699H13.332V11.4945C13.332 12.0312 13.332 12.4741 13.3026 12.8349C13.272 13.2096 13.2062 13.5541 13.0414 13.8776C12.7857 14.3794 12.3778 14.7873 11.876 15.043C11.5524 15.2079 11.208 15.2736 10.8332 15.3042C10.4725 15.3337 10.0296 15.3337 9.49291 15.3337H6.50448C5.96784 15.3337 5.52494 15.3337 5.16415 15.3042C4.78942 15.2736 4.44495 15.2079 4.12139 15.043C3.61962 14.7873 3.21168 14.3794 2.95601 13.8776C2.79115 13.5541 2.72544 13.2096 2.69483 12.8349C2.66535 12.4741 2.66536 12.0312 2.66536 11.4945L2.66536 4.66699H1.9987C1.63051 4.66699 1.33203 4.36852 1.33203 4.00033C1.33203 3.63214 1.63051 3.33366 1.9987 3.33366H4.66538C4.66557 3.02764 4.66745 2.76083 4.68574 2.53697C4.70728 2.27341 4.75482 2.01127 4.88335 1.75901C5.0751 1.38269 5.38106 1.07673 5.75738 0.88498C6.00964 0.756451 6.27178 0.708907 6.53535 0.687373C6.78506 0.666971 7.08824 0.666981 7.43967 0.666993ZM3.9987 4.66699V11.467C3.9987 12.038 3.99922 12.4262 4.02373 12.7263C4.04761 13.0186 4.0909 13.168 4.14402 13.2723C4.27185 13.5232 4.47583 13.7272 4.72671 13.855C4.83098 13.9081 4.98045 13.9514 5.27272 13.9753C5.57278 13.9998 5.96098 14.0003 6.53203 14.0003H9.46536C10.0364 14.0003 10.4246 13.9998 10.7247 13.9753C11.0169 13.9514 11.1664 13.9081 11.2707 13.855C11.5216 13.7272 11.7255 13.5232 11.8534 13.2723C11.9065 13.168 11.9498 13.0186 11.9737 12.7263C11.9982 12.4262 11.9987 12.038 11.9987 11.467V4.66699H3.9987ZM9.99865 3.33366H5.99875C5.99904 3.02359 6.00108 2.81165 6.01465 2.64555C6.02945 2.46444 6.05456 2.3973 6.07136 2.36433C6.13528 2.23889 6.23726 2.1369 6.3627 2.07299C6.39567 2.05619 6.46281 2.03108 6.64392 2.01628C6.83281 2.00085 7.081 2.00033 7.46536 2.00033H8.53203C8.9164 2.00033 9.16458 2.00085 9.35347 2.01628C9.53458 2.03108 9.60173 2.05619 9.63469 2.07299C9.76013 2.1369 9.86212 2.23889 9.92603 2.36433C9.94283 2.3973 9.96795 2.46444 9.98275 2.64555C9.99632 2.81165 9.99835 3.02359 9.99865 3.33366ZM6.66536 7.00033C7.03355 7.00033 7.33203 7.2988 7.33203 7.66699V11.0003C7.33203 11.3685 7.03355 11.667 6.66536 11.667C6.29717 11.667 5.9987 11.3685 5.9987 11.0003V7.66699C5.9987 7.2988 6.29717 7.00033 6.66536 7.00033ZM9.33203 7.00033C9.70022 7.00033 9.9987 7.2988 9.9987 7.66699V11.0003C9.9987 11.3685 9.70022 11.667 9.33203 11.667C8.96384 11.667 8.66536 11.3685 8.66536 11.0003V7.66699C8.66536 7.2988 8.96384 7.00033 9.33203 7.00033Z" fill="currentColor"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="flex justify-center sm:justify-end search-input">
                <button class="z-10 ltr:-mr-10 rtl:-ml-10 py-1.5 ltr:pr-2 ltr:pl-3 rtl:pl-2 rtl:pr-3">
                    <svg class="text-color-14 dark:text-white" width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <g clip-path="url(#clip0_11638_6143)">
                            <g clip-path="url(#clip1_11638_6143)">
                                <path d="M18.9221 17.9523L14.8944 13.7633C15.93 12.5322 16.4974 10.9833 16.4974 9.37077C16.4974 5.60329 13.4322 2.53809 9.66472 2.53809C5.89723 2.53809 2.83203 5.60329 2.83203 9.37077C2.83203 13.1383 5.89723 16.2035 9.66472 16.2035C11.0791 16.2035 12.4269 15.7769 13.5792 14.967L17.6376 19.1879C17.8072 19.364 18.0353 19.4612 18.2798 19.4612C18.5113 19.4612 18.7308 19.3729 18.8975 19.2125C19.2516 18.8718 19.2629 18.3067 18.9221 17.9523ZM9.66472 4.32053C12.4495 4.32053 14.715 6.58601 14.715 9.37077C14.715 12.1555 12.4495 14.421 9.66472 14.421C6.87995 14.421 4.61447 12.1555 4.61447 9.37077C4.61447 6.58601 6.87995 4.32053 9.66472 4.32053Z" fill="currentColor"/>
                            </g>
                        </g>
                        <defs>
                            <clipPath id="clip0_11638_6143">
                                <rect width="22" height="22" fill="white"/>
                            </clipPath>
                            <clipPath id="clip1_11638_6143">
                                <rect width="16.9231" height="16.9231" fill="white" transform="translate(2.53906 2.53809)"/>
                            </clipPath>
                        </defs>
                    </svg>
                </button>
                <input class="w-full sm:w-[340px] bg-white dark:bg-[#333332] py-[10px] dark:text-white placeholder:text-color-89 rounded-lg text-14 font-normal ltr:pl-[42px] rtl:pr-[42px] ltr:pr-3 rtl:pl-3 border border-color-DF dark:border-color-47 font-Figtree folder-search-input" type="text" placeholder="Search character">
            </div>
        </div>
    </div>

    <div class="9xl:px-[185px] 7xl:px-[140px] px-5 flex justify-between items-center mt-6 pb-4 breadcrumb-box">
        <!-- Breadcrumb -->
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-2">
                @php 
                    $totalBreadCrumbs = count($breadCrumbs); 
                @endphp
                @foreach ($breadCrumbs as $key => $breadCrumb)
                    @if ($loop->first) 
                        <li class="inline-flex items-center">
                            <a href="{{ $totalBreadCrumbs == 1 ? 'javascript:void(0)' : $breadCrumb['view'] }}"  class="inline-flex gap-2 items-center text-sm font-medium text-gray-700 hover:text-blue-600 dark:text-gray-400 dark:hover:text-white">
                                <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M8.59851 2.625H12.0828C12.3259 2.62497 12.5665 2.67563 12.789 2.77374C13.0115 2.87185 13.2111 3.01526 13.3751 3.19482C13.5391 3.37438 13.6639 3.58614 13.7415 3.81661C13.8191 4.04708 13.8478 4.29119 13.8258 4.53338L13.2684 10.6584C13.2289 11.0932 13.0283 11.4975 12.7059 11.792C12.3836 12.0865 11.9629 12.2498 11.5263 12.25H2.47089C2.0343 12.2498 1.61354 12.0865 1.29121 11.792C0.968884 11.4975 0.768274 11.0932 0.728762 10.6584L0.171387 4.53338C0.1341 4.12806 0.239982 3.72249 0.470637 3.38713L0.436512 2.625C0.436512 2.16087 0.620886 1.71575 0.949075 1.38756C1.27726 1.05937 1.72238 0.875 2.18651 0.875H5.39951C5.8636 0.875099 6.30865 1.05954 6.63676 1.38775L7.36126 2.11225C7.68937 2.44046 8.13442 2.6249 8.59851 2.625ZM1.31676 2.73C1.50401 2.66175 1.70526 2.625 1.91526 2.625H6.63676L6.01814 2.00637C5.85408 1.84227 5.63156 1.75005 5.39951 1.75H2.18651C1.9573 1.74996 1.73722 1.83986 1.5736 2.00038C1.40997 2.16089 1.31587 2.3792 1.31151 2.60837L1.31676 2.73Z" fill="#898989"/>
                                </svg>

                                <span class="text-color-47 dark:text-white text-15 font-Figtree font-normal">{{ ucfirst($breadCrumb['name']) }}</span>
                            </a>
                        </li>
                    @endif
                    @if ($key == 1 && $totalBreadCrumbs > 2)
                        <li>
                            <div class="flex items-center gap-1 relative">
                                <svg class="text-color-47 dark:text-color-DF" width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M4.18306 9.83263C4.42714 10.0558 4.82286 10.0558 5.06694 9.83263L8.81694 6.40406C9.06102 6.1809 9.06102 5.8191 8.81694 5.59594L5.06694 2.16737C4.82286 1.94421 4.42714 1.94421 4.18306 2.16737C3.93898 2.39052 3.93898 2.75233 4.18306 2.97549L7.49112 6L4.18306 9.02451C3.93898 9.24767 3.93898 9.60948 4.18306 9.83263Z" fill="currentColor"/>
                                </svg>
                                <a href="{{ $breadCrumb['view'] }}" class="text-color-47 dark:text-white text-15 font-Figtree font-normal">
                                    <a href="javascript:void(0)" class="table-dropdown-click relative font-Figtree text-15 font-normal text-color-47 dark:text-color-DF">.........</a>
                                    <div class="absolute ltr:left-0 rtl:right-0 action-dropdown top-4 mt-2 w-[201px] border border-color-89 dark:border-color-47 rounded-lg bg-white dark:bg-[#333332] z-50 table-drop-body dropdown-shadow">
                                        <div class="my-2 flex flex-col gap-1.5">
                                            @foreach($breadCrumbs as $k => $sub)
                                                @if (!$loop->first && !$loop->last)
                                                    <a href="{{ $sub['view'] }}" class="flex justify-start items-center gap-2 text-14 font-normal text-color-14 dark:text-white font-Figtree px-4 py-[9px] hover:bg-color-F6 dark:hover:bg-[#3A3A39] text-left">
                                                        <span class="w-4 h-4 text-color-47 dark:text-white">
                                                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path d="M9.82687 3H13.8089C14.0868 2.99997 14.3617 3.05787 14.616 3.16999C14.8703 3.28212 15.0984 3.44601 15.2858 3.65122C15.4733 3.85643 15.6159 4.09845 15.7045 4.36184C15.7932 4.62524 15.826 4.90422 15.8009 5.181L15.1639 12.181C15.1187 12.6779 14.8894 13.14 14.5211 13.4766C14.1527 13.8131 13.6718 13.9998 13.1729 14H2.82387C2.32491 13.9998 1.84404 13.8131 1.47567 13.4766C1.1073 13.14 0.878027 12.6779 0.832871 12.181L0.195871 5.181C0.153257 4.71778 0.274265 4.25427 0.537871 3.871L0.498871 3C0.498871 2.46957 0.709584 1.96086 1.08466 1.58579C1.45973 1.21071 1.96844 1 2.49887 1H6.17087C6.70126 1.00011 7.20989 1.2109 7.58487 1.586L8.41287 2.414C8.78786 2.7891 9.29648 2.99989 9.82687 3ZM1.50487 3.12C1.71887 3.042 1.94887 3 2.18887 3H7.58487L6.87787 2.293C6.69038 2.10545 6.43607 2.00006 6.17087 2H2.49887C2.23691 1.99995 1.9854 2.1027 1.7984 2.28614C1.6114 2.46959 1.50385 2.71909 1.49887 2.981L1.50487 3.12Z" fill="currentColor"/>
                                                            </svg>
                                                        </span>
                                                        <p>{{ $sub['name'] }}</p>
                                                    </a>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </li>
                    @endif
                    @if ($loop->count != 1 && $loop->last) 
                        <li>
                            <div class="flex items-center gap-2">
                                <svg class="text-color-47 dark:text-color-DF" width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M4.18306 9.83263C4.42714 10.0558 4.82286 10.0558 5.06694 9.83263L8.81694 6.40406C9.06102 6.1809 9.06102 5.8191 8.81694 5.59594L5.06694 2.16737C4.82286 1.94421 4.42714 1.94421 4.18306 2.16737C3.93898 2.39052 3.93898 2.75233 4.18306 2.97549L7.49112 6L4.18306 9.02451C3.93898 9.24767 3.93898 9.60948 4.18306 9.83263Z" fill="currentColor"/>
                                </svg>
                                    
                                <a href="javascript:void(0)" class="text-color-14 dark:text-white text-15 font-Figtree font-medium">
                                    {{ $breadCrumb['name'] }}
                                </a>
                            </div>
                        </li>
                    @endif
                @endforeach
            </ol>
        </nav>
        <div>
            <div class="flex gap-7 relative justify-center items-center">
                <a class="mark-hover" href="javascript:void(0)">
                    <input type="checkbox" id="toggleView" class="hidden toggleView">
                    <label class="cursor-pointer" for="toggleView">
                        <div class="list-up flex items-center gap-2 text-color-47 dark:text-white text-15 font-Figtree font-normal tooltips">
                            <svg class="text-color-89 dark:text-white" width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M1.00203 3.35288H1.00425C1.0043 3.46338 1.04794 3.56939 1.12571 3.64789C1.20347 3.72638 1.30907 3.77102 1.41955 3.77209L12.5803 3.77246C12.6912 3.77197 12.7974 3.72739 12.8757 3.6488C12.9539 3.5702 12.9979 3.46381 12.998 3.35288H12.9998V1.41884C12.9996 1.30769 12.9553 1.20116 12.8767 1.12263C12.798 1.0441 12.6914 1 12.5803 1H1.41955C1.30829 1.00005 1.20161 1.04427 1.12294 1.12295C1.04427 1.20162 1.00005 1.30831 1 1.41958C1 1.42679 1.00185 1.43363 1.00222 1.44084L1.00203 3.35288ZM12.5801 5.61386H1.41955C1.30829 5.61391 1.20161 5.65813 1.12294 5.73681C1.04427 5.81548 1.00005 5.92218 1 6.03344C1 6.04065 1.00185 6.04749 1.00222 6.05471V7.96675H1.00444C1.00448 8.07724 1.04813 8.18326 1.12589 8.26175C1.20365 8.34025 1.30925 8.38488 1.41974 8.38595L12.5805 8.38632C12.6914 8.38583 12.7976 8.34125 12.8759 8.26266C12.9541 8.18407 12.9981 8.07767 12.9982 7.96675H13V6.0327C12.9997 5.92153 12.9553 5.81503 12.8766 5.73652C12.7979 5.65802 12.6912 5.61391 12.5801 5.61386ZM12.5801 10.2275H1.41955C1.30829 10.2276 1.20161 10.2718 1.12294 10.3505C1.04427 10.4292 1.00005 10.5359 1 10.6471C1 10.6543 1.00185 10.6612 1.00222 10.6684V12.5804H1.00444C1.00448 12.6909 1.04813 12.7969 1.12589 12.8754C1.20365 12.9539 1.30925 12.9986 1.41974 12.9996L12.5805 13C12.6914 12.9995 12.7976 12.9548 12.8759 12.8762C12.9541 12.7976 12.9981 12.6912 12.9982 12.5802H13V10.6462C12.9996 10.5351 12.9552 10.4286 12.8765 10.3501C12.7978 10.2717 12.6912 10.2276 12.5801 10.2275Z" fill="currentColor"/>
                            </svg>
                            <span class="hidden lg:block w-[67px]">{{ __('List View') }}</span>
                        </div>
                        <div class="grid-down hidden flex items-center gap-2 text-color-47 dark:text-white text-15 font-Figtree font-normal tooltips">
                            <svg class="text-color-89 dark:text-white" width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M1.66667 6.33333H5.66667C5.84348 6.33333 6.01305 6.2631 6.13807 6.13807C6.2631 6.01305 6.33333 5.84348 6.33333 5.66667V1.66667C6.33333 1.48986 6.2631 1.32029 6.13807 1.19526C6.01305 1.07024 5.84348 1 5.66667 1H1.66667C1.48986 1 1.32029 1.07024 1.19526 1.19526C1.07024 1.32029 1 1.48986 1 1.66667V5.66667C1 5.84348 1.07024 6.01305 1.19526 6.13807C1.32029 6.2631 1.48986 6.33333 1.66667 6.33333ZM8.33333 6.33333H12.3333C12.5101 6.33333 12.6797 6.2631 12.8047 6.13807C12.9298 6.01305 13 5.84348 13 5.66667V1.66667C13 1.48986 12.9298 1.32029 12.8047 1.19526C12.6797 1.07024 12.5101 1 12.3333 1H8.33333C8.15652 1 7.98695 1.07024 7.86193 1.19526C7.73691 1.32029 7.66667 1.48986 7.66667 1.66667V5.66667C7.66667 5.84348 7.73691 6.01305 7.86193 6.13807C7.98695 6.2631 8.15652 6.33333 8.33333 6.33333ZM1.66667 13H5.66667C5.84348 13 6.01305 12.9298 6.13807 12.8047C6.2631 12.6797 6.33333 12.5101 6.33333 12.3333V8.33333C6.33333 8.15652 6.2631 7.98695 6.13807 7.86193C6.01305 7.73691 5.84348 7.66667 5.66667 7.66667H1.66667C1.48986 7.66667 1.32029 7.73691 1.19526 7.86193C1.07024 7.98695 1 8.15652 1 8.33333V12.3333C1 12.5101 1.07024 12.6797 1.19526 12.8047C1.32029 12.9298 1.48986 13 1.66667 13ZM8.33333 13H12.3333C12.5101 13 12.6797 12.9298 12.8047 12.8047C12.9298 12.6797 13 12.5101 13 12.3333V8.33333C13 8.15652 12.9298 7.98695 12.8047 7.86193C12.6797 7.73691 12.5101 7.66667 12.3333 7.66667H8.33333C8.15652 7.66667 7.98695 7.73691 7.86193 7.86193C7.73691 7.98695 7.66667 8.15652 7.66667 8.33333V12.3333C7.66667 12.5101 7.73691 12.6797 7.86193 12.8047C7.98695 12.9298 8.15652 13 8.33333 13Z" fill="currentColor"/>
                            </svg>
                            <span class="hidden lg:block w-[67px]">{{ __('Grid View') }}</span>
                        </div>
                    </label>
                </a>
                <a href="javascript:void(0)" class="flex mark-hover items-center gap-2 text-color-47 dark:text-white text-15 font-Figtree font-normal tooltips" id="bookmark-file-filter" data-text="bookmark">
                    <svg class="text-color-89 dark:text-white" width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M1.75 1.75V13.5625C1.75 13.7169 1.83139 13.8599 1.96417 13.9387C2.09695 14.0175 2.26144 14.0205 2.397 13.9466L7 11.4359L11.603 13.9466C11.7386 14.0205 11.9031 14.0175 12.0358 13.9387C12.1686 13.8599 12.25 13.7169 12.25 13.5625V1.75C12.25 0.783502 11.4665 0 10.5 0H3.5C2.5335 0 1.75 0.783502 1.75 1.75Z" fill="currentColor"/>
                    </svg>
                    <span class="hidden lg:block">{{ __('Bookmarks')}}</span>
                </a>
                <a href="javascript:void(0)" data-target="create-folder" data-effect="mfp-zoom-in" class="modal-trigger flex mark-hover items-center gap-2 text-color-47 dark:text-white text-15 font-Figtree font-normal tooltips">
                    <svg class="text-color-89 dark:text-white" width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M7.33333 11.2632C7.33333 11.4958 7.36 11.7216 7.39333 11.9474H1.33333C0.979711 11.9474 0.640573 11.8032 0.390524 11.5466C0.140476 11.2899 0 10.9419 0 10.5789V2.36842C0 1.60895 0.593333 1 1.33333 1H5.33333L6.66667 2.36842H12C12.3536 2.36842 12.6928 2.51259 12.9428 2.76922C13.1929 3.02585 13.3333 3.37391 13.3333 3.73684V7.71211C12.7467 7.36316 12.0667 7.1579 11.3333 7.1579C9.12667 7.1579 7.33333 8.99842 7.33333 11.2632ZM12 10.5789V8.52632H10.6667V10.5789H8.66667V11.9474H10.6667V14H12V11.9474H14V10.5789H12Z" fill="currentColor"/>
                    </svg>                            
                    <span class="hidden lg:block">{{ __('Add New Folder') }}</span>
                </a>
            </div>
        </div>
    </div>

    @php
        $folderCount = 0;
        $filesCount = 0;
        foreach ($folders as $folder) {
            if ($folder->type == 'folder') {
                $folderCount++;
            } else {
                $filesCount++;
            }
        }
    @endphp

    <div class="9xl:px-[185px] 7xl:px-[140px] px-5 7xl:pb-[22px] lg:pb-36 2xl:pb-28 pb-28 parent-table">
        @if ($folderCount != 0)
            <p class="text-15 font-medium text-color-14 dark:text-white mb-2.5 folder-tittle hidden">{{ __('Folders') }}</p>
        @endif
        <div id="documents-table-body" class="rounded-xl {{ count($folders) != 0 ? '' : 'hidden' }}">
            <div class="container-box relative grid grid-cols-1 bg-white dark:bg-color-3A rounded-xl [&>*:last-child]:border-b-white dark:[&>*:last-child]:border-b-color-3A [&>*:last-child]:rounded-b-xl">
                {{-- table head --}}
                <div class="cursor-pointer py-[9px] px-2.5 mx-1.5 mt-1.5 sm:px-[18px] bg-color-DF dark:bg-color-47 rounded-lg check-data drive-table-head">
                    <a href="javascript:void(0)">
                        <div class="flex gap-4 sm:gap-px items-center">
                            <div class="relative flex items-center gap-3 w-[175px] min-[990px]:w-[348px] xl:w-[400px] 5xl:w-[492px] min-[1730px]:w-[615px]">
                                <p class="text-color-14 dark:text-white text-14 font-Figtree font-medium">
                                    {{ __('Name') }}
                                </p>
                            </div>
                            <div class="check-data flex-1 hidden xl:inline-flex media-stash">
                                <span class="text-color-14 dark:text-white text-14 font-Figtree font-medium">{{ __('Bookmark') }}</span>
                            </div>
                            <div class="check-data flex-1 hidden xl:inline-flex media-stash">
                                <span class="text-color-14 dark:text-white text-14 font-Figtree font-medium">{{ __('Items') }}</span>
                            </div>
                            <div class="check-data inline-flex w-[77px] sm:w-[100px] 2xl:w-[207px]">
                                <span class="text-color-14 dark:text-white text-14 font-Figtree font-medium">{{ __('Creator') }}</span>
                            </div>
                            <div class="flex-1 hidden sm:inline-flex modified">
                                <span class="text-color-14 dark:text-white text-14 font-Figtree font-medium">{{ __('Modified') }}</span>
                            </div>
                            <div class="check-data w-[67px] max-sm:flex-1 flex justify-end">
                                <span class="text-color-14 dark:text-white text-14 font-Figtree font-medium">{{ __('Actions') }}</span>
                            </div>
                        </div>
                    </a>
                </div>
                @foreach ($folders as $folder)
                    @php
                        if (isset($folder) && isset($userFavoriteFiles[$folder->type])) {
                            $ids = explode(',', $userFavoriteFiles[$folder->type]);
                        } else {
                            $ids = [];
                        }
                    @endphp
                    {{-- folder view --}}
                    @if($folder->type == 'folder')
                        <div id="{{ $folder->type . "-" . $folder->id }}" class="{{ in_array($folder->id, $ids) ? 'favorite' : 'non-favorite' }} search-folder folder-item relative view-mode list-view view-box cutom-border-active bg-white dark:bg-color-3A cursor-pointer py-[15px] sm:pt-6 sm:pb-[23px] px-4 sm:px-6" type="folder">
                            <a href="{{ route('user.folderView', $folder->slug) }}" onclick="return false;">
                                <div class="flex gap-4 sm:gap-px sm:items-center content-parent">
                                    <div class="flex sm:items-center gap-3 w-[175px] min-[990px]:w-[348px] xl:w-[400px] 5xl:w-[492px] min-[1730px]:w-[615px] xl:pr-[65px] file-name">
                                        <div>
                                            <svg class=" text-[#FF774B]" width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M11.0552 3.375H15.535C15.8476 3.37497 16.1569 3.4401 16.443 3.56624C16.729 3.69238 16.9857 3.87676 17.1966 4.10762C17.4074 4.33848 17.5678 4.61076 17.6676 4.90707C17.7674 5.20339 17.8043 5.51725 17.776 5.82863L17.0594 13.7036C17.0086 14.2627 16.7506 14.7825 16.3362 15.1611C15.9218 15.5397 15.3808 15.7498 14.8195 15.75H3.17685C2.61552 15.7498 2.07455 15.5397 1.66013 15.1611C1.24571 14.7825 0.98778 14.2627 0.93698 13.7036L0.220355 5.82863C0.172414 5.30751 0.308548 4.78606 0.605105 4.35488L0.56123 3.375C0.56123 2.77826 0.798283 2.20597 1.22024 1.78401C1.6422 1.36205 2.21449 1.125 2.81123 1.125H6.94223C7.53892 1.12513 8.11112 1.36226 8.53298 1.78425L9.46448 2.71575C9.88634 3.13774 10.4585 3.37487 11.0552 3.375ZM1.69298 3.51C1.93373 3.42225 2.19248 3.375 2.46248 3.375H8.53298L7.73761 2.57963C7.52668 2.36863 7.24057 2.25006 6.94223 2.25H2.81123C2.51653 2.24995 2.23357 2.36553 2.0232 2.57191C1.81282 2.77829 1.69183 3.05898 1.68623 3.35362L1.69298 3.51Z" fill="currentColor"/>
                                            </svg>
                                        </div>
                                        
                                        <div class="flex flex-col">
                                            <p class="text-color-14 folder-name w-[145px] min-[990px]:w-[300px] 5xl:w-[492px] dark:text-white text-14 font-Figtree font-medium line-clamp-2 sm:line-clamp-1 word-break">
                                                {{ $folder->name }}
                                            </p>
                                            <p class="mt-2 block sm:hidden modified-time text-color-89 font-Figtree text-13 font-medium">{{ !empty($folder->updated_at) ? timeToGo($folder->updated_at, false, 'ago') : timeToGo($folder->created_at, false, 'ago') }}</p>
                                        </div>
                                    </div>
                                    <div class="check-data flex-1 hidden xl:inline-flex bookmark-folder">
                                        <a href="javascript: void(0)" class="inline-block {{ $folder->type }}-bookmark-{{ $folder->id }}" title="Bookmarks"  onclick="fileBookmarkToggle(this)" data-file-type="{{ $folder->type }}" data-file-id="{{ $folder->id }}" data-is-favorite="{{ in_array($folder->id, $ids) ? 'true' : 'false' }}">
                                            @if (in_array($folder->id, $ids))
                                                <svg width="12" height="14" viewBox="0 0 12 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M0.75 1.75V13.5625C0.75 13.7169 0.831395 13.8599 0.964172 13.9387C1.09695 14.0175 1.26144 14.0205 1.397 13.9466L6 11.4359L10.603 13.9466C10.7386 14.0205 10.9031 14.0175 11.0358 13.9387C11.1686 13.8599 11.25 13.7169 11.25 13.5625V1.75C11.25 0.783502 10.4665 0 9.5 0H2.5C1.5335 0 0.75 0.783502 0.75 1.75Z" fill="url(#paint1_linear_140_371_{{ $folder->id }})"/>
                                                    <defs>
                                                    <linearGradient id="paint1_linear_140_371_{{ $folder->id }}" x1="7.5768" y1="12.2769" x2="1.83073" y2="2.55166" gradientUnits="userSpaceOnUse">
                                                    <stop stop-color="#E60C84"/>
                                                    <stop offset="1" stop-color="#FFCF4B"/>
                                                    </linearGradient>
                                                    </defs>
                                                </svg>
                                                    
                                            @else
                                                <svg class="unmarked" width="12" height="14" viewBox="0 0 12 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M0.75 1.75V13.5625C0.75 13.7169 0.831395 13.8599 0.964172 13.9387C1.09695 14.0175 1.26144 14.0205 1.397 13.9466L6 11.4359L10.603 13.9466C10.7386 14.0205 10.9031 14.0175 11.0358 13.9387C11.1686 13.8599 11.25 13.7169 11.25 13.5625V1.75C11.25 0.783502 10.4665 0 9.5 0H2.5C1.5335 0 0.75 0.783502 0.75 1.75Z" fill="#898989"/>
                                                </svg>
                                                
                                            @endif
                                        </a>
                                        <div class="document-spinner"></div>
                                    </div>
                                    <div class="check-data flex-1 hidden xl:inline-flex media-stash">
                                        <span class="text-color-89 font-Figtree text-13 font-medium folder-count">
                                            {{ $folder->item_count ? $folder->item_count : '0'  }}
                                        </span>
                                    </div>
                                    <div class="check-data w-[100px] 2xl:w-[207px] inline-flex">
                                        <span class="text-color-89 font-Figtree text-13 font-medium">
                                            {{ $folder->user?->name ??  '--' }}
                                        </span>
                                    </div>
                                    <div class="flex-1 hidden sm:inline-flex modified">
                                        <span class="text-color-89 font-Figtree text-13 font-medium">
                                            {{ !empty($folder->updated_at) ? timeToGo($folder->updated_at, false, 'ago') : timeToGo($folder->created_at, false, 'ago') }}
                                        </span>
                                    </div>
                                    <div class="w-[67px] flex justify-end max-sm:flex-1">
                                        <div class="relative inline-block">
                                            <button class="table-dropdown-click">
                                                <a href="javascript:void(0)" class="cursor-pointer text-color-14 dark:text-white border p-[7px] border-color-89 dark:border-color-47 dark:bg-color-47 rounded-lg flex justify-end action-dot">
                                                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M9.5 13C9.5 13.8284 8.82843 14.5 8 14.5C7.17157 14.5 6.5 13.8284 6.5 13C6.5 12.1716 7.17157 11.5 8 11.5C8.82843 11.5 9.5 12.1716 9.5 13ZM9.5 8C9.5 8.82843 8.82843 9.5 8 9.5C7.17157 9.5 6.5 8.82843 6.5 8C6.5 7.17157 7.17157 6.5 8 6.5C8.82843 6.5 9.5 7.17157 9.5 8ZM9.5 3C9.5 3.82843 8.82843 4.5 8 4.5C7.17157 4.5 6.5 3.82843 6.5 3C6.5 2.17157 7.17157 1.5 8 1.5C8.82843 1.5 9.5 2.17157 9.5 3Z" fill="currentColor"/>
                                                    </svg>
                                                </a>
                                            </button>
                                            <div class="absolute ltr:right-9 rtl:left-9 action-dropdown -top-2 mt-2 w-[180px] sm:w-[201px] border border-color-89 dark:border-color-47 rounded-lg bg-white dark:bg-[#333332] z-50 table-drop-body dropdown-shadow">
                                                <div class="my-2">
                                                    <a href="javascript:void(0)" data-id="{{ $folder->id }}" onclick="downloadFolder(this)" class="flex justify-start items-center gap-2 text-14 font-normal text-color-14 dark:text-white font-Figtree px-4 py-2 hover:bg-color-F6 dark:hover:bg-[#3A3A39] text-left">
                                                        <span class="w-4 h-4">
                                                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path fill-rule="evenodd" clip-rule="evenodd" d="M8 2C8.36819 2 8.66667 2.29848 8.66667 2.66667V9.05719L10.8619 6.86193C11.1223 6.60158 11.5444 6.60158 11.8047 6.86193C12.0651 7.12228 12.0651 7.54439 11.8047 7.80474L8.4714 11.1381C8.21106 11.3984 7.78895 11.3984 7.5286 11.1381L4.19526 7.80474C3.93491 7.54439 3.93491 7.12228 4.19526 6.86193C4.45561 6.60158 4.87772 6.60158 5.13807 6.86193L7.33333 9.05719V2.66667C7.33333 2.29848 7.63181 2 8 2ZM2.66667 10.6667C3.03486 10.6667 3.33333 10.9651 3.33333 11.3333V12.6667C3.33333 12.8435 3.40357 13.013 3.5286 13.1381C3.65362 13.2631 3.82319 13.3333 4 13.3333H12C12.1768 13.3333 12.3464 13.2631 12.4714 13.1381C12.5964 13.013 12.6667 12.8435 12.6667 12.6667V11.3333C12.6667 10.9651 12.9651 10.6667 13.3333 10.6667C13.7015 10.6667 14 10.9651 14 11.3333V12.6667C14 13.1971 13.7893 13.7058 13.4142 14.0809C13.0391 14.456 12.5304 14.6667 12 14.6667H4C3.46957 14.6667 2.96086 14.456 2.58579 14.0809C2.21071 13.7058 2 13.1971 2 12.6667V11.3333C2 10.9651 2.29848 10.6667 2.66667 10.6667Z" fill="currentColor"/>
                                                            </svg>
                                                        </span>
                                                        <p>{{ __('Download') }}</p>
                                                    </a>
                                                    <a id="{{ $folder->id }}" data-name="{{ $folder->name }}" href="javascript:void(0)" data-target="rename-popup" data-effect="mfp-zoom-in" class="modal-trigger rename-folder flex justify-start items-center gap-2 text-14 font-normal text-color-14 dark:text-white font-Figtree px-4 py-2 hover:bg-color-F6 dark:hover:bg-[#3A3A39] text-left">
                                                        <span class="w-4 h-4">
                                                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path fill-rule="evenodd" clip-rule="evenodd" d="M1.66797 14.0002C1.66797 13.632 1.96645 13.3335 2.33464 13.3335H14.3346C14.7028 13.3335 15.0013 13.632 15.0013 14.0002C15.0013 14.3684 14.7028 14.6668 14.3346 14.6668H2.33464C1.96645 14.6668 1.66797 14.3684 1.66797 14.0002Z" fill="currentColor"/>
                                                                <path fill-rule="evenodd" clip-rule="evenodd" d="M10.5649 1.3335C10.7418 1.33346 10.9114 1.40374 11.0365 1.52886L13.4715 3.96485C13.7317 4.22518 13.7317 4.64714 13.4715 4.90746L6.57717 11.8048C6.45214 11.9299 6.28253 12.0002 6.10567 12.0002H3.66667C3.29848 12.0002 3 11.7017 3 11.3335V8.90683C3 8.73016 3.07013 8.56071 3.19498 8.43571L10.0933 1.52904C10.2183 1.40388 10.388 1.33353 10.5649 1.3335ZM10.5652 2.94335L4.33333 9.18274V10.6668H5.82944L12.0574 4.43617L10.5652 2.94335Z" fill="currentColor"/>
                                                            </svg>
                                                        </span>
                                                        <p>{{ __('Rename') }}</p>
                                                    </a>
                                                    <a href="javascript:void(0)" id="drive-modal" data-target="move-folder-mod" data-effect="mfp-zoom-in" class="modal-trigger flex justify-start items-center gap-2 text-14 font-normal text-color-14 dark:text-white font-Figtree px-4 py-2 hover:bg-color-F6 dark:hover:bg-[#3A3A39] text-left"
                                                    data-id="{{ $folder->id }}" data-type="folder" data-content="{{ $folder->name }}" data-parent-id="{{ $folder->parent_folder }}">
                                                        <span class="w-4 h-4">
                                                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path d="M13.332 11.9998H2.66536V5.33317H13.332V11.9998ZM7.9987 3.99984L6.66536 2.6665H2.66536C2.31174 2.6665 1.9726 2.80698 1.72256 3.05703C1.47251 3.30708 1.33203 3.64622 1.33203 3.99984V11.9998C1.33203 12.3535 1.47251 12.6926 1.72256 12.9426C1.9726 13.1927 2.31174 13.3332 2.66536 13.3332H13.332C14.072 13.3332 14.6654 12.7398 14.6654 11.9998V5.33317C14.6654 4.97955 14.5249 4.64041 14.2748 4.39036C14.0248 4.14031 13.6857 3.99984 13.332 3.99984H7.9987ZM7.33203 9.33317V7.99984H9.9987V5.99984L12.6654 8.6665L9.9987 11.3332V9.33317H7.33203Z" fill="currentColor"/>
                                                            </svg>
                                                        </span>
                                                        <p>{{ __('Move') }}</p>
                                                    </a>
                                                    <a href="javascript:void(0)" class="{{ $folder->type }}-grid-bookmark-{{ $folder->id }} bookmark-option hidden flex justify-start items-center gap-2 text-14 font-normal text-color-14 dark:text-white font-Figtree px-4 py-2 hover:bg-color-F6 dark:hover:bg-[#3A3A39] text-left"
                                                        onclick="gridViewBookmarkToggle(this)" data-file-type="{{ $folder->type }}" data-file-id="{{ $folder->id }}" data-is-favorite="{{ in_array($folder->id, $ids) ? 'true' : 'false' }}">
                                                        <span class="w-4 h-4">
                                                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path d="M3.33464 1.3335H12.668C12.8448 1.3335 13.0143 1.40373 13.1394 1.52876C13.2644 1.65378 13.3346 1.82335 13.3346 2.00016V14.7622C13.3347 14.8218 13.3188 14.8803 13.2886 14.9317C13.2583 14.983 13.2149 15.0254 13.1627 15.0542C13.1106 15.0831 13.0516 15.0974 12.9921 15.0958C12.9325 15.0941 12.8744 15.0765 12.824 15.0448L8.0013 12.0202L3.17864 15.0442C3.12821 15.0758 3.07023 15.0934 3.0107 15.0951C2.95118 15.0968 2.89229 15.0825 2.84017 15.0537C2.78804 15.0249 2.74457 14.9827 2.71429 14.9314C2.68401 14.8802 2.66801 14.8217 2.66797 14.7622V2.00016C2.66797 1.82335 2.73821 1.65378 2.86323 1.52876C2.98826 1.40373 3.15782 1.3335 3.33464 1.3335ZM12.0013 2.66683H4.0013V12.9548L8.0013 10.4475L12.0013 12.9548V2.66683Z" fill="currentColor"/>
                                                            </svg>
                                                        </span>
                                                        <p>{{ in_array($folder->id, $ids) ? __('Undo bookmark') : __('Bookmark') }}</p>
                                                    </a>
                                                    <a id="{{ $folder->id }}" data-type="{{ $folder->type }}" href="javascript:void(0)" data-target="delete-popup" data-effect="mfp-zoom-in" class="modal-trigger flex justify-start items-center gap-2 text-14 font-normal text-color-14 dark:text-white font-Figtree px-4 py-2 hover:bg-color-F6 dark:hover:bg-[#3A3A39] text-left">
                                                        <span class="w-4 h-4">
                                                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path fill-rule="evenodd" clip-rule="evenodd" d="M7.43967 0.666504H8.55773C8.90916 0.666493 9.21233 0.666482 9.46205 0.686885C9.72561 0.708419 9.98776 0.755963 10.24 0.884492C10.6163 1.07624 10.9223 1.3822 11.114 1.75852C11.2426 2.01078 11.2901 2.27292 11.3117 2.53649C11.3299 2.76034 11.3318 3.02715 11.332 3.33317H13.9987C14.3669 3.33317 14.6654 3.63165 14.6654 3.99984C14.6654 4.36803 14.3669 4.66651 13.9987 4.66651H13.332V11.4941C13.332 12.0307 13.332 12.4736 13.3026 12.8344C13.272 13.2091 13.2062 13.5536 13.0414 13.8771C12.7857 14.3789 12.3778 14.7869 11.876 15.0425C11.5524 15.2074 11.208 15.2731 10.8332 15.3037C10.4725 15.3332 10.0296 15.3332 9.49291 15.3332H6.50448C5.96784 15.3332 5.52494 15.3332 5.16415 15.3037C4.78942 15.2731 4.44495 15.2074 4.12139 15.0425C3.61962 14.7869 3.21168 14.3789 2.95601 13.8771C2.79115 13.5536 2.72544 13.2091 2.69483 12.8344C2.66535 12.4736 2.66536 12.0307 2.66536 11.494L2.66536 4.66651H1.9987C1.63051 4.66651 1.33203 4.36803 1.33203 3.99984C1.33203 3.63165 1.63051 3.33317 1.9987 3.33317H4.66538C4.66557 3.02715 4.66745 2.76034 4.68574 2.53649C4.70728 2.27292 4.75482 2.01078 4.88335 1.75852C5.0751 1.3822 5.38106 1.07624 5.75738 0.884492C6.00964 0.755963 6.27178 0.708419 6.53535 0.686885C6.78506 0.666482 7.08824 0.666493 7.43967 0.666504ZM3.9987 4.66651V11.4665C3.9987 12.0376 3.99922 12.4258 4.02373 12.7258C4.04761 13.0181 4.0909 13.1676 4.14402 13.2718C4.27185 13.5227 4.47583 13.7267 4.72671 13.8545C4.83098 13.9076 4.98045 13.9509 5.27272 13.9748C5.57278 13.9993 5.96098 13.9998 6.53203 13.9998H9.46536C10.0364 13.9998 10.4246 13.9993 10.7247 13.9748C11.0169 13.9509 11.1664 13.9076 11.2707 13.8545C11.5216 13.7267 11.7255 13.5227 11.8534 13.2718C11.9065 13.1676 11.9498 13.0181 11.9737 12.7258C11.9982 12.4258 11.9987 12.0376 11.9987 11.4665V4.66651H3.9987ZM9.99865 3.33317H5.99875C5.99904 3.0231 6.00108 2.81116 6.01465 2.64506C6.02945 2.46395 6.05456 2.39681 6.07136 2.36384C6.13528 2.2384 6.23726 2.13642 6.3627 2.0725C6.39567 2.05571 6.46281 2.03059 6.64392 2.01579C6.83281 2.00036 7.081 1.99984 7.46536 1.99984H8.53203C8.9164 1.99984 9.16458 2.00036 9.35347 2.01579C9.53458 2.03059 9.60173 2.05571 9.63469 2.0725C9.76013 2.13642 9.86212 2.2384 9.92603 2.36384C9.94283 2.39681 9.96795 2.46395 9.98275 2.64506C9.99632 2.81116 9.99835 3.0231 9.99865 3.33317ZM6.66536 6.99984C7.03355 6.99984 7.33203 7.29832 7.33203 7.66651V10.9998C7.33203 11.368 7.03355 11.6665 6.66536 11.6665C6.29717 11.6665 5.9987 11.368 5.9987 10.9998V7.66651C5.9987 7.29832 6.29717 6.99984 6.66536 6.99984ZM9.33203 6.99984C9.70022 6.99984 9.9987 7.29832 9.9987 7.66651V10.9998C9.9987 11.368 9.70022 11.6665 9.33203 11.6665C8.96384 11.6665 8.66536 11.368 8.66536 10.9998V7.66651C8.66536 7.29832 8.96384 6.99984 9.33203 6.99984Z" fill="currentColor"/>
                                                            </svg>
                                                        </span>
                                                        <p>{{ __('Delete') }}</p>
                                                    </a>
                                                
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @elseif($folder->type == 'image_variant' || $folder->type == 'video')
                        {{-- image view --}}
                        @php
                            $image = \Modules\OpenAI\Entities\Archive::where('id', $folder->id)->first();
                        @endphp
                        <div id="{{ $folder->type . "-" . $folder->id }}" data-parent-id="{{ $folder->parent_folder }}" class="{{ in_array($folder->id, $ids) ? 'favorite' : 'non-favorite' }} search-folder folder-name folder-item relative view-mode list-view view-box cutom-border-active bg-white dark:bg-color-3A cursor-pointer py-[15px] sm:pt-6 sm:pb-[23px] px-4 sm:px-6">
                            <a href="{{ route("user.gallery.show") . "?slug={$folder->slug}" }}" onclick="return false;">
                                <div class="flex gap-4 sm:gap-px sm:items-center content-parent">
                                    <div class="flex sm:items-center gap-3 w-[175px] min-[990px]:w-[348px] xl:w-[400px] 5xl:w-[492px] min-[1730px]:w-[615px] xl:pr-[65px] file-name">
                                        @if ($folder->type == 'image_variant')
                                            <svg class="other-svg" width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <g clip-path="url(#clip0_11614_6102)">
                                                <path d="M0.00390625 3.375C0.00390625 2.77826 0.240959 2.20597 0.662916 1.78401C1.08487 1.36205 1.65717 1.125 2.25391 1.125H15.7539C16.3506 1.125 16.9229 1.36205 17.3449 1.78401C17.7669 2.20597 18.0039 2.77826 18.0039 3.375V14.625C18.0039 15.2217 17.7669 15.794 17.3449 16.216C16.9229 16.6379 16.3506 16.875 15.7539 16.875H2.25391C1.65717 16.875 1.08487 16.6379 0.662916 16.216C0.240959 15.794 0.00390625 15.2217 0.00390625 14.625V3.375ZM1.12891 13.5V14.625C1.12891 14.9234 1.24743 15.2095 1.45841 15.4205C1.66939 15.6315 1.95554 15.75 2.25391 15.75H15.7539C16.0523 15.75 16.3384 15.6315 16.5494 15.4205C16.7604 15.2095 16.8789 14.9234 16.8789 14.625V10.6875L12.6298 8.49713C12.5243 8.44428 12.4048 8.42594 12.2883 8.44472C12.1718 8.4635 12.0642 8.51843 11.9807 8.60175L7.80691 12.7755L4.81441 10.782C4.70636 10.7101 4.57676 10.6777 4.44759 10.6904C4.31841 10.7031 4.19761 10.7601 4.10566 10.8517L1.12891 13.5ZM6.75391 6.1875C6.75391 5.73995 6.57612 5.31072 6.25965 4.99426C5.94318 4.67779 5.51396 4.5 5.06641 4.5C4.61885 4.5 4.18963 4.67779 3.87316 4.99426C3.5567 5.31072 3.37891 5.73995 3.37891 6.1875C3.37891 6.63505 3.5567 7.06428 3.87316 7.38074C4.18963 7.69721 4.61885 7.875 5.06641 7.875C5.51396 7.875 5.94318 7.69721 6.25965 7.38074C6.57612 7.06428 6.75391 6.63505 6.75391 6.1875Z" fill="#898989"/>
                                                </g>
                                                <defs>
                                                <clipPath id="clip0_11614_6102">
                                                <rect width="18" height="18" fill="white"/>
                                                </clipPath>
                                                </defs>
                                            </svg>
                                        @else 
                                        <svg class="other-svg" xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="18" height="18" x="0" y="0" viewBox="0 0 512 512" style="enable-background:new 0 0 512 512" xml:space="preserve" class="">
                                            <g>
                                              <g fill-rule="evenodd">
                                                <path d="M440.59 206.676H99.418l327.7-94.93a10.018 10.018 0 0 0 5.976-4.781 9.989 9.989 0 0 0 .847-7.606L416.793 40.16C409.941 16.516 387.926 0 363.253 0c-5.198 0-10.378.738-15.401 2.191L40.176 91.321c-14.23 4.12-26.024 13.581-33.215 26.632-7.188 13.05-8.875 28.078-4.754 42.305l16.754 57.836v238.254C18.96 487.035 43.926 512 74.609 512h120.164c5.524 0 10-4.477 10-10s-4.476-10-10-10H74.613c-19.66 0-35.652-15.992-35.652-35.652V320.262H430.589v136.086c0 19.66-15.991 35.652-35.651 35.652H274.773c-5.52 0-10 4.477-10 10s4.48 10 10 10h120.165c30.687 0 55.652-24.965 55.652-55.652V216.676c0-5.524-4.477-10-10-10zm-176.332 93.586 42.488-73.586h55.262l-42.485 73.586zm-78.36 0 42.489-73.586h55.261l-42.484 73.586zm-78.355 0 42.484-73.586h55.266l-42.488 73.586zm37.18-129.457-71.149-68.336 53.309-15.442a9.92 9.92 0 0 0 1.312 1.543l71.149 68.336-53.309 15.442a9.951 9.951 0 0 0-1.312-1.543zm134-125.84L349.87 113.3l-53.308 15.441a9.742 9.742 0 0 0-1.313-1.543l-71.148-68.336 53.308-15.441a9.92 9.92 0 0 0 1.313 1.543zM203.457 66.77l71.148 68.332-53.308 15.445a9.742 9.742 0 0 0-1.313-1.543l-71.148-68.336 53.309-15.441c.378.543.816 1.062 1.312 1.543zm149.961-45.368c3.21-.93 6.52-1.402 9.836-1.402 15.824 0 29.937 10.578 34.328 25.727l14.367 49.59-40.12 11.62a10.165 10.165 0 0 0-1.317-1.542l-71.145-68.333zM24.48 127.602c4.61-8.372 12.16-14.434 21.262-17.07l5.875-1.704a10.17 10.17 0 0 0 1.313 1.543l71.148 68.336-88.293 25.578-14.367-49.59c-2.637-9.097-1.547-18.718 3.062-27.093zm14.48 99.074h87.974l-42.485 73.586H38.961zm303.657 73.586 42.485-73.586h45.488v73.586zm0 0" fill="#898989" opacity="1" data-original="#000000" class=""></path>
                                                <path d="M303.922 405.113a9.997 9.997 0 0 0-5-8.66l-87.856-50.723a10.006 10.006 0 0 0-10 0 9.997 9.997 0 0 0-5 8.66v101.446a9.995 9.995 0 0 0 5 8.656 9.983 9.983 0 0 0 10 0l87.856-50.719a9.997 9.997 0 0 0 5-8.66zm-87.856 33.403V371.71l57.856 33.402zM234.773 492c-5.507 0-10 4.492-10 10s4.493 10 10 10c5.512 0 10-4.492 10-10s-4.488-10-10-10zm0 0" fill="#898989" opacity="1" data-original="#000000" class=""></path>
                                              </g>
                                            </g>
                                          </svg>
                                                                                 
                                        @endif
                                            
                                        <div class="flex flex-col">
                                            <p class="text-color-14 w-[145px] folder-name min-[990px]:w-[300px] 5xl:w-[492px] dark:text-white text-14 font-Figtree font-medium line-clamp-2 sm:line-clamp-1 drive-tittle">
                                                {{ $folder->name }}
                                            </p>
                                            <p class="mt-2 block sm:hidden modified-time text-color-89 font-Figtree text-13 font-medium">{{ !empty($folder->updated_at) ? timeToGo($folder->updated_at, false, 'ago') : timeToGo($folder->created_at, false, 'ago') }}</p>
                                        </div>
                                    </div>
                                    <div class="check-data flex-1 hidden xl:inline-flex bookmark-box">
                                        <a href="javascript: void(0)" class="inline-block {{ $folder->type }}-bookmark-{{ $folder->id }}" title="Bookmarks"  onclick="fileBookmarkToggle(this)" data-file-type="{{ $folder->type }}" data-file-id="{{ $folder->id }}" data-is-favorite="{{ in_array($folder->id, $ids) ? 'true' : 'false' }}">
                                            @if (in_array($folder->id, $ids))
                                                <svg width="12" height="14" viewBox="0 0 12 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M0.75 1.75V13.5625C0.75 13.7169 0.831395 13.8599 0.964172 13.9387C1.09695 14.0175 1.26144 14.0205 1.397 13.9466L6 11.4359L10.603 13.9466C10.7386 14.0205 10.9031 14.0175 11.0358 13.9387C11.1686 13.8599 11.25 13.7169 11.25 13.5625V1.75C11.25 0.783502 10.4665 0 9.5 0H2.5C1.5335 0 0.75 0.783502 0.75 1.75Z" fill="url(#paint1_linear_140_371_{{ $folder->id }})"/>
                                                    <defs>
                                                    <linearGradient id="paint1_linear_140_371_{{ $folder->id }}" x1="7.5768" y1="12.2769" x2="1.83073" y2="2.55166" gradientUnits="userSpaceOnUse">
                                                    <stop stop-color="#E60C84"/>
                                                    <stop offset="1" stop-color="#FFCF4B"/>
                                                    </linearGradient>
                                                    </defs>
                                                </svg>
                                                    
                                            @else
                                                <svg class="unmarked" width="12" height="14" viewBox="0 0 12 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M0.75 1.75V13.5625C0.75 13.7169 0.831395 13.8599 0.964172 13.9387C1.09695 14.0175 1.26144 14.0205 1.397 13.9466L6 11.4359L10.603 13.9466C10.7386 14.0205 10.9031 14.0175 11.0358 13.9387C11.1686 13.8599 11.25 13.7169 11.25 13.5625V1.75C11.25 0.783502 10.4665 0 9.5 0H2.5C1.5335 0 0.75 0.783502 0.75 1.75Z" fill="#898989"/>
                                                </svg>
                                                
                                            @endif
                                        </a>
                                        <div class="document-spinner"></div>
                                    </div>
                                    <div class="check-data flex-1 hidden xl:inline-flex media-stash">
                                        <span class="text-color-89 font-Figtree text-13 font-medium">{{ $folder->item_count ? $folder->item_count : '--'  }}</span>
                                    </div>
                                    <div class="check-data w-[100px] 2xl:w-[207px] inline-flex">
                                        <span class="text-color-89 font-Figtree text-13 font-medium">{{ $folder->creator_name ??  '--' }}</span>
                                    </div>
                                    <div class="flex-1 hidden sm:inline-flex modified">
                                        <span class="text-color-89 font-Figtree text-13 font-medium">{{ !empty($folder->updated_at) ? timeToGo($folder->updated_at, false, 'ago') : timeToGo($folder->created_at, false, 'ago') }}</span>
                                    </div>
                                    <div class="w-[67px] flex justify-end max-sm:flex-1">
                                        <div class="relative inline-block">
                                            <button class="table-dropdown-click">
                                                <a href="javascript:void(0)" class="cursor-pointer text-color-14 dark:text-white border p-[7px] border-color-89 dark:border-color-47 dark:bg-color-47 rounded-lg flex justify-end action-dot">
                                                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M9.5 13C9.5 13.8284 8.82843 14.5 8 14.5C7.17157 14.5 6.5 13.8284 6.5 13C6.5 12.1716 7.17157 11.5 8 11.5C8.82843 11.5 9.5 12.1716 9.5 13ZM9.5 8C9.5 8.82843 8.82843 9.5 8 9.5C7.17157 9.5 6.5 8.82843 6.5 8C6.5 7.17157 7.17157 6.5 8 6.5C8.82843 6.5 9.5 7.17157 9.5 8ZM9.5 3C9.5 3.82843 8.82843 4.5 8 4.5C7.17157 4.5 6.5 3.82843 6.5 3C6.5 2.17157 7.17157 1.5 8 1.5C8.82843 1.5 9.5 2.17157 9.5 3Z" fill="currentColor"/>
                                                    </svg>
                                                </a>
                                            </button>
                                            <div class="absolute ltr:right-9 rtl:left-9 action-dropdown -top-2 mt-2 w-[180px] sm:w-[201px] border border-color-89 dark:border-color-47 rounded-lg bg-white dark:bg-[#333332] z-50 table-drop-body dropdown-shadow">
                                                <div class="my-2">
                                                    <a href="{{ $image->type == 'image_variant' ? $image->imageUrl() : $image->videoUrl() }}" download="{{ $folder->name }}" class="file-need-download flex justify-start items-center gap-2 text-14 font-normal text-color-14 dark:text-white font-Figtree px-4 py-2 hover:bg-color-F6 dark:hover:bg-[#3A3A39] text-left">
                                                        <span class="w-4 h-4">
                                                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path fill-rule="evenodd" clip-rule="evenodd" d="M8 2C8.36819 2 8.66667 2.29848 8.66667 2.66667V9.05719L10.8619 6.86193C11.1223 6.60158 11.5444 6.60158 11.8047 6.86193C12.0651 7.12228 12.0651 7.54439 11.8047 7.80474L8.4714 11.1381C8.21106 11.3984 7.78895 11.3984 7.5286 11.1381L4.19526 7.80474C3.93491 7.54439 3.93491 7.12228 4.19526 6.86193C4.45561 6.60158 4.87772 6.60158 5.13807 6.86193L7.33333 9.05719V2.66667C7.33333 2.29848 7.63181 2 8 2ZM2.66667 10.6667C3.03486 10.6667 3.33333 10.9651 3.33333 11.3333V12.6667C3.33333 12.8435 3.40357 13.013 3.5286 13.1381C3.65362 13.2631 3.82319 13.3333 4 13.3333H12C12.1768 13.3333 12.3464 13.2631 12.4714 13.1381C12.5964 13.013 12.6667 12.8435 12.6667 12.6667V11.3333C12.6667 10.9651 12.9651 10.6667 13.3333 10.6667C13.7015 10.6667 14 10.9651 14 11.3333V12.6667C14 13.1971 13.7893 13.7058 13.4142 14.0809C13.0391 14.456 12.5304 14.6667 12 14.6667H4C3.46957 14.6667 2.96086 14.456 2.58579 14.0809C2.21071 13.7058 2 13.1971 2 12.6667V11.3333C2 10.9651 2.29848 10.6667 2.66667 10.6667Z" fill="currentColor"/>
                                                            </svg>
                                                        </span>
                                                        <p>{{ __('Download') }}</p>
                                                    </a>
                                                    <a href="javascript:void(0)" id="drive-modal" data-parent-id="{{ $folder->parent_folder }}" data-target="move-folder-mod" data-effect="mfp-zoom-in" class="modal-trigger flex justify-start items-center gap-2 text-14 font-normal text-color-14 dark:text-white font-Figtree px-4 py-2 hover:bg-color-F6 dark:hover:bg-[#3A3A39] text-left" data-content="{{ $folder->name }}">
                                                        <span class="w-4 h-4">
                                                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path d="M13.332 11.9998H2.66536V5.33317H13.332V11.9998ZM7.9987 3.99984L6.66536 2.6665H2.66536C2.31174 2.6665 1.9726 2.80698 1.72256 3.05703C1.47251 3.30708 1.33203 3.64622 1.33203 3.99984V11.9998C1.33203 12.3535 1.47251 12.6926 1.72256 12.9426C1.9726 13.1927 2.31174 13.3332 2.66536 13.3332H13.332C14.072 13.3332 14.6654 12.7398 14.6654 11.9998V5.33317C14.6654 4.97955 14.5249 4.64041 14.2748 4.39036C14.0248 4.14031 13.6857 3.99984 13.332 3.99984H7.9987ZM7.33203 9.33317V7.99984H9.9987V5.99984L12.6654 8.6665L9.9987 11.3332V9.33317H7.33203Z" fill="currentColor"/>
                                                            </svg>
                                                        </span>
                                                        <p>{{ __('Move') }}</p>
                                                    </a>
                                                    <a href="javascript:void(0)" class="{{ $folder->type }}-grid-bookmark-{{ $folder->id }} bookmark-option hidden flex justify-start items-center gap-2 text-14 font-normal text-color-14 dark:text-white font-Figtree px-4 py-2 hover:bg-color-F6 dark:hover:bg-[#3A3A39] text-left"
                                                        onclick="gridViewBookmarkToggle(this)" data-file-type="{{ $folder->type }}" data-file-id="{{ $folder->id }}" data-is-favorite="{{ in_array($folder->id, $ids) ? 'true' : 'false' }}">
                                                        <span class="w-4 h-4">
                                                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path d="M3.33464 1.3335H12.668C12.8448 1.3335 13.0143 1.40373 13.1394 1.52876C13.2644 1.65378 13.3346 1.82335 13.3346 2.00016V14.7622C13.3347 14.8218 13.3188 14.8803 13.2886 14.9317C13.2583 14.983 13.2149 15.0254 13.1627 15.0542C13.1106 15.0831 13.0516 15.0974 12.9921 15.0958C12.9325 15.0941 12.8744 15.0765 12.824 15.0448L8.0013 12.0202L3.17864 15.0442C3.12821 15.0758 3.07023 15.0934 3.0107 15.0951C2.95118 15.0968 2.89229 15.0825 2.84017 15.0537C2.78804 15.0249 2.74457 14.9827 2.71429 14.9314C2.68401 14.8802 2.66801 14.8217 2.66797 14.7622V2.00016C2.66797 1.82335 2.73821 1.65378 2.86323 1.52876C2.98826 1.40373 3.15782 1.3335 3.33464 1.3335ZM12.0013 2.66683H4.0013V12.9548L8.0013 10.4475L12.0013 12.9548V2.66683Z" fill="currentColor"/>
                                                            </svg>
                                                        </span>
                                                        <p>{{ in_array($folder->id, $ids) ? __('Undo bookmark') : __('Bookmark') }}</p>
                                                    </a>
                                                    <a href="javascript:void(0)" data-type="{{ $folder->type }}" data-target="delete-popup" data-effect="mfp-zoom-in" class="modal-trigger flex justify-start items-center gap-2 text-14 font-normal text-color-14 dark:text-white font-Figtree px-4 py-2 hover:bg-color-F6 dark:hover:bg-[#3A3A39] text-left">
                                                        <span class="w-4 h-4">
                                                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path fill-rule="evenodd" clip-rule="evenodd" d="M7.43967 0.666504H8.55773C8.90916 0.666493 9.21233 0.666482 9.46205 0.686885C9.72561 0.708419 9.98776 0.755963 10.24 0.884492C10.6163 1.07624 10.9223 1.3822 11.114 1.75852C11.2426 2.01078 11.2901 2.27292 11.3117 2.53649C11.3299 2.76034 11.3318 3.02715 11.332 3.33317H13.9987C14.3669 3.33317 14.6654 3.63165 14.6654 3.99984C14.6654 4.36803 14.3669 4.66651 13.9987 4.66651H13.332V11.4941C13.332 12.0307 13.332 12.4736 13.3026 12.8344C13.272 13.2091 13.2062 13.5536 13.0414 13.8771C12.7857 14.3789 12.3778 14.7869 11.876 15.0425C11.5524 15.2074 11.208 15.2731 10.8332 15.3037C10.4725 15.3332 10.0296 15.3332 9.49291 15.3332H6.50448C5.96784 15.3332 5.52494 15.3332 5.16415 15.3037C4.78942 15.2731 4.44495 15.2074 4.12139 15.0425C3.61962 14.7869 3.21168 14.3789 2.95601 13.8771C2.79115 13.5536 2.72544 13.2091 2.69483 12.8344C2.66535 12.4736 2.66536 12.0307 2.66536 11.494L2.66536 4.66651H1.9987C1.63051 4.66651 1.33203 4.36803 1.33203 3.99984C1.33203 3.63165 1.63051 3.33317 1.9987 3.33317H4.66538C4.66557 3.02715 4.66745 2.76034 4.68574 2.53649C4.70728 2.27292 4.75482 2.01078 4.88335 1.75852C5.0751 1.3822 5.38106 1.07624 5.75738 0.884492C6.00964 0.755963 6.27178 0.708419 6.53535 0.686885C6.78506 0.666482 7.08824 0.666493 7.43967 0.666504ZM3.9987 4.66651V11.4665C3.9987 12.0376 3.99922 12.4258 4.02373 12.7258C4.04761 13.0181 4.0909 13.1676 4.14402 13.2718C4.27185 13.5227 4.47583 13.7267 4.72671 13.8545C4.83098 13.9076 4.98045 13.9509 5.27272 13.9748C5.57278 13.9993 5.96098 13.9998 6.53203 13.9998H9.46536C10.0364 13.9998 10.4246 13.9993 10.7247 13.9748C11.0169 13.9509 11.1664 13.9076 11.2707 13.8545C11.5216 13.7267 11.7255 13.5227 11.8534 13.2718C11.9065 13.1676 11.9498 13.0181 11.9737 12.7258C11.9982 12.4258 11.9987 12.0376 11.9987 11.4665V4.66651H3.9987ZM9.99865 3.33317H5.99875C5.99904 3.0231 6.00108 2.81116 6.01465 2.64506C6.02945 2.46395 6.05456 2.39681 6.07136 2.36384C6.13528 2.2384 6.23726 2.13642 6.3627 2.0725C6.39567 2.05571 6.46281 2.03059 6.64392 2.01579C6.83281 2.00036 7.081 1.99984 7.46536 1.99984H8.53203C8.9164 1.99984 9.16458 2.00036 9.35347 2.01579C9.53458 2.03059 9.60173 2.05571 9.63469 2.0725C9.76013 2.13642 9.86212 2.2384 9.92603 2.36384C9.94283 2.39681 9.96795 2.46395 9.98275 2.64506C9.99632 2.81116 9.99835 3.0231 9.99865 3.33317ZM6.66536 6.99984C7.03355 6.99984 7.33203 7.29832 7.33203 7.66651V10.9998C7.33203 11.368 7.03355 11.6665 6.66536 11.6665C6.29717 11.6665 5.9987 11.368 5.9987 10.9998V7.66651C5.9987 7.29832 6.29717 6.99984 6.66536 6.99984ZM9.33203 6.99984C9.70022 6.99984 9.9987 7.29832 9.9987 7.66651V10.9998C9.9987 11.368 9.70022 11.6665 9.33203 11.6665C8.96384 11.6665 8.66536 11.368 8.66536 10.9998V7.66651C8.66536 7.29832 8.96384 6.99984 9.33203 6.99984Z" fill="currentColor"/>
                                                            </svg>
                                                        </span>
                                                        <p>{{ __('Delete') }}</p>
                                                    </a>
                                                
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                            @if ( $image->type == 'image_variant') 
                                <img class="neg-transition-scale hidden image-views mt-2.5 w-full h-[154px] rounded-lg object-cover" src="{{ $image->imageUrl(['thumbnill' => true, 'size' => 'small']) }}" alt="{{ __('Image') }}">
                            @else
                                <video class="object-cover rounded-lg myVideo neg-transition-scale hidden image-views mt-2.5 w-full h-[154px] rounded-lg object-cover">
                                    <source src="{{ $image->videoUrl() }}" type="video/mp4">
                                </video>
                            @endif
                        </div>

                    @elseif($folder->type == 'template' || $folder->type == 'code_chat_reply' || $folder->type == 'long_article')
                        @php 
                            if ($folder->type == 'template') {
                                $view = route('user.editContent', $folder->slug);
                            } else if ($folder->type == 'code_chat_reply') {
                                $view = route('user.codeView', $folder->slug);
                            } else {
                                $view = route('user.long_article.edit', ['id' => $folder->id]);
                            }
                        @endphp
                        {{-- docs view --}}
                        <div id="{{ $folder->type . "-" . $folder->id }}" class="{{ in_array($folder->id, $ids) ? 'favorite' : 'non-favorite' }} search-folder folder-name folder-item relative view-mode list-view view-box cutom-border-active bg-white dark:bg-color-3A cursor-pointer py-[15px] sm:pt-6 sm:pb-[23px] px-4 sm:px-6">
                            <a href="{{ $view }}" onclick="return false;">
                                <div class="flex gap-4 sm:gap-px sm:items-center content-parent">
                                    <div class="flex sm:items-center gap-3 w-[175px] min-[990px]:w-[348px] xl:w-[400px] 5xl:w-[492px] min-[1730px]:w-[615px] xl:pr-[65px] file-name">
                                        <svg class="other-svg" width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M10.4545 0H4.5C3.25736 0 2.25 1.00736 2.25 2.25V15.75C2.25 16.9926 3.25736 18 4.5 18H13.5C14.7426 18 15.75 16.9926 15.75 15.75V5.2955C15.75 4.99713 15.6315 4.71098 15.4205 4.5L11.25 0.329505C11.039 0.118527 10.7529 0 10.4545 0ZM10.6875 3.9375V1.6875L14.0625 5.0625H11.8125C11.1912 5.0625 10.6875 4.55882 10.6875 3.9375ZM5.0625 10.125C4.75184 10.125 4.5 9.87316 4.5 9.5625C4.5 9.25184 4.75184 9 5.0625 9H12.9375C13.2482 9 13.5 9.25184 13.5 9.5625C13.5 9.87316 13.2482 10.125 12.9375 10.125H5.0625ZM4.5 11.8125C4.5 11.5018 4.75184 11.25 5.0625 11.25H12.9375C13.2482 11.25 13.5 11.5018 13.5 11.8125C13.5 12.1232 13.2482 12.375 12.9375 12.375H5.0625C4.75184 12.375 4.5 12.1232 4.5 11.8125ZM5.0625 14.625C4.75184 14.625 4.5 14.3732 4.5 14.0625C4.5 13.7518 4.75184 13.5 5.0625 13.5H9.5625C9.87316 13.5 10.125 13.7518 10.125 14.0625C10.125 14.3732 9.87316 14.625 9.5625 14.625H5.0625Z" fill="#898989"/>
                                        </svg>
                                        <div class="flex flex-col">
                                            <p class="text-color-14 w-[145px] folder-name min-[990px]:w-[300px] 5xl:w-[492px] dark:text-white text-14 font-Figtree font-medium line-clamp-2 sm:line-clamp-1 drive-tittle">
                                                {{ $folder->name }}
                                            </p>
                                            <p class="mt-2 block sm:hidden modified-time text-color-89 font-Figtree text-13 font-medium">{{ !empty($folder->updated_at) ? timeToGo($folder->updated_at, false, 'ago') : timeToGo($folder->created_at, false, 'ago') }}</p>
                                        </div>
                                    </div>
                                    <div class="check-data flex-1 hidden xl:inline-flex bookmark-box">
                                        <a href="javascript: void(0)" class="inline-block {{ $folder->type }}-bookmark-{{ $folder->id }}" title="Bookmarks"  onclick="fileBookmarkToggle(this)" data-file-type="{{ $folder->type }}" data-file-id="{{ $folder->id }}" data-is-favorite="{{ in_array($folder->id, $ids) ? 'true' : 'false' }}">
                                            @if (in_array($folder->id, $ids))
                                                <svg width="12" height="14" viewBox="0 0 12 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M0.75 1.75V13.5625C0.75 13.7169 0.831395 13.8599 0.964172 13.9387C1.09695 14.0175 1.26144 14.0205 1.397 13.9466L6 11.4359L10.603 13.9466C10.7386 14.0205 10.9031 14.0175 11.0358 13.9387C11.1686 13.8599 11.25 13.7169 11.25 13.5625V1.75C11.25 0.783502 10.4665 0 9.5 0H2.5C1.5335 0 0.75 0.783502 0.75 1.75Z" fill="url(#paint1_linear_140_371_{{ $folder->id }})"/>
                                                    <defs>
                                                    <linearGradient id="paint1_linear_140_371_{{ $folder->id }}" x1="7.5768" y1="12.2769" x2="1.83073" y2="2.55166" gradientUnits="userSpaceOnUse">
                                                    <stop stop-color="#E60C84"/>
                                                    <stop offset="1" stop-color="#FFCF4B"/>
                                                    </linearGradient>
                                                    </defs>
                                                </svg>
                                                    
                                            @else
                                                <svg class="unmarked" width="12" height="14" viewBox="0 0 12 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M0.75 1.75V13.5625C0.75 13.7169 0.831395 13.8599 0.964172 13.9387C1.09695 14.0175 1.26144 14.0205 1.397 13.9466L6 11.4359L10.603 13.9466C10.7386 14.0205 10.9031 14.0175 11.0358 13.9387C11.1686 13.8599 11.25 13.7169 11.25 13.5625V1.75C11.25 0.783502 10.4665 0 9.5 0H2.5C1.5335 0 0.75 0.783502 0.75 1.75Z" fill="#898989"/>
                                                </svg>
                                                
                                            @endif
                                        </a>
                                        <div class="document-spinner"></div>
                                    </div>
                                    <div class="check-data flex-1 hidden xl:inline-flex media-stash">
                                        <span class="text-color-89 font-Figtree text-13 font-medium">{{ $folder->item_count ? $folder->item_count : '--'  }}</span>
                                    </div>
                                    <div class="check-data w-[100px] 2xl:w-[207px] inline-flex">
                                        <span class="text-color-89 font-Figtree text-13 font-medium">{{ $folder->creator_name ??  '--' }}</span>
                                    </div>
                                    <div class="flex-1 hidden sm:inline-flex modified">
                                        <span class="text-color-89 font-Figtree text-13 font-medium">{{ !empty($folder->updated_at) ? timeToGo($folder->updated_at, false, 'ago') : timeToGo($folder->created_at, false, 'ago') }}</span>
                                    </div>
                                    <div class="w-[67px] flex justify-end max-sm:flex-1">
                                        <div class="relative inline-block">
                                            <button class="table-dropdown-click">
                                                <a href="javascript:void(0)" class="cursor-pointer text-color-14 dark:text-white border p-[7px] border-color-89 dark:border-color-47 dark:bg-color-47 rounded-lg flex justify-end action-dot">
                                                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M9.5 13C9.5 13.8284 8.82843 14.5 8 14.5C7.17157 14.5 6.5 13.8284 6.5 13C6.5 12.1716 7.17157 11.5 8 11.5C8.82843 11.5 9.5 12.1716 9.5 13ZM9.5 8C9.5 8.82843 8.82843 9.5 8 9.5C7.17157 9.5 6.5 8.82843 6.5 8C6.5 7.17157 7.17157 6.5 8 6.5C8.82843 6.5 9.5 7.17157 9.5 8ZM9.5 3C9.5 3.82843 8.82843 4.5 8 4.5C7.17157 4.5 6.5 3.82843 6.5 3C6.5 2.17157 7.17157 1.5 8 1.5C8.82843 1.5 9.5 2.17157 9.5 3Z" fill="currentColor"/>
                                                    </svg>
                                                </a>
                                            </button>
                                            <div class="absolute ltr:right-9 rtl:left-9 action-dropdown -top-2 mt-2 w-[180px] sm:w-[201px] border border-color-89 dark:border-color-47 rounded-lg bg-white dark:bg-[#333332] z-50 table-drop-body dropdown-shadow">
                                                <div class="my-2">
                                                    <a href="javascript:void(0)" data-id="{{ $folder->id }}" data-name="{{ cleanedUrl(trimWords($folder->name, 30, '')) }}" onclick="downloadDocument(this)" data-type="{{ $folder->type }}" class="flex justify-start items-center gap-2 text-14 font-normal text-color-14 dark:text-white font-Figtree px-4 py-2 hover:bg-color-F6 dark:hover:bg-[#3A3A39] text-left">
                                                        <span class="w-4 h-4">
                                                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path fill-rule="evenodd" clip-rule="evenodd" d="M8 2C8.36819 2 8.66667 2.29848 8.66667 2.66667V9.05719L10.8619 6.86193C11.1223 6.60158 11.5444 6.60158 11.8047 6.86193C12.0651 7.12228 12.0651 7.54439 11.8047 7.80474L8.4714 11.1381C8.21106 11.3984 7.78895 11.3984 7.5286 11.1381L4.19526 7.80474C3.93491 7.54439 3.93491 7.12228 4.19526 6.86193C4.45561 6.60158 4.87772 6.60158 5.13807 6.86193L7.33333 9.05719V2.66667C7.33333 2.29848 7.63181 2 8 2ZM2.66667 10.6667C3.03486 10.6667 3.33333 10.9651 3.33333 11.3333V12.6667C3.33333 12.8435 3.40357 13.013 3.5286 13.1381C3.65362 13.2631 3.82319 13.3333 4 13.3333H12C12.1768 13.3333 12.3464 13.2631 12.4714 13.1381C12.5964 13.013 12.6667 12.8435 12.6667 12.6667V11.3333C12.6667 10.9651 12.9651 10.6667 13.3333 10.6667C13.7015 10.6667 14 10.9651 14 11.3333V12.6667C14 13.1971 13.7893 13.7058 13.4142 14.0809C13.0391 14.456 12.5304 14.6667 12 14.6667H4C3.46957 14.6667 2.96086 14.456 2.58579 14.0809C2.21071 13.7058 2 13.1971 2 12.6667V11.3333C2 10.9651 2.29848 10.6667 2.66667 10.6667Z" fill="currentColor"/>
                                                            </svg>
                                                        </span>
                                                        <p>{{ __('Download') }}</p>
                                                    </a>
                                                    <a href="javascript:void(0)" id="drive-modal" data-target="move-folder-mod" data-parent-id="{{ $folder->parent_folder }}" data-effect="mfp-zoom-in" class="modal-trigger flex justify-start items-center gap-2 text-14 font-normal text-color-14 dark:text-white font-Figtree px-4 py-2 hover:bg-color-F6 dark:hover:bg-[#3A3A39] text-left" data-content="{{ $folder->name }}">
                                                        <span class="w-4 h-4">
                                                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path d="M13.332 11.9998H2.66536V5.33317H13.332V11.9998ZM7.9987 3.99984L6.66536 2.6665H2.66536C2.31174 2.6665 1.9726 2.80698 1.72256 3.05703C1.47251 3.30708 1.33203 3.64622 1.33203 3.99984V11.9998C1.33203 12.3535 1.47251 12.6926 1.72256 12.9426C1.9726 13.1927 2.31174 13.3332 2.66536 13.3332H13.332C14.072 13.3332 14.6654 12.7398 14.6654 11.9998V5.33317C14.6654 4.97955 14.5249 4.64041 14.2748 4.39036C14.0248 4.14031 13.6857 3.99984 13.332 3.99984H7.9987ZM7.33203 9.33317V7.99984H9.9987V5.99984L12.6654 8.6665L9.9987 11.3332V9.33317H7.33203Z" fill="currentColor"/>
                                                            </svg>
                                                        </span>
                                                        <p>{{ __('Move') }}</p>
                                                    </a>
                                                    <a href="javascript:void(0)" class="{{ $folder->type }}-grid-bookmark-{{ $folder->id }} bookmark-option hidden flex justify-start items-center gap-2 text-14 font-normal text-color-14 dark:text-white font-Figtree px-4 py-2 hover:bg-color-F6 dark:hover:bg-[#3A3A39] text-left"
                                                        onclick="gridViewBookmarkToggle(this)" data-file-type="{{ $folder->type }}" data-file-id="{{ $folder->id }}" data-is-favorite="{{ in_array($folder->id, $ids) ? 'true' : 'false' }}">
                                                        <span class="w-4 h-4">
                                                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path d="M3.33464 1.3335H12.668C12.8448 1.3335 13.0143 1.40373 13.1394 1.52876C13.2644 1.65378 13.3346 1.82335 13.3346 2.00016V14.7622C13.3347 14.8218 13.3188 14.8803 13.2886 14.9317C13.2583 14.983 13.2149 15.0254 13.1627 15.0542C13.1106 15.0831 13.0516 15.0974 12.9921 15.0958C12.9325 15.0941 12.8744 15.0765 12.824 15.0448L8.0013 12.0202L3.17864 15.0442C3.12821 15.0758 3.07023 15.0934 3.0107 15.0951C2.95118 15.0968 2.89229 15.0825 2.84017 15.0537C2.78804 15.0249 2.74457 14.9827 2.71429 14.9314C2.68401 14.8802 2.66801 14.8217 2.66797 14.7622V2.00016C2.66797 1.82335 2.73821 1.65378 2.86323 1.52876C2.98826 1.40373 3.15782 1.3335 3.33464 1.3335ZM12.0013 2.66683H4.0013V12.9548L8.0013 10.4475L12.0013 12.9548V2.66683Z" fill="currentColor"/>
                                                            </svg>
                                                        </span>
                                                        <p>{{ in_array($folder->id, $ids) ? __('Undo bookmark') : __('Bookmark') }}</p>
                                                    </a>
                                                    <a id="{{ $folder->id }}" data-type="{{ $folder->type }}" href="javascript:void(0)" data-target="delete-popup" data-effect="mfp-zoom-in" class="modal-trigger flex justify-start items-center gap-2 text-14 font-normal text-color-14 dark:text-white font-Figtree px-4 py-2 hover:bg-color-F6 dark:hover:bg-[#3A3A39] text-left">
                                                        <span class="w-4 h-4">
                                                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path fill-rule="evenodd" clip-rule="evenodd" d="M7.43967 0.666504H8.55773C8.90916 0.666493 9.21233 0.666482 9.46205 0.686885C9.72561 0.708419 9.98776 0.755963 10.24 0.884492C10.6163 1.07624 10.9223 1.3822 11.114 1.75852C11.2426 2.01078 11.2901 2.27292 11.3117 2.53649C11.3299 2.76034 11.3318 3.02715 11.332 3.33317H13.9987C14.3669 3.33317 14.6654 3.63165 14.6654 3.99984C14.6654 4.36803 14.3669 4.66651 13.9987 4.66651H13.332V11.4941C13.332 12.0307 13.332 12.4736 13.3026 12.8344C13.272 13.2091 13.2062 13.5536 13.0414 13.8771C12.7857 14.3789 12.3778 14.7869 11.876 15.0425C11.5524 15.2074 11.208 15.2731 10.8332 15.3037C10.4725 15.3332 10.0296 15.3332 9.49291 15.3332H6.50448C5.96784 15.3332 5.52494 15.3332 5.16415 15.3037C4.78942 15.2731 4.44495 15.2074 4.12139 15.0425C3.61962 14.7869 3.21168 14.3789 2.95601 13.8771C2.79115 13.5536 2.72544 13.2091 2.69483 12.8344C2.66535 12.4736 2.66536 12.0307 2.66536 11.494L2.66536 4.66651H1.9987C1.63051 4.66651 1.33203 4.36803 1.33203 3.99984C1.33203 3.63165 1.63051 3.33317 1.9987 3.33317H4.66538C4.66557 3.02715 4.66745 2.76034 4.68574 2.53649C4.70728 2.27292 4.75482 2.01078 4.88335 1.75852C5.0751 1.3822 5.38106 1.07624 5.75738 0.884492C6.00964 0.755963 6.27178 0.708419 6.53535 0.686885C6.78506 0.666482 7.08824 0.666493 7.43967 0.666504ZM3.9987 4.66651V11.4665C3.9987 12.0376 3.99922 12.4258 4.02373 12.7258C4.04761 13.0181 4.0909 13.1676 4.14402 13.2718C4.27185 13.5227 4.47583 13.7267 4.72671 13.8545C4.83098 13.9076 4.98045 13.9509 5.27272 13.9748C5.57278 13.9993 5.96098 13.9998 6.53203 13.9998H9.46536C10.0364 13.9998 10.4246 13.9993 10.7247 13.9748C11.0169 13.9509 11.1664 13.9076 11.2707 13.8545C11.5216 13.7267 11.7255 13.5227 11.8534 13.2718C11.9065 13.1676 11.9498 13.0181 11.9737 12.7258C11.9982 12.4258 11.9987 12.0376 11.9987 11.4665V4.66651H3.9987ZM9.99865 3.33317H5.99875C5.99904 3.0231 6.00108 2.81116 6.01465 2.64506C6.02945 2.46395 6.05456 2.39681 6.07136 2.36384C6.13528 2.2384 6.23726 2.13642 6.3627 2.0725C6.39567 2.05571 6.46281 2.03059 6.64392 2.01579C6.83281 2.00036 7.081 1.99984 7.46536 1.99984H8.53203C8.9164 1.99984 9.16458 2.00036 9.35347 2.01579C9.53458 2.03059 9.60173 2.05571 9.63469 2.0725C9.76013 2.13642 9.86212 2.2384 9.92603 2.36384C9.94283 2.39681 9.96795 2.46395 9.98275 2.64506C9.99632 2.81116 9.99835 3.0231 9.99865 3.33317ZM6.66536 6.99984C7.03355 6.99984 7.33203 7.29832 7.33203 7.66651V10.9998C7.33203 11.368 7.03355 11.6665 6.66536 11.6665C6.29717 11.6665 5.9987 11.368 5.9987 10.9998V7.66651C5.9987 7.29832 6.29717 6.99984 6.66536 6.99984ZM9.33203 6.99984C9.70022 6.99984 9.9987 7.29832 9.9987 7.66651V10.9998C9.9987 11.368 9.70022 11.6665 9.33203 11.6665C8.96384 11.6665 8.66536 11.368 8.66536 10.9998V7.66651C8.66536 7.29832 8.96384 6.99984 9.33203 6.99984Z" fill="currentColor"/>
                                                            </svg>
                                                        </span>
                                                        <p>{{ __('Delete') }}</p>
                                                    </a>
                                                
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                            <div class="w-full h-[154px] mt-2.5 rounded-lg bg-[#F9F7F7] dark:bg-[#333332] hidden image-views flex justify-center items-center">
                                <svg width="36" height="36" viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M20.909 0H9C6.51472 0 4.5 2.01472 4.5 4.5V31.5C4.5 33.9853 6.51472 36 9 36H27C29.4853 36 31.5 33.9853 31.5 31.5V10.591C31.5 9.99425 31.2629 9.42196 30.841 9L22.5 0.65901C22.078 0.237053 21.5057 0 20.909 0ZM21.375 7.875V3.375L28.125 10.125H23.625C22.3824 10.125 21.375 9.11764 21.375 7.875ZM10.125 20.25C9.50368 20.25 9 19.7463 9 19.125C9 18.5037 9.50368 18 10.125 18H25.875C26.4963 18 27 18.5037 27 19.125C27 19.7463 26.4963 20.25 25.875 20.25H10.125ZM9 23.625C9 23.0037 9.50368 22.5 10.125 22.5H25.875C26.4963 22.5 27 23.0037 27 23.625C27 24.2463 26.4963 24.75 25.875 24.75H10.125C9.50368 24.75 9 24.2463 9 23.625ZM10.125 29.25C9.50368 29.25 9 28.7463 9 28.125C9 27.5037 9.50368 27 10.125 27H19.125C19.7463 27 20.25 27.5037 20.25 28.125C20.25 28.7463 19.7463 29.25 19.125 29.25H10.125Z" fill="#AE85EF"/>
                                </svg>
                            </div> 
                        </div>
                    @else
                        @php 
                            $voiceover = '';
                            if ($folder->type == 'audio') {
                                $audio = \Modules\OpenAI\Entities\Audio::where('id', $folder->id)->first();
                            }
                             
                        @endphp
                        {{-- Music view --}}
                        <div id="{{ $folder->type . "-" . $folder->id }}" class="{{ in_array($folder->id, $ids) ? 'favorite' : 'non-favorite' }} search-folder folder-name folder-item relative view-mode list-view view-box cutom-border-active bg-white dark:bg-color-3A cursor-pointer py-[15px] sm:pt-6 sm:pb-[23px] px-4 sm:px-6">
                            <a href="{{ $folder->type == 'speech_to_text_chat_reply' ? route('user.editSpeech', ['id' => techEncrypt($folder->id)]) :  route('user.textToSpeechView', ['id' => $folder->id]) }}" onclick="return false;">
                                <div class="flex gap-4 sm:gap-px sm:items-center content-parent">
                                    <div class="flex sm:items-center gap-3 w-[175px] min-[990px]:w-[348px] xl:w-[400px] 5xl:w-[492px] min-[1730px]:w-[615px] xl:pr-[65px] file-name">
                                        <svg class="other-svg" width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <g clip-path="url(#clip0_11617_6125)">
                                            <path d="M14.1735 7.19971L10.8075 3.82471C10.7566 3.09299 10.931 2.36323 11.3072 1.73356C11.6834 1.1039 12.2434 0.60454 12.9119 0.302646C13.5803 0.000751495 14.3252 -0.0891974 15.0464 0.0448966C15.7675 0.178991 16.4303 0.530696 16.9455 1.05271C17.4658 1.5693 17.816 2.23245 17.9492 2.95345C18.0824 3.67446 17.9923 4.41895 17.6909 5.08734C17.3894 5.75572 16.8911 6.31614 16.2626 6.69364C15.634 7.07113 14.9052 7.24769 14.1735 7.19971ZM1.66353 13.7697L9.94353 5.49871L12.4905 8.04571L4.21053 16.3257L1.67253 13.7697H1.66353ZM0.403531 16.3167L2.30253 14.4087L3.58053 15.6867L1.67253 17.5947L0.394531 16.3167H0.403531ZM8.99853 13.4997L10.7985 11.6997V17.9997H8.99853V13.4997Z" fill="#898989"/>
                                            </g>
                                            <defs>
                                            <clipPath id="clip0_11617_6125">
                                            <rect width="18" height="18" fill="white"/>
                                            </clipPath>
                                            </defs>
                                        </svg>
                                            
                                        <div class="flex flex-col">
                                            <p class="text-color-14 w-[145px] folder-name min-[990px]:w-[300px] 5xl:w-[492px] dark:text-white text-14 font-Figtree font-medium line-clamp-2 sm:line-clamp-1 drive-tittle">
                                                {{ $folder->name }}
                                            </p>
                                            <p class="mt-2 block sm:hidden modified-time text-color-89 font-Figtree text-13 font-medium">{{ !empty($folder->updated_at) ? timeToGo($folder->updated_at, false, 'ago') : timeToGo($folder->created_at, false, 'ago') }}</p>
                                        </div>
                                    </div>
                                    <div class="check-data flex-1 hidden xl:inline-flex bookmark-box">
                                        <a href="javascript: void(0)" class="inline-block {{ $folder->type }}-bookmark-{{ $folder->id }}" title="Bookmarks"  onclick="fileBookmarkToggle(this)" data-file-type="{{ $folder->type }}" data-file-id="{{ $folder->id }}" data-is-favorite="{{ in_array($folder->id, $ids) ? 'true' : 'false' }}">
                                            @if (in_array($folder->id, $ids))
                                                <svg width="12" height="14" viewBox="0 0 12 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M0.75 1.75V13.5625C0.75 13.7169 0.831395 13.8599 0.964172 13.9387C1.09695 14.0175 1.26144 14.0205 1.397 13.9466L6 11.4359L10.603 13.9466C10.7386 14.0205 10.9031 14.0175 11.0358 13.9387C11.1686 13.8599 11.25 13.7169 11.25 13.5625V1.75C11.25 0.783502 10.4665 0 9.5 0H2.5C1.5335 0 0.75 0.783502 0.75 1.75Z" fill="url(#paint1_linear_140_371_{{ $folder->id }})"/>
                                                    <defs>
                                                    <linearGradient id="paint1_linear_140_371_{{ $folder->id }}" x1="7.5768" y1="12.2769" x2="1.83073" y2="2.55166" gradientUnits="userSpaceOnUse">
                                                    <stop stop-color="#E60C84"/>
                                                    <stop offset="1" stop-color="#FFCF4B"/>
                                                    </linearGradient>
                                                    </defs>
                                                </svg>
                                                    
                                            @else
                                                <svg class="unmarked" width="12" height="14" viewBox="0 0 12 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M0.75 1.75V13.5625C0.75 13.7169 0.831395 13.8599 0.964172 13.9387C1.09695 14.0175 1.26144 14.0205 1.397 13.9466L6 11.4359L10.603 13.9466C10.7386 14.0205 10.9031 14.0175 11.0358 13.9387C11.1686 13.8599 11.25 13.7169 11.25 13.5625V1.75C11.25 0.783502 10.4665 0 9.5 0H2.5C1.5335 0 0.75 0.783502 0.75 1.75Z" fill="#898989"/>
                                                </svg>
                                                
                                            @endif
                                        </a>
                                        <div class="document-spinner"></div>
                                    </div>
                                    <div class="check-data flex-1 hidden xl:inline-flex media-stash">
                                        <span class="text-color-89 font-Figtree text-13 font-medium">{{ $folder->item_count ? $folder->item_count : '--'  }}</span>
                                    </div>
                                    <div class="check-data w-[100px] 2xl:w-[207px] inline-flex">
                                        <span class="text-color-89 font-Figtree text-13 font-medium">{{ $folder->creator_name ??  '--' }}</span>
                                    </div>
                                    <div class="flex-1 hidden sm:inline-flex modified">
                                        <span class="text-color-89 font-Figtree text-13 font-medium">{{ !empty($folder->updated_at) ? timeToGo($folder->updated_at, false, 'ago') : timeToGo($folder->created_at, false, 'ago') }}</span>
                                    </div>
                                    <div class="w-[67px] flex justify-end max-sm:flex-1">
                                        <div class="relative inline-block">
                                            <button class="table-dropdown-click">
                                                <a href="javascript:void(0)" class="cursor-pointer text-color-14 dark:text-white border p-[7px] border-color-89 dark:border-color-47 dark:bg-color-47 rounded-lg flex justify-end action-dot">
                                                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M9.5 13C9.5 13.8284 8.82843 14.5 8 14.5C7.17157 14.5 6.5 13.8284 6.5 13C6.5 12.1716 7.17157 11.5 8 11.5C8.82843 11.5 9.5 12.1716 9.5 13ZM9.5 8C9.5 8.82843 8.82843 9.5 8 9.5C7.17157 9.5 6.5 8.82843 6.5 8C6.5 7.17157 7.17157 6.5 8 6.5C8.82843 6.5 9.5 7.17157 9.5 8ZM9.5 3C9.5 3.82843 8.82843 4.5 8 4.5C7.17157 4.5 6.5 3.82843 6.5 3C6.5 2.17157 7.17157 1.5 8 1.5C8.82843 1.5 9.5 2.17157 9.5 3Z" fill="currentColor"/>
                                                    </svg>
                                                </a>
                                            </button>
                                            <div class="absolute ltr:right-9 rtl:left-9 action-dropdown -top-2 mt-2 w-[180px] sm:w-[201px] border border-color-89 dark:border-color-47 rounded-lg bg-white dark:bg-[#333332] z-50 table-drop-body dropdown-shadow">
                                                <div class="my-2">
                                                    <a class=" {{ $folder->type == 'audio' ? 'file-need-download' : ''  }} flex justify-start items-center gap-2 text-14 font-normal text-color-14 dark:text-white font-Figtree px-4 py-2 hover:bg-color-F6 dark:hover:bg-[#3A3A39] text-left"
                                                        href="{{ $folder->type == 'speech_to_text_chat_reply' ? "javascript:void(0)" : $audio->googleAudioUrl() }}"
                                                        @if ($folder->type == 'audio') download="{{ cleanedUrl(trimWords($audio->prompt, 30, '')) }}" @endif 
                                                        @if ($folder->type == 'speech_to_text_chat_reply') data-id="{{ $folder->id }}" data-name="{{ cleanedUrl(trimWords($folder->name, 30, '')) }}" onclick="downloadDocument(this)" data-type="{{ $folder->type }}" @endif>
                                                        <span class="w-4 h-4">
                                                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path fill-rule="evenodd" clip-rule="evenodd" d="M8 2C8.36819 2 8.66667 2.29848 8.66667 2.66667V9.05719L10.8619 6.86193C11.1223 6.60158 11.5444 6.60158 11.8047 6.86193C12.0651 7.12228 12.0651 7.54439 11.8047 7.80474L8.4714 11.1381C8.21106 11.3984 7.78895 11.3984 7.5286 11.1381L4.19526 7.80474C3.93491 7.54439 3.93491 7.12228 4.19526 6.86193C4.45561 6.60158 4.87772 6.60158 5.13807 6.86193L7.33333 9.05719V2.66667C7.33333 2.29848 7.63181 2 8 2ZM2.66667 10.6667C3.03486 10.6667 3.33333 10.9651 3.33333 11.3333V12.6667C3.33333 12.8435 3.40357 13.013 3.5286 13.1381C3.65362 13.2631 3.82319 13.3333 4 13.3333H12C12.1768 13.3333 12.3464 13.2631 12.4714 13.1381C12.5964 13.013 12.6667 12.8435 12.6667 12.6667V11.3333C12.6667 10.9651 12.9651 10.6667 13.3333 10.6667C13.7015 10.6667 14 10.9651 14 11.3333V12.6667C14 13.1971 13.7893 13.7058 13.4142 14.0809C13.0391 14.456 12.5304 14.6667 12 14.6667H4C3.46957 14.6667 2.96086 14.456 2.58579 14.0809C2.21071 13.7058 2 13.1971 2 12.6667V11.3333C2 10.9651 2.29848 10.6667 2.66667 10.6667Z" fill="currentColor"/>
                                                            </svg>
                                                        </span>
                                                        <p>{{ __('Download') }}</p>
                                                    </a>
                                                    <a href="javascript:void(0)" id="drive-modal" data-target="move-folder-mod" data-parent-id="{{ $folder->parent_folder }}" data-effect="mfp-zoom-in" class="modal-trigger flex justify-start items-center gap-2 text-14 font-normal text-color-14 dark:text-white font-Figtree px-4 py-2 hover:bg-color-F6 dark:hover:bg-[#3A3A39] text-left" data-content="{{ $folder->name }}">
                                                        <span class="w-4 h-4">
                                                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path d="M13.332 11.9998H2.66536V5.33317H13.332V11.9998ZM7.9987 3.99984L6.66536 2.6665H2.66536C2.31174 2.6665 1.9726 2.80698 1.72256 3.05703C1.47251 3.30708 1.33203 3.64622 1.33203 3.99984V11.9998C1.33203 12.3535 1.47251 12.6926 1.72256 12.9426C1.9726 13.1927 2.31174 13.3332 2.66536 13.3332H13.332C14.072 13.3332 14.6654 12.7398 14.6654 11.9998V5.33317C14.6654 4.97955 14.5249 4.64041 14.2748 4.39036C14.0248 4.14031 13.6857 3.99984 13.332 3.99984H7.9987ZM7.33203 9.33317V7.99984H9.9987V5.99984L12.6654 8.6665L9.9987 11.3332V9.33317H7.33203Z" fill="currentColor"/>
                                                            </svg>
                                                        </span>
                                                        <p>{{ __('Move') }}</p>
                                                    </a>
                                                    <a href="javascript:void(0)" class="{{ $folder->type }}-grid-bookmark-{{ $folder->id }} bookmark-option hidden flex justify-start items-center gap-2 text-14 font-normal text-color-14 dark:text-white font-Figtree px-4 py-2 hover:bg-color-F6 dark:hover:bg-[#3A3A39] text-left"
                                                        onclick="gridViewBookmarkToggle(this)" data-file-type="{{ $folder->type }}" data-file-id="{{ $folder->id }}" data-is-favorite="{{ in_array($folder->id, $ids) ? 'true' : 'false' }}">
                                                        <span class="w-4 h-4">
                                                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path d="M3.33464 1.3335H12.668C12.8448 1.3335 13.0143 1.40373 13.1394 1.52876C13.2644 1.65378 13.3346 1.82335 13.3346 2.00016V14.7622C13.3347 14.8218 13.3188 14.8803 13.2886 14.9317C13.2583 14.983 13.2149 15.0254 13.1627 15.0542C13.1106 15.0831 13.0516 15.0974 12.9921 15.0958C12.9325 15.0941 12.8744 15.0765 12.824 15.0448L8.0013 12.0202L3.17864 15.0442C3.12821 15.0758 3.07023 15.0934 3.0107 15.0951C2.95118 15.0968 2.89229 15.0825 2.84017 15.0537C2.78804 15.0249 2.74457 14.9827 2.71429 14.9314C2.68401 14.8802 2.66801 14.8217 2.66797 14.7622V2.00016C2.66797 1.82335 2.73821 1.65378 2.86323 1.52876C2.98826 1.40373 3.15782 1.3335 3.33464 1.3335ZM12.0013 2.66683H4.0013V12.9548L8.0013 10.4475L12.0013 12.9548V2.66683Z" fill="currentColor"/>
                                                            </svg>
                                                        </span>
                                                        <p>{{ in_array($folder->id, $ids) ? __('Undo bookmark') : __('Bookmark') }}</p>
                                                    </a>
                                                    <a id="{{ $folder->id }}" data-type="{{ $folder->type }}" href="javascript:void(0)" data-target="delete-popup" data-effect="mfp-zoom-in" class="modal-trigger flex justify-start items-center gap-2 text-14 font-normal text-color-14 dark:text-white font-Figtree px-4 py-2 hover:bg-color-F6 dark:hover:bg-[#3A3A39] text-left">
                                                        <span class="w-4 h-4">
                                                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path fill-rule="evenodd" clip-rule="evenodd" d="M7.43967 0.666504H8.55773C8.90916 0.666493 9.21233 0.666482 9.46205 0.686885C9.72561 0.708419 9.98776 0.755963 10.24 0.884492C10.6163 1.07624 10.9223 1.3822 11.114 1.75852C11.2426 2.01078 11.2901 2.27292 11.3117 2.53649C11.3299 2.76034 11.3318 3.02715 11.332 3.33317H13.9987C14.3669 3.33317 14.6654 3.63165 14.6654 3.99984C14.6654 4.36803 14.3669 4.66651 13.9987 4.66651H13.332V11.4941C13.332 12.0307 13.332 12.4736 13.3026 12.8344C13.272 13.2091 13.2062 13.5536 13.0414 13.8771C12.7857 14.3789 12.3778 14.7869 11.876 15.0425C11.5524 15.2074 11.208 15.2731 10.8332 15.3037C10.4725 15.3332 10.0296 15.3332 9.49291 15.3332H6.50448C5.96784 15.3332 5.52494 15.3332 5.16415 15.3037C4.78942 15.2731 4.44495 15.2074 4.12139 15.0425C3.61962 14.7869 3.21168 14.3789 2.95601 13.8771C2.79115 13.5536 2.72544 13.2091 2.69483 12.8344C2.66535 12.4736 2.66536 12.0307 2.66536 11.494L2.66536 4.66651H1.9987C1.63051 4.66651 1.33203 4.36803 1.33203 3.99984C1.33203 3.63165 1.63051 3.33317 1.9987 3.33317H4.66538C4.66557 3.02715 4.66745 2.76034 4.68574 2.53649C4.70728 2.27292 4.75482 2.01078 4.88335 1.75852C5.0751 1.3822 5.38106 1.07624 5.75738 0.884492C6.00964 0.755963 6.27178 0.708419 6.53535 0.686885C6.78506 0.666482 7.08824 0.666493 7.43967 0.666504ZM3.9987 4.66651V11.4665C3.9987 12.0376 3.99922 12.4258 4.02373 12.7258C4.04761 13.0181 4.0909 13.1676 4.14402 13.2718C4.27185 13.5227 4.47583 13.7267 4.72671 13.8545C4.83098 13.9076 4.98045 13.9509 5.27272 13.9748C5.57278 13.9993 5.96098 13.9998 6.53203 13.9998H9.46536C10.0364 13.9998 10.4246 13.9993 10.7247 13.9748C11.0169 13.9509 11.1664 13.9076 11.2707 13.8545C11.5216 13.7267 11.7255 13.5227 11.8534 13.2718C11.9065 13.1676 11.9498 13.0181 11.9737 12.7258C11.9982 12.4258 11.9987 12.0376 11.9987 11.4665V4.66651H3.9987ZM9.99865 3.33317H5.99875C5.99904 3.0231 6.00108 2.81116 6.01465 2.64506C6.02945 2.46395 6.05456 2.39681 6.07136 2.36384C6.13528 2.2384 6.23726 2.13642 6.3627 2.0725C6.39567 2.05571 6.46281 2.03059 6.64392 2.01579C6.83281 2.00036 7.081 1.99984 7.46536 1.99984H8.53203C8.9164 1.99984 9.16458 2.00036 9.35347 2.01579C9.53458 2.03059 9.60173 2.05571 9.63469 2.0725C9.76013 2.13642 9.86212 2.2384 9.92603 2.36384C9.94283 2.39681 9.96795 2.46395 9.98275 2.64506C9.99632 2.81116 9.99835 3.0231 9.99865 3.33317ZM6.66536 6.99984C7.03355 6.99984 7.33203 7.29832 7.33203 7.66651V10.9998C7.33203 11.368 7.03355 11.6665 6.66536 11.6665C6.29717 11.6665 5.9987 11.368 5.9987 10.9998V7.66651C5.9987 7.29832 6.29717 6.99984 6.66536 6.99984ZM9.33203 6.99984C9.70022 6.99984 9.9987 7.29832 9.9987 7.66651V10.9998C9.9987 11.368 9.70022 11.6665 9.33203 11.6665C8.96384 11.6665 8.66536 11.368 8.66536 10.9998V7.66651C8.66536 7.29832 8.96384 6.99984 9.33203 6.99984Z" fill="currentColor"/>
                                                            </svg>
                                                        </span>
                                                        <p>{{ __('Delete') }}</p>
                                                    </a>
                                                
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                            <div class="w-full h-[154px] mt-2.5 rounded-lg bg-[#F9F7F7] dark:bg-[#333332] hidden image-views flex justify-center items-center">
                                <svg width="36" height="36" viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M16.5714 20.9004V1H32V6.66695H20.4286V27.4458C20.4282 29.109 19.8675 30.7257 18.8335 32.045C17.7995 33.3643 16.35 34.3127 14.7096 34.7429C13.0693 35.1731 11.3299 35.0611 9.76117 34.4244C8.19241 33.7877 6.88198 32.6618 6.0331 31.2213C5.18421 29.7808 4.8443 28.1062 5.06609 26.4572C5.28788 24.8082 6.05897 23.2769 7.25978 22.1009C8.46059 20.9249 10.024 20.1698 11.7076 19.9527C13.3912 19.7357 15.1008 20.0688 16.5714 20.9004Z" fill="#F0789D"/>
                                </svg>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
            <div class="relative hidden others-div grid grid-cols-1 xs:grid-cols-2 lg:grid-cols-3 2xl:grid-cols-4 8xl:grid-cols-6 gap-4 mt-14">
                    <!-- Divs without type="folder" will appear here when toggled to grid view -->
            </div>
        </div>
        <div class="flex flex-col justify-center items-center mt-[80px] no-folder {{ count($folders) == 0 ? '' : 'hidden' }}">
            <svg width="44" height="44" viewBox="0 0 44 44" fill="none" xmlns="http://www.w3.org/2000/svg">
                <g clip-path="url(#clip0_11826_6486)">
                <path d="M41.6469 8.7265H21.9439L21.7672 7.58053C21.57 6.30063 20.3489 5.25342 19.0538 5.25342H9.57634C8.28123 5.25342 7.0601 6.30063 6.86308 7.58053L6.68641 8.7265H6.0148C4.71982 8.7265 3.66016 9.78616 3.66016 11.0811V36.3924C3.66016 37.6875 4.71982 38.747 6.0148 38.747H41.6468C42.9418 38.747 44.0015 37.6875 44.0015 36.3924V11.0813C44.0018 9.78616 42.9419 8.7265 41.6469 8.7265Z" fill="#FFA400"/>
                <path d="M41.16 11.2642H5.83984V35.2048H41.16V11.2642Z" fill="white"/>
                <path d="M40.371 16.173C40.1654 14.8943 38.9375 13.8481 37.6426 13.8481H2.01046C0.715359 13.8481 -0.176132 14.8943 0.0295049 16.173L3.28498 36.4226C3.49061 37.7013 4.71845 38.7474 6.01343 38.7474H41.6454C42.9404 38.7474 43.8319 37.7013 43.6264 36.4226L40.371 16.173Z" fill="#F6C65B"/>
                </g>
                <defs>
                <clipPath id="clip0_11826_6486">
                <rect width="44" height="44" fill="white"/>
                </clipPath>
                </defs>
            </svg>
            <p class="text-color-89 text-15 font-Figtree font-medium mt-3 text-center xxs:w-[346px]">{{ __('Folder is empty') }}</p>
        </div>
        <div class="drive-pagination">
            {{ $folders->links('site.layout.partials.pagination') }}
        </div>
        
    </div>

    <!-- Move data Modal -->
    @include('openai::blades.move-folder-modal')

    <!-- Rename Modal -->
    <div id="rename-popup" class="relative bg-white dark:bg-color-3A p-6 w-[345px] xs:w-[388px] sm:w-[466px] rounded-xl white-popup mfp-with-anim mfp-hide">
        <form name="folderUpdateSubmit" id="folderUpdateSubmit">
            {!! csrf_field() !!}
            <div class="folder-show">
                <p class="font-Figtree text-color-14 dark:text-white text-20 font-semibold -mt-1">
                    {{ __('Rename Folder')}}
                </p>
                <p class="font-Figtree text-color-14 dark:text-white text-14 font-normal mt-3 pt-6 border-t border-t-color-DF dark:border-t-color-47">
                    {{ __('Folder Name')}}
                </p>
                <div>
                    <input class="form-control w-full px-4 h-12 py-1.5 text-base mt-1.5 font-normal font-Figtree text-color-14 dark:!text-white bg-white dark:bg-[#333332] border border-color-89 dark:!border-color-47 rounded-xl focus:text-color-14 focus:dark:!text-white focus:bg-white focus:border-color-89 focus:outline-none"
                    type="text" 
                    name="name"
                    id="rename_folder_name" required
                    oninvalid="this.setCustomValidity('{{ __('This field is required.') }}')"
                    >
                </div>
                <input type="text" class="hidden" name="slug" id="rename_slug" />
                <input type="int" class="hidden" name="userId" id="userId" value ="{{ auth()->user()->id }}" />
                <input type="int" class="hidden" name="folderId" id="folder_id" />

                <div class="flex items-center mt-6 gap-[16px]">
                    <button type="submit" class="flex items-center gap-2.5 font-Figtree bg-color-14 text-white font-semibold text-15 py-2.5 px-[41px] rounded-xl">
                        {{ __('Update') }}
                        <span class="loader-template folder-rename-loader hidden">
                            <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" width="72" height="72" viewBox="0 0 72 72" fill="none">
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
                        </span>  
                    </button>
                    <a href="javascript:void(0)" class="close-popup font-Figtree text-color-14 dark:text-white font-semibold text-15 py-2.5 px-[42px] border border-color-89 dark:border-color-47 bg-color-F6 dark:bg-color-47 rounded-xl">
                        {{ __('Cancel') }}
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Create folder Modal -->
    <div id="create-folder" class="relative bg-white dark:bg-color-3A p-6 w-[345px] xs:w-[388px] sm:w-[466px] rounded-xl white-popup mfp-with-anim mfp-hide">
        <form name="folderCreateSubmit" id="folderCreateSubmit">
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
                        type="text" name="name" id="folder_name" required oninvalid="this.setCustomValidity('{{ __('This field is required.') }}')">
                </div>
                <input type="text" class="hidden" name="slug" id="slug" />
                <input type="int" class="hidden" name="userId" id="user_id" value ="{{ auth()->user()->id }}" />
                <input type="int" class="hidden" name="parentId" id="parent_id" value ="{{ $parentId }}" />
                
                <div class="flex items-center mt-6 gap-[16px]">
                    <button type="submit" class="flex items-center gap-2.5 font-Figtree bg-color-14 text-white font-semibold text-15 py-2.5 px-[43px] rounded-xl">
                        {{ __('Create') }}
                        <span class="loader-template folder-loader hidden">
                            <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" width="72" height="72" viewBox="0 0 72 72" fill="none">
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
                        </span>
                    </button>
                    <a href="javascript:void(0)" class="close-popup font-Figtree text-color-14 dark:text-white font-semibold text-15 py-2.5 px-[42px] border border-color-89 dark:border-color-47 bg-color-F6 dark:bg-color-47 rounded-xl">
                        {{ __('Cancel') }}
                    </a>
                </div>
            </div>
        </form>
    </div>


    <!-- Delete Modal -->
    <div id="delete-popup" class="relative flex flex-col items-center bg-white dark:bg-color-3A p-6 w-[345px] xs:w-[388px] rounded-xl white-popup mfp-with-anim mfp-hide">
        <p class="font-Figtree text-color-14 dark:text-white text-20 font-semibold text-center">
            {{ __('Delete Selected?') }}
        </p>
        <p class="font-Figtree text-color-14 dark:text-white text-16 font-medium text-center mt-3 w-[280px]">
            {{ __('Are you sure you want to delete all the selected files?') }}
        </p>
        <div class="flex justify-center items-center mt-7 gap-[16px]">
            <a href="javascript:void(0)" class="close-popup font-Figtree text-color-14 dark:text-white font-semibold text-15 py-[11px] px-[42px] border border-color-89 dark:border-color-47 bg-color-F6 dark:bg-color-47 rounded-xl">
                {{ __('Cancel') }}
            </a>
            <a href="javascript:void(0)" id="delete-folder" class="flex gap-2 delete-folder font-Figtree text-white font-semibold text-15 py-[11px] px-[30px] bg-color-DFF rounded-xl">
                <span>{{ __('Yes, Delete') }}</span>
                <svg class="folder-del-loader animate-spin h-5 w-5 hidden" xmlns="http://www.w3.org/2000/svg" width="72" height="72" viewBox="0 0 72 72" fill="none">
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
            </a>
        </div>
    </div>
    
</div>
@endsection

@section('js')
    <script src="{{ asset('Modules/OpenAI/Resources/assets/js/customer/drive.min.js') }}"></script>
@endsection

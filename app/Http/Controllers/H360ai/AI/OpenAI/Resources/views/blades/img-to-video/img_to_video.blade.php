@extends('layouts.user_master')
@section('page_title', __('Video Maker'))
@section('content')
    {{-- main content --}}
    <div
        class="w-[68.9%] 7xl:w-[83.9%] bg-color-F6 dark:bg-[#292929] flex flex-col flex-1 border-l dark:border-[#474746] border-color-DF h-screen">
        <div
            class="subscription-main flex xl:flex-row flex-col xl:h-full subscription-main md:overflow-auto sidebar-scrollbar md:h-screen overflow-x-hidden 2xl:gap-[55px]">
            <div class="xl:w-[401px] 5xl:w-[474px] pt-14">
                @include('openai::user.includes.sidebar-img-to-video')
            </div>
            <div class="grow xl:px-0 px-5 xl:pt-[74px] pt-0 9xl:pb-[46px] pb-24 xl:w-1/2">
                <div class="xl:mt-4 ltr:xl:mr-12 rtl:xl:ml-12">
                    <div class="sm:flex justify-end items-center mb-5">
                        <div class="sm:text-right text-center">
                            <div class="gap-2.5 items-center hidden min-[1200px]:flex flex-row-reverse">
                                <p class="dark:text-white text-color-14 text-[15px] font-normal leading-[22px] font-Figtree">
                                    {{ __('Columns') }}</p>
                                <input dir="ltr" class="range progress-bar w-full progress-input" id="progressInput"
                                    min="0" max="100" type="range" value="50" step="1" />
                            </div>
                        </div>
                    </div>
                    <div id="video-gallery"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- end main content --}}

    <div class="modal index-modal absolute z-[9999999999] top-0 left-0 right-0 w-full h-full">
        <div class="modal-overlay fixed z-10 top-0 right-0 left-0 w-full h-full">
        </div>
        <div class="modal-wrapper  modal-wrapper modal-transition fixed inset-0 z-10">
            <div class="modal-body flex h-full justify-center p-4 text-center items-center sm:p-0">
                <div class="modal-content modal-transition rounded-xl py-6 md:px-[54px] bg-white dark:bg-color-3A px-8">
                    <p class="font-Figtree text-color-14 dark:text-white text-16 font-medium text-center">
                        {{ __('Are you sure you want to delete this Item?') }}</p>
                    <div class="flex justify-center items-center mt-7 gap-[16px]">
                        <a href="javascript: void(0)"
                            class="font-Figtree text-color-14 dark:text-white font-semibold text-15 py-[11px] px-[42px] border border-color-89 dark:border-color-47 bg-color-F6 dark:bg-color-47 rounded-xl modal-toggle">
                            {{ __('Cancel') }}</a>
                        <a href="javascript: void(0)"
                            class="font-Figtree text-white font-semibold text-15 py-[11px] px-[30px] modal-toggle bg-color-DFF rounded-xl delete-image">
                            {{ __('Yes, Delete') }} </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script>
    'use strict';
    var csrf = '{{ csrf_token() }}';
    </script>
    <script src="{{ asset('public/assets/js/user/img-to-video.min.js') }}"></script>
    <script src="{{ asset('Modules/OpenAI/Resources/assets/js/customer/video.min.js') }}"></script>
@endsection

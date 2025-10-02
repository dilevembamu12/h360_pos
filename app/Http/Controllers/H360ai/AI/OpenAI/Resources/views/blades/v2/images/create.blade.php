@extends('layouts.user_master')
@section('page_title', __('Image Maker'))
@section('content')
    <div class="w-[68.9%] 7xl:w-[83.9%] dark:bg-[#292929] flex flex-col flex-1 border-l dark:border-[#474746] border-color-DF h-screen">
        <div class="subscription-main flex xl:flex-row flex-col xl:h-full subscription-main md:overflow-auto sidebar-scrollbar md:h-screen overflow-x-hidden">
            <div class="bg-[#F6F3F2] dark:bg-[#3A3A39] xl:w-[401px] 5xl:w-[474px] sidebar-scrollbar xl:overflow-auto xl:h-screen pt-14">
                @include('openai::blades.v2.images.form')
            </div>
            <div class="grow xl:px-0 px-5 xl:pt-[74px] pt-5 9xl:pb-[46px] pb-24 dark:bg-[#292929] xl:overflow-x-hidden xl:overflow-y-auto sidebar-scrollbar h-screen xl:w-1/2">
                @include('openai::blades.v2.images.output')
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script>
        'use strict';
        var PROMT_URL = "{{ !empty($promtUrl) ? $promtUrl : '' }}";
        $("#tooltip-2").tooltip();

        var csrf = '{{ csrf_token() }}';
        var url = '{{ route("user.image.store") }}';
        var imageCreateUrl = '{{ route("user.image.store") }}';

    </script>
    <script src="{{ asset('public/dist/js/custom/validation.min.js') }}"></script>
    <script src="{{ asset('Modules/OpenAI/Resources/assets/js/customer/image/script.min.js') }}"></script>
@endsection

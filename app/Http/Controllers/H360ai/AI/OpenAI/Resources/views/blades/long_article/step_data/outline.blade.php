@if (isset($heading) && !empty($outlinesData))
    {!! $heading !!}
@endif

@foreach ($outlinesData as $key => $outlines)
    <label for="LongArticleOutline-{{ $key }}" class="radio-card pb-6">
        <input class="hidden ItemClicked" type="radio" name="radio_value" id="LongArticleOutline-{{ $key }}" data-value="{{ json_encode($outlines) }}"/>
        <div class="card-content-wrapper bg-white dark:bg-[#3A3A39] mt-3 relative px-[14px] lg:px-5 py-[14px] lg:py-4 border-2 border-[#DFDFDF] dark:border-[#474746] rounded-xl">
            <span class="check-icon "></span>
            <div>
                <ul class="ml-5 !list-disc">
                    @foreach ($outlines as $outline)
                        <li class="text-color-14 dark:text-white text-16 leading-[26px] font-normal font-Figtree wrap-anywhere">{{ $outline }}</li>
                    @endforeach
                </ul>
                <p class="text-color-89 text-14 font-medium font-Figtree wrap-anywhere mt-1">{{ count($outlines) }} {{ __('headings in the outline') }}</p>
            </div>
        </div>
    </label>
@endforeach
@if (isset($heading) && !empty($titles))
    {!! $heading !!}
@endif

@foreach ($titles as $key => $title)
    <label for="LongArticleTitle-{{ $key }}" class="radio-card pb-6">
        <input class="hidden ItemClicked" type="radio" name="radio_value" id="LongArticleTitle-{{ $key }}" data-value="{{ $title }}"/>
        <div class="card-content-wrapper bg-white dark:bg-[#3A3A39] mt-3 relative px-[14px] lg:px-5 py-[14px] lg:py-4 border-2 border-[#DFDFDF] dark:border-[#474746] rounded-xl">
            <span class="check-icon "></span>
            <div class="card-content">
            <p class="text-color-14 dark:text-white text-18 font-normal font-Figtree wrap-anywhere">
                {{ $title }}
            </p>
            </div>
        </div>
    </label>
@endforeach

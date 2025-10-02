
@if (!is_null($articleData?->article_value))
    <h1 class="text-color-14 text-24 font-semibold font-RedHat dark:text-white">{{ $articleData->title }}</h1>

    @php
        $result = $articleData->content;
    @endphp
    {!! preg_replace('/\*\*(.*?)\*\*/', '<br><br><h1 class="text-color-14 text-24 font-semibold font-RedHat dark:text-white">$1</h1>', $result) !!}

    <div class="mt-3">
        <a href="{{ route('user.long_article.edit', $articleData->id) }}" class=" magic-bg rounded-lg text-[14px] text-white justify-center items-center font-medium py-[9px] flex text-center cursor-pointer font-Figtree w-max px-6 whitespace-nowrap gap-2">
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
@else
    <div class="mt-3">
        <h1 class="text-color-14 text-24 font-semibold font-RedHat dark:text-white">{{ __('No article generated.') }}</h1>
    </div>
@endif

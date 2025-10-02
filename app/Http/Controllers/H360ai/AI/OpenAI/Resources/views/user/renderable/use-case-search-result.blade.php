@if(count($useCases) > 0)
<div class="grid 9xl:grid-cols-5 5xl:grid-cols-4 4xl:grid-cols-3 xs:grid-cols-2 grid-cols-1 gap-4 xl:gap-[23px] pb-8">
    @foreach($useCases as $useCase)
        <div class="parent-template relative bg-white dark:bg-[#3A3A39] border-design-2 rounded-xl border border-color-DF dark:border-[#474746] {{ in_array($useCase->id, $userUseCaseFavorites) ? 'favorated' : 'non-favorite' }}" id="{{ $useCase->id }}">
            <div class="tab-content-{{$useCase->id}}">
                <a href="{{ route('user.template', $useCase->slug) }}">
                    <div class="p-4 xl:p-[30px] xl:pb-6">
                        <img class="rounded-full w-12 h-12 neg-transition-scale" src="{{ asset($useCase->fileUrl()) }}" alt="{{ __('Image') }}">
                        <p class="text-color-14 dark:text-white font-semibold text-18 mt-7 break-words line-clamp-double">
                            {{ trimWords($useCase->name, 55) }}
                        </p>
                        <p class="text-13 xl:text-14 text-color-14 dark:text-color-DF font-light mt-2.5 break-words font-Figtree">{{ trimWords($useCase->description,85)}}</p>
                    </div>
                </a>
                <a href="javascript: void(0)" class="absolute top-0 right-0 p-4 dynamic-use-case toggle-favorite favorite-use-case-{{ $useCase->id }} " data-use-case-id="{{ $useCase->id }}" data-is-favorite="{{ in_array($useCase->id, $userUseCaseFavorites) ? 'true' : 'false' }}">
                    <span class="flex items-center justify-center">
                        @if (in_array($useCase->id, $userUseCaseFavorites))
                            <svg width="19" height="18" viewBox="0 0 19 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M4.06383 17.3731C3.62909 17.5965 3.13682 17.206 3.22435 16.7071L4.15779 11.3864L0.195168 7.6102C-0.175161 7.25729 0.0165395 6.61204 0.512652 6.54156L6.02344 5.7587L8.48057 0.891343C8.70191 0.452886 9.3015 0.452886 9.52285 0.891343L11.98 5.7587L17.4908 6.54156C17.9869 6.61204 18.1786 7.25729 17.8083 7.6102L13.8456 11.3864L14.7791 16.7071C14.8666 17.206 14.3743 17.5965 13.9396 17.3731L9.00171 14.8351L4.06383 17.3731Z" fill="url(#paint0_linear_301_431-{{ $useCase->id }})"/>
                                <defs>
                                <linearGradient id="paint0_linear_301_431-{{ $useCase->id }}" x1="11.7048" y1="15.3605" x2="6.10185" y2="1.87361" gradientUnits="userSpaceOnUse">
                                <stop stop-color="#E60C84"/>
                                <stop offset="1" stop-color="#FFCF4B"/>
                                </linearGradient>
                                </defs>
                            </svg>
                        @else
                            <svg width="19" height="18" viewBox="0 0 19 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M3.22435 16.7071C3.13682 17.206 3.62909 17.5965 4.06383 17.3731L9.00171 14.8351L13.9396 17.3731C14.3743 17.5965 14.8666 17.206 14.7791 16.7071L13.8456 11.3864L17.8083 7.6102C18.1786 7.25729 17.9869 6.61204 17.4908 6.54156L11.98 5.7587L9.52285 0.891343C9.3015 0.452886 8.70191 0.452886 8.48057 0.891343L6.02344 5.7587L0.512652 6.54156C0.0165395 6.61204 -0.175161 7.25729 0.195168 7.6102L4.15779 11.3864L3.22435 16.7071ZM8.74203 13.5929L4.59556 15.7241L5.37659 11.2722C5.41341 11.0623 5.34418 10.8474 5.1935 10.7038L1.92331 7.58745L6.48215 6.93983C6.67061 6.91305 6.83516 6.79269 6.92406 6.61658L9.00171 2.50096L11.0794 6.61658C11.1683 6.79269 11.3328 6.91305 11.5213 6.93983L16.0801 7.58745L12.8099 10.7038C12.6592 10.8474 12.59 11.0623 12.6268 11.2722L13.4079 15.7241L9.26139 13.5929C9.0976 13.5088 8.90582 13.5088 8.74203 13.5929Z" fill="#898989"/>
                            </svg>
                        @endif
                    </span>
                </a>
            </div>
            <div class="spinner favorite-template-loader"></div>
        </div>
    @endforeach
</div>
@else
<div class="xl:flex justify-center items-center w-full">
    <p class="text-color-14 dark:text-white font-semibold text-18 mt-7 text-center">{{ __('No templates found.') }}</p>
</div>
@endif

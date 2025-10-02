<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18" fill="none">
    <g clip-path="url(#clip0_4514_3509)">
    <path d="M13.9714 7.00665C13.8679 6.84578 13.6901 6.75015 13.5 6.75015H9.56255V0.562738C9.56255 0.297241 9.37693 0.0677446 9.11706 0.0126204C8.85269 -0.0436289 8.59394 0.0924942 8.48594 0.334366L3.986 10.4592C3.90838 10.6325 3.92525 10.835 4.02875 10.9936C4.13225 11.1533 4.31 11.2501 4.50012 11.2501H8.43757V17.4375C8.43757 17.703 8.62319 17.9325 8.88306 17.9876C8.92244 17.9955 8.96181 18 9.00006 18C9.21831 18 9.42193 17.8729 9.51418 17.6659L14.0141 7.54102C14.0906 7.36664 14.076 7.1664 13.9714 7.00665Z" fill="url(#paint0_linear_4514_3509)"></path>
    </g>
    <defs>
    <linearGradient id="paint0_linear_4514_3509" x1="10.5204" y1="15.7845" x2="2.32033" y2="5.3758" gradientUnits="userSpaceOnUse">
    <stop offset="0" stop-color="#E60C84"></stop>
    <stop offset="1" stop-color="#FFCF4B"></stop>
    </linearGradient>
    <clipPath id="clip0_4514_3509">
    <rect width="18" height="18" fill="white"></rect>
    </clipPath>
    </defs>
</svg>

<p class="text-color-14 dark:text-white font-Figtree font-normal text-[14px]">{{ __('Credits Balance:') }} <span class="total-word-left font-semibold text-[14px] text-[#E22861] dark:text-[#FCCA19]">{{ $wordLimit == -1 ? __('Unlimited') : max(0, $wordLeft) }}</span> {{ __('words left') }}</p>

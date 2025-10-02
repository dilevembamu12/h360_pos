
<div>
    @php 
        $totalBreadCrumbs = count($breadCrumbs); 
    @endphp
    <nav class="flex my-4 px-5">
        <ol class="inline-flex items-center space-x-2">
            <ol class="inline-flex items-center space-x-2">
                @php 
                    $totalBreadCrumbs = count($breadCrumbs); 
                @endphp
                @foreach ($breadCrumbs as $key => $breadCrumb)
                    @if ($loop->first) 
                        <li class="inline-flex items-center">
                            <a href="javascript:void(0)" @if($totalBreadCrumbs > 1) data-folder-id="{{ $breadCrumb['id'] }}" data-parent-id="{{ $breadCrumb['parent_folder'] }}" onclick="fetchFolderData(this)" @endif 
                                class="inline-flex gap-2 items-center text-sm font-medium text-gray-700 hover:text-blue-600 dark:text-gray-400 dark:hover:text-white">
                                <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M8.59851 2.625H12.0828C12.3259 2.62497 12.5665 2.67563 12.789 2.77374C13.0115 2.87185 13.2111 3.01526 13.3751 3.19482C13.5391 3.37438 13.6639 3.58614 13.7415 3.81661C13.8191 4.04708 13.8478 4.29119 13.8258 4.53338L13.2684 10.6584C13.2289 11.0932 13.0283 11.4975 12.7059 11.792C12.3836 12.0865 11.9629 12.2498 11.5263 12.25H2.47089C2.0343 12.2498 1.61354 12.0865 1.29121 11.792C0.968884 11.4975 0.768274 11.0932 0.728762 10.6584L0.171387 4.53338C0.1341 4.12806 0.239982 3.72249 0.470637 3.38713L0.436512 2.625C0.436512 2.16087 0.620886 1.71575 0.949075 1.38756C1.27726 1.05937 1.72238 0.875 2.18651 0.875H5.39951C5.8636 0.875099 6.30865 1.05954 6.63676 1.38775L7.36126 2.11225C7.68937 2.44046 8.13442 2.6249 8.59851 2.625ZM1.31676 2.73C1.50401 2.66175 1.70526 2.625 1.91526 2.625H6.63676L6.01814 2.00637C5.85408 1.84227 5.63156 1.75005 5.39951 1.75H2.18651C1.9573 1.74996 1.73722 1.83986 1.5736 2.00038C1.40997 2.16089 1.31587 2.3792 1.31151 2.60837L1.31676 2.73Z" fill="#898989"/>
                                </svg>
    
                                <span class="text-color-47 dark:text-white text-15 font-Figtree font-normal">{{ ucfirst($breadCrumb['name']) }}</span>
                            </a>
                        </li>
                    @endif
                    @if ($key == 1 && $totalBreadCrumbs > 2)
                        <li>
                            <div class="flex items-center gap-2 relative">
                                <svg class="text-color-47 dark:text-color-DF" width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M4.18306 9.83263C4.42714 10.0558 4.82286 10.0558 5.06694 9.83263L8.81694 6.40406C9.06102 6.1809 9.06102 5.8191 8.81694 5.59594L5.06694 2.16737C4.82286 1.94421 4.42714 1.94421 4.18306 2.16737C3.93898 2.39052 3.93898 2.75233 4.18306 2.97549L7.49112 6L4.18306 9.02451C3.93898 9.24767 3.93898 9.60948 4.18306 9.83263Z" fill="currentColor"/>
                                </svg>
                                <a href="javascript:void(0)" class="text-color-47 dark:text-white text-15 font-Figtree font-normal" 
                                data-folder-id="{{ $breadCrumb['id'] }}" data-parent-id="{{ $breadCrumb['parent_folder'] }}" onclick="fetchFolderData(this)">
                                    <a href="javascript:void(0)" class="table-dropdown-click relative font-Figtree text-15 font-normal text-color-47 dark:text-color-DF">.........</a>
                                    <div class="absolute ltr:left-0 rtl:right-0 action-dropdown top-4 mt-2 w-[201px] border border-color-89 dark:border-color-47 rounded-lg bg-white dark:bg-[#333332] z-50 table-drop-body dropdown-shadow">
                                        <div class="my-2 flex flex-col gap-1.5">
                                            @foreach($breadCrumbs as $k => $sub)
                                                @if (!$loop->first && !$loop->last)
                                                    <a href="javascript:void(0)" class="flex justify-start items-center gap-2 text-14 font-normal text-color-14 dark:text-white font-Figtree px-4 py-[9px] hover:bg-color-F6 dark:hover:bg-[#3A3A39] text-left"
                                                    data-folder-id="{{ $breadCrumb['id'] }}" data-parent-id="{{ $breadCrumb['parent_folder'] }}" onclick="fetchFolderData(this)">
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
        </ol>
    </nav>

</div>

<div class="h-[286px] overflow-y-auto sidebar-scrollbar border-t border-t-color-DF dark:border-t-color-47 move-modal-sidebar">
    <div class="my-[15px] flex flex-col gap-1.5" id="folder-modal-view" data-next-page-url="{{ $folders->nextPageUrl() }}">
        {{-- if folder is not available then this section will be show --}}
        @forelse ($folders as $folder)
            @if ( !in_array($folder->id, $excludedFolderIds))
                <a href="javascript:void(0)" class="flex justify-start items-center gap-2.5 text-15 font-medium text-color-14 dark:text-white font-Figtree px-7 py-[9px] hover:bg-color-F6 dark:hover:bg-[#3A3A39] text-left"
                    data-folder-id="{{ $folder->id }}" data-parent-id="{{ $folder->parent_folder }}" onclick="fetchFolderData(this)">
                    <span class="w-[18px] h-[18px] text-color-47 dark:text-white">
                        <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M11.0552 3.375H15.535C15.8476 3.37497 16.1569 3.4401 16.443 3.56624C16.729 3.69238 16.9857 3.87676 17.1966 4.10762C17.4074 4.33848 17.5678 4.61076 17.6676 4.90707C17.7674 5.20339 17.8043 5.51725 17.776 5.82863L17.0594 13.7036C17.0086 14.2627 16.7506 14.7825 16.3362 15.1611C15.9218 15.5397 15.3808 15.7498 14.8195 15.75H3.17685C2.61552 15.7498 2.07455 15.5397 1.66013 15.1611C1.24571 14.7825 0.98778 14.2627 0.93698 13.7036L0.220355 5.82863C0.172414 5.30751 0.308548 4.78606 0.605105 4.35488L0.56123 3.375C0.56123 2.77826 0.798283 2.20597 1.22024 1.78401C1.6422 1.36205 2.21449 1.125 2.81123 1.125H6.94223C7.53892 1.12513 8.11112 1.36226 8.53298 1.78425L9.46448 2.71575C9.88634 3.13774 10.4585 3.37487 11.0552 3.375ZM1.69298 3.51C1.93373 3.42225 2.19248 3.375 2.46248 3.375H8.53298L7.73761 2.57963C7.52668 2.36863 7.24057 2.25006 6.94223 2.25H2.81123C2.51653 2.24995 2.23357 2.36553 2.0232 2.57191C1.81282 2.77829 1.69183 3.05898 1.68623 3.35362L1.69298 3.51Z" fill="currentColor"/>
                        </svg>
                    </span>
                    <p>{{ trimWords($folder->name, 50) }}</p>
                </a>
            @endif
        @empty
            <div class="flex justify-start items-center gap-2.5 text-15 font-medium text-color-14 dark:text-white font-Figtree px-7 py-[9px] text-left" id="no-folder-modal">
                <span class="w-[18px] h-[18px] text-color-DF dark:text-color-47">
                    <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M11.0552 3.375H15.535C15.8476 3.37497 16.1569 3.4401 16.443 3.56624C16.729 3.69238 16.9857 3.87676 17.1966 4.10762C17.4074 4.33848 17.5678 4.61076 17.6676 4.90707C17.7674 5.20339 17.8043 5.51725 17.776 5.82863L17.0594 13.7036C17.0086 14.2627 16.7506 14.7825 16.3362 15.1611C15.9218 15.5397 15.3808 15.7498 14.8195 15.75H3.17685C2.61552 15.7498 2.07455 15.5397 1.66013 15.1611C1.24571 14.7825 0.98778 14.2627 0.93698 13.7036L0.220355 5.82863C0.172414 5.30751 0.308548 4.78606 0.605105 4.35488L0.56123 3.375C0.56123 2.77826 0.798283 2.20597 1.22024 1.78401C1.6422 1.36205 2.21449 1.125 2.81123 1.125H6.94223C7.53892 1.12513 8.11112 1.36226 8.53298 1.78425L9.46448 2.71575C9.88634 3.13774 10.4585 3.37487 11.0552 3.375ZM1.69298 3.51C1.93373 3.42225 2.19248 3.375 2.46248 3.375H8.53298L7.73761 2.57963C7.52668 2.36863 7.24057 2.25006 6.94223 2.25H2.81123C2.51653 2.24995 2.23357 2.36553 2.0232 2.57191C1.81282 2.77829 1.69183 3.05898 1.68623 3.35362L1.69298 3.51Z" fill="currentColor"/>
                    </svg>
                </span>
                <p> {{ __('No folders') }}</p>
            </div>
        @endforelse
    </div>
    <div class="loader-template hidden mx-auto items-center dark:bg-color-3A absolute w-full h-full top-0 flex flex-col justify-center !bg-opacity-50 bg-white rounded-xl drive-content-loader">
        <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" width="72"
        height="72" viewBox="0 0 72 72" fill="none">
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
    </div>
</div>

<script src="{{ asset('Modules/OpenAI/Resources/assets/js/customer/drive-modal.min.js') }}"></script>
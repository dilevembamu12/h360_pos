<div>
    @if ($model != 'openai')
        <div class="flex flex-col mt-6">
            <label class="font-normal text-14 text-color-2C dark:text-white"
                for="">{{ __('Image To Image') }}</label>
                <input
                class="w-full cursor-pointer rounded-xl border border-color-89 dark:border-color-47 px-3 file:-mx-3 file:cursor-pointer file:overflow-hidden file:rounded-none file:border-0 file:border-solid file:border-inherit dark:file:!bg-[#474746] file:bg-color-DF file:px-3 file:py-4 file:h-16 h-12 bg-white dark:bg-[#333332] file:[border-inline-end-width:1px] file:[margin-inline-end:0.75rem] file:text-color-14 dark:file:text-white form-control text-color-14 dark:text-white file:transition-none focus:outline-none focus:dark:!border-color-47"
                name="image" id="file_input" type="file" />
        </div>
    @endif
    
    @if ($option->{$model . '_variant'} || $option->{$model . '_resulation'})
        <div class="grid grid-cols-2 gap-6 justify-between mt-6">
            @if ($option->{$model . '_variant'})
                <div>
                    <div class="font-normal custom-dropdown-arrow text-14 text-color-2C dark:text-white">
                        <label>{{ __('Number of Variants') }}</label>
                        <select
                            class="selectg block w-full text-base leading-6 font-medium text-color-2C bg-white bg-clip-padding bg-no-repeat dark:bg-[#333332] rounded-xl dark:rounded-2xl m-0 focus:text-color-2C focus:bg-white focus:border-color-89 focus:outline-none"
                            id="variant">
                            @foreach ( processPreferenceData($option->{$model . '_variant'}) as $key => $data )
                                <option value="{{ $data }}" {{ $key == 0 ? 'selected' : '' }}> {{ $data }} </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            @endif
            @if ($option->{$model . '_resulation'})
                <div>
                    <div class="font-normal custom-dropdown-arrow text-14 text-color-2C dark:text-white">
                        <label class="">{{ __('Resolution') }}</label>
                        <select
                            class="selectg block w-full text-base leading-6 font-medium text-color-2C bg-white bg-clip-padding bg-no-repeat dark:bg-[#333332] rounded-xl dark:rounded-2xl m-0 focus:text-color-2C focus:bg-white focus:border-color-89 focus:outline-none"
                            id="size">
                            @foreach (sortResolution(processPreferenceData($option->{$model . '_resulation'})) as $key => $data )
                                <option value="{{ $data }}" {{ $key == 0 ? 'selected' : '' }} {{ !subscription('isAdminSubscribed') && !subscription('isValidResolution', $userId, $data) ? 'disabled' : ''  }}> {{ $data }} </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            @endif
        </div>
    @endif
    
    @if ($option->{$model . '_artStyle'} || $option->{$model . '_lightingStyle'})
        <div class="grid grid-cols-2 gap-6 justify-between mt-6">
            @if ($option->{$model . '_artStyle'})
                <div>
                    <div>
                        <div class="font-normal custom-dropdown-arrow text-14 text-color-2C dark:text-white">
                            <label>{{ __('Image Style') }}</label>
                            <select class="selectg block w-full text-base leading-6 font-medium text-color-2C bg-white bg-clip-padding bg-no-repeat dark:bg-[#333332] rounded-xl dark:rounded-2xl m-0 focus:text-color-2C focus:bg-white focus:border-color-89 focus:outline-none" id="art-style">
                                @foreach ( processPreferenceData($option->{$model . '_artStyle'}) as $key => $data)
                                    <option value="{{ $data }}" {{ $key == 0 ? 'selected' : '' }}> {{ $data }} </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            @endif
            @if ($option->{$model . '_lightingStyle'})
                <div class="font-normal custom-dropdown-arrow text-14 text-color-2C dark:text-white">
                    <label>{{ __('Lighting Effects') }}</label>
                    <select
                        class="selectg block w-full text-base leading-6 font-medium text-color-2C bg-white bg-clip-padding bg-no-repeat rounded-xl dark:bg-[#333332] dark:rounded-2xl m-0 focus:text-color-2C focus:bg-white focus:border-color-89 focus:outline-none" id="ligting-style">
                            @foreach ( processPreferenceData($option->{$model . '_lightingStyle'}) as $key => $data)
                                <option value="{{ $data }}" {{ $key == 0 ? 'selected' : '' }}> {{ $data }} </option>
                            @endforeach
                    </select>
                </div>
            @endif
        </div>
    @endif
</div>

<script src="{{ asset('Modules/OpenAI/Resources/assets/js/image-tom-select.min.js') }}"></script>
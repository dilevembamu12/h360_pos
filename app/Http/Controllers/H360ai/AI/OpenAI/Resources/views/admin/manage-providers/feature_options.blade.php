
@extends('admin.layouts.app')
@section('page_title', __('Provider Options'))

@section('content')

    <!-- Main content -->
    <div class="col-sm-12" id="company-settings-container">
        <div class="card">
            <div class="card-body row">
                <div class="col-lg-3 col-12 z-index-10 pe-0 ps-0 ps-md-3">
                    @include('admin.layouts.includes.feature_menu')
                </div>
                <div class="col-lg-9 col-12 ps-0">
                    <div class="card card-info shadow-none mb-0">
                        <div class="card-header p-t-20 border-bottom">
                            <h5>{{ __(':x Options', ['x' => ucwords($providerName)]) }}</h5>
                            <div class="card-header-right">

                            </div>
                        </div>
                        

                        <div class="card-body">
                            @if ($featureOptions)
                            <div class="d-flex justify-content-start alert alert-warning">
                                <b>{{ __('Fields left blank or has single value won\'t be appear on the user panel.') }}</b>
                            </div>
                            @endif
                            <form method="post" action="{{ route('admin.features.provider_manage', [$featureName, $providerName]) }}" id="aiSettings">
                                @csrf
                                <div class="tab-content p-0 box-shadow-unset" id="topNav-v-pills-tabContent">

                                    @if (isset($fields) && !empty($fields))
                                        @foreach ($featureOptions as $option)
                                            @foreach ($fields as $field)
                                                @if ($option['name'] == $field['name'])

                                                    @if($option['type'] == 'checkbox')
                                                        <div class="form-group row">
                                                            <label for="rating"
                                                                class="col-sm-2 control-label text-left">{{ $field['label'] }}</label>
                                                            <div class="col-9 d-flex mt-neg-2">
                                                                <div class="ltr:me-3 rtl:ms-3">
                                                                    <div class="switch switch-bg d-inline m-r-10">
                                                                        <input type="checkbox" name="{{ $field['name'] }}"
                                                                            class="checkActivity" id="{{ $field['name'] }}"
                                                                             {{ $field['value'] == 'on' ? 'checked' : '' }} >
                                                                        <label for="{{ $field['name'] }}" class="cr"></label>
                                                                    </div>
                                                                </div>
                                                                <div class="mt-2">
                                                                    <span>{{ __('Enable :x for :y generation.', ['x' => ucwords($providerName), 'y' => ucwords($featureName)]) }}</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif
                                                    @if ($option['type'] == 'dropdown')
                                                        <div class="form-group row">
                                                            <div class="col-12">
                                                                <label for="{{ $option['name'] . '[]' }}" class="control-label">{{ $option['label'] }}</label>
                                                                <select class="form-control select2 inputFieldDesign sl_common_bx @if($option['label'] == 'Languages') language-tags @endif" name="{{ $option['name'] . '[]' }}" multiple @if(isset($option['required']) && $option['required']) required @endif>
                                                                    @foreach ($option['value'] as $value)
                                                                        <option value="{{ $value }}" {{ in_array($value, $field['value']) ? 'selected' : '' }}> {{ $value }} </option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                    @endif

                                                    @if ($option['type'] == 'number')
                                                        <div class="form-group row">
                                                            <div class="col-12">
                                                                <label for="{{ $option['name'] }}" class="control-label">{{ $option['label'] }}</label>
                                                                <input class="form-control inputFieldDesign positive-int-number" type="{{ $option['type'] }}" name="{{ $option['name'] }}"
                                                                    @if(isset($option['required']) && $option['required']) required @endif
                                                                    @if (isset($option['min'])) min="{{ $option['min'] }}" data-min="{{ __('The value must be :x than or equal to :y', ['x' => __('greater'), 'y' => $option['min']]) }}" @endif
                                                                    @if (isset($option['max'])) max="{{ $option['max'] }}" data-max="{{ __('The value must be :x than or equal to :y.', ['x' => __('less'), 'y' => $option['max']]) }}" @endif
                                                                    id="{{ $option['name'] }}" value="{{ $field['value'] }}" >
                                                            </div>
                                                        </div>
                                                    @endif
                                                    @if ($option['type'] == 'slider')
                                                        <div class="form-group row">
                                                            <div class="col-12">
                                                                <label for="{{ $option['name'] }}" class="control-label">{{ $option['label'] }}</label>
                                                                <input class="form-control inputFieldDesign positive-int-number" type="number" name="{{ $option['name'] }}"
                                                                    @if(isset($option['required']) && $option['required']) required @endif
                                                                    @if (isset($option['min'])) min="{{ $option['min'] }}" data-min="{{ __('The value must be :x than or equal to :y', ['x' => __('greater'), 'y' => $option['min']]) }}" @endif
                                                                    @if (isset($option['max'])) max="{{ $option['max'] }}" data-max="{{ __('The value must be :x than or equal to :y.', ['x' => __('less'), 'y' => $option['max']]) }}" @endif
                                                                    id="{{ $option['name'] }}" value="{{ $field['value'] }}" >
                                                            </div>
                                                        </div>
                                                    @endif

                                                    @if ($option['type'] == 'dropdown-with-image')
                                                        <div class="form-group row d-none">
                                                            <div class="col-12">
                                                                <label for="{{ $option['name'] . '[]' }}" class="control-label">{{ $option['label'] }}</label>
                                                                <select class="form-control select2 imageStyle inputFieldDesign sl_common_bx" name="{{ $option['name'] . '[]' }}" multiple>
                                                                    @foreach ($option['value'] as $value)
                                                                        <option value="{{ json_encode($value) }}" data-image="{{ objectStorage()->url($value['url']) }}" selected> {{ $value['label'] }} </option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                    @endif

                                                @endif
                                            @endforeach
                                        @endforeach
                                    @else
                                        @foreach ($featureOptions as $option)

                                            @if($option['type'] == 'checkbox')
                                                <div class="form-group row">
                                                    <label for="rating"
                                                        class="col-sm-2 control-label text-left">{{ $option['label'] }}</label>
                                                    <div class="col-9 d-flex mt-neg-2">
                                                        <div class="ltr:me-3 rtl:ms-3">
                                                            <div class="switch switch-bg d-inline m-r-10">
                                                                <input type="checkbox" name="{{ $option['name'] }}"
                                                                    class="checkActivity" id="{{ $option['name'] }}" {{ $option['value'] == 'on' ? 'checked' : '' }} >
                                                                <label for="{{ $option['name'] }}" class="cr"></label>
                                                            </div>
                                                        </div>
                                                        <div class="mt-2">
                                                            <span>{{ __('Enable :x for :y generation.', ['x' => ucwords($providerName), 'y' => ucwords($featureName)]) }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif

                                            @if ($option['type'] == 'dropdown')
                                                <div class="form-group row">
                                                    <div class="col-12">
                                                        <label for="{{ $option['name'] }}" class="control-label">{{ $option['label'] }}</label>
                                                        <select class="form-control select2 inputFieldDesign sl_common_bx @if($option['label'] == 'Languages') language-tags @endif"
                                                            name="{{ $option['name'] . '[]' }}" multiple>
                                                            @foreach ($option['value'] as $value)
                                                                <option value="{{ $value }}" selected> {{ $value }} </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            @endif

                                            @if ($option['type'] == 'number')
                                                <div class="form-group row">
                                                    <div class="col-12">
                                                        <label for="{{ $option['name'] }}" class="control-label">{{ $option['label'] }}</label>
                                                        <input class="form-control inputFieldDesign positive-int-number" type="{{ $option['type'] }}" name="{{ $option['name'] }}"
                                                            @if(isset($option['required']) && $option['required']) required @endif
                                                            @if (isset($option['min'])) min="{{ $option['min'] }}" data-min="{{ __('The value must be :x than or equal to :y', ['x' => __('greater'), 'y' => $option['min']]) }}" @endif
                                                            @if (isset($option['max'])) max="{{ $option['max'] }}" data-max="{{ __('The value must be :x than or equal to :y.', ['x' => __('less'), 'y' => $option['max']]) }}" @endif
                                                            id="{{ $option['name'] }}" value="{{ $option['value'] }}" >
                                                    </div>
                                                </div>
                                            @endif
                                            
                                            @if ($option['type'] == 'dropdown-with-image')
                                                <div class="form-group row d-none">
                                                    <div class="col-12">
                                                        <label for="{{ $option['name'] . '[]' }}" class="control-label">{{ $option['label'] }}</label>
                                                        <select class="form-control select2 imageStyle inputFieldDesign sl_common_bx" name="{{ $option['name'] . '[]' }}" multiple>
                                                            @foreach ($option['value'] as $value)
                                                                <option value="{{ json_encode($value) }}" data-image="{{ objectStorage()->url($value['url']) }}" selected> {{ $value['label'] }} </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            @endif
                                            
                                            @if ($option['type'] == 'slider')
                                                <div class="form-group row">
                                                    <div class="col-12">
                                                        <label for="{{ $option['name'] }}" class="control-label">{{ $option['label'] }}</label>
                                                        <input class="form-control inputFieldDesign positive-int-number" type="number" name="{{ $option['name'] }}"
                                                            @if(isset($option['required']) && $option['required']) required @endif
                                                            @if (isset($option['min'])) min="{{ $option['min'] }}" data-min="{{ __('The value must be :x than or equal to :y', ['x' => __('greater'), 'y' => $option['min']]) }}" @endif
                                                            @if (isset($option['max'])) max="{{ $option['max'] }}" data-max="{{ __('The value must be :x than or equal to :y.', ['x' => __('less'), 'y' => $option['max']]) }}" @endif
                                                            id="{{ $option['name'] }}" value="{{ $option['value'] }}">
                                                    </div>
                                                </div>
                                            @endif

                                        @endforeach
                                    @endif
                                <div class="footer py-0">
                                    <div class="form-group row">
                                        <label for="btn_save" class="col-sm-3 control-label"></label>
                                        <div class="m-auto">
                                            <button type="submit"
                                                class="btn form-submit custom-btn-submit float-right package-submit-button"
                                                id="footer-btn">{{ __('Save') }}</button>
                                        </div>
                                    </div>
                                </div>
                                </div>
                            </form>

                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('mediamanager::image.modal_image')

@endsection

@section('js')
    <script src="{{ asset('Modules/OpenAI/Resources/assets/js/admin/feature-options.min.js') }}"></script>
    <script src="{{ asset('Modules/OpenAI/Resources/assets/js/feature-options.min.js') }}"></script>
    <script src="{{ asset('public/dist/js/custom/validation.min.js') }}"></script>
@endsection

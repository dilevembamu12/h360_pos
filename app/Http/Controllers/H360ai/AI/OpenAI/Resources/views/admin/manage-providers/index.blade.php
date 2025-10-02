
@extends('admin.layouts.app')
@section('page_title', __('Feature and providers'))
@section('css')
@endsection

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
                            <h5>{{ __('Providers') }}</h5>
                            <div class="card-header-right">

                            </div>
                        </div>
                        <div class="card-body">
                            @foreach ($providers as $key => $provider)
                                @php
                                    $details = $provider->description();  
                                @endphp
                                <div class="tab-pane" id="v-pills-" role="tabpanel" aria-labelledby="v-pills--tab">

                                    <div class="mx-1 mt-2 border">
                                        <div class="row my-3 px-1">
                                            <div class="row align-items-center justify-content-center">
                                                <div class="col-auto ml-3">
                                                    <img class="img-fluid rounded-circle w-40p" src="{{ file_exists($details['image'] ?? '') ? asset($details['image']) : asset('Modules/OpenAI/Resources/assets/image/ai_provider.jpg') }}" alt="Ai Provider">
                                                </div>
                                                <div class="col">
                                                    <div class="d-flex parent-title">
                                                        <h6 class="d-inline-block text-muted text-uppercase"><strong>{{ $details['title'] ?? '' }}</strong>
                                                                                                            </h6>
                                                    </div>
                                                    <p class="m-b-0 text-muted">{{ $details['description'] ?? '' }}</p>
                                                </div>
                                                <div class="col-auto text-right">
                                                    <a href="{{ route('admin.features.provider_manage', [$featureName, $provider->alias()]) }}" class="mt-3 text-c-blue mb-4 text-uppercase">{{ __('Manage') }}</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div> 
                                </div>
                            @endforeach
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    
@endsection

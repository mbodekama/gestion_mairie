<x-app-layout :title="__('Dashboard')">
    <div class="row g-3 mb-3">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="mb-1">{{ __('Welcome back, :name!', ['name' => auth()->user()->name]) }}</h4>
                    <p class="mb-0 text-600">{{ __("Here's what's happening with your account today.") }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-md-6 col-xxl-3">
            <div class="card h-md-100 ecommerce-card-min-width">
                <div class="card-header pb-0">
                    <h6 class="mb-0 mt-2 d-flex align-items-center">{{ __('Weekly Sales') }}<span class="ms-1 text-400" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __("Calculated according to last week's sales") }}"><span class="far fa-question-circle" data-fa-transform="shrink-1"></span></span></h6>
                </div>
                <div class="card-body d-flex flex-column justify-content-end">
                    <div class="row">
                        <div class="col">
                            <p class="font-sans-serif lh-1 mb-1 fs-5">$47K</p><span class="badge badge-subtle-success rounded-pill fs-11">+3.5%</span>
                        </div>
                        <div class="col-auto ps-0">
                            <div class="echart-bar-weekly-sales h-100"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xxl-3">
            <div class="card h-md-100">
                <div class="card-header pb-0">
                    <h6 class="mb-0 mt-2">{{ __('Total Order') }}</h6>
                </div>
                <div class="card-body d-flex flex-column justify-content-end">
                    <div class="row justify-content-between">
                        <div class="col-auto align-self-end">
                            <div class="fs-5 fw-normal font-sans-serif text-700 lh-1 mb-1">58.4K</div><span class="badge rounded-pill fs-11 bg-200 text-primary"><span class="fas fa-caret-up me-1"></span>13.6%</span>
                        </div>
                        <div class="col-auto ps-0 mt-n4">
                            <div class="echart-default-total-order" data-echarts='{"tooltip":{"trigger":"axis","formatter":"{b0} : {c0}"},"xAxis":{"data":["Week 4","Week 5","Week 6","Week 7"]},"series":[{"type":"line","data":[20,40,100,120],"smooth":true,"lineStyle":{"width":3}}],"grid":{"bottom":"2%","top":"2%","right":"0","left":"10px"}}' data-echart-responsive="true"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xxl-3">
            <div class="card h-md-100">
                <div class="card-body">
                    <div class="row h-100 justify-content-between g-0">
                        <div class="col-5 col-sm-6 col-xxl pe-2">
                            <h6 class="mt-1">{{ __('Market Share') }}</h6>
                            <div class="fs-11 mt-3">
                                <div class="d-flex flex-between-center mb-1">
                                    <div class="d-flex align-items-center"><span class="dot bg-primary"></span><span class="fw-semi-bold">Samsung</span></div>
                                    <div class="d-xxl-none">33%</div>
                                </div>
                                <div class="d-flex flex-between-center mb-1">
                                    <div class="d-flex align-items-center"><span class="dot bg-info"></span><span class="fw-semi-bold">Huawei</span></div>
                                    <div class="d-xxl-none">29%</div>
                                </div>
                                <div class="d-flex flex-between-center mb-1">
                                    <div class="d-flex align-items-center"><span class="dot bg-300"></span><span class="fw-semi-bold">Apple</span></div>
                                    <div class="d-xxl-none">20%</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-auto position-relative">
                            <div class="echart-market-share"></div>
                            <div class="position-absolute top-50 start-50 translate-middle text-1100 fs-7">26M</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xxl-3">
            <div class="card h-md-100">
                <div class="card-header d-flex flex-between-center pb-0">
                    <h6 class="mb-0">{{ __('Weather') }}</h6>
                    <div class="dropdown font-sans-serif btn-reveal-trigger">
                        <button class="btn btn-link text-600 btn-sm dropdown-toggle dropdown-caret-none btn-reveal" type="button" id="dropdown-weather-update" data-bs-toggle="dropdown" data-boundary="viewport" aria-haspopup="true" aria-expanded="false"><span class="fas fa-ellipsis-h fs-11"></span></button>
                        <div class="dropdown-menu dropdown-menu-end border py-2" aria-labelledby="dropdown-weather-update"><a class="dropdown-item" href="#!">{{ __('View') }}</a><a class="dropdown-item" href="#!">{{ __('Export') }}</a>
                            <div class="dropdown-divider"></div><a class="dropdown-item text-danger" href="#!">{{ __('Remove') }}</a>
                        </div>
                    </div>
                </div>
                <div class="card-body pt-2">
                    <div class="row g-0 h-100 align-items-center">
                        <div class="col">
                            <div class="d-flex align-items-center"><img class="me-3" src="{{ asset('assets/img/icons/weather-icon.png') }}" alt="" height="60" />
                                <div>
                                    <h6 class="mb-2">New York City</h6>
                                    <div class="fs-11 fw-semi-bold">
                                        <div class="text-warning">{{ __('Sunny') }}</div>{{ __('Precipitation: :value', ['value' => '50%']) }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-auto text-center ps-2">
                            <div class="fs-5 fw-normal font-sans-serif text-primary mb-1 lh-1">31&deg;</div>
                            <div class="fs-10 text-800">32&deg; / 25&deg;</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-0">
        <div class="col-lg-6 pe-lg-2 mb-3">
            <div class="card h-lg-100 overflow-hidden">
                <div class="card-header bg-body-tertiary">
                    <div class="row align-items-center">
                        <div class="col">
                            <h6 class="mb-0">{{ __('Running Projects') }}</h6>
                        </div>
                        <div class="col-auto text-center pe-x1">
                            <select class="form-select form-select-sm">
                                <option>{{ __('Working Time') }}</option>
                                <option>{{ __('Estimated Time') }}</option>
                                <option>{{ __('Billable Time') }}</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    @php
                        $projects = [
                            ['name' => 'Falcon', 'initial' => 'F', 'color' => 'primary', 'progress' => 38, 'time' => '12:50:00'],
                            ['name' => 'Reign', 'initial' => 'R', 'color' => 'success', 'progress' => 79, 'time' => '25:20:00'],
                            ['name' => 'Boots4', 'initial' => 'B', 'color' => 'info', 'progress' => 90, 'time' => '58:20:00'],
                            ['name' => 'Raven', 'initial' => 'R', 'color' => 'warning', 'progress' => 40, 'time' => '21:20:00'],
                            ['name' => 'Slick', 'initial' => 'S', 'color' => 'danger', 'progress' => 70, 'time' => '31:20:00'],
                        ];
                    @endphp
                    @foreach ($projects as $index => $project)
                        <div class="row g-0 align-items-center py-2 position-relative {{ $loop->last ? '' : 'border-bottom' }} border-200">
                            <div class="col ps-x1 py-1 position-static">
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-xl me-3">
                                        <div class="avatar-name rounded-circle bg-{{ $project['color'] }}-subtle text-dark"><span class="fs-9 text-{{ $project['color'] }}">{{ $project['initial'] }}</span></div>
                                    </div>
                                    <div class="flex-1">
                                        <h6 class="mb-0 d-flex align-items-center"><a class="text-800 stretched-link" href="#!">{{ $project['name'] }}</a><span class="badge rounded-pill ms-2 bg-200 text-primary">{{ $project['progress'] }}%</span></h6>
                                    </div>
                                </div>
                            </div>
                            <div class="col py-1">
                                <div class="row flex-end-center g-0">
                                    <div class="col-auto pe-2">
                                        <div class="fs-10 fw-semi-bold">{{ $project['time'] }}</div>
                                    </div>
                                    <div class="col-5 pe-x1 ps-2">
                                        <div class="progress bg-200 me-2" style="height: 5px;" role="progressbar" aria-valuenow="{{ $project['progress'] }}" aria-valuemin="0" aria-valuemax="100">
                                            <div class="progress-bar rounded-pill" style="width: {{ $project['progress'] }}%"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="card-footer bg-body-tertiary p-0"><a class="btn btn-sm btn-link d-block w-100 py-2" href="#!">{{ __('Show all projects') }}<span class="fas fa-chevron-right ms-1 fs-11"></span></a></div>
            </div>
        </div>
        <div class="col-lg-6 ps-lg-2 mb-3">
            <div class="card h-lg-100">
                <div class="card-header">
                    <div class="row flex-between-center">
                        <div class="col-auto">
                            <h6 class="mb-0">{{ __('Total Sales') }}</h6>
                        </div>
                        <div class="col-auto d-flex">
                            <select class="form-select form-select-sm select-month me-2">
                                <option value="0">{{ __('January') }}</option>
                                <option value="1">{{ __('February') }}</option>
                                <option value="2">{{ __('March') }}</option>
                                <option value="3">{{ __('April') }}</option>
                                <option value="4">{{ __('May') }}</option>
                                <option value="5">{{ __('Jun') }}</option>
                                <option value="6">{{ __('July') }}</option>
                                <option value="7">{{ __('August') }}</option>
                                <option value="8">{{ __('September') }}</option>
                                <option value="9">{{ __('October') }}</option>
                                <option value="10">{{ __('November') }}</option>
                                <option value="11">{{ __('December') }}</option>
                            </select>
                            <div class="dropdown font-sans-serif btn-reveal-trigger">
                                <button class="btn btn-link text-600 btn-sm dropdown-toggle dropdown-caret-none btn-reveal" type="button" id="dropdown-total-sales" data-bs-toggle="dropdown" data-boundary="viewport" aria-haspopup="true" aria-expanded="false"><span class="fas fa-ellipsis-h fs-11"></span></button>
                                <div class="dropdown-menu dropdown-menu-end border py-2" aria-labelledby="dropdown-total-sales"><a class="dropdown-item" href="#!">{{ __('View') }}</a><a class="dropdown-item" href="#!">{{ __('Export') }}</a>
                                    <div class="dropdown-divider"></div><a class="dropdown-item text-danger" href="#!">{{ __('Remove') }}</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body h-100 pe-0">
                    <div class="echart-line-total-sales h-100" data-echart-responsive="true"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-0">
        <div class="col-lg-6 col-xl-7 col-xxl-8 mb-3 pe-lg-2">
            <div class="card h-lg-100">
                <div class="card-body d-flex align-items-center">
                    <div class="w-100">
                        <h6 class="mb-3 text-800">{{ __('Using Storage') }} <strong class="text-1100">1775.06 MB</strong> {{ __('of 2 GB') }}</h6>
                        <div class="progress-stacked mb-3 rounded-3" style="height: 10px;">
                            <div class="progress" style="width: 43.72%;" role="progressbar" aria-valuenow="43.72" aria-valuemin="0" aria-valuemax="100">
                                <div class="progress-bar bg-progress-gradient border-end border-100 border-2"></div>
                            </div>
                            <div class="progress" style="width: 18.76%;" role="progressbar" aria-valuenow="18.76" aria-valuemin="0" aria-valuemax="100">
                                <div class="progress-bar bg-info border-end border-100 border-2"></div>
                            </div>
                            <div class="progress" style="width: 9.38%;" role="progressbar" aria-valuenow="9.38" aria-valuemin="0" aria-valuemax="100">
                                <div class="progress-bar bg-success border-end border-100 border-2"></div>
                            </div>
                            <div class="progress" style="width: 28.14%;" role="progressbar" aria-valuenow="28.14" aria-valuemin="0" aria-valuemax="100">
                                <div class="progress-bar bg-200"></div>
                            </div>
                        </div>
                        <div class="row fs-10 fw-semi-bold text-500 g-0">
                            <div class="col-auto d-flex align-items-center pe-3"><span class="dot bg-primary"></span><span>{{ __('Regular') }}</span><span class="d-none d-md-inline-block d-lg-none d-xxl-inline-block">(895MB)</span></div>
                            <div class="col-auto d-flex align-items-center pe-3"><span class="dot bg-info"></span><span>{{ __('System') }}</span><span class="d-none d-md-inline-block d-lg-none d-xxl-inline-block">(379MB)</span></div>
                            <div class="col-auto d-flex align-items-center pe-3"><span class="dot bg-success"></span><span>{{ __('Shared') }}</span><span class="d-none d-md-inline-block d-lg-none d-xxl-inline-block">(192MB)</span></div>
                            <div class="col-auto d-flex align-items-center"><span class="dot bg-200"></span><span>{{ __('Free') }}</span><span class="d-none d-md-inline-block d-lg-none d-xxl-inline-block">(576MB)</span></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6 col-xl-5 col-xxl-4 mb-3 ps-lg-2">
            <div class="card h-lg-100">
                <div class="bg-holder bg-card" style="background-image:url({{ asset('assets/img/icons/spot-illustrations/corner-1.png') }});"></div>
                <!--/.bg-holder-->
                <div class="card-body position-relative">
                    <h5 class="text-warning">{{ __('Running out of your space?') }}</h5>
                    <p class="fs-10 mb-0">{{ __('Your storage will be running out soon. Get more space and powerful productivity features.') }}</p><a class="btn btn-link fs-10 text-warning mt-lg-3 ps-0" href="#!">{{ __('Upgrade storage') }}<span class="fas fa-chevron-right ms-1" data-fa-transform="shrink-4 down-1"></span></a>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-12">
            <div class="card h-100">
                <div class="card-header d-flex flex-between-center bg-body-tertiary py-2">
                    <h6 class="mb-0">{{ __('Account') }}</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="avatar avatar-xl me-3">
                            <div class="avatar-name rounded-circle bg-primary-subtle text-primary">
                                <span>{{ Str::of(auth()->user()->name)->substr(0, 1)->upper() }}</span>
                            </div>
                        </div>
                        <div>
                            <h6 class="mb-0">{{ auth()->user()->name }}</h6>
                            <p class="mb-0 text-600 fs-10">{{ auth()->user()->email }}</p>
                        </div>
                    </div>
                    <p class="mb-0 text-600 fs-10">
                        {{ __('Member since :date', ['date' => auth()->user()->created_at->translatedFormat('d M Y')]) }}
                    </p>
                </div>
                <div class="card-footer bg-body-tertiary py-2 text-end">
                    <a class="btn btn-link btn-sm px-0 fw-medium" href="{{ route('profile.edit') }}">{{ __('Manage profile') }}<span class="fas fa-chevron-right ms-1 fs-11"></span></a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

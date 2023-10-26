@extends('layouts.main') 
@section('title', 'Dashboard')
@section('content')
    <!-- push external head elements to head -->
    @push('head')
        <link rel="stylesheet" href="{{ asset('plugins/weather-icons/css/weather-icons.min.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/owl.carousel/dist/assets/owl.carousel.min.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/owl.carousel/dist/assets/owl.theme.default.min.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/chartist/dist/chartist.min.css') }}">
    @endpush

    <div class="container-fluid">
    	<div class="row">
    		<!-- page statustic chart start -->
            <div class="col-lg-4 col-sm-12">
                <div class="card card-red text-white">
                    <div class="card-block">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <p class="mb-0">{{ __('Users')}}</p>
                            </div>
                            <div class="col-4 text-right">
                                <i class="fa fa-users f-30"></i>
                            </div>
                        </div>
                       <div>
                        <h3 class="mb-0">{{ count($users)}}</h3>
                       </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-sm-12">
                <div class="card card-blue text-white">
                    <div class="card-block">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <p class="mb-0">{{ __('Vendors')}}</p>
                            </div>
                            <div class="col-4 text-right">
                                <i class="fa fa-user f-30"></i>
                            </div>
                        </div>
                        <div>
                            <h3 class="mb-0">{{ count($vendors)}}</h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-sm-12">
                <div class="card card-green text-white">
                    <div class="card-block">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <p class="mb-0">{{ __('Logistics')}}</p>
                            </div>
                            <div class="col-4 text-right">
                                <i class="ik ik-user f-30"></i>
                            </div>
                        </div>
                        <div>
                            <h3 class="mb-0">{{ count($logistic)}}</h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-sm-12">
                <div class="card card-yellow text-white">
                    <div class="card-block">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <p class="mb-0">{{ __('Meals')}}</p>
                            </div>
                            <div class="col-4 text-right">
                                <i class="ik f-30">৳</i>
                            </div>
                        </div>
                        <div>
                            <h3 class="mb-0">{{ count($meals)}}</h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-sm-12">
                <div class="card card-green text-white">
                    <div class="card-block">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <p class="mb-0">{{ __('Orders')}}</p>
                            </div>
                            <div class="col-4 text-right">
                                <i class="ik ik-shopping-cart f-30"></i>
                            </div>
                        </div>
                        <div>
                            <h3 class="mb-0">{{ count($orders)}}</h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-sm-12">
                <div class="card card-red text-white">
                    <div class="card-block">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <p class="mb-0">{{ __('Pending Orders')}}</p>
                            </div>
                            <div class="col-4 text-right">
                                <i class="fas fa-cube f-30"></i>
                            </div>
                        </div>
                       <div>
                        <h4 class="mb-0">{{ count($pendingOrders)}}</h4>
                       </div>
                    </div>
                </div>
            </div>
            <!-- page statustic chart end -->
            <!-- product and new customar start -->
            <div class="col-lg-12 col-sm-12">
                <div class="card table-card">
                    <div class="card-header">
                        <h3>{{ __('Transaction History')}}</h3>
                        <div class="card-header-right">
                            <ul class="list-unstyled card-option">
                                <li><i class="ik ik-chevron-left action-toggle"></i></li>
                                <li><i class="ik ik-minus minimize-card"></i></li>
                                <li><i class="ik ik-x close-card"></i></li>
                            </ul>
                        </div>
                    </div>
                    <div class="card-block">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-center">{{ __('S/N')}}</th>
                                        <th class="text-center">{{ __('Reference')}}</th>
                                        <th class="text-center">{{ __('Business Name')}}</th>
                                        <th class="text-center">{{ __('Email')}}</th>
                                        {{-- <th class="text-center">{{ __('Account Type')}}</th> --}}
                                        <th class="text-center">{{ __('Amount')}}</th>
                                        <th class="text-center">{{ __('Payment Method')}}</th>
                                        <th class="text-center">{{ __('Status')}}</th>
                                        <th class="text-center">{{ __('Transacted Date')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $count = 0; @endphp
                                    @forelse ($transactions as $transaction)
                                        <tr>
                                            <td class="text-center">{{ __(++$count)}}</td>
                                            <td class="text-center">{{ __($transaction->reference)}}</td>
                                            <td class="text-center"><a href="/users/{{ __($transaction->owner->unique_id)}}">{{ __($transaction->owner->business_name ?? $transaction->owner->name )}}</a></td>
                                            <td class="text-center">{{ __($transaction->owner->email)}}</td>
                                            {{-- <td class="text-center">{{ __($transaction->owner->userRole->name)}}</td> --}}
                                            <td class="text-center">{{ __(number_format($transaction->amount))}} {{auth()->user()->currency()}}</td>
                                            <td class="text-center">{{ __($transaction->channel)}}</td>
                                            <td class="text-center">
                                                <span class="badge light badge-{{$transaction->status == 'pending' ? 'warning':'success' }} ">
                                                    {{ $transaction->status }}
                                                </span>
                                            </td>
                                            <td class="text-center">{{ __($transaction->created_at->diffForHumans())}}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="12" class="text-center">No Data Available</td></tr>
                                    @endforelse
                                    <x-slot name="pagination">
                                        {{ $transactions->render("pagination::bootstrap-4") }} 
                                    </x-slot>
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
            <!-- product and new customar end -->
            <!-- Application Sales start -->
            <div class="col-md-12">
                <div class="card table-card">
                    <div class="card-header">
                        <h3>{{ __('Withdrawal History')}}</h3>
                        <div class="card-header-right">
                            <ul class="list-unstyled card-option">
                                <li><i class="ik ik-chevron-left action-toggle"></i></li>
                                <li><i class="ik ik-minus minimize-card"></i></li>
                                <li><i class="ik ik-x close-card"></i></li>
                            </ul>
                        </div>
                    </div>
                    <div class="card-block p-b-0">
                        <div class="table-responsive scroll-widget">
                            <table class="table table-hover table-borderless mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-center">{{ __('S/N')}}</th>
                                        {{-- <th class="text-center">{{ __('Account Type')}}</th> --}}
                                        <th class="text-center">{{ __('User Name')}}</th>
                                        <th class="text-center">{{ __('User Balance')}}</th>
                                        <th class="text-center">{{ __('Requested Amount')}}</th>
                                        <th class="text-center">{{ __('Bank Details')}}</th>
                                        <th class="text-center">{{ __('Status')}}</th>
                                        <th class="text-center">{{ __('Requested Date')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $count = 0; @endphp
                                    @forelse ($withdrawals as $withdrawal)
                                        <tr>
                                            <td class="text-center">{{ __(++$count)}}</td>
                                            {{-- <td class="text-center">{{ __($withdrawal->owner->userRole->name)}}</td> --}}
                                            <td class="text-center"><a href="/users/{{ __($withdrawal->owner->unique_id)}}">{{ __($withdrawal->owner->business_name ?? $withdrawal->owner->name )}}</a></td>
                                            <td class="text-center">{{ __(number_format($withdrawal->owner->main_balance))}} {{auth()->user()->currency()}}</td>
                                            <td class="text-center">{{ __(number_format($withdrawal->amount))}} {{auth()->user()->currency()}}</td>
                                            <td class="text-center">
                                                <a href="#viewDetails{{$withdrawal->unique_id}}" data-toggle="modal" data-target="#viewDetails{{$withdrawal->unique_id}}" class="btn btn-primary">View Details</a>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge light badge-{{$withdrawal->status == 'failed' ? 'warning':'success' }} ">
                                                    {{ $withdrawal->status }}
                                                </span>
                                            </td>
                                            <td class="text-center">{{ __($withdrawal->created_at->diffForHumans())}}</td>
                                            <td class="text-center">
                                                <div class="table-actions">
                                                    <a data-toggle="modal" data-target="#deleteWithdrawal{{$withdrawal->unique_id}}"><i class="ik ik-trash-2"></i></a>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="12" class="text-center">No Data Available</td></tr>
                                    @endforelse
                                    <x-slot name="pagination">
                                        {{ $withdrawals->render("pagination::bootstrap-4") }} 
                                    </x-slot>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Application Sales end -->
    	</div>
    </div>
	<!-- push external js -->
    @push('script')
        <script src="{{ asset('plugins/owl.carousel/dist/owl.carousel.min.js') }}"></script>
        <script src="{{ asset('plugins/chartist/dist/chartist.min.js') }}"></script>
        <script src="{{ asset('plugins/flot-charts/jquery.flot.js') }}"></script>
        <!-- <script src="{{ asset('plugins/flot-charts/jquery.flot.categories.js') }}"></script> -->
        <script src="{{ asset('plugins/flot-charts/curvedLines.js') }}"></script>
        <script src="{{ asset('plugins/flot-charts/jquery.flot.tooltip.min.js') }}"></script>

        <script src="{{ asset('plugins/amcharts/amcharts.js') }}"></script>
        <script src="{{ asset('plugins/amcharts/serial.js') }}"></script>
        <script src="{{ asset('plugins/amcharts/themes/light.js') }}"></script>
       
        
        <script src="{{ asset('js/widget-statistic.js') }}"></script>
        <script src="{{ asset('js/widget-data.js') }}"></script>
        <script src="{{ asset('js/dashboard-charts.js') }}"></script>
        
    @endpush
@endsection
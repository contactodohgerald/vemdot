@extends('layouts.main')
@section('title', 'Profile')
@section('content')


<div class="container-fluid">
    {{-- page header section --}}
    <x-pageHeader header="Profile" />
    
    <div class="row">
        <div class="col-lg-4 col-md-5">
            <div class="card">
                <div class="card-body">
                    <div class="text-center">
                        <img src="{{asset($user->avatar ?? asset('default.png'))}}" style="object-fit: cover;" class="rounded-circle" width="150" />
                        <h4 class="card-title mt-10">{{ __($user->name)}}</h4>
                        <p class="card-subtitle">{{ __($user->userRole ? $user->userRole->name : null)}}</p>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-6 text-center">
                            <h6>Account Status</h6>
                            <p class="card-subtitle">{{ __($user->status)}}</p>
                        </div>

                        <div class="col-md-6 text-center">
                            <h6>KYC Status</h6>
                            <p class="card-subtitle">{{ __($user->kyc_status)}}</p>
                        </div>
                    </div>
                </div>
                <hr class="mb-0">
                <div class="card-body">
                    <small class="text-muted d-block pt-30">{{ __('Social Profile')}}</small>
                    <br/>
                    <button class="btn btn-icon btn-facebook"><i class="fab fa-facebook-f"></i></button>
                    <button class="btn btn-icon btn-twitter"><i class="fab fa-twitter"></i></button>
                    <button class="btn btn-icon btn-instagram"><i class="fab fa-instagram"></i></button>
                </div>
            </div>
        </div>
        <div class="col-lg-8 col-md-7">
            <div class="card">
                <ul class="nav nav-pills custom-pills" id="pills-tab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="pills-profile-tab" data-toggle="pill" href="#last-month" role="tab" aria-controls="pills-profile" aria-selected="false">{{ __('Profile')}}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="pills-timeline-tab" data-toggle="pill" href="#current-month" role="tab" aria-controls="pills-timeline" aria-selected="true">{{ __('Edit')}}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="pills-kyc-tab" data-toggle="pill" href="#previous-month" role="tab" aria-controls="pills-kyc" aria-selected="false">{{ __('KYC')}}</a>
                    </li>
                </ul>
                <div class="tab-content" id="pills-tabContent">
                    <div class="tab-pane fade show active" id="last-month" role="tabpanel" aria-labelledby="pills-profile-tab">
                        @include('pages.users.components.edit')
                    </div>
                    <div class="tab-pane fade" id="current-month" role="tabpanel" aria-labelledby="pills-timeline-tab">
                        @include('pages.users.components.settings')
                    </div>
                    <div class="tab-pane fade" id="previous-month" role="tabpanel" aria-labelledby="pills-kyc-tab">
                        @include('pages.users.components.details')
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

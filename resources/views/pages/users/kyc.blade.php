@extends('layouts.main')
@section('title', 'KYC Requests')
@section('content')

<!-- push external head elements to head -->
@push('head')
<link rel="stylesheet" href="{{ asset('plugins/DataTables/datatables.min.css') }}">
@endpush

<div class="container-fluid">
    {{-- page header section --}}
    <x-pageHeader header="KYC Requests" />

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header"><h3>{{ __('KYC Request')}}</h3></div>
                <div class="card-body">
                    <table id="data_table" class="table">
                        <thead>
                            <tr>
                                <th>{{ __('Id')}}</th>
                                <th class="nosort">{{ __('Image')}}</th>
                                <th>{{ __('Name')}}</th>
                                <th>{{ __('Email')}}</th>
                                <th>{{ __('Role')}}</th>
                                <th>{{ __('Status')}}</th>
                                <th class="nosort">{{ __('Action')}}</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{$count = 0}}
                            @forelse ($users as $user)
                                <tr>
                                    <td>{{ __(++$count)}}</td>
                                    <td>
                                        <img src="{{asset($user->avatar ?? asset('default.png'))}}" class="table-user-thumb" alt="{{ $user->name }}" id="view_kyc">
                                    </td>
                                    <td>{{ __($user->name)}}</td>
                                    <td>{{ __($user->email)}}</td>
                                    <td>{{ __($user->userRole->name)}}</td>
                                    <td>
                                        <span class="badge badge-{{ $user->kyc_status == 'pending' ? 'danger' : 'success' }}">{{ __(ucfirst($user->kyc_status))}}</span>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#kyc-modal-{{ $user->id }}">View</button>
                                    </td>
                                </tr>

                                <div class="modal fade" id="kyc-modal-{{ $user->id }}" tabindex="-1" role="dialog" aria-modal="true" aria-hidden="true">
                                    <div class="modal-dialog modal-md" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h4 class="modal-title">Identification Image</h4>
                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                            </div>
                                            <div class="modal-body row">
                                                <div class="col-lg-12 text-center">
                                                    <img src="{{asset($user->id_image ?? asset('default.png'))}}" class="img-responsive" width="400"  height="300" alt="{{ $user->name }}" id="view_kyc">
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <a href="{{route('users.kyc.update', ['status' => 'confirmed', 'user_id' => $user->unique_id])}}" class="btn btn-success">Confirm</a>
                                                <a href="{{route('users.kyc.update', ['status' => 'declined', 'user_id' => $user->unique_id])}}" class="btn btn-danger">Decline</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            @empty
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- push external js -->
@push('script')
<script src="{{ asset('plugins/DataTables/datatables.min.js') }}"></script>
<script src="{{ asset('js/datatables.js') }}"></script>
@endpush

@endsection

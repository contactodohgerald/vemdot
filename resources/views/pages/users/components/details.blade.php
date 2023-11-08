<div class="card-body text-center">
    <div class=" mb-2">
        <img src="{{asset($user->id_image ?? asset('default.png'))}}" class="img-fluid" />
    </div>
    @if($user->kyc_status == 'pending')
        <div>
            <a href="{{route('users.kyc.update', ['status' => 'confirmed', 'user_id' => $user->unique_id])}}" class="btn btn-success">Confirm</a>
            <a href="{{route('users.kyc.update', ['status' => 'declined', 'user_id' => $user->unique_id])}}" class="btn btn-danger">Decline</a>
        </div>
    @endif
</div>

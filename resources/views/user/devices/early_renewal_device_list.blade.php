@extends('layouts.user')
@section('content')
    <user-earlyrenewal-device-list :devicedata="{{ json_encode($devices) }}" :stripekey="{{ json_encode($stripePublicKey) }}"></user-earlyrenewal-device-list>
@endsection

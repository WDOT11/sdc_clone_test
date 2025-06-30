@extends('layouts.user')
@section('content')
    <user-renewal-device-list :devicedata="{{ json_encode($deviceData) }}" :stripekey="{{ json_encode($stripePublicKey) }}"></user-renewal-device-list>
@endsection
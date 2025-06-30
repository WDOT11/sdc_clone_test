@extends('layouts.user')
@section('content')
    <user-file-claim :shippingoptionsdata="{{ json_encode($shippingOptions) }}" :stripekey="{{ json_encode($stripePublicKey) }}"></user-file-claim>
@endsection
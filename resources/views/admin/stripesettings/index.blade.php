@extends('layouts.admin')
@section('content')
    <admin-stripe-setting :stripedata="{{ json_encode($stripeData) }}"></admin-stripe-setting>
@endsection
@extends('layouts.admin')
@section('content')
    <admin-shipping-supplies :shippingsupplydata="{{ json_encode($shippingSupplyData) }}" :pagination="{{ json_encode($pagination) }}"></admin-shipping-supplies>
@endsection
@extends('layouts.user')
@section('content')
    <user-shipping-supplies-index :boxtypes="{{ json_encode($boxTypes) }}" :shippingsupplydata="{{ json_encode($shippingSupplyData) }}" :pagination="{{ json_encode($pagination) }}"></user-shipping-supplies-index>
@endsection
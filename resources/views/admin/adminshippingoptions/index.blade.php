@extends('layouts.admin')
@section('content')
    <admin-shipping-options :shippingoptions="{{json_encode($shippingOptions)}}" :paginationdata="{{json_encode($pagination)}}"></admin-shipping-options>
@endsection
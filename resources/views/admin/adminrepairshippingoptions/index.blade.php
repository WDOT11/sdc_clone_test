@extends('layouts.admin')
@section('content')
    <admin-repairs-shipping-options :shippingoptions="{{json_encode($shippingOptions)}}" :paginationdata="{{json_encode($pagination)}}"></admin-repairs-shipping-options>
@endsection
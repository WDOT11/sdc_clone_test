@extends('layouts.admin')
@section('content') 
    <admin-supply-boxes :shippingsupplyboxdata="{{ json_encode($shippingSupplyBoxData) }}" :paginationdata="{{ json_encode($pagination) }}"></admin-supply-boxes>
@endsection
@extends('layouts.admin')
@section('content')
    <admin-file-claim :shippingoptionsdata="{{ json_encode($shippingOptions) }}"></admin-file-claim>
@endsection
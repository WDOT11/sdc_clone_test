@extends('layouts.admin')
@section('content')
<!-- <claimreason-master :claimreasons="@json($claimReasons)" :pagination="@json($claimReasons)"></claimreason-master> -->
    <claimreason-master :claimreasonsdata="{{json_encode($claimReasons)}}" :paginationdata="{{json_encode($pagination)}}"></claimreason-master>
@endsection
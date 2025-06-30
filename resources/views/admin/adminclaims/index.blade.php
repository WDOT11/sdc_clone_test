@extends('layouts.admin')
@section('content')
    <admin-track-claim :claimsdata="{{json_encode($claimsData)}}" :claimreasons="{{json_encode($claimReasons)}}" :paginationdata="{{json_encode($pagination)}}" :totalclaimrequests="{{ $totalClaimRequests }}"></admin-track-claim>
@endsection
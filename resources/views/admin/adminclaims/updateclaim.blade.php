@extends('layouts.admin')
@section('content')
    <admin-update-claim :claimdata="{{ json_encode($claim) }}"></admin-update-claim>
@endsection
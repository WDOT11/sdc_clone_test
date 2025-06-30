@extends('layouts.admin')
@section('content')
    <admin-login-logs :logdata="{{json_encode($logData)}}" :paginationdata="{{json_encode($pagination)}}"></admin-login-logs>
@endsection
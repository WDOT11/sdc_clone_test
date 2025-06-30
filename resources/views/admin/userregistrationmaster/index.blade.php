@extends('layouts.admin')
@section('content')
    <admin-user-registartion-index :userdata="{{ json_encode($userData) }}" :pagination="{{ json_encode($pagination) }}" :roles="{{ json_encode($roles) }}" :totalusers="{{ $totalUsers }}"></admin-user-registartion-index>
@endsection
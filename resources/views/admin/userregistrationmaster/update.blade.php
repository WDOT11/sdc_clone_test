@extends('layouts.admin')
@section('content')
    <admin-user-registartion-update :users="{{ json_encode($editData) }}" :roles="{{ json_encode($roles) }}"></admin-user-registartion-update>
@endsection
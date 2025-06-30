@extends('layouts.admin')
@section('content')
    <user-route-permission-list :routespermissions='@json($routePermissionData)':roles='@json($roles)'></user-route-permission-list>
@endsection
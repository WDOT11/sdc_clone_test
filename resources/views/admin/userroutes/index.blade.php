@extends('layouts.admin')
@section('content')
    <user-route-list :routesdata='@json($routes)' :paginationdata='@json($pagination)'></user-route-list>
@endsection
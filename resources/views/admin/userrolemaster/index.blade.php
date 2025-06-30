@extends('layouts.admin')
@section('content')
<user-role-list :rolesdata='@json($roles)' :paginationdata='@json($pagination)'></user-role-list>
@endsection
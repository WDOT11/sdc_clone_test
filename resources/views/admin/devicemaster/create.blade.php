@extends('layouts.admin')
@section('content')
    <device-master-create
        :deviceorgdata='@json($orgData)'
    >
    </device-master-create>
@endsection

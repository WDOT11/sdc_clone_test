@extends('layouts.admin')
@section('content')
    <device-master-update :devices='@json($device)' :deviceorgdata='@json($orgData)'></device-master-update>
@endsection
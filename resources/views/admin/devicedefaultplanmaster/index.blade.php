@extends('layouts.admin')
@section('content')
    <default-device-plan-index :plandata='@json($planData)' :pagination='@json($pagination)'></default-device-plan-index>
@endsection

@extends('layouts.admin')
@section('content')
    <device-plan-index
        :deviceplandata='@json($planData)'
        :pagination='@json($pagination)'>
    </device-plan-index>
@endsection
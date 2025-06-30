@extends('layouts.admin')
@section('content')
    <repair-plan-index :deviceplandata='@json($planData)' :pagination='@json($pagination)'></repair-plan-index>
@endsection
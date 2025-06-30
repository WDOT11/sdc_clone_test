@extends('layouts.admin')
@section('content')
    <device-model
        :devicemodeldata='@json($modelData)'
        :familydata='@json($familyData)'
        :pagination='@json($pagination)'>
    </device-model>
@endsection

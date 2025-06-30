@extends('layouts.admin')
@section('content')
    <device-family
        :familydata='@json($familyData)'
        :pagination='@json($pagination)'>
    </device-family>
@endsection

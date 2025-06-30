@extends('layouts.admin')
@section('content')
    <device-brand
        :branddata='@json($brandData)'
        :familydata='@json($familyData)'
        :pagination='@json($pagination)'>
    </device-brand>
@endsection

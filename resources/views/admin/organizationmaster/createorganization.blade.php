@extends('layouts.admin')
@section('content')
    <organization-create :claimreasons="{{json_encode($claimReasons)}}" :devicemodels="{{json_encode($deviceModels)}}"></organization-create>
@endsection

@extends('layouts.admin')
@section('content')
    <repairreason-master :repairreasonsdata="{{ json_encode($repairReasons) }}" :paginationdata="{{ json_encode($pagination) }}"></repairreason-master>
@endsection
@extends('layouts.admin')
@section('content')
    <admin-track-repair :repairdata="{{json_encode($repairData)}}" :paginationdata="{{json_encode($pagination)}}" :totalrepairrequests="{{ $totalRepairRequests }}"></admin-track-repair>
@endsection
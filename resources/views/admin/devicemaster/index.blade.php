@extends('layouts.admin')
@section('content')
    <device-master-index :devicedata='@json($deviceData)' :pagination='@json($pagination)' :devicebrands="{{ json_encode($deviceBrands) }}" :totaldevices="{{ $totalDevices }}"></device-master-index>
@endsection

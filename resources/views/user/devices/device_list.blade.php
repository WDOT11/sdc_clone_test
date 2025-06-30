@extends('layouts.user')
@section('content')
    <user-device-list :devicedata="{{ json_encode($deviceData) }}" :devicebrands="{{ json_encode($deviceBrands) }}" :totaldevices="{{ json_encode($totalDevices) }}" :totalcovereddevices="{{ json_encode($totalCoveredDevices) }}" :totaluncovereddevices="{{ json_encode($totalUncoveredDevices) }}" :pagination="{{ json_encode($pagination) }}"></user-device-list>
@endsection

@extends('layouts.admin')

@section('content')
    <admin-dashboard-design
        :totaldevices="{{ $totalDevices }}"
        :coverddevices="{{ $totalCoveredDevices }}"
        :uncoverddevices="{{ $totalUncoveredDevices }}"
        :totalinsuredclaims="{{ $totalInsuredClaims }}"
        :totaluninsuredclaims="{{ $totalUninsuredClaims }}">
    </admin-dashboard-design>
@endsection

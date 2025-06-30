@extends('layouts.user')

@section('content')
    <user-home
    :totaldevices="{{ $totalDevices }}"
    :coverddevices="{{ $totalCoveredDevices }}"
    :uncoverddevices="{{ $totalUncoveredDevices }}"
    :totalinsuredclaims="{{ $totalInsuredClaims }}"
    :totaluninsuredclaims="{{ $totalUninsuredClaims }}">
   </user-home>
@endsection

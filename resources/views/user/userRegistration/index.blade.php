@extends('layouts.user')
@section('content')
  <user-hod-director-registration-index :userdata="{{ json_encode($userData) }}" :pagination="{{ json_encode($pagination) }}" :roles="{{ json_encode($roles) }}" :orgdata="{{ json_encode($orgData) }}"></user-hod-director-registration-index>
@endsection
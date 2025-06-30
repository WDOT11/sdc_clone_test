@extends('layouts.user')
@section('content')
    <user-hod-director-registration-update :users="{{ json_encode($editData) }}" :roles="{{ json_encode($roles) }}"></user-hod-director-registration-update>
@endsection
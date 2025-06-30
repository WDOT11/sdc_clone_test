@extends('layouts.user')

@section('content')
<user-update-claims :claimdata="{{json_encode($claim)}}"></user-update-claims>
@endsection
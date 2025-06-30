@extends('layouts.user')

@section('content')
    <user-notifications  :notificationdata="{{json_encode($notificationsData)}}" :pagination="{{json_encode($pagination)}}"></user-notifications>
@endsection

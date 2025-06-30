@extends('layouts.admin')
@section('content')
    <admin-notifications  :notificationdata="{{json_encode($notificationsData)}}" :pagination="{{json_encode($pagination)}}"></admin-notifications>
@endsection
@extends('layouts.admin')
@section('content')
    <admin-support-ticket :ticketdata="{{json_encode($supportTicket)}}" :pagination="{{json_encode($pagination)}}"></admin-support-ticket>
@endsection
@extends('layouts.admin')
@section('content')
    <admin-smtp2go-setting :smtpdata="{{ json_encode($smtpData) }}"></admin-smtp2go-setting>
@endsection
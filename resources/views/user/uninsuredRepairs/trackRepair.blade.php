@extends('layouts.user')

@section('content')
<user-track-repair :repairdata="{{ json_encode($repairData) }}" :pagination="{{ json_encode($pagination) }}"></user-track-repair>
@endsection
@extends('layouts.admin')
@section('content')
<div class="container">
    <device-master-details :device="{{json_encode($device)}}"></device-master-details>
</div>
@endsection
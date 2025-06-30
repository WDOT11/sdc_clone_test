@extends('layouts.admin')
@section('content')
<admin-repair-request :repairreasondata="{{ json_encode($repair_reasons) }}" :shippingoptionsdata="{{ json_encode($shippingOptions) }}"></admin-repair-request>
@endsection

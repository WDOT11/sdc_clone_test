@extends('layouts.user')

@section('content')
<user-repair-request :repairreasondata="{{ json_encode($repair_reasons) }}" :userdata="{{ json_encode($userData) }}" :shippingoptionsdata="{{ json_encode($shippingOptions) }}" :stripekey="{{ json_encode($stripePublicKey) }}"></user-repair-request>
@endsection
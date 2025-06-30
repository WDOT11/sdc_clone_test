@extends('layouts.user')

@section('content')
{{-- <user-track-claims  :claimdata="{{json_encode($claimData)}}" :claimreasons={{ json_encode($claim_reasons) }} :pagination="{{json_encode($pagination)}}"></user-track-claims> --}}
<user-track-claims  :claimdata='@json($claimData)' :claimreasons='@json($claim_reasons)' :pagination='@json($pagination)'></user-track-claims>
@endsection
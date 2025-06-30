@extends('layouts.public')
@section('content')

    <get-coverage-public :stripekey="{{ json_encode($stripePublicKey) }}"></get-coverage-public>

@endsection
@extends('layouts.user')
@section('content')
    <user-transactions :transactiondata="{{json_encode($transactionData)}}" :paginationdata="{{json_encode($pagination)}}"></user-transactions>
@endsection

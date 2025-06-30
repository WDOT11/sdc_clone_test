@extends('layouts.admin')
@section('content')
    <admin-transactions :transactiondata="{{json_encode($transactionData)}}" :paginationdata="{{json_encode($pagination)}}"></admin-transactions>
@endsection

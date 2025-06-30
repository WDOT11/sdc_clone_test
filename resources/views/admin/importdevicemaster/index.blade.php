@extends('layouts.admin')
@section('content')
    <device-import-master-index :devicecsvdata="{{ json_encode($deviceCsvData) }}" :paginationdata="{{ json_encode($pagination) }}">
    </device-import-master-index>
@endsection
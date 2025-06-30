@extends('layouts.admin')
@section('content')
<organization-list :organizationsdata="{{ json_encode($orgData) }}" :paginationdata="{{ json_encode($pagination) }}" :totalorgs="{{ $totalOrgs }}"></organization-list>
@endsection
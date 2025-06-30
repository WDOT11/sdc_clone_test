@extends('layouts.admin')
@section('content')
    <admin-role-setting :roles='@json($roles)' :persoanlcoveragerole='@json($persoanlCoverageRole)' :educoveragerole='@json($eduCoverageRole)' :newuserrole='@json($newUserRole)'></admin-role-setting>
@endsection
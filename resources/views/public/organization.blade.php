@extends('layouts.public')
@section('content')
    <div class="container-fluid  py-5 card_public_bg public_coveragePg">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-12 col-md-10">
                    <div class="card pt-5 bg-transparent border-0 ">
                        <div class="card-body">
                            <h1 class="welcome_txt text-center"> Welcome to Smart Tech Insurance</h1>
                            <p class="mt-2 text-white text-center">
                                This platform aims to provide real-time coverage information for students' devices. It will help students identify potential areas of concern, such as outdated devices, low battery levels, or unauthorized access.
                            </p>
                    </div> </div>
                </div>
            </div>
        </div>
    </div>
    <public-organization :org="{{ json_encode($organization) }}" :suborgs="{{ json_encode($subOrgs) }}" :stripekey="{{ json_encode($stripePublicKey) }}"></public-organization>

@endsection
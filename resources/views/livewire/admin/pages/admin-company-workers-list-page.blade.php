@extends('layouts.admin.app')


@section('content')
    <div class="content-body">
        <!-- users list start -->
        <div class="content-header row">

            <div class="content-header-left col-md-9 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-start mb-0">Company staff</h2>
                        <div class="breadcrumb-wrapper">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">Home</a>
                                </li>
                                <li class="breadcrumb-item"><a href="{{route('admin.companies')}}">Companies</a>
                                </li>
                                <li class="breadcrumb-item"><a href="{{route('admin.company-profile', $company->id)}}">{{$company->name}}</a>
                                </li>
                                <li class="breadcrumb-item active">Staff
                                </li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <section class="app-user-list">
            <!-- list and filter start -->
            @livewire('admin-company-worker-list', ['company' => $company])

            <!-- list and filter end -->

            @livewire('company-create-worker-form', ['company' => $company])
        </section>
        <!-- users list ends -->

    </div>
@endsection

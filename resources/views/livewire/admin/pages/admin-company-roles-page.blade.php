@extends('layouts.admin.app')


@section('content')
    <div class="content-body">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-start mb-0">Roles</h2>
                    <div class="breadcrumb-wrapper">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{route('company.dashboard')}}">Home</a>
                            </li>
                            <li class="breadcrumb-item"><a href="{{route('admin.companies')}}">Companies</a>
                            </li>
                            <li class="breadcrumb-item"><a href="{{route('admin.company-profile', $company->id)}}">{{$company->name}}</a>
                            </li>
                            <li class="breadcrumb-item active">Roles
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Role cards -->
        @livewire('admin-company-roles-list', ['company' => $company])
        <!--/ Role cards -->

        <!-- Add Role Modal -->
        @livewire('company-create-role-form', ['company' => $company])
        <!--/ Add Role Modal -->

    </div>
@endsection

@extends('layouts.admin.app')


@section('content')
    <div class="content-body">
        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-start mb-0">Shop</h2>
                        <div class="breadcrumb-wrapper">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">Home</a>
                                </li>
                                <li class="breadcrumb-item"><a href="{{route('admin.companies')}}">Companies</a>
                                </li>
                                <li class="breadcrumb-item"><a href="{{route('admin.company-profile', $company->id)}}">{{$company->name}}</a>
                                </li>
                                <li class="breadcrumb-item active">Invoices
                                </li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <section class="invoice-list-wrapper">
            <div class="card">
                <div class="card-datatable table-responsive">
                    <p class="badge-light-primary mb-1 mt-1 m-lg-1" >
                        <a class="btn btn-primary" href="{{route('admin.company-create-invoice', $company->id)}}">Create Invoice</a>
                    </p>
                    @livewire('admin-company-invoice-list', ['company' => $company])
                </div>
            </div>
        </section>

    </div>
@endsection

@extends('layouts.admin.app')


@section('content')
    <div class="content-body">
        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-start mb-0">Invoices</h2>
                        <div class="breadcrumb-wrapper">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{route('company.dashboard')}}">Home</a>
                                </li>
                                <li class="breadcrumb-item"><a href="{{route('admin.invoices')}}">invoices</a>
                                </li>
                                <li class="breadcrumb-item"><a href="{{route('admin.invoice-preview', $invoice->id)}}">Preview</a>
                                </li>
                                <li class="breadcrumb-item active">Edit
                                </li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <section class="invoice-add-wrapper">

            @livewire('admin-edit-invoice', ['invoice' => $invoice])

            <div class="w-25">
                <a class="btn btn-outline-success w-100 mb-75" href="{{route('admin.invoice-preview', $invoice->id)}}">Preview invoice</a>
            </div>

        </section>
    </div>
@endsection

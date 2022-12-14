@extends('layouts.admin.app')


@section('content')
    <div class="content-body">
        <!-- users list start -->
        <div class="content-header row">

            <div class="content-header-left col-md-9 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-start mb-0">Settings</h2>
                        <div class="breadcrumb-wrapper">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">Home</a>
                                </li>
                                <li class="breadcrumb-item active">Settings
                                </li>
{{--                                <li class="breadcrumb-item"><a href="{{route('admin.company-profile', $product->company->id)}}">{{$product->company->name}}</a>--}}
{{--                                </li>--}}
{{--                                <li class="breadcrumb-item"><a href="{{route('admin.company-products', $product->company->id)}}">Products</a>--}}
{{--                                </li>--}}
{{--                                <li class="breadcrumb-item active">{{$product->name}}--}}
{{--                                </li>--}}
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <section class="app-user-list">


            @livewire('admin-settings-form')
            <!-- list and filter end -->
        </section>
        <!-- users list ends -->

    </div>
@endsection

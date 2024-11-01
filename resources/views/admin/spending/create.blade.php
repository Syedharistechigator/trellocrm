@extends('admin.layouts.app')

@section('cxmTitle', 'Create File')

@section('content')
<link rel="stylesheet" href="{{ asset('assets/plugins/dropify/css/dropify.min.css') }}">
<section class="content">
    <div class="body_scroll">
        <div class="block-header">
            <div class="row">
                <div class="col-lg-7 col-md-6 col-sm-12">
                    <h2>File</h2>

                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}"><i class="zmdi zmdi-home"></i>  {{ config('app.home_name') }}</a></li>
                        <li class="breadcrumb-item"><a href="#">File</a></li>
                        <li class="breadcrumb-item active">Create</li>
                    </ul>
                    <button class="btn btn-primary btn-icon mobile_menu" type="button"><i class="zmdi zmdi-sort-amount-desc"></i></button>
                </div>
                <div class="col-lg-5 col-md-6 col-sm-12 text-right">
                    <button class="btn btn-warning btn-icon rounded-circle right_icon_toggle_btn" type="button"><i class="zmdi zmdi-arrow-right"></i></button>
                </div>
            </div>
        </div>

        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="header">
                            <h2><strong>Create New</strong> File</h2>
                        </div>

                        <div class="body">
                            <form action="#" method="POST">
                                <div class="form-group">
                                    <input type="file" class="dropify" data-allowed-file-extensions="jpg png" data-max-file-size="100K">
                                </div>
                                <div class="text-center">
                                    <button type="submit" class="btn btn-warning">Submit</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection

@push('cxmScripts')
    <script src="{{ asset('assets/plugins/dropify/js/dropify.min.js') }}"></script>
    @include('admin.team.script')

    <script>
        $('.dropify').dropify();
    </script>
@endpush

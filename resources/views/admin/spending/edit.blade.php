@extends('admin.layouts.app')
@section('content')
<section class="content">
    <div class="body_scroll">
        <div class="block-header">
            <div class="row">
                <div class="col-lg-7 col-md-6 col-sm-12">
                    <h2>Spending</h2>

                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}"><i class="zmdi zmdi-home"></i>  {{ config('app.home_name') }}</a></li>
                        <li class="breadcrumb-item"><a href="#">Spending</a></li>
                        <li class="breadcrumb-item active">Edit</li>
                    </ul>
                    <button class="btn btn-primary btn-icon mobile_menu" type="button"><i class="zmdi zmdi-sort-amount-desc"></i></button>
                </div>
                <div class="col-lg-5 col-md-6 col-sm-12 text-right">
                    <button class="btn btn-warning btn-icon btn-round right_icon_toggle_btn" type="button"><i class="zmdi zmdi-arrow-right"></i></button>
                </div>
            </div>
        </div>

        <div class="container-fluid">
            <!-- Basic Validation -->
            <div class="row clearfix">
                <div class="col-lg-12 col-md-12 col-sm-12">
                    <div class="card">
                        <div class="header">
                            <h2><strong>Edit</strong> Spending</h2>
                        </div>

                        <div class="body">

                            <form id="spending_update_form">
                                @csrf
                                @method('PUT')
                                <input type="hidden" id="hdn" class="form-control" name="hdn" value="{{$spendingData->id}}">

                                <div class="row">
                                   <div class="col-md-6">
                                        <div class="form-group form-float">
                                            <label for="email_address">Team</label>
                                            <select class="form-control show-tick ms select2" data-placeholder="Select" id="team_hnd" name="team_key"  required>
                                                <option></option>
                                                @foreach($team as $teams )
                                                <option value="{{$teams->team_key}}" <?php if($teams->team_key == $spendingData->team_key) {echo "selected";}?>>{{$teams->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group form-float">
                                            <label for="email_address">Brand</label>
                                            <select class="form-control show-tick ms select2" data-placeholder="Select" id="brand_hnd" name="brand_key" required>
                                                <option></option>
                                                @foreach($brand as $brand )
                                                <option value="{{$brand->brand_key}}" <?php if($brand->brand_key == $spendingData->brand_key) {echo "selected";}?>>{{$brand->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <label for="email_address">Spending Date</label>
                                        <div class="form-group form-float">
                                        <input type="date" id="spending_date" class="form-control" placeholder="Spending Date" name="spending_date" value="{{$spendingData->spending_date}}">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="email_address">Platform</label>
                                        <div class="form-group form-float">
                                        <input type="text" id="spending_platform" class="form-control" placeholder="Spending Platform" name="spending_platform" value="{{$spendingData->platform}}">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="email_address">Amount</label>
                                        <div class="form-group form-float">
                                        <input type="text" id="spending_amount" class="form-control" placeholder="Spending Amount" name="spending_amount" value="{{$spendingData->amount}}">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <label for="email_address"></label>
                                        <div class="form-group form-float">
                                        <input id="update_data" type="submit" value="Submit" class=" btn-warning btn-round btn-block">
                                        </div>
                                    </div>
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
       @include('admin.Spending.script')
@endpush

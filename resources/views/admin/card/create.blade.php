@extends('admin.layouts.app')

@section('cxmTitle', 'Create Card')

@section('content')
<section class="content">
    <div class="body_scroll">
        <div class="block-header">
            <div class="row">
                <div class="col-lg-7 col-md-6 col-sm-12">
                    <h2>Card</h2>

                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}"><i class="zmdi zmdi-home"></i>  {{ config('app.home_name') }}</a></li>
                        <li class="breadcrumb-item"><a href="#">Card</a></li>
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
            <!-- Basic Validation -->
            <div class="row clearfix">
                <div class="col-lg-12 col-md-12 col-sm-12">
                    <div class="card">
                        <div class="header">
                            <h2><strong>Create New</strong> Card</h2>
                        </div>
                        <div id="alerts"></div>
                        <div class="alert alert-success" role="alert" id="#show12" style="display:none;">
                            <strong>Bootstrap</strong> Better check yourself, <a target="_blank" href="https://getbootstrap.com/docs/4.2/components/input-group/">View More</a>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true"><i class="zmdi zmdi-close"></i></span>
                            </button>
                        </div>

                        <div class="body">
                            <form id="card_form" method="POST">
                                @csrf
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group form-float">
                                            <label for="email_address">Title</label>
                                            <input type="text" class="form-control" name="title" minlength="3" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group form-float">
                                            <label for="email_address">Team</label>
                                            <select class="form-control" name="team_id" required>
                                                <option value="">Select Team</option>
                                                <?php $teams = App\Models\Team::where('status', '1')->get();?>
                                                <?php foreach ($teams as $key => $team): ?>
                                                <option value="{{$team->id}}">{{$team->name}}</option>
                                                <?php endforeach ?>

                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group form-float">
                                            <label for="email_address">Position</label>
                                            <input type="number" class="form-control" name="position" required>
                                        </div>
                                    </div>

                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <button class="btn btn-warning btn-round" type="submit">SUBMIT</button>
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
    @include('admin.card.script')
@endpush

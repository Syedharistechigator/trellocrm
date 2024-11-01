@extends('admin.layouts.app')

@section('cxmTitle', 'Card')

@section('content')
<section class="content">
    <div class="body_scroll">
        <div class="block-header">
            <div class="row">
                <div class="col-lg-7 col-md-6 col-sm-12">
                    <h2>Card List</h2>

                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}"><i class="zmdi zmdi-home"></i>  {{ config('app.home_name') }}</a></li>
                        <li class="breadcrumb-item">Card</li>
                        <li class="breadcrumb-item active"> List</li>
                    </ul>
                    <button class="btn btn-primary btn-icon mobile_menu" type="button"><i class="zmdi zmdi-sort-amount-desc"></i></button>
                </div>
                <div class="col-lg-5 col-md-6 col-sm-12 text-right">
                    @include('includes.admin.cxm-top-right-toggle-btn')
                </div>
            </div>
        </div>
        <div class="container-fluid">
            <div class="row clearfix">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="table-responsive">
                            <table id="cardTable" class="table table-striped table-hover theme-color js-exportable" xdata-sorting="false">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Title</th>
                                        <th>Team</th>
                                        <th>Position</th>
                                        <th data-breakpoints="xs md">Status</th>
                                        <th class="text-center" data-breakpoints="sm xs md">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                	@foreach($cards as $card)
                                    <tr>
                                        <td class="align-middle">{{$card->id}}</td>
                                        <td class="align-middle">{{$card->title}}</td>
                                        <?php $team = App\Models\Team::where('id', $card->team_id)->first();?>
                                        <td class="align-middle">{{isset($team->name)?$team->name:'-'}}</td>
                                        <td class="align-middle">{{$card->position}}</td>

                                        <td class="align-middle">
                                            <div class="custom-control custom-switch">
                                              {{--<span style="left: -41px; position: relative; top: 2px;">Unpublish</span>--}}
                                              <input data-id="{{$card->id}}" type="checkbox" class="custom-control-input toggle-class" id="customSwitch{{$card->id}}" {{ $card->status ? 'checked' : '' }}>
                                              <label class="custom-control-label" for="customSwitch{{$card->id}}"></label>
                                              {{--<span style="position: relative; top: 2px;">Publish</span>--}}
                                            </div>
                                        </td>
                                        <td class="text-center align-middle">
                                            <a title="Edit" href="{{route('card.edit',[$card->id],'/edit')}}" class="btn btn-warning btn-sm btn-round"><i class="zmdi zmdi-edit"></i></a>
                                            <a title="Delete" data-id="{{$card->id}}" data-type="confirm" href="javascript:void(0);" class="btn btn-danger btn-sm btn-round delButton"><i class="zmdi zmdi-delete"></i></a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
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

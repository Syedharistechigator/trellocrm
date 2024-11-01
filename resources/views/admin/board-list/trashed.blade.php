@extends('admin.layouts.app')

@section('cxmTitle', 'Trashed')

@section('content')
<section class="content">
    <div class="body_scroll">
        <div class="block-header">
            <div class="row">
                <div class="col-lg-7 col-md-6 col-sm-12">
                    <h2>Trashed Board List</h2>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}"><i class="zmdi zmdi-home"></i>  {{ config('app.home_name') }}</a></li>
                        <li class="breadcrumb-item">Board Lists</li>
                        <li class="breadcrumb-item active">Trashed</li>
                    </ul>
                    <button class="btn btn-primary btn-icon mobile_menu" type="button"><i class="zmdi zmdi-sort-amount-desc"></i></button>
                </div>
                <div class="col-lg-5 col-md-6 col-sm-12 text-right">
                     <button type="button" id="restoreAll" class="btn btn-danger btn-round restoreAllButton">Restore All</button>
                    @include('includes.admin.cxm-top-right-toggle-btn')
                </div>
            </div>
        </div>
        <div class="container-fluid">
            <div class="row clearfix">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="table-responsive">
                            <table id="BoardListTrashedTable" class="table table-striped table-hover theme-color js-exportable" data-sorting="false">
                                <thead>
                                    <tr>
                                        <th>Id#</th>
                                        <th>Title</th>
                                        <th>Team</th>
                                        <th>Team Count</th>
                                        <th>Position</th>
                                        <th class="text-center" data-breakpoints="xs md">Status</th>
                                        <th data-breakpoints="xs md">Delete Date</th>
                                        <th class="text-center" data-breakpoints="sm xs md">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($board_lists as $board_list)
                                        <tr>
                                            <td class="align-middle">{{$board_list->id}}</td>
                                            <td class="align-middle">{{$board_list->title}}</td>
                                            <td class="align-middle">@foreach($board_list->getTeams as $team)
                                                    {{ $team->name }}
                                                    @if (!$loop->last)
                                                        ,
                                                    @endif
                                                @endforeach</td>
                                            <td class="align-middle">{{$board_list->getTeams ? $board_list->getTeams->count():""}}</td>
                                            <td class="align-middle">{{$board_list->position}}</td>
                                            <td class="text-center">{!! ($board_list->status == 1)?'<i class="zmdi zmdi-check-circle text-success" title="Publish"></i>' :'<i class="zmdi zmdi-close-circle text-danger" title="Unpublish"></i>' !!}</td>
                                            <td>{{$board_list->deleted_at->format('j F, Y')}}
                                                <br>{{$board_list->deleted_at->format('h:i:s A')}}
                                                <br>{{$board_list->created_at->diffForHumans()}}
                                            </td>
                                            <td class="text-center">
                                                <a title="Restore" data-id="{{$board_list->id}}" class="btn btn-warning btn-sm btn-round restoreButton"><i class="zmdi zmdi-refresh"></i></a>
                                                <a title="Force Delete" data-id="{{$board_list->id}}" href="javascript:void(0);" class="btn btn-danger btn-sm btn-round board-list-force-del"><i class="zmdi zmdi-delete"></i></a>
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
    @include('admin.board-list.script')
@endpush

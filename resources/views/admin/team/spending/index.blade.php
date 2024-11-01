@extends('admin.layouts.app')@section('cxmTitle', 'Spending')
@section('content')
    @push('css')
        @include('admin.team.spending.style')
    @endpush
    <section class="content">
        <div class="body_scroll">
            <div class="block-header">
                <div class="row">
                    <div class="col-lg-7 col-md-6 col-sm-12">
                        <h2> Spending</h2>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}"><i class="zmdi zmdi-home"></i> {{ config('app.home_name') }}
                                </a></li> <li class="breadcrumb-item"> Spending </li>
                            <li class="breadcrumb-item active"><a href="{{route('admin.team.spending.index')}}"> List</a></li>
                        </ul>
                        <button class="btn btn-primary btn-icon mobile_menu" type="button">
                            <i class="zmdi zmdi-sort-amount-desc"></i></button>
                    </div>
                    <div class="col-lg-5 col-md-6 col-sm-12 text-right">
                        <button class="btn btn-success btn-icon rounded-circle" type="button" data-toggle="modal" id="create-team-spending-modal-btn" data-target="#createTeamSpendingModal">
                            <i class="zmdi zmdi-plus"></i></button>
                        @include('includes.admin.cxm-top-right-toggle-btn')
                    </div>
                </div>
            </div>
            <div class="container-fluid">
                <div class="row clearfix">
                    <div class="col-lg-12 col-md-12 col-sm-12">
                        <div class="card">
                            <div class="body">
                                <form id="searchForm">
                                    @csrf
                                    <div class="row clearfix">
                                        <div class="col-lg-4 col-md-6">
                                            <label for="search-team">Team</label>
                                            <select id="search-team" name="teamKey" class="form-control cxm-live-search-fix" data-icon-base="zmdi" data-tick-icon="zmdi-check" data-show-tick="true" title="Select Team" data-live-search="true" data-live-search-id="team-search">
                                                <option value="">All Teams</option>
                                                @foreach($teams as $team)
                                                    <option value="{{ $team->team_key }}" {{ request('teamKey') == $team->team_key ? 'selected' : '' }}>
                                                        {{ $team->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-lg-4 col-md-6">
                                            <label for="search-month">Month</label>
                                            <select id="search-month" name="month" class="form-control cxm-live-search-fix" data-icon-base="zmdi" data-tick-icon="zmdi-check" data-show-tick="true" title="Select Month" data-live-search="true" data-live-search-id="team-search">
                                                <option value="">All Months</option>
                                                @foreach(config('app.months') as $m)
                                                    <option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>{{ $m }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-lg-4 col-md-6">
                                            <label for="search-year">Year</label>
                                            <select id="search-year" name="year" class="form-control cxm-live-search-fix" data-icon-base="zmdi" data-tick-icon="zmdi-check" data-show-tick="true" title="Select Year" data-live-search="true" data-live-search-id="team-search">
                                                <option value="">All Years</option>
                                                @for($y = 2021; $y <= 2030; $y++)
                                                    <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>
                                                        {{ $y }}
                                                    </option>
                                                @endfor
                                            </select>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row clearfix">
                    <div class="col-lg-12">
                        <div class="">
                            <div class="table-responsive">
                                <table id="TeamSpendingTable" class="table table-striped table-hover xjs-basic-example theme-color">
                                    <thead>
                                    <tr>
                                        <th class='text-nowrap'>ID #</th>
                                        <th class='text-nowrap'>Team</th>
                                        <th class='text-nowrap'>Accounts</th>
                                        <th class='text-nowrap'>Amount Spent</th>
                                        <th class='text-nowrap'>Limit</th>
                                        <th class='text-nowrap'>Month</th>
                                        <th class='text-nowrap'>Year</th>
                                        <th data-breakpoints="sm xs md">Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($team_spendings as $team_spending)
                                        <tr id="tr-{{$team_spending->id}}">
                                            <td class="align-middle">{{$team_spending->id}}</td>
                                            <td class="align-middle">{{optional($team_spending->getTeam)->name}}</td>
                                            <td class="align-middle">{{$team_spending->accounts}}</td>
                                            <td class="align-middle">${{$team_spending->amount}}</td>
                                            <td class="align-middle">${{$team_spending->limit}}</td>
                                            <td class="align-middle">{{ DateTime::createFromFormat('!m', $team_spending->month)->format('F') }}</td>
                                            <td class="align-middle">{{ $team_spending->year }}</td>
                                            <td class="align-middle text-nowrap">
                                                <button data-id="{{$team_spending->id}}" title="Edit" class="btn btn-warning btn-sm btn-round editTeamSpending" data-toggle="modal" data-target="#editTeamSpendingModal">
                                                    <i class="zmdi zmdi-edit"></i></button>
                                                <a title="Delete" data-id="{{$team_spending->id}}" data-type="confirm" href="javascript:void(0);" class="btn btn-danger btn-sm btn-round delButton"><i class="zmdi zmdi-delete"></i></a>
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
    <!-- Create Team Spending -->
    <div class="modal fade" id="createTeamSpendingModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="title" id="defaultModalLabel">Create A New Spending</h4>
                </div>
                <form method="POST" id="create_form">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="team_key">Select Team Name</label>
                            <select id="team_key" name="team_key" class="change form-control cxm-live-search-fix" data-icon-base="zmdi" data-tick-icon="zmdi-check" data-show-tick="true" title="Select Team" data-live-search="true" required>
                                <option disabled>Select Team</option>
                                @foreach($teams as $team)
                                    <option value="{{$team->team_key}}">{{$team->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="accounts">Accounts</label>
                            <input type="number" id="accounts" name="accounts" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="amount">Amount Spent</label>
                            <input type="number" step="0.01" id="amount" name="amount" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="limit">Limit</label>
                            <input type="number" step="0.01" id="limit" name="limit" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="month">Select Month</label>
                            <select id="month" name="month" class="change form-control cxm-live-search-fix" data-icon-base="zmdi" data-tick-icon="zmdi-check" data-show-tick="true" title="Select Month" data-live-search="true" required>
                                <option disabled>Select Month</option>
                                @for ($i = 1; $i <= 12; $i++)
                                    <option value="{{ $i }}" {{ $i == date('n') ? 'selected' : '' }}>{{ date("F", mktime(0, 0, 0, $i, 10)) }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="year">Select Year</label>
                            <select id="year" name="year" class="change form-control cxm-live-search-fix" data-icon-base="zmdi" data-tick-icon="zmdi-check" data-show-tick="true" title="Select Year" data-live-search="true" required>
                                <option disabled>Select Year</option>
                                @for ($i = 2021; $i <= 2030; $i++)
                                    <option value="{{ $i }}" {{ $i == date('Y') ? 'selected' : '' }}>{{$i}}</option>
                                @endfor
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" id="addStatusBtn" class="btn btn-success btn-round">SAVE</button>
                        <button type="button" class="btn btn-danger btn-round" data-dismiss="modal">CLOSE</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Team Spending -->
    <div class="modal fade" id="editTeamSpendingModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="title" id="defaultModalLabel">Edit Spending</h4>
                </div>
                <form method="POST" id="update_form">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="edit_team_key">Select Team Name</label>
                            <select id="edit_team_key" name="team_key" class="change form-control cxm-live-search-fix" data-icon-base="zmdi" data-tick-icon="zmdi-check" data-show-tick="true" title="Select Team" data-live-search="true" required>
                                <option disabled>Select Team</option>
                                @foreach($teams as $team)
                                    <option value="{{$team->team_key}}">{{$team->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="edit_accounts">Accounts</label>
                            <input type="number" id="edit_accounts" name="accounts" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="edit_amount">Amount Spent</label>
                            <input type="number" step="0.01" id="edit_amount" name="amount" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="edit_limit">Limit</label>
                            <input type="number" step="0.01" id="edit_limit" name="limit" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="edit_month">Select Month</label>
                            <select id="edit_month" name="month" class="change form-control cxm-live-search-fix" data-icon-base="zmdi" data-tick-icon="zmdi-check" data-show-tick="true" title="Select Month" data-live-search="true" required>
                                <option disabled>Select Month</option>
                                @for ($i = 1; $i <= 12; $i++)
                                    <option value="{{ $i }}" {{ $i == date('n') ? 'selected' : '' }}>{{ date("F", mktime(0, 0, 0, $i, 10)) }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="edit_year">Select Year</label>
                            <select id="edit_year" name="year" class="change form-control cxm-live-search-fix" data-icon-base="zmdi" data-tick-icon="zmdi-check" data-show-tick="true" title="Select Year" data-live-search="true" required>
                                <option disabled>Select Year</option>
                                @for ($i = 2021; $i <= 2030; $i++)
                                    <option value="{{ $i }}" {{ $i == date('Y') ? 'selected' : '' }}>{{$i}}</option>
                                @endfor
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success btn-round">SAVE</button>
                        <button type="button" class="btn btn-danger btn-round" data-dismiss="modal">CLOSE</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection
@push('cxmScripts')
    @include('admin.team.spending.script')
@endpush

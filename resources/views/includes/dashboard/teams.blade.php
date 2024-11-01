<div class="card">
    <div class="header">
        <h2><strong><i class="zmdi zmdi-chart"></i>
                @if (str_contains(request()->server('SERVER_NAME'), 'uspto-filing') != false)
                    Uspto
                @else
                    Techigator
                @endif</strong> Teams</h2>
    </div>
    <div class="table-responsive">
        <table class="table table-striped table-hover theme-color" data-sorting="false">
            <thead>
                <tr>
                    <th>Team</th>
                    <th>&nbsp;</th>
                    <th>Last Month</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data['teamData'] as $team)
                <tr>
                    <td>
                        <a href="javascript:void(0)" class="text-muted">{{$team->name}}</a>
                    </td>
                    <td style="text-transform:capitalize;">{{$team->type}}</td>
                    <td>${{$team->lastmonthamount}}</td>
                    <td>${{$team->amount}}
                        @if($team->amount > $team->lastmonthamount)
                        <i class="zmdi zmdi-trending-up text-success"></i>
                        @else
                        <i class="zmdi zmdi-trending-down text-warning"></i>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<div class="card">
    <div class="header">
        <h2><strong>@if (str_contains(request()->server('SERVER_NAME'), 'uspto-filing') != false)
                    Uspto
                @else
                    Techigator
                @endif</strong> Brands</h2>
    </div>
    <div class="table-responsive">
        <table class="table table-striped table-hover theme-color" data-sorting="false">
            <thead>
                <tr>
                    <th>Brand</th>
                    <th></th>
                    <th>Last Month</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data['brandData'] as $brand)
                <tr>
                    <td class="w70">
                        <img class="w50" src="{{$brand->brandLogo}}" alt="">
                    </td>
                    <td>
                        <a href="javascript:void(0)" class="text-muted">{{$brand->brandName}}</a>
                    </td>
                    <td>${{$brand->lastmonthamount}}</td>
                    <td>${{$brand->amount}}
                        @if($brand->amount > $brand->lastmonthamount)
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

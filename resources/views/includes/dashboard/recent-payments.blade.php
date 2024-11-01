<div class="card">
    <div class="header">
        <h2><strong><i class="zmdi zmdi-money"></i> Recent</strong> Payments</h2>
    </div>
    <div class="table-responsive">
        <table class="table table-striped table-hover theme-color" data-sorting="false">
            <thead>
                <tr>
                    <th style="width:60px;">#</th>
                    <th>Brand Name</th>
                    <th>Agent</th>
                    <th>Client</th>                                    
                    <th>Amount</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data['recentPayments'] as $payment)   
                <tr>
                    <td>{{$payment->id}}</td>
                    <td>{{$payment->brandName}}</td>
                    <td>{{$payment->agentName}}</td>
                    <td>{{$payment->name}}</td>
                    <td>${{$payment->amount}}</td>
                    <td><span class="badge badge-success rounded-pill">Paid</span></td>
                </tr>
                @endforeach                
            </tbody>
        </table>
    </div> 
</div>
                
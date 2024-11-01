<div class="card">
    <div class="header">
        <h2><strong><i class="zmdi zmdi-chart"></i> PPC </strong> Spending</h2>
    </div>
    <div class="body" style="min-height:480px;">
        <div class="form-row align-items-center">
            <div class="col-md-6 mb-3">
                <h3>$ {{$data['yearSpending']}}</h3>
                <h5>$ {{$data['monthSpending']}}</h5>
            </div>
            <div class="col-md-6 mb-3">
                <div class="text-center">
                    <input type="text" class="knob" data-linecap="round" value="{{$data['monthSpending']}}" data-width="125" data-height="125" data-thickness="0.25" data-fgColor="#64c8c0" readonly>
                </div>
            </div>
        </div>
        <hr class="border-info mb-5">        
        <div class="form-row">
            <div class="col-md-6">
                <div class="card w_data_1">
                    <div class="xbody">
                        <div class="w_icon pink"><i class="zmdi zmdi-google"></i></div>
                        <h4 class="mt-3 mb-0">{{$data['googleSpending']}}</h4>
                        <span class="text-muted">Google</span>
                        <!-- <div class="w_description text-success">
                            <i class="zmdi zmdi-trending-up"></i>
                            <span>15.5%</span>
                        </div> -->
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card w_data_1">
                    <div class="xbody">
                        <div class="w_icon cyan"><i class="zmdi zmdi-blogger"></i></div>
                        <h4 class="mt-3 mb-0">{{$data['bingSpending']}}</h4>
                        <span class="text-muted">Bing</span>
                        <!-- <div class="w_description text-success">
                            <i class="zmdi zmdi-trending-up"></i>
                            <span>95.5%</span>
                        </div> -->
                    </div>
                </div>
            </div>
            <!-- <div class="col-md-4">
                <div class="card w_data_1">
                    <div class="xbody">
                        <div class="w_icon pink"><i class="zmdi zmdi-facebook"></i></div>
                        <h4 class="mt-3 mb-0">12.1k</h4>
                        <span class="text-muted">Bugs Fixed</span>
                        <div class="w_description text-success">
                            <i class="zmdi zmdi-trending-up"></i>
                            <span>15.5%</span>
                        </div>
                    </div>
                </div>
            </div> -->
        </div>
    </div>
</div>
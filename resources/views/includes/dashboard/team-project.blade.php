<div class="card">
    <div class="header">
        <h2><strong><i class="zmdi zmdi-chart"></i> Team</strong> Project</h2>
    </div>
    <div class="body">
        <div class="row">            
            @foreach($data['projectStatus'] as $status)
            <div class="col-md-6">
                <div class="form-row align-items-center">
                    <div class="col-auto">
                        <div class="p-3 bg-{{$status->status_color}} shadow rounded-circle d-flex justify-content-center align-items-center" style="width:40px; height:40px; border:2px #fd4f33 solid;">
                            <h4 class="my-0 text-white">{{$status->count}}</h4>
                        </div>
                    </div>
                    <div class="col">
                        <h6>{{$status->status}}</h6>                
                    </div>
                </div>
                <hr>
            </div>
            @endforeach
        </div>                                        
    </div>
    <div class="body" style="margin-top:10px;">
        <div class="row">            
            @foreach($data['projectcategoriesdata'] as $category)
            <div class="col-md-6">
                <div class="form-row align-items-center">
                    <div class="col-auto">
                        <div class="p-3 bg-purple shadow rounded-circle d-flex justify-content-center align-items-center" style="width:40px; height:40px; border:2px #999 solid;">
                            <h4 class="my-0 text-white">{{$category->count}}</h4>
                        </div>
                    </div>
                    <div class="col">
                        <h6>{{$category->name}}</h6>                
                    </div>
                </div>
                <hr class="my-2">
            </div>
            @endforeach
        </div>                                        
    </div>
</div>


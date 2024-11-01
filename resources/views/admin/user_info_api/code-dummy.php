//  $brand = DB::table('brands')->where('id', $id)->update([
        //     'name' => request('name'),
        //     'brand_url' => request('brand_url'),
        //     'logo' => request('logo'),  
        // ]);


<div class="onoffswitch">
                                                <input data-id="{{$brand->id}}" type="checkbox" name="onoffswitch" class="onoffswitch-checkbox" id="myonoffswitch" data-on="Active" data-off="InActive" {{ $brand->active ? 'checked' : '' }}>
                                                <label class="onoffswitch-label" for="myonoffswitch">
                                                    <span class="onoffswitch-inner"></span>
                                                    <span class="onoffswitch-switch"></span>
                                                </label>
                                            </div>


                                            <input data-id="{{$brand->id}}" class="toggle-class" type="checkbox" data-onstyle="success" data-offstyle="danger" data-toggle="toggle" data-on="Active" data-off="InActive" {{ $brand->active ? 'checked' : '' }}>



                                            .onoffswitch {
    position: relative; width: 90px;
    -webkit-user-select:none; -moz-user-select:none; -ms-user-select: none;
}

.onoffswitch-checkbox {
    display: none;
}

.onoffswitch-label {
    display: block; overflow: hidden; cursor: pointer;
    border: 2px solid #999999; border-radius: 20px;
}

.onoffswitch-inner {
    display: block; width: 200%; margin-left: -100%;
    -moz-transition: margin 0.3s ease-in 0s; -webkit-transition: margin 0.3s ease-in 0s;
    -o-transition: margin 0.3s ease-in 0s; transition: margin 0.3s ease-in 0s;
}

.onoffswitch-inner:before, .onoffswitch-inner:after {
    display: block; float: left; width: 50%; height: 30px; padding: 0; line-height: 30px;
    font-size: 14px; color: white; font-family: Trebuchet, Arial, sans-serif; font-weight: bold;
    -moz-box-sizing: border-box; -webkit-box-sizing: border-box; box-sizing: border-box;
}

.onoffswitch-inner:before {
    content: "YES";
    padding-left: 10px;
    background-color: #2FCCFF; color: #FFFFFF;
}

.onoffswitch-inner:after {
    content: "NO";
    padding-right: 10px;
    background-color: #EEEEEE; color: #999999;
    text-align: right;
}

.onoffswitch-switch {
    display: block; width: 18px; margin: 6px;
    background: #FFFFFF;
    border: 2px solid #999999; border-radius: 20px;
    position: absolute; top: 0; bottom: 0; right: 56px;
    -moz-transition: all 0.3s ease-in 0s; -webkit-transition: all 0.3s ease-in 0s;
    -o-transition: all 0.3s ease-in 0s; transition: all 0.3s ease-in 0s; 
}

.onoffswitch-checkbox:checked + .onoffswitch-label .onoffswitch-inner {
    margin-left: 0;
}

.onoffswitch-checkbox:checked + .onoffswitch-label .onoffswitch-switch {
    right: 0px; 
}


 .onoffswitch1 {
    position: relative; width: 90px;
    -webkit-user-select:none; -moz-user-select:none; -ms-user-select: none;
}

.onoffswitch1-checkbox {
    display: none;
}

.onoffswitch1-label {
    display: block; overflow: hidden; cursor: pointer;
    border: 2px solid #999999; border-radius: 30px;
}

.onoffswitch1-inner {
    display: block; width: 200%; margin-left: -100%;
    -moz-transition: margin 0.3s ease-in 0s; -webkit-transition: margin 0.3s ease-in 0s;
    -o-transition: margin 0.3s ease-in 0s; transition: margin 0.3s ease-in 0s;
}

.onoffswitch1-inner:before, .onoffswitch1-inner:after {
    display: block; float: left; width: 50%; height: 30px; padding: 0; line-height: 30px;
    font-size: 14px; color: white; font-family: Trebuchet, Arial, sans-serif; font-weight: bold;
    -moz-box-sizing: border-box; -webkit-box-sizing: border-box; box-sizing: border-box;
    border-radius: 30px;
    box-shadow: 0px 15px 0px rgba(0,0,0,0.08) inset;
}

.onoffswitch1-inner:before {
    content: "YES";
    padding-left: 10px;
    background-color: #2FCCFF; color: #FFFFFF;
    border-radius: 30px 0 0 30px;
}

.onoffswitch1-inner:after {
    content: "NO";
    padding-right: 10px;
    background-color: #EEEEEE; color: #999999;
    text-align: right;
    border-radius: 0 30px 30px 0;
}

.onoffswitch1-switch {
    display: block; width: 30px; margin: 0px;
    background: #FFFFFF;
    border: 2px solid #999999; border-radius: 30px;
    position: absolute; top: 0; bottom: 0; right: 56px;
    -moz-transition: all 0.3s ease-in 0s; -webkit-transition: all 0.3s ease-in 0s;
    -o-transition: all 0.3s ease-in 0s; transition: all 0.3s ease-in 0s; 
    background-image: -moz-linear-gradient(center top, rgba(0,0,0,0.1) 0%, rgba(0,0,0,0) 80%); 
    background-image: -webkit-linear-gradient(center top, rgba(0,0,0,0.1) 0%, rgba(0,0,0,0) 80%); 
    background-image: -o-linear-gradient(center top, rgba(0,0,0,0.1) 0%, rgba(0,0,0,0) 80%); 
    background-image: linear-gradient(center top, rgba(0,0,0,0.1) 0%, rgba(0,0,0,0) 80%);
    box-shadow: 0 1px 1px white inset;
}

.onoffswitch1-checkbox:checked + .onoffswitch1-label .onoffswitch1-inner {
    margin-left: 0;
}

.onoffswitch1-checkbox:checked + .onoffswitch1-label .onoffswitch1-switch {
    right: 0px; 
}

.onoffswitch2 {
    position: relative; width: 90px;
    -webkit-user-select:none; -moz-user-select:none; -ms-user-select: none;
}

.onoffswitch2-checkbox {
    display: none;
}

.onoffswitch2-label {
    display: block; overflow: hidden; cursor: pointer;
    border: 2px solid #999999; border-radius: 5px;
}

.onoffswitch2-inner {
    display: block; width: 200%; margin-left: -100%;
    -moz-transition: margin 0.3s ease-in 0s; -webkit-transition: margin 0.3s ease-in 0s;
    -o-transition: margin 0.3s ease-in 0s; transition: margin 0.3s ease-in 0s;
}

.onoffswitch2-inner:before, .onoffswitch2-inner:after {
    display: block; float: left; width: 50%; height: 30px; padding: 0; line-height: 30px;
    font-size: 14px; color: white; font-family: Trebuchet, Arial, sans-serif; font-weight: bold;
    -moz-box-sizing: border-box; -webkit-box-sizing: border-box; box-sizing: border-box;
}

.onoffswitch2-inner:before {
    content: "YES";
    padding-left: 10px;
    background-color: #2FCCFF; color: #FFFFFF;
}

.onoffswitch2-inner:after {
    content: "NO";
    padding-right: 10px;
    background-color: #EEEEEE; color: #999999;
    text-align: right;
}

.onoffswitch2-switch {
    display: block; width: 18px; margin: 0px;
    background: #FFFFFF;
    border: 2px solid #999999; border-radius: 5px;
    position: absolute; top: 0; bottom: 0; right: 68px;
    -moz-transition: all 0.3s ease-in 0s; -webkit-transition: all 0.3s ease-in 0s;
    -o-transition: all 0.3s ease-in 0s; transition: all 0.3s ease-in 0s; 
    background-image: -moz-linear-gradient(center top, rgba(0,0,0,0.1) 0%, rgba(0,0,0,0) 100%);
    background-image: -webkit-linear-gradient(center top, rgba(0,0,0,0.1) 0%, rgba(0,0,0,0) 100%);
    background-image: -o-linear-gradient(center top, rgba(0,0,0,0.1) 0%, rgba(0,0,0,0) 100%);
    background-image: linear-gradient(center top, rgba(0,0,0,0.1) 0%, rgba(0,0,0,0) 100%);
}

.onoffswitch2-checkbox:checked + .onoffswitch2-label .onoffswitch2-inner {
    margin-left: 0;
}

.onoffswitch2-checkbox:checked + .onoffswitch2-label .onoffswitch2-switch {
    right: 0px; 
}

.onoffswitch3
{
    position: relative; width: 90px;
    -webkit-user-select:none; -moz-user-select:none; -ms-user-select: none;
}

.onoffswitch3-checkbox {
    display: none;
}

.onoffswitch3-label {
    display: block; overflow: hidden; cursor: pointer;
    border: 0px solid #999999; border-radius: 0px;
}

.onoffswitch3-inner {
    display: block; width: 200%; margin-left: -100%;
    -moz-transition: margin 0.3s ease-in 0s; -webkit-transition: margin 0.3s ease-in 0s;
    -o-transition: margin 0.3s ease-in 0s; transition: margin 0.3s ease-in 0s;
}

.onoffswitch3-inner > span {
    display: block; float: left; position: relative; width: 50%; height: 30px; padding: 0; line-height: 30px;
    font-size: 14px; color: white; font-family: Trebuchet, Arial, sans-serif; font-weight: bold;
    -moz-box-sizing: border-box; -webkit-box-sizing: border-box; box-sizing: border-box;
}

.onoffswitch3-inner .onoffswitch3-active {
    padding-left: 10px;
    background-color: #EEEEEE; color: #FFFFFF;
}

.onoffswitch3-inner .onoffswitch3-inactive {
    padding-right: 10px;
    background-color: #EEEEEE; color: #FFFFFF;
    text-align: right;
}

.onoffswitch3-switch {
    display: block; width: 18px; margin: 0px; text-align: center; 
    border: 0px solid #999999;border-radius: 0px; 
    position: absolute; top: 0; bottom: 0;
}
.onoffswitch3-active .onoffswitch3-switch {
    background: #27A1CA; left: 0;
}
.onoffswitch3-inactive .onoffswitch3-switch {
    background: #A1A1A1; right: 0;
}

.onoffswitch3-active .onoffswitch3-switch:before {
    content: " "; position: absolute; top: 0; left: 18px; 
    border-style: solid; border-color: #27A1CA transparent transparent #27A1CA; border-width: 15px 9px;
}


.onoffswitch3-inactive .onoffswitch3-switch:before {
    content: " "; position: absolute; top: 0; right: 18px; 
    border-style: solid; border-color: transparent #A1A1A1 #A1A1A1 transparent; border-width: 15px 9px;
}


.onoffswitch3-checkbox:checked + .onoffswitch3-label .onoffswitch3-inner {
    margin-left: 0;
}

.onoffswitch4 {
    position: relative; width: 90px;
    -webkit-user-select:none; -moz-user-select:none; -ms-user-select: none;
}

.onoffswitch4-checkbox {
    display: none;
}

.onoffswitch4-label {
    display: block; overflow: hidden; cursor: pointer;
    border: 2px solid #27A1CA; border-radius: 0px;
}

.onoffswitch4-inner {
    display: block; width: 200%; margin-left: -100%;
    -moz-transition: margin 0.3s ease-in 0s; -webkit-transition: margin 0.3s ease-in 0s;
    -o-transition: margin 0.3s ease-in 0s; transition: margin 0.3s ease-in 0s;
}

.onoffswitch4-inner:before, .onoffswitch4-inner:after {
    display: block; float: left; width: 50%; height: 30px; padding: 0; line-height: 26px;
    font-size: 14px; color: white; font-family: Trebuchet, Arial, sans-serif; font-weight: bold;
    -moz-box-sizing: border-box; -webkit-box-sizing: border-box; box-sizing: border-box;
    border: 2px solid transparent;
    background-clip: padding-box;
}

.onoffswitch4-inner:before {
    content: "Yes";
    padding-left: 10px;
    background-color: #FFFFFF; color: #27A1CA;
}

.onoffswitch4-inner:after {
    content: "No";
    padding-right: 10px;
    background-color: #FFFFFF; color: #666666;
    text-align: right;
}

.onoffswitch4-switch {
    display: block; width: 25px; margin: 0px;
    background: #27A1CA;
    position: absolute; top: 0; bottom: 0; right: 65px;
    -moz-transition: all 0.3s ease-in 0s; -webkit-transition: all 0.3s ease-in 0s;
    -o-transition: all 0.3s ease-in 0s; transition: all 0.3s ease-in 0s; 
}

.onoffswitch4-checkbox:checked + .onoffswitch4-label .onoffswitch4-inner {
    margin-left: 0;
}

.onoffswitch4-checkbox:checked + .onoffswitch4-label .onoffswitch4-switch {
    right: 0px; 
}



.cmn-toggle 
{
  position: absolute;
  margin-left: -9999px;
  visibility: hidden;
}

.cmn-toggle + label 
{
  display: block;
  position: relative;
  cursor: pointer;
  outline: none;
  -webkit-user-select: none;
  -moz-user-select: none;
  -ms-user-select: none;
  user-select: none;
}

input.cmn-toggle-round-flat + label 
{
  padding: 2px;
  width: 75px;
  height: 30px;
  background-color: #dddddd;
  -webkit-border-radius: 60px;
  -moz-border-radius: 60px;
  -ms-border-radius: 60px;
  -o-border-radius: 60px;
  border-radius: 60px;
  -webkit-transition: background 0.4s;
  -moz-transition: background 0.4s;
  -o-transition: background 0.4s;
  transition: background 0.4s;
}

input.cmn-toggle-round-flat + label:before, input.cmn-toggle-round-flat + label:after 
{
  display: block;
  position: absolute;
  content: "";
}

input.cmn-toggle-round-flat + label:before 
{
  top: 2px;
  left: 2px;
  bottom: 2px;
  right: 2px;
  background-color: #fff;
  -webkit-border-radius: 60px;
  -moz-border-radius: 60px;
  -ms-border-radius: 60px;
  -o-border-radius: 60px;
  border-radius: 60px;
  -webkit-transition: background 0.4s;
  -moz-transition: background 0.4s;
  -o-transition: background 0.4s;
  transition: background 0.4s;
}

input.cmn-toggle-round-flat + label:after 
{
  top: 4px;
  left: 4px;
  bottom: 4px;
  width: 22px;
  background-color: #dddddd;
  -webkit-border-radius: 52px;
  -moz-border-radius: 52px;
  -ms-border-radius: 52px;
  -o-border-radius: 52px;
  border-radius: 52px;
  -webkit-transition: margin 0.4s, background 0.4s;
  -moz-transition: margin 0.4s, background 0.4s;
  -o-transition: margin 0.4s, background 0.4s;
  transition: margin 0.4s, background 0.4s;
}

input.cmn-toggle-round-flat:checked + label 
{
  background-color: #27A1CA;
}

input.cmn-toggle-round-flat:checked + label:after 
{
  margin-left: 45px;
  background-color: #27A1CA;
}

div.switch5 { clear: both; margin: 0px 0px; }
div.switch5 > input.switch:empty { margin-left: -999px; }
div.switch5 > input.switch:empty ~ label { position: relative; float: left; line-height: 1.6em; text-indent: 4em; margin: 0.2em 0px; cursor: pointer; -moz-user-select: none; }
div.switch5 > input.switch:empty ~ label:before, input.switch:empty ~ label:after { position: absolute; display: block; top: 0px; bottom: 0px; left: 0px; content: "off"; width: 3.6em; height: 1.5em; text-indent: 2.4em; color: rgb(153, 0, 0); background-color: rgb(204, 51, 51); border-radius: 0.3em; box-shadow: 0px 0.2em 0px rgba(0, 0, 0, 0.3) inset; }
div.switch5 > input.switch:empty ~ label:after { content: " "; width: 1.4em; height: 1.5em; top: 0.1em; bottom: 0.1em; text-align: center; text-indent: 0px; margin-left: 0.1em; color: rgb(255, 136, 136); background-color: rgb(255, 255, 255); border-radius: 0.15em; box-shadow: 0px -0.2em 0px rgba(0, 0, 0, 0.2) inset; transition: all 100ms ease-in 0s; }
div.switch5 > input.switch:checked ~ label:before { content: "on"; text-indent: 0.5em; color: rgb(102, 255, 102); background-color: rgb(51, 153, 51); }
div.switch5 > input.switch:checked ~ label:after { margin-left: 2.1em; color: rgb(102, 204, 102); }
div.switch5 > input.switch:focus ~ label { color: rgb(0, 0, 0); }
div.switch5 > input.switch:focus ~ label:before { box-shadow: 0px 0px 0px 3px rgb(153, 153, 153); }







.switch6 {  max-width: 17em;  margin: 0 auto; }
.switch6-light > span, .switch-toggle > span {  color: #000000; }
.switch6-light span span, .switch6-light label, .switch-toggle span span, .switch-toggle label {  color: #2b2b2b; }

.switch-toggle a, 
.switch6-light span span { display: none; }

.switch6-light { display: block; height: 30px; position: relative; overflow: visible; padding: 0px; margin-left:0px; }
.switch6-light * { box-sizing: border-box; }
.switch6-light a { display: block; transition: all 0.3s ease-out 0s; }

.switch6-light label, 
.switch6-light > span { line-height: 30px; vertical-align: middle;}

.switch6-light label {font-weight: 700; margin-bottom: px; max-width: 100%;}

.switch6-light input:focus ~ a, .switch6-light input:focus + label { outline: 1px dotted rgb(136, 136, 136); }
.switch6-light input { position: absolute; opacity: 0; z-index: 5; }
.switch6-light input:checked ~ a { right: 0%; }
.switch6-light > span { position: absolute; left: -100px; width: 100%; margin: 0px; padding-right: 100px; text-align: left; }
.switch6-light > span span { position: absolute; top: 0px; left: 0px; z-index: 5; display: block; width: 50%; margin-left: 100px; text-align: center; }
.switch6-light > span span:last-child { left: 50%; }
.switch6-light a { position: absolute; right: 50%; top: 0px; z-index: 4; display: block; width: 50%; height: 100%; padding: 0px; }





@if($brand->status == 1)
                                                <span class="col-green">Publish</span>
                                                @else
                                                <span class="col-red">Unpublish</span>
                                                @endif



<input type="hidden" id="hdn" class="form-control" name="hdn" value="{{$brand->id}}">  
 <!-- <form id="brand_update_form3" action="{{ route('brand.update',[$brand->id]) }}" method="post"> -->


        // $validated = $request->validate([
        //     'name' => 'required|max:255|unique:brands,name,'.$brand->id,
        //     'brand_url' => 'required|max:255',
        //     'logo' => 'required|max:255',
        // ]);

        // $brand->update( $request->only(['name', 'brand_url', 'logo']) );

        $brand = DB::table('brands')->where('id', $id)->update([
            'name' => request('name'),
            'brand_url' => request('brand_url'),
            'logo' => request('logo'),
            'status' => request('status'),  
        ]);


        admin/brand/{{$brand->id}}

         <button id="update_data" class="btn btn-raised btn-primary waves-effect" type="submit">
                                    Update
                                </button>


                                $brand = Brand::find($id);

        $brand->name = $request->name;
        $brand->brand_url = $request->brand_url;
        $brand->logo = $request->logo;
        $brand->status = $request->status;
        $brand->save();


         protected $fillable = ['name','brand_url','logo','status'];






         // Delete Brand
$("#BrandTable").on("click", ".delButton", function(){
   
  var cid = $(this).data("id");
   
  swal({
        title: "Are you sure?",
        text: "Once deleted, you will not be able to recover this brand!",
        icon: "warning",
        buttons: true,
        dangerMode: true,
      })
      .then((willDelete) => {
        if (willDelete) {
           console.log('success'); 

           $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                type: "DELETE",
                url: "brand/"+cid,
                success: function (data) {
                    //var colorName = 'alert-danger';
                    //var notifText = 'Brand successfully Deleted!';
                    //showNotification(colorName, notifText, placementFrom, placementAlign, animateEnter, animateExit);
                },
                error: function (data) {
                    console.log('Error:', data);
                }
            });
            swal("Poof! Your brand has been successfully deleted!", {
                icon: "success",
                setInterval('location.reload()', 2000);
            });
        } else {
          swal("Your imaginary file is safe!");
          console.log('error');
        }
    });   
});




swal({
        title: "Are you sure?",
        text: "Once deleted, you will not be able to recover this brand!",
        icon: "warning",
        buttons: true,
        dangerMode: true,
    })
    .then((willDelete) => {
        if (willDelete) {
           console.log('success'); 
           swal("Poof! Your brand has been deleted!", {
            icon: "success",
          });
        } else {
          swal("Your imaginary file is safe!");
          console.log('error');
        }
    });






    $("#BrandTable").on("click", ".delButton", function(){
   
    swal({
        title: "Are you sure?",
        text: "Once deleted, you will not be able to recover this brand!",
        icon: "warning",
        buttons: true,
        dangerMode: true,
    })
    .then((willDelete) => {
        if (willDelete) {
           var cid = $(this).data("id");
           $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

           $.ajax({
                    type: "DELETE",
                    url: "brand/"+cid,
                    success: function (data) {

                    },
                    error: function (data) {
                        console.log('Error:', data);
                    }
            });
        swal("Poof! Your brand has been deleted!", {
            icon: "success",
        });
        } else {
          swal("Your imaginary file is safe!");
          console.log('error');
        }
    });
});






confirm("Are You sure want to Restore All Brand !");
   console.log('test');

  $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

   $.ajax({
            type: "Get",
            url: "{{ route('restoreallbrand') }}",
            success: function () {
               //var colorName = 'alert-success';
               //var notifText = 'Brand successfully Restore!';
               //showNotification(colorName, notifText, placementFrom, placementAlign, animateEnter, animateExit);
               swal("Good job!", "Restore all brand successfully!", "success");
               setInterval('location.reload()', 2000);        // Using .reload() method.
            },
            error: function (data) {
                swal("Error!", "Request Fail!", "error");
                console.log('Error:', data);
            }
    });
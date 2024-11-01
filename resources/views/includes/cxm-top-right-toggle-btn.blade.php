@if(Auth::user()->type == 'client')
    <button class="btn btn-purple bg-purple btn-icon right_icon_toggle_btn rounded-circle" type="button" title="Show/Hide Right Sidebar" >
        <i class="zmdi zmdi-arrow-right"></i>
    </button>
@elseif(Auth::user()->type == 'staff')
    <button class="btn btn-success btn-icon right_icon_toggle_btn rounded-circle" type="button" title="Show/Hide Right Sidebar" >
        <i class="zmdi zmdi-arrow-right"></i>
    </button>
@else
    <button class="btn btn-info btn-icon right_icon_toggle_btn rounded-circle" type="button" title="Show/Hide Right Sidebar" >
        <i class="zmdi zmdi-arrow-right"></i>
    </button>
@endif
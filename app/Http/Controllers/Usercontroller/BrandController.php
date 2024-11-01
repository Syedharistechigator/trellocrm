<?php

namespace App\Http\Controllers\Usercontroller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Brand;
use App\Models\AssignBrand;
use Illuminate\Support\Facades\Auth;
use DB;

class BrandController extends Controller
{
    // Brnad Index
    public function index()
    {
        $id = Auth::user()->team_key;
        $data = AssignBrand::where('team_key',$id)->get();

        $team_brand = array();

        foreach($data as $a){
            $brand_key =  $a->brand_key;
            $brands = Brand::where('brand_key',$brand_key)->get();
            foreach($brands as $brand){
                $a['brandLogo'] = $brand->logo;
                $a['brandName'] = $brand->name;
                $a['brandUrl'] = $brand->brand_url;
                $a['brandCreate'] = $brand->created_at;
                $a['assignStatus'] = $brand->assign_status;
                $a['status'] = $brand->status;
                array_push($team_brand,$a);
            }    
            
        }
        return view('brand.index',compact('team_brand'));
    }

}

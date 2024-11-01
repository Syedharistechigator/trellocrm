<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;

class ApiBrandController extends Controller
{
    public function brand_url_list(){
        return response()->json(['brand_urls'=>Brand::where('status',1)->where('crawl',1)->pluck('brand_url')]);
    }
}

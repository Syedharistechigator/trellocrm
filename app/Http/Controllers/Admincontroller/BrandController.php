<?php

namespace App\Http\Controllers\Admincontroller;

use App\Http\Controllers\Controller;
use App\Models\AssignBrand;
use App\Models\Brand;
use App\Models\PaymentMethod;
use App\Models\PaymentMethodExpigate;
use App\Models\PaymentMethodPayArc;
use App\Models\Team;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Facades\Http;
use Yajra\DataTables\Facades\DataTables;

class BrandController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\JsonResponse
     */

    public function index()
    {
        $brands = array();
        $brandsList = Brand::all();

        foreach ($brandsList as $brand) {
            $merchantId = $brand->merchant_id;
            $brand['merchantName'] = PaymentMethod::where('id', $merchantId)->value('merchant');
            $brand_a = AssignBrand::where('brand_key', $brand->brand_key)->get();
            $teamName = "";
            foreach ($brand_a as $brand_b) {
                $team = Team::where('team_key', $brand_b->team_key)->value('name');
                $teamName .= $team . ", ";
            }
            $brand['assignTeams'] = $teamName;
            array_push($brands, $brand);
        }
        return view('admin.brand.index', compact('brands'));
    }

    /**Yajra table start */
//    public function index(Request $request)
//    {
//        if ($request->ajax()) {
//            $model = Brand::select();
//            if ($request->has('order')) {
//                $order = $request->input('order')[0]; // Assuming you only have one sorting column
//                $columnIndex = $order['column'];
//                $columnName = $request->input('columns')[$columnIndex]['data'];
//                $columnDirection = $order['dir'];
//
//                // You can customize sorting logic for different columns
//                switch ($columnName) {
//                    case 'name':
//                        $model->orderBy('name', $columnDirection);
//                        break;
//                    case 'brand_key':
//                        $model->orderBy('brand_key', $columnDirection);
//                        break;
//                    case 'brand_url':
//                        $model->orderBy('brand_url', $columnDirection);
//                        break;
//                    case 'is_paypal':
//                        $model->orderBy('is_paypal', $columnDirection);
//                        break;
//                    case 'assign_teams':
//                        $model->orderBy('assign_teams', $columnDirection);
//                        break;
//                    // Add more cases for other columns as needed
//                    default:
//                        // Default sorting logic, if no specific column is matched
//                        $model->orderBy('id', 'asc');
//                }
//            } else {
//                // Default sorting if no sorting parameter is provided
//                $model->orderBy('id', 'asc');
//            }
//
//            return DataTables::eloquent($model)
//                ->setRowId('id')
//                ->addColumn('logo', function ($model) {
//                    return '<img class="lazy" src="' . $model->logo . '" width="100" alt="Brand Logo" loading="lazy">';
//                })
//                ->addColumn('name', function ($model) {
//                    return $model->name;
//                })
//                ->filterColumn('name', function ($query, $keyword) {
//                    $query->where('name', 'like', "%$keyword%");
//                })
//                ->addColumn('assign_teams', function ($model) {
//                    return $model->getTeams->pluck('name')->implode(', ');
//                })
//                ->filterColumn('assign_teams', function ($query, $keyword) {
//                    $query->whereHas('getTeams', function ($subQuery) use ($keyword) {
//                        $subQuery->where('name', 'like', "%$keyword%");
//                    });
//                })
//                ->orderColumn('assign_teams', function ($query, $orderDirection) {
//                    $query->orderByRaw("(
//                        SELECT GROUP_CONCAT(name SEPARATOR ', ')
//                        FROM teams
//                        WHERE team_key IN (
//                            SELECT team_key
//                            FROM assign_brands
//                            WHERE brand_key = brands.brand_key
//                        )
//                    ) $orderDirection");
//                })
//                ->addColumn('brand_key', function ($model) {
//                    return $model->brand_key;
//                })
//                ->filterColumn('brand_key', function ($query, $keyword) {
//                    $query->where('brand_key', 'like', "%$keyword%");
//                })
//                ->addColumn('brand_url', function ($model) {
//                    return $model->brand_url;
//                })
//                ->addColumn('merchant_name', function ($model) {
//                    return $model->getMerchant->merchant ?? "";
//                })
//                ->filterColumn('merchant_name', function ($query, $keyword) {
//                    $query->whereHas('getMerchant', function ($subQuery) use ($keyword) {
//                        $subQuery->where('merchant', 'like', "%$keyword%");
//                    });
//                })
//                ->addColumn('is_paypal', function ($model) {
//                    return $model->is_paypal == 1 ? "Yes" : "No";
//                })
//                ->filterColumn('is_paypal', function ($query, $keyword) {
//                    $query->where('is_paypal', $keyword == 'Yes' || $keyword == 'yes' ? 1 : 0);
//                })
//                ->addColumn('assign_status', function ($model) {
//                    return $model->assign_status == 1 ? '<i class="zmdi zmdi-check-circle text-success" title="Active"></i>' :
//                        '<i class="zmdi zmdi-close-circle text-danger" title="Inactive"></i>';
//                })
//                ->addColumn('status', function ($model) {
//                    $toggleHtml = '<div class="custom-control custom-switch">';
//                    $toggleHtml .= '<span style="left: -41px; position: relative; top: 2px;">Unpublish</span>';
//                    $toggleHtml .= '<input data-id="' . $model->id . '" type="checkbox" class="custom-control-input toggle-class" id="customSwitch' . $model->id . '" ' . ($model->status ? 'checked' : '') . '>';
//                    $toggleHtml .= '<label class="custom-control-label" for="customSwitch' . $model->id . '"></label>';
//                    $toggleHtml .= '<span style="position: relative; top: 2px;">Publish</span>';
//                    $toggleHtml .= '</div>';
//                    return $toggleHtml;
//                })
//                ->addColumn('action', function ($model) {
//                    $btn = '<a title="Edit" href="' . route('brand.edit', [$model->id], '/edit') . '" class="btn btn-warning btn-sm btn-round"><i class="zmdi zmdi-edit"></i></a>
//                            <a title="Delete" data-id="' . $model->id . '" data-type="confirm" href="javascript:void(0);" class="btn btn-danger btn-sm btn-round delButton"><i class="zmdi zmdi-delete"></i></a>';
//                    return $btn;
//                })
//                ->orderColumn('assign_teams', 'getTeams.name $1')
//                ->rawColumns(['logo', 'name', 'assign_teams', 'brand_key', 'brand_url', 'merchant_name', 'is_paypal', 'assign_status', 'status', 'action'])
//                ->toJson();
//        }
//        return view('admin.brand.index');
//    }
    /**Yajra table end */

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function create()
    {
        $methods = PaymentMethod::where('status', 1)->get();
        $payment_method_expigates = PaymentMethodExpigate::where('status', 1)->get();
        $payment_method_payarcs = PaymentMethodPayArc::where('status', 1)->get();
        $brand_types = array_unique(array_merge(['Book', 'Design'], Brand::where('status', 1)->get()->pluck('brand_type')->unique()->toArray()));
        return view('admin.brand.create', compact('brand_types', 'methods', 'payment_method_expigates', 'payment_method_payarcs'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            $brand = Brand::create([
                'name' => $request->get('name'),
                'brand_type' => $request->get('brand_type', 'Book'),
                'brand_key' => random_int(100000, 999999),
                'brand_url' => $request->get('brand_url'),
                'logo' => $request->get('logo'),
                'status' => $request->get('status'),
                'merchant_id' => $request->get('merchant'),
                'is_paypal' => $request->get('is_paypal'),
                'is_amazon' => $request->get('is_amazon'),
                'expigate_id' => $request->get('expigate_id', 1),
                'payarc_id' => $request->get('payarc_id', 1),
                'default_merchant_id' => $request->get('default_merchant_id'),
                'crawl' => $request->get('crawl'),
                'checkout_version' => $request->get('checkout_version'),
                'smtp_host' => $request->get('smtp_host'),
                'smtp_email' => $request->get('smtp_email'),
                'smtp_password' => $request->get('smtp_password'),
                'smtp_port' => $request->get('smtp_port'),
                'admin_email' => $request->get('admin_email'),
                'phone' => $request->get('phone'),
                'phone_secondary' => $request->get('phone_secondary'),
                'email' => $request->get('email'),
                'email_href' => $request->get('email_href'),
                'contact_email' => $request->get('contact_email'),
                'contact_email_href' => $request->get('contact_email_href'),
                'website_name' => $request->get('website_name'),
                'website_logo' => $request->get('website_logo'),
                'address' => $request->get('address'),
                'chat' => htmlspecialchars($request->get('chat'), ENT_QUOTES, 'UTF-8'),
            ]);
            return response()->json(['success' => 'Brand created successfully!', 'data' => $brand]);

        } catch (QueryException $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Brand $brand
     * @return \Illuminate\Http\Response
     */
    public function show(Brand $brand)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Brand $brand
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function edit($id)
    {
        $brand = Brand::find($id);
        $methods = PaymentMethod::where('status', 1)->get();
        $payment_method_expigates = PaymentMethodExpigate::where('status', 1)->where('name', '!=', 'Amazon')->get();
        $payment_method_payarcs = PaymentMethodPayArc::where('status', 1)->get();
        $brand_types = array_unique(array_merge(['Book', 'Design'], Brand::where('status', 1)->get()->pluck('brand_type')->unique()->toArray()));
        return view('admin.brand.edit', compact('brand', 'brand_types', 'methods', 'payment_method_expigates', 'payment_method_payarcs'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Brand $brand
     * @return Brand|Brand[]|\Illuminate\Http\Response|\LaravelIdea\Helper\App\Models\_IH_Brand_C
     */
    public function update(Request $request, $id)
    {

        $brand = Brand::find($id);
        $brand->name = $request->name;
        $brand->brand_type = $request->brand_type;
        $brand->brand_url = $request->brand_url;
        $brand->logo = $request->logo;
        $brand->status = $request->status;
        $brand->merchant_id = $request->merchant;
        $brand->is_paypal = $request->is_paypal;
        $brand->is_amazon = $request->get('is_amazon', 0);
        $brand->expigate_id = $request->get('expigate_id', 1);
        $brand->payarc_id = $request->get('payarc_id', 1);
        $brand->default_merchant_id = $request->get('default_merchant_id');
        $brand->crawl = $request->get('crawl');
        $brand->checkout_version = $request->get('checkout_version');
        $brand->smtp_host = $request->smtp_host;
        $brand->smtp_email = $request->smtp_email;
        $brand->smtp_password = $request->smtp_password;
        $brand->smtp_port = $request->smtp_port;
        $brand->admin_email = $request->admin_email;
        $brand->phone = $request->phone;
        $brand->phone_secondary = $request->phone_secondary;
        $brand->email = $request->email;
        $brand->email_href = $request->email_href;
        $brand->contact_email = $request->contact_email;
        $brand->contact_email_href = $request->contact_email_href;
        $brand->website_name = $request->website_name;
        $brand->website_logo = $request->website_logo;
        $brand->address = $request->address;
        $brand->chat =  htmlspecialchars($request->get('chat'), ENT_QUOTES, 'UTF-8');
        $brand->save();

        return $brand;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Brand $brand
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Brand::find($id)->delete();
    }


    public function trashedbrand()
    {
        $brands = Brand::onlyTrashed()->get();
        return view('admin.brand.trashed', compact('brands'));
    }

    public function restore($id)
    {
        Brand::onlyTrashed()->whereId($id)->restore();
    }

    public function restoreAll()
    {
        Brand::onlyTrashed()->restore();
    }


    public function changeStatus(Request $request)
    {
        $brand = Brand::find($request->brand_id);
        $brand->status = $request->status;
        $brand->save();

        return response()->json(['success' => 'Status change successfully.']);
    }

    public function brandforceDelete($id)
    {
        Brand::onlyTrashed()->whereId($id)->forceDelete();
    }


}

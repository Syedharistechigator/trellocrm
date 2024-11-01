<?php

namespace App\Http\Controllers\Admincontroller;

use App\Http\Controllers\Controller;
use App\Http\Controllers\EmailController;
use App\Models\IpAddress;
use App\Models\Lead;
use App\Models\Team;
use App\Models\Brand;
use App\Models\Admin;
use App\Models\LeadStatus;

//use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Notifications\LeadNotification;
use App\Notifications\LeadPushNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Carbon\Carbon;
use App\Models\LeadComments;
use App\Models\AssignBrand;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\QueryException;

use DB;
use Illuminate\Support\Str;
use PharIo\Manifest\Email;
use Yajra\DataTables\Facades\DataTables;


class LeadController extends EmailController
{
//    public function validate_ip(Request $request)
//    {
//        $ip = $request->input('ip');
//        $apiKey = 'YRBxsNK06zRg3v3GtcAOidJ0A2Wfspt5'; // Retrieve from config or .env
//
//        $countries = ['US', 'CA'];
//        $parameters = [
//            'country' => $countries,
//        ];
//
//        $formattedParameters = http_build_query($parameters);
//
//        $url = sprintf(
//            'https://www.ipqualityscore.com/api/json/ip/%s/%s?%s',
//            $apiKey,
//            $ip,
//            $formattedParameters
//        );
//
//        $httpClient = new Client();
//        $response = $httpClient->get($url);
//        $result = json_decode($response->getBody(), true);
//
//        return response()->json($result);
//    }
//    public function validate_phone(Request $request)
//    {
//        $phone = $request->input('phone');
//        $apiKey = 'YRBxsNK06zRg3v3GtcAOidJ0A2Wfspt5'; // Retrieve from config or .env
//
//        $countries = ['US', 'CA'];
//        $parameters = [
//            'country' => $countries,
//        ];
//
//        $formattedParameters = http_build_query($parameters);
//
//        $url = sprintf(
//            'https://www.ipqualityscore.com/api/json/phone/%s/%s?%s',
//            $apiKey,
//            $phone,
//            $formattedParameters
//        );
//
//        $httpClient = new Client();
//        $response = $httpClient->get($url);
//        $result = json_decode($response->getBody(), true);
//
//        return response()->json($result);
//    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
//    public function index()
//    {
//        //dd(Carbon::today());
//        $leads = Lead::orderBy('id', 'desc')->whereDate('created_at', Carbon::today())->get();
//        $teams = Team::where('status', '1')->get();
//        $brands = Brand::where('status', '1')->get();
//        $leadsStatus = LeadStatus::all();
//
//        $leadsdata = array();
//        foreach ($leads as $lead) {
//
//            $brandKey = $lead->brand_key;
//            $statusId = $lead->status;
//            $lead['brandName'] = DB::table('brands')->where('brand_key', $brandKey)->value('name');
//
//            $leadStatus = LeadStatus::where('id', $statusId)->first();
//            $lead['status'] = $leadStatus->status;
//            $lead['statusColor'] = $leadStatus->leadstatus_color;
//
//            array_push($leadsdata, $lead);
//        }
//        return view('admin.lead.index', compact('leadsdata', 'teams', 'brands', 'leadsStatus'));
//    }

    public function index(Request $request)
    {
        $result = $this->getData($request, new Lead());
        $leads = $result['data'];
        $fromDate = $result['fromDate'];
        $toDate = $result['toDate'];
        $teamKey = $result['teamKey'];
        $brandKey = $result['brandKey'];
        $teams = Team::where('status', '1')->get();
        $brands = Brand::where('status', '1')->get();
        $leadsStatus = LeadStatus::all();
        return view('admin.lead.index', compact('fromDate', 'toDate', 'teamKey', 'brandKey', 'leads', 'teams', 'brands', 'leadsStatus'));
    }

    public function YD_index(Request $request)
    {
        $teams = Team::where('status', '1')->get();
        $brands = Brand::where('status', '1')->get();
        $leadsStatus = LeadStatus::all();
        if ($request->ajax()) {

            $model = Lead::query();
            if ($request->filled('team_key') && $request->team_key > 0) {
                $model->where('team_key', $request->team_key)->get();
            }
            if ($request->filled('brand_key') && $request->brand_key > 0) {
                $model->where('brand_key', $request->brand_key)->get();
            }
            if ($request->filled('from_date') && $request->filled('to_date')) {
                $model->whereBetween('created_at', [$request->from_date, $request->to_date]);
            } else {
                $model->whereDate('created_at', Carbon::today());
            }
            $model->orderBy('id', 'desc');
            if ($request->filled('search.value')) {
                $searchValue = $request->input('search.value');
                $model->where(function ($query) use ($searchValue) {
                    $query->where('lead_contact', 'LIKE', "%$searchValue%")
                        ->orWhereHas('getBrandName', function ($subquery) use ($searchValue) {
                            $subquery->where('name', 'LIKE', "%$searchValue%");
                        })
                        ->orWhere('created_at', 'LIKE', "%$searchValue%");
                });
            }
            return DataTables::eloquent($model)
                /** Add sensitive columns to remove from response*/
                ->removeColumn('ip_response', 'ip_response', 'is_ip_verify', 'is_number_verify', 'email_response', 'is_email_verify', 'number_response', 'server_response', 'deleted_at')
                ->setRowId('id')
                ->addColumn('checkbox', function ($model) {
                    return '<input type="checkbox" name="ids[' . $model->id . ']" value="' . $model->id . '">';
                })
                ->editColumn('lead_title', function ($model) {
                    return '<a class="text-warning" href="' . route('lead.show', $model->id) . '"> ' . $model->title . '</a>';
                })
                ->addColumn('lead_contact', function ($model) {
                    return $model->name . '<br>' . $model->email . '<br>' . $model->lead_ip;
                })
                ->editColumn('created_at', function ($model) {
                    return '<span class="text-muted">' . $model->created_at->format('j F, Y') . '<br>' . $model->created_at->format('h:i:s A') . '<br>' . $model->created_at->diffForHumans() . '</span>';
                })
                ->addColumn('get_brand_name', function ($model) {
                    return '<span class="text-muted"> ' . ($model->getBrandName ? $model->getBrandName->name : "") . '</span>';
                })
                ->editColumn('lead_value', function ($model) {
                    return '<span class="text-muted"> ' . ($model->value ? ('$' . $model->value . '.00') : '---') . '</span>';
                })
                ->editColumn('lead_status', function ($model) {
                    return '<span class="badge badge-' . ($model->getStatus ? $model->getStatus->leadstatus_color : "") . ' rounded-pill">' . ($model->getStatus ? $model->getStatus->status : "") . '</span>';
                })
                ->addColumn('action', function ($model) {
                    $btn = "";
                    if ($model->view == '0') {
                        $btn .= '<a href = "javascript:void(0);" class="btn btn-info btn-sm btn-round" ><i class="zmdi zmdi-eye-off" ></i ></a >';
                    } else {
                        $btn .= '<a href="javascript:void(0);" class="btn btn-info btn-sm btn-round"><i class="zmdi zmdi-eye"></i></a>';
                    }
                    $btn .= '<a title="View" href="' . route('lead.show', $model->id) . '" class="btn btn-warning btn-sm btn-round"><i class="zmdi zmdi-open-in-new"></i></a>
                                  <a title="Change Status" data-id="' . $model->id . '" data-type="confirm" href="javascript:void(0);" class=" btn btn-info btn-sm btn-round statusChange" data-toggle="modal" data-target="#changeStatusModal"><i class="zmdi zmdi-settings"></i></a>
                                  <a title="Comments" data-id="' . $model->id . '" href="#" class=" btn btn-neutral btn-sm btn-round LeadComments" data-toggle="modal" data-target="#cxmCommentsModal"><i class="zmdi zmdi-comments text-warning"></i></a>';
                    if (Auth::guard('admin')->user()->type == 'super') {
                        $btn .= '<a title = "Delete" data - id = "' . $model->id . '" data - type = "confirm" href = "javascript:void(0);" class="btn btn-danger btn-sm btn-round delButton" ><i class="zmdi zmdi-delete" ></i ></a >';
                    }
                    return $btn;
                })
                ->rawColumns(['checkbox', 'lead_id', 'lead_title', 'lead_contact', 'created_at', 'get_brand_name', 'lead_value', 'lead_status', 'action'])
                ->toJson();
        }
        return view('admin.lead.YD_index', compact('teams', 'brands', 'leadsStatus'));
    }

//    public function getIpResponse()
//    {
//        /** Expired Token
//         * 12b59c8b5bf82e
//         * 478789134a7b9f
//         * c4d5bd23f6904c 90%
//         */
//        $curl = curl_init();
//        if ($_SERVER['REMOTE_ADDR'] == '::1' || $_SERVER['REMOTE_ADDR'] == '127.0.0.1') {
//
//            curl_setopt($curl, CURLOPT_URL, "https://ipinfo.io/" . $_SERVER['REMOTE_ADDR'] . "/json?token=590a01c8690db0");
//        } else {
//            curl_setopt($curl, CURLOPT_URL, "https://ipinfo.io/" . $_SERVER['REMOTE_ADDR'] . "/json?token=590a01c8690db0");
//        }
//        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
//        $ipResponse = curl_exec($curl);
//        $ipResponse = (array)json_decode($ipResponse);
//        if ($ipResponse && isset($ipResponse['status']) && $ipResponse['status'] == 429) {
//            Log::error('Token Expired ');
//
//            $ipResponse['ip'] = "Token Expired";
//            $ipResponse['city'] = "Token Expired";
//            $ipResponse['state'] = "Token Expired";
//            $ipResponse['country'] = "Token Expired";
//        }
//        $ipResponse['ip'] = $ipResponse['ip'] ?? null;
//        $ipResponse['city'] = $ipResponse['city'] ?? null;
//        $ipResponse['state'] = $ipResponse['region'] ?? $ipResponse['state'] ?? null;
//        $ipResponse['country'] = $ipResponse['country'] ?? null;
//        $ipResponse['postal'] = $ipResponse['postal'] ?? null;
//        return $ipResponse;
//    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */

    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'required',
        ];

        $messages = [
            'name.required' => 'The name field is required.',
            'name.string' => 'The name must be a string.',
            'name.max' => 'The name may not be greater than :max characters.',
            'email.required' => 'The email field is required.',
            'email.email' => 'Please enter a valid email address.',
            'phone.required' => 'The phone field is required.',
        ];
        // Validate the input
        $validator = Validator::make($request->all(), $rules, $messages);
        // Check for validation errors
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }

        $admin = Admin::first();

        $brandName = DB::table('brands')->where('brand_key', $request->get('brand_key'))->first();
        $teamName = DB::table('teams')->where('team_key', $request->get('team_key'))->value('name');
        $ipResponse = $this->getIpResponse();
        $lead_data = [
            'team_key' => $request->get('team_key'),
            'brand_key' => $request->get('brand_key'),
            'title' => $request->get('title'),
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'phone' => $request->get('phone'),
            'details' => $request->get('details'),
            'source' => $request->get('source'),
            'value' => $request->get('value'),

            'lead_ip' => $_SERVER['REMOTE_ADDR'] . ' - ' . $_SERVER['SERVER_ADDR'],
            'lead_city' => $ipResponse['city'],
            'lead_state' => $ipResponse['state'],
            'lead_zip' => $ipResponse['postal'],
            'lead_country' => $ipResponse['country'],

            'lead_url' => $request->get('lead_url'),
            'keyword' => $request->get('keyword'),
            'matchtype' => $request->get('matchtype'),
            'msclkid' => $request->get('msclkid'),
            'gclid' => $request->get('gclid'),
            'server_response' => 'null',
            'view' => '0',
            'status' => '1'
        ];
//        $result = $this->BrandEmailConfig($request->get('brand_key'),false);
//        if ($result->getStatusCode() != 200) {
//            return response()->json(['error' => $result->getData()->error ?: 'Something went wrong with the brand key.']);
//        }
        $lead_data2 = $lead_data;
        $lead_data2['more_details'] = array_diff_key($request->all(), $lead_data);

        foreach (array_diff_key($request->all(), $lead_data) as $key => $value) {
            $lead_data2[$key] = $value;
        }
        $this->send_inquiry_email("Inquiry Form Submission ({$brandName->name})", $lead_data2, $brandName->name, $teamName);

        $lead_data['more_details'] = json_encode($request->all());

        try {
            $leads = Lead::create($lead_data);
        } catch (\Exception $e) {
            // Handle the database creation error here (if necessary)
            return response()->json([
                'message' => 'Failed to create lead.',
            ], 500);
        }

        $additionalData = [
            'brand_name' => $brandName->name,
            'team_name' => $teamName,
            'lead_id' => $leads->id,
        ];

        $lead = array_merge($leads->toArray(), $additionalData);
        Notification::send($admin, new LeadNotification($lead));
        Notification::send($admin, new LeadPushNotification($lead));

        return response()->json([
            'data' => $lead,
            'message' => 'Add Lead Successfully Created!'
        ], 200);
    }

    function convert_filesize($bytes, $decimals = 2)
    {
        $size = array('B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
        $factor = floor((strlen($bytes) - 1) / 3);
        return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$size[$factor];
    }


        public function form_submission(Request $request)
        {
        $brandKey = $request->input('brand_key');
        if (!$brandKey) {
            return response()->json(['errors' => 'Brand Key is required'], 422);
        }


        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
        ];
        $messages = [
            'name.required' => 'The name field is required.',
            'email.required' => 'The email field is required.',
            'phone.required' => 'The phone field is required.',
        ];
        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }
        $brand = Brand::where('brand_key', $brandKey)->first();
        if (!$brand) {
            return response()->json(['errors' => 'Oops! Brand not found'], 422);
        }
//        $teams = $brand->getTeams()->get();
//        $team = null;
//        if (Str::contains($request->url(), 'uspto-filing')) {
//            foreach ($teams as $team_val) {
//                if (!$team_val->getUsers->where('type', 'tm-ppc')->first()) {
//                    $team = $team_val;
//                    break;
//                }
//            }
//        } else {
//            $team = $teams->first();
//        }
        $team = $brand->getTeams()->first();
//        if (!$team) {
//            return response()->json(['errors' => 'Oops! Team not found'], 422);
//        }
        $price = preg_replace('/[^0-9]/', '', $request->input('value', 0));
        $price = is_numeric($price) ? $price : 0;

        $reffer = '';
        if (isset($_SERVER["HTTP_REFERER"]) && !empty($_SERVER["HTTP_REFERER"])) {
            $reffer = $_SERVER["HTTP_REFERER"];
        }
        $ipResponse = $this->getIpResponse();
//        $ipAddresses = $testingIPs = ['199.250.222.28','127.0.0.1','203.135.30.178',];
        $ipAddresses = IpAddress::where('list_type', 1)->where('status', 1)->get()->pluck('ip_address')->toArray();

        $leadStatus = in_array($ipResponse['ip'], $ipAddresses, true) ? 10 : 1;
        $leadData = [
            'team_key' => optional($team)->team_key,
            'brand_key' => $brandKey,
            'title' => $request->get('title') != '' ? $request->get('title') : 'Inquiry Form',
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'phone' => $request->get('phone'),
            'details' => $request->get('details'),
            'source' => $request->get('source'),
            'value' => $price,
            'options' => json_encode($request->get('optional')),
            'lead_ip' => $_SERVER['REMOTE_ADDR'] . ' - ' . ($_SERVER['SERVER_ADDR'] ?? ""),
            'lead_city' => $ipResponse['city'],
            'lead_state' => $ipResponse['state'],
            'lead_zip' => $ipResponse['postal'],
            'lead_country' => $ipResponse['country'],

            'lead_url' => $request->get('lead_url'),
            'keyword' => $request->get('keyword'),
            'matchtype' => $request->get('matchtype'),
            'msclkid' => $request->get('msclkid'),
            'gclid' => $request->get('gclid'),
            'server_response' => $reffer,
            'view' => '0',
            'status' => $leadStatus,
        ];
        $leadData2 = $leadData;
        $leadData2['more_details'] = array_diff_key($request->all(), $leadData);

        foreach (array_diff_key($request->all(), $leadData) as $key => $value) {
            $leadData2[$key] = $value;
        }

        $this->send_inquiry_email("Inquiry Form Submission ($brand->name)", $leadData2, $brand->name, optional($team)->name);


        $leadData['more_details'] = json_encode($request->except('file'));
        $files = [];
        if ($request->hasFile('file')) {
            foreach ($request->file('file') as $file) {
                if ($file->isValid()) {
                    $file_name = time() . '-' . random_int(11, 20) . '.' . $file->getClientOriginalExtension();
                    $file_directory = str_contains($file->getMimeType(), 'image') ? 'images' : 'files';
                    $file_directory_path = public_path("assets/{$file_directory}/leads/{$file->getMimeType()}/");

                    $filedata = [
                        'file_name' => $file_name,
                        'original_name' => $file->getClientOriginalName(),
                        'mime_type' => $file->getMimeType(),
                        'file_size' => $this->convert_filesize($file->getSize()),
                        'extension' => $file->getClientOriginalExtension(),
                        'file_path' => $file_directory_path . $file_name
                    ];
                    $files[] = $filedata;
                    $file->move($file_directory_path, $file_name);
                } else {
                    $files[] = [
                        'original_name' => 'Invalid file ' . $file->getClientOriginalName()
                    ];
                }
            }
        }
        $leadData['file'] = json_encode($files);
        try {
            $leads = Lead::create($leadData);
        } catch (QueryException $e) {
            return response()->json(['message' => 'Database error: ' . $e->getMessage()], 500);
        } catch (\Exception $e) {

            return response()->json(['message' => 'Failed to create lead.',], 500);
        }

        $additionalData = [
            'brand_name' => $brand->name,
            'team_name' => optional($team)->name,
            'lead_id' => $leads->id,
            'optional' => $request->get('optional'),
        ];


        $lead = array_merge($leads->toArray(), $additionalData);
        if (!empty($files)) {
            $lead['file'] = isset($files[0]['original_name']) ? $files[0]['original_name'] : "";
        }
        $admin = Admin::first();
        Notification::send($admin, new LeadNotification($lead));
        Notification::send($admin, new LeadPushNotification($lead));

        return response()->json(['data' => $lead, 'message' => 'Add Lead Successfully Created!']);
    }


    /**
     * Display the specified resource.
     *
     * @param \App\Models\Lead $lead
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $lead = Lead::find($id);
        $brandName = DB::table('brands')->where('brand_key', $lead->brand_key)->value('name');
        $leadStatus = LeadStatus::where('id', $lead->status)->first();
        $lead['status'] = $leadStatus->status;
        $lead['statusColor'] = $leadStatus->leadstatus_color;
        $lead['brandName'] = $brandName;

        return view('admin.lead.show', compact('lead'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Lead $lead
     * @return \Illuminate\Http\Response
     */
    public function edit(Lead $lead)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Lead $lead
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Lead $lead)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Lead $lead
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Lead::find($id)->delete();
    }


    public function team(Request $request)
    {

        $searchText = $request->search;

        $leads = array();

        if ($searchText == 0) {
            $teamLeads = Lead::orderBy('id', 'desc')->get();
        } else {
            $teamLeads = Lead::where('team_key', $searchText)->get();
        }

        $html = "";
        $html .= '<thead><tr><th>&nbsp;</th><th>ID#</th><th>Title</th><th>Contact</th><th>Date</th><th>Brand</th><th>Value</th><th class="text-center">Status</th><th class="text-center">Action</th></tr></thead>';

        $html .= '<tbody>';

        foreach ($teamLeads as $lead) {

            $brandKey = $lead->brand_key;
            $statusId = $lead->status;
            $brandName = DB::table('brands')->where('brand_key', $brandKey)->value('name');
            $lead['brandName'] = $brandName;

            $leadStatus = LeadStatus::where('id', $statusId)->first();
            $lead['status'] = $leadStatus->status;
            $lead['statusColor'] = $leadStatus->leadstatus_color;

            array_push($leads, $lead);


            $html .= '<tr>';
            $html .= '<td class="align-middle"><input type="checkbox" name="ids[' . $lead->id . ']" value="{{$lead->id}}"></td>';
            $html .= '<td class="align-middle">' . $lead->id . '</td>';
            $html .= '<td class="align-middle"><a class="text-warning" href="' . route('lead.show', $lead->id) . '"><span class="zmdi zmdi-open-in-new"></span> ' . $lead->title . '</a></td>';
            $html .= '<td>' . $lead->name . '<br>' . $lead->email . '<br>' . $lead->lead_ip . '</td>';
            $html .= '<td>' . $lead->created_at->format('j F, Y') . '<br>' . $lead->created_at->format('h:i:s A') . '<br>' . $lead->created_at->diffForHumans() . '</td>';
            $html .= '<td>' . $lead->brandName . '</td>';
            $html .= '<td>$' . $lead->value . '.00</td>';
            $html .= '<td class="text-center align-middle"><span class="badge badge-' . $lead->statusColor . ' rounded-pill">' . $lead->status . '</span></td>';
            $html .= '<td>';
            if ($lead->view == '0') {
                $html .= '<a href="javascript:void(0);" class="btn btn-info btn-sm btn-round"><i class="zmdi zmdi-eye"></i></a>';
            } else {
                $html .= '<a href="javascript:void(0);" class="btn btn-info btn-sm btn-round"><i class="zmdi zmdi-eye"></i></a>';
            }
            $html .= '<a title="View" href="' . route('lead.show', $lead->id) . '" class="btn btn-warning btn-sm btn-round"><i class="zmdi zmdi-open-in-new"></i></a>';
            $html .= '<a title="Change Status" data-id="' . $lead->id . '" data-type="confirm" href="javascript:void(0);" class=" btn btn-info btn-sm btn-round statusChange" data-toggle="modal" data-target="#changeStatusModal"><i class="zmdi zmdi-settings"></i></a>';
            $html .= '<a title="Comments" data-id="' . $lead->id . '" href="#" class=" btn btn-neutral btn-sm btn-round LeadComments" data-toggle="modal" data-target="#cxmCommentsModal"><i class="zmdi zmdi-comments text-warning"></i></a>';
            $html .= '<a title="Delete" data-id="' . $lead->id . '" data-type="confirm" href="javascript:void(0);" class="btn btn-danger btn-sm btn-round delButton"><i class="zmdi zmdi-delete"></i></a>';
            $html .= '</td>';
            $html .= '</tr>';

        }

        $html .= '</tbody>';
        return $html;
    }


    public function brand(Request $request)
    {

        $searchText = $request->search;

        $leads = array();

        if ($searchText == 0) {
            $teamLeads = Lead::all();
        } else {
            $teamLeads = Lead::where('brand_key', $searchText)->get();
        }

        $html = "";
        $html .= '<thead><tr><th>&nbsp;</th><th>ID#</th><th>Title</th><th>Contact</th><th>Date</th><th>Brand</th><th>Value</th><th class="text-center">Status</th><th class="text-center">Action</th></tr></thead>';

        $html .= '<tbody>';

        foreach ($teamLeads as $lead) {

            $brandKey = $lead->brand_key;
            $statusId = $lead->status;
            $brandName = DB::table('brands')->where('brand_key', $brandKey)->value('name');
            $lead['brandName'] = $brandName;

            $leadStatus = LeadStatus::where('id', $statusId)->first();
            $lead['status'] = $leadStatus->status;
            $lead['statusColor'] = $leadStatus->leadstatus_color;
            array_push($leads, $lead);

            $html .= '<tr>';
            $html .= '<td class="align-middle"><input type="checkbox" name="ids[' . $lead->id . ']" value="{{$lead->id}}"></td>';
            $html .= '<td class="align-middle">' . $lead->id . '</td>';
            $html .= '<td class="align-middle"><a class="text-warning" href="' . route('lead.show', $lead->id) . '"><span class="zmdi zmdi-open-in-new"></span> ' . $lead->title . '</a></td>';
            $html .= '<td>' . $lead->name . '<br>' . $lead->email . '<br>' . $lead->lead_ip . '</td>';
            $html .= '<td>' . $lead->created_at->format('j F, Y') . '<br>' . $lead->created_at->format('h:i:s A') . '<br>' . $lead->created_at->diffForHumans() . '</td>';
            $html .= '<td>' . $lead->brandName . '</td>';
            $html .= '<td>$' . $lead->value . '.00</td>';
            $html .= '<td class="text-center align-middle"><span class="badge badge-' . $lead->statusColor . ' rounded-pill">' . $lead->status . '</span></td>';
            $html .= '<td>';
            if ($lead->view == '0') {
                $html .= '<a href="javascript:void(0);" class="btn btn-info btn-sm btn-round"><i class="zmdi zmdi-eye"></i></a>';
            } else {
                $html .= '<a href="javascript:void(0);" class="btn btn-info btn-sm btn-round"><i class="zmdi zmdi-eye"></i></a>';
            }
            $html .= '<a title="View" href="' . route('lead.show', $lead->id) . '" class="btn btn-warning btn-sm btn-round"><i class="zmdi zmdi-open-in-new"></i></a>';
            $html .= '<a title="Change Status" data-id="' . $lead->id . '" data-type="confirm" href="javascript:void(0);" class=" btn btn-info btn-sm btn-round statusChange" data-toggle="modal" data-target="#changeStatusModal"><i class="zmdi zmdi-settings"></i></a>';
            $html .= '<a title="Comments" data-id="' . $lead->id . '" href="#" class=" btn btn-neutral btn-sm btn-round LeadComments" data-toggle="modal" data-target="#cxmCommentsModal"><i class="zmdi zmdi-comments text-warning"></i></a>';
            $html .= '<a title="Delete" data-id="' . $lead->id . '" data-type="confirm" href="javascript:void(0);" class="btn btn-danger btn-sm btn-round delButton"><i class="zmdi zmdi-delete"></i></a>';
            $html .= '</td>';
            $html .= '</tr>';

        }

        $html .= '</tbody>';
        return $html;
    }


    public function monthlydata(Request $request)
    {

        $fromDate = $request->startDate;
        $toDate = $request->endDate;

        $leads = array();

        DB::enableQueryLog();
        // $teamLeads = Lead::whereDay('created_at',$data[2])
        // ->whereMonth('created_at', $data[1])
        // ->whereYear('created_at', $data[0])
        // ->orderBy('id', 'desc')->get();
        $teamLeads = Lead::whereBetween('created_at', [$fromDate, $toDate])->get();

        //$quries = DB::getQueryLog();
        //dd($quries);

        $html = "";
        $html .= '<thead><tr><th>&nbsp;</th><th>ID#</th><th>Title</th><th>Contact</th><th>Date</th><th>Brand</th><th>Value</th><th class="text-center">Status</th><th class="text-center">Action</th></tr></thead>';

        $html .= '<tbody>';

        foreach ($teamLeads as $lead) {

            $brandKey = $lead->brand_key;
            $statusId = $lead->status;
            $brandName = DB::table('brands')->where('brand_key', $brandKey)->value('name');
            $lead['brandName'] = $brandName;
            $leadStatus = LeadStatus::where('id', $statusId)->first();
            $lead['status'] = $leadStatus->status;
            $lead['statusColor'] = $leadStatus->leadstatus_color;

            array_push($leads, $lead);

            $html .= '<tr>';
            $html .= '<td class="align-middle"><input type="checkbox" name="ids[' . $lead->id . ']" value="{{$lead->id}}"></td>';
            $html .= '<td class="align-middle">' . $lead->id . '</td>';
            $html .= '<td class="align-middle"><a class="text-warning" href="' . route('lead.show', $lead->id) . '"><span class="zmdi zmdi-open-in-new"></span> ' . $lead->title . '</a></td>';
            $html .= '<td>' . $lead->name . '<br>' . $lead->email . '<br>' . $lead->lead_ip . '</td>';
            $html .= '<td>' . $lead->created_at->format('j F, Y') . '<br>' . $lead->created_at->format('h:i:s A') . '<br>' . $lead->created_at->diffForHumans() . '</td>';
            $html .= '<td>' . $lead->brandName . '</td>';
            $html .= '<td>$' . $lead->value . '.00</td>';
            $html .= '<td class="text-center align-middle"><span class="badge badge-' . $lead->statusColor . ' rounded-pill">' . $lead->status . '</span></td>';
            $html .= '<td>';
            if ($lead->view == '0') {
                $html .= '<a href="javascript:void(0);" class="btn btn-info btn-sm btn-round"><i class="zmdi zmdi-eye"></i></a>';
            } else {
                $html .= '<a href="javascript:void(0);" class="btn btn-info btn-sm btn-round"><i class="zmdi zmdi-eye"></i></a>';
            }
            $html .= '<a title="View" href="' . route('lead.show', $lead->id) . '" class="btn btn-warning btn-sm btn-round"><i class="zmdi zmdi-open-in-new"></i></a>';
            $html .= '<a title="Change Status" data-id="' . $lead->id . '" data-type="confirm" href="javascript:void(0);" class=" btn btn-info btn-sm btn-round statusChange" data-toggle="modal" data-target="#changeStatusModal"><i class="zmdi zmdi-settings"></i></a>';
            $html .= '<a title="Comments" data-id="' . $lead->id . '" href="#" class=" btn btn-neutral btn-sm btn-round LeadComments" data-toggle="modal" data-target="#cxmCommentsModal"><i class="zmdi zmdi-comments text-warning"></i></a>';
            $html .= '<a title="Delete" data-id="' . $lead->id . '" data-type="confirm" href="javascript:void(0);" class="btn btn-danger btn-sm btn-round delButton"><i class="zmdi zmdi-delete"></i></a>';
            $html .= '</td>';
            $html .= '</tr>';
        }

        $html .= '</tbody>';
        return $html;
    }


    public function leadStatus(Request $request)
    {

        $leadId = $request->lead_id;
        $status = $request->LeadStatus;

        $lead = Lead::find($leadId);
        $lead->status = $status;
        $lead->save();

        return $lead;
    }

    public function onlyTrashedlead()
    {
        $leads = Lead::onlyTrashed()->get();

        $teams = Team::where('status', '1')->get();
        $brands = Brand::where('status', '1')->get();
        $leadsStatus = LeadStatus::all();

        $leadsdata = array();
        foreach ($leads as $lead) {

            $brandKey = $lead->brand_key;
            $statusId = $lead->status;
            $brandName = DB::table('brands')->where('brand_key', $brandKey)->value('name');
            $lead['brandName'] = $brandName;

            $leadStatus = LeadStatus::where('id', $statusId)->first();
            $lead['status'] = $leadStatus->status;
            $lead['statusColor'] = $leadStatus->leadstatus_color;

            array_push($leadsdata, $lead);
        }

        return view('admin.lead.trashed', compact('leadsdata', 'teams', 'brands', 'leadsStatus'));
    }

    public function leadRestore(Request $request, $id)
    {
        Lead::withTrashed()->find($id)->restore();
    }

    public function leadforceDelete(Request $request, $id)
    {
        Lead::onlyTrashed()->find($id)->forceDelete();
    }


    //get Lead Comments
    public function get_lead_comments($id)
    {

        $commentsData = array();
        $comments = LeadComments::where('leadid', $id)->orderBy('created_at', 'desc')->get();

        foreach ($comments as $comment) {
            if ($comment->type == 'admin') {
                $comment['userName'] = Admin::where('id', $comment->creatorid)->value('name');;
            } else {
                $comment['userName'] = User::where('id', $comment->creatorid)->value('name');
            }
            $comment['commentDate'] = date('M d, Y', strtotime($comment->created_at));
            array_push($commentsData, $comment);
        }
        return $commentsData;
    }


    //create lead comments
    public function admin_create_comments(Request $request)
    {

        $creatorid = Auth::user()->id;

        $comment = LeadComments::create([
            'creatorid' => $creatorid,
            'leadid' => $request->get('lead_id'),
            'comment_text' => $request->get('comment'),
            'type' => $request->get('type'),
        ]);

        return $comment;
    }

    //Delete Leads
    public function delete_leads(Request $request)
    {
        $delIds = $request->ids;

        Lead::whereIn('id', $delIds)->delete();
        return redirect()->back();
    }


}

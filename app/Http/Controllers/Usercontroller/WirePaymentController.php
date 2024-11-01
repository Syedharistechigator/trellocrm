<?php

namespace App\Http\Controllers\Usercontroller;

use App\Http\Controllers\Controller;
use App\Models\AssignBrand;
use App\Models\Brand;
use App\Models\Client;
use App\Models\User;
use App\Models\WirePaymentModel;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Validator;

class WirePaymentController extends Controller
{


    public function index(Request $request)
    {
        $team_key = Auth::user()->team_key;
        $type = Auth::user()->type;
        if ($type != 'lead') {
            return redirect()->route('dashboard')->with('error', 'Oops! You are not allowed to access it.');
        }

        $brands = null;
        /** Team Brands */
        if ($type === 'qa') {
            $brands = Brand::withoutTrashed()->get();
            $assign_brands = AssignBrand::whereHas('getBrandWithOutTrashed')->get();
        } elseif ($type === 'ppc') {
            $assign_brands = AssignBrand::whereIn('team_key', auth()->user()->assigned_teams ?? [])->whereHas('getBrandWithOutTrashed')->get();
        } else {
            $assign_brands = AssignBrand::where('team_key', $team_key)->whereHas('getBrandWithOutTrashed')->get();
        }
        /** Team Clients */
        $teamClients = Client::where('team_key', $team_key)->get();

        /** Team Members */
        $members = User::where(['team_key' => $team_key, 'status' => 1])->where('type', '!=', 'client')->orderBy('type')->get();

        /** Creating Payment Instance to pass in dynamic filter */
        $query = new WirePaymentModel();

        $result = $this->userDataFilter($request, $query, $assign_brands);
        $brandKey = $result['brandKey'];
        $fromDate = $result['fromDate'];
        $toDate = $result['toDate'];
        $wire_payments = $result['data'];
        $paymentApprovals = $wire_payments->pluck('payment_approval', 'id')->toArray();

        return view('payment.wire-payment.index', compact('brands', 'brandKey', 'fromDate', 'toDate', 'assign_brands', 'members', 'teamClients', 'wire_payments', 'paymentApprovals'));
    }

    public function store(Request $request)
    {
        try {
            $rules = [
                "brand_key" => "required",
                "agent_id" => "required",
                "sales_type" => "required|in:Fresh,Upsale",
                "name" => "required|string|max:255",
                "email" => "required|email|max:255",
                "phone" => "required|string",
                "project_title" => "required|string|max:255",
                "description" => "nullable|string",
                "due_date" => [
                    'nullable',
                    'date',
                    'date_format:Y-m-d',
                    'after:' . Date::now()->subYears(2)->format('Y-m-d'),
                    'before_or_equal:' . Date::now()->addDay()->format('Y-m-d'),
                ],
                "amount" => "required|numeric",
                "transaction_id" => "nullable|string|max:255",
                "screenshots.*" => "nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048"
            ];

            $messages = [
                "brand_key.required" => "The Brand field is required.",
                "agent_id.required" => "The Agent field is required.",
                "sales_type.required" => "The Sales Type field is required.",
                "sales_type.in" => "The Sales Type field must be either Fresh or Upsale.",
                "name.required" => "The Client Name field is required.",
                "email.required" => "The Client Email field is required.",
                "email.email" => "The Client Email must be a valid email address.",
                "phone.required" => "The Client Phone Number field is required.",
                "project_title.required" => "The Project Title field is required.",
                'due_date.date' => 'Please enter a valid date for the payment.',
                'due_date.after' => 'The payment date must be after :date.',
                'due_date.before_or_equal' => 'The payment date must be before or equal to :date.',
                "amount.required" => "The Amount field is required.",
                "amount.numeric" => "The Amount field must be a number.",
                "screenshots.*.image" => "Each file must be an image.",
                "screenshots.*.mimes" => "Each image must be a file of type: jpeg, png, jpg, gif, svg.",
                "screenshots.*.max" => "Each image must not exceed 2MB."
            ];

            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            $paymentApprovals = null;
            if ($request->filled('paymentApprovals')) {
                $paymentApprovals = json_decode($request->get('paymentApprovals'), true);
                $paymentApprovalids = array_keys($paymentApprovals);
                $paymentApprovals = WirePaymentModel::whereIn('id', $paymentApprovalids)->pluck('payment_approval', 'id')->toArray();
            }
            $wire_payment = new WirePaymentModel();
            $wire_payment->actor_id = Auth::id();
            $wire_payment->actor_type = get_class(Auth::user() ?? "");
            $this->extracted($request, $wire_payment);
            if ($wire_payment->save()) {
                $wire_payment->due_date = Carbon::parse($wire_payment->due_date)->format('j F, Y');
                $wire_payment->load([
                    'getBrand' => function ($query) {
                        $query->select('id', 'brand_key', 'name', 'logo', 'brand_url');
                    },
                    'getAgent' => function ($query) {
                        $query->select('id', 'name');
                    },
                ]);
                return response()->json(['status' => 1, 'success' => 'Record created successfully.', 'data' => $wire_payment, 'paymentApprovals' => $paymentApprovals]);
            }
            return response()->json(['error' => 'Failed to create record.', 'paymentApprovals' => $paymentApprovals], 400);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    function convert_filesize($bytes, $decimals = 2)
    {
        $size = array('B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
        $factor = floor((strlen($bytes) - 1) / 3);
        return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$size[$factor];
    }


    public function extracted(Request $request, $wire_payment): void
    {
        $wire_payment->brand_key = $request->get('brand_key');
        $wire_payment->team_key = auth()->user()->team_key;
        $wire_payment->agent_id = $request->get('agent_id');
        $wire_payment->sales_type = $request->get('sales_type');
        $wire_payment->client_name = $request->get('name');
        $wire_payment->client_email = $request->get('email');
        $wire_payment->client_phone = $request->get('phone');
        $wire_payment->project_title = $request->get('project_title');
        $wire_payment->description = $request->get('description');
        $wire_payment->due_date = Carbon::parse($request->get('due_date'))->format('Y-m-d');
        $wire_payment->amount = $request->get('amount');
        $wire_payment->transaction_id = $request->get('transaction_id');
        $wire_payment->payment_approval = 'Pending';

        $screenshots = [];
        if ($request->hasFile('screenshots')) {
            foreach ($request->file('screenshots') as $file) {
                if (!$file->isValid()) {
                    throw new \RuntimeException('Invalid attachment file.');
                }
                $file_name = time() . '-' . auth()->user()->id . random_int(11, 20) . '.' . $file->getClientOriginalExtension();
                $file_directory = str_contains($file->getMimeType(), 'image') ? 'images' : 'files';
                $file_directory_path = public_path("assets/{$file_directory}/wire-payments/{$file->getMimeType()}/");

                $filedata = [
                    'file_name' => $file_name,
                    'original_name' => $file->getClientOriginalName(),
                    'mime_type' => $file->getMimeType(),
                    'file_size' => $this->convert_filesize($file->getSize()),
                    'extension' => $file->getClientOriginalExtension(),
                    'file_path' => $file_directory_path . $file_name
                ];
                $screenshots[] = $filedata;
                $file->move($file_directory_path, $file_name);

            }
        }
        $wire_payment->screenshot = json_encode($screenshots);

    }
}

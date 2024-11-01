<?php

namespace App\Http\Controllers\Admincontroller;

use App\Http\Controllers\Controller;
use App\Models\AssignBrand;
use App\Models\Brand;
use App\Models\Team;
use App\Models\User;
use App\Models\WirePaymentModel;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;
use Validator;

class WirePaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = new WirePaymentModel();
        $result = $this->getData($request, $query);
        $wire_payments = $result['data'];
        $paymentApprovals = $wire_payments->pluck('payment_approval', 'id')->toArray();
        $fromDate = $result['fromDate'];
        $toDate = $result['toDate'];
        $teamKey = $result['teamKey'];
        $brandKey = $result['brandKey'];
        $teams = Team::where('status', '1')->get();
        $brands = Brand::where('status', '1')->orderby('name', 'Asc')->get();
        $assign_brands = AssignBrand::whereHas('getBrandWithOutTrashed')->get();
        return view('admin.payment.wire-payment.index', compact('wire_payments', 'paymentApprovals', 'fromDate', 'toDate', 'teamKey', 'brandKey', 'teams', 'brands', 'assign_brands'));
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
        $wire_payment->team_key = $request->get('team_key');
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
        $wire_payment->payment_approval = 'Approved';

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

    public function create_payment($wire_payment)
    {
        $controller_request = ['team_key' => $wire_payment->team_key,
            'brand_key' => $wire_payment->brand_key,
            'agent_id' => $wire_payment->agent_id,
            'project_title' => $wire_payment->project_title,
            'description' => $wire_payment->description,
            'value' => $wire_payment->amount,
            'name' => $wire_payment->client_name,
            'email' => $wire_payment->client_email,
            'phone' => $wire_payment->client_phone,
            'due_date' => $wire_payment->due_date,
            'track_id' => $wire_payment->transaction_id ?? "0000",
            'merchant' => 'Wire Transfer',
            'sales_type' => $wire_payment->sales_type,
            'settlement' => 'settled successfully',
            'actor_id' => $wire_payment->actor_id,
            'actor_type' => $wire_payment->actor_type,
        ];
        return app()->make(adminPaymentController::class)->admin_direct_payment(new Request($controller_request));
    }

    /**
     * Handle payment approvals from the request
     */
    private function handlePaymentApprovals(Request $request)
    {
        $paymentApprovals = null;
        if ($request->filled('paymentApprovals')) {
            $paymentApprovals = json_decode($request->get('paymentApprovals'), true);
            $paymentApprovalids = array_keys($paymentApprovals);
            $paymentApprovals = WirePaymentModel::whereIn('id', $paymentApprovalids)->pluck('payment_approval', 'id')->toArray();
        }

        return $paymentApprovals;
    }

    /**
     * Load relations for the wire payment
     */
    private function loadWirePaymentRelations($wire_payment)
    {
        $wire_payment->load([
            'getBrand' => function ($query) {
                $query->select('id', 'brand_key', 'name', 'logo', 'brand_url');
            },
            'getAgent' => function ($query) {
                $query->select('id', 'name');
            },
            'actor' => function ($query) {
                $query->select('id', 'name');
            },
        ]);
    }
    private function loadApprovalRelations($wire_payment)
    {
        $wire_payment->load([
            'approval_actor' => function ($query) {
                $query->select('id', 'name');
            },
        ]);
    }
    public function store(Request $request)
    {
        try {
            $rules = [
                "brand_key" => "required",
                "team_key" => "required",
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
                "team_key.required" => "Please select a valid assigned brand.",
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

            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            $wire_payment = new WirePaymentModel();
            $wire_payment->actor_id = Auth::id();
            $wire_payment->actor_type = get_class(Auth::guard('admin')->user() ?? "");
            $this->extracted($request, $wire_payment);
            $wire_payment->approval_updated_by = Auth::id();
            $wire_payment->approval_actor_type = get_class(Auth::guard('admin')->user() ?? "");
            if ($wire_payment->save()) {
                $paymentApprovals = $this->handlePaymentApprovals($request);
                $this->loadWirePaymentRelations($wire_payment);

                $response = $this->create_payment($wire_payment);
                if ($response->getStatusCode() == 200) {
                    $wire_payment->due_date = Carbon::parse($wire_payment->due_date)->format('j F, Y');
                    $this->loadApprovalRelations($wire_payment);
                    return response()->json(['status' => 1, 'success' => 'Payment added and status updated successfully.', 'data' => $wire_payment, 'paymentApprovals' => $paymentApprovals,
                        'response_status' => $response->getStatusCode(), 'response_body' => $response->getContent()]);
                }
                $paymentApprovals[$wire_payment->id] = 'Pending';
                $wire_payment->payment_approval = 'Pending';
                $wire_payment->approval_updated_by = null;
                $wire_payment->approval_actor_type = null;
                $wire_payment->save();
                $wire_payment->due_date = Carbon::parse($wire_payment->due_date)->format('j F, Y');
                $this->loadApprovalRelations($wire_payment);

                return response()->json([
                    'error' => 'Record added, Failed to update status.',
                    'status' => 0,
                    'data' => $wire_payment,
                    'paymentApprovals' => $paymentApprovals,
                    'response_status' => $response->getStatusCode(),
                    'response_body' => $response->getContent()], $response->getStatusCode());
            }
            return response()->json(['error' => 'Failed to create record.'], 400);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function payment_approval(Request $request, $id)
    {
        try {
            $rules = [
                "payment_approval" => "required|in:Approved,Not Approved",
            ];
            $messages = [
                "payment_approval.required" => "The Payment Status field is required.",
                "payment_approval.in" => "The Payment Status field must be either Approved or Not Approved.",
            ];
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $wire_payment = WirePaymentModel::where('id', $id)->first();
            if (!$wire_payment) {
                return response()->json(['error' => 'Oops! Payment not found.'], 404);
            }
            $wire_payment->payment_approval = $request->get('payment_approval');
            $wire_payment->approval_updated_by = Auth::id();
            $wire_payment->approval_actor_type = get_class(Auth::guard('admin')->user() ?? "");
            if ($wire_payment->save()) {
                $paymentApprovals = $this->handlePaymentApprovals($request);
                if ($request->get('payment_approval') === 'Approved') {
                    $response = $this->create_payment($wire_payment);
                    if ($response->getStatusCode() == 200) {
                        $this->loadApprovalRelations($wire_payment);
                        return response()->json(['status' => 1, 'success' => 'Payment added and status updated successfully.', 'data' => $wire_payment, 'paymentApprovals' => $paymentApprovals,
                            'response_status' => $response->getStatusCode(), 'response_body' => $response->getContent()]);
                    }
                    $wire_payment->payment_approval = 'Pending';
                    $wire_payment->approval_updated_by = null;
                    $wire_payment->approval_actor_type = null;
                    $wire_payment->save();
                    $paymentApprovals[$wire_payment->id] = 'Pending';
                    $this->loadApprovalRelations($wire_payment);
                    return response()->json([
                        'error' => 'Failed to update status.',
                        'status' => 0,
                        'paymentApprovals' => $paymentApprovals,
                        'response_status' => $response->getStatusCode(),
                        'response_body' => $response->getContent()], $response->getStatusCode());
                }
                $this->loadApprovalRelations($wire_payment);
                return response()->json(['status' => 1, 'success' => 'Status updated successfully.', 'data' => $wire_payment, 'paymentApprovals' => $paymentApprovals,]);
            }
            return response()->json(['error' => 'Failed to update status.'], 400);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function view_attachment(Request $request, WirePaymentModel $wire_payment)
    {
        try {
            if ($wire_payment->screenshot) {
                $attachments = json_decode($wire_payment->screenshot, true);
                if (count($attachments) > 0) {
                    return response()->json(['status' => 1, 'success' => 'Attachments fetched successfully', 'data' => $attachments, 'count' => count($attachments)]);
                }
            }
            return response()->json(['status' => 1, 'success' => 'No attachments found for this record', 'count' => 0], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function brand_agents($id)
    {
        try {
            $agents = User::where('team_key', $id)->where('status', 1)->where('type', '!=', 'client')->orderBy('type')->get()->map(function ($user) {
                return [
                    'id' => $user->id,
                    'data' => $user->name,
                    'team_key' => $user->team_key,
                ];
            });
            return response()->json(['status' => 1, 'success' => 'Agents fetched successfully.!', 'agents' => $agents, 'count' => count($agents)]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}

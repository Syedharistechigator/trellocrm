<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\EmailController;
use App\Models\Brand;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\InvoiceSignature;
use App\Models\Lead;
use App\Models\Project;
use App\Models\SplitPayment;
use App\Models\TrackingIp;
use App\Models\User;
use App\Rules\Base64Image;
use App\Traits\BrandEmailConfigurationTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Config;

class ApiInvoiceController extends EmailController
{
    use BrandEmailConfigurationTrait;

    /**
     * Display a listing of the resource.
     *
     * @return string
     */
    public function index()
    {
//        $invoices = Invoice::all();
//        if (count($invoices) == 0) {
//            return response()->json(['error' => 'Invoice not found.'], 404);
//        }
//        return response()->json(['invoices' => $invoices]);
        $invoices = DB::table('invoices')
            ->join('brands', 'invoices.brand_key', '=', 'brands.brand_key')
            ->join('users', 'invoices.agent_id', '=', 'users.id')
            ->join('clients', 'invoices.clientid', '=', 'clients.id')
            ->join('payment_transactions_logs', 'invoices.invoice_key', '=', 'payment_transactions_logs.invoiceid')
            ->select('brands.name as brand_name', 'users.name as agent_name', 'clients.name as client_name', 'payment_transactions_logs.*', 'invoices.*')
            ->orderBy('invoices.created_at', 'desc')->take(100)->get();
        return response()->json(['invoices' => $invoices]);
    }

    public function test(Request $request, $brand_key = null, $sandbox = false)
    {
        try {
//        dd('under construction');
//        $sandbox = filter_var($sandbox, FILTER_VALIDATE_BOOLEAN);
//        $result = $this->BrandEmailConfig($brand_key,$sandbox);
//        if($result->getStatusCode() != 200){
//            return response()->json(['error'=> $result->getData()->error ?:'Something went wrong with the brand key.']);
//        }


            if ($request->has('mail_mailer')) {
                // Set Laravel environment variables for email configuration
                Config::set('mail.mailer', $request->input('mail_mailer'));
            }
            if ($request->has('mail_host')) {
                Config::set('mail.host', $request->input('mail_host'));
            }
            if ($request->has('mail_port')) {
                Config::set('mail.port', $request->input('mail_port'));
            }
            if ($request->has('mail_username')) {
                Config::set('mail.username', $request->input('mail_username'));
            }
            if ($request->has('mail_password')) {
                Config::set('mail.password', $request->input('mail_password'));
            }
            if ($request->has('mail_encryption')) {
                Config::set('mail.encryption', $request->input('mail_encryption'));
            }
            if ($request->has('mail_from_address')) {
                Config::set('mail.from.address', $request->input('mail_from_address'));
            }
            if ($request->has('mail_from_name')) {
                Config::set('mail.from.name', $request->input('mail_from_name'));
            }

            $subject = 'testing subject';
            $name = 'Developer Michael';
            $email = 'developer.michael.09@gmail.com';
            $result = $this->send_email($subject, $name, $email);

            return response()->json(['success' => "Email send successfully"]);
        } catch (\Exception $e) {
            \Log::error('Email sending error: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to send the email. Please check your email settings.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

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
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    /**
     * @throws \Exception
     */
    public function create_invoice(Request $request)
    {
        $rules = [
            'id' => 'required|integer|min:1',
            'is_split' => 'required|integer|in:0,1',
        ];

        $messages = [
            'is_split.required' => 'The Split Payment field is required.',
            'is_split.integer' => 'The Split Payment field must be an integer.',
            'is_split.in' => 'The Split Payment field must be either 1 or 0.',
        ];
        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }
        $id = $request->get('id');
        $is_split = $request->get('is_split');
        $split = [];
        $creatorid = 1;

        $lead = Lead::where('id', $id)->first();
        if (!$lead) {
            return response()->json([
                'errors' => ['lead' => 'Oops! Lead not found.'],
            ], 422);
        }
        $validator = Validator::make($lead->getAttributes(), [
            'name' => 'required',
            'email' => 'required',
            'phone' => 'required',
        ], [
            'name.required' => 'Invalid ! The lead name field is empty.',
            'email.required' => 'Invalid ! The lead email field is empty.',
            'phone.required' => 'Invalid ! The lead phone field is empty.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }
        $client = Client::where('email', $lead->email)->first();
        $user = User::where('email', $lead->email)->first();

        if (!$client) {
            $client = Client::create([
                'team_key' => $lead->team_key,
                'brand_key' => $lead->brand_key,
                'creatorid' => $creatorid,
                'name' => $lead->name,
                'email' => $lead->email,
                'phone' => $lead->phone,
                'agent_id' => $creatorid,
                'client_created_from_leadid' => 0,
                'client_description' => "",
                'address' => "",
                'status' => '1'
            ]);
            $user = User::create([
                'name' => $lead->name,
                'email' => $lead->email,
                'phone' => $lead->phone,
                'password' => Hash::make('12345678'),
                'type' => 'client',
                'team_key' => $lead->team_key,
                'clientid' => $client->id
            ]);
        }
        $tax_percentage = '0';
        $tax_amount = '0.00';
        $total_amount = $lead->value;

        $project = Project::create([
            'team_key' => $lead->team_key,
            'brand_key' => $lead->brand_key,
            'creatorid' => $creatorid,
            'clientid' => $client->id,
            'agent_id' => 1,
            'asigned_id' => '0',
            'category_id' => '1',
            'project_title' => $request->project_title,
            'project_description' => $request->project_description,
            'project_status' => '1',
            'project_progress' => '1',
            'project_cost' => $lead->value,
        ]);

        $invoice_key = random_int(11, 99) . substr(time(), 1, 3) . random_int(11, 99) . substr(time(), 8, 2);
        $invoice = Invoice::create([
            'invoice_num' => 'INV-' . random_int(100000, 999999),
            'invoice_key' => $invoice_key,
            'team_key' => $lead->team_key,
            'brand_key' => $lead->brand_key,
            'creatorid' => $creatorid,
            'clientid' => $client->id,
            'agent_id' => 1,
            'due_date' => Carbon::now(),
            'invoice_descriptione' => "Invoice Description",
            'sales_type' => "Fresh",
            'status' => 'due',
            'project_id' => $project->id,
            'cur_symbol' => 'USD',
            'final_amount' => $lead->value,
            'tax_percentage' => $tax_percentage,
            'tax_amount' => $tax_amount,
            'total_amount' => $total_amount,
            'creator_role' => 'ADM',
            'is_split' => $is_split,
        ]);
        if ($total_amount > 3 && $is_split == 1) {
            $split[] = SplitPayment::create([
                'invoice_id' => $invoice_key,
                'amount' => 1,
            ]);
            $split[] = SplitPayment::create([
                'invoice_id' => $invoice_key,
                'amount' => 2,
            ]);
        }
        $leadAllInvoices = Invoice::where(['team_key' => $lead->team_key, 'brand_key' => $lead->brand_key, 'final_amount' => $lead->value, 'total_amount' => $lead->value])->get();
        $leadAllInvoices->makeHidden(['payment_id']);

        return response()->json([
            'message' => 'Invoice created successfully!',
//            'lead' => $lead,
//            'client' => $client,
//            'user' => $user,
//            'project' => $project,
            'invoice' => $invoice,
            'split' => $split,
//            'lead_all_invoices' => $leadAllInvoices,
        ]);

    }

    public function create_tracking_ip(Request $request, $id = null)
    {
        $rules = [
            'id' => 'required|integer|min:1',
        ];
        $validator = Validator::make(['id' => $id], $rules);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }
        $brand = Brand::where('brand_key', $id)->where('status', 1)->first();
        if (!$brand) {
            return response()->json(['errors' => 'Oops! Brand not found.'], 422);
        }

        if ($request->has('checkout_version')) {
            $brand->checkout_version = $request->checkout_version;
            $brand->save();
        }

//        $ipResponse = Http::get("https://ipinfo.io/{$_SERVER['REMOTE_ADDR']}/json?token=590a01c8690db0");
////        if ($this->isLocalIp($ipResponse->json('ip'))) {
////            return response()->json(['errors' => 'Local IP address detected.'], 400);
////        }
//        if (!$ipResponse->successful()) {
//            return response()->json(['errors' => 'API request failed.'], 500);
//        }
//        if ($ipResponse->json('ip') == null) {
//            return response()->json(['errors' => 'Invalid API response.'], 500);
//        }
        $ipResponse = $this->getIpResponse();
        if (isset($ipResponse['all_token_expire']) && $ipResponse['all_token_expire'] == true) {
            return response()->json(['errors' => 'API request failed. All Expire'], 500);
        }
        $result = TrackingIp::create([
            'brand_id' => $id,
            'url' => $request->url,
            'ip' => $ipResponse['ip']??null,
            'ip_response' => json_encode($ipResponse),
        ]);

        $brand->is_visitor = 1;
        $result['is_visited'] = $brand->save();
        return response()->json(['data' => $result]);
    }

    private function isLocalIp($ip)
    {
        $localIpRanges = ['10.0.0.0/8', '172.16.0.0/12', '192.168.0.0/16', '127.0.0.0/8'];
        foreach ($localIpRanges as $range) {
            [$subnet, $mask] = explode('/', $range);
            if ((ip2long($ip) & ~((1 << (32 - $mask)) - 1)) == ip2long($subnet)) {
                return true;
            }
        }
        return false;
    }

    public function add_signature(Request $request)
    {
        /** Defining rules to validate */
        $rules = [
            'invoice_id' => 'required|integer',
            'signature' => ['required', new Base64Image],
        ];
        /** Defining rules message to show validation messages */
        $messages = [
            'invoice_id.required' => 'The Invoice number field is required.',
        ];
        /** Validating through Validator Library */
        $validator = Validator::make($request->all(), $rules, $messages);
        /** Return errors if validator fails */
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }

        /** Opening try catch block to catch any error*/
        try {
            /** Returning error if invoice not found */
            if (!Invoice::where('invoice_key', $request->input('invoice_id'))->first()) {
                return response()->json(['errors' => 'Oops! Invoice not found.'], 404);
            }
            /** Now save it to Invoice signature table*/
            $signature = new InvoiceSignature();
            $signature->invoice_id = $request->input('invoice_id');
            $signature->signature = $request->input('signature');
            $signature->save();

            /** Return success response */
            return response()->json(['success'=>'Signature added successfully.']);
        } catch (\Exception $e) {
            /** Return error response */
            return response()->json(['errors' => $e->getMessage(),], 422);
        }
    }
}

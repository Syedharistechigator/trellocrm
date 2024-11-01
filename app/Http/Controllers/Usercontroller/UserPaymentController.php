<?php

namespace App\Http\Controllers\Usercontroller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Payment;
use App\Models\Invoice;
use App\Models\Project;
use App\Models\User;
use App\Models\Brand;
use App\Models\Refund;
use App\Models\AssignBrand;
use App\Models\Client;
use App\Models\Team;
use Illuminate\Support\Facades\Hash;

class UserPaymentController extends Controller
{
//    public function index()
//    {
//        $id = Auth::user()->team_key;
//        $type = Auth::user()->type;
//        $agentId = Auth::user()->id;
//        $staff_Div = Auth::user()->staff_division;
//
//        //Team Brand
//        $data = AssignBrand::where('team_key',$id)->get();
//
//        $teamBrand = array();
//
//        foreach($data as $a){
//            $brand_key =  $a->brand_key;
//            $brands = Brand::where('brand_key',$brand_key)->get();
//            foreach($brands as $brand){
//                $a['brandKey'] = $brand->brand_key;
//                $a['brandName'] = $brand->name;
//                array_push($teamBrand,$a);
//            }
//        }
//
//        $paymentData = array();
//
//        // if($type == 'staff'){
//        //     $payments = Payment::where(['team_key'=>$id,'agent_id'=>$agentId])->get();
//        // }elseif($type == 'qa'){
//        //     $payments = Payment::all();
//        // }
//        // else{
//        //     $payments = Payment::where('team_key',$id)->get();
//        // }
//
//        // if($type == 'qa' or $type == 'ppc'){
//        //     $payments = Payment::all();
//        // }
//        // else{
//        //     $payments = Payment::where('team_key',$id)->get();
//        // }
//
//        if($type == 'qa' or $type == 'ppc'){
////            $payments = Payment::orderBy('id', 'desc')->Paginate(15);
//            $payments = Payment::orderBy('id', 'desc')->get();
//        }
//        else{
////            $payments = Payment::where('team_key',$id)->orderBy('id', 'desc')->Paginate(15);
////            $payments = Payment::where('team_key',$id)->orderBy('id', 'desc')->get();
//
//            $brandKeys = array_column($teamBrand, 'brandKey');
//            $payments = Payment::whereIn('brand_key', $brandKeys)
//                ->orderBy('id', 'desc')
//                ->get();
//        }
//
//        // $payments = Payment::where('team_key',$id)->get();
//        foreach($payments as $payment){
//            $agent_id   = $payment->agent_id;
//            $brandKey  = $payment->brand_key;
//
//            $agent_name = User::where('id',$agent_id)->value('name');
//            $payment['agentName'] = $agent_name;
//
//            $brand_name = Brand::where('brand_key', $brandKey)->value('name');
//            $payment['brandName'] = $brand_name;
//
//            array_push($paymentData,$payment);
//        }
//
//
//        //Team Client
//        $teamClients = Client::where('team_key',$id)->get();
//
//        //Team Members
//        $members = User::where(['team_key' => $id , 'status' => 1])
//        ->where('type','!=','client')->orderBy('type')->get();
//
//        return view('payment.payment',compact('paymentData','teamBrand','members','teamClients','payments'));
//    }
    public function index(Request $request)
    {
        $team_key = Auth::user()->team_key;
        $type = Auth::user()->type;
        $brands = null;

        /** Team Members */
        $members = User::where(['team_key' => $team_key, 'status' => 1])->where('type', '!=', 'client')->orderBy('type')->get();

        /** Team Brands */
        if ($type === 'qa') {
            $brands = Brand::withoutTrashed()->get();
            $assign_brands = AssignBrand::whereHas('getBrandWithOutTrashed')->get();
        } elseif ($type === 'ppc') {
            $assignedTeams = auth()->user()->assigned_teams ?? [];
            if (auth()->user()->team_key && !in_array(auth()->user()->team_key, $assignedTeams)) {
                $assignedTeams[] = auth()->user()->team_key;
            }
            $assign_brands = AssignBrand::whereIn('team_key', $assignedTeams)->whereHas('getBrandWithOutTrashed')->get();
            $members = User::whereIn('team_key', $assignedTeams)->where('status', 1)->where('type', '!=', 'client')->get();
        } else {
            $assign_brands = AssignBrand::where('team_key', $team_key)->whereHas('getBrandWithOutTrashed')->get();
        }
        /** Team Clients */
//        $teamClients = Client::whereIn('brand_key', $assign_brands->pluck('brand_key'))->orWhere('team_key', $team_key)->get();


        /** Creating Payment Instance to pass in dynamic filter */
        $query = new Payment();

        $result = $this->userDataFilter($request, $query, $assign_brands);
        $brandKey = $result['brandKey'];
        $fromDate = $result['fromDate'];
        $toDate = $result['toDate'];
        $payments = $result['data'];

        return view('payment.payment', compact('brands', 'brandKey', 'fromDate', 'toDate', 'assign_brands', 'members', 'payments'));
    }

    public function show_client_payment()
    {

        $loginClientId = Auth::user()->clientid;

        $paymentData = array();

        $payments = Payment::where('clientid', $loginClientId)->get();
        foreach ($payments as $payment) {

            $projectId = $payment->project_id;

            $payment['projectTitle'] = Project::where('id', $projectId)->value('project_title');

            array_push($paymentData, $payment);
        }

        return view('payment.clientpayment', compact('paymentData'));
    }

    public function get_payment_detail($id)
    {
        $payment = Payment::find($id);
        return $payment;
    }

    public function payment_refund(Request $request)
    {

        $paymentId = $request->get('payment_id');
        $type = $request->get('type');

        Refund::create([
            'team_key' => $request->get('team_key'),
            'brand_key' => $request->get('brand_key'),
            'agent_id' => $request->get('agent_id'),
            'client_id' => $request->get('client_id'),
            'invoice_id' => $request->get('invoice_id'),
            'payment_id' => $paymentId,
            'authorizenet_transaction_id' => $request->get('auth_transaction_id'),
            'amount' => $request->get('amount'),
            'reason' => $request->get('reason'),
            'type' => $type,
            'qa_approval' => 1
        ]);

        if ($type == "refund") {
            $paymentStatus = 2;
        } else {
            $paymentStatus = 3;
        }

        $payment = Payment::find($paymentId);
        $payment->payment_status = $paymentStatus;
        $payment->save();

        Invoice::where('invoice_key', $request->get('invoice_id'))->update(['status' => $type]);

    }

    public function show_payment_refund()
    {

        $id = Auth::user()->team_key;
        $type = Auth::user()->type;
        $staffDev = Auth::user()->staff_division;
        $agentId = Auth::user()->id;

        if ($type == 'hob' or $type == 'qa') {
            $refunds = Refund::all();
        } elseif ($type == 'staff') {
            $refunds = Refund::where(['team_key' => $id, 'agent_id' => $agentId])->get();
        } else {
            $refunds = Refund::where('team_key', $id)->get();
        }

        return view('payment.refund', compact('refunds'));
    }

    public function refund_status_approved(Request $request)
    {

        $id = $request->id;

        $refund = Refund::find($id);
        $refund->qa_approval = 1;
        $refund->save();

        $type = $refund->type;

        if ($type == "refund") {
            $paymentStatus = 2;
        } else {
            $paymentStatus = 3;
        }

        $payment = Payment::find($refund->payment_id);
        $payment->payment_status = $paymentStatus;
        $payment->save();

        Invoice::where('invoice_key', $refund->invoice_id)->update(['status' => $type]);

        return $refund;

    }

    /**DM => Update*/
    public function direct_payment(Request $request)
    {
        try {
            if (Auth::user()->type != 'lead' || str_contains(request()->server('SERVER_NAME'), 'uspto-filing') == false) {
                return response()->json(['error' => 'Oops! You are not allowed to access it.'], 500);
            }

            $team_key = Auth::user()->team_key;
            $creator_id = Auth::user()->id;

            $invoice_key = random_int(11, 99) . substr(time(), 1, 3) . random_int(11, 99) . substr(time(), 8, 2);
            $invoice_num = 'INV-' . random_int(100000, 999999);
            $client = Client::where('email', $request->get('email'))->first();
            if (!$client) {
                $client = Client::create(['team_key' => $team_key, 'brand_key' => $request->get('brand_key'), 'creatorid' => $creator_id, 'name' => $request->get('name'), 'email' => $request->get('email'), 'phone' => $request->get('phone'), 'agent_id' => $request->get('agent_id'), 'status' => '1']);
                if ($client) {
                    User::create(['name' => $request->get('name'), 'email' => $request->get('email'), 'phone' => $request->get('phone'), 'password' => Hash::make('12345678'), 'type' => 'client', 'team_key' => $team_key, 'clientid' => $client->id]);
                }
            }
            $project = Project::create(['team_key' => $team_key, 'brand_key' => $request->get('brand_key'), 'creatorid' => $creator_id, 'clientid' => $client->id, 'agent_id' => $request->get('agent_id'), 'asigned_id' => '0', 'category_id' => '1', 'project_title' => $request->get('project_title'), 'project_description' => $request->get('description'), 'project_status' => '1', 'project_progress' => '1', 'project_cost' => $request->get('value')]);
            if ($project) {
                $invoice = Invoice::create(['invoice_num' => $invoice_num, 'invoice_key' => $invoice_key, 'team_key' => $team_key, 'brand_key' => $request->get('brand_key'), 'creatorid' => $creator_id, 'clientid' => $client->id, 'agent_id' => $request->get('agent_id'), 'final_amount' => $request->get('value'), 'due_date' => $request->get('due_date'), 'invoice_descriptione' => $request->get('description'), 'sales_type' => $request->get('sales_type'), 'status' => 'Paid', 'project_id' => $project->id,]);
                if ($invoice) {
                    $payment = Payment::create(['team_key' => $team_key, 'brand_key' => $request->get('brand_key'), 'creatorid' => $creator_id, 'actor_id' => $creator_id, 'actor_type' => get_class(Auth::user() ?? ""), 'agent_id' => $request->get('agent_id'), 'clientid' => $client->id, 'invoice_id' => $invoice_key, 'project_id' => $project->id, 'name' => $request->get('name'), 'email' => $request->get('email'), 'phone' => $request->get('phone'), 'address' => '', 'amount' => $request->get('value'), 'payment_status' => '1', 'authorizenet_transaction_id' => $request->get('track_id'), 'payment_gateway' => $request->get('merchant'), 'auth_id' => '', 'response_code' => '', 'message_code' => '', 'payment_notes' => $request->get('description'), 'sales_type' => $request->get('sales_type'),]);
                    if ($payment) {
                        return response()->json(['success' => 'succeeded.!']);
                    }
                }
            }
            return response()->json(['error' => 'Response error'], 500);
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
            return response()->json(['error' => 'Response error'], 500);
        }
    }

//    public function direct_payment(Request $request){
//
//        $teamKey = Auth::user()->team_key;
//        $creatorid = Auth::user()->id;
//
//        $client_exists = Client::where('email',$request->get('email'))->first();
//
//        if($client_exists){
//            $project = Project::create([
//                'team_key'  => $teamKey,
//                'brand_key' => $request->get('brand_key'),
//                'creatorid' => $creatorid,
//                'clientid'  => $client_exists->id,
//                'agent_id'  => $request->get('agent_id'),
//                'asigned_id' => '0',
//                'category_id'  => '1',
//                'project_title'  => $request->get('project_title'),
//                'project_description'  => $request->get('description'),
//                'project_status'  => '1',
//                'project_progress' => '1',
//                'project_cost' => $request->get('value')
//            ]);
//            $projectID = $project->id;
//            $invoiceKey = random_int(11,99).substr(time(), 1, 3).random_int(11,99).substr(time(), 8, 2);
//
//            if($projectID){
//                $invoice = Invoice::create([
//                    'invoice_num'   => 'INV-'.random_int(100000, 999999),
//                    'invoice_key'   => $invoiceKey,
//                    'team_key'      => $teamKey,
//                    'brand_key'     => $request->get('brand_key'),
//                    'creatorid'     => $creatorid,
//                    'clientid'      => $client_exists->id,
//                    'agent_id'      => $request->get('agent_id'),
//                    'final_amount'  => $request->get('value'),
//                    'due_date'      => $request->get('due_date'),
//                    'invoice_descriptione'      => $request->get('description'),
//                    'sales_type'    => $request->get('sales_type'),
//                    'status'        => 'Paid',
//                    'project_id'    => $projectID,
//                ]);
//            }
//
//            //$request->get('transaction_id')
//            $payment = Payment::create([
//                'team_key'   => $teamKey,
//                'brand_key'  => $request->get('brand_key'),
//                'creatorid'  => $creatorid,
//                'agent_id'   => $request->get('agent_id'),
//                'clientid'   => $client_exists->id,
//                'invoice_id' => $invoiceKey,
//                'project_id' => $projectID,
//                'name'       => $request->get('name'),
//                'email'      => $request->get('email'),
//                'phone'      => $request->get('phone'),
//                'address'    => '',
//                'amount'     => $request->get('value'),
//                'payment_status' => '1',
//                'authorizenet_transaction_id' => $request->get('track_id'),
//                'payment_gateway' => $request->get('merchant'),
//                'auth_id'       => '',
//                'response_code' => '',
//                'message_code'  =>  '',
//                'payment_notes' => $request->get('description'),
//                'sales_type'    => $request->get('sales_type'),
//            ]);
//        }else{
//            $client = Client::create([
//                'team_key'  => $teamKey,
//                'brand_key' => $request->get('brand_key'),
//                'creatorid' => $creatorid,
//                'name'      => $request->get('name'),
//                'email'     => $request->get('email'),
//                'phone'     => $request->get('phone'),
//                'agent_id'  => $request->get('agent_id'),
//                'status' => '1'
//            ]);
//            $clientID = $client->id;
//            $users = User::create([
//                'name'          => $request->get('name'),
//                'email'         => $request->get('email'),
//                'phone'         => $request->get('phone'),
//                'password'      => Hash::make('12345678'),
//                'type'          => 'client',
//                'team_key'      => $teamKey,
//                'clientid'      => $clientID
//            ]);
//
//            if($clientID){
//                $project = Project::create([
//                    'team_key'  => $teamKey,
//                    'brand_key' => $request->get('brand_key'),
//                    'creatorid' => $creatorid,
//                    'clientid'  => $clientID,
//                    'agent_id'  => $request->get('agent_id'),
//                    'asigned_id' => '0',
//                    'category_id'  => '1',
//                    'project_title'  => $request->get('project_title'),
//                    'project_description'  => $request->get('description'),
//                    'project_status'  => '1',
//                    'project_progress' => '1',
//                    'project_cost' => $request->get('value')
//                ]);
//            }
//            $projectID = $project->id;
//            $invoiceKey = random_int(11,99).substr(time(), 1, 3).random_int(11,99).substr(time(), 8, 2);
//
//            if($projectID){
//                $invoice = Invoice::create([
//                    'invoice_num'   => 'INV-'.random_int(100000, 999999),
//                    'invoice_key'   => $invoiceKey,
//                    'team_key'      => $teamKey,
//                    'brand_key'     => $request->get('brand_key'),
//                    'creatorid'     => $creatorid,
//                    'clientid'      => $clientID,
//                    'agent_id'      => $request->get('agent_id'),
//                    'final_amount'  => $request->get('value'),
//                    'due_date'      => $request->get('due_date'),
//                    'invoice_descriptione'      => $request->get('description'),
//                    'sales_type'    => $request->get('sales_type'),
//                    'status'        => 'Paid',
//                    'project_id'    => $projectID,
//                ]);
//            }
//
//            //$request->get('transaction_id')
//            $payment = Payment::create([
//                'team_key'   => $teamKey,
//                'brand_key'  => $request->get('brand_key'),
//                'creatorid'  => $creatorid,
//                'agent_id'   => $request->get('agent_id'),
//                'clientid'   => $clientID,
//                'invoice_id' => $invoiceKey,
//                'project_id' => $projectID,
//                'name'       => $request->get('name'),
//                'email'      => $request->get('email'),
//                'phone'      => $request->get('phone'),
//                'address'    => '',
//                'amount'     => $request->get('value'),
//                'payment_status' => '1',
//                'authorizenet_transaction_id' => $request->get('track_id'),
//                'payment_gateway' => $request->get('merchant'),
//                'auth_id'       => '',
//                'response_code' => '',
//                'message_code'  =>  '',
//                'payment_notes' => $request->get('description'),
//                'sales_type'    => $request->get('sales_type'),
//            ]);
//        }
//    }

    public function show_payment_details($id)
    {
        $team_key = Auth::user()->team_key;
        $type = Auth::user()->type;
        $payment = Payment::where('id', $id)->first();
        if (!$payment) {
            return redirect()->route('user.payments.index')->withErrors(['error' => 'Payment not found.']);
        }
        /** Team Brands */
        if ($type === 'ppc') {
            $assignedTeams = auth()->user()->assigned_teams ?? [];
            if (auth()->user()->team_key && !in_array(auth()->user()->team_key, $assignedTeams)) {
                $assignedTeams[] = auth()->user()->team_key;
            }
            $assign_brands = AssignBrand::whereIn('team_key', $assignedTeams)->whereHas('getBrandWithOutTrashed')->get()->pluck('brand_key')->toArray();
            if (!in_array($payment->team_key, $assignedTeams) && !in_array($payment->brand_key, $assign_brands)) {
                return redirect()->route('user.payments.index')->withErrors(['error' => 'Insufficient rights.']);
            }
            $payment = Payment::where('id', $id)->where(function ($query) use ($assignedTeams, $assign_brands) {
                $query->whereIn('team_key', $assignedTeams)->orWhereIn('brand_key', $assign_brands);
            })->first();
        } else {
            $assign_brands = AssignBrand::whereHas('getBrandWithOutTrashed')->where('team_key', $team_key)->get()->pluck('brand_key')->toArray();
            if ($payment->team_key != $team_key && !in_array($payment->brand_key, $assign_brands)) {
                return redirect()->route('user.payments.index')->withErrors(['error' => 'Insufficient rights.']);
            }
            $payment = Payment::where('id', $id)->where(function ($query) use ($team_key, $assign_brands) {
                $query->where('team_key', $team_key)->orWhereIn('brand_key', $assign_brands);
            })->first();
        }
        if (!$payment) {
            return redirect()->route('user.payments.index')->withErrors(['error' => 'Payment not found.']);
        }
        $payment['teamName'] = Team::where('team_key', $payment->team_key)->value('name');
        $payment['brandName'] = Brand::where('brand_key', $payment->brand_key)->value('name');
        $payment['agentName'] = User::where('id', $payment->agent_id)->value('name');
        $payment['creatorName'] = User::where('id', $payment->creatorid)->value('name');
        $payment['projectTitle'] = Project::where('id', $payment->project_id)->value('project_title');
        return view('payment.details', compact('payment'));
    }

    public function compliance_varified_payment(Request $request)
    {

        $pid = $request->get('payment_id');
        $file = $request->file('upload_file');
        $note = $request->get('description');
        $varified_val = $request->get('varified');

        $varified = ($varified_val == null) ? '0' : '1';

        $filePath = time() . '.' . $file->getClientOriginalExtension();

        $payment = Payment::find($pid);
        $payment->compliance_varified_note = $note;
        $payment->audio = $filePath;
        $payment->compliance_verified = $varified;
        $payment->save();

        //Move Uploaded File
        $destinationPath = 'uploads';
        $file->move($destinationPath, $filePath);

        return $payment;

    }

    public function search_payment(Request $request)
    {
        $search = $request->input('searchText');
        $type = Auth::user()->type;
        $team_key = Auth::user()->team_key;
        $query = Payment::query();
        if ($type === 'ppc') {
            $assignedTeams = auth()->user()->assigned_teams ?? [];
            if (auth()->user()->team_key && !in_array(auth()->user()->team_key, $assignedTeams)) {
                $assignedTeams[] = auth()->user()->team_key;
            }
            $assign_brands = AssignBrand::whereIn('team_key', $assignedTeams)
                ->whereHas('getBrandWithOutTrashed')
                ->get()
                ->pluck('brand_key')
                ->toArray();

            $query->where(function ($query) use ($assignedTeams, $assign_brands) {
                $query->whereIn('team_key', $assignedTeams)
                    ->orWhereIn('brand_key', $assign_brands);
            });
        } else {
            $assign_brands = AssignBrand::where('team_key', $team_key)
                ->whereHas('getBrandWithOutTrashed')
                ->get()
                ->pluck('brand_key')
                ->toArray();

            $query->where(function ($query) use ($team_key, $assign_brands) {
                $query->where('team_key', $team_key)
                    ->orWhereIn('brand_key', $assign_brands);
            });
        }
        $payments = $query->where(function ($query) use ($search) {
            $query->where('name', 'LIKE', "%{$search}%")
                ->orWhere('email', 'LIKE', "%{$search}%")
                ->orWhere('authorizenet_transaction_id', 'LIKE', "%{$search}%")
                ->orWhere('amount', 'LIKE', "%{$search}%");
        })->get();

        // Return the search view with the resluts compacted
        return view('payment.searchpayment', compact('payments'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Payment $payment
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            $user = Auth::user();

            $payment = Payment::where('id', $id)->where('actor_id', $user->id())->first();
            if (!$payment) {
                return response()->json(['error' => 'Record not found or you are not allowed to access it.'], 404);
            }
            if ($user->type === 'ppc') {
                $assignedTeams = auth()->user()->assigned_teams ?? [];
                if (auth()->user()->team_key && !in_array(auth()->user()->team_key, $assignedTeams)) {
                    $assignedTeams[] = auth()->user()->team_key;
                }
                $assign_brands = AssignBrand::whereIn('team_key', $assignedTeams)
                    ->whereHas('getBrandWithOutTrashed')
                    ->get()
                    ->pluck('brand_key')
                    ->toArray();

                if (!in_array($payment->team_key, $assignedTeams) && !in_array($payment->brand_key, $assign_brands)) {
                    return response()->json(['error' => 'Insufficient rights to delete this record.'], 403);
                }
            } else {
                $assign_brands = AssignBrand::where('team_key', $user->team_key)
                    ->whereHas('getBrandWithOutTrashed')
                    ->get()
                    ->pluck('brand_key')
                    ->toArray();

                if ($payment->team_key != $user->team_key && !in_array($payment->brand_key, $assign_brands)) {
                    return response()->json(['error' => 'Insufficient rights to delete this record.'], 403);
                }
            }

            if ($user->type != 'lead') {
                return response()->json(['error' => 'You are not allowed to delete this record.'], 403);
            }
            $payment->delete();
            return response()->json(['success' => 'Record deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

}


<?php

namespace App\Http\Controllers\Admincontroller;

use App\Http\Controllers\Controller;
use App\Models\AssignUserBrandEmail;
use App\Models\Department;
use App\Models\EmailConfiguration;
use App\Models\Team;
use App\Models\Brand;
use App\Models\User;
use App\Models\AssignBrand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules;
use Carbon\Carbon;
use App\Models\Payment;
use App\Models\Refund;
use App\Models\Project;
use App\Models\Client;
use App\Models\ProjectStatus;
use App\Models\Invoice;
use App\Models\Expense;

use DB;

class TeamController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $teamsData = array();
        $teams = Team::all();
        $members = User::where('status', 1)->where('type', '!=', 'client')->get();
        $brands = Brand::where(['status' => '1', 'assign_status' => '0'])->get();

        foreach ($teams as $team) {
            $team_key = $team->team_key;

            $team_Lead = User::where('id', $team->team_lead)->value('name');
            $teams_a = AssignBrand::where('team_key', $team_key)->get();
            $brandName = "";
            foreach ($teams_a as $team_b) {
                $drand = Brand::where('brand_key', $team_b->brand_key)->value('name');
                $brandName .= $drand . ", ";
            }
            $team['assignBrands'] = $brandName;
            $team['teamLead'] = $team_Lead;
            array_push($teamsData, $team);
        }
        return view('admin.team.index', compact('teamsData', 'brands', 'members'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $members = User::where('status', 1)->where('type', '!=', 'client')->get();
        return view('admin.team.create', compact('members'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $teamKey = random_int(100000, 999999);

        Team::create([
            'name' => $request->get('name'),
            'team_key' => $teamKey,
            'status' => $request->get('status'),
            'team_lead' => $request->get('team_lead')
        ]);

        User::where('id', $request->get('team_lead'))->update(['team_key' => $teamKey, 'type' => 'lead', 'staff_division' => 'lead']);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Team $team
     * @return \Illuminate\Http\Response
     */
    public function show(Team $team)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Team $team
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $team = Team::find($id);

        $members = User::where('status', 1)->where('type', '!=', 'client')->where('type', '!=', 'tm-ppc')->get();
        $tm_ppc_users = User::where('status', 1)->where('type', 'tm-ppc')->get();
        $brands = Brand::where('status', '1')->get();
        $brand_a = AssignBrand::where('team_key', $team->team_key)->get('brand_key');
        $b = array();
        foreach ($brand_a as $ba) {
            array_push($b, $ba->brand_key);
        }

        $brandData = array();
        foreach ($brands as $brand) {
            if (in_array($brand->brand_key, $b)) {
                $brand['assingBrand'] = "checked";
            } else {
                $brand['assingBrand'] = "";
            }
            array_push($brandData, $brand);
        }

        return view('admin.team.edit', compact('team', 'members', 'brandData', 'tm_ppc_users'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Team $team
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'tm_ppc' => 'sometimes|array',
            'tm_ppc.*' => 'exists:users,id',
        ]);
        $agents = $request->get('agents');
        $assBrands = $request->get('assignbrands');
        $tm_ppc = $request->get('tm_ppc', []);

        $team = Team::find($id);
        User::where('team_key', $team->team_key)->where('type', 'tm-ppc')->update(['team_key' => NULL]);
        if (!empty($tm_ppc)) {
            User::whereIn('id', $tm_ppc)->update(['team_key' => $team->team_key, 'type' => 'tm-ppc']);
        }
        // User::where('id', $team->team_lead)->update(['team_key'=> NUll,'type'=>'staff','staff_division'=>'agent']);
        //User::where('team_key', $team->team_key)->update(['team_key'=> NUll,'type'=>'staff','staff_division'=>'agent']);
        User::where(['team_key' => $team->team_key, 'type' => 'staff'])->update(['team_key' => NUll, 'type' => 'staff', 'staff_division' => 'agent']);

        $team->name = $request->brandName;
        $team->status = $request->status;
        if ($request->team_lead != '') {
            $team->team_lead = $request->team_lead;
        }
        $team->save();

        if (!empty($agents)) {
            foreach ($agents as $agent) {
                User::where('id', $agent)->update(['team_key' => $team->team_key]);
            }
        }

        $assBrand = AssignBrand::where('team_key', $team->team_key)->get();
        foreach ($assBrand as $as) {
            DB::table('brands')->where('brand_key', $as->brand_key)->update(['assign_status' => 0]);
        }

        AssignBrand::where('team_key', $team->team_key)->delete();

        if (!empty($assBrands)) {
            foreach ($assBrands as $bb) {
                AssignBrand::create(['team_key' => $team->team_key, 'brand_key' => $bb]);
                DB::table('brands')->where('brand_key', $bb)->update(['assign_status' => 1]);
            }
        }

        if ($request->team_lead != '0') {
            User::where('id', $request->team_lead)->update(['team_key' => $team->team_key, 'type' => 'lead', 'staff_division' => 'lead']);
        }

        return $team;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Team $team
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Team::find($id)->delete();
    }

    public function trashedteam()
    {
        $teams = Team::onlyTrashed()->get();
        return view('admin.team.trashed', compact('teams'));
    }

    public function restoreteam($id)
    {
        Team::onlyTrashed()->whereId($id)->restore();
    }

    public function teamrestoreAll()
    {
        Team::onlyTrashed()->restore();
    }

    public function teamchangeStatus(Request $request)
    {
        $team = Team::find($request->brand_id);
        $team->status = $request->status;
        $team->save();

        return response()->json(['success' => 'Status change successfully.']);
    }

    public function assignBrand(Request $request)
    {

        $team_keys = $request->team_id;
        $brand_keys = $request->brand_key;
        $myArray = explode(',', $brand_keys);

        $data = array();
        foreach ($myArray as $brand) {
            $drands = DB::table('brands')->where('brand_key', $brand)->update(['assign_status' => 1]);

            AssignBrand::create([
                'team_key' => $team_keys,
                'brand_key' => $brand
            ]);
        }

        // $drands_data = Team::whereLike('brand_key', $myArray)->get();
        //$team = Team::find($request->team_id);
        //$team->brand_key = $request->brand_key;
        //$team->save();

        //return $myArray;

        return $drands;
    }

    public function createTeamMember(Request $request)
    {

        //DB::enableQueryLog();
        $teamKey = $request->get('team_key');
        $agents = $request->get('agents');

        foreach ($agents as $agent) {
            User::where('id', $agent)->update(['team_key' => $teamKey]);
        }

        return response()->json(['success' => 'Status change successfully.']);

    }

    public function showMembers()
    {

        $teams = Team::where('status', 1)->get();
        $membersData = User::where('status', '1')->where('type', '!=', 'client')->get();
        $departments = Department::where('status', '1')->get();
        $members = array();
        foreach ($membersData as $m) {

            $teamAmount = Payment::where(
                [
                    'payment_status' => 1,
                    'agent_id' => $m->id
                ])->whereMonth('created_at', Carbon::now()->month)->sum('amount');

            settype($teamAmount, "integer");
            $m['amount'] = $teamAmount;
            if ($teamAmount != 0) {
                if ($m->target != 0) {
                    $m->percentage = ($teamAmount * 100) / $m->target;
                } else {
                    $m->percentage = 0;
                }
            } else {
                $m['percentage'] = 0;
            }
            array_push($members, $m);
        }
        return view('admin.team.member', compact('members', 'teams', 'departments'));
    }

    public function showMemberProfile($id)
    {
        $member = User::find($id);

        $email_configuration_ids = optional($member->getUserBrandEmails)->pluck('id')->toArray() ?? [];
        /** If according to brands */
//        $member_brand_keys = optional($member->getTeamBrands)->pluck('brand_key')->toArray() ?? [];
//        $email_configurations = EmailConfiguration::whereIn('brand_key',$member_brand_keys)->where('status', 1)->get();
        /** Without brand validation*/
//        $brands = Brand::orderBy('name')->withTrashed()->get();
//        $fetch_email_configurations = EmailConfiguration::where('status', 1)->get();
//        $email_configurations = [];
//        foreach ($brands as $brand) {
//            foreach ($fetch_email_configurations as $fetch_email_configuration) {
//                if ($brand->brand_key === $fetch_email_configuration->brand_key) {
//                    $email_configurations[$brand->name][] = $fetch_email_configuration;
//                }
//            }
//        }
        $fetch_email_configurations = EmailConfiguration::where('status', 1)->with('getBrand')->get()->sortBy('getBrand.name');

        $email_configurations = [];

        foreach ($fetch_email_configurations as $fetch_email_configuration) {
            $brandName = $fetch_email_configuration->getBrand->name;
            $email_configurations[$brandName . ' - ' . $fetch_email_configuration->brand_key . ' - <a href="' . $fetch_email_configuration->getBrand->brand_url . '" target="_blank"><i class="zmdi zmdi-link"></i> ' . $fetch_email_configuration->getBrand->brand_url . '</a>'][] = $fetch_email_configuration;
        }

        $teamAmount = Payment::where(['payment_status' => 1, 'agent_id' => $member->id])->whereMonth('created_at', Carbon::now()->month)->sum('amount');
        settype($teamAmount, "integer");
        $member['achived_amount'] = $teamAmount;


        //member month Fresh payment
        $agentFreshPayment = Payment::select(DB::raw("(SUM(amount)) as amount"), DB::raw("MONTHNAME(created_at) as monthname"))
            ->where(['payment_status' => 1, 'agent_id' => $member->id, 'sales_type' => 'Fresh'])
            ->whereYear('created_at', date('Y'))
            ->groupBy('monthname')
            ->get();

        $agentFreshPaymentData = array();

        foreach ($agentFreshPayment as $key1 => $monthWise1) {

            if ('January' == $monthWise1->monthname) {
                $agentFreshPaymentData['January'] = [$monthWise1->monthname, $monthWise1->amount];
            }
            if ('February' == $monthWise1->monthname) {
                $agentFreshPaymentData['February'] = [$monthWise1->monthname, $monthWise1->amount];
            }
            if ('March' == $monthWise1->monthname) {
                $agentFreshPaymentData['March'] = [$monthWise1->monthname, $monthWise1->amount];
            }
            if ('April' == $monthWise1->monthname) {
                $agentFreshPaymentData['April'] = [$monthWise1->monthname, $monthWise1->amount];
            }
            if ('May' == $monthWise1->monthname) {
                $agentFreshPaymentData['May'] = [$monthWise1->monthname, $monthWise1->amount];
            }
            if ('June' == $monthWise1->monthname) {
                $agentFreshPaymentData['June'] = [$monthWise1->monthname, $monthWise1->amount];
            }
            if ('July' == $monthWise1->monthname) {
                $agentFreshPaymentData['July'] = [$monthWise1->monthname, $monthWise1->amount];
            }
            if ('August' == $monthWise1->monthname) {
                $agentFreshPaymentData['August'] = [$monthWise1->monthname, $monthWise1->amount];
            }
            if ('September' == $monthWise1->monthname) {
                $agentFreshPaymentData['September'] = [$monthWise1->monthname, $monthWise1->amount];
            }
            if ('October' == $monthWise1->monthname) {
                $agentFreshPaymentData['October'] = [$monthWise1->monthname, $monthWise1->amount];
            }
            if ('November' == $monthWise1->monthname) {
                $agentFreshPaymentData['November'] = [$monthWise1->monthname, $monthWise1->amount];
            }
            if ('December' == $monthWise1->monthname) {
                $agentFreshPaymentData['December'] = [$monthWise1->monthname, $monthWise1->amount];
            }
        }

        //member month Upsales payment
        $agentUpsalePayment = Payment::select(DB::raw("(SUM(amount)) as amount"), DB::raw("MONTHNAME(created_at) as monthname"))
            ->where(['payment_status' => 1, 'agent_id' => $member->id, 'sales_type' => 'Upsale'])
            ->whereYear('created_at', date('Y'))
            ->groupBy('monthname')
            ->get();

        $agentUpsalePaymentData = array();

        foreach ($agentUpsalePayment as $key2 => $monthWise2) {

            if ('January' == $monthWise2->monthname) {
                $agentUpsalePaymentData['January'] = [$monthWise2->monthname, $monthWise2->amount];
            }
            if ('February' == $monthWise2->monthname) {
                $agentUpsalePaymentData['February'] = [$monthWise2->monthname, $monthWise2->amount];
            }
            if ('March' == $monthWise2->monthname) {
                $agentUpsalePaymentData['March'] = [$monthWise2->monthname, $monthWise2->amount];
            }
            if ('April' == $monthWise2->monthname) {
                $agentUpsalePaymentData['April'] = [$monthWise2->monthname, $monthWise2->amount];
            }
            if ('May' == $monthWise2->monthname) {
                $agentUpsalePaymentData['May'] = [$monthWise2->monthname, $monthWise2->amount];
            }
            if ('June' == $monthWise2->monthname) {
                $agentUpsalePaymentData['June'] = [$monthWise2->monthname, $monthWise2->amount];
            }
            if ('July' == $monthWise2->monthname) {
                $agentUpsalePaymentData['July'] = [$monthWise2->monthname, $monthWise2->amount];
            }
            if ('August' == $monthWise2->monthname) {
                $agentUpsalePaymentData['August'] = [$monthWise2->monthname, $monthWise2->amount];
            }
            if ('September' == $monthWise2->monthname) {
                $agentUpsalePaymentData['September'] = [$monthWise2->monthname, $monthWise2->amount];
            }
            if ('October' == $monthWise2->monthname) {
                $agentUpsalePaymentData['October'] = [$monthWise2->monthname, $monthWise2->amount];
            }
            if ('November' == $monthWise2->monthname) {
                $agentUpsalePaymentData['November'] = [$monthWise2->monthname, $monthWise2->amount];
            }
            if ('December' == $monthWise2->monthname) {
                $agentUpsalePaymentData['December'] = [$monthWise2->monthname, $monthWise2->amount];
            }
        }

        //member refund
        $refundYearMonthWise = Refund::select(DB::raw("(SUM(amount)) as amount"), DB::raw("MONTHNAME(created_at) as monthname"))
            ->where(['qa_approval' => 1, 'type' => 'refund', 'agent_id' => $member->id])
            ->whereYear('created_at', date('Y'))
            ->groupBy('monthname')
            ->get();


        $refundYearMonthWiseData = array();

        foreach ($refundYearMonthWise as $key3 => $monthWise3) {

            if ('January' == $monthWise3->monthname) {
                $refundYearMonthWiseData['January'] = [$monthWise3->monthname, $monthWise3->amount];
            }
            if ('February' == $monthWise3->monthname) {
                $refundYearMonthWiseData['February'] = [$monthWise3->monthname, $monthWise3->amount];
            }
            if ('March' == $monthWise3->monthname) {
                $refundYearMonthWiseData['March'] = [$monthWise3->monthname, $monthWise3->amount];
            }
            if ('April' == $monthWise3->monthname) {
                $refundYearMonthWiseData['April'] = [$monthWise3->monthname, $monthWise3->amount];
            }
            if ('May' == $monthWise3->monthname) {
                $refundYearMonthWiseData['May'] = [$monthWise3->monthname, $monthWise3->amount];
            }
            if ('June' == $monthWise3->monthname) {
                $refundYearMonthWiseData['June'] = [$monthWise3->monthname, $monthWise3->amount];
            }
            if ('July' == $monthWise3->monthname) {
                $refundYearMonthWiseData['July'] = [$monthWise3->monthname, $monthWise3->amount];
            }
            if ('August' == $monthWise3->monthname) {
                $refundYearMonthWiseData['August'] = [$monthWise3->monthname, $monthWise3->amount];
            }
            if ('September' == $monthWise3->monthname) {
                $refundYearMonthWiseData['September'] = [$monthWise3->monthname, $monthWise3->amount];
            }
            if ('October' == $monthWise3->monthname) {
                $refundYearMonthWiseData['October'] = [$monthWise3->monthname, $monthWise3->amount];
            }
            if ('November' == $monthWise3->monthname) {
                $refundYearMonthWiseData['November'] = [$monthWise3->monthname, $monthWise3->amount];
            }
            if ('December' == $monthWise3->monthname) {
                $refundYearMonthWiseData['December'] = [$monthWise3->monthname, $monthWise3->amount];
            }
        }


        $chargebackYearMonthWise = Refund::select(DB::raw("(SUM(amount)) as amount"), DB::raw("MONTHNAME(created_at) as monthname"))
            ->where(['qa_approval' => 1, 'type' => 'chargeback', 'agent_id' => $member->id])
            ->whereYear('created_at', date('Y'))
            ->groupBy('monthname')
            ->get();

        $chargebackYearMonthWiseData = array();

        foreach ($chargebackYearMonthWise as $key4 => $monthWise4) {

            if ('January' == $monthWise4->monthname) {
                $chargebackYearMonthWiseData['January'] = [$monthWise4->monthname, $monthWise4->amount];
            }
            if ('February' == $monthWise4->monthname) {
                $chargebackYearMonthWiseData['February'] = [$monthWise4->monthname, $monthWise4->amount];
            }
            if ('March' == $monthWise4->monthname) {
                $chargebackYearMonthWiseData['March'] = [$monthWise4->monthname, $monthWise4->amount];
            }
            if ('April' == $monthWise4->monthname) {
                $chargebackYearMonthWiseData['April'] = [$monthWise4->monthname, $monthWise4->amount];
            }
            if ('May' == $monthWise4->monthname) {
                $chargebackYearMonthWiseData['May'] = [$monthWise4->monthname, $monthWise4->amount];
            }
            if ('June' == $monthWise4->monthname) {
                $chargebackYearMonthWiseData['June'] = [$monthWise4->monthname, $monthWise4->amount];
            }
            if ('July' == $monthWise4->monthname) {
                $chargebackYearMonthWiseData['July'] = [$monthWise4->monthname, $monthWise4->amount];
            }
            if ('August' == $monthWise4->monthname) {
                $chargebackYearMonthWiseData['August'] = [$monthWise4->monthname, $monthWise4->amount];
            }
            if ('September' == $monthWise4->monthname) {
                $chargebackYearMonthWiseData['September'] = [$monthWise4->monthname, $monthWise4->amount];
            }
            if ('October' == $monthWise4->monthname) {
                $chargebackYearMonthWiseData['October'] = [$monthWise4->monthname, $monthWise4->amount];
            }
            if ('November' == $monthWise4->monthname) {
                $chargebackYearMonthWiseData['November'] = [$monthWise4->monthname, $monthWise4->amount];
            }
            if ('December' == $monthWise4->monthname) {
                $chargebackYearMonthWiseData['December'] = [$monthWise4->monthname, $monthWise4->amount];
            }
        }

        //Agent Project
        $projectsData = array();
        $projects = Project::where('agent_id', $member->id)->get();

        foreach ($projects as $yy) {
            $cName = Client::where('id', $yy->id)->value('name');
            $yy['clientName'] = $cName;

            $brand = Brand::where('brand_key', $yy->brand_key)->value('name');
            $yy['brandName'] = $brand;

            $agent = User::where('id', $yy->agent_id)->first();
            $yy['agentName'] = $agent->name;
            $yy['agentDesignation'] = $agent->designation;
            $yy['agentImage'] = $agent->image;

            $pm = User::where('id', $yy->asigned_id)->first();
            if ($pm) {
                $yy['pmName'] = $pm->name;
                $yy['pmDesignation'] = $pm->designation;
                $yy['pmImage'] = $pm->image;
            } else {
                $yy['pmName'] = '---';
                $yy['pmDesignation'] = '----';
            }
            $status = ProjectStatus::where('id', $yy->project_status)->first();
            $yy['status'] = $status->status;
            $yy['statusColor'] = $status->status_color;

            array_push($projectsData, $yy);
        }

        //Agent Invoice
        $invoiceData = array();
        $invoices = Invoice::where('agent_id', $member->id)->get();
        foreach ($invoices as $invoice) {
            $client_id = $invoice->clientid;

            $client_name = Client::where('id', $client_id)->value('name');
            $invoice['clientName'] = $client_name;

            array_push($invoiceData, $invoice);
        }

        //Agent Payments
        $paymentData = array();
        $payments = Payment::where('agent_id', $member->id)->get();

        foreach ($payments as $payment) {
            $brandKey = $payment->brand_key;

            $brand_name = Brand::where('brand_key', $brandKey)->value('name');
            $payment['brandName'] = $brand_name;

            array_push($paymentData, $payment);
        }

        //Agent Refund
        $refunds = Refund::where('agent_id', $member->id)->get();
        //Agent Expence
        $expenses = Expense::where('agent_id', $member->id)->get();

        return view('admin.team.profile', compact('email_configuration_ids', 'email_configurations', 'member', 'agentFreshPaymentData', 'agentUpsalePaymentData', 'refundYearMonthWiseData', 'chargebackYearMonthWiseData', 'projectsData', 'invoiceData', 'paymentData', 'refunds', 'expenses'));
    }

    public function assign_unassign_brand_emails(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email_id' => 'required|exists:email_configurations,id',
            ], [
                'email_id.required' => 'The email configuration id field is required.',
                'email_id.exists' => 'The selected email configuration id is invalid.',
            ]);
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $user = User::where('id', $id)->where('type', '!=', 'client')->where('status', 1)->first();
            if ($user) {
                $assign = 0;
                $email_configuration_ids = AssignUserBrandEmail::where('user_id', $user->id)->get()->pluck('email_configuration_id')->toArray();

                $unchange_check = AssignUserBrandEmail::where('user_id', $user->id)->where('email_configuration_id', $request->email_id)->first();
                if ($request->input('checked') == 'false' && !$unchange_check) {
                    $assign = 0;
                } elseif ($request->input('checked') == 'true' && $unchange_check) {
                    $assign = 1;
                }
                if (($request->input('checked') == 'false' && !$unchange_check) || ($request->input('checked') == 'true' && $unchange_check)) {
                    return response()->json([
                        'success' => 'User brand email already changed.',
                        'status' => 1,
                        'assign' => $assign,
                        'action' => 'unchanged',
                        'email_id' => $request->email_id,
                    ]);
                }


                if (in_array((int)$request->email_id, $email_configuration_ids, true)) {
                    $email_configuration_ids = array_diff($email_configuration_ids, [$request->email_id]);
                } else {
                    $email_configuration_ids = array_unique(array_merge($email_configuration_ids, [$request->email_id]));
                }
                $email_configuration_exists = EmailConfiguration::whereIn('id', $email_configuration_ids)->where('status', 1)->get();
                $user->setUserBrandEmails()->sync($email_configuration_exists);
                $fetch_assign_user_brand_email = AssignUserBrandEmail::where('user_id', $user->id)->where('email_configuration_id', $request->email_id)->first();
                if ($fetch_assign_user_brand_email) {
                    $assign = 1;
                }
                return response()->json([$assign == 1 ? 'success' : 'warning' => $assign == 1 ? 'Assign user brand email successfully!' : "Unassign user brand email successfully!",
                    'status' => 1,
                    'assign' => $assign,
                    'email_id' => $request->email_id,
                ]);
            }
            throw new \RuntimeException('Failed to assign brand email.');
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function changeMemberstatus(Request $request)
    {

        // dd($request);
        $membersData = User::where('id', $request->member_id)->first();

        User::where('id', $membersData->id)->update(
            array('status' => $request->status,
            ));

        return response()->json(['success' => 'Status change successfully.']);
    }


    public function showInactivemembers()
    {

        $teams = Team::all();
        $membersData = User::where('status', '0')
//            ->where('type', 'staff')
            ->latest('updated_at')->get();

        $members = array();
        foreach ($membersData as $m) {

            $teamAmount = Payment::where(
                [
                    'response_code' => 1,
                    'agent_id' => $m->id
                ])->whereMonth('created_at', Carbon::now()->month)->sum('amount');

            settype($teamAmount, "integer");
            $m['amount'] = $teamAmount;

            if ($m->target == 0) {
                $m['percentage'] = 0;
            } else {
                $m['percentage'] = ($teamAmount * 100) / $m->target;
            }
            array_push($members, $m);
        }
        return view('admin.team.inactivemember', compact('members', 'teams'));
    }

    public function create_employee(Request $request)
    {

        $rules = [
            'assigned_departments' => 'nullable|array',
            'assigned_departments.*' => 'exists:departments,id',
            "user_access" => "required|in:0,1,2",
        ];
        $messages = [
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        if ($request->get('type') == 'tm-client') {
            $client = Client::where('email', $request->get('email'))->first();
            if (!$client) {
                $client = new Client();
                $client->team_key = 0000;
                $client->brand_key = 0000;
                $client->creatorid = 0;
                $client->name = $request->get('name');
                $client->email = $request->get('email');
                $client->phone = $request->get('phone');
                $client->agent_id = null;
                $client->client_created_from_leadid = 0;
                $client->client_description = "";
                $client->address = "";
                $client->status = '1';
                $client->save();


                $user = new User();
                $user->name = $client->name;
                $user->email = $client->email;
                $user->phone = $client->phone;
                $user->password = Hash::make('12345678');
                $user->type = 'tm-client';
                $user->clientid = $client->id;
                $user->user_access = $request->get('user_access');
                $user->save();
            }
        } else {

            if ($request->get('lead_special_access'))
                $lead_special_access = 1;
            else
                $lead_special_access = 0;

            $selectedTeamKeys = [];
            if ($request->get('edit_type') === 'ppc') {
                $selectedTeamKeys = $request->get('assigned_team_key') ?? [];
                if (in_array(0, $selectedTeamKeys, true)) {
                    $selectedTeamKeys = Team::pluck('team_key')->toArray();
                }
                if (count($selectedTeamKeys) == 0) {
                    $selectedTeamKeys = null;
                }
            }
            $user = User::create([

                'lead_special_access' => $lead_special_access,
                'name' => $request->get('name'),
                'email' => $request->get('email'),
                'phone' => $request->get('phone'),
                'designation' => $request->get('designation'),
                'password' => Hash::make('12345678'),
                'pseudo_name' => $request->get('pseudo_name'),
                'pseudo_email' => $request->get('pseudo_email'),
                'type' => $request->get('type'),
                'target' => $request->get('target'),
                'image' => $request->get('image'),
                'status' => '1',
                'user_access' => $request->get('user_access'),
                'assigned_teams' => $selectedTeamKeys,
//                'has_department' => $request->boolean('has_department') ? 1 : 0
            ]);
            if ($request->filled('assigned_departments') && in_array($request->input('type'), ['executive', 'ppc', 'third-party-user'])) {
                $user->setDepartment()->sync($request->get('assigned_departments'));
            }
        }
    }


    public function edit_employee($id)
    {

        return User::where('id', $id)->with('getDepartment')->first();
    }


    public function update_employee(Request $request, $id)
    {

        $edit_lead_special_access = 0;
        if ($request->edit_lead_special_access == true) {
            $edit_lead_special_access = 1;
        }
        $selectedTeamKeys = [];
        if ($request->get('edit_type') === 'ppc') {
            $selectedTeamKeys = $request->get('assigned_team_key') ?? [];
            if (in_array(0, $selectedTeamKeys)) {
                $selectedTeamKeys = Team::pluck('team_key')->toArray();
            }
            if (count($selectedTeamKeys) == 0) {
                $selectedTeamKeys = null;
            }
        }

        $user = User::where('id', $id)->first();
        if (!$user) {
            return response()->json(['error', 'Oops! Employee not found.'], 404);
        }
        $user->name = $request->edit_name;
        $user->email = $request->edit_email;
        $user->phone = $request->edit_phone;
        $user->designation = $request->edit_designation;
        $user->pseudo_name = $request->edit_pseudo_name;
        $user->pseudo_email = $request->edit_pseudo_email;
        $user->target = $request->edit_target;
        $user->image = $request->edit_image;
        $user->type = $request->edit_type;
        $user->status = $request->edit_status;
        $user->lead_special_access = $edit_lead_special_access;
        $user->assigned_teams = $selectedTeamKeys;
        $user->user_access = $request->edit_user_access;
//        $user->has_department = $request->boolean('has_department') ? 1 : 0;
        $user->save();
        if (in_array($request->input('edit_type'), ['executive', 'ppc', 'third-party-user'])) {
            if ($request->filled('assigned_departments')) {
                $user->setDepartment()->sync($request->get('assigned_departments'));
            } else {
                $user->setDepartment()->detach();
            }
        } else {
            $user->setDepartment()->detach();
        }
        $user->load('getDepartment');
        return $user;

    }

    /** update password employee */
    public function update_employee_pass(Request $request)
    {
        return User::where('id', $request->mid)->update(['password' => Hash::make($request->edit_pass)]);
    }
}

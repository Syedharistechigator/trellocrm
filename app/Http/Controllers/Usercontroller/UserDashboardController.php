<?php

namespace App\Http\Controllers\Usercontroller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Payment;
use App\Models\Invoice;
use App\Models\LeadStatus;
use App\Models\Lead;
use App\Models\Brand;
use App\Models\Team;
use App\Models\User;
use App\Models\AssignBrand;
use App\Models\ProjectStatus;
use App\Models\ProjectCategory;
use App\Models\Project;
use App\Models\Client;
use App\Models\Spending;
use App\Models\Comment;
use App\Models\Refund;
use App\Models\Expense;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserDashboardController extends Controller
{
    public function index()
    {

        $now = \Carbon\Carbon::now();


        $userType = Auth::user()->type;

        if ($userType == 'lead') {

            $teamKey = Auth::user()->team_key;

            //Month Payment
            $monthPayment = Payment::where(['payment_status' => 1, 'team_key' => $teamKey])
                ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth(),])
                ->sum('amount');
            //set Type
            settype($monthPayment, "integer");
            //------end------

            //Today Payment
            $todayPayment = Payment::where(['payment_status' => 1, 'team_key' => $teamKey])
                ->whereDate('created_at', Carbon::today())
                ->sum('amount');
            settype($todayPayment, "integer");
            //--------end---------------

            //Fresh Payment
            $dueInvoicePayment = Payment::where(['payment_status' => 1, 'team_key' => $teamKey, 'sales_type' => 'Fresh'])
                ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth(),])
                ->sum('amount');
            settype($dueInvoicePayment, "integer");
            //--------end---------------

            //Due Invoice Payment
            $overdueInvoicePayment = Payment::where(['payment_status' => 1, 'team_key' => $teamKey, 'sales_type' => 'Upsale'])
                ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth(),])
                ->sum('amount');
            settype($overdueInvoicePayment, "integer");
            //--------end---------------

            //Total Expense
            $totalExpense = Expense::where(['status' => 1, 'team_key' => $teamKey])
                ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth(),])
                ->sum('amount');
            settype($totalExpense, "integer");
            //--------end---------------


            //Leads Status
            $leadstatus = LeadStatus::all();
            $leadData = array();
            $statusValue = array();
            $statusColor = array();
            $i = 1;

            foreach ($leadstatus as $status) {
                $leads = Lead::where(['status' => $status->id, 'team_key' => $teamKey])->count();
                $statusName = array('data' . $i => $status->status);

                $value = array('data' . $i, $leads);
                array_push($statusValue, $value);
                $leadData['data' . $i] = $status->status;
                $statusColor['data' . $i] = "Aero.colors['" . $status->leadstatus_color . "']";
                $i++;
            }
            //--------end---------------

            //Current Year Income
            $yearIncome = Payment::where(['payment_status' => 1, 'team_key' => $teamKey])
                ->whereYear('created_at', $now->format('Y'))
                ->sum('amount');
            settype($yearIncome, "integer");
            //--------end---------------

            //Current Year Refund
            $yearRefund = Refund::where(['type' => 'refund', 'team_key' => $teamKey, 'qa_approval' => 1])
                ->whereYear('created_at', $now->format('Y'))
                ->sum('amount');
            settype($yearRefund, "integer");
            //--------end---------------

            //Current Year Charge back
            $yearchargeback = Refund::where(['type' => 'chargeback', 'team_key' => $teamKey, 'qa_approval' => 1])
                ->whereYear('created_at', $now->format('Y'))
                ->sum('amount');
            settype($yearchargeback, "integer");
            //--------end---------------


            //Current year month wise data
            $yearMonthWise = Payment::select(DB::raw("(SUM(amount)) as amount"), DB::raw("MONTHNAME(created_at) as monthname"))
                ->where(['payment_status' => 1, 'team_key' => $teamKey])
                ->whereYear('created_at', date('Y'))
                ->groupBy('monthname')
                ->get();

            $month = array();
            $monthAmount = array('data1');
            $m2 = array();

            //$categories = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October','November','December'];

            foreach ($yearMonthWise as $key => $monthWise) {

                if ('January' == $monthWise->monthname) {
                    $m2['January'] = [$monthWise->monthname, $monthWise->amount];
                }
                if ('February' == $monthWise->monthname) {
                    $m2['February'] = [$monthWise->monthname, $monthWise->amount];
                }
                if ('March' == $monthWise->monthname) {
                    $m2['March'] = [$monthWise->monthname, $monthWise->amount];
                }
                if ('April' == $monthWise->monthname) {
                    $m2['April'] = [$monthWise->monthname, $monthWise->amount];
                }
                if ('May' == $monthWise->monthname) {
                    $m2['May'] = [$monthWise->monthname, $monthWise->amount];
                }
                if ('June' == $monthWise->monthname) {
                    $m2['June'] = [$monthWise->monthname, $monthWise->amount];
                }
                if ('July' == $monthWise->monthname) {
                    $m2['July'] = [$monthWise->monthname, $monthWise->amount];
                }
                if ('August' == $monthWise->monthname) {
                    $m2['August'] = [$monthWise->monthname, $monthWise->amount];
                }
                if ('September' == $monthWise->monthname) {
                    $m2['September'] = [$monthWise->monthname, $monthWise->amount];
                }
                if ('October' == $monthWise->monthname) {
                    $m2['October'] = [$monthWise->monthname, $monthWise->amount];
                }
                if ('November' == $monthWise->monthname) {
                    $m2['November'] = [$monthWise->monthname, $monthWise->amount];
                }
                if ('December' == $monthWise->monthname) {
                    $m2['December'] = [$monthWise->monthname, $monthWise->amount];
                }
            }

            // Team Refund & Charge Back
            // year month wise data

            $refundYearMonthWise = Refund::select(DB::raw("(SUM(amount)) as amount"), DB::raw("MONTHNAME(created_at) as monthname"))
                ->where(['team_key' => $teamKey, 'type' => 'refund'])
                ->whereYear('created_at', date('Y'))
                ->groupBy('monthname')
                ->get();


            $refundYearMonthWiseData = array();

            foreach ($refundYearMonthWise as $key1 => $monthWise1) {

                if ('January' == $monthWise1->monthname) {
                    $refundYearMonthWiseData['January'] = [$monthWise1->monthname, $monthWise1->amount];
                }
                if ('February' == $monthWise1->monthname) {
                    $refundYearMonthWiseData['February'] = [$monthWise1->monthname, $monthWise1->amount];
                }
                if ('March' == $monthWise1->monthname) {
                    $refundYearMonthWiseData['March'] = [$monthWise1->monthname, $monthWise1->amount];
                }
                if ('April' == $monthWise1->monthname) {
                    $refundYearMonthWiseData['April'] = [$monthWise1->monthname, $monthWise1->amount];
                }
                if ('May' == $monthWise1->monthname) {
                    $refundYearMonthWiseData['May'] = [$monthWise1->monthname, $monthWise1->amount];
                }
                if ('June' == $monthWise1->monthname) {
                    $refundYearMonthWiseData['June'] = [$monthWise1->monthname, $monthWise1->amount];
                }
                if ('July' == $monthWise1->monthname) {
                    $refundYearMonthWiseData['July'] = [$monthWise1->monthname, $monthWise1->amount];
                }
                if ('August' == $monthWise1->monthname) {
                    $refundYearMonthWiseData['August'] = [$monthWise1->monthname, $monthWise1->amount];
                }
                if ('September' == $monthWise1->monthname) {
                    $refundYearMonthWiseData['September'] = [$monthWise1->monthname, $monthWise1->amount];
                }
                if ('October' == $monthWise1->monthname) {
                    $refundYearMonthWiseData['October'] = [$monthWise1->monthname, $monthWise1->amount];
                }
                if ('November' == $monthWise1->monthname) {
                    $refundYearMonthWiseData['November'] = [$monthWise1->monthname, $monthWise1->amount];
                }
                if ('December' == $monthWise1->monthname) {
                    $refundYearMonthWiseData['December'] = [$monthWise1->monthname, $monthWise1->amount];
                }
            }


            $chargebackYearMonthWise = Refund::select(DB::raw("(SUM(amount)) as amount"), DB::raw("MONTHNAME(created_at) as monthname"))
                ->where(['team_key' => $teamKey, 'type' => 'chargeback'])
                ->whereYear('created_at', date('Y'))
                ->groupBy('monthname')
                ->get();

            $chargebackYearMonthWiseData = array();

            foreach ($chargebackYearMonthWise as $key1 => $monthWise1) {

                if ('January' == $monthWise1->monthname) {
                    $chargebackYearMonthWiseData['January'] = [$monthWise1->monthname, $monthWise1->amount];
                }
                if ('February' == $monthWise1->monthname) {
                    $chargebackYearMonthWiseData['February'] = [$monthWise1->monthname, $monthWise1->amount];
                }
                if ('March' == $monthWise1->monthname) {
                    $chargebackYearMonthWiseData['March'] = [$monthWise1->monthname, $monthWise1->amount];
                }
                if ('April' == $monthWise1->monthname) {
                    $chargebackYearMonthWiseData['April'] = [$monthWise1->monthname, $monthWise1->amount];
                }
                if ('May' == $monthWise1->monthname) {
                    $chargebackYearMonthWiseData['May'] = [$monthWise1->monthname, $monthWise1->amount];
                }
                if ('June' == $monthWise1->monthname) {
                    $chargebackYearMonthWiseData['June'] = [$monthWise1->monthname, $monthWise1->amount];
                }
                if ('July' == $monthWise1->monthname) {
                    $chargebackYearMonthWiseData['July'] = [$monthWise1->monthname, $monthWise1->amount];
                }
                if ('August' == $monthWise1->monthname) {
                    $chargebackYearMonthWiseData['August'] = [$monthWise1->monthname, $monthWise1->amount];
                }
                if ('September' == $monthWise1->monthname) {
                    $chargebackYearMonthWiseData['September'] = [$monthWise1->monthname, $monthWise1->amount];
                }
                if ('October' == $monthWise1->monthname) {
                    $chargebackYearMonthWiseData['October'] = [$monthWise1->monthname, $monthWise1->amount];
                }
                if ('November' == $monthWise1->monthname) {
                    $chargebackYearMonthWiseData['November'] = [$monthWise1->monthname, $monthWise1->amount];
                }
                if ('December' == $monthWise1->monthname) {
                    $chargebackYearMonthWiseData['December'] = [$monthWise1->monthname, $monthWise1->amount];
                }
            }

            $expenseYearMonthWise = Expense::select(DB::raw("(SUM(amount)) as amount"), DB::raw("MONTHNAME(created_at) as monthname"))
                ->where(['team_key' => $teamKey, 'status' => 1])
                ->whereYear('created_at', date('Y'))
                ->groupBy('monthname')
                ->get();

            $expenseYearMonthWiseData = array();

            foreach ($expenseYearMonthWise as $key2 => $monthWise2) {

                if ('January' == $monthWise2->monthname) {
                    $expenseYearMonthWiseData['January'] = [$monthWise2->monthname, $monthWise2->amount];
                }
                if ('February' == $monthWise2->monthname) {
                    $expenseYearMonthWiseData['February'] = [$monthWise2->monthname, $monthWise2->amount];
                }
                if ('March' == $monthWise2->monthname) {
                    $expenseYearMonthWiseData['March'] = [$monthWise2->monthname, $monthWise2->amount];
                }
                if ('April' == $monthWise2->monthname) {
                    $expenseYearMonthWiseData['April'] = [$monthWise2->monthname, $monthWise2->amount];
                }
                if ('May' == $monthWise2->monthname) {
                    $expenseYearMonthWiseData['May'] = [$monthWise2->monthname, $monthWise2->amount];
                }
                if ('June' == $monthWise2->monthname) {
                    $expenseYearMonthWiseData['June'] = [$monthWise2->monthname, $monthWise2->amount];
                }
                if ('July' == $monthWise2->monthname) {
                    $expenseYearMonthWiseData['July'] = [$monthWise2->monthname, $monthWise2->amount];
                }
                if ('August' == $monthWise2->monthname) {
                    $expenseYearMonthWiseData['August'] = [$monthWise2->monthname, $monthWise2->amount];
                }
                if ('September' == $monthWise2->monthname) {
                    $expenseYearMonthWiseData['September'] = [$monthWise2->monthname, $monthWise2->amount];
                }
                if ('October' == $monthWise2->monthname) {
                    $expenseYearMonthWiseData['October'] = [$monthWise2->monthname, $monthWise2->amount];
                }
                if ('November' == $monthWise2->monthname) {
                    $expenseYearMonthWiseData['November'] = [$monthWise2->monthname, $monthWise2->amount];
                }
                if ('December' == $monthWise2->monthname) {
                    $expenseYearMonthWiseData['December'] = [$monthWise2->monthname, $monthWise2->amount];
                }
            }


            //Recent Payments
            $paymentData = array();
            $payments = Payment::where(['payment_status' => 1, 'team_key' => $teamKey])
                ->orderBy('id', 'desc')->limit(5)->get();

            foreach ($payments as $payment) {
                $agentId = $payment->agent_id;
                $brandKey = $payment->brand_key;

                $agent_name = User::where('id', $agentId)->value('name');
                $payment['agentName'] = $agent_name;

                $brand_name = Brand::where('brand_key', $brandKey)->value('name');
                $payment['brandName'] = $brand_name;

                array_push($paymentData, $payment);
            }
            //--------end---------------

            //Current Month Brand Income
            $data = AssignBrand::where('team_key', $teamKey)->get();
            $brandData = array();

            foreach ($data as $a) {

                $brand_key = $a->brand_key;
                $brands = Brand::where(['brand_key' => $brand_key, 'status' => 1])->get();
                foreach ($brands as $brand) {
                    $a['brandLogo'] = $brand->logo;
                    $a['brandName'] = $brand->name;

                    $brandAmount = Payment::where(['brand_key' => $brand->brand_key, 'payment_status' => 1])
                        ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth(),])
                        ->sum('amount');

                    settype($brandAmount, "integer");
                    $a['amount'] = thousand_format($brandAmount);

                    //last month income
                    $lastMonthRevenue = Payment::select('*')
                        ->where(['brand_key' => $brand->brand_key, 'payment_status' => 1])
                        ->whereMonth('created_at', Carbon::now()->subMonth()->month)
                        ->sum('amount');

                    settype($lastMonthRevenue, "integer");

                    $a['lastmonthamount'] = thousand_format($lastMonthRevenue);

                    array_push($brandData, $a);
                }

            }
            //--------end---------------

            //Current Month Team Income
            $members = User::where(['team_key' => $teamKey, 'status' => 1])
                ->where('type', '!=', 'client')
                ->orderBy('type', 'asc')->get();
            $teamData = array();
            foreach ($members as $team) {

                $teamAmount = Payment::where(
                    [
                        'team_key' => $teamKey,
                        'payment_status' => 1,
                        'agent_id' => $team->id
                    ])
                    ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth(),])
                    ->sum('amount');

                settype($teamAmount, "integer");
                $team['amount'] = thousand_format($teamAmount);

                //last month income
                $lastMonthRevenue = Payment::select('*')
                    ->where([
                        'team_key' => $teamKey,
                        'payment_status' => 1,
                        'agent_id' => $team->id
                    ])
                    ->whereMonth('created_at', Carbon::now()->subMonth()->month)
                    ->sum('amount');

                settype($lastMonthRevenue, "integer");

                $team['lastmonthamount'] = thousand_format($lastMonthRevenue);

                array_push($teamData, $team);
            }


            //Team Project Status - Project Count
            $project_status = ProjectStatus::all();
            $project_status_data = array();
            foreach ($project_status as $status) {
                $project_count = Project::where(['project_status' => $status->id, 'team_key' => $teamKey])->count();
                $status['count'] = $project_count;
                array_push($project_status_data, $status);
            }

            //Team Project Category - Project Count
            $project_categories = ProjectCategory::all();
            $project_categories_data = array();
            foreach ($project_categories as $category) {
                $project_count = Project::where(['category_id' => $category->id, 'team_key' => $teamKey])->count();
                $category['count'] = $project_count;
                array_push($project_categories_data, $category);
            }


            // ======================Spending Data ========================

            //Current Year Spending
            $yearSpending = Spending::where('team_key', $teamKey)
                ->whereYear('created_at', $now->format('Y'))
                ->sum('amount');
            settype($yearSpending, "integer");
            //--------end---------------

            //total Spendings
            $monthSpending = Spending::where('team_key', $teamKey)
                ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth(),])
                ->sum('amount');
            settype($monthSpending, "integer");


            //Google Spending
            $googleSpending = Spending::where(['team_key' => $teamKey, 'platform' => 'Google'])
                ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth(),])
                ->sum('amount');
            settype($googleSpending, "integer");

            //Bing Spending
            $bingSpending = Spending::where(['team_key' => $teamKey, 'platform' => 'Bing'])
                ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth(),])
                ->sum('amount');
            settype($bingSpending, "integer");


            //

            // ======================End Spending Data ==================

            $data = [
                "monthPayment" => thousand_format($monthPayment),
                "todayPayment" => thousand_format($todayPayment),
                "dueInvoicePayment" => thousand_format($dueInvoicePayment),
                "overdueInvoicePayment" => thousand_format($overdueInvoicePayment),
                "leadstatus" => json_encode($leadData),
                "statusValue" => json_encode($statusValue),
                "statusColor" => json_encode($statusColor),
                "yearlyIncome" => thousand_format($yearIncome),
                "monthWise" => json_encode($month),
                "monthWiseAmount" => json_encode($monthAmount),
                "brandData" => $brandData,
                "teamData" => $teamData,
                "recentPayments" => $paymentData,
                "m2" => $m2,
                'projectStatus' => $project_status_data,
                'projectcategoriesdata' => $project_categories_data,
                'googleSpending' => thousand_format($googleSpending),
                'bingSpending' => thousand_format($bingSpending),
                'yearSpending' => thousand_format($yearSpending),
                'monthSpending' => thousand_format($monthSpending),

                "yearRefund" => thousand_format($yearRefund),
                "yearchargeback" => thousand_format($yearchargeback),
                "refundYearMonthWiseData" => $refundYearMonthWiseData,
                "chargebackYearMonthWiseData" => $chargebackYearMonthWiseData,
                "totalExpense" => thousand_format($totalExpense),
                "expenseYearMonthWiseData" => $expenseYearMonthWiseData,


            ];

            //--------end---------------


        } elseif ($userType == 'staff') {

            $staffId = Auth::user()->id;

            // ====================== Top 4 Box =========================
            //Month Payment
            $monthPayment = Payment::where(['payment_status' => 1, 'agent_id' => $staffId])
                ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth(),])
                ->sum('amount');
            //set Type
            settype($monthPayment, "integer");
            //------end------

            //Today Payment
            $todayPayment = Payment::where(['payment_status' => 1, 'agent_id' => $staffId])
                ->whereDate('created_at', Carbon::today())
                ->sum('amount');
            settype($todayPayment, "integer");
            //--------end---------------

            //Fresh Payment
            $dueInvoicePayment = Payment::where(['payment_status' => 1, 'agent_id' => $staffId, 'sales_type' => 'Fresh'])
                ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth(),])
                ->sum('amount');
            settype($dueInvoicePayment, "integer");
            //--------end---------------

            //Due Invoice Payment
            $overdueInvoicePayment = Payment::where(['payment_status' => 1, 'agent_id' => $staffId, 'sales_type' => 'Upsale'])
                ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth(),])
                ->sum('amount');
            settype($overdueInvoicePayment, "integer");
            //--------end---------------

            // ====================== end Top 4 Box =========================


            // ====================== Sales Report =========================

            //Current Year Income
            $yearIncome = Payment::where(['payment_status' => 1, 'agent_id' => $staffId])
                ->whereYear('created_at', $now->format('Y'))
                ->sum('amount');
            settype($yearIncome, "integer");
            //--------end---------------

            //Current Year Refund
            $yearRefund = Refund::where(['type' => 'refund', 'agent_id' => $staffId, 'qa_approval' => 1])
                ->whereYear('created_at', $now->format('Y'))
                ->sum('amount');
            settype($yearRefund, "integer");
            //--------end---------------

            //Current Year Charge back
            $yearchargeback = Refund::where(['type' => 'chargeback', 'agent_id' => $staffId, 'qa_approval' => 1])
                ->whereYear('created_at', $now->format('Y'))
                ->sum('amount');
            settype($yearchargeback, "integer");
            //--------end---------------

            // agent Project Count
            $projectCount = Project::where('agent_id', $staffId)->count();

            //Current year month wise data
            $yearMonthWise = Payment::select(DB::raw("(SUM(amount)) as amount"), DB::raw("MONTHNAME(created_at) as monthname"))
                ->where(['payment_status' => 1, 'agent_id' => $staffId])
                ->whereYear('created_at', date('Y'))
                ->groupBy('monthname')
                ->get();

            $month = array();
            $monthAmount = array('data1');
            $m2 = array();

            $categories = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];

            foreach ($yearMonthWise as $key => $monthWise) {

                if ('January' == $monthWise->monthname) {
                    $m2['January'] = [$monthWise->monthname, $monthWise->amount];
                }
                if ('February' == $monthWise->monthname) {
                    $m2['February'] = [$monthWise->monthname, $monthWise->amount];
                }
                if ('March' == $monthWise->monthname) {
                    $m2['March'] = [$monthWise->monthname, $monthWise->amount];
                }
                if ('April' == $monthWise->monthname) {
                    $m2['April'] = [$monthWise->monthname, $monthWise->amount];
                }
                if ('May' == $monthWise->monthname) {
                    $m2['May'] = [$monthWise->monthname, $monthWise->amount];
                }
                if ('June' == $monthWise->monthname) {
                    $m2['June'] = [$monthWise->monthname, $monthWise->amount];
                }
                if ('July' == $monthWise->monthname) {
                    $m2['July'] = [$monthWise->monthname, $monthWise->amount];
                }
                if ('August' == $monthWise->monthname) {
                    $m2['August'] = [$monthWise->monthname, $monthWise->amount];
                }
                if ('September' == $monthWise->monthname) {
                    $m2['September'] = [$monthWise->monthname, $monthWise->amount];
                }
                if ('October' == $monthWise->monthname) {
                    $m2['October'] = [$monthWise->monthname, $monthWise->amount];
                }
                if ('November' == $monthWise->monthname) {
                    $m2['November'] = [$monthWise->monthname, $monthWise->amount];
                }
                if ('December' == $monthWise->monthname) {
                    $m2['December'] = [$monthWise->monthname, $monthWise->amount];
                }
            }


            //Recent Payments
            $paymentData = array();
            $payments = Payment::where(['payment_status' => 1, 'agent_id' => $staffId])
                ->orderBy('id', 'desc')->limit(5)->get();

            foreach ($payments as $payment) {
                $agentId = $payment->agent_id;
                $brandKey = $payment->brand_key;

                $agent_name = User::where('id', $agentId)->value('name');
                $payment['agentName'] = $agent_name;

                $brand_name = Brand::where('brand_key', $brandKey)->value('name');
                $payment['brandName'] = $brand_name;

                array_push($paymentData, $payment);
            }
            //--------end---------------

            //Team Project Status - Project Count
            $project_status = ProjectStatus::all();
            $project_status_data = array();
            foreach ($project_status as $status) {
                $project_count = Project::where(['project_status' => $status->id, 'agent_id' => $staffId])->count();
                $status['count'] = $project_count;
                array_push($project_status_data, $status);
            }

            //Team Project Category - Project Count
            $project_categories = ProjectCategory::all();
            $project_categories_data = array();
            foreach ($project_categories as $category) {
                $project_count = Project::where(['category_id' => $category->id, 'agent_id' => $staffId])->count();
                $category['count'] = $project_count;
                array_push($project_categories_data, $category);
            }


            //Latest Activity comments
            $clientComments = Comment::where('creatorid', $staffId)->orderBy('id', 'desc')->get();
            $comments = array();
            foreach ($clientComments as $comment) {
                $comment['creatorName'] = User::where('id', $comment->creatorid)->value('pseudo_name');
                $comment['clientName'] = Client::where('id', $comment->clientid)->value('name');
                $comment['projectName'] = Project::where('id', $comment->projectid)->value('project_title');

                array_push($comments, $comment);
            }

            $data = [
                "monthPayment" => thousand_format($monthPayment),
                "todayPayment" => thousand_format($todayPayment),
                "dueInvoicePayment" => thousand_format($dueInvoicePayment),
                "overdueInvoicePayment" => thousand_format($overdueInvoicePayment),
                "yearlyIncome" => thousand_format($yearIncome),
                "monthWise" => json_encode($month),
                "monthWiseAmount" => json_encode($monthAmount),
                "recentPayments" => $paymentData,
                "m2" => $m2,
                'projectStatus' => $project_status_data,
                "yearRefund" => thousand_format($yearRefund),
                "yearchargeback" => thousand_format($yearchargeback),
                'projectCount' => $projectCount,
                'clientComments' => $comments,
                'projectcategoriesdata' => $project_categories_data,

            ];


        }
        elseif ($userType == 'tm-user') {

            $staffId = Auth::user()->id;

            /**  Recent Payments */
            $paymentData = [];
            $payments = Payment::where(['payment_status' => 1, 'agent_id' => $staffId])
                ->orderBy('id', 'desc')->limit(5)->get();

            foreach ($payments as $payment) {
                $agentId = $payment->agent_id;
                $brandKey = $payment->brand_key;
                $agent_name = User::where('id', $agentId)->value('name');
                $payment['agentName'] = $agent_name;
                $brand_name = Brand::where('brand_key', $brandKey)->value('name');
                $payment['brandName'] = $brand_name;
                $paymentData[] = $payment;
            }
            /**  Recent Payments End */

            // ====================== Sales Report =========================

            /** =================================== Current year month wise data ========================================== */
            $yearMonthWise = Payment::select(DB::raw("(SUM(amount)) as amount"), DB::raw("MONTHNAME(created_at) as monthname"))
                ->where('payment_status', 1)
                ->where( 'agent_id' , $staffId)
                ->whereYear('created_at', now()->year)
                ->groupBy('monthname')
                ->get();
            $m2 = [];
            $monthData = $yearMonthWise->pluck('amount', 'monthname');

            foreach (config('app.months') as $monthName) {
                $m2[$monthName] = [$monthName, $monthData->get($monthName, 0)];
            }
            /** =================================== End Current year month wise data ========================================== */

            $data = [
                /** ============================= Top 4 Cards ========================= */
                "monthPayment" => thousand_format((int)Payment::monthSuccessPayments(null, $staffId)->sum('amount')),
                "todayPayment" => thousand_format((int)Payment::todaySuccessPayments(null, $staffId)->sum('amount')),
                "dueInvoicePayment" => thousand_format((int)Payment::freshSuccessPayments(null, $staffId)->sum('amount')),
                "overdueInvoicePayment" => thousand_format((int)Payment::upsaleSuccessPayments(null, $staffId)->sum('amount')),
                /** ============================= End Top 4 Cards ========================= */
                "recentPayments" => $paymentData,
                "m2" => $m2,
            ];


        } elseif ($userType == 'qa') {

            $refundYearMonthWise = Refund::select(DB::raw("(SUM(amount)) as amount"), DB::raw("MONTHNAME(created_at) as monthname"))
                ->where('type', 'refund')
                ->whereYear('created_at', date('Y'))
                ->groupBy('monthname')
                ->get();

            $refundYearMonthWiseData = array();

            foreach ($refundYearMonthWise as $key1 => $monthWise1) {

                if ('January' == $monthWise1->monthname) {
                    $refundYearMonthWiseData['January'] = [$monthWise1->monthname, $monthWise1->amount];
                }
                if ('February' == $monthWise1->monthname) {
                    $refundYearMonthWiseData['February'] = [$monthWise1->monthname, $monthWise1->amount];
                }
                if ('March' == $monthWise1->monthname) {
                    $refundYearMonthWiseData['March'] = [$monthWise1->monthname, $monthWise1->amount];
                }
                if ('April' == $monthWise1->monthname) {
                    $refundYearMonthWiseData['April'] = [$monthWise1->monthname, $monthWise1->amount];
                }
                if ('May' == $monthWise1->monthname) {
                    $refundYearMonthWiseData['May'] = [$monthWise1->monthname, $monthWise1->amount];
                }
                if ('June' == $monthWise1->monthname) {
                    $refundYearMonthWiseData['June'] = [$monthWise1->monthname, $monthWise1->amount];
                }
                if ('July' == $monthWise1->monthname) {
                    $refundYearMonthWiseData['July'] = [$monthWise1->monthname, $monthWise1->amount];
                }
                if ('August' == $monthWise1->monthname) {
                    $refundYearMonthWiseData['August'] = [$monthWise1->monthname, $monthWise1->amount];
                }
                if ('September' == $monthWise1->monthname) {
                    $refundYearMonthWiseData['September'] = [$monthWise1->monthname, $monthWise1->amount];
                }
                if ('October' == $monthWise1->monthname) {
                    $refundYearMonthWiseData['October'] = [$monthWise1->monthname, $monthWise1->amount];
                }
                if ('November' == $monthWise1->monthname) {
                    $refundYearMonthWiseData['November'] = [$monthWise1->monthname, $monthWise1->amount];
                }
                if ('December' == $monthWise1->monthname) {
                    $refundYearMonthWiseData['December'] = [$monthWise1->monthname, $monthWise1->amount];
                }
            }


            $chargebackYearMonthWise = Refund::select(DB::raw("(SUM(amount)) as amount"), DB::raw("MONTHNAME(created_at) as monthname"))
                ->where('type', 'chargeback')
                ->whereYear('created_at', date('Y'))
                ->groupBy('monthname')
                ->get();

            $chargebackYearMonthWiseData = array();

            foreach ($chargebackYearMonthWise as $key1 => $monthWise1) {

                if ('January' == $monthWise1->monthname) {
                    $chargebackYearMonthWiseData['January'] = [$monthWise1->monthname, $monthWise1->amount];
                }
                if ('February' == $monthWise1->monthname) {
                    $chargebackYearMonthWiseData['February'] = [$monthWise1->monthname, $monthWise1->amount];
                }
                if ('March' == $monthWise1->monthname) {
                    $chargebackYearMonthWiseData['March'] = [$monthWise1->monthname, $monthWise1->amount];
                }
                if ('April' == $monthWise1->monthname) {
                    $chargebackYearMonthWiseData['April'] = [$monthWise1->monthname, $monthWise1->amount];
                }
                if ('May' == $monthWise1->monthname) {
                    $chargebackYearMonthWiseData['May'] = [$monthWise1->monthname, $monthWise1->amount];
                }
                if ('June' == $monthWise1->monthname) {
                    $chargebackYearMonthWiseData['June'] = [$monthWise1->monthname, $monthWise1->amount];
                }
                if ('July' == $monthWise1->monthname) {
                    $chargebackYearMonthWiseData['July'] = [$monthWise1->monthname, $monthWise1->amount];
                }
                if ('August' == $monthWise1->monthname) {
                    $chargebackYearMonthWiseData['August'] = [$monthWise1->monthname, $monthWise1->amount];
                }
                if ('September' == $monthWise1->monthname) {
                    $chargebackYearMonthWiseData['September'] = [$monthWise1->monthname, $monthWise1->amount];
                }
                if ('October' == $monthWise1->monthname) {
                    $chargebackYearMonthWiseData['October'] = [$monthWise1->monthname, $monthWise1->amount];
                }
                if ('November' == $monthWise1->monthname) {
                    $chargebackYearMonthWiseData['November'] = [$monthWise1->monthname, $monthWise1->amount];
                }
                if ('December' == $monthWise1->monthname) {
                    $chargebackYearMonthWiseData['December'] = [$monthWise1->monthname, $monthWise1->amount];
                }
            }

            $data = [
                "refundYearMonthWiseData" => $refundYearMonthWiseData,
                "chargebackYearMonthWiseData" => $chargebackYearMonthWiseData,
            ];


        } else {

            $clientUserId = Auth::user()->id;
            $loginClientId = Auth::user()->clientid;


            //Due Invoice Payment
            $dueInvoicePayment = Invoice::where(['status' => 'due', 'clientid' => $loginClientId])
                ->sum('final_amount');
            settype($dueInvoicePayment, "integer");

//            ->whereBetween('created_at', [now()->startOfMonth(),now()->endOfMonth(),])
            // ->whereMonth('created_at', Carbon::now()->month)
            //--------end---------------

            //Due Invoice Payment
            $overdueInvoicePayment = Invoice::where('due_date', '<', $now)
                ->where(['status' => 'due', 'clientid' => $loginClientId])
                ->sum('final_amount');
            settype($overdueInvoicePayment, "integer");
            //--------end---------------

            //Pending Projects
            $pendingProject = Project::where(['project_status' => 3, 'clientid' => $loginClientId])->count();
            //complete Projects
            $completeProject = Project::where(['project_status' => 5, 'clientid' => $loginClientId])->count();
            //client Project
            $clientProjectCXM = Project::where('clientid', $loginClientId)->get();
            $clientProject = array();
            foreach ($clientProjectCXM as $yy) {
                $status = ProjectStatus::where('id', $yy->project_status)->first();
                $yy['status'] = $status->status;
                $yy['statusColor'] = $status->status_color;
                array_push($clientProject, $yy);
            }

            //Latest Activity comments
            $clientComments = Comment::where(['clientid' => $loginClientId, 'type' => 'staff'])->orderBy('id', 'desc')->get();

            $comments = array();
            foreach ($clientComments as $comment) {
                $comment['creatorName'] = User::where('id', $comment->creatorid)->value('pseudo_name');
                $comment['clientName'] = Client::where('id', $comment->clientid)->value('name');
                $comment['projectName'] = Project::where('id', $comment->projectid)->value('project_title');

                array_push($comments, $comment);
            }


            $data = [
                "pendingProject" => $pendingProject,
                "completeProject" => $completeProject,
                "clientProject" => $clientProject,
                "dueInvoicePayment" => thousand_format($dueInvoicePayment),
                "overdueInvoicePayment" => thousand_format($overdueInvoicePayment),
                'clientComments' => $comments

            ];
        }
        return view('dashboard', compact('data'));

    }

    public function userProfile()
    {
        $id = Auth::user()->id;
        $member = User::find($id);
        $teamAmount = Payment::where(['payment_status' => 1, 'agent_id' => $member->id])
            ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth(),])
            ->sum('amount');
        settype($teamAmount, "integer");
        $member['achived_amount'] = $teamAmount;
        return view('userProfile', compact('member'));
    }
    public function password_confirmation(Request $request){
        $rules = [
            'password' => 'required',
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }
        if (!Auth::check()) {
            return response()->json([
                'error' => 'Unauthorized',
                'message' => 'Unauthorized! Please login again.'
            ], 401);
        }
        if (!Hash::check($request->password, Auth::user()->password)) {
            return response()->json([
                'errors' => ['password' => ['Incorrect Password.']],
            ], 422);
        }
        return response()->json([
            'success' => true,
            'message' => 'Password confirmed.',
        ]);
    }
    public function profile_update(Request $request){
        $rules = [
            'name' => 'required',
            'pseudo_email' => 'required',
            'phone' => 'required',
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json([
                    'error' => 'User not found',
                    'message' => 'Authenticated user not found'
                ], 404);
            }
            $user->name = $request->input('name');
            $user->pseudo_email = $request->input('pseudo_email');
            $user->phone = $request->input('phone');
            $user->save();

            return response()->json([
                'success' => 'Profile updated successfully.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to update profile',
                'message' => $e->getMessage()
            ], 500);
        }
    }
    public function user_profile_password_update(Request $request)
    {
        $rules = [
            'old_password' => 'required',
            'password' => 'required|min:6|confirmed',
            'password_confirmation' => 'required|min:6',
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }
        $user = auth()->user();
        if (!Hash::check($request->old_password, $user->password)) {
            return response()->json([
                'errors' => ['old_password' => ['The provided old password is incorrect.']],
            ], 422);
        }
        $user->update(['password' => Hash::make($request->password),]);

        return response()->json(['message' => 'Password updated successfully.'], 200);
    }
    public function update_profile_image(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 400);
        }

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('assets/images/profile_images'), $imageName);
            auth()->user()->update(['image' => $imageName]);
            return response()->json([
                'success' => true,
            ]);
        }
        return response()->json([
            'error' => false,
        ]);
    }
}

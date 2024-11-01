<?php

namespace App\Http\Controllers\Admincontroller;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Client;
use App\Models\Expense;
use App\Models\Invoice;
use App\Models\Lead;
use App\Models\LeadStatus;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\ProjectStatus;
use App\Models\Refund;
use App\Models\Spending;
use App\Models\Team;
use App\Models\ThirdPartyRoleModel;
use App\Models\WirePaymentModel;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Models\Team\{
    IndirectCostingModel as TeamIndirectCostingModel,
    SpendingModel as TeamSpendingModel,
    TargetModel as TeamTargetModel,
    CarryForwardModel as TeamCarryForwardModel,
    FixedCostingModel as TeamFixedCostingModel
};
use App\Models\User;
use DB;


class adminDashboardController extends Controller
{
    public function stats(Request $request)
    {
        $teams = Team::where('status', '1')->get();
        $selected_month_num = $current_month = Carbon::now()->month;
        $selected_month = $request->get('month', Carbon::now()->format('F'));
        $current_year = Carbon::now()->year;
        $selected_year = $request->get('year', Carbon::now()->year);
        $selected_team = $request->get('teamKey');

        if ($request->filled('month')) {
            $input_month = Str::title($request->input('month'));
            $monthNumeric = array_search($input_month, config('app.months'));
            if ($monthNumeric !== false) {
                $selected_month_num = $monthNumeric + 1;
            }
        }


        $target_amount = TeamTargetModel::applyTeamKey($selected_team)->applyMonth($selected_month)->applyYear($selected_year)->sum('amount');

        $team_spending_amount = TeamSpendingModel::applyTeamKey($selected_team)->applyMonth($selected_month)->applyYear($selected_year)->sum('amount');
        $team_spending_limit = TeamSpendingModel::applyTeamKey($selected_team)->applyMonth($selected_month)->applyYear($selected_year)->sum('limit');
        $team_spending_percentage = $team_spending_limit != 0 ? ($team_spending_amount / $team_spending_limit) * 100 : $team_spending_amount;

        /**==========================================================================================================**/

        $gross_amount = Payment::applyTeamKey($selected_team)->applyMonth($selected_month)->applyYear($selected_year)->sum('amount');
        $gross_sales = $target_amount != 0 ? ($gross_amount / $target_amount) * 100 : $gross_amount;

        /**==========================================================================================================**/

        $charge_back_refund = Payment::applyTeamKey($selected_team)->applyPaymentStatusProcessTime($selected_month, $selected_year)->whereIn('payment_status', [2, 3])->sum('amount');

        $average_charge_back_refund_gross_sales = $gross_amount != 0 && $charge_back_refund != 0 ? ($charge_back_refund / $gross_amount) * 100 : 0;

        /**==========================================================================================================**/

        $charge_back = Payment::applyTeamKey($selected_team)->applyPaymentStatusProcessTime($selected_month, $selected_year)->where('payment_status', 3)->sum('amount');

        $average_charge_back_gross_sales = $gross_amount != 0 && $charge_back != 0 ? ($charge_back / $gross_amount) * 100 : 0;

        /**==========================================================================================================**/

        $refund = Payment::applyTeamKey($selected_team)->applyPaymentStatusProcessTime($selected_month, $selected_year)->where('payment_status', 2)->sum('amount');

        $average_refund_gross_sales = $gross_amount != 0 && $refund != 0 ? ($refund / $gross_amount) * 100 : 0;

        /**==========================================================================================================**/

        $net_sales = $gross_amount - $charge_back_refund;

        $net_sales_percentage = $net_sales != 0 && $target_amount != 0 ? ($net_sales / $target_amount) * 100 : 0;

        /**==========================================================================================================**/

        $roas_target_exp = $target_amount != 0 && $team_spending_limit != 0 ? ($target_amount / $team_spending_limit) * 100 : 0;
        $roas = $net_sales != 0 && $team_spending_amount != 0 ? ($net_sales / $team_spending_amount) * 100 : 0;
        $roas_percentage = $roas_target_exp != 0 && $roas != 0 ? ($roas / $roas_target_exp) * 100 : 0;

        /**==========================================================================================================**/

        $carry_forward_amount = TeamCarryForwardModel::applyTeamKey($selected_team)->applyMonth($selected_month)->applyYear($selected_year)->sum('amount');

        /**==========================================================================================================**/

        $fixed_costing_amount = TeamFixedCostingModel::applyTeamKey($selected_team)->applyMonth($selected_month)->applyYear($selected_year)->sum('amount');

        /**==========================================================================================================**/

//        $indirect_costing_amount = TeamIndirectCostingModel::applyTeamKey($selected_team)->applyMonth($selected_month)->applyYear($selected_year)->sum('amount');

        /**==========================================================================================================**/

        $wire_payment_amount = WirePaymentModel::applyTeamKey($selected_team)->applyCreatedAt($selected_month, $selected_year)->where('payment_approval','Approved')->sum('amount');

        /**==========================================================================================================**/

        $start_date = Carbon::create($selected_year, $selected_month_num, 1)->startOfMonth();
        $end_date = $start_date->copy()->endOfMonth();

        $total_working_days = $start_date->diffInDaysFiltered(function (Carbon $date) {
            return !$date->isWeekend();
        }, $end_date);
        if ($selected_month_num === $current_month) {
            $remaining_working_days = $start_date->copy()->addDays(Carbon::now()->day)->diffInDaysFiltered(function (Carbon $date) {
                return !$date->isWeekend();
            }, $end_date);
        } elseif ($selected_month_num < $current_month) {
            $remaining_working_days = 0;
        } else {
            $remaining_working_days = $total_working_days;
        }

        /**==========================================================================================================**/

        $current_day_amount = Payment::applyTeamKey($selected_team)->applyDate(Carbon::now()->day)->applyMonth($selected_month)->applyYear($selected_year)->sum('amount');
        $current_day_avg = $remaining_working_days != 0 && $total_working_days != 0 && $total_working_days - $remaining_working_days != 0 ? ($net_sales / ($total_working_days - $remaining_working_days)) : 0;
        $current_day_percentage = $current_day_avg != 0 && $remaining_working_days != 0 && $total_working_days != 0 && $total_working_days - $remaining_working_days != 0 ? ($current_day_amount / $current_day_avg) * 100 : 0;

        /**==========================================================================================================**/

        $req_sales_per_day = $target_amount != 0 && $net_sales != 0 && $remaining_working_days != 0 ? ($target_amount - $net_sales / $remaining_working_days) : 0;

        $percentage_achieved = $current_day_avg != 0 ? ($req_sales_per_day / $current_day_avg) * 100 : 0;

        /**==========================================================================================================**/
        $third_party_payments = ThirdPartyRoleModel::applyTeamKey($selected_team)->applyMonth($selected_month)->applyYear($selected_year)->get()->sum('amount');

        $third_party_payments_percentage = $net_sales != 0 ? ($third_party_payments / $net_sales) * 100 : $third_party_payments;

        /**==========================================================================================================**/
        if (isset($gross_amount, $charge_back_refund, $team_spending_amount, $fixed_costing_amount, $third_party_payments, $wire_payment_amount)) {
            $dm_current_profit = ($gross_amount - ($gross_amount * 0.16) - $charge_back_refund - $team_spending_amount - $fixed_costing_amount - $third_party_payments) + $wire_payment_amount;
            $current_profit = abs($dm_current_profit);
            $current_is_profit = $dm_current_profit >= 0;
//        $current_profit_percentage = $current_is_profit ? number_format($dm_current_profit, 2, '.', '') : number_format(abs($dm_current_profit), 2, '.', '');
            $current_profit_percentage = $current_profit != 0 && $target_amount != 0 ? ($current_profit / $target_amount) * 100 : 0;

        } else {
            $current_profit = 0;
            $current_is_profit = 0;
            $current_profit_percentage = 0;
        }
        /**==========================================================================================================**/
        if (isset($gross_amount, $charge_back_refund, $team_spending_amount, $fixed_costing_amount, $carry_forward_amount, $third_party_payments, $wire_payment_amount)) {
            $dm_over_all_profit = ($gross_amount - ($gross_amount * 0.16) - $charge_back_refund - $team_spending_amount - $carry_forward_amount - $fixed_costing_amount - $third_party_payments) + $wire_payment_amount;
            $over_all_profit = abs($dm_over_all_profit);
            $over_all_is_profit = $dm_over_all_profit >= 0;
//        $over_all_profit_percentage = $over_all_is_profit ? number_format($dm_over_all_profit, 2, '.', '') : number_format(abs($dm_over_all_profit), 2, '.', '');
            $over_all_profit_percentage = $over_all_profit != 0 && $target_amount != 0 ? ($over_all_profit / $target_amount) * 100 : 0;
        } else {
            $over_all_profit = 0;
            $over_all_is_profit = 0;
            $over_all_profit_percentage = 0;
        }
        /**==========================================================================================================**/

        if (isset($charge_back_refund, $team_spending_amount, $fixed_costing_amount, $third_party_payments, $wire_payment_amount)) {
            $dm_remaining_be = ($charge_back_refund + $team_spending_amount + $fixed_costing_amount + $third_party_payments - $wire_payment_amount +
                    (($charge_back_refund + $team_spending_amount + $fixed_costing_amount + $third_party_payments - $wire_payment_amount) / 0.84 * 0.16)) - $gross_amount;
            $remaining_be = abs($dm_remaining_be);
            $remaining_be_is_profit = $dm_remaining_be >= 0;
            $remaining_be_percentage = $remaining_be != 0 && $target_amount != 0 ? ($remaining_be / $target_amount) * 100 : 0;

        } else {
            $remaining_be = 0;
            $remaining_be_is_profit = 0;
            $remaining_be_percentage = 0;
        }

        /**==========================================================================================================**/

        $fresh_accounts = Invoice::applyTeamKey($selected_team)->applyMonth($selected_month)->applyYear($selected_year)->where('sales_type', 'Fresh')->where('status', 'paid')
            ->whereHas('getClient', function ($query) use ($selected_month, $selected_year) {
                $query->applyMonth($selected_month)->applyYear($selected_year);
            })->distinct('clientid')
            ->count('clientid');
        $account_target = TeamSpendingModel::applyTeamKey($selected_team)->applyMonth($selected_month)->applyYear($selected_year)->sum('accounts');

        $fresh_accounts_percentage = $fresh_accounts != 0 && $account_target != 0 ? ($fresh_accounts / $account_target) * 100 : 0;
        /**==========================================================================================================**/

        $projected_net_sales = $total_working_days != 0 && $current_day_avg != 0 ? $total_working_days * $current_day_avg : 0;

        /**==========================================================================================================**/

        $chart_data = [];
        for ($year = 2021; $year <= $current_year; $year++) {
            foreach (config('app.months') as $month) {
                $chart_data['labels'][] = "$month $year";
                if (Carbon::now()->startOfMonth()->gte(Carbon::create($year, Carbon::parse($month)->month, 1))) {
                    $dm_gross_amount = Payment::applyTeamKey($selected_team)->applyMonth($month)->applyYear($year)->sum('amount');
                    $chart_data['gross_sales'][] = number_format($dm_gross_amount, 2, '.', '');
                    $dm_charge_back_refund = Payment::applyTeamKey($selected_team)->applyMonth($month)->applyYear($year)->whereIn('payment_status', [2, 3])->sum('amount');
                    $dm_net_sales = $dm_gross_amount - $dm_charge_back_refund;
                    $chart_data['net_sales'][] = $dm_net_sales ? number_format($dm_net_sales, 2, '.', '') : '0.00';
                    $dm_spending = TeamSpendingModel::applyTeamKey($selected_team)->applyMonth($month)->applyYear($year)->sum('amount');
                    $chart_data['spending'][] = $dm_spending ? number_format($dm_spending, 2, '.', '') : '0.00';
                    $dm_charge_back_refund = Payment::applyTeamKey($selected_team)->applyMonth($month)->applyYear($year)->whereIn('payment_status', [2, 3])->sum('amount');
                    $chart_data['charge_back_refund'][] = $dm_charge_back_refund ? number_format($dm_charge_back_refund, 2, '.', '') : '0.00';
                } else {
                    $chart_data['net_sales'][] = '0.00';
                    $chart_data['spending'][] = '0.00';
                    $chart_data['charge_back_refund'][] = '0.00';
                    $chart_data['gross_sales'][] = '0.00';
                }
            }
        }
        /**==========================================================================================================**/

        return view('admin.stats.dashboard', compact(
            'teams',
            'selected_month',
            'gross_amount',
            'gross_sales',
            'charge_back_refund','average_charge_back_refund_gross_sales',
            'charge_back','average_charge_back_gross_sales',
            'refund','average_refund_gross_sales',
            'net_sales', 'net_sales_percentage',
            'target_amount',
            'team_spending_amount', 'team_spending_limit', 'team_spending_percentage',
            'carry_forward_amount',
            'fixed_costing_amount',
            'roas_target_exp', 'roas', 'roas_percentage',
            'total_working_days',
            'remaining_working_days',
            'current_day_amount', 'current_day_avg', 'current_day_percentage',
            'req_sales_per_day', 'percentage_achieved',
            'third_party_payments',
            'third_party_payments_percentage',
            'current_profit', 'current_is_profit', 'current_profit_percentage',
            'over_all_profit', 'over_all_is_profit', 'over_all_profit_percentage',
            'fresh_accounts', 'account_target', 'fresh_accounts_percentage',
            'projected_net_sales',
            'remaining_be_is_profit','remaining_be','remaining_be_percentage',
            /** CHART - DATA */
            'chart_data'
        ));
    }

    public function index()
    {
        //sendEmail();

        //Leads Status
        $leadstatus = LeadStatus::all();
        $statusColor = $statusValue = [];
        $leadData = [];
        $i = 1;

        foreach ($leadstatus as $status) {
            $leads = Lead::where('status', $status->id)->count();
            $statusValue[] = ['data' . $i, $leads];
            $leadData['data' . $i] = $status->status;
            $statusColor['data' . $i] = "Aero.colors['" . $status->leadstatus_color . "']";
            $i++;
        }
        //--------end---------------

        /** =================================== Current year month wise data ========================================== */
        $yearMonthWise = Payment::select(DB::raw("(SUM(amount)) as amount"), DB::raw("MONTHNAME(created_at) as monthname"))
            ->where('payment_status', 1)
            ->whereYear('created_at', now()->year)
            ->groupBy('monthname')
            ->get();
        $month = $m2 = [];
        $monthAmount = array('data1');

        $monthData = $yearMonthWise->pluck('amount', 'monthname');

        foreach (config('app.months') as $monthName) {
            $m2[$monthName] = [$monthName, $monthData->get($monthName, 0)];
        }
        /** =================================== End Current year month wise data ========================================== */

        /** =================================== Current year month wise Expense data ========================================== */
        $yearExpMonthWise = Expense::select(DB::raw("(SUM(amount)) as amount"), DB::raw("MONTHNAME(created_at) as monthname"))
            ->where('status', 1)
            ->whereYear('created_at', now()->year)
            ->groupBy('monthname')
            ->get();

        $expMonthData = $yearExpMonthWise->pluck('amount', 'monthname');

        $yearExpMonthWiseData = [];
        foreach (config('app.months') as $monthName) {
            $yearExpMonthWiseData[$monthName] = [$monthName, $expMonthData->get($monthName, 0)];
        }
        /** =================================== End Current year month wise Expense data ========================================== */


        /** =================================== Brand Payments ========================================== */
        $brands = Brand::where('status', 1)->get();
        $brandData = [];
        foreach ($brands as $brand) {

            /** Brand Current Month Income */
            $brand['amount'] = $brand->CurrentMonthAmount;

            /** Brand Last Month Income */
            $brand['lastmonthamount'] = $brand->LastMonthAmount;

            /** Brand Current Month Spendings */
            $brand['spending'] = $brand->CurrentMonthSpending;
            $brand['total_spending'] = $brand->TotalSpending;

            $brandData[] = $brand;
        }
        /** =================================== End Brand Payments ========================================== */

        //Recent Payments
        $paymentData = [];
        $payments = Payment::where('payment_status', 1)
            ->orderBy('id', 'desc')->limit(5)->get();

        foreach ($payments as $payment) {
            $teamKey = $payment->team_key;
            $brandKey = $payment->brand_key;

            $team_name = Team::where('team_key', $teamKey)->value('name');
            $payment['teamName'] = $team_name;

            $brand_name = Brand::where('brand_key', $brandKey)->value('name');
            $payment['brandName'] = $brand_name;

            array_push($paymentData, $payment);
        }
        //--------end---------------


        //Current Month Team Income
        $teams = Team::where('status', 1)->get(); //get all brands
        $teamData = [];
        foreach ($teams as $team) {

            $teamAmount = Payment::where(['team_key' => $team->team_key, 'payment_status' => 1])
//                ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth(),])
                ->whereMonth('created_at', Carbon::now()->month)
                ->whereYear('created_at', Carbon::now()->year)
                ->sum('amount');

            settype($teamAmount, "integer");
            $team['amount'] = thousand_format($teamAmount);

            //last month income
            $lastMonthRevenue = Payment::select('*')
                ->where(['team_key' => $team->team_key, 'payment_status' => 1])
                ->whereMonth('created_at', Carbon::now()->subMonth()->month)
                ->whereYear('created_at', Carbon::now()->year)
                ->sum('amount');

            settype($lastMonthRevenue, "integer");

            $team['lastmonthamount'] = thousand_format($lastMonthRevenue);

            array_push($teamData, $team);
        }
        //--------end---------------

        /** =================================== Team Refund Year Month Wise Data ========================================== */
        $refundYearMonthWise = Refund::select(DB::raw("(SUM(amount)) as amount"), DB::raw("MONTHNAME(created_at) as monthname"))
            ->where(['qa_approval' => 1, 'type' => 'refund'])
            ->whereYear('created_at', date('Y'))
            ->groupBy('monthname')
            ->get();


        $refundYearMonthWiseData = [];
        $refundMonthData = $refundYearMonthWise->pluck('amount', 'monthname');
        foreach (config('app.months') as $monthName) {
            $refundYearMonthWiseData[$monthName] = [$monthName, $refundMonthData->get($monthName, 0)];
        }
        /** =================================== End Team Refund Year Month Wise Data ========================================== */

        /** =================================== Charge Back Year Month Wise Data ========================================== */
        $chargebackYearMonthWise = Refund::select(DB::raw("(SUM(amount)) as amount"), DB::raw("MONTHNAME(created_at) as monthname"))
            ->where(['qa_approval' => 1, 'type' => 'chargeback'])
            ->whereYear('created_at', date('Y'))
            ->groupBy('monthname')
            ->get();

        $chargebackYearMonthWiseData = [];
        $chargebackMonthData = $chargebackYearMonthWise->pluck('amount', 'monthname');

        foreach (config('app.months') as $monthName) {
            $chargebackYearMonthWiseData[$monthName] = [$monthName, $chargebackMonthData->get($monthName, 0)];
        }
        /** =================================== End Charge Back Year Month Wise Data ========================================== */

        //Team Project Status - Project Count
        $project_status = ProjectStatus::all();
        $project_status_data = [];
        foreach ($project_status as $status) {
            $project_count = Project::where('project_status', $status->id)->count();
            $status['count'] = $project_count;
            array_push($project_status_data, $status);
        }

        //Team Project Category - Project Count
        $project_categories = ProjectCategory::all();
        $project_categories_data = [];
        foreach ($project_categories as $category) {
            $project_count = Project::where('category_id', $category->id)->count();
            $category['count'] = $project_count;
            array_push($project_categories_data, $category);
        }

        //Recent Payments
        $projectData = [];
        $projects = Project::orderBy('id', 'desc')->limit(5)->get();

        foreach ($projects as $yy) {
            $cName = Client::where('id', $yy->id)->value('name');
            $yy['clientName'] = $cName;

            $brand = Brand::where('brand_key', $yy->brand_key)->value('name');
            $yy['brandName'] = $brand;

            $agent = $yy->getAgent ?? User::where('id', $yy->agent_id)->withTrashed()->first();
            $yy['agentName'] = $agent->name ?? "";
            $yy['agentDesignation'] = $agent->designation ?? "";
            $yy['agentImage'] = $agent->image ?? "";

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

            array_push($projectData, $yy);
        }

        /** =================================== Current Week Data ========================================== */
        //current Week Data
        $weekDayWise = Payment::select(DB::raw("(SUM(amount)) as amount"), DB::raw("DAYNAME(created_at) as dayname"))
            ->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
            ->where('payment_status', 1)
            ->whereYear('created_at', date('Y'))
            ->groupBy('dayname')
            ->get();

        $weekDaysWiseData = [];
        $weekDayData = $weekDayWise->pluck('amount', 'dayname');
        foreach (config('app.days') as $dayName) {
            $weekDaysWiseData[$dayName] = [$dayName, $weekDayData->get($dayName, 0)];
        }
        /** =================================== End Current Week Data ========================================== */

        //merchant payment data
        $payment_methods = [];
        // 5 id developer testing
        $payment_method_data = PaymentMethod::where('id', '!=', 5)->get();

        foreach ($payment_method_data as $payment) {
            $merchant_monthPayment = Payment::where(['payment_status' => 1, 'merchant_id' => $payment->id])
                ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth(),])
                ->sum('amount');
            //set Type
            settype($merchant_monthPayment, "integer");
            $payment['paymentMonth'] = $merchant_monthPayment;
            array_push($payment_methods, $payment);
        }

        $data = [

            /** ============================= Top 4 Cards ========================= */
            "monthPayment" => thousand_format((int)Payment::monthSuccessPayments()->sum('amount')),
            "todayPayment" => thousand_format((int)Payment::todaySuccessPayments()->sum('amount')),
            "dueInvoicePayment" => thousand_format((int)Payment::freshSuccessPayments()->sum('amount')),
            "overdueInvoicePayment" => thousand_format((int)Payment::upsaleSuccessPayments()->sum('amount')),
            /** ============================= End Top 4 Cards ========================= */

            "leadstatus" => json_encode($leadData),
            "statusValue" => json_encode($statusValue),
            "statusColor" => json_encode($statusColor),
            "monthWise" => json_encode($month),
            "monthWiseAmount" => json_encode($monthAmount),
            "brandData" => $brandData,
            "teamData" => $teamData,
            "recentPayments" => $paymentData,

            /** ============================= Top 4 Cards ========================= */
            "monthSpending" => thousand_format((int)Spending::monthlySpending()->sum('amount')),
            "yearSpending" => thousand_format((int)Spending::yearlySpending()->sum('amount')),
            "googleSpending" => thousand_format((int)Spending::platformSpending('Google')->sum('amount')),
            "bingSpending" => thousand_format((int)Spending::platformSpending('Bing')->sum('amount')),
            /** ============================= End Top 4 Cards ========================= */

            /** ============================= Sales Report ========================= */
            "yearlyIncome" => thousand_format((int)Payment::yearlySuccessIncome()->sum('amount')),
            "yearRefund" => thousand_format((int)Refund::yearlyRefund()->sum('amount')),
            "yearchargeback" => thousand_format((int)Refund::yearlyChargeback()->sum('amount')),
            "yearExpence" => thousand_format((int)Expense::yearlyExpense()->sum('amount')),
            /** ============================= End Sales Report ========================= */

            /** ============================= Week & Month Wise Data ========================= */
            "m2" => $m2,
            "yearExpMonthWiseData" => $yearExpMonthWiseData,
            "refundYearMonthWiseData" => $refundYearMonthWiseData,
            "chargebackYearMonthWiseData" => $chargebackYearMonthWiseData,
            'weekDaysWise' => $weekDaysWiseData,
            /** ============================= End Week & Month Wise Data ========================= */

            'projectStatus' => $project_status_data,
            'projectcategoriesdata' => $project_categories_data,
            'recentProject' => $projectData,

        ];

        return view('admin.dashboard', compact('data'));
    }

    public function view_profile()
    {
        return view('admin.profile');
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
        if (!Hash::check($request->password, Auth::guard('admin')->user()->password)) {
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
//            'name' => 'required',
//            'pseudo_email' => 'required',
//            'phone' => 'required',
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }
        try {
            $admin = Auth::guard('admin')->user();
            if (!$admin) {
                return response()->json([
                    'error' => 'User not found',
                    'message' => 'Authenticated user not found'
                ], 404);
            }
            if ($request->has('name')) {
                $admin->name = $request->input('name');
            }
            if ($request->has('pseudo_email')) {
                $admin->pseudo_email = $request->input('pseudo_email');
            }
            if ($request->has('phone')) {
                $admin->phone = $request->input('phone');
            }
            $admin->save();

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
    public function password_update(Request $request)
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
        $admin = auth()->guard('admin')->user();
        if (!Hash::check($request->old_password, $admin->password)) {
            return response()->json([
                'errors' => ['old_password' => ['The provided old password is incorrect.']],
            ], 422);
        }
        $admin->update(['password' => Hash::make($request->password),]);

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
            $image->move(public_path('assets/images/profile_images/admin'), $imageName);
            auth()->guard('admin')->user()->update(['image' => $imageName]);
            return response()->json([
                'success' => true,
            ]);
        }
        return response()->json([
            'error' => false,
        ]);
    }

    /** Old Dashboard Index Function*/
//    public function index(){
//
//        //sendEmail();
//        $now = \Carbon\Carbon::now();
//
//    // ============================= Top 4 Cards =========================
//        //Month Payment
//        $monthPayment = Payment::where('payment_status',1)
//        ->whereMonth('created_at', Carbon::now()->month)
//        ->sum('amount');
//        //set Type
//        settype($monthPayment, "integer");
//
//        //Today Payment
//        $todayPayment = Payment::where('payment_status',1)
//        ->whereDate('created_at', Carbon::today())
//        ->sum('amount');
//        settype($todayPayment, "integer");
//
//        //Fresh Payment
//        $dueInvoicePayment = Payment::where(['payment_status' => 1, 'sales_type'=>'Fresh'])
//        ->whereMonth('created_at', Carbon::now()->month)
//        ->sum('amount');
//        settype($dueInvoicePayment, "integer");
//
//        //upsale Payment
//        $overdueInvoicePayment = Payment::where(['payment_status' => 1, 'sales_type'=>'Upsale'])
//        ->whereMonth('created_at', Carbon::now()->month)
//        ->sum('amount');
//        settype($overdueInvoicePayment, "integer");
//
//    // ============================= End Top 4 Cards =========================
//
//
//    // ============================= Sales Report  =========================
//
//        //Current Year Income
//         $yearIncome = Payment::where('payment_status',1)
//         ->whereYear('created_at', $now->format('Y'))
//         ->sum('amount');
//         settype($yearIncome, "integer");
//        //--------end---------------
//
//        $yearExpence = Expense::where('status',1)
//        ->whereYear('created_at', $now->format('Y'))
//        ->sum('amount');
//        settype($yearExpence, "integer");
//
//        //--------end---------------
//
//        //Current Year Refund
//        $yearRefund = Refund::where(['type'=>'refund','qa_approval'=>1])
//        ->whereYear('created_at', $now->format('Y'))
//        ->sum('amount');
//         settype($yearRefund, "integer");
//       //--------end---------------
//
//       //Current Year Charge back
//       $yearchargeback = Refund::where(['type'=>'chargeback', 'qa_approval'=>1])
//       ->whereYear('created_at', $now->format('Y'))
//       ->sum('amount');
//        settype($yearchargeback, "integer");
//      //--------end---------------
//
//      // ============================= End Sales Report =========================
//
//      // ======================Spending Data ========================
//
//      //Current Year Spending
//        $yearSpending = Spending::whereYear('created_at', $now->format('Y'))
//        ->sum('amount');
//        settype($yearSpending, "integer");
//      //--------end---------------
//
//      //total Spendings
//      $monthSpending = Spending::whereMonth('created_at', Carbon::now()->month)
//        ->sum('amount');
//      settype($monthSpending, "integer");
//
//
//      //Google Spending
//      $googleSpending = Spending::where('platform','Google')
//        ->whereMonth('created_at', Carbon::now()->month)
//        ->sum('amount');
//        settype($googleSpending, "integer");
//
//       //Bing Spending
//      $bingSpending = Spending::where('platform','Bing')
//      ->whereMonth('created_at', Carbon::now()->month)
//      ->sum('amount');
//      settype($bingSpending, "integer");
//
//       // ======================End Spending Data ==================
//
//
//
//        //Leads Status
//        $leadstatus = LeadStatus::all();
//        $leadData = array();
//        $statusValue = array();
//        $statusColor = array();
//        $i = 1;
//
//        foreach($leadstatus as $status){
//           $leads = Lead::where('status',$status->id)->count();
//           $statusName = array('data'.$i => $status->status);
//
//           $value = array('data'.$i , $leads);
//           array_push($statusValue,$value);
//           $leadData['data'.$i] = $status->status;
//           $statusColor['data'.$i] = "Aero.colors['".$status->leadstatus_color."']";
//           $i++;
//        }
//        //--------end---------------
//
//        //Current year month wise data
//        $yearMonthWise = Payment::select(DB::raw("(SUM(amount)) as amount"),DB::raw("MONTHNAME(created_at) as monthname"))
//        ->where('payment_status',1)
//        ->whereYear('created_at', date('Y'))
//        ->groupBy('monthname')
//        ->get();
//
//        $month = array();
//        $monthAmount = array('data1');
//        $m2 = array();
//
//        $categories = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October','November','December'];
//
//        foreach($yearMonthWise as $key => $monthWise){
//
//            if('January' == $monthWise->monthname){
//              $m2['January']=[$monthWise->monthname,$monthWise->amount];
//            }
//            if('February' == $monthWise->monthname){
//              $m2['February']=[$monthWise->monthname,$monthWise->amount];
//            }
//            if('March' == $monthWise->monthname){
//              $m2['March']=[$monthWise->monthname,$monthWise->amount];
//            }
//            if('April' == $monthWise->monthname){
//              $m2['April']=[$monthWise->monthname,$monthWise->amount];
//            }
//            if('May' == $monthWise->monthname){
//              $m2['May']=[$monthWise->monthname,$monthWise->amount];
//            }
//            if('June' == $monthWise->monthname){
//              $m2['June']=[$monthWise->monthname,$monthWise->amount];
//            }
//            if('July' == $monthWise->monthname){
//              $m2['July']=[$monthWise->monthname,$monthWise->amount];
//            }
//            if('August' == $monthWise->monthname){
//              $m2['August']=[$monthWise->monthname,$monthWise->amount];
//            }
//            if('September' == $monthWise->monthname){
//              $m2['September']=[$monthWise->monthname,$monthWise->amount];
//            }
//            if('October' == $monthWise->monthname){
//              $m2['October']=[$monthWise->monthname,$monthWise->amount];
//            }
//            if('November' == $monthWise->monthname){
//              $m2['November']=[$monthWise->monthname,$monthWise->amount];
//            }
//            if('December' == $monthWise->monthname){
//              $m2['December']=[$monthWise->monthname,$monthWise->amount];
//            }
//        }
//
//        //Current year month wise Expence data
//        $yearExpMonthWise = Expense::select(DB::raw("(SUM(amount)) as amount"),DB::raw("MONTHNAME(created_at) as monthname"))
//        ->where('status',1)
//        ->whereYear('created_at', date('Y'))
//        ->groupBy('monthname')
//        ->get();
//
//        $yearExpMonthWiseData = array();
//
//        foreach($yearExpMonthWise as $key1 => $monthWise1){
//
//          if('January' == $monthWise1->monthname){
//            $yearExpMonthWiseData['January']=[$monthWise1->monthname,$monthWise1->amount];
//          }
//          if('February' == $monthWise1->monthname){
//            $yearExpMonthWiseData['February']=[$monthWise1->monthname,$monthWise1->amount];
//          }
//          if('March' == $monthWise1->monthname){
//            $yearExpMonthWiseData['March']=[$monthWise1->monthname,$monthWise1->amount];
//          }
//          if('April' == $monthWise1->monthname){
//            $yearExpMonthWiseData['April']=[$monthWise1->monthname,$monthWise1->amount];
//          }
//          if('May' == $monthWise1->monthname){
//            $yearExpMonthWiseData['May']=[$monthWise1->monthname,$monthWise1->amount];
//          }
//          if('June' == $monthWise1->monthname){
//            $yearExpMonthWiseData['June']=[$monthWise1->monthname,$monthWise1->amount];
//          }
//          if('July' == $monthWise1->monthname){
//            $yearExpMonthWiseData['July']=[$monthWise1->monthname,$monthWise1->amount];
//          }
//          if('August' == $monthWise1->monthname){
//            $yearExpMonthWiseData['August']=[$monthWise1->monthname,$monthWise1->amount];
//          }
//          if('September' == $monthWise1->monthname){
//            $yearExpMonthWiseData['September']=[$monthWise1->monthname,$monthWise1->amount];
//          }
//          if('October' == $monthWise1->monthname){
//            $yearExpMonthWiseData['October']=[$monthWise1->monthname,$monthWise1->amount];
//          }
//          if('November' == $monthWise1->monthname){
//            $yearExpMonthWiseData['November']=[$monthWise1->monthname,$monthWise1->amount];
//          }
//          if('December' == $monthWise1->monthname){
//            $yearExpMonthWiseData['December']=[$monthWise1->monthname,$monthWise1->amount];
//          }
//
//
//      }
//
//      //--------end---------------
//
//      //Current Month Brand Income
//        $brands = Brand::where('status',1)->get(); //get all brands
//        $brandData = array();
//        $brandSpendingsData = array();
//
//        foreach($brands as $brand){
//
//            $brandAmount = Payment::where(['brand_key' => $brand->brand_key, 'payment_status' => 1])
//            ->whereMonth('created_at', Carbon::now()->month)
//            ->sum('amount');
//
//            settype($brandAmount, "integer");
//            $brand['amount'] = thousand_format($brandAmount);
//
//            //last month income
//            $lastMonthRevenue = Payment::select('*')
//            ->where(['brand_key' => $brand->brand_key, 'payment_status' => 1])
//            ->whereMonth('created_at', Carbon::now()->subMonth()->month)
//            ->sum('amount');
//
//            settype($lastMonthRevenue, "integer");
//
//            $brand['lastmonthamount'] = thousand_format($lastMonthRevenue);
//
//            $brandSpending = Spending::where('brand_key' , $brand->brand_key)
//            ->whereMonth('created_at', Carbon::now()->month)
//            ->sum('amount');
//
//            $brand['spending'] = thousand_format($brandSpending);
//
//            array_push($brandData,$brand);
//        }
//      //--------end---------------
//
//
//      //Recent Payments
//        $paymentData = array();
//        $payments = Payment::where('payment_status',1)
//                    ->orderBy('id','desc')->limit(5)->get();
//
//        foreach($payments as $payment){
//            $teamKey   = $payment->team_key;
//            $brandKey  = $payment->brand_key;
//
//            $team_name = Team::where('team_key', $teamKey)->value('name');
//            $payment['teamName'] = $team_name;
//
//            $brand_name = Brand::where('brand_key', $brandKey)->value('name');
//            $payment['brandName'] = $brand_name;
//
//            array_push($paymentData,$payment);
//        }
//      //--------end---------------
//
//
//        //Current Month Team Income
//        $teams = Team::where('status',1)->get(); //get all brands
//        $teamData = array();
//        foreach($teams as $team){
//
//            $teamAmount = Payment::where(['team_key' => $team->team_key, 'payment_status' => 1])
//            ->whereMonth('created_at', Carbon::now()->month)
//            ->sum('amount');
//
//            settype($teamAmount, "integer");
//            $team['amount'] = thousand_format($teamAmount);
//
//            //last month income
//            $lastMonthRevenue = Payment::select('*')
//            ->where(['team_key' => $team->team_key, 'payment_status' => 1])
//            ->whereMonth('created_at', Carbon::now()->subMonth()->month)
//            ->sum('amount');
//
//            settype($lastMonthRevenue, "integer");
//
//            $team['lastmonthamount'] = thousand_format($lastMonthRevenue);
//
//            array_push($teamData,$team);
//        }
//        //--------end---------------
//
//        // Team Refund & Charge Back
//        // year month wise data
//        $refundYearMonthWise = Refund::select(DB::raw("(SUM(amount)) as amount"),DB::raw("MONTHNAME(created_at) as monthname"))
//        ->where(['qa_approval'=>1, 'type'=>'refund'])
//        ->whereYear('created_at', date('Y'))
//        ->groupBy('monthname')
//        ->get();
//
//
//        $refundYearMonthWiseData = array();
//
//        foreach($refundYearMonthWise as $key1 => $monthWise1){
//
//            if('January' == $monthWise1->monthname){
//              $refundYearMonthWiseData['January']=[$monthWise1->monthname,$monthWise1->amount];
//            }
//            if('February' == $monthWise1->monthname){
//              $refundYearMonthWiseData['February']=[$monthWise1->monthname,$monthWise1->amount];
//            }
//            if('March' == $monthWise1->monthname){
//              $refundYearMonthWiseData['March']=[$monthWise1->monthname,$monthWise1->amount];
//            }
//            if('April' == $monthWise1->monthname){
//              $refundYearMonthWiseData['April']=[$monthWise1->monthname,$monthWise1->amount];
//            }
//            if('May' == $monthWise1->monthname){
//              $refundYearMonthWiseData['May']=[$monthWise1->monthname,$monthWise1->amount];
//            }
//            if('June' == $monthWise1->monthname){
//              $refundYearMonthWiseData['June']=[$monthWise1->monthname,$monthWise1->amount];
//            }
//            if('July' == $monthWise1->monthname){
//              $refundYearMonthWiseData['July']=[$monthWise1->monthname,$monthWise1->amount];
//            }
//            if('August' == $monthWise1->monthname){
//              $refundYearMonthWiseData['August']=[$monthWise1->monthname,$monthWise1->amount];
//            }
//            if('September' == $monthWise1->monthname){
//              $refundYearMonthWiseData['September']=[$monthWise1->monthname,$monthWise1->amount];
//            }
//            if('October' == $monthWise1->monthname){
//              $refundYearMonthWiseData['October']=[$monthWise1->monthname,$monthWise1->amount];
//            }
//            if('November' == $monthWise1->monthname){
//              $refundYearMonthWiseData['November']=[$monthWise1->monthname,$monthWise1->amount];
//            }
//            if('December' == $monthWise1->monthname){
//              $refundYearMonthWiseData['December']=[$monthWise1->monthname,$monthWise1->amount];
//            }
//        }
//
//
//        $chargebackYearMonthWise = Refund::select(DB::raw("(SUM(amount)) as amount"),DB::raw("MONTHNAME(created_at) as monthname"))
//        ->where(['qa_approval'=>1, 'type'=>'chargeback'])
//        ->whereYear('created_at', date('Y'))
//        ->groupBy('monthname')
//        ->get();
//
//        $chargebackYearMonthWiseData = array();
//
//        foreach($chargebackYearMonthWise as $key1 => $monthWise1){
//
//            if('January' == $monthWise1->monthname){
//              $chargebackYearMonthWiseData['January']=[$monthWise1->monthname,$monthWise1->amount];
//            }
//            if('February' == $monthWise1->monthname){
//              $chargebackYearMonthWiseData['February']=[$monthWise1->monthname,$monthWise1->amount];
//            }
//            if('March' == $monthWise1->monthname){
//              $chargebackYearMonthWiseData['March']=[$monthWise1->monthname,$monthWise1->amount];
//            }
//            if('April' == $monthWise1->monthname){
//              $chargebackYearMonthWiseData['April']=[$monthWise1->monthname,$monthWise1->amount];
//            }
//            if('May' == $monthWise1->monthname){
//              $chargebackYearMonthWiseData['May']=[$monthWise1->monthname,$monthWise1->amount];
//            }
//            if('June' == $monthWise1->monthname){
//              $chargebackYearMonthWiseData['June']=[$monthWise1->monthname,$monthWise1->amount];
//            }
//            if('July' == $monthWise1->monthname){
//              $chargebackYearMonthWiseData['July']=[$monthWise1->monthname,$monthWise1->amount];
//            }
//            if('August' == $monthWise1->monthname){
//              $chargebackYearMonthWiseData['August']=[$monthWise1->monthname,$monthWise1->amount];
//            }
//            if('September' == $monthWise1->monthname){
//              $chargebackYearMonthWiseData['September']=[$monthWise1->monthname,$monthWise1->amount];
//            }
//            if('October' == $monthWise1->monthname){
//              $chargebackYearMonthWiseData['October']=[$monthWise1->monthname,$monthWise1->amount];
//            }
//            if('November' == $monthWise1->monthname){
//              $chargebackYearMonthWiseData['November']=[$monthWise1->monthname,$monthWise1->amount];
//            }
//            if('December' == $monthWise1->monthname){
//              $chargebackYearMonthWiseData['December']=[$monthWise1->monthname,$monthWise1->amount];
//            }
//        }
//
//        //Team Project Status - Project Count
//        $project_status =  ProjectStatus::all();
//        $project_status_data = array();
//        foreach($project_status as $status){
//            $project_count = Project::where('project_status' , $status->id)->count();
//            $status['count'] = $project_count;
//            array_push($project_status_data,$status);
//        }
//
//        //Team Project Category - Project Count
//        $project_categories =  ProjectCategory::all();
//        $project_categories_data = array();
//        foreach($project_categories as $category){
//            $project_count = Project::where('category_id' , $category->id)->count();
//            $category['count'] = $project_count;
//            array_push($project_categories_data,$category);
//        }
//
//        //Recent Payments
//        $projectData = array();
//        $projects = Project::orderBy('id','desc')->limit(5)->get();
//
//        foreach($projects as $yy){
//          $cName = Client::where('id',$yy->id)->value('name');
//          $yy['clientName'] = $cName;
//
//          $brand = Brand::where('brand_key',$yy->brand_key)->value('name');
//          $yy['brandName'] = $brand;
//
//          $agent = $yy->getAgent?? User::where('id',$yy->agent_id)->withTrashed()->first();
//          $yy['agentName'] = $agent->name??"";
//          $yy['agentDesignation'] = $agent->designation??"";
//          $yy['agentImage'] = $agent->image??"";
//
//          $pm = User::where('id',$yy->asigned_id)->first();
//          if($pm){
//              $yy['pmName'] = $pm->name;
//              $yy['pmDesignation'] = $pm->designation;
//              $yy['pmImage'] = $pm->image;
//          }else{
//              $yy['pmName'] = '---';
//              $yy['pmDesignation'] = '----';
//          }
//          $status = ProjectStatus::where('id',$yy->project_status)->first();
//          $yy['status'] = $status->status;
//          $yy['statusColor'] = $status->status_color;
//
//          array_push($projectData,$yy);
//        }
//
//        //current Week Data
//        $weekDayWise = Payment::select(DB::raw("(SUM(amount)) as amount"),DB::raw("DAYNAME(created_at) as dayname"))
//        ->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
//        ->where('payment_status',1)
//        ->whereYear('created_at', date('Y'))
//        ->groupBy('dayname')
//        ->get();
//
//        $weekDaysWiseData = array();
//
//        foreach($weekDayWise as $key => $dayWise){
//
//          if('Monday' == $dayWise->dayname){
//            $weekDaysWiseData['Monday']=[$dayWise->dayname,$dayWise->amount];
//          }
//          if('Tuesday' == $dayWise->dayname){
//            $weekDaysWiseData['Tuesday']=[$dayWise->dayname,$dayWise->amount];
//          }
//          if('Wednesday' == $dayWise->dayname){
//            $weekDaysWiseData['Wednesday']=[$dayWise->dayname,$dayWise->amount];
//          }
//          if('Thursday' == $dayWise->dayname){
//            $weekDaysWiseData['Thursday']=[$dayWise->dayname,$dayWise->amount];
//          }
//          if('Friday' == $dayWise->dayname){
//            $weekDaysWiseData['Friday']=[$dayWise->dayname,$dayWise->amount];
//          }
//          if('Saturday' == $dayWise->dayname){
//            $weekDaysWiseData['Saturday']=[$dayWise->dayname,$dayWise->amount];
//          }
//          if('Sunday' == $dayWise->dayname){
//            $weekDaysWiseData['Sunday']=[$dayWise->dayname,$dayWise->amount];
//          }
//      }
//
//
//      //merchant payment data
//      $payment_methods = array();
//      // 5 id developer testing
//      $payment_method_data = PaymentMethod::where('id','!=',5)->get();
//
//      foreach($payment_method_data as $payment){
//           $merchant_monthPayment = Payment::where(['payment_status'=>1,'merchant_id'=>$payment->id])
//           ->whereMonth('created_at', Carbon::now()->month)
//           ->sum('amount');
//          //set Type
//          settype($merchant_monthPayment, "integer");
//          $payment['paymentMonth'] =$merchant_monthPayment;
//          array_push($payment_methods,$payment);
//      }
//      //dd($payment_methods);
//
//      $data = [
//          "monthPayment"          => thousand_format($monthPayment),
//          "todayPayment"          => thousand_format($todayPayment),
//          "dueInvoicePayment"     => thousand_format($dueInvoicePayment),
//          "overdueInvoicePayment" => thousand_format($overdueInvoicePayment),
//          "leadstatus"            => json_encode($leadData),
//          "statusValue"           => json_encode($statusValue),
//          "statusColor"           => json_encode($statusColor),
//          "yearlyIncome"          => thousand_format($yearIncome),
//          "monthWise"             => json_encode($month),
//          "monthWiseAmount"       => json_encode($monthAmount),
//          "brandData"             => $brandData,
//          "teamData"              => $teamData,
//          "recentPayments"        => $paymentData,
//          "m2"                    => $m2,
//          "yearExpMonthWiseData"  => $yearExpMonthWiseData,
//          "yearSpending"          => thousand_format($yearSpending),
//          "yearRefund"            => thousand_format($yearRefund),
//          "yearchargeback"        => thousand_format($yearchargeback),
//          'googleSpending'        => thousand_format($googleSpending),
//          'bingSpending'          => thousand_format($bingSpending),
//          'yearSpending'          => thousand_format($yearSpending),
//          'monthSpending'         => thousand_format($monthSpending),
//          'yearExpence'           => thousand_format($yearExpence),
//          "refundYearMonthWiseData"     => $refundYearMonthWiseData,
//          "chargebackYearMonthWiseData" => $chargebackYearMonthWiseData,
//          'projectStatus'         => $project_status_data,
//          'projectcategoriesdata'  => $project_categories_data,
//          'recentProject'         => $projectData,
//          'weekDaysWise'          => $weekDaysWiseData
//
//      ];
//
//        return view('admin.dashboard',compact('data'));
//    }

}

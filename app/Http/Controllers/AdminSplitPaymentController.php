<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

/** Models */

use App\Models\Brand;
use App\Models\CcInfo;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\PaymentMethod;
use App\Models\SplitPayment;
use App\Models\Payment;
use App\Models\PaymentTransactionsLog;
use App\Models\User;
use App\Models\PaymentErrorCods;

/** Authorize Packages */

use net\authorize\api\contract\v1 as AnetAPI;
use net\authorize\api\controller as AnetController;
use Carbon\Carbon;
use Config;


class AdminSplitPaymentController extends Controller
{
    public function index()
    {
        $splitPaymentsPaidInvoices = SplitPayment::whereRelation('getInvoice',"status",'paid')->orderBy('status', 'ASC')->get();
        $splitPaymentsDueInvoices = SplitPayment::whereRelation('getInvoice',"status",'due')->orderBy('status', 'ASC')->get();

        return view('admin.splitpayments.index', compact('splitPaymentsPaidInvoices','splitPaymentsDueInvoices'));

    }

    public function pay_now_split_payments($id)
    {
        return $this->pay_now_split_payments_global($id);
    }
}

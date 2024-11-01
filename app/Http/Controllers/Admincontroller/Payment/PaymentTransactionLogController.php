<?php

namespace App\Http\Controllers\Admincontroller\Payment;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\PaymentTransactionsLog;
use App\Models\Team;
use Illuminate\Http\Request;

class PaymentTransactionLogController extends Controller
{
    public function index(Request $request){
        $result = $this->getData($request, new PaymentTransactionsLog(),false);
        $payment_transaction_logs = $result['data'];
        $fromDate = $result['fromDate'];
        $toDate = $result['toDate'];
        $teamKey = $result['teamKey'];
        $brandKey = $result['brandKey'];
        $teams = Team::where('status', '1')->get();
        $brands = Brand::where('status', '1')->orderby('name', 'Asc')->get();
        return view('admin.payment.payment-transaction-log.index',compact('payment_transaction_logs','fromDate','toDate','teamKey','brandKey','teams','brands'));
    }
}

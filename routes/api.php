<?php

use App\Http\Controllers\Adminauth\AuthenticatedSessionController;
use App\Http\Controllers\Api\ApiTrelloUrlController;
use App\Http\Controllers\Api\ApiTrelloAttachmentUrlController;
use App\Http\Controllers\Auth\AuthenticatedSessionController as UserAuthenticatedSessionController;
use App\Http\Controllers\Admincontroller\LeadController;
use App\Http\Controllers\Admincontroller\Payment\AuthorizePaymentController;
use App\Http\Controllers\Admincontroller\PaymentMethod\PaymentMethodController;
use App\Http\Controllers\Api\ApiAuthorizePaymentController;
use App\Http\Controllers\Api\ApiAuthorizePaymentAuthorizationController;
use App\Http\Controllers\Api\ApiAuthorizeUnsettledPaymentController;
use App\Http\Controllers\Api\ApiAuthorizeWebhookController;
use App\Http\Controllers\Api\ApiBoardListCardBulkDataController;
use App\Http\Controllers\Api\ApiBrandController;
use App\Http\Controllers\Api\ApiExpigatePaymentController;
use App\Http\Controllers\Api\ApiInvoiceController;
use App\Http\Controllers\Api\ApiLeadController;
use App\Http\Controllers\Api\ApiMerchantDescriptorController;
use App\Http\Controllers\Api\ApiPayarcPaymentController;
use App\Http\Controllers\Api\ApiPaymentController;
use App\Http\Controllers\Api\ApiSplitPaymentController;
use App\Http\Controllers\PaymentApiController;
use App\Http\Controllers\PaymentController;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ApiGmailController;

Route::get('gmail-listing', [ApiGmailController::class, 'listMessages']);

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

//Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//    return $request->user();
//});
Route::middleware(['web'])->get('/csrf-token', function (Request $request) {
    try {
        return response()->json(['csrf_token' => csrf_token()]);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage(),'line'=>$e->getLine()], 500);
    }
});
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/user', function (Request $request) {
        try {
            $user = $request->user();
            return response()->json(new UserResource($user), 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage(),'line'=>$e->getLine()], 500);
        }
    });
});

Route::post('store', [LeadController::class, 'store']);
Route::get('/get_merchants', [PaymentMethodController::class, 'get_merchants']);
Route::get('/show_invoice/{id}', [PaymentController::class, 'show_api']);
Route::post('store_payment', [PaymentController::class, 'create_payment_api']);
Route::post('direct_payment', [PaymentController::class, 'direct_payment_api']);
Route::post('store_tans_log', [PaymentController::class, 'create_trans_log_api']);
Route::get('/show_paid_invoice/{id}', [PaymentController::class, 'show_paid_invoice_api']);
Route::get('/get_payment_error_code', [PaymentController::class, 'get_payment_error_codes']);
Route::post('crm-api-payment-create', [PaymentApiController::class, 'crm_api_payment_create']);

Route::post('crm-api-payment-create-authorize', [PaymentApiController::class, 'crm_api_payment_create_authorize'])->name('api.authorize.payment');
/**Michael*/
$BRAND_URL_LIST = Route::get('brand-url-list', [ApiBrandController::class, 'brand_url_list']);
$VALIDATE_PHONE = Route::post('validatePhone', [LeadController::class, 'validate_phone']);
$VALIDATE_IP = Route::post('validateIp', [LeadController::class, 'validate_ip']);
$LOGIN_API = Route::post('/admin/login', [AuthenticatedSessionController::class, 'apiLogin']);
$SPLIT_PAYMENT_BY_ID = Route::get('pay-now-split-payments/{id?}', [ApiSplitPaymentController::class, 'pay_now_split_payments']);
//$ALL_LEADS = Route::get('/all-leads', [ApiLeadController::class, 'index']);
$LEAD_BY_ID = Route::get('/lead/{id?}', [ApiLeadController::class, 'show']);
$TEST_MAIL = Route::get('/test/{brand_key?}/{sandbox?}', [ApiInvoiceController::class, 'test']);
//$ALL_INVOICES = Route::get('/all-invoices', [ApiInvoiceController::class, 'index']);
$CREATE_INVOICE = Route::post('/create-invoice', [ApiInvoiceController::class, 'create_invoice']);
$CREATE_TRANSACTION_LOGS = Route::post('/create-transaction-logs', [PaymentApiController::class, 'create_transaction_logs']);
/**Check payment with in a month*/
$MULTI_PAYMENT_EXPIGATE_AUTHORIZE = Route::post('/multi-payments-expigate-authorize', [ApiPaymentController::class, 'multi_payments_expigate_authorize']);
$MULTI_PAYMENT = Route::post('/multi-payments', [ApiPaymentController::class, 'multi_payments'])->name('api.multi.payments');
/** 1 param = number of times , 2 param = number of minutes*/
$CREATE_TRACKING_IP_WITH_MIDDLEWARE = Route::middleware('throttle:12,1')->group(function () {
    $CREATE_TRACKING_IP = Route::post('/create-tracking-ip/{id?}', [ApiInvoiceController::class, 'create_tracking_ip']);
});
$EXPIGATE_PROCESS_PAYMENT = Route::post('/expigate-process-payment', [ApiExpigatePaymentController::class, 'process_payment'])->name('api.expigate.payment');
$PAYARC_PROCESS_PAYMENT = Route::post('/payarc-process-payment', [ApiPayarcPaymentController::class, 'process_payment'])->name('api.payarc.payment');
$AUTHORIZE_PROCESS_PAYMENT = Route::post('/authorize-process-payment', [ApiAuthorizePaymentController::class, 'process_payment'])->name('api.authorize.payment.new');

$AUTHORIZE_PAYMENT_AUTHORIZATION = Route::post('/authorize-payment-authorization', [ApiAuthorizePaymentAuthorizationController::class, 'payment_authorization'])->name('api.authorize.payment.authorization');
$AUTHORIZE_CAPTURE_AUTHORIZED_PAYMENT = Route::post('/authorize-payment-capture-authorized-invoice', [ApiAuthorizePaymentAuthorizationController::class, 'payment_capture_authorized_invoice'])->name('api.authorize.payment.capture.authorized.invoice');
$ADD_SIGNATURE_TO_INVOICE = Route::post('/add-signature-to-invoice', [ApiInvoiceController::class, 'add_signature'])->name('api.add.signature.to.invoice');
$LEAD_FORM_SUBMISSION = Route::post('form_submission', [LeadController::class, 'form_submission']);
Route::post('crm-api-expigate-update', [PaymentApiController::class, 'crm_api_expigate_update']);

$AUTHORIZE_WEBHOOK = Route::post('authorize-webhook', [ApiAuthorizeWebhookController::class, 'handle_webhook']);
$BASE64ENCODE = Route::post('base64-encode', static function (Request $request) {
    return base64_encode($request->base64_encode);
});

$AUTHORIZE_WEBHOOK = Route::post('authorize-webhook', [ApiAuthorizeWebhookController::class, 'handle_webhook']);
$AUTHORIZE_CHECK_UNSETTLED = Route::get('check-payment-status', [ApiAuthorizeUnsettledPaymentController::class, 'check_payment_status']);
$MERCHANT_DESCRIPTOR = Route::get('merchant-descriptor/{merchant?}/{id?}', [ApiMerchantDescriptorController::class, 'merchant_descriptor']);

$IMPORT_BOARD_LIST_CARD = Route::post('board-list-card', [ApiBoardListCardBulkDataController::class, 'store']);
$IMPORT_BOARD_LIST_CARD_PHP = Route::post('board-list-card-php', [ApiBoardListCardBulkDataController::class, 'save_php']);
$TRELLO_FIRST_URL = Route::get('/url/index', [ApiTrelloUrlController::class, 'index']);
$TRELLO_URLS = Route::post('/urls/create', [ApiTrelloUrlController::class, 'store']);
$TRELLO_FIRST_ATTACHMENT_URL = Route::get('/attachment-url/index', [ApiTrelloAttachmentUrlController::class, 'index']);
$TRELLO_ATTACHMENT_URLS = Route::post('/attachment-urls/create', [ApiTrelloAttachmentUrlController::class, 'store']);
$USER_LOGIN_API = Route::post('/login', [UserAuthenticatedSessionController::class, 'apiLogin']);
$IMPORT_BOARD_LIST_CARD_MEMBER = Route::get('save-trello-users/{id?}', [ApiBoardListCardBulkDataController::class, 'save_trello_users']);
$CREATE_BOARD_LIST_CARD_MEMBER = Route::post('create-trello-user', [ApiBoardListCardBulkDataController::class, 'create_trello_users']);
$BOARD_LIST_CARD_ADD_ATTACHMENT = Route::post('/board-list-cards/add-attachment-php', [ApiBoardListCardBulkDataController::class, 'add_attachment_php']);
$BOARD_LIST_CARD_UPDATE_COVER_IMAGE_PHP = Route::post('/board-list-cards/cover-image/update-php', [ApiBoardListCardBulkDataController::class, 'update_cover_image_php']);

/**  react trello api */
require __DIR__ . '/react-trello-api.php';

/** Update - Cb*/
Route::post('crm-api-paypal-create', [PaymentApiController::class, 'crm_api_paypal_create']);

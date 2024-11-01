<?php

use App\Http\Controllers\{AdminSplitPaymentController,
    Controller,
    generalController,
    GoogleSocialiteController,
    LogoutAllSessionsController,
    MerchantFeeAndTaxPaymentController,
    PaymentController,
    RequestTradeMarkController,
    Usercontroller\NotificationController as UserNotificationController
};
use App\Http\Controllers\Admincontroller\{adminClientController,
    adminController,
    adminDashboardController,
    adminLogController,
    adminInvoiceController,
    adminPaymentController,
    AdminProjectController,
    DepartmentController as AdminDepartmentController,
    BoardListController as AdminBoardListController,
    BoardListCardController as AdminBoardListCardController,
    BrandController,
    CardController as AdminCardController,
    CategoryController,
    CustomerSheetController as AdminCustomerSheetController,
    EmailConfigurationController as AdminEmailConfigurationController,
    IpAddressController as AdminIpAddressController,
    LeadController,
    LeadStatusController,
    Payment\AuthorizePaymentController,
    Payment\PaymentMultipleResponseController,
    Payment\PaymentTransactionLogController,
    PaymentMethod\PaymentMethodController,
    PaymentMethod\PaymentMethodExpigateController,
    PaymentMethod\PaymentMethodPayArcController,
    SpendingController,
    TaskController,
    Team\SpendingController as AdminTeamSpendingController,
    Team\TargetController as AdminTeamTargetController,
    Team\IndirectCostingController as AdminIndirectCostingController,
    Team\CarryForwardController as AdminCarryForwardController,
    Team\FixedCostingController as AdminFixedCostingController,
    TeamController as AdminTeamController,
    ThirdPartyRoleController as AdminThirdPartyRoleController,
    User_info_apiController,
    Website_viewController,
    WirePaymentController as AdminWirePaymentController
};
use App\Http\Controllers\Usercontroller\{BoardController as UserBoardController,
    BoardListCardController as UserBoardListCardController,
    ClientController,
    CustomerSheetController as UserCustomerSheetController,
    EmailSystemController as UserEmailSystemController,
    ExpenseController,
    InvoiceController,
    LeadController as UserLeadController,
    ProjectController,
    RedirectToTrelloController,
    TeamController as UserTeamController,
    ThirdPartyRoleController as UserThirdPartyRoleController,
    UserDashboardController,
    UserPaymentController,
    UserSpendingController,
    WirePaymentController as UserWirePaymentController
};
use Illuminate\Support\Facades\Route;

//userController

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::get('/', function () {
    return view('auth.login');
});
Route::any('/logs', [adminLogController::class, 'index'])->name('userlogs');

Route::get('request-trademark', [RequestTradeMarkController::class, 'index'])->name('request.trademark');
Route::post('request-trademark-submit', [RequestTradeMarkController::class, 'submit_form'])->name('request.trademark.submit');
Route::middleware(['auth', 'track.views'])->group(function () {

    //Dashboard Routes
    Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('dashboard');
    Route::get('/logout-all-sessions', [LogoutAllSessionsController::class, 'logoutAllSessions']);
    
    Route::middleware(['trello-access'])->group(function () {
        Route::get('/redirect-to-trello', [RedirectToTrelloController::class, 'redirectToTrello'])->name('user.redirect-to-trello');
    });
    Route::middleware(['crm-access'])->group(function () {
        Route::post('/notifications/mark-all-as-read', [UserNotificationController::class, 'mark_all_as_read'])->name('user.notifications.mark.all.as.read');
        Route::get('/profile', [UserDashboardController::class, 'userProfile'])->name('user.profile');
        Route::post('/password-confirmation', [UserDashboardController::class, 'password_confirmation'])->name('user.password.confirmation');
        Route::post('/profile-update', [UserDashboardController::class, 'profile_update'])->name('user.profile.update');
        Route::post('/update-profile-image', [UserDashboardController::class, 'update_profile_image'])->name('update_profile_image');
        Route::post('/user-update-password', [UserDashboardController::class, 'user_profile_password_update'])->name('user_profile_password_update');
    //    Route::put('/user.update/{id}', [App\Http\Controllers\Usercontroller\UserDashboardController::class, 'userProfileupdate']);
    
        //brand
        Route::get('/brand', [App\Http\Controllers\Usercontroller\BrandController::class, 'index'])->name('brand');
    
        //team
        Route::get('/team', [UserTeamController::class, 'index'])->name('team');
        Route::post('/assign-unassign-user-brand-email/{id?}', [UserTeamController::class, 'assign_unassign_brand_emails'])->name('user.assign.unassign.user.brand.email');
        Route::get('/fetch-member-emails/{id?}', [UserTeamController::class, 'fetch_member_emails'])->name('user.fetch.member.emails');
    
        /**User Side Board List*/
        Route::get('/board', [UserBoardController::class, 'index'])->name('user.board.index');
        Route::post('/board/board-card-change', [UserBoardController::class, 'board_card_change'])->name('user.board.card.change');
    
        Route::get('encrypt/{id}', [Controller::class, 'encrypt'])->name('encrypt');
        Route::get('decrypt/{id}', [Controller::class, 'decrypt'])->name('decrypt');
    
        Route::get('/team/board/{id?}', [UserTeamController::class, 'board'])->name('team.board');
        Route::get('/board/task_card_change', [UserTeamController::class, 'task_card_change'])->name('team.task_card_change');
    
        /**Board List Cards Routes */
        Route::prefix('/board-list-cards')->group(function () {
            Route::get('/testtest', function () {
                return response()->json('dad');
            })->name('testtest');
            Route::get('/index', [UserBoardListCardController::class, 'index'])->name('user.board.list.card.index');
            Route::post('/store', [UserBoardListCardController::class, 'store'])->name('user.board.list.card.store');
            Route::post('/update-title', [UserBoardListCardController::class, 'title_update'])->name('user.board.list.card.title.update');
            Route::post('/update-image/{id}', [UserBoardListCardController::class, 'image_update'])->name('user.board.list.card.image.update');
            Route::post('/update-attachment-as-cover-image', [UserBoardListCardController::class, 'att_as_cover_img'])->name('user.board.list.card.set.attachment.cover.image');
            Route::post('/image-background-color-update/{id}', [UserBoardListCardController::class, 'update_cover_background_color'])->name('user.board.list.card.image.background.color.update');
            Route::post('/update-image-background-size', [UserBoardListCardController::class, 'update_image_size'])->name('user.board.list.card.image.size.update');
            Route::post('/assign-unassign-member/{id}', [UserBoardListCardController::class, 'assign_unassign_member'])->name('user.board.list.card.assign.unassign.member');
            Route::post('/assign-own-member/{id}', [UserBoardListCardController::class, 'assign_own_member'])->name('user.board.list.card.assign.own.member');
            Route::post('/add-comment/{id}', [UserBoardListCardController::class, 'add_comment'])->name('user.board.list.card.add.comment');
            Route::post('/update-comments', [UserBoardListCardController::class, 'update_comment'])->name('user.board.list.card.update.comment');
            Route::delete('/delete-comment/{id}', [UserBoardListCardController::class, 'delete_comment'])->name('user.board.list.card.delete.comment');
            Route::post('/add-attachment/{id}', [UserBoardListCardController::class, 'add_attachment'])->name('user.board.list.card.add.attachment');
            Route::delete('/delete-attachment/{id}', [UserBoardListCardController::class, 'delete_attachment'])->name('user.board.list.card.delete.attachment');
            Route::get('/card-comment/{id}', [UserBoardListCardController::class, 'get_comment_by_id'])->name('user.board.list.card.get.comment');
            Route::get('/card-description/{id}', [UserBoardListCardController::class, 'get_description_by_id'])->name('user.board.list.card.get.description');
            Route::post('/card-update-description/{id}', [UserBoardListCardController::class, 'card_update_description'])->name('user.board.list.card.update.description');
            Route::get('/card-all-comments/{id}', [UserBoardListCardController::class, 'get_card_all_comments'])->name('user.board.list.card.all.comments');
            Route::post('/update-dates', [UserBoardListCardController::class, 'update_dates'])->name('user.board.list.card.update.dates');
            Route::post('/remove-dates', [UserBoardListCardController::class, 'remove_dates'])->name('user.board.list.card.remove.dates');
            Route::get('/edit/{id}', [UserBoardListCardController::class, 'edit'])->name('user.board.list.card.edit');
            Route::post('/update/{id}', [UserBoardListCardController::class, 'update'])->name('user.board.list.card.update');
            Route::post('/label-create/{id?}', [UserBoardListCardController::class, 'label_create'])->name('user.board.list.label.create');
            Route::post('/label-assign-unassign/{id?}', [UserBoardListCardController::class, 'label_assign_unassign'])->name('user.board.list.label.assign_unassign');
            Route::post('/label-remove/{id?}', [UserBoardListCardController::class, 'label_remove'])->name('user.board.list.label.remove');
    
            /** TODO */
    //        Route::post('/update/{id}', [UserBoardListCardController::class, 'update'])->name('user.board.list.card.update');
    //        Route::get('/destroy/{id}', [UserBoardListCardController::class, 'destroy'])->name('user.board.list.card.destroy');
    //        Route::get('/restore/{id}', [UserBoardListCardController::class, 'restore'])->name('user.board.list.card.restore');
    //        Route::get('/trashed', [UserBoardListCardController::class, 'trashed'])->name('user.board.list.card.trashed');
    //        Route::get('/restore-all', [UserBoardListCardController::class, 'restore_all'])->name('user.board.list.card.restore.all');
    //        Route::get('/force-delete/{id}', [UserBoardListCardController::class, 'force_delete'])->name('user.board.list.card.force.delete');
    //        Route::get('/change-status', [UserBoardListCardController::class, 'change_status'])->name('user.board.list.card.change.status');
        });
    
        /** User Email System Routes */
        Route::prefix('/emails')->group(function () {
            Route::get('/{email?}/inbox', [UserEmailSystemController::class, 'index'])->name('user.email.system.index');
            Route::get('/{email?}/sent', [UserEmailSystemController::class, 'index'])->name('user.email.system.sent');
            Route::get('/{email?}/spam', [UserEmailSystemController::class, 'index'])->name('user.email.system.spam');
            Route::get('/{email?}/trash', [UserEmailSystemController::class, 'index'])->name('user.email.system.trash');
            Route::get('/{email?}/{type?}/message/{message_id?}', [UserEmailSystemController::class, 'read_message'])->name('user.email.system.read.message');
            Route::get('/reply-message-body', [UserEmailSystemController::class, 'reply_message_body'])->name('user.email.system.reply.message.body');
            Route::post('/mark-as-read-unread', [UserEmailSystemController::class, 'mark_as_read_unread'])->name('user.email.system.mark.message.read.unread');
            Route::post('/compose-message', [UserEmailSystemController::class, 'compose_message'])->name('user.email.system.compose.message');
            Route::post('/reply-message', [UserEmailSystemController::class, 'reply_message'])->name('user.email.system.reply.message');
            Route::post('/add-attachment', [UserEmailSystemController::class, 'add_attachment'])->name('user.email.system.add.attachment');
    //        Route::post('/submit-message', [UserEmailSystemController::class, 'submit_message'])->name('user.email.system.submit.reply');
            Route::get('/get-message-with-thread/{id?}', [UserEmailSystemController::class, 'get_message_with_thread'])->name('user.email.system.get.message.with.thread');
            Route::get('/get-email-suggestions', [UserEmailSystemController::class, 'email_suggestions'])->name('user.email.system.get.email.suggestions');
            Route::post('/edit-signature', [UserEmailSystemController::class, 'edit_signature'])->name('user.email.system.edit.signature');
        });
        /** User Email System Routes */
    
        /** TM User Routes */
        /******** Customer Sheet Routes */
        Route::prefix('/customer-sheets')->group(function () {
            Route::get('', [UserCustomerSheetController::class, 'index'])->name('user.customer.sheet.index');
            Route::post('/store', [UserCustomerSheetController::class, 'store'])->name('user.customer.sheet.store');
            Route::get('/edit/{id}', [UserCustomerSheetController::class, 'edit'])->name('user.customer.sheet.edit');
            Route::post('/update/{id}', [UserCustomerSheetController::class, 'update'])->name('user.customer.sheet.update');
            Route::get('/view-attachment/{id}', [UserCustomerSheetController::class, 'view_attachment'])->name('user.customer.sheet.view.attachment');
            Route::post('/add-attachment/{id}', [UserCustomerSheetController::class, 'add_attachment'])->name('user.customer.sheet.add.attachment');
            Route::get('/destroy-attachment/{id}', [UserCustomerSheetController::class, 'destroy_attachment'])->name('user.customer.sheet.attachment.destroy');
            Route::get('/destroy/{id}', [UserCustomerSheetController::class, 'destroy'])->name('user.customer.sheet.destroy');
        });
        /******* Customer Sheet Routes */
        /** TM User Routes */
    
        /** Third Party User Routes */
        /******** Third Party Role Routes */
        Route::prefix('/third-party-roles')->group(function () {
            Route::get('', [UserThirdPartyRoleController::class, 'index'])->name('user.third.party.role.index');
            Route::get('/get-teams-agents-and-clients/{team_key?}', [UserThirdPartyRoleController::class, 'get_teams_agents_and_clients'])->name('user.third.party.role.team.agents.clients');
            Route::get('/get-client-paid-invoices/{team_key?}/{client_id?}', [UserThirdPartyRoleController::class, 'get_client_paid_invoices'])->name('user.third.party.client.paid.invoices');
            Route::post('/store', [UserThirdPartyRoleController::class, 'store'])->name('user.third.party.role.store');
            Route::get('/edit/{id?}', [UserThirdPartyRoleController::class, 'edit'])->name('user.third.party.role.edit');
            Route::post('/update/{id?}', [UserThirdPartyRoleController::class, 'update'])->name('user.third.party.role.update');
        });
        /******* Third Party Role Routes */
        /** Third Party User Routes */
    
        //Lead
        Route::get('/leads', [UserLeadController::class, 'index'])->name('user.leads.index');
        Route::post('/create-lead', [UserLeadController::class, 'create_lead'])->name('user.lead.create');
        Route::get('/lead/{id}', [UserLeadController::class, 'show'])->name('leadshow');
        Route::post('/teambrandLead', [UserLeadController::class, 'team_brands'])->name('teambrandleads');
        Route::post('/monthlead', [UserLeadController::class, 'monthly_lead'])->name('monthleads');
        Route::get('/changeUserleadStatus', [UserLeadController::class, 'leadStatus'])->name('changeUserLeadStatus');
        Route::get('/assignlead', [UserLeadController::class, 'assinglead'])->name('assignedLead');
        Route::get('/mylead', [UserLeadController::class, 'my_lead'])->name('myLead');
        Route::get('/brandteam/{id}', [UserLeadController::class, 'teamBrands'])->name('leadTeamBrand'); // lead page [show team brands]
    
        //Lead Comments
        Route::get('userleadcomments/{id}', [LeadController::class, 'get_lead_comments'])->name('userleadComments');
        Route::post('/usercreatecomments', [LeadController::class, 'admin_create_comments'])->name('userCreateComments');
    
        //Invoice
        Route::get('/invoices', [InvoiceController::class, 'index'])->name('user.invoices.index');
        Route::resource('/invoice', InvoiceController::class);
        Route::post('/createinvoice', [InvoiceController::class, 'create_invoice'])->name('storeInvoice');
        Route::get('/sendinvoice/{id}', [InvoiceController::class, 'send_invoice_email'])->name('sendinvoicemail');
        Route::get('/clientproject/{id}', [InvoiceController::class, 'show_client_projects'])->name('clientProject');
        Route::get('/my-invoices', [InvoiceController::class, 'show_client_invoice'])->name('clientInvoice'); //client side Invoice list
        Route::get('/publishinvoice/{id}', [InvoiceController::class, 'publish_invoice'])->name('publishinvoice');
        Route::get('/teamMember/{brand_key?}/{team_key?}', [InvoiceController::class, 'teamAgent'])->name('teamAgent'); // lead page [show team brands]
        Route::get('/team-brands/{id}', [InvoiceController::class, 'teamBrands'])->name('user.team.brands');
    
        //client
        Route::get('/get-client/{id?}/spending', [ClientController::class, 'get_spending'])->name('user.client.spending');
        Route::get('/clients', [ClientController::class, 'index'])->name('user.clients.index');
        Route::resource('/client', ClientController::class);
        Route::post('/createclientinvoice', [ClientController::class, 'create_client_invoice'])->name('createClientInvoice');
        Route::post('/createclientproject', [ClientController::class, 'create_client_project'])->name('createClientProject');
    
        //User Payments
        Route::prefix('/user-payments')->group(function () {
            Route::get('/', [UserPaymentController::class, 'index'])->name('user.payments.index'); //For User Dashboard
            Route::get('/destroy/{id}', [UserPaymentController::class, 'destroy'])->name('user.payment.destroy');
        });
    
        /******** Wire Payment Routes Start *******/
        Route::prefix('/wire-payments')->group(function () {
            Route::get('', [UserWirePaymentController::class, 'index'])->name('user.wire.payments.index');
            Route::post('/store', [UserWirePaymentController::class, 'store'])->name('user.wire.payment.store');
        });
        /******** Wire Payment Routes End *******/
    
        Route::resource('/userpayment', UserPaymentController::class);
        Route::get('/payments', [UserPaymentController::class, 'show_client_payment'])->name('clientPyament'); //client side Payment list
        Route::get('/paymentdetail/{id}', [UserPaymentController::class, 'get_payment_detail'])->name('getPaymentDetails'); //team Lead get payment details for Refund
        Route::post('/paymentrefund', [UserPaymentController::class, 'payment_refund'])->name('PaymentRefund');
        Route::get('/refunds', [UserPaymentController::class, 'show_payment_refund'])->name('refundList'); //client side Payment list
        Route::get('/approvedrefunds', [UserPaymentController::class, 'refund_status_approved'])->name('refundStatusApproved'); //client side Payment list
        Route::post('/createpayment', [UserPaymentController::class, 'direct_payment'])->name('createPayment');
        Route::get('/showpayment/{id}', [UserPaymentController::class, 'show_payment_details'])->name('showPaymentDetail');
        Route::post('/compliancevarified', [UserPaymentController::class, 'compliance_varified_payment'])->name('complianceVarified');
        Route::get('searchUserPayment', [UserPaymentController::class, 'search_payment'])->name('searchPayment');
    
        /** Upsale Multi Payment*/
        Route::post('/user-create-upsale-mutli-payment', [PaymentController::class, 'upsale_multi_payment'])->name('user_upsale_multi_payment');
        Route::post('/user-create-upsale-payment', [App\Http\Controllers\PaymentController::class, 'upsale_payment'])->name('UserUpsalePayment'); //Create Project/Client payment
        Route::get('/client_card_info/{id}', [App\Http\Controllers\PaymentController::class, 'get_client_card_info']);
    
        // Projects
        /**Sequence must be like this otherwise new functions below resource won't work*/
        Route::match(['get', 'post'], '/project/new-index', [ProjectController::class, 'new_index'])->name('project.new.index');
        Route::get('/project/load-more-projects', [ProjectController::class, 'load_more_projects'])->name('project.load.more.project');
        Route::get('/project/new-detail/{id?}', [ProjectController::class, 'new_detail'])->name('project.new.detail');
        Route::get('/project/show_new/{id?}', [ProjectController::class, 'show_new'])->name('project.show.new');
        Route::post('/project/new-store', [ProjectController::class, 'new_store'])->name('project.new.store');
        Route::resource('/project', ProjectController::class);
        Route::get('/changeProjectStatus', [App\Http\Controllers\Usercontroller\ProjectController::class, 'projectStatus'])->name('changeClientProjectStatus');
        Route::get('/updateProjectDetails', [App\Http\Controllers\Usercontroller\ProjectController::class, 'projectDetailsUpdate'])->name('updateProjectDescription');
        Route::post('/createprojectinvoice', [ProjectController::class, 'create_project_invoice'])->name('createProjectInvoice');
        Route::post('/uploadprojectfile', [ProjectController::class, 'upload_project_file'])->name('uploadProjectFile');
        Route::get('deletefile/{id}', [ProjectController::class, 'delete_file'])->name('deletefile');
        Route::post('/createcomment', [ProjectController::class, 'create_project_comment'])->name('createComment');
        Route::get('/visibilityFilestatus', [ProjectController::class, 'visibilityFilestatus']);
        Route::get('/all-comments', [ProjectController::class, 'all_comments'])->name('allComments');
        Route::get('/projects', [ProjectController::class, 'client_projects'])->name('clientProjects');
    
        //Task Routes
        Route::resource('/task', TaskController::class);
        Route::get('/task_trashed', [TaskController::class, 'trashed_task'])->name('trashed_task');
        Route::get('/task_restore/{id}', [TaskController::class, 'restore'])->name('restore_task');
        Route::get('/task_restoreall', [TaskController::class, 'restoreAll'])->name('restoreall_task');
        Route::get('/task_forcedelete/{id}', [TaskController::class, 'task_forceDelete'])->name('task_forcedelete');
        Route::get('/task_changeStatus', [TaskController::class, 'task_changeStatus'])->name('changetaskStatus');
        //client side project list
        Route::get('/changeProjectManager', [ProjectController::class, 'projectManager'])->name('changeProjectManager');
        Route::get('/amProjects', [ProjectController::class, 'account_manager_projects'])->name('accountManagerProjects'); //client side project list
        Route::post('/createprojectpayment', [ProjectController::class, 'create_project_payment'])->name('createProjectPayment');
    
        //Expense
        Route::resource('/expense', ExpenseController::class);
        Route::get('/showbrands/{id}', [ExpenseController::class, 'show_team_brands'])->name('showTeamBrands');
        Route::get('/brandproject/{id}', [ExpenseController::class, 'show_brand_projects'])->name('showBrandProject');
        Route::get('/projectdetail/{id}', [ExpenseController::class, 'project_detail'])->name('projectDetail');
    
        // PPC Spendings
        Route::resource('/userspending', UserSpendingController::class);
    
    
    });
});

require __DIR__ . '/auth.php';

// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth'])->name('dashboard');

// Route::get('/admin/dashboard', function () {
//     return view('admin.dashboard');
// })->middleware(['auth:admin'])->name('admin.dashboard');

// Route::resource('/admin/category', CategoryController::class);

Route::middleware(['auth:admin', 'track.admin.views'])->group(function () {
    Route::get('/logout-all-sessions', [LogoutAllSessionsController::class, 'logoutAllSessions']);

    Route::prefix('/admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [adminDashboardController::class, 'index'])->name('dashboard');
        Route::any('/log', [adminLogController::class, 'index'])->name('log');
        Route::any('/logupdate', [adminLogController::class, 'updateipd'])->name('logupdate');
        Route::prefix('/profile')->name('profile.')->group(function () {
            Route::get('/', [adminDashboardController::class, 'view_profile'])->name('index');
            Route::post('/password-confirmation', [adminDashboardController::class, 'password_confirmation'])->name('password.confirmation');
            Route::post('/update', [adminDashboardController::class, 'profile_update'])->name('update');
            Route::post('/update-image', [adminDashboardController::class, 'update_profile_image'])->name('update.image');
            Route::post('/update-password', [adminDashboardController::class, 'password_update'])->name('password.update');
        });
    });
    Route::prefix('/admin/stats')->group(function () {
        Route::get('', [adminDashboardController::class, 'stats'])->name('admin.stats');
        Route::get('spending', [AdminTeamSpendingController::class, 'index'])->name('admin.stats.spending.index');
        Route::get('target', [AdminTeamTargetController::class, 'index'])->name('admin.stats.target.index');
//        Route::get('indirect-costing', [AdminIndirectCostingController::class, 'index'])->name('admin.stats.indirect-costing.index');
        Route::get('carry-forward', [AdminCarryForwardController::class, 'index'])->name('admin.stats.carry-forward.index');
        Route::get('fixed-costing', [AdminFixedCostingController::class, 'index'])->name('admin.stats.fixed-costing.index');
        Route::get('third-party-role', [AdminThirdPartyRoleController::class, 'index'])->name('admin.stats.third-party-role.index');
    });

    /******** Department Routes */
    Route::prefix('/admin/department')->group(function () {
        Route::get('', [AdminDepartmentController::class, 'index'])->name('admin.department.index');
        Route::post('/store', [AdminDepartmentController::class, 'store'])->name('admin.department.store');
        Route::get('/edit/{id?}', [AdminDepartmentController::class, 'edit'])->name('admin.department.edit');
        Route::post('/update/{id?}', [AdminDepartmentController::class, 'update'])->name('admin.department.update');
        Route::get('/destroy/{id?}', [AdminDepartmentController::class, 'destroy'])->name('admin.department.destroy');
        Route::post('/change-status', [AdminDepartmentController::class, 'change_status'])->name('admin.department.change.status');
        Route::get('/get-board-lists/{id?}', [AdminDepartmentController::class, 'get_board_lists'])->name('admin.department.get.board.lists');
    });
    /******** Department Routes */

    Route::resource('/admin/category', CategoryController::class);

    //Admin Account
    Route::resource('/admin/account', adminController::class)->except(['update']);
    Route::post('/admin/account/update/{id?}', [adminController::class, 'update'])->name('admin.account.update');


    //User_info_api Routes
    Route::resource('/admin/user_info_api', User_info_apiController::class);
    Route::get('/user_info_api_trashed', [User_info_apiController::class, 'trashed_user_info_api'])->name('trashed_user_info_api');
    Route::get('/user_info_api_restore/{id}', [User_info_apiController::class, 'restore'])->name('restore_user_info_api');
    Route::get('/user_info_api_restoreall', [User_info_apiController::class, 'restoreAll'])->name('restoreall_user_info_api');
    Route::get('/user_info_api_forcedelete/{id}', [User_info_apiController::class, 'user_info_api_forceDelete'])->name('user_info_api_forcedelete');
    Route::get('/user_info_api_changeStatus', [User_info_apiController::class, 'user_info_api_changeStatus'])->name('changeuser_info_apiStatus');
    //Website_views Routes
    Route::resource('/admin/website_view', Website_viewController::class);

    /**Board List Namings Routes */
    Route::prefix('/admin/board-list')->group(function () {
        Route::get('', [AdminBoardListController::class, 'index'])->name('admin.board.list.index');
        Route::get('/create', [AdminBoardListController::class, 'create'])->name('admin.board.list.create');
        Route::post('/store', [AdminBoardListController::class, 'store'])->name('admin.board.list.store');
        Route::get('/show/{id}', [AdminBoardListController::class, 'show'])->name('admin.board.list.show');
        Route::get('/edit/{id}', [AdminBoardListController::class, 'edit'])->name('admin.board.list.edit');
        Route::post('/update/{id}', [AdminBoardListController::class, 'update'])->name('admin.board.list.update');
        Route::get('/destroy/{id}', [AdminBoardListController::class, 'destroy'])->name('admin.board.list.destroy');
        Route::get('/restore/{id}', [AdminBoardListController::class, 'restore'])->name('admin.board.list.restore');
        Route::get('/trashed', [AdminBoardListController::class, 'trashed'])->name('admin.board.list.trashed');
        Route::get('/restore-all', [AdminBoardListController::class, 'restore_all'])->name('admin.board.list.restore.all');
        Route::get('/force-delete/{id}', [AdminBoardListController::class, 'force_delete'])->name('admin.board.list.force.delete');
        Route::post('/change-status', [AdminBoardListController::class, 'change_status'])->name('admin.board.list.change.status');
    });
    /**Board List Card Namings Routes */
    Route::prefix('/admin/board-list-cards')->group(function () {
        Route::get('', [AdminBoardListCardController::class, 'index'])->name('admin.board.list.cards.index');
        Route::get('/edit/{id}', [AdminBoardListCardController::class, 'edit'])->name('admin.board.list.cards.edit');
        Route::post('/update/{id}', [AdminBoardListCardController::class, 'update'])->name('admin.board.list.cards.update');
    });


    Route::resource('/admin/card', AdminCardController::class);
    Route::get('/card_changeStatus', [AdminCardController::class, 'card_changeStatus'])->name('changecardStatus');


    Route::resource('/admin/brand', BrandController::class);
    Route::get('/trashed', [BrandController::class, 'trashedbrand'])->name('trashedbrand');
    Route::get('/restore/{id}', [BrandController::class, 'restore'])->name('restorebrand');
    Route::get('/restoreall', [BrandController::class, 'restoreAll'])->name('restoreallbrand');
    Route::get('/forcedelete/{id}', [BrandController::class, 'brandforceDelete'])->name('brandforcedelete');
    Route::get('/changeBrandStatus', [BrandController::class, 'changeStatus']);

    //Team Routes
    Route::resource('/admin/team', AdminTeamController::class);
    Route::get('/team_trashed', [AdminTeamController::class, 'trashedteam'])->name('trashedteam');
    Route::get('/team_restore/{id}', [AdminTeamController::class, 'restoreteam'])->name('restoreteam');
    Route::get('/team_restore_all', [AdminTeamController::class, 'teamrestoreAll'])->name('restoreallteam');
    Route::get('teamchangeStatus', [AdminTeamController::class, 'teamchangeStatus']);
    Route::get('assignBrand', [AdminTeamController::class, 'assignBrand']);
    Route::get('teammember', [AdminTeamController::class, 'createTeamMember'])->name('creatTeam');
    Route::get('memberlist', [AdminTeamController::class, 'showMembers'])->name('memberList');
    Route::get('memberprofile/{id}', [AdminTeamController::class, 'showMemberProfile'])->name('memberProfile');
    Route::get('inactivememberlist', [AdminTeamController::class, 'showInactivemembers'])->name('inactivememberlist');
    Route::get('/changeMemberStatus', [AdminTeamController::class, 'changeMemberstatus']);
    Route::post('/createemployee', [AdminTeamController::class, 'create_employee'])->name('createEmployee');

    /******** Team Target Routes */
    Route::prefix('/admin/team-target')->group(function () {
        Route::get('', [AdminTeamTargetController::class, 'index'])->name('admin.team.target.index');
        Route::post('/store', [AdminTeamTargetController::class, 'store'])->name('admin.team.target.store');
        Route::get('/edit/{id?}', [AdminTeamTargetController::class, 'edit'])->name('admin.team.target.edit');
        Route::post('/update/{id?}', [AdminTeamTargetController::class, 'update'])->name('admin.team.target.update');
        Route::get('/destroy/{id?}', [AdminTeamTargetController::class, 'destroy'])->name('admin.team.target.destroy');
    });
    /******** Team Target Routes */

    /******** Team Fixed Costing Routes */
    Route::prefix('/admin/fixed-costing')->group(function () {
        Route::get('', [AdminFixedCostingController::class, 'index'])->name('admin.team.fixed-costing.index');
        Route::post('/store', [AdminFixedCostingController::class, 'store'])->name('admin.team.fixed-costing.store');
        Route::get('/edit/{id?}', [AdminFixedCostingController::class, 'edit'])->name('admin.team.fixed-costing.edit');
        Route::post('/update/{id?}', [AdminFixedCostingController::class, 'update'])->name('admin.team.fixed-costing.update');
        Route::get('/destroy/{id?}', [AdminFixedCostingController::class, 'destroy'])->name('admin.team.fixed-costing.destroy');
    });
    /******** Team Fixed Forward Routes */

    /******** Team Carry Forward Routes */
    Route::prefix('/admin/carry-forward')->group(function () {
        Route::get('', [AdminCarryForwardController::class, 'index'])->name('admin.team.carry-forward.index');
        Route::post('/store', [AdminCarryForwardController::class, 'store'])->name('admin.team.carry-forward.store');
        Route::get('/edit/{id?}', [AdminCarryForwardController::class, 'edit'])->name('admin.team.carry-forward.edit');
        Route::post('/update/{id?}', [AdminCarryForwardController::class, 'update'])->name('admin.team.carry-forward.update');
        Route::get('/destroy/{id?}', [AdminCarryForwardController::class, 'destroy'])->name('admin.team.carry-forward.destroy');
    });
    /******** Team Carry Forward Routes */

    /******** Team Indirect Costing Routes */
//    Route::prefix('/admin/indirect-costing')->group(function () {
//        Route::get('', [AdminIndirectCostingController::class, 'index'])->name('admin.team.indirect-costing.index');
//        Route::post('/store', [AdminIndirectCostingController::class, 'store'])->name('admin.team.indirect-costing.store');
//        Route::get('/edit/{id?}', [AdminIndirectCostingController::class, 'edit'])->name('admin.team.indirect-costing.edit');
//        Route::post('/update/{id?}', [AdminIndirectCostingController::class, 'update'])->name('admin.team.indirect-costing.update');
//        Route::get('/destroy/{id?}', [AdminIndirectCostingController::class, 'destroy'])->name('admin.team.indirect-costing.destroy');
//    });
    /******** Team Indirect Forward Routes */

    /******** Team Spending Routes */
    Route::prefix('/admin/team-spending')->group(function () {
        Route::get('', [AdminTeamSpendingController::class, 'index'])->name('admin.team.spending.index');
        Route::post('/store', [AdminTeamSpendingController::class, 'store'])->name('admin.team.spending.store');
        Route::get('/edit/{id?}', [AdminTeamSpendingController::class, 'edit'])->name('admin.team.spending.edit');
        Route::post('/update/{id?}', [AdminTeamSpendingController::class, 'update'])->name('admin.team.spending.update');
        Route::get('/destroy/{id?}', [AdminTeamSpendingController::class, 'destroy'])->name('admin.team.spending.destroy');
    });
    /******** Team Spending Routes */

    Route::post('admin/assign-unassign-user-brand-email/{id}', [AdminTeamController::class, 'assign_unassign_brand_emails'])->name('admin.assign.unassign.user.brand.email');


    Route::get('/editemployee/{id}', [AdminTeamController::class, 'edit_employee'])->name('editemployee');
    Route::put('/updateemployee/{id}', [AdminTeamController::class, 'update_employee']);
    Route::post('/update-employee-pass', [AdminTeamController::class, 'update_employee_pass'])->name('update_employee_pass');

    /******** Customer Sheet Routes */
    Route::prefix('/admin/customer-sheets')->group(function () {
        Route::get('', [AdminCustomerSheetController::class, 'index'])->name('admin.customer.sheet.index');
        Route::post('/store', [AdminCustomerSheetController::class, 'store'])->name('admin.customer.sheet.store');
        Route::get('/edit/{id}', [AdminCustomerSheetController::class, 'edit'])->name('admin.customer.sheet.edit');
        Route::post('/update/{id}', [AdminCustomerSheetController::class, 'update'])->name('admin.customer.sheet.update');
        Route::get('/view-attachment/{id}', [AdminCustomerSheetController::class, 'view_attachment'])->name('admin.customer.sheet.view.attachment');
        Route::post('/add-attachment/{id}', [AdminCustomerSheetController::class, 'add_attachment'])->name('admin.customer.sheet.add.attachment');
        Route::get('/destroy-attachment/{id}', [AdminCustomerSheetController::class, 'destroy_attachment'])->name('admin.customer.sheet.attachment.destroy');
        Route::get('/destroy/{id}', [AdminCustomerSheetController::class, 'destroy'])->name('admin.customer.sheet.destroy');
        Route::get('/restore/{id}', [AdminCustomerSheetController::class, 'restore'])->name('admin.customer.sheet.restore');
        Route::get('/trashed', [AdminCustomerSheetController::class, 'trashed'])->name('admin.customer.sheet.trashed');
        Route::get('/restore-all', [AdminCustomerSheetController::class, 'restore_all'])->name('admin.customer.sheet.restore.all');
        Route::get('/force-delete/{id}', [AdminCustomerSheetController::class, 'force_delete'])->name('admin.customer.sheet.force.delete');
        Route::get('/logs', [AdminCustomerSheetController::class, 'log'])->name('admin.customer.sheet.log');
    });
    /******* Customer Sheet Routes */

    /******** Third Party Role Routes */
    Route::prefix('/admin/third-party-roles')->group(function () {
        Route::get('', [AdminThirdPartyRoleController::class, 'index'])->name('admin.third.party.role.index');
        Route::get('/get-teams-agents-and-clients/{team_key?}', [AdminThirdPartyRoleController::class, 'get_teams_agents_and_clients'])->name('admin.third.party.role.team.agents.clients');
        Route::get('/get-client-paid-invoices/{team_key?}/{client_id?}', [AdminThirdPartyRoleController::class, 'get_client_paid_invoices'])->name('admin.third.party.client.paid.invoices');
        Route::post('/store', [AdminThirdPartyRoleController::class, 'store'])->name('admin.third.party.role.store');
        Route::get('/edit/{id?}', [AdminThirdPartyRoleController::class, 'edit'])->name('admin.third.party.role.edit');
        Route::post('/update/{id?}', [AdminThirdPartyRoleController::class, 'update'])->name('admin.third.party.role.update');
        Route::get('/destroy/{id}', [AdminThirdPartyRoleController::class, 'destroy'])->name('admin.third.party.role.destroy');
        Route::get('/restore/{id}', [AdminThirdPartyRoleController::class, 'restore'])->name('admin.third.party.role.restore');
        Route::get('/trashed', [AdminThirdPartyRoleController::class, 'trashed'])->name('admin.third.party.role.trashed');
        Route::get('/restore-all', [AdminThirdPartyRoleController::class, 'restore_all'])->name('admin.third.party.role.restore.all');
        Route::get('/force-delete/{id}', [AdminThirdPartyRoleController::class, 'force_delete'])->name('admin.third.party.role.force.delete');
        Route::get('/logs', [AdminThirdPartyRoleController::class, 'log'])->name('admin.third.party.role.log');
    });
    /******* Third Party Role Routes */

    Route::prefix('/admin/email-configurations')->group(function () {
        Route::get('', [AdminEmailConfigurationController::class, 'index'])->name('admin.email.configuration.index');
        Route::get('/create', [AdminEmailConfigurationController::class, 'create'])->name('admin.email.configuration.create');
        Route::post('/store', [AdminEmailConfigurationController::class, 'store'])->name('admin.email.configuration.store');

        Route::get('/show/{id}', [AdminEmailConfigurationController::class, 'show'])->name('admin.email.configuration.show');
        Route::get('/edit/{id}', [AdminEmailConfigurationController::class, 'edit'])->name('admin.email.configuration.edit');
        Route::post('/update/{id}', [AdminEmailConfigurationController::class, 'update'])->name('admin.email.configuration.update');
        Route::get('/destroy/{id}', [AdminEmailConfigurationController::class, 'destroy'])->name('admin.email.configuration.destroy');
        Route::get('/restore/{id}', [AdminEmailConfigurationController::class, 'restore'])->name('admin.email.configuration.restore');
        Route::get('/trashed', [AdminEmailConfigurationController::class, 'trashed'])->name('admin.email.configuration.trashed');
        Route::get('/restore-all', [AdminEmailConfigurationController::class, 'restore_all'])->name('admin.email.configuration.restore.all');
        Route::get('/force-delete/{id}', [AdminEmailConfigurationController::class, 'force_delete'])->name('admin.email.configuration.force.delete');
        Route::post('/change-status', [AdminEmailConfigurationController::class, 'change_status'])->name('admin.email.configuration.change.status');
    });
//    Route::prefix('/admin/zoom-configurations')->group(function () {
//        Route::get('', [AdminZoomConfigurationController::class, 'index'])->name('admin.zoom.configuration.index');
//        Route::get('/create', [AdminZoomConfigurationController::class, 'create'])->name('admin.zoom.configuration.create');
//        Route::post('/store', [AdminZoomConfigurationController::class, 'store'])->name('admin.zoom.configuration.store');
//
//        Route::get('/phone-numbers/{id}', [AdminZoomConfigurationController::class, 'phone_numbers'])->name('admin.zoom.configuration.phone.numbers');
//        Route::get('/fetch-call-logs/{id}', [AdminZoomConfigurationController::class, 'fetch_call_logs'])->name('admin.zoom.configuration.fetch.call.logs');
//        Route::post('/fetch-more-call-logs', [AdminZoomConfigurationController::class, 'fetch_more_call_logs'])->name('admin.zoom.configuration.fetch.more.call.logs');
//        Route::get('/show/{id}', [AdminZoomConfigurationController::class, 'show'])->name('admin.zoom.configuration.show');
//        Route::get('/edit/{id}', [AdminZoomConfigurationController::class, 'edit'])->name('admin.zoom.configuration.edit');
//        Route::post('/update/{id}', [AdminZoomConfigurationController::class, 'update'])->name('admin.zoom.configuration.update');
//        Route::get('/destroy/{id}', [AdminZoomConfigurationController::class, 'destroy'])->name('admin.zoom.configuration.destroy');
//        Route::get('/restore/{id}', [AdminZoomConfigurationController::class, 'restore'])->name('admin.zoom.configuration.restore');
//        Route::get('/trashed', [AdminZoomConfigurationController::class, 'trashed'])->name('admin.zoom.configuration.trashed');
//        Route::get('/restore-all', [AdminZoomConfigurationController::class, 'restore_all'])->name('admin.zoom.configuration.restore.all');
//        Route::get('/force-delete/{id}', [AdminZoomConfigurationController::class, 'force_delete'])->name('admin.zoom.configuration.force.delete');
//        Route::post('/change-status', [AdminZoomConfigurationController::class, 'change_status'])->name('admin.zoom.configuration.change.status');
//    });

    /** Google Call Back*/
    Route::get('/redirect/google/{id?}', [GoogleSocialiteController::class, 'redirect'])->name('google.redirect');
    Route::get('/callback/google', [GoogleSocialiteController::class, 'handle_call_back'])->name('handle.google.call.back');

    // PPC Spendings
    Route::resource('/admin/spending', SpendingController::class);
    Route::get('/admin/spending/create', [SpendingController::class, 'create'])->name('spending_create');

    // Projects
    Route::resource('/admin/adminproject', AdminProjectController::class);

    //Lead Routes
    Route::get('/admin/leads', [LeadController::class, 'index'])->name('admin.leads.index');
    Route::resource('/admin/lead', LeadController::class);
    Route::get('/admin/lead-YD_index', [LeadController::class, 'YD_index'])->name('admin.lead.YD_index');
    Route::post('/teamLead', [LeadController::class, 'team'])->name('teamleads');
    Route::post('/brandLead', [LeadController::class, 'brand'])->name('brandleads');
    Route::post('/month', [LeadController::class, 'monthlydata'])->name('monthlydata');
    Route::get('/changeleadStatus', [LeadController::class, 'leadStatus'])->name('changeleadStatus');
    Route::get('/trashedlead', [LeadController::class, 'onlyTrashedlead'])->name('onlyTrashedlead');
    Route::put('/leadRestore/{id}', [LeadController::class, 'leadRestore'])->name('leadRestore');
    Route::delete('/leadforceDelete/{id}', [LeadController::class, 'leadforceDelete'])->name('leadforceDelete');
    Route::post('/deleteLeads', [LeadController::class, 'delete_leads'])->name('deleteLeads');

    //Lead Status
    Route::resource('/admin/leadstatus', LeadStatusController::class)->except(['update']);
    Route::post('/admin/leadstatus/update/{leadStatus?}', [LeadStatusController::class, 'update'])->name('admin.lead_status.update');


    //Lead Comments
    Route::get('leadcomments/{id}', [LeadController::class, 'get_lead_comments'])->name('leadComments');
    Route::post('/admincreatecomments', [LeadController::class, 'admin_create_comments'])->name('adminCreateComments');


    //Admin Client Routes
    Route::resource('/admin/clientadmin', adminClientController::class);
    Route::post('/admin/client_add_phone', [adminClientController::class, 'client_add_phone'])->name('client_add_phone');
    Route::any('admin/client_destroy_phone/{id?}', [adminClientController::class, 'client_destroy_phone'])->name('client_destroy_phone');
    // Admin Invoice Routes
    Route::get('/admin/invoices', [adminInvoiceController::class, 'index'])->name('admin.invoices.index');
    Route::resource('/admin/invoiceadmin', adminInvoiceController::class);
    Route::get('/admin/view-payment-invoice/{invoice?}', [adminInvoiceController::class, 'view_payment_invoice'])->name('view_payment_invoice');
    Route::post('/teamInvoice', [adminInvoiceController::class, 'team_Invoices'])->name('teamInvoices');
    Route::post('/brandInvoices', [adminInvoiceController::class, 'brand_Invoicess'])->name('brandInvoicess');
    Route::post('/brandMember', [adminInvoiceController::class, 'brandteamAgent'])->name('brandteamAgent'); // lead page [show team brands]
    Route::post('/admincreateinvoice', [adminInvoiceController::class, 'admin_create_invoice'])->name('adminStoreInvoice');
    Route::get('/translog/{id?}', [adminInvoiceController::class, 'payment_trans_log'])->name('transLog');

    // Admin Ip Address Controller
    Route::prefix('/admin/ip-address')->group(function () {
        Route::get('', [AdminIpAddressController::class, 'index'])->name('admin.ip.address.index');
        Route::get('/create', [AdminIpAddressController::class, 'create'])->name('admin.ip.address.create');
        Route::post('/store', [AdminIpAddressController::class, 'store'])->name('admin.ip.address.store');
        Route::get('/show/{id}', [AdminIpAddressController::class, 'show'])->name('admin.ip.address.show');
        Route::get('/edit/{id}', [AdminIpAddressController::class, 'edit'])->name('admin.ip.address.edit');
        Route::post('/update/{id}', [AdminIpAddressController::class, 'update'])->name('admin.ip.address.update');
        Route::get('/destroy/{id}', [AdminIpAddressController::class, 'destroy'])->name('admin.ip.address.destroy');
        Route::get('/restore/{id}', [AdminIpAddressController::class, 'restore'])->name('admin.ip.address.restore');
        Route::get('/trashed', [AdminIpAddressController::class, 'trashed'])->name('admin.ip.address.trashed');
        Route::get('/restore-all', [AdminIpAddressController::class, 'restore_all'])->name('admin.ip.address.restore.all');
        Route::get('/force-delete/{id}', [AdminIpAddressController::class, 'force_delete'])->name('admin.ip.address.force.delete');
        Route::post('/change-status', [AdminIpAddressController::class, 'change_status'])->name('admin.ip.address.change.status');
    });

    // Payment Method Authorize.net Routes
    Route::prefix('/admin/payment-method/authorize')->group(function () {
        Route::get('', [PaymentMethodController::class, 'index'])->name('admin.payment.method.authorize.index');
        Route::get('/create', [PaymentMethodController::class, 'create'])->name('admin.payment.method.authorize.create');
        Route::post('/store', [PaymentMethodController::class, 'store'])->name('admin.payment.method.authorize.store');
        Route::get('/show/{id}', [PaymentMethodController::class, 'show'])->name('admin.payment.method.authorize.show');
        Route::get('/edit/{id}', [PaymentMethodController::class, 'edit'])->name('admin.payment.method.authorize.edit');
        Route::post('/update/{id?}', [PaymentMethodController::class, 'update'])->name('admin.payment.method.authorize.update');
        Route::get('/destroy/{id}', [PaymentMethodController::class, 'destroy'])->name('admin.payment.method.authorize.destroy');
        Route::get('/change/mode', [PaymentMethodController::class, 'changeMode'])->name('admin.payment.method.authorize.change.mode');
        Route::get('/change/status', [PaymentMethodController::class, 'changeStatus'])->name('admin.payment.method.authorize.change.status');
        Route::get('/change/authorization', [PaymentMethodController::class, 'changeAuthorization'])->name('admin.payment.method.authorize.authorization');
        Route::get('/held_trans_list/{id}', [PaymentMethodController::class, 'get_held_trans_list'])->name('admin.payment.method.authorize.held.transaction');
        Route::get('/approved_held_trans/{id}', [PaymentMethodController::class, 'approved_all_held_trans'])->name('admin.payment.method.authorize.approved.held.transaction');
    });

    // Payment Method Expigate Routes
    Route::prefix('/admin/payment-method/expigate')->group(function () {
        Route::get('', [PaymentMethodExpigateController::class, 'index'])->name('admin.payment.method.expigate.index');
        Route::get('/create', [PaymentMethodExpigateController::class, 'create'])->name('admin.payment.method.expigate.create');
        Route::post('/store', [PaymentMethodExpigateController::class, 'store'])->name('admin.payment.method.expigate.store');
        Route::get('/show/{id}', [PaymentMethodExpigateController::class, 'show'])->name('admin.payment.method.expigate.show');
        Route::get('/edit/{id}', [PaymentMethodExpigateController::class, 'edit'])->name('admin.payment.method.expigate.edit');
        Route::post('/update/{id}', [PaymentMethodExpigateController::class, 'update'])->name('admin.payment.method.expigate.update');
        Route::get('/destroy/{id}', [PaymentMethodExpigateController::class, 'destroy'])->name('admin.payment.method.expigate.destroy');
        Route::get('/change/mode', [PaymentMethodExpigateController::class, 'changeMode'])->name('admin.payment.method.expigate.change.mode');
        Route::get('/change/status', [PaymentMethodExpigateController::class, 'changeStatus'])->name('admin.payment.method.expigate.change.status');
    });

    // Payment Method PayArc Routes
    Route::prefix('/admin/payment-method/payarc')->group(function () {
        Route::get('', [PaymentMethodPayArcController::class, 'index'])->name('admin.payment.method.payarc.index');
        Route::get('/create', [PaymentMethodPayArcController::class, 'create'])->name('admin.payment.method.payarc.create');
        Route::post('/store', [PaymentMethodPayArcController::class, 'store'])->name('admin.payment.method.payarc.store');
        Route::get('/show/{id}', [PaymentMethodPayArcController::class, 'show'])->name('admin.payment.method.payarc.show');
        Route::get('/edit/{id}', [PaymentMethodPayArcController::class, 'edit'])->name('admin.payment.method.payarc.edit');
        Route::post('/update/{id}', [PaymentMethodPayArcController::class, 'update'])->name('admin.payment.method.payarc.update');
        Route::get('/destroy/{id}', [PaymentMethodPayArcController::class, 'destroy'])->name('admin.payment.method.payarc.destroy');
        Route::get('/change/mode', [PaymentMethodPayArcController::class, 'changeMode'])->name('admin.payment.method.payarc.change.mode');
        Route::get('/change/status', [PaymentMethodPayArcController::class, 'changeStatus'])->name('admin.payment.method.payarc.change.status');
    });

    Route::prefix('/admin/payment-transaction-logs')->group(function () {
        Route::get('', [PaymentTransactionLogController::class, 'index'])->name('admin.payment.transaction.log.index');
    });
    Route::prefix('/admin/payment-multiple-responses')->group(function () {
        Route::get('', [PaymentMultipleResponseController::class, 'index'])->name('admin.payment.multiple.response.index');
    });

    Route::prefix('/admin/authorize')->group(function () {
        Route::get('check-payment-status/{id?}', [AuthorizePaymentController::class, 'check_payment_status'])->name('admin.check.payment.status');
    });

    //Payment Route
    Route::resource('/admin/paymentadmin', adminPaymentController::class);
    Route::get('/admin/payments/unsettled', [adminPaymentController::class, 'unsettled_payments'])->name('admin.payment.unsettled.index');
    /******** Wire Payment Routes Start *******/
    Route::prefix('/admin/wire-payments')->group(function () {
        Route::get('', [AdminWirePaymentController::class, 'index'])->name('admin.wire.payments.index');
        Route::post('/store', [AdminWirePaymentController::class, 'store'])->name('admin.wire.payment.store');
        Route::post('/payment-approval/{id?}', [AdminWirePaymentController::class, 'payment_approval'])->name('admin.wire.payment.change.approval.status');
        Route::get('/view-attachment/{wire_payment?}', [AdminWirePaymentController::class, 'view_attachment'])->name('admin.wire.payment.view.attachment');
        Route::get('/brand-agents/{id?}', [AdminWirePaymentController::class, 'brand_agents'])->name('admin.wire.payments.brand.agents');
    });
    /******** Wire Payment Routes End *******/
    Route::post('/refundpayment', [adminPaymentController::class, 'refund_payment'])->name('refundPayment');
    Route::post('/teamPayment', [adminPaymentController::class, 'team_Payment'])->name('teamPayment');
    Route::post('/brandPayment', [adminPaymentController::class, 'brand_Payment'])->name('brandPayment');
    Route::get('/adminrefunds', [adminPaymentController::class, 'show_payment_refund'])->name('adminRefundList'); //client side Payment list
    Route::get('/adminPaymentTeamMember/{id}', [adminPaymentController::class, 'paymentTeamAgent'])->name('paymentteamAgent');
    Route::post('/admincreatepayment', [adminPaymentController::class, 'admin_direct_payment'])->name('admin_add_Payment');

    Route::post('/admin-create-upsale-mutli-payment', [PaymentController::class, 'upsale_multi_payment'])->name('admin_upsale_multi_payment');
    Route::post('/admin-create-upsale-direct-payment', [PaymentController::class, 'upsale_direct_payment'])->name('admin_upsale_direct_payment');
    Route::post('/admin-create-client-payment', [adminPaymentController::class, 'admin_create_payment'])->name('adminCreateprojectPayment'); //Create Project/Client payment
    Route::post('/admin-create-upsale-payment', [App\Http\Controllers\PaymentController::class, 'upsale_payment'])->name('adminUsalePayment'); //Create Project/Client payment
    Route::get('/admin_client_card_info/{id?}', [App\Http\Controllers\PaymentController::class, 'get_client_card_info'])->name('admin.client.card.info.active.status');
    Route::get('/admin_client_card_info_inactive_status/{id?}', [App\Http\Controllers\PaymentController::class, 'get_client_card_info_inactive_status'])->name('admin.client.card.info.inactive.status');

    /** Split Payments Routes Start*/
    Route::get('/admin/split-payments', [AdminSplitPaymentController::class, 'index'])->name('admin_split_payments');
    Route::get('/admin/pay-now-split-payments/{id?}', [AdminSplitPaymentController::class, 'pay_now_split_payments'])->name('admin_pay_now_split_payments');
    /** Split Payments Routes End*/
    //General Routes
    Route::get('/clear-cache-all', [generalController::class, 'clearCache'])->name('clearCache');
    Route::get('/cronjob-calllogs', [generalController::class, 'cronjob_call_logs'])->name('cronjob_call_logs');


});
Route::get('/merchant-payment', [MerchantFeeAndTaxPaymentController::class, 'MerchantFeeAndTaxPayment'])->name('MerchantFeeAndTaxPayment');

require __DIR__ . '/adminauth.php';

//without Login Payment Routes
Route::resource('/payment', App\Http\Controllers\PaymentController::class);
Route::get('/pay', [App\Http\Controllers\PaymentController::class, 'store'])->name('pay');

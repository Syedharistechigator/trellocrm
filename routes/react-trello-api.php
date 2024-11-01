<?php

use App\Http\Middleware\ApiVersion;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Board\V1\{
    ApiTeamController as ApiTeamControllerV1,
    ApiDepartmentController as ApiDepartmentControllerV1,
    ApiBoardListController as ApiBoardListControllerV1,
    ApiBoardListCardController as ApiBoardListCardControllerV1,
    ApiBoardListCardNotificationController as ApiBoardListCardNotificationControllerV1,
    ApiBoardListCardDescriptionController as ApiBoardListCardDescriptionControllerV1,
    ApiBoardListCardUserController as ApiBoardListCardUserControllerV1,
    ApiBoardListCardAttachmentController as ApiBoardListCardAttachmentControllerV1,
    ApiBoardListCardCommentController as ApiBoardListCardCommentControllerV1,
    ApiBoardListCardTitleController as ApiBoardListCardTitleControllerV1,
    ApiBoardListCardCoverImageController as ApiBoardListCardCoverImageControllerV1,
    ApiBoardListCardLabelController as ApiBoardListCardLabelControllerV1,
    ApiBoardListCardDateController as ApiBoardListCardDateControllerV1,
    ApiClientController as ApiClientControllerV1,
    ApiUserController as ApiUserControllerV1
};

Route::middleware([ApiVersion::class])->group(function () {
    Route::prefix('board')->group(function () {
        Route::prefix('v1')->group(function () {
            Route::middleware('auth:sanctum')->group(function () {
                /** Departments */
                Route::controller(ApiDepartmentControllerV1::class)->prefix('departments')->group(function () {
                    $DEPARTMENTS = Route::get('', 'index');
                });
                /** Teams */
                Route::controller(ApiTeamControllerV1::class)->prefix('teams')->group(function () {
                    $TEAMS = Route::get('', 'index');
                });
                /** Board List */
                Route::controller(ApiBoardListControllerV1::class)->prefix('board-lists')->group(function () {
                    $BOARD_LISTS = Route::get('', 'index');
                    $BOARD_LIST_SCROLL = Route::get('fetch-more-cards/{id?}', 'scroll_board_list');
                });
                /** Board List Card */
                Route::prefix('board-list-cards')->group(function () {
                    Route::controller(ApiBoardListCardControllerV1::class)->group(function () {
                        $BOARD_LIST_CARD_STORE = Route::post('/store', 'store');
                        $BOARD_LIST_CARD_BY_ID = Route::get('/show/{id?}', 'show');
                        $BOARD_LIST_CARD_BY_ID = Route::get('/show_debug/{id?}', 'show_debug');
                        $BOARD_LIST_CARD_BY_ID = Route::get('/show-raw/{id?}', 'show_raw');
                        $BOARD_LIST_CARD_SEARCH = Route::get('/search', 'search');
//                      $MOVED_BOARD_LIST_CARD = Route::post('/move-board-card', 'move_card');
//                      $MOVED_BOARD_LIST_CARD_2 = Route::post('/move-card', 'move_card_2');
                        $MOVED_BOARD_LIST_CARD_3 = Route::post('/move-card', 'move_card_3');
                        $MOVED_BOARD_LIST_CARD_MOVE_CARD_ON_DRAG = Route::post('/move-card-on-dropdown', 'move_card_on_dropdown');
                        $BOARD_LIST_CARD_CHANGE_CLIENT = Route::post('/change-client', 'change_client');
                        $BOARD_LIST_CARD_CHANGE_TEAM = Route::post('/change-team', 'change_team');
                        $BOARD_LIST_CARD_TASK_COMPLETED = Route::post('/task-completed', 'task_completed');
                        $BOARD_LIST_CARD_DELETE = Route::post('/delete', 'delete');
                        $BOARD_LIST_CARD_RESTORE = Route::post('/restore', 'restore');
                    });
                    Route::controller(ApiBoardListCardNotificationControllerV1::class)->prefix('notifications')->group(function () {
                        $BOARD_LIST_CARD_NOTIFICATIONS = Route::get('/all', 'index');
                        $BOARD_LIST_CARD_NOTIFICATION_MARK_AS_READ = Route::post('/mark-as-read', 'mark_as_read');
                        $BOARD_LIST_CARD_NOTIFICATION_MARK_ALL_AS_READ = Route::get('/mark-all-as-read', 'mark_all_as_read');
                    });
                    Route::controller(ApiBoardListCardDescriptionControllerV1::class)->group(function () {
                        $BOARD_LIST_CARD_CREATE_DESCRIPTION = Route::post('/add-description', 'add_description');
                        $BOARD_LIST_CARD_UPDATE_DESCRIPTION = Route::post('/description/update', 'update_description');
                    });
                    Route::controller(ApiBoardListCardUserControllerV1::class)->group(function () {
                        $BOARD_LIST_CARD_ASSIGNED_UNASSIGN_MEMBER = Route::post('/assign-unassign-member', 'assign_unassign_member');
                    });
                    Route::controller(ApiBoardListCardCoverImageControllerV1::class)->prefix('cover-image')->group(function () {
                        $BOARD_LIST_CARD_CREATE_COVER_IMAGE = Route::post('/create', 'create');
                        $BOARD_LIST_CARD_UPDATE_COVER_IMAGE = Route::post('/update', 'update');
                        $BOARD_LIST_CARD_UPDATE_COVER_IMAGE_BACKGROUND_COLOR = Route::post('/update-background-color', 'update_cover_background_color');
                        $BOARD_LIST_CARD_SET_ATTACHMENT_AS_COVER_IMAGE = Route::post('/attachment-as-cover-image', 'set_attachment_as_cover_image');
                        $BOARD_LIST_CARD_REMOVE_COVER_IMAGE = Route::post('/remove', 'remove');
                    });
                    Route::controller(ApiBoardListCardTitleControllerV1::class)->prefix('title')->group(function () {
                        $BOARD_LIST_CARD_CREATE_TITLE = Route::post('/create', 'create');
                        $BOARD_LIST_CARD_UPDATE_TITLE = Route::post('/update', 'update');
                        $BOARD_LIST_CARD_DELETE_TITLE = Route::post('/delete', 'delete');
                    });
                    Route::controller(ApiBoardListCardAttachmentControllerV1::class)->group(function () {
                        $BOARD_LIST_CARD_CREATE_ATTACHMENT = Route::post('/add-attachments', 'add_attachments');
                        $BOARD_LIST_CARD_ATTACHMENT_BY_ID = Route::get('/attachment/show/{id?}', 'show');
                        $BOARD_LIST_CARD_DELETE_ATTACHMENT = Route::post('/attachment/delete', 'delete');
                    });
                    Route::controller(ApiBoardListCardCommentControllerV1::class)->prefix('comment')->group(function () {
                        $BOARD_LIST_CARD_COMMENT_BY_ID = Route::get('/show/{id?}', 'show');
                        $BOARD_LIST_CARD_CREATE_COMMENT = Route::post('/create', 'create');
                        $BOARD_LIST_CARD_UPDATE_COMMENT = Route::post('/update', 'update');
                        $BOARD_LIST_CARD_DELETE_COMMENT = Route::post('/delete', 'delete');
                    });
                    Route::controller(ApiBoardListCardLabelControllerV1::class)->prefix('label')->group(function () {
                        $BOARD_LIST_CARD_LABEL_BY_ID = Route::get('/show/{id?}', 'show');
                        $BOARD_LIST_CARD_CREATE_LABEL = Route::post('/create', 'create');
                        $BOARD_LIST_CARD_UPDATE_LABEL = Route::post('/update', 'update');
                        $BOARD_LIST_CARD_ASSIGN_UNASSIGN_LABEL = Route::post('/assign-unassign', 'assign_unassign');
                        $BOARD_LIST_CARD_DELETE_LABEL = Route::post('/delete', 'delete');
                    });
                    Route::controller(ApiBoardListCardDateControllerV1::class)->prefix('date')->group(function () {
                        $BOARD_LIST_CARD_UPDATE_DATE = Route::post('/update', 'update');
                        $BOARD_LIST_CARD_REMOVE_DATE = Route::post('/remove', 'remove');
                    });
                });
                /** Client */
                Route::controller(ApiClientControllerV1::class)->prefix('clients')->group(function () {
                    $BOARD_CLIENT = Route::get('', 'index');
                });
                /** User */
                Route::controller(ApiUserControllerV1::class)->prefix('users')->group(function () {
                    $BOARD_USERS = Route::get('', 'index');
                });
            });
        });


        /** Version 2 Example */
//    Route::prefix('v2')->group(function () {
//        Route::controller(ApiBoardListControllerV2::class)->prefix('board-lists')->group(function() {
//            Route::get('', 'index');
//        });
//    });
    });
});

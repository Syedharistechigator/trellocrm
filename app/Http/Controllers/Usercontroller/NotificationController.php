<?php

namespace App\Http\Controllers\Usercontroller;

use App\Http\Controllers\Controller;
use App\Notifications\BoardListNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NotificationController extends Controller
{
    public function mark_all_as_read(Request $request)
    {
        try {
            $rules = [
                'tab' => 'required',
            ];
            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            if ($request->get('tab') === 'board-notifications') {
//                auth()->user()
//                    ->unreadNotifications()
//                    ->where('type', BoardListNotification::class)
//                    ->update(['read_at' => now()]);
            }
            return response()->json(['success' => 'Marked notifications as read.']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Unable to mark notification as read', 'message' => $e->getMessage(), 'line' => $e->getLine()],500);
        }
    }
}

<?php

namespace App\Http\Controllers\Usercontroller;

use App\Http\Controllers\Controller;
use App\Models\AssignUserBrandEmail;
use App\Models\EmailConfiguration;
use App\Models\LogAction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Team;
use App\Models\Card;
use App\Models\User;
use Carbon\Carbon;
use App\Models\Payment;
use App\Models\Project;
use App\Models\Task;
use DB;
use Illuminate\Support\Facades\Validator;

class TeamController extends Controller
{
    // Team Index
    public function index()
    {
        $user = Auth::user();
        $team_key = Auth::user()->team_key;
        $email_configurations = null;
        if (Auth::user()->type === 'lead') {
            $member_brand_keys = optional(Auth::user()->getTeamBrands)->pluck('brand_key')->toArray() ?? [];
            $email_configurations = EmailConfiguration::whereIn('brand_key', $member_brand_keys)->where('status', 1)->get();
        } elseif (Auth::user()->type === 'ppc') {
            $email_configurations = EmailConfiguration::where('status', 1)->get();
        }
        //$membersData = User::where(['team_key' => $team_key , 'status' => 1, 'type' => 'staff'])->orderBy('type', 'asc')->get();
        $membersData = User::where(['team_key' => $team_key, 'status' => 1])
            ->where('type', '!=', 'client')
            ->orderBy('type', 'asc')->get();
        $members = array();

        foreach ($membersData as $m) {

            $teamAmount = Payment::where(
                [
                    'team_key' => $team_key,
                    'response_code' => 1,
                    'agent_id' => $m->id
                ])->whereMonth('created_at', Carbon::now()->month)->sum('amount');

            settype($teamAmount, "integer");
            $m['amount'] = $teamAmount;
            if ($m->target > 0) {
                $m['percentage'] = ($teamAmount * 100) / $m->target;
            } else {
                $m['percentage'] = 0;
            }
            $m['email_configuration_ids'] = optional($email_configurations)->pluck('id')->toArray();
            $members[] = $m;
            $m_assign_email_ids = AssignUserBrandEmail::where('user_id', $m->id)->get()->pluck('email_configuration_id')->toArray();
            $m_email_configurations = EmailConfiguration::whereIn('id', $m_assign_email_ids)->orwhereIn('id', $email_configurations->pluck('id'))->where('status', 1)->get();
            $member_email_configurations = [];
            if ($m_email_configurations->isNotEmpty()) {
                $member_email_configurations[$m->id] = $m_email_configurations;
            }
        }
        return view('team.index', compact('member_email_configurations', 'email_configurations', 'members'));
    }

    public function fetch_member_emails($id)
    {
        try {
            $email_configuration_ids = User::findOrFail(substr($id, 2, -2))->getUserBrandEmails()->pluck('email_configurations.id')->toArray();
            $member_brand_keys = optional(Auth::user()->getTeamBrands)->pluck('brand_key')->toArray() ?? [];

            if (Auth::user()->type === 'lead') {
                $fetch_email_configurations = EmailConfiguration::where('email', 'not like', '%dev%')
                    ->where(function ($query) use ($member_brand_keys, $email_configuration_ids) {
                        $query->whereIn('brand_key', $member_brand_keys)->orWhereIn('id', $email_configuration_ids);
                    })
                    ->with(['getBrand' => function ($query) {
                        $query->select('id', 'brand_key', 'name', 'brand_url');
                    }])
                    ->where('status', 1)->get(['id', 'parent_id', 'brand_key', 'email', 'status'])->sortBy('getBrand.name');
            } elseif (Auth::user()->type === 'ppc') {
                $fetch_email_configurations = EmailConfiguration::where('status', 1)->get(['id', 'parent_id', 'brand_key', 'email', 'status']);
            }
            $email_configurations = [];
            foreach ($fetch_email_configurations as $fetch_email_configuration) {
                $brandName = $fetch_email_configuration->getBrand->name;
                $email_configurations[$brandName . ' - ' . $fetch_email_configuration->brand_key . ' - <a href="' . $fetch_email_configuration->getBrand->brand_url . '" target="_blank"><i class="zmdi zmdi-link"></i> ' . $fetch_email_configuration->getBrand->brand_url . '</a>'][] = $fetch_email_configuration;
            }

            return response()->json(['status' => 1, 'email_configuration_ids' => $email_configuration_ids, 'email_configurations' => $email_configurations]);
        } catch (\Exception $e) {
            return response()->json(['status' => 0, 'error' => $e->getMessage()], 500);
        }
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

            if (!in_array(Auth::user()->type, ['lead', 'ppc'])) {
                return response()->json(['errors' => 'Oops! You don\'t have access to do it.'], 422);
            }
            $remove_brand_key = $remove_email_id = false;
            $allowed_email_configuration_ids = $member_brand_keys = [];
            $assign = 0;
            $user = User::where('id', substr($id, 2, -2))->where('type', '!=', 'client')->where('status', 1)->first();

            if (Auth::user()->type === 'lead') {
                $member_brand_keys = optional($user->getTeamBrands)->pluck('brand_key')->toArray() ?? [];
                $allowed_email_configuration_ids = EmailConfiguration::whereIn('brand_key', $member_brand_keys)->pluck('id')->toArray();
            }
            /** Uncomment to allow ppc user to assign brand's emails to teammates */
//            elseif (Auth::user()->type === 'ppc') {
//                $allowed_email_configuration_ids = EmailConfiguration::pluck('id')->toArray();
//            }


            if ($user) {
                $email_configuration_ids = AssignUserBrandEmail::where('user_id', $user->id)->get()->pluck('email_configuration_id')->toArray();

                $unchange_check = AssignUserBrandEmail::where('user_id', $user->id)->where('email_configuration_id', $request->email_id)->first();
                if ($request->input('checked') == 'false' && !$unchange_check){
                    $assign = 0;
                } elseif ($request->input('checked') == 'true' && $unchange_check){
                    $assign = 1;
                }
                $email_configuration_external_brand = EmailConfiguration::where('id', $request->email_id)->where('status', 1)->first();

                $brand_key = $email_configuration_external_brand->brand_key;

                if (!$unchange_check && !in_array($email_configuration_external_brand->brand_key, $member_brand_keys)) {
                    $remove_email_id = true;
                    $remove_brand_key = true;
                    $brand_key = $email_configuration_external_brand->brand_key;
                } elseif (!$unchange_check && !in_array($request->email_id, $allowed_email_configuration_ids)) {
                    $remove_email_id = true;
                }
                if (($request->input('checked') == 'false' && !$unchange_check) || ($request->input('checked') == 'true' && $unchange_check)) {
                    return response()->json([
                        'success' => 'User brand email already changed.',
                        'status' => 1,
                        'assign' => $assign,
                        'action' => 'unchanged',
                        'email_id' => $request->email_id,
                        'brand_key' => $brand_key,
                        'remove_brand_key' => $remove_brand_key,
                        'remove_email_id' => $remove_email_id,
                    ]);
                }
                $remove_brand = EmailConfiguration::where('id', $request->email_id)->pluck('brand_key')->first();

                if (Auth::user()->type === 'lead' && !in_array($request->email_id, $allowed_email_configuration_ids) && !in_array($request->email_id, $email_configuration_ids)) {
                    return response()->json(['errors' => 'Oops! You don\'t have access to this brand.', 'assign' => $assign, 'brand_key' => $remove_brand], 422);
                }
                if (in_array((int)$request->email_id, $email_configuration_ids, true)) {
                    $email_configuration_ids = array_diff($email_configuration_ids, [$request->email_id]);
                } else {
                    $email_configuration_ids = array_unique(array_merge($email_configuration_ids, [$request->email_id]));
                }
                $action = in_array($request->email_id, $email_configuration_ids) ? 'created' : 'deleted';

                $email_configuration_exists = EmailConfiguration::whereIn('id', $email_configuration_ids)->where('status', 1)->get();
                if ($action === 'deleted') {
                    $find_record = AssignUserBrandEmail::where('user_id', $user->id)->where('email_configuration_id', $request->email_id)->first();
                    $this->log_Action($action, $find_record);
                }
                $user->setUserBrandEmails()->sync($email_configuration_exists);
                if ($action === 'created') {
                    $find_record = AssignUserBrandEmail::where('user_id', $user->id)->where('email_configuration_id', $request->email_id)->first();
                    $this->log_Action($action, $find_record);
                }
                $fetch_assign_user_brand_email = AssignUserBrandEmail::where('user_id', $user->id)->where('email_configuration_id', $request->email_id)->first();
                if ($fetch_assign_user_brand_email) {
                    $assign = 1;
                }


                if (!$fetch_assign_user_brand_email && !in_array($email_configuration_external_brand->brand_key, $member_brand_keys)) {
                    $remove_email_id = true;
                    $remove_brand_key = true;
                } elseif (!$fetch_assign_user_brand_email && !in_array($request->email_id, $allowed_email_configuration_ids)) {
                    $remove_email_id = true;
                }

                return response()->json([$assign == 1 ? 'success' : 'warning' => $assign == 1 ? 'Assign user brand email successfully!' : "Unassign user brand email successfully!",
                    'status' => 1,
                    'assign' => $assign,
                    'brand_key' => $brand_key,
                    'remove_brand_key' => $remove_brand_key,
                    'remove_email_id' => $remove_email_id,
                    'action' => $action,
                    'email_id' => $request->email_id,
                ]);
            }
            throw new \RuntimeException('Failed to assign brand email.');
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function log_Action($action, $find_record)
    {
        $logData = [
            'actor_id' => Auth::id(),
            'actor_type' => get_class(Auth::user()),
            'action' => $action,
            'loggable_id' => $find_record->id,
            'loggable_type' => get_class($find_record),
            'previous_record' => json_encode($find_record),
        ];
        $log = new LogAction($logData);
        $log->save();
    }

    public function board($team_key)
    {
        $team = Team::where('team_key', $team_key)->first();
        $cards = Card::where(['team_id' => $team->id, 'status' => 1])->get();

        foreach ($cards as $card) {
            $project = Project::where('team_key', $team_key)->first();
            $card->team_key = $team_key;
            $card->project_id = $project->id;
        }
        // dd($card);
        return view('team.board', compact('team', 'cards'));
    }

    public function task_card_change(Request $request)
    {
        // Task card change
        // dd($request->id,$request->card_id,$request->old_card_id,$request->sort);
        $task = Task::where('id', $request->id)->first();
        $task->card_id = $request->card_id;
        $task->save();
        // New card task sortig
        $card = Card::where('id', $request->card_id)->first();
        $card->sort_tasks = $request->sort;
        $card->save();
        // Old card task sortig
        $oldcard = Card::where('id', $request->old_card_id)->first();
        // dd($request->card_id,$request->old_card_id);
        if ($oldcard->sort_tasks != '' && $oldcard->sort_tasks != ',' && $request->card_id != $request->old_card_id) {
            $requestid = $request->id . ',';
            $valuesToRemove = str_replace(array($requestid, $request->id, ',,'), array('', '', ','), $oldcard->sort_tasks);
            $oldcard->sort_tasks = $valuesToRemove;
            $oldcard->save();
        }
        return redirect()->back()->with('success', 'Task updated successfully!');
    }
}

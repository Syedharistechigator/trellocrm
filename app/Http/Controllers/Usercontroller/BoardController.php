<?php

namespace App\Http\Controllers\Usercontroller;

use App\Http\Controllers\Controller;
use App\Models\AssignBrand;
use App\Models\BoardList;
use App\Models\BoardListCard;
use App\Models\BoardListCardActivity;
use App\Models\BoardListTeam;
use App\Models\Brand;
use App\Models\Client;
use App\Models\Label;
use App\Models\Project;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class BoardController extends Controller
{
    public function index()
    {
        if (auth()->user()->type === "client") {
            return back();
        }
        $clients = Client::where('status', 1)->get();
        $board_lists = BoardList::where('status', 1)
            ->whereHas('getBoardListTeams', function ($query) {
                $query->where('team_key', auth()->user()->team_key);
            })
            ->get()->sortBy('position');
        $brandKeys = Auth::user()->getTeamBrands->pluck('brand_key')->toArray();
        $projects = Project::whereIn('brand_key', $brandKeys)->get();
        $users = User::where('type', '!=', 'client')->where('status', 1)->get();
        return view('board.index', compact('clients', 'board_lists', 'projects', 'users'));
    }

    public function board_card_change(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:board_list_cards,id',
            'board_list_id' => 'required|exists:board_lists,id',
            'position' => 'required|integer',

        ], [
            'id.required' => 'The board list card id field is required.',
            'id.exists' => 'The selected board list card id is invalid.',
            'board_list_id.required' => 'The board list id is required.',
            'board_list_id.exists' => 'The selected board list card id is invalid.',
            'position.required' => 'The position is required.',
            'position.integer' => 'The position must be an integer.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            $board_list_card = BoardListCard::findOrFail($request->id);
            $new_board_list_id = $request->board_list_id;
            $new_position = $request->position;

            $current_position = $board_list_card->position;
            $old_board_list_id = $board_list_card->board_list_id;

            if ($new_board_list_id != $old_board_list_id) {
                $resp = $this->moveToAnotherBoardList($board_list_card, $old_board_list_id, $new_board_list_id, $current_position, $new_position);
            } else {
                $resp = $this->repositionWithinSameBoardList($board_list_card, $old_board_list_id, $current_position, $new_position);
            }

            $board_list_card_activity = new BoardListCardActivity();
            $board_list_card_activity->board_list_card_id = $board_list_card->id;
            $board_list_card_activity->user_id = auth()->user()->id;
            $board_list_card_activity->activity = $resp['message'];
            /** 0 = comment , 1 = attachment , 2 = activity*/
            $board_list_card_activity->activity_type = 2;
            $board_list_card_activity->save();

            DB::commit();

            $responseData = [
                'request' => $request->input(),
                'new_board_list_id' => $new_board_list_id,
                'old_board_list_id' => $old_board_list_id,
                'new_position' => $resp['new_position'],
                'current_position' => $resp['current_position'],
                'success' => $resp['message'],
                'affectedCards' => $resp['affectedCards'],
            ];

            return response()->json($responseData);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage(),'line'=> $e->getLine()], 500);
        }
    }

    private function moveToAnotherBoardList($board_list_card, $old_board_list_id, $new_board_list_id, $current_position, $new_position)
    {
        $target_board_list_card_count = BoardListCard::where('board_list_id', $new_board_list_id)->count();

        if ($new_position > $target_board_list_card_count + 1) {
            $new_position = $target_board_list_card_count + 1;
        }

        $affectedCards['old_board_list_cards'] = BoardListCard::where('board_list_id', $old_board_list_id)
            ->where('position', '>', $current_position)
            ->get()->toArray();
        $affectedCards['new_board_list_cards'] = BoardListCard::where('board_list_id', $new_board_list_id)
                ->where('position', '>=', $new_position)
                ->get()->toArray();
        BoardListCard::where('board_list_id', $old_board_list_id)
            ->where('position', '>', $current_position)
            ->decrement('position');
        BoardListCard::where('board_list_id', $new_board_list_id)
            ->where('position', '>=', $new_position)
            ->increment('position');

        $message = "Moved this card from " . optional($board_list_card->getBoardList)->title . " to " . BoardList::where('id', $new_board_list_id)->value('title');

        $board_list_card->board_list_id = $new_board_list_id;
        $board_list_card->position = $new_position;
        $board_list_card->save();

        return ['message' => $message, 'new_position' => $new_position, 'current_position' => $current_position, 'affectedCards' => $affectedCards];
    }

    private function repositionWithinSameBoardList($board_list_card, $old_board_list_id, $current_position, $new_position)
    {
        $max_position = BoardListCard::where('board_list_id', $old_board_list_id)->max('position');

        if ($new_position < 1) {
            $new_position = 1;
        } elseif ($new_position > $max_position) {
            $new_position = $max_position;
        }
        $affectedCards = [];
        if ($new_position != $current_position) {
            if ($new_position > $current_position) {
                $affectedCards['old_board_list_cards'] = BoardListCard::where('board_list_id', $old_board_list_id)
                    ->whereBetween('position', [$current_position + 1, $new_position])
                    ->get()->toArray();
                BoardListCard::where('board_list_id', $old_board_list_id)
                    ->whereBetween('position', [$current_position + 1, $new_position])
                    ->decrement('position');
            } else {
                $affectedCards['old_board_list_cards'] = BoardListCard::where('board_list_id', $old_board_list_id)
                    ->whereBetween('position', [$new_position, $current_position - 1])
                    ->get()->toArray();
                BoardListCard::where('board_list_id', $old_board_list_id)
                    ->whereBetween('position', [$new_position, $current_position - 1])
                    ->increment('position');
            }
            $board_list_card->position = $new_position;
            $board_list_card->save();
            $message = "Repositioned this card within " . optional($board_list_card->getBoardList)->title;
        } else {
            $message = "No change in position.";
        }
        return ['message' => $message, 'new_position' => $new_position, 'current_position' => $current_position, 'affectedCards' => $affectedCards];
    }


}

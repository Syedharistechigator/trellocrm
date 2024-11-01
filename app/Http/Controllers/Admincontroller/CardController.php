<?php

namespace App\Http\Controllers\Admincontroller;

use App\Http\Controllers\Controller;
use App\Models\Card;
use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Facades\Validator;

class cardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $cards = Card::orderBy('id','asc')->get();
        $cards = Card::all();
        // dd($cards);
        return view('admin.card.index',compact('cards'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.card.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = Card::create([
            'title' => $request->get('title'),
            'team_id' => $request->get('team_id'),
            'position' => $request->get('position'),
        ]);
        return $data;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\card  $card
     * @return \Illuminate\Http\Response
     */
    public function show(card $card)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\card  $card
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $card = Card::find($id);
        return view('admin.card.edit', compact('card'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\card  $card
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        $card = Card::find($id);
        $card->title = $request->title;
        $card->team_id = $request->team_id;
        $card->position = $request->position;
        $card->save();

        return $card;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\card  $card
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Card::find($id)->delete();
    }
    public function card_changeStatus(Request $request)
    {
        $card = Card::find($request->card_id);
        $card->status = $request->status;
        $card->save();

        return response()->json(['success'=>'Status change successfully.']);
    }
}

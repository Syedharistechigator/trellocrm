<?php

namespace App\Http\Controllers\Admincontroller;

use App\Http\Controllers\Controller;
use App\Models\Card;
use App\Models\Task;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Facades\Validator;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $tasks = Task::all();
        return view('admin.task.index',compact('tasks'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.task.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = Task::create([
            'card_id' => $request->get('card_id'),
            'project_id' => $request->get('project_id'),
            'title' => $request->get('title'),
            'proiority' => $request->get('proiority'),
            'description' => $request->get('description'),
            'assign_to' => $request->get('assign_to'),
            'due_date' => $request->get('due_date'),
        ]);

        $card=Card::where('id', $request->get('card_id'))->first();
        $card->sort_tasks=$request->get('sort_tasks').','.$data->id;
        $card->save();
        return $data;

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\task  $task
     * @return \Illuminate\Http\Response
     */
    public function show(task $task)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\task  $task
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $task = Task::find($id);
        return view('admin.task.edit', compact('task'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\task  $task
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        $task = Task::find($id);
        $task->key = $request->key;
        $task->email = $request->email;
        $task->balance = $request->balance;
        $task->save();

        return $task;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\task  $task
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Task::find($id)->delete();
    }


    public function trashed_task(){
        $tasks = Task::onlyTrashed()->get();
        return view('admin.task.trashed',compact('tasks'));
    }

    public function restore($id){
        Task::onlyTrashed()->whereId($id)->restore();
    }

    public function restoreAll(){
        Task::onlyTrashed()->restore();
    }


    public function task_changeStatus(Request $request)
    {
        $task = Task::find($request->task_id);
        $task->status = $request->status;
        $task->save();

        return response()->json(['success'=>'Status change successfully.']);
    }

    public function task_forceDelete($id){
        Task::onlyTrashed()->whereId($id)->forceDelete();
    }


}

@extends('layouts.app')

@section('cxmTitle', 'Board')

@section('content')
     <link rel="stylesheet" href="{{asset('assets/css/board/plugin.css')}}">
<section class="content">
    <div class="body_scroll">
        <div class="block-header">
            <div class="row">
                <div class="col-lg-7 col-md-6 col-sm-12">
                    <h2>{{$team->name}} Board List</h2>

                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{route('dashboard')}}"><i class="zmdi zmdi-home"></i> {{ config('app.home_name') }}</a></li>
                        <li class="breadcrumb-item">{{$team->name}}</li>
                        <li class="breadcrumb-item active"> List</li>
                    </ul>
                    <button class="btn btn-primary btn-icon mobile_menu" type="button"><i class="zmdi zmdi-sort-amount-desc"></i></button>
                </div>
                <div class="col-lg-5 col-md-6 col-sm-12 text-right">
                    @include('includes.cxm-top-right-toggle-btn')
                </div>
            </div>
        </div>
    </div>
 <?php
$cards_ids=$cards->pluck('id');
$cardstabs='';
foreach ($cards_ids as $key => $cards_id) {
  $cardstabs.='&quot;card-'.$cards_id.'&quot;,';
}
?>
    <div class="section-body">
          <div class="row">
            <div class="modal-edit-task trigger--fire-modal-4"></div>
            <div class="modal-add-task-details trigger--fire-modal-3"></div>
            <div class="col-12">
            <div class="board" data-plugin="dragula" data-containers="[<?=substr($cardstabs, 0, -1)?>]">
              <?php foreach ($cards as $key => $card): ?>
              <?php
              $taskIds = explode(',', $card->sort_tasks);
              $tasks=App\Models\Task::whereIn('id',$taskIds)->get();
              if($tasks->isEmpty()){
              $tasks=App\Models\Task::where('card_id', $card->id)->get();
              }
              ?>
              <div class="tasks animated {{$card->sort_tasks}}" data-cardname="{{$card->title}}" data-sr-id="{{$card->id}}">
                  <div class="mt-0 task-header text-uppercase">{{$card->title}} ( <span class="count">{{$tasks->count()}}</span>) </div>
                  <div id="card-{{$card->id}}" data-status="{{$card->id}}" class="task-list-items">
              <?php foreach ($tasks as $key => $task): ?>
                    <div class="card mb-0" id="{{$task->id}}" data-sort="{{$task->id}}">
                      <div class="card-body p-3">
                        <div class="card-header-action float-right">
                          <div class="dropdown card-widgets">
                            <a href="#" class="btn dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                              <i class="fas fa-cog"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right">
                              <a class="dropdown-item has-icon modal-edit-task-ajax" data-toggle="modal" data-target="#exampleModal" href="#">
                                <i class="fas fa-pencil-alt"></i> Edit </a>
                              <a class="dropdown-item has-icon modal-duplicate-task-ajax" data-id="{{$task->id}}" href="#">
                                <i class="fas fa-copy"></i> Duplicate </a>
                              <a class="dropdown-item has-icon delete-task-alert" data-task_id="{{$task->id}}" data-project_id="936" href="http://localhost/tg-crm-two/projects/delete_task/{{$task->id}}/936">
                                <i class="far fa-trash-alt"></i> Delete </a>
                            </div>
                          </div>
                        </div>
                        <div>
                          <a href="{{route('project.show', $task->project_id)}}" target="_blank" data-id="" class="text-body modal-add-task-details-ajax">{{$task->title}}</a>
                        </div>
                        <?php if ($task->proiority==1){
                          $proiority_text='Low';
                          $proiority_color='badge-info';
                         }elseif($task->proiority==2){
                          $proiority_text='Medium';
                          $proiority_color='badge-primary';
                         }else{
                          $proiority_text='High';
                          $proiority_color='badge-danger';
                         }?>
                        <span class="badge {{$proiority_color}} projects-badge">{{$proiority_text}}</span>
                        <p class="mt-2 mb-2">
                          <span class="text-nowrap d-inline-block">
                            <i class="fas fa-comments text-muted"></i>
                            <b>1</b> Comments </span>
                        </p>
                        <small class="float-right text-muted mt-2">{{$task->created_at->format('j F, Y')}} , {{$task->created_at->format('h:i:s A')}}
                         <br><span class="float-right">{{$task->created_at->diffForHumans()}}</span></small>
                         {{--
                        <figure class="avatar mr-2 avatar-sm" data-toggle="tooltip" data-title="Bhavna">
                          <img alt="image" src="http://localhost/tg-crm-two/assets/profiles/1582710915.13.jpg" class="rounded-circle">
                        </figure>
                        --}}
                      </div>
                    </div>
              <?php endforeach ?>
                  </div>


                  <div class="mt-3 p-0 task-header text-center">@if(Auth::user()->type != 'client')
                    <button class="btn btn-success btn-icon rounded-circle cxm-btn-create" data-cartid="{{$card->id}}" data-cartsort="{{$card->sort_tasks}}" type="button" data-toggle="modal" data-target="#cxmDetailModal" title="Add Task"><i class="zmdi zmdi-plus" ></i></button> Add a card
                @else
                    <button class="btn btn-success btn-icon rounded-circle cxm-btn-create d-none" data-cartId="{{$card->id}}" data-cartSort="{{$card->sort_tasks}}" type="button" data-toggle="modal" data-target="#cxmDetailModal" title="Add Task"><i class="zmdi zmdi-plus" ></i></button> Add a card
                @endif
                </div>

                </div>
              <?php endforeach ?>
              </div>
            </div>
          </div>
        </div>
</section>
<div class="modal fade" id="cxmDetailModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="title" id="defaultModalLabel">Add Task</h4>
            </div>
            <form id="task_form" method="POST">
            @csrf
            <input type="hidden" name="card_id" value="0">
            <input type="hidden" name="sort_tasks" value="1">
            <div class="modal-body">
                <div class="body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group form-float">
                                            <label for="Project">Project</label>
<?php
$projects = App\Models\Project::select('id','project_title')->get();
?>
                                            <select name="project_id" required class="form-control p-0" id="Project">
                                                <option value="">Select Project</option>
                                                <?php foreach ($projects as $key => $value): ?>
                                                <option value="{{$value->id}}" {{ Request::segment(3) == $value->id ? 'selected' : '' }}>{{$value->project_title}}</option>

                                                <?php endforeach ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group form-float">
                                            <label for="email_address">Title</label>
                                            <input type="text" class="form-control" name="title" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group form-float">
                                            <label for="email_address">Proiority</label>
                                            <select name="proiority" required class="form-control p-0">
                                                <option value="1">Low</option>
                                                <option value="2">Medium</option>
                                                <option value="3">High</option>
                                            </select>
                                        </div>
                                    </div>

                                </div>

                                <div class="row">

                                    <div class="col-md-12">
                                        <div class="form-group form-float">
                                            <label for="email_address">Description</label>
                                            <textarea class="form-control" name="description" required></textarea>
                                        </div>
                                    </div>

                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group form-float">
                                            <label for="email_address">Assign To</label>
                                            <input type="text" class="form-control" name="assign_to" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group form-float">
                                            <label for="email_address">Due Date</label>
                                            <input type="date" class="form-control" name="due_date" required>
                                        </div>
                                    </div>

                                </div>
                                <!-- <div class="row">
                                    <div class="col-md-6">
                                        <button class="btn btn-warning btn-round" type="submit">SUBMIT</button>
                                    </div>

                                </div> -->
                        </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-success btn-round" type="submit">Create Task</button>
                <button type="button" class="btn btn-danger btn-round" data-dismiss="modal">CLOSE</button>
            </div>
            </form>
        </div>
    </div>
</div>
@endsection
@push('cxmScripts')
    @include('team.board-script')
@endpush

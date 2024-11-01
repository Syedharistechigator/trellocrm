@extends('layouts.app')@section('cxmTitle', 'Board')

@section('content')
    @push('css')
        <style type="text/css">
            .loading_div {
                position: fixed;
                top: 50%;
                left: 0;
                width: 100%;
                z-index: 99999;
                display: none;
                align-items: center;
                justify-content: center;
                transform: translateY(-50%);
                height: 100%;
            }

            .loader-05 {
                display: inline-block;
                width: 5.5em;
                height: 5.5em;
                color: inherit;
                vertical-align: middle;
                pointer-events: none;
            }

            .loader-05 {
                border: 0.2em solid transparent;
                border-top-color: currentcolor;
                border-radius: 50%;
                -webkit-animation: 1s loader-05 linear infinite;
                animation: 1s loader-05 linear infinite;
                position: relative;
            }

            .loader-05:before {
                content: "";
                display: none;
                width: inherit;
                height: inherit;
                position: absolute;
                top: -0.3em;
                left: -0.3em;
                border: 0.3em solid currentcolor;
                border-radius: 50%;
                opacity: 0.5;
            }

            @-webkit-keyframes loader-05 {
                0% {
                    transform: rotate(0deg);
                }
                100% {
                    transform: rotate(360deg);
                }
            }

            @keyframes loader-05 {
                0% {
                    transform: rotate(0deg);
                }
                100% {
                    transform: rotate(360deg);
                }
            }

            .loading_div {
                display: none;
            }

            .overlay {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.5);
                z-index: 99998;
            }

            .ck-powered-by-balloon {
                display: none !important;
            }

            .member-avatar {
                background-color: orangered;
                color: #fff;
            }

            .member-avatar:hover {
                color: #000;
            }

            body {
                --ck-z-default: 100;
                --ck-z-modal: calc(var(--ck-z-default) + 999);
                --ck-color-mention-background: hsla(220, 100%, 54%, 0.4);

                /* Make the mention text dark grey. */
                --ck-color-mention-text: hsl(0, 0%, 15%);
            }

            .ck.ck-balloon-panel {
                z-index: 1050 !important;
            }

            button.ck-button {
                border-radius: 3px;
                box-sizing: border-box;
                display: inline-block;
                line-height: 20px;
                padding: 6px 12px;
                position: relative;
                background-color: #0c66e4;
                box-shadow: none;
                border: none;
                color: #ffffff;
                text-decoration: none;
                margin: 8px 0px 8px 0px;
                cursor: pointer;
            }


            button.ck-button:hover {
                cursor: pointer;
            }

            button.ck-button:focus {
                background-color: #0b5ed3;
            }

            button.ck-button:disabled {
                background-color: #091e4208;
                color: #091e424f;
                cursor: not-allowed;
            }

            span.show-desc, span.hide-desc {
                border-radius: 10px;
                padding: 8px 12px;
                cursor: pointer;
            }

            span.hide-desc {
                background-color: var(--ds-background-neutral, #091e420f);
                color: var(--ds-text, #172b4d);
                z-index: 1;
                position: relative;
                display: block;
            }

            span.show-desc {
                z-index: 2;
                display: block;
                width: 100%;
            }

            .member-avatar {
                cursor: pointer;
            }

            .image-size {
                cursor: pointer;
                border-radius: 10px;
                height: 70px;
                display: none;
            }

            .image-size-without-bg {
                width: 50%;
            }

            .image-size-with-bg {
                width: 60%;
                object-fit: contain;
            }

            p[id^="board-list-date-"] {
                padding-top: 1rem;
            }

            p[id^="board-list-members-"] {
                padding-top: 1rem;
            }

            .spinner {
                position: fixed;
                top: 73%;
                left: 50%;
                transform: translate(-50%, -50%);
                background: none;
                border-radius: 8px;
                padding: 20px;
                text-align: center;
                z-index: 1000;
            }

            .spinner img {
                /*width: 50px;*/
                height: 15px;
                display: block;
                margin: 0 auto;
            }

            form.card-label {
                overflow-y: scroll;
                height: 400px;
            }

            .ui-range-highlight a {

                background-color: rgb(97 160 248 / 44%) !important;
                color: #0c66e4 !important;
            }

            .ui-range-highlight a.ui-state-active {
                background-color: rgb(97 160 248 / 44%) !important;
                color: #0c66e4 !important;
            }

            .ui-datepicker-current-day a.ui-state-active {
                background-color: rgb(97 160 248 / 44%) !important;
                color: #0c66e4 !important;
            }

            .ui-datepicker-today a.ui-state-highlight {
                background-color: red !important;
                color: white !important;
            }

            .coment-div {
                display: flex;
                gap: 10px;
                padding: 8px 0;
            }

            .attachment-div .attachment-thumbnail span.att-name i {
                font-size: 35px;
            }

            .board-members h4.bm-heading, .card-members h4.bm-heading {
                margin-top: 20px;
            }

            .member-assign .board-members .board-members-list,
            .member-assign .card-members .card-member-assignment {
                overflow-y: auto;
                scrollbar-width: thin;
                scrollbar-color: #888888 #f3f3f3;
            }

            .member-assign .board-members .board-members-list {
                max-height: 35vh;
            }

            .member-assign .card-members .card-member-assignment {
                max-height: 25vh;
            }

            .member-assign .board-members .board-members-list::-webkit-scrollbar-track,
            .member-assign .card-members .card-member-assignment::-webkit-scrollbar-track {
                background: #f3f3f3;
            }

            .member-assign .board-members .board-members-list::-webkit-scrollbar-thumb,
            .member-assign .card-members .card-member-assignment::-webkit-scrollbar-thumb {
                background: #888888;
            }

            .member-assign .board-members .board-members-list::-webkit-scrollbar-thumb:hover,
            .member-assign .card-members .card-member-assignment::-webkit-scrollbar-thumb:hover {
                background: #555;
            }

            .attachments-for-cover {
                overflow: scroll;
                max-height: 250px
            }

            .show-comment img {
                max-height: 290px;
                max-width: 480px;
            }
        </style>

    @endpush
    <section class="content trelloBoard">
        <div class="body_scroll">
            <div class="block-header">
                <div class="row">
                    <div class="col-lg-7 col-md-6 col-sm-12">
                        <h2>{{ optional(Auth::user()->getTeam)->name }} Board</h2>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ route('dashboard') }}"><i class="zmdi zmdi-home"></i> TG</a></li>
                            <li class="breadcrumb-item">{{ optional(Auth::user()->getTeam)->name }}</li>
                            <li class="breadcrumb-item active"> List</li>
                        </ul>
                    </div>
                    <div class="col-lg-5 col-md-6 col-sm-12 text-right">
                        @include('includes.cxm-top-right-toggle-btn')
                    </div>
                </div>
            </div>
        </div>
        <div class="section-body">
            <div class="row">
                <div class="modal-edit-task trigger--fire-modal-4"></div>
                <div class="modal-add-task-details trigger--fire-modal-3"></div>
                <div class="col-12">
                    <div class="board main-board card-wrapper" data-plugin="dragula" data-containers="[{!! $board_lists->pluck('id')->map(function ($id) { return '&quot;board-list-' . $id . '&quot;'; })->implode(',') !!}]">
                        <div class="dot-submenu-overlay"></div>
                        @foreach ($board_lists as $key => $board_list)
                            <div class="tasks tasks-list animated {{ $board_list->sort_tasks }}" data-sr-id="{{ $board_list->id }}" data-cardname="{{ $board_list->title }}">
                                <div class="mt-0 task-header menu-card-header text-uppercase">
                                    <div class="app-card">
                                        <h3 class="app-card-heading">{{$board_list->title}} ( {{$board_list->id}} ) </h3>
                                        <div class="dropdown">
                                            <button class="app-card-edit" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="material-symbols-outlined">
                                            more_horiz
                                            </span></button>
                                            <div class="dropdown-menu add-member-drop main-card-drop current-step" aria-labelledby="dropdownMenuButton" id="dropdown-menu-id">
                                                <div class="add-member buttons-container">
                                                    <button class="close-btn-pop close-member" onclick="closeDiv()">
                                                        <span class="material-symbols-outlined"> close</span>
                                                    </button>
                                                    <h4 class="mem-heading">List actions</h4>
                                                    <ul class="drop-list-action">
                                                        <li><a href="javascript:void(0);" onclick="showDiv()">Add Card...</a>
                                                        </li> <li>
                                                            <a href="javascript:void(0);" class="dynamicBtn" data-target="copy-drop-list" onClick="showDropdown()"> Copy List... </a>
                                                        </li> <li>
                                                            <a href="javascript:void(0);" class="dynamicBtn" data-target="move-drop-list" onClick="showDropdown()">Move List...</a>
                                                        </li> <li><a href="javascript:void(0);">Watch</a></li>
                                                    </ul>
                                                    <h5 class="main-card-heading">Automation</h5>
                                                    <ul class="drop-list-action">
                                                        <li>
                                                            <a href="javascript:void(0);" class="dynamicBtn" data-target="newrule-drop-list" onClick="showDropdown()">When a card is added to the list...</a>
                                                        </li>
                                                        <li><a href="javascript:void(0);">Every day, sort list by...</a>
                                                        </li>
                                                        <li><a href="javascript:void(0);">Every Monday, sort list by...</a>
                                                        </li> <li><a href="javascript:void(0);">Create a rule...</a>
                                                        </li>
                                                    </ul>
                                                    <ul class="drop-list-action">
                                                        <li>
                                                            <a href="javascript:void(0);" class="dynamicBtn" data-target="move-all-drop-list" onClick="showDropdown()">Move all cards in this list...</a>
                                                        </li> <li>
                                                            <a href="javascript:void(0);" class="dynamicBtn" data-target="archive-all-drop-list" onClick="showDropdown()">Archive all cards in this list...</a>
                                                        </li>
                                                    </ul>
                                                    <ul class="drop-list-action">
                                                        <li><a href="javascript:void(0);">Archive this list</a></li>
                                                    </ul>
                                                </div>
                                            </div>
                                            <div class="inner-dropdown" id="inner-dropdown" style="display: none;"></div>
                                        </div>
                                    </div>
                                </div>
                                <div id="board-list-{{ $board_list->id }}" data-status="{{ $board_list->id }}" class="task-list-items">
                                    @if(isset($board_list->getBoardListCards))
                                        @foreach ($board_list->getBoardListCards->sortBy('position') as $key => $board_list_card)
                                            <div class="card submenu-card mb-0 board-list-card" id="board-list-card-{{ $board_list_card->id }}" data-board-list-card-id="{{ $board_list_card->id }}" data-sort="{{ $board_list->id }}" data-toggle="modal" data-target="#edit-board-list-card-modal">
                                                <div class="card-body open-card-item">
                                                    <div class="submenu">
                                                        <div class="submenu_handle main-card-toggle">
                                                            <div class="image-container container2 cover-image-main-div" id="cover-image-main-div-{{$board_list_card->id}}" style="{{$board_list_card->cover_image_size ==1?"background-color:".$board_list_card->cover_background_color :""}}">
                                                                <img class="selected-image cover-image" id="cover-image-{{$board_list_card->id}}" src="{{$board_list_card->cover_image ?
(file_exists(public_path("assets/images/board-list-card/{$board_list_card->cover_image}")) ?
     asset("assets/images/board-list-card/{$board_list_card->cover_image}") :
      (file_exists(public_path("assets/images/board-list-card/activities/{$board_list_card->cover_image}") ) ?
       asset("assets/images/board-list-card/activities/{$board_list_card->cover_image}") :
       (file_exists(public_path('assets/images/board-list-card/activities/'. (optional($board_list_card)->client_id ?? "random-client")) . "/" .$board_list_card->cover_image)?
       asset("assets/images/board-list-card/activities/".(optional($board_list_card)->client_id ?? "random-client")."/{$board_list_card->cover_image}"):""))): (file_exists(public_path("assets/images/board-list-card/original/{$board_list_card->cover_image}")) ?
     asset("assets/images/board-list-card/original/{$board_list_card->cover_image}") :"")}}" style="{{$board_list_card->cover_image && (file_exists(public_path("assets/images/board-list-card/original/{$board_list_card->cover_image}")) || file_exists(public_path("assets/images/board-list-card/{$board_list_card->cover_image}")) || file_exists(public_path("assets/images/board-list-card/activities/{$board_list_card->cover_image}")) || (file_exists(public_path('assets/images/board-list-card/activities/' . (optional($board_list_card)->client_id ?? 'random-client') . '/' . $board_list_card->cover_image)))) ? 'display:block!important;' : '' }}{{$board_list_card->cover_image_size ==1?"width: 60%;height: 100%;" :""}}" alt="Selected Image">
                                                            </div>
                                                            <div class="main-card-edit do-not-target">
                                                                <button class="main-card-button"><span class="material-symbols-outlined">
                                                        edit
                                                    </span></button>
                                                            </div>
                                                            <div class="submenu_title">
                                                                <h4 id="card-title-{{ $board_list_card->id }}">{{$board_list_card->title}} ( {{$board_list_card->id}} ) </h4>
                                                                <div class="drag-detail do-not-target">
                                                                    <ul class="us-card-list-detail" id="us-card-list-detail-{{ $board_list_card->id }}">
                                                                        @if($board_list_card->due_date)
                                                                            <li id="board-list-date-data-{{ $board_list_card->id }}">
                                                                                <a href="javascript:void(0);" class="ddt-list-clock"><i class="far fa-clock"></i>
                                                                                    <p id="board-list-date-{{ $board_list_card->id }}">{{ $board_list_card->start_date && $board_list_card->is_check_start_date == 1  ? \Carbon\Carbon::parse($board_list_card->start_date)->format('M j') .' - '.\Carbon\Carbon::parse($board_list_card->due_date)->format('M j') : \Carbon\Carbon::parse($board_list_card->due_date)->format('M j')}}</p>
                                                                                </a></li>
                                                                        @endif
                                                                        <li><a href="javascript:void(0);" class="ddt_list"><i class="fas fa-align-justify"></i></a>
                                                                        </li> <li>
                                                                            <a href="javascript:void(0);" title="comment{{isset($board_list_card->getComments) && count($board_list_card->getComments) > 1 ? 's' : ''}}" class="ddt_list comment-count-div"><i class="far fa-comment"></i>
                                                                                <span class="comment-count">{{isset($board_list_card->getComments) ? count($board_list_card->getComments) : 0}}</span>
                                                                            </a></li> <li>
                                                                            <a href="javascript:void(0);" title="attachment{{isset($board_list_card->getAttachments) && count($board_list_card->getAttachments) > 1 ? 's' : ''}}" class="ddt_list attachment-count-div"><i class="fas fa-paperclip"></i>
                                                                                <span class="attachment-count">{{isset($board_list_card->getAttachments) ? count($board_list_card->getAttachments) : 0}}</span>
                                                                            </a> </li>
                                                                    </ul>
                                                                    <ul class="us-card-list-member" id="us-card-list-member-{{ $board_list_card->id }}">
                                                                        @if($board_list_card->getBoardListCardUsers)
                                                                            @foreach($board_list_card->getBoardListCardUsers as $user_key => $user)
                                                                                @php
                                                                                    $fnl = strtolower($user['name'][0]);
                                                                                    $color = $fnl >= 'a' && $fnl <= 'e' ? "color1" :( $fnl >= 'f' && $fnl <= 'j' ? "color2" : ($fnl >= 'k' && $fnl <= 'o' ? "color3" : ($fnl >= 'p' && $fnl <= 't' ? "color4" : ($fnl >= 'u' && $fnl <= 'x' ? "color5" : "color6"))));
                                                                                @endphp
                                                                                <li class="memeber-card-overlay dropdown">
                                                                                    <a href="javascript:void(0);" class="btn btn-secondary dropdown-toggle {{ $color }}" type="button" id="board-card-list-assign-members" title="{{ $user->name }}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                                                        <p id="board-list-members-{{ $board_list_card->id }}">
                                                                                            {{ strtoupper(substr(implode('', array_map(function ($word) {return strtoupper($word[0]);}, explode(' ', $user['name']))), 0, 2)) }}
                                                                                        </p>
                                                                                    </a>
                                                                                    <div class="dropdown-menu profile-card member-outer" aria-labelledby="board-card-list-assign-members">
                                                                                        <div class="pchead">
                                                                                            <div class="pc-name">
                                                                                                <button class="close-btn-pop">
                                                                                                <span class="material-symbols-outlined">
                                                                                                    close
                                                                                                </span>
                                                                                                </button>
                                                                                                <div class="short-name">
                                                                                                    <span class="{{ $color }}">{{ strtoupper(substr(implode('', array_map(function ($word) {return strtoupper($word[0]);}, explode(' ', $user['name']))), 0, 2)) }}</span>
                                                                                                </div>
                                                                                                <div class="short-detail">
                                                                                                    <h3>{{ $user->name }}</h3>
                                                                                                    <h4>{{ $user->email }} </h4>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                        <a href="javascript:void(0);" class="view-profile view-pro-out">View Profile</a>
                                                                                        <div class="pcfoot">
                                                                                            <a href="javascript:void(0);" class="view-profile view-pro-out">Remove from card</a>
                                                                                        </div>
                                                                                    </div>
                                                                                </li>

                                                                            @endforeach
                                                                        @endif
                                                                    </ul>
                                                                </div>
                                                            </div>
                                                            <div class="overlay-div-menu-list">
                                                                <ul>
                                                                    <li><a href="javascript:void(0);"><span class="material-symbols-outlined">open_in_browser</span>Open Card</a></li>
                                                                    <li class="dropdown label-open-overlay">
                                                                        <a href="javascript:void(0);" class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                                            <span class="material-symbols-outlined">sell</span> Edit Labels
                                                                        </a>
                                                                        <div class="dropdown-menu add-member-drop" aria-labelledby="dropdownMenuButton">
                                                                            <div class="add-member">
                                                                                <button class="close-btn-pop close-member"><span class="material-symbols-outlined">
                                                                        close
                                                                    </span>
                                                                                </button>
                                                                                <h4 class="mem-heading">Label</h4>
                                                                                {{--@include('board.assign-new-board-labels')--}}
                                                                                {{--<form>
                                                                                    <div class="form-group">
                                                                                        <input type="text" class="form-control"
                                                                                               id="searchMember"
                                                                                               aria-describedby="searchHelp"
                                                                                               placeholder="Search Labels">
                                                                                    </div>
                                                                                    <label class="bm-heading">Board Member</label>
                                                                                    <div class="form-check">
                                                                                        <input type="checkbox" class="form-check-label form-check-input"
                                                                                               id="exampleCheck1">
                                                                                        <label class="form-check-label new-mock"
                                                                                               for="exampleCheck1">Need More Amazing
                                                                                            Mock Design</label>
                                                                                        <span><i class="far fa-edit"></i></span>
                                                                                    </div>
                                                                                    <div class="form-check">
                                                                                        <input type="checkbox" class="form-check-label form-check-input"
                                                                                               id="exampleCheck2">
                                                                                        <label class="form-check-label new-mock"
                                                                                               for="exampleCheck2"></label>
                                                                                        <span><i class="far fa-edit"></i></span>
                                                                                    </div>
                                                                                    <div class="form-check">
                                                                                        <input type="checkbox" class="form-check-label form-check-input"
                                                                                               id="exampleCheck3">
                                                                                        <label class="form-check-label urgent"
                                                                                               for="exampleCheck3">Urgent</label>
                                                                                        <span><i class="far fa-edit"></i></span>
                                                                                    </div>
                                                                                    <div class="form-check">
                                                                                        <input type="checkbox" class="form-check-label form-check-input"
                                                                                               id="exampleCheck4">
                                                                                        <label class="form-check-label urgent"
                                                                                               for="exampleCheck4"></label>
                                                                                        <span><i class="far fa-edit"></i></span>
                                                                                    </div>
                                                                                    <div class="form-check">
                                                                                        <input type="checkbox" class="form-check-label form-check-input"
                                                                                               id="exampleCheck5">
                                                                                        <label class="form-check-label high-priority"
                                                                                               for="exampleCheck5">Most High Priority
                                                                                        </label>
                                                                                        <span><i class="far fa-edit"></i></span>
                                                                                    </div>
                                                                                </form>--}}
                                                                                <button class="show-btn dynamiclabelBtn" onClick="openlabelDiv()" data-target="popover-new" id="new-label-btn">Create a new label
                                                                                </button>
                                                                                <button class="show-btn show-btn-label">Show more label
                                                                                </button>
                                                                                <button class="show-btn">Enable Colorblind friendly mode
                                                                                </button>
                                                                            </div>
                                                                        </div>
                                                                    </li> <li class="edit-open-overlay dropdown">
                                                                        <a href="javascript:void(0);" class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="material-symbols-outlined">person</span>Change Members</a>
                                                                        {{--                                                                    @include('board.assign-new-board-members')--}}
                                                                    </li> <li class="change-cover-overlay dropdown">
                                                                        <a href="javascript:void(0);" class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="material-symbols-outlined">
                                                                credit_card
                                                            </span>Change Cover</a>
                                                                        <div class="dropdown-menu add-member-drop" aria-labelledby="dropdownMenuButton">
                                                                            <div class="add-member">
                                                                                <button class="close-btn-pop close-member"><span class="material-symbols-outlined">
                                                                        close
                                                                    </span>
                                                                                </button>
                                                                                <h4 class="mem-heading">Cover</h4>
                                                                                <h3 class="bm-heading">Size</h3>
                                                                                <figure>
                                                                                    <img src="" alt="img">
                                                                                    <img src="" alt="img">
                                                                                </figure>
                                                                                <h3 class="bm-heading">Colors</h3>
                                                                                <ul class="color-list">
                                                                                    <li>
                                                                                        <span class="list-diamension m-green"></span>
                                                                                    </li> <li>
                                                                                        <span class="list-diamension m-yellow"></span>
                                                                                    </li> <li>
                                                                                        <span class="list-diamension m-orange"></span>
                                                                                    </li> <li>
                                                                                        <span class="list-diamension m-red"></span>
                                                                                    </li> <li>
                                                                                        <span class="list-diamension m-purple"></span>
                                                                                    </li>
                                                                                </ul>
                                                                                <form>
                                                                                    <h3 class="bm-heading">Attachments </h3>
                                                                                    <div class="upload-btn-wrapper border-none">
                                                                                        <button class="btn">Upload a file
                                                                                        </button>
                                                                                        <input type="file" name="file-name"/>
                                                                                    </div>
                                                                                    <p>Tip: Drag an image on to the card to upload it. </p>
                                                                                </form>
                                                                                <h3 class="bm-heading">Photos from Unsplash</h3>
                                                                                <!-- <ul class="unsplash-list">
                                                                                                        <li><a href="javascript:void(0);">
                                                                                                                <figure><img src="#" alt=""></figure>
                                                                                                            </a></li>
                                                                                                    </ul> -->
                                                                                <button class="show-btn">Search for Photos
                                                                                </button>
                                                                            </div>
                                                                        </div>
                                                                    </li> <li class="move-card-overlay dropdown">
                                                                        <a href="javascript:void(0);" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="material-symbols-outlined">
                                                                east
                                                            </span>Move</a>
                                                                        <div class="dropdown-menu add-member-drop" aria-labelledby="dropdownMenuButton">
                                                                            <div class="add-member">
                                                                                <button class="close-btn-pop close-member"><span class="material-symbols-outlined">
                                                                        close
                                                                    </span>
                                                                                </button>
                                                                                <h4 class="mem-heading">Move card</h4>
                                                                                <h3 class="bm-heading">Select destination</h3>
                                                                                <form>
                                                                                    <div class="row mx-0">
                                                                                        <div class="col-md-12 p-1">
                                                                                            <div class="form-group select-top">
                                                                                                <button class="select-btn">
                                                                                                    <span class="bord-label">Board</span>
                                                                                                    <span class="bord-value">Web
                                                                                        Projects</span>
                                                                                                </button>
                                                                                                <select class="form-control" id="exampleFormControlSelect1">
                                                                                                    <optgroup label="Alpha Design Crew">
                                                                                                        <option>Website Projects (current)
                                                                                                        </option>
                                                                                                    </optgroup>
                                                                                                    <optgroup label="Book Writing">
                                                                                                        <option>Chelsea Web Projects
                                                                                                        </option>
                                                                                                        <option>
                                                                                                            Professional Writers Help
                                                                                                        </option>
                                                                                                    </optgroup>
                                                                                                </select>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="row mx-0">
                                                                                        <div class="col-md-8 p-1">
                                                                                            <div class="form-group select-top">
                                                                                                <button class="select-btn">
                                                                                                    <span class="bord-label">List</span>
                                                                                                    <span class="bord-value">App
                                                                                        Development</span>
                                                                                                </button>
                                                                                                <select class="form-control" id="exampleFormControlSelect1">
                                                                                                    <option>New Task (current)
                                                                                                    </option>
                                                                                                    <option>Inprogress
                                                                                                    </option>
                                                                                                    <option>Needs Clearification
                                                                                                    </option>
                                                                                                    <option>Incomplete Brief
                                                                                                    </option>
                                                                                                    <option>Review</option>
                                                                                                    <option>Revision
                                                                                                    </option>
                                                                                                    <option>Revision Review
                                                                                                    </option>
                                                                                                    <option>Waiting for Feedback
                                                                                                    </option>
                                                                                                    <option>Completed
                                                                                                    </option>
                                                                                                </select>
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="col-md-4 p-1">
                                                                                            <div class="form-group select-top">
                                                                                                <button class="select-btn">
                                                                                                    <span class="bord-label">Position</span>
                                                                                                    <span class="bord-value">1</span>
                                                                                                </button>
                                                                                                <select class="form-control" id="exampleFormControlSelect1">
                                                                                                    <option>1 (current)
                                                                                                    </option>
                                                                                                </select>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="last-button mt-2">
                                                                                        <button class="btn">Move</button>
                                                                                    </div>
                                                                                </form>
                                                                            </div>
                                                                        </div>
                                                                    </li>
                                                                    <!-- <li class="copy-cover-overlay dropdown"><a href="javascript:void(0);"
                                                                            class="btn btn-secondary dropdown-toggle" type="button"
                                                                            id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true"
                                                                            aria-expanded="false"><span class="material-symbols-outlined">
                                                                                content_copy
                                                                            </span>Copy</a>

                                                                    </li> -->
                                                                    <li class="date-cover-overlay dropdown">
                                                                        <a href="javascript:void(0)" class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                                            <span class="material-symbols-outlined">schedule</span> Edit Dates
                                                                        </a>
                                                                        <div class="dropdown-menu add-member-drop" aria-labelledby="dropdownMenuButton">
                                                                            <div class="add-member">
                                                                                <button class="close-btn-pop close-member"><span class="material-symbols-outlined">
                                                                        close
                                                                    </span>
                                                                                </button>
                                                                                {{--@include('board.assign-new-board-dates')--}}
                                                                                {{--<h4 class="mem-heading">Dates</h4>
                                                                                <div id="datepicker" class="calendar"></div>
                                                                                <button class="show-btn">Show other
                                                                                    Workspace members
                                                                                </button>--}}
                                                                            </div>
                                                                        </div>
                                                                    </li>
                                                                    <li><a href="javascript:void(0);"><span class="material-symbols-outlined">
                                                                folder_open
                                                            </span>Archive</a></li>
                                                                </ul>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <button class="overlay-div-menu-btn">Save</button>
                                            </div>
                                        @endforeach
                                    @endif
                                    <div id="cardContainer">
                                        <!-- Existing cards will be appended here -->
                                    </div>
                                    <div id="add-card-input-{{ $board_list->id }}" style="display: none" class="add-card-input" data-board-list-id="{{$board_list->id }}">
                                        <form id="create_board_card_form" class="cardboard-form">
                                            <input type="hidden" name="board_list_id" value="{{ $board_list->id }}">
                                            <div class="form-group">
                                                <select id="card-client-{{ $board_list->id }}" name="client_id" class="card-title form-control cxm-live-search-fix" data-icon-base="zmdi" data-tick-icon="zmdi-check" data-show-tick="true" title="Select Client" data-live-search="true" required>
                                                    @foreach($clients as $client)
                                                        <option value="{{$client->id}}">{{$client->email}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <style></style>
                                            <div class="form-group">
                                                <input id="card-title-{{ $board_list->id }}" name="title" class="card-title" placeholder="Enter a title for this card..." required/>
                                            </div>
                                            <div class="wel-btn">
                                                <button type="submit" id="add-card-btn-{{ $board_list->id }}" class="add-card-btn" data-board-list-id="{{$board_list->id }}">
                                                    Add Card
                                                </button>
                                                <button type="button" id="add-card-close-btn-{{ $board_list->id }}" class="add-card-close-btn" data-board-list-id="{{$board_list->id }}">
                                                    <span class="material-symbols-outlined">close</span>
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                <div class="task-header main-card-footer text-center">
                                    {{--                                    <button class="btn btn-success btn-icon rounded-circle add-card-btn"--}}
                                    {{--                                            data-board-list-id="{{ $board_list->id }}"
                                    type="button" data-toggle="modal"--}}
                                    {{--                                            data-target="#create-board-card-modal" title="Add Task"><i--}}
                                    {{--                                            class="zmdi zmdi-plus"></i></button>--}}
                                    {{--                                    Add a card--}}
                                    <div class="app-card-add" id="board-list-card-btn-{{ $board_list->id }}">
                                        <button type="button" name="answer" value="Show Div" data-board-list-id="{{$board_list->id }}" class="app-card-add-button">
                                            <span class="material-symbols-outlined">add</span> Add a Card
                                        </button>
                                        <button class="app-card-edit">
                                            <span class="material-symbols-outlined">wysiwyg</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>




    <div class="copy-drop-list" style="display: none;">
        <div class="inner-drop-header">
            <button id="add-close-btn" class="menu-card-return" onclick="returnDiv()">
                <span class="material-symbols-outlined"> arrow_back_ios </span>
            </button>
            <h4 class="mem-heading">Copy list</h4>
            <button id="add-close-btn" class="menu-card-return" onclick="closeDiv()">
                <span class="material-symbols-outlined"> close </span>
            </button>
        </div>
        <div class="inner-drop-body">
            <form>
                <label for="exampleFormControlTextarea1" class="copy-card-label">Name</label>
                <textarea class="form-control copy-card-text" id="exampleFormControlTextarea1" rows="3" placeholder="App development"></textarea>
                <button type="submit" id="addCard" class="menu-card-button">Create List</button>
            </form>
        </div>
    </div>

    <div class="move-drop-list" style="display: none;">
        <div class="inner-drop-header">
            <button id="add-close-btn" class="menu-card-return" onclick="returnDiv()">
                <span class="material-symbols-outlined"> arrow_back_ios </span>
            </button>
            <h4 class="mem-heading">Move list</h4>
            <button id="add-close-btn" class="menu-card-return" onclick="closeDiv()">
                <span class="material-symbols-outlined"> close </span>
            </button>
        </div>
        <div class="inner-drop-body">
            <form class="add-member">
                <div class="row mx-0">
                    <div class="col-md-12 p-1">
                        <div class="form-group select-top">
                            <button class="select-btn">
                                <span class="bord-label">Board</span> <span class="bord-value">Web Projects</span>
                            </button>
                            <select class="form-control" id="exampleFormControlSelect1">
                                <optgroup label="Alpha Design Crew">
                                    <option>Website Projects (current)</option>
                                </optgroup>
                                <optgroup label="Book Writing">
                                    <option>Chelsea Web Projects</option>
                                    <option>Professional Writers Help</option>
                                </optgroup>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row mx-0">
                    <div class="col-md-12 p-1">
                        <div class="form-group select-top">
                            <button class="select-btn">
                                <span class="bord-label">Position</span> <span class="bord-value">1</span>
                            </button>
                            <select class="form-control" id="exampleFormControlSelect1">
                                <option>1 (current)</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="last-button mt-2">
                    <button class="btn">Move</button>
                </div>
            </form>
        </div>
    </div>

    <div class="newrule-drop-list" style="display: none;">
        <div class="inner-drop-header">
            <button id="add-close-btn" class="menu-card-return" onclick="returnDiv()">
                <span class="material-symbols-outlined"> arrow_back_ios </span>
            </button>
            <h4 class="mem-heading">New rule</h4>
            <button id="add-close-btn" class="menu-card-return" onclick="closeDiv()">
                <span class="material-symbols-outlined"> close </span>
            </button>
        </div>
        <div class="inner-drop-body">
            <div>
                <form>
                    <label for="exampleFormControlTextarea1" class="copy-card-label">When</label>
                    <div class="card-and-list">
                        <h5 class="grn-card">Card & lists</h5>
                        <p class="grn-text">When a card is added to list</p>
                        <select id="inputState" class="form-control">
                            <option selected>App develpoment</option>
                            <option>New Task</option>
                        </select>
                    </div>
                    <label for="exampleFormControlTextarea1" class="copy-card-label">Then</label>
                    <button type="submit" class="card-and-list-btn"><span class="material-symbols-outlined">
                        add
                    </span> Add action
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="move-all-drop-list" style="display: none;">
        <div class="inner-drop-header">
            <button id="add-close-btn" class="menu-card-return" onclick="returnDiv()">
                <span class="material-symbols-outlined"> arrow_back_ios </span>
            </button>
            <h4 class="mem-heading">Move all cards in list</h4>
            <button id="add-close-btn" class="menu-card-return" onclick="closeDiv()">
                <span class="material-symbols-outlined"> close </span>
            </button>
        </div>
        <div class="inner-drop-body">
            <div>
                <ul class="move-all-list">
                    <li class=""><a href="javascript:void(0);">New Task</a></li>
                    <li class=""><a href="javascript:void(0);">In Progress</a></li>
                    <li class=""><a href="javascript:void(0);">Needs Clearification</a></li>
                    <li class=""><a href="javascript:void(0);">Incomplete Brief</a></li>
                    <li class=""><a href="javascript:void(0);">Review</a></li>
                    <li class=""><a href="javascript:void(0);">Revision</a></li>
                    <li class=""><a href="javascript:void(0);">Revision Review</a></li>
                    <li class=""><a href="javascript:void(0);">Waiting for Feedback</a></li>
                    <li class=""><a href="javascript:void(0);">Completed</a></li>
                    <li class="move-current"><a href="javascript:void(0);">App Development</a></li>
                </ul>
            </div>
        </div>
    </div>

    <div class="archive-all-drop-list" style="display: none;">
        <div class="inner-drop-header">
            <button id="add-close-btn" class="menu-card-return" onclick="returnDiv()">
                <span class="material-symbols-outlined"> arrow_back_ios </span>
            </button>
            <h4 class="mem-heading">Archive all cards in this list?</h4>
            <button id="add-close-btn" class="menu-card-return" onclick="closeDiv()">
                <span class="material-symbols-outlined"> close </span>
            </button>
        </div>
        <div class="inner-drop-body">
            <div>
                <p class="archive-para">This will remove all the cards in this list from
                    <br> the board. To view archived cards and bring <br> them back to the board, click “Menu” >
                    <br> “Archived Items.” </p>
                <button type="submit" class="archive-all-list-btn"><span class="material-symbols-outlined">
                    add
                </span> Add action
                </button>
            </div>
        </div>
    </div>

    <!-- modal start -->
    @include('board.board-modal')
    <!-- modal end -->
@endsection
@push('cxmScripts')
    @include('board.ckeditor-script')
    @include('board.script')
@endpush

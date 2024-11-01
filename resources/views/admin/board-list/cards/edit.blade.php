@extends('admin.layouts.app')@section('cxmTitle', 'Edit Board List Card')

@section('content')
    @push('css')
        <link rel="stylesheet"
              href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200"/>
    @endpush
    <section class="content">
        <div class="body_scroll">
            <div class="block-header">
                <div class="row">
                    <div class="col-lg-7 col-md-6 col-sm-12">
                        <h2>Board List</h2>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}"><i
                                        class="zmdi zmdi-home"></i> {{ config('app.home_name') }}
                                </a></li>
                            <li class="breadcrumb-item"><a href="{{route('admin.board.list.cards.index')}}">Board List
                                    Card</a>
                            </li>
                            <li class="breadcrumb-item active">Edit</li>
                        </ul>
                        <button class="btn btn-primary btn-icon mobile_menu" type="button">
                            <i class="zmdi zmdi-sort-amount-desc"></i></button>
                    </div>
                    <div class="col-lg-5 col-md-6 col-sm-12 text-right">
                        <a href="{{ route('admin.board.list.cards.index') }}"
                           class="btn btn-success btn-icon rounded-circle" type="button"><i
                                class="zmdi zmdi-arrow-left"></i></a>
                        <button class="btn btn-warning btn-icon rounded-circle right_icon_toggle_btn" type="button">
                            <i class="zmdi zmdi-arrow-right"></i></button>
                    </div>
                </div>
            </div>
            <div class="container-fluid">
                <!-- Basic Validation -->
                <div class="row clearfix">
                    <div class="col-lg-12 col-md-12 col-sm-12">
                        <div class="card">
                            <div class="header">
                                <h2><strong>Edit</strong> Board List Card</h2>
                            </div>
                            <div class="body">
                                <form id="board_list_card_update_form">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group form-float">
                                                <label for="title">Title</label>
                                                <input type="text" class="form-control" id="title" name="title"
                                                       value="{{$board_list_card->title}}" minlength="3" required>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group form-float">
                                                <label for="department">Department</label>
                                                <select id="department" name="department"
                                                        class="form-control cxm-live-search-fix show-tick"
                                                        data-live-search="true"
                                                        data-show-tick="true"
                                                        data-placeholder="Select Department">
                                                    @foreach($departments as $department)
                                                        <option
                                                            value="{{ $department->id }}" {{ optional($board_list_card->getBoardList->getDepartment)->id == $department->id ? 'selected class="btn-warning"' : '' }}>{{ $department->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group form-float">
                                                <label for="board-list">Board List</label>
                                                <select id="board-list" name="board_list"
                                                        class="form-control cxm-live-search-fix show-tick"
                                                        data-live-search="true"
                                                        data-show-tick="true"
                                                        data-placeholder="Select Board List">
                                                    @if(isset($board_list_card->getBoardList->getDepartment->id) && isset($board_list_card->getBoardList->getDepartment->getBoardLists))
                                                        @foreach($board_list_card->getBoardList->getDepartment->getBoardLists as $board_list)
                                                            <option
                                                                value="{{ $board_list->id }}" {{ $board_list_card->board_list_id == $board_list->id ? 'selected class="btn-warning"' : '' }}>{{ $board_list->title }}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group form-float select-div">
                                                <label for="team">Team</label>
                                                <select id="team" name="team"
                                                        class="form-control cxm-live-search-fix show-tick"
                                                        data-live-search="true"
                                                        data-show-tick="true"
                                                        data-placeholder="Select Team">
                                                    @foreach($teams as $team)
                                                        <option
                                                            value="{{ $team->team_key }}" {{ $board_list_card->team_key == $team->team_key ? 'selected class="btn-warning"' : '' }}>{{ $team->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group form-float select-div">
                                                <label for="client">Client</label>
                                                <select id="client" name="client"
                                                        class="form-control cxm-live-search-fix show-tick"
                                                        data-live-search="true"
                                                        data-show-tick="true"
                                                        data-placeholder="Select Client">
                                                    @foreach($clients as $client)
                                                        <option
                                                            value="{{ $client->id }}" {{ $board_list_card->client_id == $client->id ? 'selected class="btn-warning"' : '' }}>{{ $client->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <style>
                                            .show-desc img {
                                                height: 50%;
                                                width: 30%;
                                            }

                                            span.show-desc {
                                                border: 1px solid #ced4da;
                                                padding: 9px;
                                                border-radius: 5px;
                                            }

                                            span.show-desc p {
                                                margin-bottom: 0rem;
                                            }

                                            .ck-restricted-editing_mode_standard.ck.ck-content.ck-editor__editable.ck-rounded-corners.ck-editor__editable_inline.ck-focused {
                                                color: #495057;
                                                background-color: #fff;
                                                border-color: #ffb87f;
                                                outline: 0;
                                                box-shadow: 0 0 0 .2rem rgb(255 153 72 / 24%);
                                                border-radius: 5px !important;
                                            }

                                            .form-control:focus {
                                                border-color: #ffb87f !important;
                                                box-shadow: 0 0 0 .2rem rgb(255 153 72 / 24%) !important;
                                            }

                                            .upload-btn-wrapper input[type="file"] {
                                                font-size: 100px;
                                                position: absolute;
                                                left: 0;
                                                top: 0;
                                                opacity: 0;
                                                height: 100% !important;
                                                width: 100%;
                                            }

                                            .upload-btn-wrapper input[type="file"] {
                                                font-size: 100px;
                                                position: absolute;
                                                left: 0;
                                                top: 0;
                                                opacity: 0;
                                                height: 100% !important;
                                                width: 100%;
                                            }
                                        </style>
                                        <div class="col-md-12">
                                            <div class="form-group form-float">
                                                <label for="description">Description</label>
                                                <span class="show-desc"
                                                      style="cursor:pointer; display: {{$board_list_card->description ? "block" : "none"}};">{!! $board_list_card->description !!}</span>
                                                {{--                                                <textarea class="form-control" id="description" name="description"--}}
                                                {{--                                                          rows="4" required>--}}
                                                {{--                                                    {{ old('description', $board_list_card->description) }}--}}
                                                {{--                                                </textarea>--}}
                                                <div class="ck-desc" style="display: none;">
                                                    <div class="row">
                                                        <div class="col-md-11">
                                                            <textarea id="description" class="ckEditor ck-desc d-none"
                                                                      name="description">{{ old('description', $board_list_card->description) }}</textarea>
                                                        </div>
                                                        <div class="col-md-1">
                                                            <div class="button-group d-flex flex-column"
                                                                 style="width: 40px;">
                                                                <button type="button" id="save-description"
                                                                        class="btn btn-secondary btn-sm mb-1 desc-btn bdr-shadow"
                                                                        style="width: 40px;border: 1px solid #c7c6c6;border-color: #ffb87f !important; color: #ff9948;background-color: #fff;">
                                                                    <i class="fa fa-check">✓</i>
                                                                </button>
                                                                <button type="button" id="close-description"
                                                                        class="btn btn-secondary btn-sm desc-btn bdr-shadow"
                                                                        style="width: 40px;border: 1px solid #c7c6c6;border-color: #ffb87f !important; color: #ff9948;background-color: #fff;">
                                                                    <i class="fa fa-times">x</i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <style>
                                            .image-container {
                                                position: relative;
                                                display: inline-block;
                                            }

                                            .modal-cover-image {
                                                transition: filter 0.3s;
                                            }

                                            .image-container:hover #cover-image-object {
                                                filter: blur(2px);
                                                opacity: 0.5;
                                            }

                                            .bdr-shadow:hover {
                                                box-shadow: 0 0 0 .2rem rgb(255 153 72 / 24%) !important;
                                            }

                                            .tooltip {
                                                visibility: hidden;
                                                width: 120px;
                                                background-color: #0000009c;
                                                color: #fff;
                                                text-align: center;
                                                border-radius: 3px;
                                                padding: 5px;
                                                position: absolute;
                                                z-index: 1;
                                                bottom: 43.5%;
                                                left: 49%;
                                                margin-left: -60px;
                                                opacity: 0;
                                                transition: opacity 0.3s;
                                            }

                                            .image-container:hover .tooltip {
                                                visibility: visible;
                                                opacity: 1;
                                            }


                                        </style>
                                        <div class="col-md-12">
                                            <div class="form-group form-float">
                                                <label for="upload-cover-image">Cover Image</label>
                                                <div class="background-cover"
                                                     style="display: {{ $board_list_card->cover_image ? 'block' : 'none' }}; max-width: 500px; max-height: 500px;">
                                                    <div class="image-container container1" id="container1"
                                                         onclick="document.getElementById('upload-cover-image').click()">
                                                        <object
                                                            data="https://devops.appxeon.com/devops/original/{{$board_list_card->cover_image}}"
                                                            id="cover-image-object" width="500">
                                                            <img class="selected-image modal-cover-image"
                                                                 id="modal-cover-image"
                                                                 src="{{ $cover_image_url_trait($board_list_card) }}"
                                                                 width="500" alt="{{$board_list_card->title}}"
                                                                 loading="lazy">
                                                        </object>
                                                        <span class="tooltip">click to change image</span>
                                                    </div>
                                                </div>
                                                <button type="button" id="remove-cover-image"
                                                        class="btn btn-secondary btn-sm desc-btn bdr-shadow"
                                                        style="display: {{$board_list_card->cover_image == null ? "none":"block"}}; width: 40px;border: 1px solid #c7c6c6;border-color: #ffb87f !important; color: #ff9948;background-color: #fff;width: auto !important;">
                                                    <i class="fa fa-times">Remove Cover Image</i>
                                                </button>
                                                <div class="upload-btn-wrapper border-none" id="upload-btn-wrapper"
                                                     style="display: {{ $board_list_card->cover_image ? 'none' : 'block' }};">
                                                    <input type="file" id="upload-cover-image" name="cover_image"
                                                           accept="image/*"
                                                           class="upload-cover-image"
                                                           onchange="previewCoverImage(event)">
                                                    <label class="file-input__label" for="upload-cover-image">
                                                        <span class="material-symbols-outlined">credit_card</span>
                                                        <span>Upload a Cover Image</span>
                                                    </label>
                                                </div>
                                                <input type="hidden" id="remove_cover_image" name="remove_cover_image" value="0">
                                            </div>
                                        </div>

                                        <style>
                                            .color-list {
                                                display: flex;
                                                justify-content: space-between;
                                                flex-wrap: wrap;
                                            }

                                            .list-diamension {
                                                height: 30px !important;
                                                width: 50px !important;
                                                display: flex;
                                                border-radius: 5px;
                                                cursor: pointer;
                                            }
                                        </style>
                                        <div class="col-md-4 mb-3">
                                            <div class="">
                                                <label for="cover-image-color">Cover Image Color</label>
                                                <input type="color" id="cover-image-color"
                                                       name="cover_background_color" class="form-control"
                                                       value="{{$board_list_card->cover_background_color ?? "#ffffff"}}">
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group form-float">
                                                <label for="cover-image-size">Cover Image Size</label>
                                                <select id="cover-image-size" name="cover_image_size"
                                                        class="form-control show-tick"
                                                        data-placeholder="Select Cover Image Size">
                                                    <option
                                                        value="0" {{ $board_list_card->cover_image_size == 0 ? 'selected class="btn-warning"' : '' }}>
                                                        Without Background Color
                                                    </option>
                                                    <option
                                                        value="1" {{ $board_list_card->cover_image_size == 1 ? 'selected class="btn-warning"' : '' }}>
                                                        With Background Color
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group form-float">
                                                <label for="priority">Priority</label>
                                                <select id="priority" name="priority" class="form-control show-tick"
                                                        data-placeholder="Select Cover Image Size">
                                                    <option
                                                        value="1" {{ $board_list_card->priority == 1 ? 'selected class="btn-warning"' : '' }}>
                                                        Low
                                                    </option>
                                                    <option
                                                        value="2" {{ $board_list_card->priority == 2 ? 'selected class="btn-warning"' : '' }}>
                                                        Medium
                                                    </option>
                                                    <option
                                                        value="3" {{ $board_list_card->priority == 3 ? 'selected class="btn-warning"' : '' }}>
                                                        High
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group form-float">
                                                <label for="is-check-start-date">Is Checked Start Date</label>
                                                <select id="is-check-start-date" name="is_check_start_date"
                                                        class="form-control show-tick"
                                                        data-placeholder="Select Is Checked Start Date">
                                                    <option
                                                        value="0" {{ $board_list_card->is_check_start_date == 0 ? 'selected class="btn-warning"' : '' }}>
                                                        UnChecked
                                                    </option>
                                                    <option
                                                        value="1" {{ $board_list_card->is_check_start_date == 1 ? 'selected class="btn-warning"' : '' }}>
                                                        Checked
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group form-float">
                                                <label for="start-date">Start Date</label>
                                                <input id="start-date" type="date" class="form-control"
                                                       value="{{ $board_list_card->start_date ? \Carbon\Carbon::parse($board_list_card->start_date)->format('Y-m-d') : '' }}"
                                                       name="start_date">
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group form-float">
                                                <label for="is-check-due-date">Is Checked Due Date</label>
                                                <select id="is-check-due-date" name="is_check_due_date"
                                                        class="form-control show-tick"
                                                        data-placeholder="Select Is Checked Due Date">
                                                    <option
                                                        value="0" {{ $board_list_card->is_check_due_date == 0 ? 'selected class="btn-warning"' : '' }}>
                                                        UnChecked
                                                    </option>
                                                    <option
                                                        value="1" {{ $board_list_card->is_check_due_date == 1 ? 'selected class="btn-warning"' : '' }}>
                                                        Checked
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group form-float">
                                                <label for="due-date">Due Date</label>
                                                <input id="due-date" type="date" class="form-control"
                                                       value="{{ $board_list_card->due_date ? \Carbon\Carbon::parse($board_list_card->due_date)->format('Y-m-d') : '' }}"
                                                       name="due_date">
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group form-float">
                                                <label for="task-completed">Task Completed</label>
                                                <select id="task-completed" name="task_completed"
                                                        class="form-control show-tick"
                                                        data-placeholder="Select Task Completed">
                                                    <option
                                                        value="0" {{ $board_list_card->task_completed == 0 ? 'selected class="btn-warning"' : '' }}>
                                                        In complete
                                                    </option>
                                                    <option
                                                        value="1" {{ $board_list_card->task_completed == 1 ? 'selected class="btn-warning"' : '' }}>
                                                        Completed
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group form-float">
                                                <label for="position">Position</label>
                                                <input id="position" type="number" class="form-control"
                                                       value="{{$board_list_card->position}}" name="position"
                                                       required>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="custom-control custom-switch mb-3">
                                                <input type="checkbox" data-id="{{$board_list_card->id}}"
                                                       class="custom-control-input toggle-class change-status"
                                                       id="customSwitchstatus{{$board_list_card->id}}"
                                                       name="status" {{$board_list_card->status == 1 ? "checked" : ""}}>
                                                <label class="custom-control-label"
                                                       for="customSwitchstatus{{$board_list_card->id}}">Status</label>
                                            </div>
                                        </div>
                                    </div>
                                    <input id="update_data" type="submit" value="Submit"
                                           class="btn btn-warning btn-round">
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@push('cxmScripts')
    @include('admin.board-list.cards.ckeditor-script')
    @include('admin.board-list.cards.script')

    <script>
        $(document).ready(function () {
            $('#team').selectpicker();
        });

        $(".show-desc").on("click", function () {
            $(this).hide();
            $(".ck-desc").show();
        });
        $(".desc-btn").on("click", function () {
            $(".ck-desc").hide();
            $(".show-desc").show();
        });
        $("#save-description").on("click", function () {
            $(".show-desc").html(MyEditor["description"].getData());
            $("#description").val(MyEditor["description"].getData());
        });
        $("#close-description").on("click", function () {
            MyEditor["description"].setData($("#description").val());
        });

        $("#remove-cover-image").on('click', function () {
            $('#remove-cover-image , .background-cover').hide();
            $('#upload-cover-image').val('');
            $('.background-cover #modal-cover-image').attr('src', '');
            $('.background-cover #cover-image-object').attr('data', '');
            $('#upload-btn-wrapper').show();
            $('#remove_cover_image').val('1');
            $('#remove_cover_image').val('1');
        });

        function previewCoverImage(event) {
            const file = event.target.files[0];
            const $backgroundCover = $('.background-cover');
            const $modalCoverImage = $backgroundCover.find('#modal-cover-image');
            const $coverImageObject = $backgroundCover.find('#cover-image-object');
            const $uploadBtnWrapper = $('#upload-btn-wrapper');

            if (!file) {
                return;
            }

            const reader = new FileReader();
            reader.onload = function (e) {
                $modalCoverImage.attr('src', e.target.result);
                $coverImageObject.attr('data', e.target.result);
                $('#remove-cover-image').show();
                $backgroundCover.show();
                $uploadBtnWrapper.hide();
            };

            reader.onerror = function () {
                console.error('Error reading file');
            };

            reader.readAsDataURL(file);
        }
    </script>

@endpush

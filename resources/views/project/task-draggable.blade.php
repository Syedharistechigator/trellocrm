@extends('layouts.app')

@section('cxmTitle', 'Project Task')

@section('content')

    <!-- Todo task drageable page start -->
    <section class="content">
        <div class="body_scroll">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="proj-head">
                            <h3><a href="javascript:;">Business Card</a> Tasks</h3>
                            <div class="filter-buttons">
                                <a href="javascript:;" class="btn-project" data-toggle="modal"
                                   data-target="#creatprojectModal">Create Task</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="drag-sec">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="menu-container">
                        <ul class="drage-menu">
                            <div class="menu_bar">
                                <div class="menu_handle" data-toggle="tooltip" title="Drag N Drop"><span
                                        class="icon-hand fa-xs"></span></div>
                                <span class="menu_title">Panel 1</span>
                            </div>

                            <li class="submenu">
                                <div class="submenu_handle" data-toggle="tooltip" title="Drag N Drop">
                                    <span class="icon-hand fa-xs"></span>
                                </div>
                                <span class="submenu_title">Block A: Lorem ipsum dolor sit amet, consectetur adipiscing
                                elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</span>
                            </li>

                            <li class="submenu">
                                <div class="submenu_handle" data-toggle="tooltip" title="Drag N Drop">
                                    <span class="icon-hand fa-xs"></span>
                                </div>
                                <span class="submenu_title">Block B: Lorem ipsum dolor sit amet, consectetur adipiscing
                                elit.</span>
                            </li>

                            <li class="submenu">
                                <div class="submenu_handle" data-toggle="tooltip" title="Drag N Drop">
                                    <span class="icon-hand fa-xs"></span>
                                </div>
                                <span class="submenu_title">Block C: Lorem ipsum dolor dolore magna aliqua.</span>
                            </li>
                        </ul>

                        <ul class="drage-menu">
                            <div class="menu_bar">
                                <div class="menu_handle" data-toggle="tooltip" title="Drag N Drop">
                                    <span class="icon-hand fa-xs"></span>
                                </div>
                                <span class="menu_title">Panel 2</span>
                            </div>
                        </ul>

                        <ul class="drage-menu">
                            <div class="menu_bar">
                                <div class="menu_handle" data-toggle="tooltip" title="Drag N Drop">
                                    <span class="icon-hand fa-xs"></span>
                                </div>
                                <span class="menu_title">Panel 3</span>
                            </div>
                        </ul>

                        <ul class="drage-menu">
                            <div class="menu_bar">
                                <div class="menu_handle" data-toggle="tooltip" title="Drag N Drop">
                                    <span class="icon-hand fa-xs"></span>
                                </div>
                                <span class="menu_title">Panel 4</span>
                            </div>
                        </ul>

                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Todo task drageable page end -->

@endsection
@push('cxmScripts')
@endpush

@extends('layouts.app')

@section('cxmTitle', 'Project Task')

@section('content')

    <!-- Todo task page start -->
<section class="content">
    <div class="body_scroll">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="proj-head">
                        <h3>Tasks</h3>
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

<section class="task-pg-sec">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="section-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="sec-list-form">
                                                <form>
                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <input type="text" class="form-control"
                                                                       placeholder="Project Name">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <select id="inputState" class="form-control">
                                                                    <option selected>Select Status</option>
                                                                    <option>Done</option>
                                                                    <option>Todo</option>
                                                                    <option>Inprogress</option>
                                                                    <option>Review</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <input type="text" class="form-control"
                                                                       placeholder="Task Due Dates Between">
                                                            </div>
                                                        </div>


                                                    </div>
                                                    <div class="row mt-3">
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <select id="inputClients" class="form-control">
                                                                    <option selected>Select Client</option>
                                                                    <option>Client Name</option>
                                                                    <option>Client Name</option>
                                                                    <option>Client Name</option>
                                                                    <option>Client Name</option>
                                                                    <option>Client Name</option>
                                                                </select>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <select id="inputUser" class="form-control">
                                                                    <option selected>Select User</option>
                                                                    <option>User Name</option>
                                                                    <option>User Name</option>
                                                                    <option>User Name</option>
                                                                    <option>User Name</option>
                                                                    <option>User Name</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <button type="submit"
                                                                    class="btn btn-primary">Filter
                                                            </button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>

                                        </div>
                                    </div>
                                    <div class="bootstrap-table bootstrap4">
                                        <div class="fixed-table-toolbar">
                                            <div class="bs-bars float-left"></div>
                                            <div class="columns columns-right btn-group float-right">
                                                <button
                                                    class="btn btn-secondary" type="button" name="refresh"
                                                    aria-label="Refresh" title="Refresh">
                                                    <i class="zmdi zmdi-refresh-sync"></i>

                                                </button>
                                                <div class="keep-open btn-group" title="Columns">
                                                    <button class="btn btn-secondary dropdown-toggle" type="button"
                                                            data-toggle="dropdown" aria-label="Columns"
                                                            title="Columns">
                                                        <i class="zmdi zmdi-format-list-bulleted"></i>

                                                        <span class="caret"></span>
                                                    </button>
                                                    <div class="dropdown-menu dropdown-menu-right"><label
                                                            class="dropdown-item dropdown-item-marker"><input
                                                                type="checkbox" data-field="id" value="0">
                                                            <span>ID</span></label><label
                                                            class="dropdown-item dropdown-item-marker"><input
                                                                type="checkbox" data-field="title" value="1"
                                                                checked="checked"> <span>Tasks</span></label><label
                                                            class="dropdown-item dropdown-item-marker"><input
                                                                type="checkbox" data-field="project_id" value="2">
                                                            <span>ID</span></label><label
                                                            class="dropdown-item dropdown-item-marker"><input
                                                                type="checkbox" data-field="project_title" value="3"
                                                                checked="checked">
                                                            <span>Projects</span></label><label
                                                            class="dropdown-item dropdown-item-marker"><input
                                                                type="checkbox" data-field="projects_userss"
                                                                value="4"
                                                                checked="checked"> <span>Users</span></label><label
                                                            class="dropdown-item dropdown-item-marker"><input
                                                                type="checkbox" data-field="projects_clientss"
                                                                value="5"
                                                                checked="checked">
                                                            <span>Clients</span></label><label
                                                            class="dropdown-item dropdown-item-marker"><input
                                                                type="checkbox" data-field="description" value="6">
                                                            <span>Description</span></label><label
                                                            class="dropdown-item dropdown-item-marker"><input
                                                                type="checkbox" data-field="priority" value="7">
                                                            <span>Priority</span></label><label
                                                            class="dropdown-item dropdown-item-marker"><input
                                                                type="checkbox" data-field="status" value="8"
                                                                checked="checked"> <span>Status</span></label><label
                                                            class="dropdown-item dropdown-item-marker"><input
                                                                type="checkbox" data-field="due_date" value="9">
                                                            <span>Due Date</span></label><label
                                                            class="dropdown-item dropdown-item-marker"><input
                                                                type="checkbox" data-field="action" value="10"
                                                                checked="checked"> <span>Action</span></label></div>
                                                </div>
                                                <div class="export btn-group">
                                                    <button class="btn btn-secondary dropdown-toggle"
                                                            aria-label="Export" data-toggle="dropdown" type="button"
                                                            title="Export data">
                                                        <i class="zmdi zmdi-download"></i>

                                                        <span class="caret"></span>
                                                    </button>
                                                    <div class="dropdown-menu dropdown-menu-right"><a
                                                            class="dropdown-item " href="#"
                                                            data-type="txt">TXT</a><a
                                                            class="dropdown-item " href="#"
                                                            data-type="excel">MS-Excel</a></div>
                                                </div>
                                            </div>
                                            <div class="float-right search btn-group">
                                                <input class="form-control

        search-input" type="text" placeholder="Search" autocomplete="off">
                                            </div>
                                        </div>

                                        <div class="fixed-table-container">
                                            <div class="fixed-table-header">
                                                <table></table>
                                            </div>
                                            <div class="fixed-table-body">
                                                <div class="fixed-table-loading table table-bordered table-hover">
                                                    <span class="loading-wrap">
                                                        <span class="loading-text">Loading, please wait</span>
                                                        <span class="animation-wrap"><span
                                                                class="animation-dot"></span></span>
                                                    </span>
                                                </div>
                                                <table class="table-striped table table-bordered table-hover"
                                                       id="tasks_list" data-toggle="table"
                                                       data-url="https://taskhub.company/projects/get_projects_list"
                                                       data-click-to-select="true" data-side-pagination="server"
                                                       data-pagination="true"
                                                       data-page-list="[5, 10, 20, 50, 100, 200]"
                                                       data-search="true" data-show-columns="true"
                                                       data-show-refresh="true"
                                                       data-trim-on-search="false" data-sort-name="id"
                                                       data-sort-order="desc" data-mobile-responsive="true"
                                                       data-toolbar=""
                                                       data-show-export="true" data-maintain-selected="true"
                                                       data-export-types="[&quot;txt&quot;,&quot;excel&quot;]"
                                                       data-export-options="{
                      &quot;fileName&quot;: &quot;tasks-list&quot;,
                      &quot;ignoreColumn&quot;: [&quot;state&quot;]
                    }" data-query-params="queryParams2">
                                                    <thead>
                                                    <tr>
                                                        <th data-field="title">
                                                            <div class="th-inner sortable both">Tasks</div>
                                                            <div class="fht-cell"></div>
                                                        </th>
                                                        <th data-field="project_title">
                                                            <div class="th-inner sortable both">Projects</div>
                                                            <div class="fht-cell"></div>
                                                        </th>
                                                        <th data-field="projects_userss">
                                                            <div class="th-inner ">Users</div>
                                                            <div class="fht-cell"></div>
                                                        </th>
                                                        <th data-field="projects_clientss">
                                                            <div class="th-inner ">Clients</div>
                                                            <div class="fht-cell"></div>
                                                        </th>
                                                        <th data-field="status">
                                                            <div class="th-inner sortable both">Status</div>
                                                            <div class="fht-cell"></div>
                                                        </th>
                                                        <th data-field="action">
                                                            <div class="th-inner ">Action</div>
                                                            <div class="fht-cell"></div>
                                                        </th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    <tr data-index="0">
                                                        <td>Basic Logo Concept</td>
                                                        <td>نوێ کردنەوە اقامه</td>
                                                        <td>
                                                            <li class="media">
                                                                <a href="https://taskhub.company/assets/profiles/1608193549.684.jpg"
                                                                   data-lightbox="images" data-title="Main">
                                                                    <img alt="image" class="mr-1 rounded-circle"
                                                                         width="30"
                                                                         src="https://taskhub.company/assets/profiles/1608193549.684.jpg">
                                                                </a>
                                                            </li>
                                                        </td>
                                                        <td>
                                                            <li class="media">
                                                                <figure class="avatar mr-1 avatar-sm"
                                                                        data-toggle="tooltip" data-title="Lazarina"
                                                                        data-initial="LE">
                                                                </figure>
                                                            </li>
                                                        </td>
                                                        <td>
                                                            <div class="badge badge-warning projects-badge">Review
                                                            </div>
                                                        </td>
                                                        <td><a href="https://taskhub.company/projects/tasks/936"
                                                               class="btn btn-light"><i
                                                                    class="zmdi zmdi-eye"></i></a>
                                                        </td>
                                                    </tr>
                                                    <tr data-index="1">
                                                        <td>Task 1</td>
                                                        <td>Create</td>
                                                        <td>
                                                            <li class="media">
                                                                <a href="https://taskhub.company/assets/profiles/1608193549.684.jpg"
                                                                   data-lightbox="images" data-title="Main">
                                                                    <img alt="image" class="mr-1 rounded-circle"
                                                                         width="30"
                                                                         src="https://taskhub.company/assets/profiles/1608193549.684.jpg">
                                                                </a><a
                                                                    href="https://taskhub.company/assets/profiles/1582696898.1046.jpg"
                                                                    data-lightbox="images" data-title="Karan">
                                                                    <img alt="image" class="mr-1 rounded-circle"
                                                                         width="30"
                                                                         src="https://taskhub.company/assets/profiles/1582696898.1046.jpg">
                                                                </a>
                                                            </li>
                                                        </td>
                                                        <td>
                                                            <li class="media">
                                                                <a href="https://taskhub.company/assets/profiles/1606123920.5628.jpg"
                                                                   data-lightbox="images" data-title="Sara">
                                                                    <img alt="image" class="mr-3 rounded-circle"
                                                                         width="50"
                                                                         src="https://taskhub.company/assets/profiles/1606123920.5628.jpg">
                                                                </a>
                                                            </li>
                                                        </td>
                                                        <td>
                                                            <div class="badge badge-warning projects-badge">Review
                                                            </div>
                                                        </td>
                                                        <td><a href="https://taskhub.company/projects/tasks/997"
                                                               class="btn btn-light"><i
                                                                    class="zmdi zmdi-eye"></i></a>
                                                        </td>
                                                    </tr>
                                                    <tr data-index="2">
                                                        <td>Test</td>
                                                        <td>Business Card</td>
                                                        <td>
                                                            <li class="media">
                                                                <a href="https://taskhub.company/assets/profiles/1608193549.684.jpg"
                                                                   data-lightbox="images" data-title="Main">
                                                                    <img alt="image" class="mr-1 rounded-circle"
                                                                         width="30"
                                                                         src="https://taskhub.company/assets/profiles/1608193549.684.jpg">
                                                                </a>
                                                            </li>
                                                        </td>
                                                        <td>
                                                            <li class="media">
                                                                <a href="https://taskhub.company/assets/profiles/1606123920.5628.jpg"
                                                                   data-lightbox="images" data-title="Sara">
                                                                    <img alt="image" class="mr-3 rounded-circle"
                                                                         width="50"
                                                                         src="https://taskhub.company/assets/profiles/1606123920.5628.jpg">
                                                                </a>
                                                            </li>
                                                        </td>
                                                        <td>
                                                            <div class="badge badge-danger projects-badge">
                                                                Inprogress
                                                            </div>
                                                        </td>
                                                        <td><a href="https://taskhub.company/projects/tasks/993"
                                                               class="btn btn-light"><i
                                                                    class="zmdi zmdi-eye"></i></a>
                                                        </td>
                                                    </tr>
                                                    <tr data-index="3">
                                                        <td>asdasda</td>
                                                        <td>Vghcfycddg</td>
                                                        <td>
                                                            <li class="media">
                                                                <a href="https://taskhub.company/assets/profiles/1608193549.684.jpg"
                                                                   data-lightbox="images" data-title="Main">
                                                                    <img alt="image" class="mr-1 rounded-circle"
                                                                         width="30"
                                                                         src="https://taskhub.company/assets/profiles/1608193549.684.jpg">
                                                                </a><a
                                                                    href="https://taskhub.company/assets/profiles/1582696898.1046.jpg"
                                                                    data-lightbox="images" data-title="Karan">
                                                                    <img alt="image" class="mr-1 rounded-circle"
                                                                         width="30"
                                                                         src="https://taskhub.company/assets/profiles/1582696898.1046.jpg">
                                                                </a>
                                                            </li>
                                                        </td>
                                                        <td>
                                                            <li class="media">
                                                                <figure class="avatar mr-1 avatar-sm"
                                                                        data-toggle="tooltip"
                                                                        data-title="O Poder do MAsterMind"
                                                                        data-initial="ON">
                                                                </figure>
                                                            </li>
                                                        </td>
                                                        <td>
                                                            <div class="badge badge-info projects-badge">Todo</div>
                                                        </td>
                                                        <td><a href="https://taskhub.company/projects/tasks/988"
                                                               class="btn btn-light"><i
                                                                    class="zmdi zmdi-eye"></i></a>
                                                        </td>
                                                    </tr>
                                                    <tr data-index="4">
                                                        <td>data</td>
                                                        <td>Tzi Project</td>
                                                        <td>
                                                            <li class="media">
                                                                <a href="https://taskhub.company/assets/profiles/1608193549.684.jpg"
                                                                   data-lightbox="images" data-title="Main">
                                                                    <img alt="image" class="mr-1 rounded-circle"
                                                                         width="30"
                                                                         src="https://taskhub.company/assets/profiles/1608193549.684.jpg">
                                                                </a><a
                                                                    href="https://taskhub.company/assets/profiles/1582711520.7795.jpg"
                                                                    data-lightbox="images" data-title="Sagar">
                                                                    <img alt="image" class="mr-1 rounded-circle"
                                                                         width="30"
                                                                         src="https://taskhub.company/assets/profiles/1582711520.7795.jpg">
                                                                </a>
                                                            </li>
                                                        </td>
                                                        <td>
                                                            <li class="media">
                                                                <a href="https://taskhub.company/assets/profiles/1606123920.5628.jpg"
                                                                   data-lightbox="images" data-title="Sara">
                                                                    <img alt="image" class="mr-3 rounded-circle"
                                                                         width="50"
                                                                         src="https://taskhub.company/assets/profiles/1606123920.5628.jpg">
                                                                </a>
                                                            </li>
                                                        </td>
                                                        <td>
                                                            <div class="badge badge-danger projects-badge">
                                                                Inprogress
                                                            </div>
                                                        </td>
                                                        <td><a href="https://taskhub.company/projects/tasks/987"
                                                               class="btn btn-light"><i
                                                                    class="zmdi zmdi-eye"></i></a>
                                                        </td>
                                                    </tr>
                                                    <tr data-index="5">
                                                        <td>3</td>
                                                        <td>55555555555555</td>
                                                        <td>
                                                            <li class="media">
                                                                <a href="https://taskhub.company/assets/profiles/1608193549.684.jpg"
                                                                   data-lightbox="images" data-title="Main">
                                                                    <img alt="image" class="mr-1 rounded-circle"
                                                                         width="30"
                                                                         src="https://taskhub.company/assets/profiles/1608193549.684.jpg">
                                                                </a>
                                                            </li>
                                                        </td>
                                                        <td>
                                                            <li class="media">
                                                                <a href="https://taskhub.company/assets/profiles/1606123920.5628.jpg"
                                                                   data-lightbox="images" data-title="Sara">
                                                                    <img alt="image" class="mr-3 rounded-circle"
                                                                         width="50"
                                                                         src="https://taskhub.company/assets/profiles/1606123920.5628.jpg">
                                                                </a>
                                                            </li>
                                                        </td>
                                                        <td>
                                                            <div class="badge badge-info projects-badge">Todo</div>
                                                        </td>
                                                        <td><a href="https://taskhub.company/projects/tasks/986"
                                                               class="btn btn-light"><i
                                                                    class="zmdi zmdi-eye"></i></a>
                                                        </td>
                                                    </tr>
                                                    <tr data-index="6">
                                                        <td>test</td>
                                                        <td>Project 1</td>
                                                        <td>
                                                            <li class="media">
                                                                <a href="https://taskhub.company/assets/profiles/1608193549.684.jpg"
                                                                   data-lightbox="images" data-title="Main">
                                                                    <img alt="image" class="mr-1 rounded-circle"
                                                                         width="30"
                                                                         src="https://taskhub.company/assets/profiles/1608193549.684.jpg">
                                                                </a>
                                                            </li>
                                                        </td>
                                                        <td>
                                                            <li class="media">
                                                                <a href="https://taskhub.company/assets/profiles/1606123920.5628.jpg"
                                                                   data-lightbox="images" data-title="Sara">
                                                                    <img alt="image" class="mr-3 rounded-circle"
                                                                         width="50"
                                                                         src="https://taskhub.company/assets/profiles/1606123920.5628.jpg">
                                                                </a>
                                                            </li>
                                                        </td>
                                                        <td>
                                                            <div class="badge badge-info projects-badge">Todo</div>
                                                        </td>
                                                        <td><a href="https://taskhub.company/projects/tasks/969"
                                                               class="btn btn-light"><i
                                                                    class="zmdi zmdi-eye"></i></a>
                                                        </td>
                                                    </tr>
                                                    <tr data-index="7">
                                                        <td>demo</td>
                                                        <td>Demo</td>
                                                        <td>
                                                            <li class="media">
                                                                <a href="https://taskhub.company/assets/profiles/1608193549.684.jpg"
                                                                   data-lightbox="images" data-title="Main">
                                                                    <img alt="image" class="mr-1 rounded-circle"
                                                                         width="30"
                                                                         src="https://taskhub.company/assets/profiles/1608193549.684.jpg">
                                                                </a>
                                                            </li>
                                                        </td>
                                                        <td>Not Found!</td>
                                                        <td>
                                                            <div class="badge badge-danger projects-badge">
                                                                Inprogress
                                                            </div>
                                                        </td>
                                                        <td><a href="https://taskhub.company/projects/tasks/980"
                                                               class="btn btn-light"><i
                                                                    class="zmdi zmdi-eye"></i></a>
                                                        </td>
                                                    </tr>
                                                    <tr data-index="8">
                                                        <td>teste</td>
                                                        <td>Pablo Nicolás Test</td>
                                                        <td>
                                                            <li class="media">
                                                                <a href="https://taskhub.company/assets/profiles/1608193549.684.jpg"
                                                                   data-lightbox="images" data-title="Main">
                                                                    <img alt="image" class="mr-1 rounded-circle"
                                                                         width="30"
                                                                         src="https://taskhub.company/assets/profiles/1608193549.684.jpg">
                                                                </a><a
                                                                    href="https://taskhub.company/assets/profiles/1582696898.1046.jpg"
                                                                    data-lightbox="images" data-title="Karan">
                                                                    <img alt="image" class="mr-1 rounded-circle"
                                                                         width="30"
                                                                         src="https://taskhub.company/assets/profiles/1582696898.1046.jpg">
                                                                </a>
                                                                <figure class="avatar mr-1 avatar-sm"
                                                                        data-toggle="tooltip" data-title="+4"
                                                                        data-initial="+4">
                                                                </figure>
                                                            </li>
                                                        </td>
                                                        <td>
                                                            <li class="media">
                                                                <figure class="avatar mr-1 avatar-sm"
                                                                        data-toggle="tooltip" data-title="Test New"
                                                                        data-initial="TT">
                                                                </figure>
                                                            </li>
                                                        </td>
                                                        <td>
                                                            <div class="badge badge-warning projects-badge">Review
                                                            </div>
                                                        </td>
                                                        <td><a href="https://taskhub.company/projects/tasks/963"
                                                               class="btn btn-light"><i
                                                                    class="zmdi zmdi-eye"></i></a>
                                                        </td>
                                                    </tr>
                                                    <tr data-index="9">
                                                        <td>Despacho</td>
                                                        <td>Mchroo3</td>
                                                        <td>
                                                            <li class="media">
                                                                <a href="https://taskhub.company/assets/profiles/1608193549.684.jpg"
                                                                   data-lightbox="images" data-title="Main">
                                                                    <img alt="image" class="mr-1 rounded-circle"
                                                                         width="30"
                                                                         src="https://taskhub.company/assets/profiles/1608193549.684.jpg">
                                                                </a>
                                                            </li>
                                                        </td>
                                                        <td>
                                                            <li class="media">
                                                                <a href="https://taskhub.company/assets/profiles/1606123920.5628.jpg"
                                                                   data-lightbox="images" data-title="Sara">
                                                                    <img alt="image" class="mr-3 rounded-circle"
                                                                         width="50"
                                                                         src="https://taskhub.company/assets/profiles/1606123920.5628.jpg">
                                                                </a>
                                                            </li>
                                                        </td>
                                                        <td>
                                                            <div class="badge badge-info projects-badge">Todo</div>
                                                        </td>
                                                        <td><a href="https://taskhub.company/projects/tasks/938"
                                                               class="btn btn-light"><i
                                                                    class="zmdi zmdi-eye"></i></a>
                                                        </td>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                            <div class="fixed-table-footer">
                                                <table>
                                                    <thead>
                                                    <tr></tr>
                                                    </thead>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="fixed-table-pagination">
                                            <div class="float-left pagination-detail">
                                                <span class="pagination-info">
                                                    Showing 1 to 10 of 131 rows
                                                </span><span class="page-list"><span class="btn-group dropdown dropup">
                                                        <button class="btn btn-secondary dropdown-toggle" type="button"
                                                                data-toggle="dropdown" aria-expanded="false">
                                                            <span class="page-size">
                                                                10
                                                            </span>
                                                            <span class="caret"></span>
                                                        </button>
                                                        <div class="dropdown-menu" x-placement="top-start"
                                                             style="position: absolute; transform: translate3d(0px, 0px, 0px); top: 0px; left: 0px; will-change: transform;">
                                                            <a class="dropdown-item " href="#">5</a><a
                                                                class="dropdown-item active" href="#">10</a><a
                                                                class="dropdown-item " href="#">20</a><a
                                                                class="dropdown-item " href="#">50</a><a
                                                                class="dropdown-item " href="#">100</a><a
                                                                class="dropdown-item " href="#">200</a>
                                                        </div>
                                                    </span> rows per page</span>
                                            </div>
                                            <div class="float-right pagination">
                                                <ul class="pagination">
                                                    <li class="page-item page-pre"><a class="page-link"
                                                                                      aria-label="previous page"
                                                                                      href="javascript:void(0)">‹</a>
                                                    </li>
                                                    <li class="page-item active"><a class="page-link"
                                                                                    aria-label="to page 1"
                                                                                    href="javascript:void(0)">1</a>
                                                    </li>
                                                    <li class="page-item"><a class="page-link"
                                                                             aria-label="to page 2"
                                                                             href="javascript:void(0)">2</a></li>
                                                    <li class="page-item"><a class="page-link"
                                                                             aria-label="to page 3"
                                                                             href="javascript:void(0)">3</a></li>
                                                    <li class="page-item"><a class="page-link"
                                                                             aria-label="to page 4"
                                                                             href="javascript:void(0)">4</a></li>
                                                    <li class="page-item"><a class="page-link"
                                                                             aria-label="to page 5"
                                                                             href="javascript:void(0)">5</a></li>
                                                    <li class="page-item page-last-separator disabled"><a
                                                            class="page-link" aria-label=""
                                                            href="javascript:void(0)">...</a></li>
                                                    <li class="page-item"><a class="page-link"
                                                                             aria-label="to page 14"
                                                                             href="javascript:void(0)">14</a></li>
                                                    <li class="page-item page-next"><a class="page-link"
                                                                                       aria-label="next page"
                                                                                       href="javascript:void(0)">›</a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                            </div>
                        </div>


                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Todo task page end -->
@endsection
@push('cxmScripts')
@endpush

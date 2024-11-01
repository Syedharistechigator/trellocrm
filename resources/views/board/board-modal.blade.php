<div class="create-project-modal" id="modal-board-list-card-id">
	<div class="modal fade" id="edit-board-list-card-modal" tabindex="-1" role="dialog" aria-labelledby="edit_title" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered" role="document">
			<div class="modal-content">
				<div class="modal-header modal-card">
					<div class="background-cover">
						<div class="image-container container1" id="container1" onclick="document.getElementById('upload-cover-image').click()">
							<img class="selected-image modal-cover-image" id="modal-cover-image" src="" alt="">
						</div>
					</div>
					<div class="modal-card-header">
						<span class="material-symbols-outlined lg-icon">laptop_mac</span>
						<div class="front-title">
							<!-- <h5 class="modal-title" id="edit_title"></h5> -->
							<textarea id="edit_title" class="editableText"></textarea>
						</div>
						<button type="button" class="close lg-icon close-modal-card" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="move-pop-list">
						<h6 class="mov-pop-heading">
							<span>in list</span>
							<a href="javascript:void(0);" id="board-list-name" class="js-open-move-from-header"></a>
						</h6>
						<!-- <div class="popoverContainer popover">
							<div class="pop-header">
							<h4 class="mem-mov-heading">Move Card</h4>
								<button class="close-btn close-mov-member" onclick="closePopover()"><span class="material-symbols-outlined">close</span></button>
							</div>
							<div class="popoverContent">
								<p>This is the content of the popover.</p>

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
																		<option>Review
																		</option>
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
						</div> -->
					</div>
				</div>
				<div class="modal-body">
					<div class="container-fluid">
						<div class="row">
							<div class="col-md-9 pl-0 pr-1">
								<div>
									<div id="drop-zone">
										<p>Drop files here</p>
									</div>
									<div id="dropped-image"></div>
								</div>
								<div class="pop-detail">
									<div class="pd-one pd-one-padding labelArea">
										<div class="membr">
											<h5>Members</h5>
											<ul class="mem-list card-member-list">
												{{--For Other User to Add--}}
												<li class="card-member-list-static">
													<div class="dropdown">
														<button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
															<i class="fas fa-plus"></i>
														</button>
														@include('board.assign-new-board-members')
													</div>
												</li>
												<div class="member-profile"></div>
											</ul>
										</div>
										@php
											$auth = auth()->user();
										@endphp
										<div class="membr label-list">
											<h5 class="label-heading d-none">Labeling</h5>
											<ul class="mem-list">
												<li class="card-member-list-static">
													<div class="dropdown label-select">
														<button class="btn btn-secondary dropdown-toggle button-label" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
															<label style="padding: 4px; border-radius: 4px;"></label>
														</button>
													</div>
												</li> <li class="card-member-list-static">
													<div class="dropdown label-modal">
														<button class="btn btn-secondary dropdown-toggle label-icon-btn d-none" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
															<i class="fas fa-plus"></i>
														</button>
														{{--                                                        @include('board.assign-new-board-labels')--}}
													</div>
												</li>
											</ul>
										</div>
									</div>
									<h4 class="description-heading">
										<span class="material-symbols-outlined lg-icon">subject</span> Description </h4>
									{{--<button type="button" class="ck-button ck-save-button"
											id="ck-save-button1">Edit
									</button>--}}
									<div class="pd-one-padding">
										<div class="comment-box">
											<form>
												<div class="form-group">
													<div class="container">
														<div class="description">
															<div>
																<span class="show-desc" style="display: none;"></span>
																<span class="hide-desc">Add a more detailed
                                                                    description…</span>
															</div>
															<div class="ck-desc" style="display: none;">
																<textarea id="editor1" class="ckEditor" name="description"></textarea>
																<button type="button" class="ck-button ck-save-button" id="ck-save-button1" disabled>Save
																</button>
																<button type="button" class="ck-close-button btn btn-danger" id="ck-close-button1" data-id="1">
																	Discard
																</button>
															</div>
														</div>
													</div>
												</div>
											</form>
										</div>
									</div>
									<div class="des-div-flex">
										<h4 class="description-heading"><span class="material-symbols-outlined attac-icon lg-icon">
                                                attach_file
                                            </span> Attachments</h4>
										<div class="dropdown">
											<a href="javascript:void(0);" class="att-add" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Add</a>
											@include('board.attachment-dropdown')
										</div>
									</div>
									<div class="main-attach-flex">
										<div id="attachments">
											<h2>Attachments</h2>
										</div>
										<div class="append-attachments"></div>
										{{--<div class="attachment-div">
											<a href="javascript:void(0);" class="attachment-thumbnail">
												<span class="att-name">docx</span> </a>
											<div class="attachment-thumbnail-detail">
												<h6 class="attachment-file-name">Giving.docx </h6>
												<span class="attatchment-time">Added yesterday at 10:09 PM</span>
												<span class="attatchment-link"><a href="javascript:void(0);">Comment</a></span>
												<span class="attatchment-link"><a href="javascript:void(0);">Delete</a></span>
												<span class="attatchment-link"><a
														href="javascript:void(0);">Edit</a></span>
											</div>
										</div>
										<div class="attachment-div">
											<a href="javascript:void(0);" class="attachment-thumbnail">
												<!-- <span class="att-name">Image</span> -->
												<span class="att-name"><img src="" alt="image"></span> </a>
											<div class="attachment-thumbnail-detail">
												<h6 class="attachment-file-name">image.jpg </h6>
												<span class="attatchment-time">Added yesterday at 10:09 PM</span>
												<span class="attatchment-link"><a href="javascript:void(0);">Comment</a></span>
												<span class="attatchment-link"><a href="javascript:void(0);">Delete</a></span>
												<span class="attatchment-link"><a
														href="javascript:void(0);">Edit</a></span>
												<div class="make-cover">
													<a href="javascript:void(0);"><span
															class="material-symbols-outlined"> credit_card </span> Make
														Cover</a>
												</div>
											</div>
										</div>--}}
									</div>
									<button href="javascript:void(0);" class="btn btn-light js-show-hide-attachments" style="display: none">Show fewer attachments.</button>
									<div class="des-div-flex">
										<h4 class="description-heading">
											<span class="material-symbols-outlined lg-icon">sort</span>Activity </h4>
										<button href="javascript:void(0);" class="btn btn-light att-add activity-detail show">Hide Details</button>
									</div>
									<div class="comment-div d-flex align-items-start " id="comment-div">
										<div class="por-small">
                                            <span class="member-avatar
                                            @php
                                            $fnl = strtolower(auth()->user()->name[0]);
                                            @endphp
                                            {{$fnl >= 'a' && $fnl <= 'e' ? "color1" :( $fnl >= 'f' && $fnl <= 'j' ? "color2" : ($fnl >= 'k' && $fnl <= 'o' ? "color3" : ($fnl >= 'p' && $fnl <= 't' ? "color4" : ($fnl >= 'u' && $fnl <= 'x' ? "color5" : "color6"))))}}
                                             " title="{{auth()->user()->name}}">{{ strtoupper(substr(implode('', array_map(function ($word) {return strtoupper($word[0]);}, explode(' ', auth()->user()->name))), 0, 2)) }}</span>
										</div>
										<div class="comment-box">
											<form>
												<div class="form-group">
													<div class="container">
														<input type="text" id="comment-input" class="form-control" placeholder="write a comment ..." readonly/>
														<div class="ck-div" style="display: none;">
															<textarea id="editor2" class="ckEditor" name="comment"></textarea>
															<button type="button" class="ck-button ck-save-button2" id="ck-save-button2" disabled>Save
															</button>
															<button type="button" class="btn btn-secondary ck-close-button" id="ck-close-button2" data-id="2">Discard
															</button>
														</div>
													</div>
												</div>
											</form>
										</div>
										<div class="spinner loading js-loading-card-actions" style="display: none;">
											<div class="spinner-inner">
												<img src="https://i.gifer.com/ZZ5H.gif" alt="Loading...">
											</div>
										</div>
									</div>
									<div class="modal-comments" id="modal-activities"></div>
									{{--                                    <div class="modal-comments" id="modal-activities-"></div>--}}
									{{--									<div class="modal-commenting"></div>--}}
								</div>
							</div>
							<div class="col-md-3 px-1">
								<div class="pop-list">
									<div class="join-own">
										<h4>Suggested</h4>
										@php
											$user_id = (((( auth()->user()->id + 783 ) * 7 ) * 7 ) * 3).auth()->user()->created_at->timestamp.random_int(111,999);
										@endphp
										<ul>
											<li>
												<a class="assign-own-members" href="javascript:void(0);" id="assign-own-members-{{$user_id}}" data-id="{{$user_id}}"><i class="far fa-user"></i>Join</a>
											</li>
										</ul>
									</div>
									<h4>Add to Card</h4>
									<ul>
										<li class="dropdown">
											<a href="javascript:void(0);" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="far fa-user"></i>Members</a>
											@include('board.assign-new-board-members')
										</li>
										{{--this label modal working--}}
										<li class="dropdown label-modal">
											<a href="javascript:void(0);" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-tag"></i>Labels</a>
											@include('board.assign-new-board-labels')
											<div class="label-dynamic-div"></div>
											<div class="popoverContainer popover popover-new" style="display: none;">
												<div class="pop-header">
													<button class="close-btn close-mov-member return-move-member" onclick="returnlabelDiv()">
														<span class="material-symbols-outlined"> arrow_back_ios </span>
													</button>
													<h4 class="mem-mov-heading">Manage Label</h4>
													<button class="close-btn close-mov-member" onclick="closelabelDiv()">
														<span class="material-symbols-outlined">close</span></button>
												</div>
												@php
													$colors = \App\Models\Color::all();
												@endphp
												<div class="popoverContent">
													<div class="label-color">
														<div class="store-label-text"></div>
													</div>
													<div class="row">
														<div class="col-12">
															<label for="">Title</label>
															<input type="text" class="form-control create-label-text">
														</div>
														<div class="col-12">
															<div class="color-multi-div">
																<label for="">Select a color</label>
																<div class="colors-label">
																	@foreach($colors as $color)
																		@if($color->id != 21)
																			<div class="span-color color-select" data-color-position="{{ $color->color_position }}" data-color-id="{{ $color->id }}" style="background-color: {{ $color->color_value }};"></div>
																		@endif
																	@endforeach
																</div>
																<button type="button" class="remov-color-btn remove-color">Remove Color</button>
															</div>
														</div>
														<div class="col-12">
															<button type="button" class="create-btn-multi create-label">Save</button>
														</div>
													</div>
												</div>
											</div>
											{{--                                            <div class="popoverContainer popover popover-edit" style="display: none;">--}}
											{{--                                                <div class="pop-header">--}}
											{{--                                                    <button class="close-btn close-mov-member return-move-member"--}}
											{{--                                                            onclick="editreturnlabelDiv()">--}}
											{{--                                                        <span class="material-symbols-outlined"> arrow_back_ios </span>--}}
											{{--                                                    </button>--}}
											{{--                                                    <h4 class="mem-mov-heading">Edit Label</h4>--}}
											{{--                                                    <button class="close-btn close-mov-member"--}}
											{{--                                                            onclick="editcloselabelDiv()">--}}
											{{--                                                        <span class="material-symbols-outlined">close</span></button>--}}
											{{--                                                </div>--}}

											{{--                                                @php--}}
											{{--                                                    $colors = \App\Models\Color::all();--}}
											{{--                                                @endphp--}}
											{{--                                                <div class="popoverContent">--}}
											{{--                                                    <div class="label-color">--}}
											{{--                                                        <div class="store-label-text"></div>--}}
											{{--                                                    </div>--}}
											{{--                                                    <div class="row">--}}
											{{--                                                        <div class="col-12">--}}
											{{--                                                            <label for="">Title</label>--}}
											{{--                                                            <input type="text" class="form-control create-label-text">--}}
											{{--                                                        </div>--}}
											{{--                                                        <div class="col-12">--}}
											{{--                                                            <div class="color-multi-div">--}}
											{{--                                                                <label for="">Select a color</label>--}}
											{{--                                                                <div class="colors-label">--}}
											{{--                                                                    @foreach($colors as $color)--}}
											{{--                                                                        @if($color->id != 21)--}}
											{{--                                                                        <div class="span-color color-select" data-color-position="{{ $color->color_position }}"--}}
											{{--                                                                             data-color-id="{{ $color->id }}"--}}
											{{--                                                                             style="background-color: {{ $color->color_value }};"></div>--}}
											{{--                                                                        @endif--}}
											{{--                                                                    @endforeach--}}
											{{--                                                                </div>--}}
											{{--                                                                <button type="button" class="remov-color-btn remove-color">Remove Color</button>--}}
											{{--                                                            </div>--}}
											{{--                                                        </div>--}}
											{{--                                                        <div class="col-12">--}}
											{{--                                                            <button type="button" class="create-btn-multi create-label">Save</button>--}}
											{{--                                                        </div>--}}
											{{--                                                    </div>--}}
											{{--                                                </div>--}}
											{{--                                            </div>--}}
										</li>
										{{--this label modal working--}}
										<li class="dropdown">
											<a href="javascript:void(0);" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="far fa-clock"></i>Dates</a>
											<div class="dropdown-menu add-member-drop" aria-labelledby="dropdownMenuButton">
												@include('board.assign-new-board-dates')
											</div>
										</li> <li class="dropdown">
											<a href="javascript:void(0);" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-paperclip"></i>Attachment</a>
											<!-- <div class="dropdown-menu add-member-drop"
												aria-labelledby="dropdownMenuButton">
												<div class="add-member">
													<button class="close-btn-pop close-member"><span
															class="material-symbols-outlined">
															close
														</span>
													</button>
													<h4 class="mem-heading">Attach</h4>
													<form>
														<h3 class="bm-heading">Attach a file from your computer</h3>
														<p>You can also drag and drop files to upload them. </p>
														<div class="upload-btn-wrapper">
															<button class="btn">Upload a file
															</button>
															<input type="file" name="file-name" />
														</div>
														<div class="form-group">
															<label class="bm-heading">Search or paste a link</label>
															<input type="text" class="form-control" id="searchMember"
																aria-describedby="searchHelp"
																placeholder="Find recent links or paste a new link">
														</div>
														<div class="form-group">
															<label class="bm-heading">Display text (optional)</label>
															<input type="text" class="form-control" id="searchMember"
																aria-describedby="searchHelp"
																placeholder="Text to display">
														</div>
													</form>
													<h3 class="bm-heading">Recently Viewed </h3>
													<ul class="viewMain-list">
														<li> <a href="javascript:void(0);">
																<div class="view-list">
																	<div class="v-icon">
																		<i class="fas fa-desktop"></i>
																	</div>
																	<div class="v-detail">
																		<div class="task-name">
																			The Farmstead
																		</div>
																		<span class="board-title">Website
																			Projects</span> <span
																			class="board-time">Viewed
																			5
																			hours
																			ago</span>
																	</div>
																</div>
															</a> </li>
														<li> <a href="javascript:void(0);">
																<div class="view-list">
																	<div class="v-icon">
																		<i class="fas fa-desktop"></i>
																	</div>
																	<div class="v-detail">
																		<div class="task-name">
																			The Farmstead
																		</div>
																		<span class="board-title">Website
																			Projects</span> <span
																			class="board-time">Viewed
																			5
																			hours
																			ago</span>
																	</div>
																</div>
															</a> </li>
													</ul>
													<div class="last-button">
														<button class="btn">Cancel</button>
														<button class="btn">Insert</button>
													</div>
												</div>
											</div> -->
											@include('board.attachment-dropdown')
										</li> <li class="dropdown">
											<a href="javascript:void(0);" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="far fa-folder"></i>Cover
											</a>
											<div class="dropdown-menu add-member-drop" aria-labelledby="dropdownMenuButton">
												<div class="add-member">
													<button class="close-btn-pop close-member"><span class="material-symbols-outlined">
                                                            close
                                                        </span>
													</button>
													<div class="dropdown-fields">
														<h4 class="mem-heading">Cover</h4>
														<h3 class="bm-heading">Size</h3>
														<figure style="display: flex;justify-content: space-between;gap: 10px;">
															<img src="" alt="img" class="modal-cover-image image-size image-size-without-bg" style="display:none;width:50%;height:70px" id="modal-cover-image-size-0" data-id="0">
															<img src="" alt="img" class="modal-cover-image image-size image-size-with-bg" style="display:none;width:60%;object-fit:contain;height:70px" id="modal-cover-image-size-1" data-id="1">
														</figure>
														<h3 class="bm-heading">Colors</h3>
														<ul class="color-list">
															<li><span class="list-diamension cover-image-bg-color m-green" id="m-green" data-id="#4bce97"></span>
															</li>
															<li><span class="list-diamension cover-image-bg-color m-yellow" id="m-yellow" data-id="#f5cd47"></span>
															</li>
															<li><span class="list-diamension cover-image-bg-color m-orange" id="m-orange" data-id="#fea362"></span>
															</li>
															<li><span class="list-diamension cover-image-bg-color m-red" id="m-red" data-id="#f87168"></span>
															</li>
															<li><span class="list-diamension cover-image-bg-color m-purple" id="m-purple" data-id="#9f8fef"></span>
															</li>
														</ul>
														<style>
                                                            .attachments-for-cover {
                                                                display: grid;
                                                                grid-template-columns: 1fr 1fr 1fr;
                                                                gap: 8px;
                                                                padding: 4px;
                                                                margin: 10px 0px;
                                                                overflow-x: hidden;
                                                            }

                                                            .att-cover-images {
                                                                cursor: pointer;
                                                            }
														</style>
														<h3 class="bm-heading">Attachments </h3>
														<div class="attachments-for-cover"></div>
														<div class="upload-btn-wrapper border-none">
															<input type="file" id="upload-cover-image" accept="image/*" class="upload-cover-image">
															<label class="file-input__label" for="upload-cover-image"><span class="material-symbols-outlined">
                                                                    credit_card
                                                                </span> <span>Upload a Cover Image</span></label>
														</div>
														<p>Tip: Drag an image on to the card to upload it. </p>
														<h3 class="bm-heading">Photos from Unsplash</h3>
														<div class="background-cover">
															<input type="file" id="unsplash-image-input" accept="image/*">
															<label class="file-input__label" for="unsplash-image-input">
                                                                <span class="material-symbols-outlined">
                                                                    credit_card
                                                                </span> <span>Cover</span></label>
														</div>
													</div>
												</div>
											</div>
										</li>
									</ul>
								</div>
								<div class="pop-list">
									<h4>Actions</h4>
									<ul>
										<li class="dropdown">
											<a href="javascript:void(0);" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-arrow-right"></i>Move</a>
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
																		<option>Review
																		</option>
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
										{{--                                    </ul> --}}
										{{--                                </div> --}}
										{{--                <form id="create_board_card_form" method="POST"> --}}
										{{--                    <input type="hidden" name="board_list_id" value=""> --}}
										{{--                    <div class="modal-body"> --}}

										{{--                        <div class="body"> --}}
										{{--                            <div class="row"> --}}
										{{--                                <div class="col-md-12"> --}}
										{{--                                    <div class="form-group form-float"> --}}
										{{--                                        <label for="title">Title</label> --}}
										{{--                                        <input type="text" class="form-control" id="title" name="title" required> --}}
										{{--                                    </div> --}}
										{{--                                </div> --}}
										{{--                            </div> --}}
										{{--                        </div> --}}
										{{--                    </div> --}}
										{{--                </div> --}}
									</ul>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div></div>{{-- create modal --}}{{--    <div class="modal fade" id="create-board-card-modal" tabindex="-1" role="dialog">--}}{{--        <div class="modal-dialog modal-dialog-centered" role="document">--}}{{--            <div class="modal-content">--}}{{--                <div class="modal-header">--}}{{--                    <h4 class="title" id="defaultModalLabel">Add Card</h4>--}}{{--                </div>--}}{{--                <form id="create_board_card_form" method="POST">--}}{{--                    @csrf--}}{{--                    <input type="hidden" name="board_list_id" value="">--}}{{--                    <div class="modal-body">--}}{{--                        <div class="body">--}}{{--                            --}}{{--                        <div class="row"> --}}{{--                            --}}{{--                            <div class="col-md-12"> --}}{{--                            --}}{{--                                <div class="form-group form-float"> --}}{{--                            --}}{{--                                    <label for="Project">Project</label> --}}{{--                            --}}{{--                                    <select name="project_id" required class="form-control p-0" id="Project"> --}}{{--                            --}}{{--                                        <option value="">Select Project</option> --}}{{--                            --}}{{--                                        @foreach ($projects as $project_key => $project_value) --}}{{--                            --}}{{--                                        <option value="{{$project_value->id}}">{{$project_value->project_title}}--}}{{--                            --}}{{--                            --}}{{--                                        </option> --}}{{--                            --}}{{--                                        @endforeach --}}{{--                            --}}{{--                                    </select> --}}{{--                            --}}{{--                                </div> --}}{{--                            --}}{{--                            </div> --}}{{--                            --}}{{--                        </div> --}}{{--                            <div class="row">--}}{{--                                <div class="col-md-12">--}}{{--                                    <div class="form-group form-float">--}}{{--                                        <label for="title">Title</label>--}}{{--                                        <input type="text" class="form-control" id="title" name="title" required>--}}{{--                                    </div>--}}{{--                                </div>--}}{{--                            </div>--}}{{--                            --}}{{--                        <div class="row"> --}}{{--                            --}}{{--                            <div class="col-md-12"> --}}{{--                            --}}{{--                                <div class="form-group form-float"> --}}{{--                            --}}{{--                                    <label for="description">Description</label> --}}{{--                            --}}{{--                                    <textarea class="form-control" id="description" name="description" --}}{{--                            --}}{{--                                        required></textarea> --}}{{--                            --}}{{--                                </div> --}}{{--                            --}}{{--                            </div> --}}{{--                            --}}{{--                        </div> --}}{{--                            --}}{{--                        <div class="row"> --}}{{--                            --}}{{--                                <div class="col-md-6"> --}}{{--                            --}}{{--                                    <div class="form-group form-float"> --}}{{--                            --}}{{--                                        <label for="assign_to">Assign To</label> --}}{{--                            --}}{{--                                        <input type="text" class="form-control" name="assign_to" required> --}}{{--                            --}}{{--                                        <select class="form-control"> --}}{{--                            --}}{{--                                            <option value=""></option> --}}{{--                            --}}{{--                                        </select> --}}{{--                            --}}{{--                                    </div> --}}{{--                            --}}{{--                                </div> --}}{{--                            --}}{{--                            <div class="col-md-12"> --}}{{--                            --}}{{--                                <div class="form-group form-float"> --}}{{--                            --}}{{--                                    <label for="due_date">Due Date</label> --}}{{--                            --}}{{--                                    <input type="date" class="form-control" id="due_date" name="due_date" required> --}}{{--                            --}}{{--                                </div> --}}{{--                            --}}{{--                            </div> --}}{{--                            --}}{{--                        </div> --}}{{--                        </div>--}}{{--                    </div>--}}{{--                    <div class="modal-footer">--}}{{--                        <button class="btn btn-success btn-round" type="submit">Create--}}{{--                            Card--}}{{--                        </button>--}}{{--                        <button type="button" class="btn btn-danger btn-round" data-dismiss="modal">CLOSE</button>--}}{{--                    </div>--}}{{--                </form>--}}{{--            </div>--}}{{--        </div>--}}{{--    </div>--}}{{-- edit modal --}}{{--    <div class="modal fade" id="edit-board-card-modal" tabindex="-1" role="dialog">--}}{{--        <div class="modal-dialog modal-dialog-centered" role="document">--}}{{--            <div class="modal-content" id="edit-modal-content" style="background-repeat: no-repeat; background-size: contain;">--}}{{--                <div class="modal-header">--}}{{--                    <h4 class="title" id="defaultModalLabel">Edit Card</h4>--}}{{--                </div>--}}{{--                <form id="edit_board_card_form" method="POST" enctype="multipart/form-data">--}}{{--                    @csrf--}}{{--                    <input type="hidden" id="edit_board_list_card_id" name="edit_board_list_card_id" value="">--}}{{--                    <div class="modal-body">--}}{{--                        <div class="body">--}}{{--                            <div class="row">--}}{{--                                <div class="col-md-12">--}}{{--                                    <div class="form-group form-float">--}}{{--                                        <label for="image">Cover Image</label>--}}{{--                                        <input type="file" class="form-control" id="cover_image" name="cover_image">--}}{{--                                    </div>--}}{{--                                </div>--}}{{--                            </div>--}}{{--                            <div class="row">--}}{{--                                <div class="col-md-12">--}}{{--                                    <div class="form-group form-float">--}}{{--                                        <label for="edit_project_id">Project</label>--}}{{--                                        <select name="edit_project_id" required class="form-control p-0" id="edit_project_id">--}}{{--                                            <option value="">Select Project</option>--}}{{--                                            @foreach ($projects as $project_key => $project_value)--}}{{--                                                <option value="{{ $project_value->id }}">--}}{{--                                                    {{ $project_value->project_title }}--}}{{--                                                </option>--}}{{--                                            @endforeach--}}{{--                                        </select>--}}{{--                                    </div>--}}{{--                                </div>--}}{{--                            </div>--}}{{--                            <div class="row">--}}{{--                                <div class="col-md-12">--}}{{--                                    <div class="form-group form-float">--}}{{--                                        <label for="edit_title">Title</label>--}}{{--                                        <input type="text" class="form-control" id="edit_title" name="edit_title" required>--}}{{--                                    </div>--}}{{--                                </div>--}}{{--                            </div>--}}{{--                            <div class="row">--}}{{--                                <div class="col-md-12">--}}{{--                                    <div class="form-group form-float">--}}{{--                                        <label for="edit_description">Description</label>--}}{{--                                        <textarea class="form-control" id="edit_description" name="edit_description" required></textarea>--}}{{--                                    </div>--}}{{--                                </div>--}}{{--                            </div>--}}{{--                            <div class="row">--}}{{--                                <div class="col-md-12">--}}{{--                                    <div class="form-group form-float">--}}{{--                                        <label for="edit_assign_to">Assign To</label>--}}{{--                                        <select name="edit_assign_to[]" required class="form-control p-0" id="edit_assign_to" title="Select user to assign card" multiple="multiple">--}}{{--                                            @foreach ($users as $user)--}}{{--                                                <option value="{{ $user->name }}">--}}{{--                                                    {{ $user->name }}
</option>
--}}{{--                                            @endforeach--}}{{--                                        </select>--}}{{--                                    </div>--}}{{--                                </div>--}}{{--                                <div class="col-md-12">--}}{{--                                    <div class="form-group form-float">--}}{{--                                        <label for="edit_due_date">Due Date</label>--}}{{--                                        <input type="date" class="form-control" id="edit_due_date" name="edit_due_date" required>--}}{{--                                    </div>--}}{{--                                </div>--}}{{--                            </div>--}}{{--                        </div>--}}{{--                    </div>--}}{{--                    <div class="modal-footer">--}}{{--                        <button class="btn btn-success btn-round" type="submit">Update Card--}}{{--                        </button>--}}{{--                        <button type="button" class="btn btn-danger btn-round" data-dismiss="modal">CLOSE</button>--}}{{--                    </div>--}}{{--                </form>--}}{{--            </div>--}}{{--        </div>--}}{{--    </div>--}}

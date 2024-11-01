@extends('layouts.app')@section('cxmTitle', 'Email')

@section('content')
    @push('css')
        <style>
            .ck.ck-balloon-panel.ck-powered-by-balloon .ck.ck-powered-by, .ck-balloon-panel_visible, .ck-powered-by__label, .ck-icon ck-reset_all-excluded {
                display: none !important;
            }

            blockquote:before {
                content: none !important;
            }

            span.mailbox-read-time.float-right {
                text-transform: none;
            }
        </style>
    @endpush
    <section class="content mail-content-tg emailBodysec">
        <div class="body_scroll">
            <div class="block-header">
                <div class="row">
                    <div class="col-lg-7 col-md-6 col-sm-12">
                        <h2>Emails</h2>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ route('dashboard') }}"><i class="zmdi zmdi-home"></i> TG</a> </li>
                            <li class="breadcrumb-item">Emails</li> <li class="breadcrumb-item active">Read Mail</li>
                        </ul>
                    </div>
                    <div class="col-lg-5 col-md-6 col-sm-12 text-right">
                        <a href="{{ ucfirst($route_name) == "Inbox" ? route('user.email.system.index', ['email' => $label_email]) : (in_array(ucfirst($route_name), ["Spam", "Trash", "Sent"]) ? route("user.email.system.".strtolower($route_name), ['email' => $label_email]) : '' )}}" class="btn btn-success btn-icon rounded-circle" type="button"><i class="zmdi zmdi-arrow-left"></i></a>
                        @include('includes.cxm-top-right-toggle-btn')
                    </div>
                </div>
            </div>
        </div>
        <div class="section-body">
            <div class="modal-edit-task trigger--fire-modal-4"></div>
            <div class="modal-add-task-details trigger--fire-modal-3"></div>
            <div class="content-wrapper">
                <div class="content">
                    <div class="card card-navy card-outline">
                        <div class="bodyHeading">
                            <h2 class="emailbodyHeading">{{$all_messages['Subject'] ??  "No Subject" }}</h2>
                        </div>
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        @if(isset($all_messages['messages']) && is_array($all_messages['messages']) && count($all_messages['messages']) > 0)
                            @foreach($all_messages['messages'] as $message_key => $message)
                                <div class="wrapReplyBody accordion" id="accordionExample">
                                    <div class="replyBodyarea">
                                        <div class="hideRmsg" data-toggle="collapse" data-target="{{(count($all_messages['messages']) - 1) != $message_key ? "#collapse-".$message_key: ""}}" aria-expanded="true" aria-controls="collapse-{{$message_key}}" data-message-key="{{$message_key}}" data-message-id="{{$message['id']}}" data-thread-id="{{$message['threadId']}}"></div>
                                        <div class="wrapReadHead d-flex">
                                            <div class="userAvatar">
                                                <span class="userAv {{$message['color_class']}}">{{strtoupper($message['name_alphabet'])}}</span>
                                            </div>
                                            <div class="rbtin">
                                                <div class="rbtemleft">
                                                    <h5 class="font-weight m-0">{{$message['message_from']??""}}</h5>
                                                    <div class="msgdropowndetails">
                                                        <span>to {{$message['message_to']? strstr($message['message_to'], '@', true) : ""}}</span>
                                                        <div class="dropdown">
                                                            <a href="javascript:;" class="tomedetails dropdown-toggle" id="tomedetailsDropdown" data-toggle="dropdown">
                                                                <span class="material-symbols-outlined">
                                                                    arrow_drop_down
                                                                    </span> </a>
                                                            <div class="dropdown-menu" aria-labelledby="tomedetailsDropdown">
                                                                <ul>
                                                                    <li>
                                                                        <div class="lefttome">
                                                                            <span class="lightfont">from:</span>
                                                                        </div>
                                                                        <div class="righttome">
                                                                            <strong>{{isset($message['From']) && count($message['From']) > 0 ? $message['From'][0]:""}}</strong>
                                                                        </div>
                                                                    </li> <li>
                                                                        <div class="lefttome">
                                                                            <span class="lightfont">to:</span>
                                                                        </div>
                                                                        <div class="righttome">
                                                                            <span class="normalfnt">
                                                                                {{$message['message_to']}}
                                                                            </span>
                                                                        </div>
                                                                    </li> <li>
                                                                        <div class="lefttome">
                                                                            <span class="lightfont">date:</span>
                                                                        </div>
                                                                        <div class="righttome">
                                                                            <span class="normalfnt">{{ isset($message['message_date']) ? date("M d, Y, g:iA", strtotime($message['message_date'])) ." (". Carbon\Carbon::parse($message['message_date'])->diffForHumans() .")" : '' }}</span>
                                                                        </div>
                                                                    </li> <li>
                                                                        <div class="lefttome">
                                                                            <span class="lightfont">subject:</span>
                                                                        </div>
                                                                        <div class="righttome">
                                                                            <span class="normalfnt">{{$message['message_subject']}}</span>
                                                                        </div>
                                                                    </li> <li>
                                                                        <div class="lefttome">
                                                                            <span class="lightfont">mailed-by:</span>
                                                                        </div>
                                                                        <div class="righttome">
                                                                            <span class="normalfnt">{{substr(strrchr($label_email, "@"), 1)}}</span>
                                                                        </div>
                                                                    </li>
                                                                </ul>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    {{--                                                    <span class="text-sm text-gray"><?php echo isset($cxmEmailHeaders['FromEmail']) ? $cxmEmailHeaders['FromEmail'] : 'From not found'; ?></span>--}}
                                                </div>
                                                <div class="rbtemright">
                                                    <h6 class="m-0">
                                                        <span class="mailbox-read-time float-right">{{ isset($message['message_date']) ? date("M d, Y, g:iA", strtotime($message['message_date'])) ." (". Carbon\Carbon::parse($message['message_date'])->diffForHumans() .")" : '' }}</span>
                                                    </h6>
                                                    <div class="dropdown">
                                                        <a href="javascript:;" class="replydot dropdown-toggle" id="dropdownMenuButton" data-toggle="dropdown">
                                                            <span class="material-symbols-outlined">more_vert</span>
                                                        </a>
                                                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                                                            <a class="dropdown-item reply-button" href="javascript:void(0);" data-message-key="{{$message_key}}" data-message-id="{{$message['id']}}" data-thread-id="{{$message['threadId']}}" onclick="$('#reply-message-{{$message_key}}').show(); " style="text-decoration: none !important;">
                                                                <span class="material-symbols-outlined">reply</span>
                                                                <span class="fs">Reply</span> </a>
                                                            {{--<a class="dropdown-item" href="javascript:;">--}}
                                                            {{--    <span class="material-symbols-outlined">--}}
                                                            {{--        forward--}}
                                                            {{--            </span> <span class="fs">Forward</span></a>--}}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="replybodyTop collapse {{(count($all_messages['messages']) - 1) == $message_key ? "show":""}}" id="collapse-{{$message_key}}" aria-labelledby="heading-{{$message_key}}" data-parent="#accordionExample" data-message-key="{{$message_key}}" data-message-id="{{$message['id']}}" data-thread-id="{{$message['threadId']}}">
                                            <div class="rtbbd">
                                                <div class="card-body p-0">
                                                    <div class="mailbox-read-message">
                                                        {{--                                                        {!! $message['message_body']  !!}--}}
                                                        {!! preg_replace('/<br>\s*<br>/', '<br>', preg_replace('/<!--(.*?)-->/s', '', $message['message_body']) ) !!}
                                                        {{--                                                        <div class="replybddetail">--}}
                                                        {{--                                                            <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Rem quasi, reiciendis earum asperiores enim numquam delectus molestias doloremque reprehenderit praesentium aut nobis impedit veritatis dolorum sed non omnis minus commodi.</p>--}}
                                                        {{--                                                        </div>--}}

                                                        @if(isset($message['message_attachments']) && !empty($message['message_attachments']))

                                                            <ul class="receiveattfile">
                                                                @foreach ($message['message_attachments'] as $attachment_key => $attachment)
                                                                    <li class="filearea">
                                                                        <div class="mainfileimg">
                                                                            @if(str_contains($attachment['mimeType'], 'image'))
                                                                                <img src="data:image/png;base64,{{ $attachment['file'] }}" alt="{{ $attachment['filename'] }}" class="thumbnailimg">
                                                                            @else
                                                                                <img src="{{file_exists(public_path("assets/images/file-type/") . $attachment['file_extension']. ".png") ? asset('assets/images/file-type/'.$attachment['file_extension'].'.png') : asset('assets/images/file-type/doc.png') }}" alt="{{ $attachment['filename'] }}" class="thumbnailimg">
                                                                            @endif
                                                                        </div>
                                                                        <div class="fhoverover">
                                                                            <div class="fhoverhead">
                                                                                <div class="fhimg">
                                                                                    <img src="{{ file_exists(public_path("assets/images/icons/icon_") . $attachment['file_extension']. "_x16.png") ? asset('assets/images/icons/icon_' . $attachment['file_extension'] . '_x16.png') : asset('assets/images/icons/icon_image_x16.png')}}" alt="Icon" class="thumbsmall-icon">
                                                                                </div>
                                                                                <div class="imageName">
                                                                                    <p class="imgn">{{$attachment['filename']}}</p>
                                                                                    <span class="imgsize">{{ number_format($attachment['size']) }} KB</span>
                                                                                    <div class="fhoverft">
                                                                                        <a download="{{ $attachment['filename'] }}" href="data:image/png;base64, {{ $attachment['file'] }}"><span class="material-symbols-outlined">
                                                                                            download
                                                                                            </span></a>
                                                                                        <!--<span class="material-symbols-outlined">
                                                                                            download
                                                                                            </span>-->
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="aSI">
                                                                            <div class="aSJ"></div>
                                                                        </div>
                                                                    </li>

                                                                @endforeach
                                                            </ul>

                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="wrapBox reply-message" id="reply-message-{{$message_key}}">
                                            <div class="userAvatar">
                                                <span class="userAv {{$all_messages['color_class']??""}}">{{strtoupper($all_messages['name_alphabet']??"")}}</span>
                                            </div>
                                            <div class="wrapReplyBox">
                                                <div class="toemail replytoemail">
                                                    <div class="mainemail d-flex align-items-center" id="mainemail-{{$message_key}}" data-message-key="{{$message_key}}">
                                                        <span class="mr-2">To</span>
                                                        <div class="emailtagsss">
                                                            <input type="text" placeholder="" id="to-{{$message_key}}" name="to[]" class="emailInput gmailInput form-control toEmailInput" data-message-key="{{$message_key}}">
                                                        </div>
                                                        <div class="gmailccbcc">
                                                            <div class="d-flex align-items-center">
                                                                <span class="showemailmagic01">Cc</span>
                                                                <span class="showemailmagic02 ml-2">Bcc</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="ccemail" id="ccemail-{{$message_key}}" data-message-key="{{$message_key}}">
                                                        <div class="d-flex align-items-center ccmail">
                                                            <span class="mr-2">Cc</span>
                                                            <input type="text" id="cc-{{$message_key}}" name="cc[]" class="emailInput gmailInput form-control ccEmailInput" data-message-key="{{$message_key}}">
                                                        </div>
                                                    </div>
                                                    <div class="bccemail" id="bccemail-{{$message_key}}" data-message-key="{{$message_key}}">
                                                        <div class="d-flex align-items-center bccmail">
                                                            <span class="mr-2">Bcc</span>
                                                            <input type="text" id="bcc-{{$message_key}}" name="bcc[]" class="emailInput gmailInput form-control bccEmailInput" data-message-key="{{$message_key}}">
                                                        </div>
                                                    </div>
                                                    <ul class="emailhint" id="contact-email-hint-{{$message_key}}" data-message-key="{{$message_key}}" style="left:55px">
                                                        <li>
                                                            <div class="wrapemailhint">
                                                                <div class="userAvatar">
                                                                    <span class="userAv color1">DM</span>
                                                                    <div class="emailname">
                                                                        <span>developer.michael.09@gmail.com</span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </li>
                                                    </ul>
                                                </div>
                                                <div class="replyBody" id="replyBody-{{$message_key}}-{{$message['id']}}-{{$message['threadId']}}">
                                                    <textarea id="editor-{{$message_key}}-{{$message['id']}}-{{$message['threadId']}}" class="ckEditor" name="reply_message" placeholder="Write a comment..."></textarea>
                                                </div>
                                                {{-- <div class="dotShowreply">
                                                    <a href="javascript:;" class="showembddetail">
                                                        <span class="material-symbols-outlined">
                                                            more_horiz
                                                            </span> </a>
                                                </div> --}}
                                                <div class="attachmentBody showAllreply">
                                                    <form action="{{route('user.email.system.add.attachment')}}" method="post" class="dropzone" id="my-awesome-dropzone-{{$message_key}}" enctype="multipart/form-data">@csrf</form>
                                                    <input type="file" id="attachment-{{$message_key}}" name="attachments[]" data-message-key="{{$message_key}}" class="file-attachment"/>
                                                </div>
                                                <div class="replyFt">
                                                    <div class="btnLeft">
                                                        <button class="btn btn-primary submit-reply-message" id="submit-reply-message-{{$message_key}}" type="submit" data-message-key="{{$message_key}}" data-message-id="{{$message['id']}}" data-thread-id="{{$message['threadId']}}">Send</button>
                                                        <a href="javascript:;" class="attachIcon">
                                                            <span class="material-symbols-outlined">
                                                                attach_file
                                                                </span> </a>
                                                    </div>
                                                    <div id="remove-reply-{{$message_key}}" class="remove-reply" data-message-key="{{$message_key}}" data-message-id="{{$message['id']}}" data-thread-id="{{$message['threadId']}}" onclick="$('#reply-message-{{$message_key}}').hide()">
                                                        <span class="material-symbols-outlined">
                                                            delete
                                                            </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div> {{-- End wrapBox reply-message --}}
                                    </div> {{-- End replyBodyarea --}}
                                </div>
                            @endforeach
                        @else
                            <p>No messages found.</p>
                        @endif
                        <div class="card-footer replybodyft">
                            <div class="d-flex">
                                <button type="button" name="reply" value="reply" class="btn btn-navy cxm-btn-1 reply-button" id="reply-button" @if(isset($all_messages['messages']) && is_array($all_messages['messages']) && count($all_messages['messages']) > 0)
                                    data-message-key="{{count($all_messages['messages']) - 1}}" data-message-id="{{$all_messages['messages'][count($all_messages['messages']) - 1]['id']}}" data-thread-id="{{$all_messages['messages'][count($all_messages['messages']) - 1]['threadId']}}" onclick="$('#reply-message-{{count($all_messages['messages']) - 1}}').show();"
                                    @endif
                                >
                                <span class="material-symbols-outlined">
                                    reply
                                    </span> Reply
                                </button>
                                {{--                                <button type="button" name="forward" value="forward" class="btn btn-navy cxm-btn-1">--}}
                                {{--                                    <span class="material-symbols-outlined">--}}
                                {{--                                        forward--}}
                                {{--                                        </span> Forward--}}
                                {{--                                </button>--}}
                            </div>
                            <button type="button" class="btn btn-default d-none">
                                <i class="far fa-trash-alt"></i> Delete
                            </button>
                            <button type="button" class="btn btn-default d-none" onclick="window.print();">
                                <i class="zmdi zmdi-print"></i> Print
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@push('cxmScripts')
    @include('email-system.ckeditor-script')
    @include('email-system.script')


    <script>
        if (typeof Dropzone !== 'undefined') {
            Dropzone.autoDiscover = false;
        }

        $(document).ready(function () {

            $('.reply-message').hide();

            const emailLists = {};

            function initializeEmailLists(messageKey) {
                emailLists[messageKey] = {
                    toEmailList: new Set(),
                    ccEmailList: new Set(),
                    bccEmailList: new Set()
                };
            }

            function isValidEmail(email) {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                return emailRegex.test(email);
            }

            function addEmailTag(container, email, emailList) {
                if (emailList.has(email) || !isValidEmail(email)) {
                    return;
                }

                const emailTag = document.createElement('span');
                emailTag.classList.add('emailTag');
                emailTag.textContent = email;

                const removeSign = document.createElement('span');
                removeSign.classList.add('removeSign');
                removeSign.textContent = '×'; // You can use any icon or text for removal

                emailTag.appendChild(removeSign);

                const inputField = container.querySelector('input[type="text"]');
                inputField.parentNode.insertBefore(emailTag, inputField);

                emailList.add(email);

                removeSign.addEventListener('click', function () {
                    emailList.delete(email);
                    emailTag.remove();
                });
            }

            function handleAddEmail(inputSelector, containerSelector, emailList, messageKey) {
                const inputField = document.querySelector(inputSelector);
                const container = document.querySelector(containerSelector);

                inputField.addEventListener('keydown', function (event) {
                    if (event.key === 'Enter' || event.key === ',') {
                        event.preventDefault();
                        const email = inputField.value.trim();
                        if (email) {
                            addEmailTag(container, email, emailList);
                            inputField.value = '';
                        }
                    }
                });
            }

            function handleInputFieldBlur(inputSelector, containerSelector, emailList, messageKey) {
                const inputField = document.querySelector(inputSelector);
                const container = document.querySelector(containerSelector);

                inputField.addEventListener('blur', function (event) {
                    const email = inputField.value.trim();
                    if (email) {
                        addEmailTag(container, email, emailList);
                        inputField.value = ''; // Clear the input field
                        $('#contact-email-hint').hide();
                    }
                });
            }

            // function handleInputFieldKeydown(inputSelector, containerSelector, emailList, messageKey) {
            //     const inputField = document.querySelector(inputSelector);
            //     const container = document.querySelector(containerSelector);
            //
            //     inputField.addEventListener('keydown', function (event) {
            //         if (event.key === 'Enter') {
            //             event.preventDefault();
            //             const email = inputField.value.trim();
            //             if (email) {
            //                 addEmailTag(container, email, emailList);
            //                 inputField.value = ''; // Clear the input field
            //             }
            //         }
            //     });
            // }

            const event = new KeyboardEvent('keydown', {
                key: 'Enter',
                keyCode: 13,
                bubbles: true,
                cancelable: true
            });
            @if(isset($all_messages['messages']) && is_array($all_messages['messages']) && count($all_messages['messages']) > 0)
            @foreach($all_messages['messages'] as $message_key => $message)
            var formattedContent = '<p class="paraReply"></p><div class="reply-block-ckditor"><blockquote>' + {!! json_encode( $message['message_body']); !!} + '</blockquote></div>';
            formattedContent = formattedContent.replace('/<br>\s*<br>/gi', '<br>');

            MyEditor["editor-{{$message_key}}-{{$message['id']}}-{{$message['threadId']}}"].setData(formattedContent);

            $('<div class="dotShowreply" id="dotShowreply-{{$message_key}}-{{$message['id']}}-{{$message['threadId']}}"><a href="javascript:;" class="showembddetail"><span class="material-symbols-outlined">more_horiz</span> </a></div> ').insertBefore($(`#replyBody-{{$message_key}}-{{$message['id']}}-{{$message['threadId']}} .ck-editor__main`));


            if (!emailLists[{{$message_key}}]) {
                initializeEmailLists({{$message_key}});
            }

            $(`#to-{{$message_key}}`).val('{{isset($message['From']) && count($message['From']) > 0 ? $message['From'][0]:""}}')
            handleAddEmail(`#to-{{$message_key}}`, `#mainemail-{{$message_key}}`, emailLists[{{$message_key}}].toEmailList, {{$message_key}});
            document.querySelector(`#to-{{$message_key}}`).dispatchEvent(event);

            @endforeach
            @endif


            var loading_div = $('.loading_div');

            var $gmailInp = $(".emailInput");
            // var $additionalElement01 = $(".fshow");
            var $additionalElement02 = $(".gmailccbcc");

            $gmailInp.focus(function () {
                console.log("Working...")
                // Show the additional element when the input is focused
                // $additionalElement01.show();
                $additionalElement02.show();
            });

            $gmailInp.blur(function () {
                // Hide the additional element when the input loses focus
                // $additionalElement01.hide();
                // $additionalElement02.hide();
            });

            $(".showemailmagic01").click(function () {
                $(".ccemail").show();
                $(this).hide()
            });

            $(".showemailmagic02").click(function () {
                $(".bccemail").show();
                $(this).hide()
            });

            $(".charea").click(function () {
                $(".composeBox").toggleClass("boxmin");
            });

            $(".compose-btn").click(function () {
                $(".composeBox").show();
            });

            $(".closebox").click(function () {
                $(this).parents(".composeBox").hide();
            });

            $('.showembddetail').click(function () {
                $(this).parent().parent().addClass('showembdReplyarea');
                $(this).parent().hide();
            });


            $(".emailInput").on("click", function () {
                var messageKey = $(this).data('message-key');
                if (messageKey == 0 || messageKey > 0) {

                    if (!emailLists[messageKey]) {
                        initializeEmailLists(messageKey);
                    }

                    handleAddEmail(`#to-${messageKey}`, `#mainemail-${messageKey}`, emailLists[messageKey].toEmailList, messageKey);
                    handleAddEmail(`#cc-${messageKey}`, `#ccemail-${messageKey}`, emailLists[messageKey].ccEmailList, messageKey);
                    handleAddEmail(`#bcc-${messageKey}`, `#bccemail-${messageKey}`, emailLists[messageKey].bccEmailList, messageKey);

                    handleInputFieldBlur(`#to-${messageKey}`, `#mainemail-${messageKey}`, emailLists[messageKey].toEmailList, messageKey);
                    handleInputFieldBlur(`#cc-${messageKey}`, `#ccemail-${messageKey}`, emailLists[messageKey].ccEmailList, messageKey);
                    handleInputFieldBlur(`#bcc-${messageKey}`, `#bccemail-${messageKey}`, emailLists[messageKey].bccEmailList, messageKey);

                    // handleInputFieldKeydown(`#to-${messageKey}`, `#mainemail-${messageKey}`, emailLists[messageKey].toEmailList, messageKey);
                    // handleInputFieldKeydown(`#cc-${messageKey}`, `#ccemail-${messageKey}`, emailLists[messageKey].ccEmailList, messageKey);
                    // handleInputFieldKeydown(`#bcc-${messageKey}`, `#bccemail-${messageKey}`, emailLists[messageKey].bccEmailList, messageKey);
                }
            });

            /** Email Suggestion */
            $(document).on("click", '.emailInput', function () {
                var inputField = $(this);
                if (inputField.val().trim() === '') {
                    inputField.closest('.toemail').find('.emailhint').hide();
                }
            });
            $(document).on('input', '.emailInput', function (e) {
                e.preventDefault();
                var input_length = $(this).val().length;
                if (input_length > 3) {
                    get_email_suggestions($(this));
                }
            });
            let emailRequest = null;

            function get_email_suggestions(input) {
                var abort = false;
                if (emailRequest !== null) {
                    abort = true;
                    console.log(emailRequest);
                }
                var url = '{{ route('user.email.system.get.email.suggestions') }}';
                var message_key = input.data('message-key');
                var isToEmail = input.hasClass('toEmailInput');
                var isCcEmail = input.hasClass('ccEmailInput');
                var isBccEmail = input.hasClass('bccEmailInput');
                emailRequest = AjaxRequestGetPromise(url, {
                    search: input.val(),
                    email: '{{$label_email}}'
                }, null, false, null, false, false, false, abort)
                    .then((res) => {
                        var contact_list = $('#contact-email-hint-' + message_key);
                        contact_list.empty();
                        if (res.status && res.status === 1 && res.emails.length > 0) {
                            res.emails.forEach(function (email) {
                                var nameAlphabet = getInitialsFromEmail(email);
                                /**First Letter*/
                                var fnl = $.trim(email).charAt(0).toLowerCase();
                                var colorClass = getColorAccordingToName(fnl);
                                var disabled = "";
                                var li_class = "";
                                if (isToEmail) {
                                    li_class = "liToEmail";
                                    if (emailLists[message_key].toEmailList.has(email)) {
                                        disabled = 'disabled';
                                    }
                                } else if (isCcEmail) {
                                    li_class = "liCcEmail";
                                    if (emailLists[message_key].ccEmailList.has(email)) {
                                        disabled = 'disabled';
                                    }
                                } else if (isBccEmail) {
                                    li_class = "liBccEmail";
                                    if (emailLists[message_key].bccEmailList.has(email)) {
                                        disabled = 'disabled';
                                    }
                                }
                                contact_list.append('<li class="' + disabled + ' ' + li_class + '"><div class="wrapemailhint"><div class="userAvatar"><span class="userAv ' + colorClass + '">' + nameAlphabet + '</span></div><div class="emailname"><span>' + email + '</span></div></div></li>');

                            });

                            var prefixes = ["mainemail", "ccemail", "bccemail"];
                            var mainDivId = null;
                            for (var i = 0; i < prefixes.length; i++) {
                                var prefix = prefixes[i];
                                var mainDiv = input.closest("[id^='" + prefix + "']");
                                if (mainDiv.length > 0) {
                                    mainDivId = mainDiv.attr('id');
                                    break;
                                }
                            }

                            updateEmailHintPosition($("#" + mainDivId), message_key);
                            contact_list.show();
                        }
                    })
                    .catch((error) => {
                        if (error.statusText === 'abort') {
                            console.log('Ajax request aborted.');
                        } else {
                            console.error("Error in Ajax request:", error);
                        }
                    });
            }

            // function updateEmailHintPosition(section, message_key) {
            //     var emailTags = section.find('.emailTag');
            //     var emailHintList = $('#contact-email-hint-' + message_key);
            //     var totalWidth = 0;
            //
            //     emailTags.each(function () {
            //         totalWidth += $(this).outerWidth(true);
            //     });
            //
            //     var maxWidth = section.find('.emailtagsss').outerWidth(true) - 55;
            //     if (totalWidth > maxWidth) {
            //         emailHintList.css('left', (maxWidth + 55) + 'px');
            //     } else {
            //         emailHintList.css('left', (totalWidth + 55) + 'px');
            //     }
            // }
            function updateEmailHintPosition(section, message_key) {
                var emailTags = section.find('.emailTag');
                var emailHintList = $('#contact-email-hint-' + message_key);
                var container = section.find('.emailtagsss');
                var totalWidth = 0;

                emailTags.each(function () {
                    totalWidth += $(this).outerWidth(true);
                });

                var maxWidth = container.outerWidth(true) - 150;

                if (totalWidth > maxWidth) {
                    var lines = [];
                    var currentLine = [];

                    // Group email tags by their top position
                    emailTags.each(function () {
                        var tagTop = $(this).position().top;
                        if (!currentLine.length || $(this).position().top === currentLine[0].top) {
                            currentLine.push($(this));
                        } else {
                            lines.push(currentLine);
                            currentLine = [$(this)];
                        }
                    });
                    lines.push(currentLine); // Push the last line

                    // Get the width of the last line
                    var lastLineWidth = lines[lines.length - 1].reduce(function (acc, cur) {
                        return acc + cur.outerWidth(true);
                    }, 0);

                    var leftPosition = lastLineWidth + 55;
                    emailHintList.css('left', leftPosition + 'px');
                } else {
                    var leftPosition = totalWidth + 55;
                    emailHintList.css('left', leftPosition + 'px');
                }
            }

            function getColorAccordingToName(fnl) {
                let colorClass = '';
                if (fnl >= 'a' && fnl <= 'e') {
                    colorClass = 'color1';
                } else if (fnl >= 'f' && fnl <= 'j') {
                    colorClass = 'color2';
                } else if (fnl >= 'k' && fnl <= 'o') {
                    colorClass = 'color3';
                } else if (fnl >= 'p' && fnl <= 't') {
                    colorClass = 'color4';
                } else if (fnl >= 'u' && fnl <= 'x') {
                    colorClass = 'color5';
                } else {
                    colorClass = 'color6';
                }
                return colorClass;
            }

            function getInitialsFromEmail(email) {
                let base_email = `{{$email}}`;
                let username = '';
                if (base_email === email) {
                    username = email.split('@')[1];
                } else {
                    username = email.split('@')[0];
                }                let domainParts = username.split('.');
                let domain = domainParts[domainParts.length - 1].toLowerCase();
                if (domain === 'com' || domain === 'net' || domain === 'org') {
                    username = domainParts[0];
                }
                return username.split('.')
                    .map(function (word) {
                        return word.charAt(0).toUpperCase();
                    })
                    .join('')
                    .substring(0, 2);
            }

            $(document).on('click', '.emailhint li', function () {
                if ($(this).hasClass('disabled')) {
                    return;
                }
                $(this).addClass('disabled');
                const email = $(this).find('.emailname span').text().trim();
                var message_key = $(this).parent().data('message-key');
                var selector_id = null;
                var list = null;
                if ($(this).hasClass('liToEmail')) {
                    selector_id = "mainemail";
                    list = emailLists[message_key].toEmailList;
                } else if ($(this).hasClass('liCcEmail')) {
                    selector_id = "ccemail";
                    list = emailLists[message_key].ccEmailList;
                } else if ($(this).hasClass('liBccEmail')) {
                    selector_id = "bccemail";
                    list = emailLists[message_key].bccEmailList;
                }
                addEmailTag(document.querySelector(`#${selector_id}-${message_key}`), email, list);
                $('#contact-email-hint-' + message_key).empty().hide();
            });

            /** Email Suggestion */

            $(document).on('click', '.submit-reply-message', function (event) {
                event.preventDefault();
                var message_key = $(this).data('message-key');
                var message_id = $(this).data('message-id');
                var thread_id = $(this).data('thread-id');
                var loading_div = $('.loading_div');

                loading_div.css('display', 'flex');

                /** Perform AJAX request to update the mark as read / unread */
                var url = '{{ route('user.email.system.reply.message') }}';
                var formData = new FormData();

                var dropzone = $('#my-awesome-dropzone-' + message_key)[0].dropzone;

                dropzone.files.forEach(function (file) {
                    formData.append('attachments[]', file);
                });

                var toEmailList = emailLists[message_key].toEmailList;
                var ccEmailList = emailLists[message_key].ccEmailList;
                var bccEmailList = emailLists[message_key].bccEmailList;

                toEmailList.forEach(email => {
                    formData.append('to[]', email);
                });
                ccEmailList.forEach(email => {
                    formData.append('cc[]', email);
                });
                bccEmailList.forEach(email => {
                    formData.append('bcc[]', email);
                });

                formData.append("message_key", message_key);
                formData.append("thread_id", thread_id);

                formData.append("email", '{{$label_email}}');
                formData.append("message_id", message_id);
                formData.append("reply_message", MyEditor["editor-" + message_key + "-" + message_id + "-" + thread_id].getData());

                loading_div.css('display', 'flex');

                AjaxRequestPostPromise(url, formData, null, false, null, false, true, true)
                    .then((res) => {
                        /** Handle success, if needed */
                        console.log(res);
                        if (res.status && res.status == 1) {
                            createToast('success', 'Reply message submitted successfully');
                        } else {
                            console.error('Failed to submit reply');
                            createToast('error', 'Failed to submit reply');
                        }
                        loading_div.css('display', 'none');
                        $("#reply-message").hide();

                        location.reload();
                    })
                    .catch((error) => {
                        createToast('error', 'Failed to submit reply');
                        console.error("Error in Ajax request:", error);
                        loading_div.css('display', 'none');
                        $("#reply-message").hide();
                    })
                    .finally(() => {
                        loading_div.css('display', 'none');
                        $("#reply-message").hide();
                    })
            });

            $(document).on("click", '.remove-reply', function () {
                var message_key = $(this).data('message-key');
                var message_id = $(this).data('message-id');
                var thread_id = $(this).data('thread-id');
                $("#reply-message-" + message_key).hide();
                // MyEditor[`editor-${message_key}-${message_id}-${thread_id}`].setData("");

            });
            //
            // $(document).on("click", '.reply-button', function () {
            //     var key = $(this).data('message-key');
            //     $("#reply-message-" + key).show();
            // })


            $(document).on('click', '.reply-button', function (event) {
                event.preventDefault();
                var loading_div = $('.loading_div');

                var message_id = $(this).data('message-id');
                var message_key = $(this).data('message-key');
                var thread_id = $(this).data('thread-id');
                var url = '{{route('user.email.system.reply.message.body')}}';

                AjaxRequestGetPromise(url, {
                    message_key: message_key,
                    message_id: message_id,
                    thread_id: thread_id,
                    label_email: '{{$label_email}}'
                }, null, false, null, false, false, false)
                    .then((res) => {
                        var formattedContent = '<p class="paraReply"></p><div class="reply-block-ckditor"><blockquote>' + res.body + '</blockquote></div>';
                        formattedContent = formattedContent.replace('/<br>\s*<br>/gi', '<br>');
                        MyEditor[`editor-${message_key}-${message_id}-${thread_id}`].setData(formattedContent);

                        /** Set variables*/
                        if (!emailLists[message_key]) {
                            initializeEmailLists(message_key);
                        } else {
                            $(`#reply-message-${message_key} .mainemail .emailTag`).remove();
                            $(`#reply-message-${message_key} .ccemail .emailTag`).remove();
                            $(`#reply-message-${message_key} .bccemail .emailTag`).remove();

                            emailLists[message_key].toEmailList.clear();
                            emailLists[message_key].ccEmailList.clear();
                            emailLists[message_key].bccEmailList.clear();
                        }

                        res.from.forEach(email => {
                            addEmailTag(document.querySelector(`#mainemail-${message_key}`), email, emailLists[message_key].toEmailList);
                        });
                        res.cc.forEach(email => {
                            addEmailTag(document.querySelector(`#ccemail-${message_key}`), email, emailLists[message_key].ccEmailList);
                        });
                        res.bcc.forEach(email => {
                            addEmailTag(document.querySelector(`#bccemail-${message_key}`), email, emailLists[message_key].bccEmailList);
                        });

                        console.log(emailLists[message_key]);

                        var dot_reply = $(`#dotShowreply-${message_key}-${message_id}-${thread_id}`);
                        dot_reply.parent().removeClass('showembdReplyarea');
                        dot_reply.show();

                        // $("#reply-message-"+message_key).show();
                        loading_div.css('display', 'none');
                    })
                    .catch((error) => {
                        createToast('error', 'Error fetching messages');
                        console.error("Error in Ajax request:", error);
                        loading_div.css('display', 'none');

                    });

                $('.ck-restricted-editing_mode_standard').addClass('ck-focused');

            });
        });

        const allowMaxFilesize = 100;
        const allowMaxFiles = 10;
        $('.file-attachment').each(function () {
            var message_key = $(this).data('message-key');
            var dropzoneId = "my-awesome-dropzone-" + message_key;
            const dropzones = []
            initializeDropzone(dropzoneId, message_key, dropzones);

        });

        function initializeDropzone(dropzoneId, message_key, dropzones) {
            var myDropzone = new Dropzone("#" + dropzoneId, {
                url: '{{route('user.email.system.add.attachment')}}',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
                paramName: "attachments",
                acceptedFiles: ".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.zip",
                autoProcessQueue: true,
                maxFiles: allowMaxFiles,
                maxFilesize: allowMaxFilesize,
                uploadMultiple: true,
                parallelUploads: 100,
                createImageThumbnails: true,
                thumbnailWidth: 120,
                thumbnailHeight: 120,
                addRemoveLinks: true,
                removedfile: function (file) {
                    var _ref;
                    return (_ref = file.previewElement) != null ? _ref.parentNode.removeChild(file.previewElement) : void 0;
                },
                timeout: 180000,
                dictRemoveFileConfirmation: "Are you Sure?",
                dictFileTooBig: `File is too big. Max allowed file size is ${allowMaxFilesize}mb`,
                dictInvalidFileType: "Invalid File Type",
                dictCancelUpload: "x",
                dictRemoveFile: "x",
                dictMaxFilesExceeded: `Only ${allowMaxFiles} files are allowed`,
                dictDefaultMessage: "Drop files here to upload",
                init: function () {
                    this.on("removedfile", function (file) {
                        console.log('success ' + file.name + " removed successfully.!");
                    });
                },
            });

            myDropzone.on("addedfile", function (file) {
                file.messageKey = message_key;
            });

            myDropzone.on("error", function (file, response) {
                createToast('error', response);
                myDropzone.removeFile(file);
            });

            dropzones.push(myDropzone)
        }

        // $('.hideRmsg').click(function () {
        //     console.log("Click")
        //     $(this).parent().toggleClass('hideDetails')
        // });

        $('.attachIcon').click(function () {
            $('.wrapReplyBox').addClass('showAtt');
        });
    </script>

@endpush


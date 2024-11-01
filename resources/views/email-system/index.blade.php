@extends('layouts.app')@section('cxmTitle', 'Email')

@section('content')
    @push('css')
        <style>
            .unread-message {
                background-color: #fff;
            }

            .read-message {
                background: #f2f6fc;
            }

            .read-message .mail-hover-list span,
            .read-message .mail-enter-link:hover .mail-hover-list,
            .mail-enter-link:hover .mail-hover-list {
                background-color: transparent;
            }

            .unread-message .mail-hover-list span,
            .unread-message .mail-enter-link:hover .mail-hover-list,
            .mail-enter-link:hover .mail-hover-list {
                background: transparent;
            }

            .ck.ck-balloon-panel.ck-powered-by-balloon .ck.ck-powered-by, .ck-balloon-panel_visible, .ck-powered-by__label, .ck-icon ck-reset_all-excluded {
                display: none !important;
            }

            blockquote:before {
                content: none !important;
            }

            .ck-editor__editable {
                min-height: 200px;
            }

            button#signature-modal-btn {
                height: 40px;
                border-bottom-left-radius: 20px;
                border-top-left-radius: 20px;
                border-bottom-right-radius: 20px;
                border-top-right-radius: 20px;
            }
        </style>
    @endpush
    <section class="content mail-content-tg">
        <div class="body_scroll">
            <div class="block-header">
                <div class="row">
                    <div class="col-lg-7 col-md-6 col-sm-12">
                        <h2>Emails</h2>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ route('dashboard') }}"><i class="zmdi zmdi-home"></i> TG </a></li>
                            <li class="breadcrumb-item">Emails</li> <li class="breadcrumb-item active"> <a href="
                            {{ $route_name == "Inbox" ? route('user.email.system.index', ['email' => $base_label_email]) : (in_array($route_name, ["Spam", "Trash", "Sent"]) ? route("user.email.system.".strtolower($route_name), ['email' => $base_label_email]) : '' )}}">{{$route_name}}</a></li>
                        </ul>
                    </div>
                    <div class="col-lg-5 col-md-6 col-sm-12 text-right">
                        <button class="btn btn-success" type="button" data-toggle="modal" id="signature-modal-btn" data-target="#edit-signature-modal">Signature</button>
                        @include('includes.cxm-top-right-toggle-btn')
                    </div>
                </div>
            </div>
        </div>
        <div class="section-body">
            <div class="row">
                <div class="modal-edit-task trigger--fire-modal-4"></div>
                <div class="modal-add-task-details trigger--fire-modal-3"></div>
                <div class="col-lg-12 col-md-12">
                    <div class="gmail-compose-part">
                        <a href="javascript:;" onclick="openDialog()" class="compose-btn" style="width: 160px;">
                            <span class="material-symbols-outlined">
                                edit
                            </span> Compose</a>
                        <div class="searchArea">
                                <span class="material-symbols-outlined">
                                    search
                                </span>
                            <input type="text" id="search-msg-input" class="seacrhInp form-control" placeholder="Search Mail">
                            <button id="search-msg-btn" class="btn btn-default">search</button>
                        </div>
                    </div>
                    <div class="mail-section-tabs" style="margin-top: 20px;">
                        <div class="tab-content" id="myTabContent">
                            <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                                <div class="mail-list-height">
                                    <input type="hidden" id="next_page_token" data-next-page-token="{{ $next_page_token }}">
                                    <input type="hidden" id="unique_message_ids" data-unique-message-ids="{{ json_encode($unique_message_ids) }}">
{{--                                    @if ($errors->any())--}}
{{--                                        <div class="alert alert-danger">--}}
{{--                                            <ul>--}}
{{--                                                @foreach ($errors->all() as $error)--}}
{{--                                                    <li>{{ $error }}</li>--}}
{{--                                                @endforeach--}}
{{--                                            </ul>--}}
{{--                                        </div>--}}
{{--                                    @endif--}}
                                    <table id="mailTable" class="mail-table">
                                        <tbody id="mail-table-tbody">
                                        <?php echo $all_messages; ?>
                                        <tr class="loading-gif-tr" id="loading-gif-tr">
                                            <td>
                                                <img src="{{asset('assets/images/loading-gif.gif')}}" style="margin: 8px 0 0 700px;height: 20px;"/>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="composeBox">
        <div class="charea">
            <h4>New Message</h4>
            <div class="chheadright">
                <span class="material-symbols-outlined">minimize</span>
                <span class="material-symbols-outlined closebox">close</span>
            </div>
        </div>
        <div class="cbarea">
            <div class="cearea">
                <div class="toemail">
                    <div class="mainemail d-flex align-items-center" id="mainemail">
                        <span class="mr-2">To</span>
                        <div class="emailtagsss">
                            <input type="text" placeholder="" id="to_id" name="to[]" class="emailInput gmailInput form-control toEmailInput">
                        </div>
                        <div class="gmailccbcc">
                            <div class="d-flex align-items-center">
                                <span class="showemailmagic01">Cc</span> <span class="showemailmagic02 ml-2">Bcc</span>
                            </div>
                        </div>
                    </div>
                    <div class="ccemail" id="ccemail">
                        <div class="d-flex align-items-center ccmail">
                            <span class="mr-2">Cc</span>
                            <div class="emailtagsss">
                                <input type="text" id="cc_id" name="cc[]" class="emailInput gmailInput form-control ccEmailInput">
                            </div>
                        </div>
                    </div>
                    <div class="bccemail" id="bccemail">
                        <div class="d-flex align-items-center bccmail">
                            <span class="mr-2">Bcc</span>
                            <div class="emailtagsss">
                                <input type="text" id="bcc_id" name="bcc[]" class="emailInput gmailInput form-control bccEmailInput">
                            </div>
                        </div>
                    </div>
                    <ul class="emailhint" id="contact-email-hint" style="left:35px">
                        {{--                        <li>--}}
                        {{--                            <div class="wrapemailhint">--}}
                        {{--                                <div class="userAvatar">--}}
                        {{--                                    <img src="https://ui-avatars.com/api/?rounded=true" class="img-fluid imgavatar"/>--}}
                        {{--                                </div>--}}
                        {{--                                <div class="emailname">--}}
                        {{--                                    <strong>Ahsan Zafar</strong>--}}
                        {{--                                    <span>ahsan.zafar.ontech@gmail.com</span>--}}
                        {{--                                </div>--}}
                        {{--                            </div>--}}
                        {{--                        </li>--}}
                    </ul>
                </div>
                <div class="toemail">
                    <input type="text" id="compose-subject" placeholder="Subject" class="subjectInp gmailInput form-control">
                </div>
            </div>
            <textarea name="compose_message" id="compose_editor" class="ckEditor gmailTextarea"></textarea>
        </div>
        <div class="attachmentBody composeAttachment">
            {{--            <div id="my-awesome-dropzone" class="dropzone"></div>--}}
            <form action="{{route('user.email.system.add.attachment')}}" method="post" class="dropzone" id="my-awesome-dropzone" enctype="multipart/form-data">@csrf</form>
            <input type="file" id="attachment" name="attachments[]" class="file-attachment"/>
        </div>
        <div class="cfarea">
            <button type="submit" id="submit-compose-message" class="btn btn-primary sendBtn">Send</button>
        </div>
    </div>

    <!-- Modals Start -->
    <div class="modal fade" id="edit-signature-modal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="title" id="defaultModalLabel">Signature</h4>
                </div>
                <form method="POST" id="edit-signature-form">
                    <div class="modal-body">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <textarea id="edit_signature" name="signature" class="form-control ckEditor" placeholder="Enter your signature here">{{$signature}}</textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" id="addLeadBtn" class="btn btn-success btn-round">SAVE</button>
                        <button type="button" class="btn btn-danger btn-round" data-dismiss="modal">CLOSE</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modals  End-->
@endsection

@push('cxmScripts')
    @include('email-system.ckeditor-script')
    @include('email-system.script')

    <script>
        Dropzone.autoDiscover = false;
        $("#loading-gif-tr").hide();
        const toEmailList = new Set();
        const ccEmailList = new Set();
        const bccEmailList = new Set();
        $(document).ready(function () {

            var loading_div = $('.loading_div');
            var $gmailInp = $(".emailInput");
            // var $additionalElement01 = $(".fshow");
            var $additionalElement02 = $(".gmailccbcc");

            $gmailInp.focus(function () {
                console.log("Working...")
                // $('.emailhint').show();
                // Show the additional element when the input is focused
                // $additionalElement01.show();
                $additionalElement02.show();
            });

            $gmailInp.blur(function () {
                // $('.emailhint').hide();
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

                var signature = {!! isset($signature) ? json_encode($signature) : 'null' !!};
                if (signature) {
                    MyEditor["compose_editor"].setData(signature);
                } else {
                    MyEditor["compose_editor"].setData('');
                }
            });

            $(".closebox").click(function () {
                $(this).parents(".composeBox").hide();
            });

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

            function handleAddEmail(inputSelector, containerSelector, emailList) {
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

            function handleInputFieldBlur(inputSelector, containerSelector, emailList) {
                const inputField = document.querySelector(inputSelector);
                const container = document.querySelector(containerSelector);
                let blurTimer;
                inputField.addEventListener('blur', function (event) {
                    clearTimeout(blurTimer);
                    blurTimer = setTimeout(function () {

                        // if ($("#contact-email-hint li").not('.disabled').length < 1) {
                        const email = inputField.value.trim();
                        if (email) {
                            addEmailTag(container, email, emailList);
                            inputField.value = '';
                            $('.emailhint').hide();
                        }
                        // }
                    }, 200);
                });
            }

            function handleInputFieldKeydown(inputSelector, containerSelector, emailList) {
                const inputField = document.querySelector(inputSelector);
                const container = document.querySelector(containerSelector);

                inputField.addEventListener('keydown', function (event) {
                    if (event.key === 'Enter') {
                        event.preventDefault();
                        const email = inputField.value.trim();
                        if (email) {
                            addEmailTag(container, email, emailList);
                            inputField.value = ''; // Clear the input field
                            $('#contact-email-hint').hide();
                        }
                    }
                });
            }

            function clearEmailTags(containerSelector, emailList) {
                const container = document.querySelector(containerSelector);
                container.querySelectorAll('.emailTag').forEach(tag => tag.remove());
                emailList.clear();
            }

            $(".emailInput").on("click", function () {
                handleAddEmail(`#to_id`, `#mainemail`, toEmailList);
                handleAddEmail(`#cc_id`, `#ccemail`, ccEmailList);
                handleAddEmail(`#bcc_id`, `#bccemail`, bccEmailList);

                handleInputFieldBlur(`#to_id`, `#mainemail`, toEmailList);
                handleInputFieldBlur(`#cc_id`, `#ccemail`, ccEmailList);
                handleInputFieldBlur(`#bcc_id`, `#bccemail`, bccEmailList);

                handleInputFieldKeydown(`#to_id`, `#mainemail`, toEmailList);
                handleInputFieldKeydown(`#cc_id`, `#ccemail`, ccEmailList);
                handleInputFieldKeydown(`#bcc_id`, `#bccemail`, bccEmailList);

            });
            /** Email Suggestion */
            // $(document).on('click', function (event) {
            //     if (!$(event.target).closest('.toEmailInput').length) {
            //         $("#contact-email-hint").hide();
            //     }
            // });
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
                var isToEmail = input.hasClass('toEmailInput');
                var isCcEmail = input.hasClass('ccEmailInput');
                var isBccEmail = input.hasClass('bccEmailInput');
                emailRequest = AjaxRequestGetPromise(url, {
                    search: input.val(),
                    email: '{{$base_label_email}}'
                }, null, false, null, false, false, false, abort)
                    .then((res) => {
                        var contact_list = $('#contact-email-hint');
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
                                    if (toEmailList.has(email)) {
                                        disabled = 'disabled';
                                    }
                                } else if (isCcEmail) {
                                    li_class = "liCcEmail";
                                    if (ccEmailList.has(email)) {
                                        disabled = 'disabled';
                                    }
                                } else if (isBccEmail) {
                                    li_class = "liBccEmail";

                                    if (bccEmailList.has(email)) {
                                        disabled = 'disabled';
                                    }
                                }
                                contact_list.append('<li class="' + disabled + ' ' + li_class + '"><div class="wrapemailhint"><div class="userAvatar"><span class="userAv ' + colorClass + '">' + nameAlphabet + '</span><div class="emailname"><span>' + email + '</span></div></div></l>');
                            });
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
                var selector_id = null;
                var list = null;

                if ($(this).hasClass('liToEmail')) {
                    selector_id = "mainemail";
                    list = toEmailList;
                } else if ($(this).hasClass('liCcEmail')) {
                    selector_id = "ccemail";
                    list = ccEmailList;
                } else if ($(this).hasClass('liBccEmail')) {
                    selector_id = "bccemail";
                    list = bccEmailList;
                }

                addEmailTag(document.querySelector(`#${selector_id}`), email, list);

                $('#contact-email-hint').empty().hide();
            });
            /** ================= **/

            if ($("#mail-table-tbody tr").length > 1 && $("#mail-table-tbody tr").length < 15) {
                $("#loading-gif-tr").show();
            }
            var loading_div = $('.loading_div');
            $(document).on('click', '#search-msg-btn', function () {
                var search = $("#search-msg-input").val();
                var url = '{{ $route_name == "Inbox" ? route('user.email.system.index', ['email' => $base_label_email]) : (in_array($route_name, ["Spam", "Trash", "Sent"]) ? route("user.email.system.".strtolower($route_name), ['email' => $base_label_email]) : '' )}}';
                loading_div.css('display', 'flex');
                AjaxRequestGetPromise(url, {search: search}, null, false, null, false, true, true)
                    .then((res) => {
                        if (res.status && res.status == 1 && res.all_messages && res.all_messages.length > 0) {
                            $('#mail-table-tbody').empty();
                            $('#mail-table-tbody').append(`<tr class="loading-gif-tr" id="loading-gif-tr">
                                                                <td>
                                                                    <img src="{{asset('assets/images/loading-gif.gif')}}" style="margin: 8px 0 0 690px;height: 25px;"/>
                                                                </td>
                                                            </tr>
                                                        `);
                            $(res.all_messages).insertBefore($('#loading-gif-tr'));
                            // $('#mail-table-tbody').append(res.all_messages);
                            $('#next_page_token').data('next-page-token', res.next_page_token);
                            $('#unique_message_ids').data('unique-message-ids', res.unique_message_ids);

                        }
                        loading_div.css('display', 'none');
                    })
                    .catch((error) => {
                        createToast('error', 'Error searching messages');
                        console.error("Error in Ajax request:", error);
                        loading_div.css('display', 'none');
                    });
            })
            var isFetching = false;

            $('#mail-table-tbody').on('scroll', function () {
                if ($(this).scrollTop() + $(this).innerHeight() + 0.812 >= $(this)[0].scrollHeight) {
                    console.log($(this).scrollTop());
                    console.log($(this).innerHeight());
                    console.log($(this)[0].scrollHeight);
                    console.log($(this).scrollTop() + $(this).innerHeight() >= $(this)[0].scrollHeight - 1);
                    if (!isFetching) {
                        fetch_emails(10, true);
                    }
                }
            });
            var fetchCount = 0;

            function fetch_emails(max_results, load = false) {
                var next_page_token = $("#next_page_token").data('next-page-token');
                var unique_message_ids = $("#unique_message_ids").data('unique-message-ids');
                var url = '{{ $route_name == "Inbox" ? route('user.email.system.index', ['email' => $base_label_email]) : (in_array($route_name, ["Spam", "Trash", "Sent"]) ? route("user.email.system.".strtolower($route_name), ['email' => $base_label_email]) : '' )}}';
                {{--var url = '{{route('user.email.system.index', ['email' => $base_label_email])}}';--}}
                    isFetching = true;

                if (load) {
                    $("#loading-gif-tr").show();
                }
                AjaxRequestGetPromise(url, {
                    next_page_token: next_page_token,
                    unique_message_ids: unique_message_ids,
                    max_results: max_results
                }, null, false, null, false, false, false)
                    .then((res) => {
                        if (res.status && res.status == 1 && res.all_messages && res.all_messages.length > 0) {
                            $(res.all_messages).insertBefore($('#loading-gif-tr'));
                            // $('#mail-table-tbody').append(res.all_messages);
                            $('#next_page_token').data('next-page-token', res.next_page_token);
                            $('#unique_message_ids').data('unique-message-ids', res.unique_message_ids);

                            fetchCount++;
                        }
                        $("#loading-gif-tr").hide();
                        loading_div.css('display', 'none');

                        if (isElementScrollable(document.getElementById('mail-table-tbody'))) {

                            console.log('Element is scrollable');
                        } else {
                            // $("#loading-gif-tr").show();
                            console.log('Element is not scrollable');
                        }
                        if (fetchCount < 3) {
                            setTimeout(function () {
                                if (!isFetching) {
                                    fetch_emails(8, true);
                                }
                            }, 7000);
                        }
                        isFetching = false;
                    })
                    .catch((error) => {
                        if (error.statusText === 'abort') {
                            console.log('Ajax request aborted.');
                        } else {
                            createToast('error', 'Error fetching messages');
                            console.error("Error in Ajax request:", error);
                        }
                        loading_div.css('display', 'none');
                        $("#loading-gif-tr").hide();
                        isFetching = false;

                    });

            }

            setTimeout(function () {
                if (!isFetching) {
                    fetch_emails(10, true);
                }
            }, 3000);


            $(document).on('click', '.mark-as-read-unread-message, .mark-as-unread-read-message', function (event) {
                event.preventDefault();
                var message_id = $(this).data('message_id');
                var label = $(this).data('label');
                var is_unread = $(this).hasClass('mark-as-read-unread-message') ? 1 : 0; /** mark-as-unread means message is already read*/

                /** Perform AJAX request to update the mark as read / unread */
                var url = '{{ route('user.email.system.mark.message.read.unread') }}';
                var formData = new FormData();
                formData.append("message_id", message_id)
                formData.append("label", label)
                formData.append("is_unread", is_unread)
                AjaxRequestPostPromise(url, formData, null, false, null, false, false, true)
                    .then((res) => {
                        /** Handle success, if needed */
                        console.log(res);
                        if (res.status && res.status == 1) {
                            let title = is_unread ? 'mark as unread' : 'mark as read';
                            let text = is_unread ? 'mark_email_unread' : 'mark_email_read';

                            $(this).closest('a').attr('title', title);
                            $(this).text(text);
                            $(this).toggleClass('mark-as-read-unread-message mark-as-unread-read-message');
                            $("tr#" + message_id).toggleClass('unread-message read-message');
                            createToast('success', 'Message status updated');
                        } else {
                            createToast('error', 'Failed to update email status');
                            console.error('Failed to update email status');
                        }
                        loading_div.css('display', 'none');
                    })
                    .catch((error) => {
                        createToast('error', 'Error changing message status');
                        console.error("Error in Ajax request:", error);
                        loading_div.css('display', 'none');
                    })
                    .finally(() => {
                        loading_div.css('display', 'none');
                    })
            });

            $(document).on('click', '#submit-compose-message', function (event) {
                event.preventDefault();
                loading_div.css('display', 'flex');

                if (toEmailList.length < 0) {
                    createToast('error', 'The Recipient address field is required.');
                    return false;
                }

                /** Perform AJAX request to update the mark as read / unread */
                var url = '{{ route('user.email.system.compose.message') }}';
                var formData = new FormData();

                var dropzone = $('#my-awesome-dropzone')[0].dropzone;

                dropzone.files.forEach(function (file) {
                    formData.append('attachments[]', file);
                });


                toEmailList.forEach(email => {
                    formData.append('to[]', email);
                });
                ccEmailList.forEach(email => {
                    formData.append('cc[]', email);
                });
                bccEmailList.forEach(email => {
                    formData.append('bcc[]', email);
                });
                formData.append("subject", $("#compose-subject").val());
                formData.append("compose_message", MyEditor["compose_editor"].getData());
                formData.append("email", '{{$base_label_email}}');

                loading_div.css('display', 'flex');

                AjaxRequestPostPromise(url, formData, null, false, null, false, true, true)
                    .then((res) => {
                        /** Handle success, if needed */
                        console.log(res);
                        if (res.status && res.status == 1) {

                            $('#compose-subject').val('');
                            MyEditor["compose_editor"].setData('');
                            clearEmailTags('.mainemail', toEmailList);
                            clearEmailTags('.ccemail', ccEmailList);
                            clearEmailTags('.bccemail', bccEmailList);
                            myDropzone.removeAllFiles();
                            createToast('success', 'Message compose successfully');

                        } else {
                            createToast('error', 'Failed to compose message');
                        }
                        loading_div.css('display', 'none');
                        $(".composeBox").hide();
                    })
                    .catch((error) => {
                        createToast('error', 'Error composing message');
                        console.error("Error in Ajax request:", error);
                        loading_div.css('display', 'none');
                        $(".composeBox").hide();
                    })
                    .finally(() => {
                        loading_div.css('display', 'none');
                        $(".composeBox").hide();
                    })
            });


            const allowMaxFilesize = 100;
            const allowMaxFiles = 10;

            // var myDropzone = new Dropzone("#" + dropzoneId, {
            var myDropzone = new Dropzone("#my-awesome-dropzone", {
                url: '{{route('user.email.system.add.attachment')}}',
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
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
                // Language Strings
                dictFileTooBig: `File is to big. Max allowed file size is ${allowMaxFilesize}mb`,
                dictInvalidFileType: "Invalid File Type",
                dictCancelUpload: "x",
                dictRemoveFile: "x",
                dictMaxFilesExceeded: `Only ${allowMaxFiles} files are allowed`,
                dictDefaultMessage: "Drop files here to upload",
                init: function () {
                    this.on("removedfile", function (file) {
                        console.log('success' + file.name + " removed successfully.!");
                    });
                }
            });
            myDropzone.on("error", function (file) {
                myDropzone.removeFile(file);
            });


            /** Signature */
            /** Add Signature Start */
            $('#edit-signature-form').on('submit', function (e) {
                e.preventDefault();
                var url = '{{ route('user.email.system.edit.signature') }}';
                var formData = new FormData();
                formData.append("label", `{{$base_label_email}}`)
                formData.append("signature", MyEditor["edit_signature"].getData())

                AjaxRequestPostPromise(url, formData, null, false, null, false, true, true)
                    .then((res) => {
                        /** Handle success, if needed */
                        console.log(res);
                        if (res.status && res.status === 1) {
                            var formattedContent = res.signature.replace('/<br>\s*<br>/gi', '<br>');

                            MyEditor["edit_signature"].setData(formattedContent)
                            MyEditor["compose_editor"].setData(formattedContent)
                            // $("#edit_signature").val(res.signature)
                            createToast('success', 'Signature updated successfully');

                        } else {
                            createToast('error', 'Failed to update signature');
                        }
                    })
                    .catch((error) => {
                        createToast('error', 'Error updating signature');
                        console.error("Error in Ajax request:", error);
                    })
                    .finally(() => {
                        $('#edit-signature-modal').modal('hide');
                    })

            });
            /** Add Signature End */
            /** Signature */

        });

        function isElementScrollable(element) {
            let overflow = element.style.overflow;
            element.style.overflow = 'hidden';
            let isScrollable = element.scrollHeight > element.clientHeight;
            element.style.overflow = overflow;
            return isScrollable;
        }

        $('.seacrhInp').focus(function () {
            $(this).parent().addClass('bgColorAdd');
        });
        $('.seacrhInp').blur(function () {
            $(this).parent().removeClass('bgColorAdd');
        });
    </script>

@endpush

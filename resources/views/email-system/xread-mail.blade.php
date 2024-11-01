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
                        <a href="{{ ucfirst($route_name) == "Inbox" ? route('user.email.system.index', ['email' => $label_email]) : (in_array(ucfirst($route_name), ["Spam", "Trash", "Sent"]) ? route("user.email.system.".strtolower($route_name), ['email' => $label_email]) : '' )}}" class="btn btn-success btn-icon rounded-circle"
                        type="button"><i class="zmdi zmdi-arrow-left"></i></a>
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
                            <h2 class="emailbodyHeading"><?php echo isset($cxmEmailHeaders['Subject']) ? $cxmEmailHeaders['Subject'] : 'Empty Subject'; ?></h2>
                        </div>

                        <div class="wrapReplyBody">
                            <div class="replyBodyarea">
                                <div class="userAvatar">
                                    <img src="https://ui-avatars.com/api/?rounded=true" class="img-fluid imgavatar" />
                                </div>
                                <div class="replybodyTop">
                                    <div class="rbtin">
                                        <div class="rbtemleft">
                                            <h5 class="font-weight m-0">InMotion Hosting</h5>
                                            <span class="text-sm text-gray"><?php echo isset($cxmEmailHeaders['FromEmail']) ? $cxmEmailHeaders['FromEmail'] : 'From not found'; ?></span>
                                        </div>
                                        <div class="rbtemright">
                                            <h6 class="m-0">
                                                <span class="mailbox-read-time float-right"><?php echo isset($cxmEmailHeaders['Date']) ? date("M d Y - g:iA", strtotime($cxmEmailHeaders['Date'])) : 'Date not found'; ?></span>
                                            </h6>
                                            <div class="dropdown">
                                                <a href="javascript:;" class="replydot dropdown-toggle"id="dropdownMenuButton" data-toggle="dropdown">
                                                    <span class="material-symbols-outlined">
                                                        more_vert
                                                        </span>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                                                    <a class="dropdown-item" href="javascript:;"><span class="material-symbols-outlined">
                                                        reply
                                                        </span> <span class="fs">Reply</span></a>
                                                    <a class="dropdown-item" href="javascript:;">
                                                        <span class="material-symbols-outlined">
                                                            forward
                                                            </span> <span class="fs">Forward</span></a>

                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                    <div class="rtbbd">
                                        <div class="card-body p-0">

                                            <div class="mailbox-read-message">
                                                <?php echo html_entity_decode(preg_replace('/<\s*style.+?<\s*\/\s*style.*?>/si', ' ', $cxmEmailBodyPure)); ?>
                                                <div class="replybddetail">
                                                    <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Rem quasi, reiciendis earum asperiores enim numquam delectus molestias doloremque reprehenderit praesentium aut nobis impedit veritatis dolorum sed non omnis minus commodi.</p>
                                                </div>
                                                <a href="javascript:;" class="showembddetail">
                                                    <span class="material-symbols-outlined">
                                                        more_horiz
                                                        </span>
                                                </a>
                                                <div class="wrapBox" id="reply-message">
                                                    <div class="userAvatar">
                                                        <img src="https://ui-avatars.com/api/?rounded=true" class="img-fluid imgavatar" />
                                                    </div>
                                                    <div class="wrapReplyBox">
                                                        <div class="toemail replytoemail">
                                                            <div class="mainemail d-flex align-items-center">
                                                                <span class="mr-2">To</span>
                                                                <input type="text" placeholder="" name="to[]" class="emailInput gmailInput form-control toEmailInput">
                                                                <div class="gmailccbcc">
                                                                    <div class="d-flex align-items-center">
                                                                        <span class="showemailmagic01">Cc</span>
                                                                        <span class="showemailmagic02 ml-2">Bcc</span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="ccemail">
                                                                <div class="d-flex align-items-center ccmail">
                                                                    <span class="mr-2">Cc</span>
                                                                    <input type="text" name="cc[]" class="emailInput gmailInput form-control ccEmailInput">
                                                                </div>
                                                            </div>
                                                            <div class="bccemail">
                                                                <div class="d-flex align-items-center bccmail">
                                                                    <span class="mr-2">Bcc</span>
                                                                    <input type="text" name="bcc[]" class="emailInput gmailInput form-control bccEmailInput">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="replyBody">
                                                            <textarea id="editor" class="ckEditor" name="reply_message"></textarea>
                                                        </div>
                                                        <div class="replyFt">
                                                            <button class="btn btn-primary" id="submit-reply-message" type="submit">Send</button>
                                                            <div id="remove-reply">
                                                                <i class="zmdi zmdi-delete"></i>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card-header emailcardheader p-0">
{{--                            <h3 class="card-title p-0">From: <?php echo isset($cxmEmailHeaders['From']) ? $cxmEmailHeaders['From'] : 'From not found'; ?></h3>--}}
                            <div class="card-tools d-none">
                                <a href="#" class="btn btn-tool" title="Previous"><i class="fas fa-chevron-left"></i></a>
                                <a href="#" class="btn btn-tool" title="Next"><i class="fas fa-chevron-right"></i></a>
                            </div>
                        </div>

                        <?php if (count($cxmAttachments) > 0){ ?>
                        <div class="card-footer bg-white">
                            <ul class="mailbox-attachments d-flex align-items-stretch clearfix">
                                    <?php
                                foreach ($cxmAttachments as $cxmAttachmentKey => $cxmAttachment) {
                                    if (isset($cxmAttachment->data)) {
                                        $cxmAttachmentFile = str_replace('-', '+', $cxmAttachment->data);
                                        $cxmAttachmentFile = str_replace('_', '/', $cxmAttachmentFile);

                                    } else {
                                        $cxmAttachmentFile = '';
                                    }
                                    ?>
                                <li>
                                        <?php if (strpos($cxmAttachment->filename, '.png') !== false || strpos($cxmAttachment->filename, '.jpg') !== false){ ?>
                                    <span class="mailbox-attachment-icon has-img overflow-hidden">
                                        <img src="data:image/png;base64, <?php echo $cxmAttachmentFile; ?>" alt="Attachment" class="ximg-thumbnail">
                                    </span>
                                    <div class="mailbox-attachment-info text-truncate">
                                        <a href="data:image/png;base64, <?php echo $cxmAttachmentFile; ?>" class="mailbox-attachment-name cxm-attach-file" data-toggle="modal" data-target=".cxmAttachmentModal"><i class="zmdi zmdi-pin"></i> <?php echo $cxmAttachment->filename ?>
                                        </a> <span class="mailbox-attachment-size clearfix mt-1">
                                        <span><?php echo number_format($cxmAttachment->size); ?> KB</span>
                                        <a download="<?php echo $cxmAttachment->filename ?>" href="data:image/png;base64, <?php echo $cxmAttachmentFile; ?>" class="btn btn-default btn-sm float-right"><i class="zmdi zmdi-cloud-download"></i></a>
                                        </span>
                                    </div>
                                    <?php } else { ?>
                                    <span class="mailbox-attachment-icon has-img overflow-hidden">
                                        <i class="far fa-file" style="font-size:90px; padding:22px 0;"></i>
                                    </span>
                                    <div class="mailbox-attachment-info text-truncate">
                                        <i class="zmdi zmdi-pin"></i> <?php echo $cxmAttachment->filename ?>
                                        <span class="mailbox-attachment-size clearfix mt-1">
                                        <span><?php echo number_format($cxmAttachment->size); ?> KB</span>
                                            <a download="<?php echo $cxmAttachment->filename ?>" href="data:image/png;base64, <?php echo $cxmAttachmentFile; ?>" class="btn btn-default btn-sm float-right"><i class="zmdi zmdi-cloud-download"></i></a>
                                        </span>
                                    </div>
                                    <?php } ?>
                                </li>
                                <?php } ?>
                            </ul>
                        </div>
                        <?php } ?>
                        <div class="card-footer replybodyft">
                            <div class="float-right">
{{--                                <form action="#" method="POST">--}}
{{--                                    @csrf--}}
                                    <button type="button" name="reply" value="reply" class="btn btn-navy cxm-btn-1" id="reply-button">
                                        <i class="zmdi zmdi-mail-reply"></i> Reply
                                    </button>
                                    <button type="button" name="forward" value="forward" class="btn btn-navy cxm-btn-1">
                                        <i class="zmdi zmdi-share"></i> Forward
                                    </button>
{{--                                    <input type="hidden" name="to" value="<?php echo $cxmEmailHeaders['FromEmail']; ?>">--}}
{{--                                    <input type="hidden" name="subject" value="<?php echo $cxmEmailHeaders['Subject']; ?>">--}}
{{--                                    <input type="hidden" name="body" value="<?php echo html_entity_decode($cxmEmailBodyEncode); ?>">--}}
{{--                                    <input type="hidden" name="attachments" value="<?php echo base64_encode(json_encode($cxmAttachments)); ?>">--}}
{{--                                </form>--}}
                            </div>
                            <button type="button" class="btn btn-default d-none">
                                <i class="far fa-trash-alt"></i> Delete
                            </button>
                            <button type="button" class="btn btn-default" onclick="window.print();">
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
        $(document).ready(function () {
            $("#reply-message").hide();
            var formattedContent = '<p></p>';
            {{--var formattedContent = '<p></p><div><blockquote>' + {!! json_encode( htmlspecialchars($formattedContentHeader) . addslashes($formattedContentBody)); !!} + '</blockquote></div>';--}}
            MyEditor["editor"].setData(formattedContent);
            var loading_div = $('.loading_div');
            $(document).on('click', '#submit-reply-message', function (event) {
                event.preventDefault();
                loading_div.css('display', 'flex');

                /** Perform AJAX request to update the mark as read / unread */
                var url = '{{ route('user.email.system.reply.message') }}';
                var formData = new FormData();

                toEmailList.forEach(email => {
                    formData.append('to[]', email);
                });
                ccEmailList.forEach(email => {
                    formData.append('cc[]', email);
                });
                bccEmailList.forEach(email => {
                    formData.append('bcc[]', email);
                });

                formData.append("email", '{{$label_email}}');
                formData.append("message_id", '{{$message_id}}');
                formData.append("reply_message", MyEditor["editor"].getData());

                loading_div.css('display', 'flex');

                AjaxRequestPostPromise(url, formData, null, false, null, false,true,true)
                    .then((res) => {
                        /** Handle success, if needed */
                        console.log(res);
                        if (res.status && res.status == 1) {
                            createToast('success','Reply message submitted successfully');
                        } else {
                            console.error('Failed to submit reply');
                            createToast('error','Failed to submit reply');
                        }
                        loading_div.css('display', 'none');
                        $("#reply-message").hide();
                    })
                    .catch((error) => {
                        createToast('error','Failed to submit reply');
                        console.error("Error in Ajax request:", error);
                        loading_div.css('display', 'none');
                        $("#reply-message").hide();
                    })
                    .finally(() => {
                        loading_div.css('display', 'none');
                        $("#reply-message").hide();
                    })
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
                $(this).parent().addClass('showembd');
                $(this).hide();
            });

            $(document).on("click", '#remove-reply', function () {
                $("#reply-message").hide();
            });

            $(document).on("click", '#reply-button', function () {
                {{--loading_div.css('display', 'flex');--}}
                {{--var email = '{{$label_email}}';--}}
                {{--var message_id = '{{$message_id}}';--}}


                {{--var url = '{{route('user.email.system.index', ['email' => $base_label_email])}}';--}}
                {{--AjaxRequestGetPromise(url, {email: email,message_id:message_id}, null, false, null, false)--}}
                {{--    .then((res) => {--}}
                {{--        if (res.status && res.status == 1 && res.all_messages && res.all_messages.length > 0) {--}}
                {{--            $('#mail-table-tbody').append(res.all_messages);--}}
                {{--            $('#next_page_token').data('next-page-token', res.next_page_token);--}}
                {{--        }--}}
                {{--        loading_div.css('display', 'none');--}}
                {{--    })--}}
                {{--    .catch((error) => {--}}
                {{--        console.error("Error in Ajax request:", error);--}}
                {{--        loading_div.css('display', 'none');--}}
                {{--    });--}}
                $("#reply-message").toggle();
            })
        });
    </script>
@endpush

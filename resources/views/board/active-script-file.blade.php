<script>
    base_url = "{{url('')}}" + "/";
    // $(function () {
    //     $(".datepicker").datepicker({firstDay: 1});
    // });

    // drop zone image start

    // function showDropZone(event) {
    //     event.preventDefault();
    //     document.getElementById('drop-zone').style.display = 'block';
    // }

    // function dropHandler(event) {
    //     event.preventDefault();
    //     // Hide the drop zone
    //     document.getElementById('drop-zone').style.display = 'none';
    //
    //     // Access the dropped files
    //     const files = event.dataTransfer.files;
    //
    //     // Display file information
    //     displayFileInfo(files[0]);
    //
    //     // Display the dropped image
    //     displayDroppedImage(files[0]);
    // }

    //
    // function displayFileInfo(file) {
    //     const fileInfo = document.getElementById('file-info');
    //     const fileName = document.getElementById('file-name');
    //     const fileSize = document.getElementById('file-size');
    //
    //     console.log(file);
    //     // Update the file information
    //     fileName.textContent = `File Name: ${file.name}`;
    //     fileSize.textContent = `File Size: ${formatBytes(file.size)}`;
    //
    //     // Show the file information div
    //     fileInfo.style.display = 'block';
    // }
    //
    // function displayDroppedImage(file) {
    //     const droppedImage = document.getElementById('dropped-image');
    //
    //     // Check if the dropped file is an image
    //     if (file.type.startsWith('image/')) {
    //         const reader = new FileReader();
    //
    //         reader.onload = function (e) {
    //             // Set the source of the image element
    //             droppedImage.innerHTML = `<img src="${e.target.result}" alt="Dropped Image">`;
    //
    //             // Show the dropped image div
    //             droppedImage.style.display = 'block';
    //         };
    //
    //         // Read the data URL of the image
    //         reader.readAsDataURL(file);
    //     }
    // }

    // Function to format bytes into a readable format
    // function formatBytes(bytes, decimals = 2) {
    //     if (bytes === 0) return '0 Bytes';
    //
    //     const k = 1024;
    //     const dm = decimals < 0 ? 0 : decimals;
    //     const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
    //
    //     const i = Math.floor(Math.log(bytes) / Math.log(k));
    //
    //     return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
    // }

    // drop zone image end

    //
    // function addAttachment() {
    //     const fileInput = document.getElementById('fileAttachment');
    //     const attachmentDiv = createAttachmentElement(fileInput.files[0]);
    //     appendAttachment(attachmentDiv);
    //     fileInput.value = '';
    // }
    //
    // function createAttachmentElement(source, target = '') {
    //     const attachmentDiv = document.createElement('div');
    //     const contentElement = source instanceof File ?
    //         (source.type.startsWith('image/') ? createImageElement(source) : createDocxLinkElement(source)) :
    //         createLinkElement(source, target);
    //     attachmentDiv.appendChild(contentElement);
    //     return attachmentDiv;
    // }
    //
    // function addLinkAttachment() {
    //     const linkInput = document.getElementById('linkField').value.trim();
    //     if (linkInput) {
    //         const attachmentDiv = createAttachmentElement(linkInput, '_blank');
    //         appendAttachment(attachmentDiv);
    //         document.getElementById('linkField').value = '';
    //     }
    // }

    // Function to handle dynamic button clicks
    document.querySelectorAll('.dynamiclabelBtn').forEach(button => {
        button.addEventListener('click', function () {
            var target = this.getAttribute('data-target');
            var bodyDiv = document.querySelector('.label-dynamic-div');
            var targetDiv = document.querySelector('.' + target);
            bodyDiv.innerHTML = '';
            var clonedDiv = targetDiv.cloneNode(true);
            clonedDiv.style.display = "block";
            bodyDiv.appendChild(clonedDiv);
        });
    });
    //
    // document.querySelectorAll('.dynamiclabelEditBtn').forEach(button => {
    //
    //     button.addEventListener('click', function () {
    //         var target = this.getAttribute('data-target');
    //         var bodyDiv = document.querySelector('.label-dynamic-div');
    //         var targetDiv = document.querySelector('.' + target);
    //
    //         //remove all Element in the .body div
    //         bodyDiv.innerHTML = '';
    //
    //         // Cloning the target div and appending it to the body div
    //         var clonedDiv = targetDiv.cloneNode(true);
    //         clonedDiv.style.display = "block"; // Ensure the cloned div is visible
    //         bodyDiv.appendChild(clonedDiv);
    //     });
    // });


    // Function to append a card to the container
    function appendCard(cardName) {
        // Create a new card element
        var newCard = $('<div class="card">' + cardName + "</div>");

        // Append the new card to the card container
        $("#cardContainer").append(newCard);
    }


    // function appendAttachment(attachmentElement) {
    //     document.getElementById('attachments').appendChild(attachmentElement);
    //     // Initialize Lightbox
    //     lightbox.init();
    // }
    //
    // function createImageElement(file) {
    //     const imgElement = document.createElement('a');
    //     imgElement.href = URL.createObjectURL(file);
    //     imgElement.setAttribute('data-lightbox', 'image'); // Set the lightbox group
    //     const img = document.createElement('img');
    //     img.src = URL.createObjectURL(file);
    //     img.style.maxWidth = '100%'; // Set max-width to 100%
    //     img.style.height = 'auto'; // Maintain aspect ratio
    //     imgElement.appendChild(img);
    //     return imgElement;
    // }
    //
    // function createDocxLinkElement(file) {
    //     const linkElement = document.createElement('a');
    //     linkElement.href = URL.createObjectURL(file);
    //     linkElement.download = file.name;
    //     linkElement.textContent = 'Download Docx';
    //     return linkElement;
    // }
    //
    // function createLinkElement(link, target = '') {
    //     const linkElement = document.createElement('a');
    //     linkElement.href = link;
    //     linkElement.target = target;
    //     linkElement.textContent = link;
    //     return linkElement;
    // }

    function returnDiv() {
        $("#inner-dropdown").css("display", "none");
        $("#dropdown-menu-id").css("display", "block");
    }

    function closeDiv() {
        $("#dropdown-menu-id, .inner-dropdown").css("display", "none");
    }

    function openlabelDiv() {
        $(".label-dynamic-div").css("display", "block");
        $(".labeldropdown-inner").css("display", "none");
    }

    function returnlabelDiv() {
        $(".label-dynamic-div").css("display", "none");
        $(".right-side-label-tab").css("display", "block");
        // $(".labeldropdown-inner").css("display", "block");
    }

    function closelabelDiv() {
        $(".label-dynamic-div").css("display", "none");
        $(".labeldropdown-inner").css("display", "none");
    }

    // function editlabelDiv() {
    //     $(".edit-label-dynamic-div").css("display", "block");
    //     $(".labeldropdown-inner").css("display", "none");
    // }
    //
    // function editreturnlabelDiv() {
    //     $(".edit-label-dynamic-div").css("display", "none");
    //     $(".right-side-label-tab").css("display", "block");
    //     // $(".labeldropdown-inner").css("display", "block");
    // }
    //
    // function editcloselabelDiv() {
    //     $(".edit-label-dynamic-div").css("display", "none");
    //     $(".labeldropdown-inner").css("display", "none");
    // }

    $(document).ready(function () {
        var startDate;
        var endDate;
        var selectedDueDate;

        function formatDateString(date) {
            return date.toLocaleDateString('en-US', {
                year: 'numeric',
                month: '2-digit',
                day: '2-digit'
            });
        }

        function formatTimeString(date) {
            return date.toLocaleTimeString('en-US', {
                hour: 'numeric',
                minute: 'numeric',
                hour12: true
            });
        }

        /** Enable/disable start date & time depending on checkbox */
        $('#check-start-date').change(function () {
            /** If start date checkbox is unchecked */
            if (!this.checked) {
                $('#board-start-date').prop('disabled', true);
            } else {
                /** If start date checkbox is checked */
                $('#board-start-date').prop('disabled', false);

                /** Check if due date checkbox is checked and there is a valid endDate */
                if ($('#check-due-date').prop('checked') && endDate) {
                    /** Use endDate as the start date */
                    $(".datepicker").datepicker("setDate", endDate);
                } else {
                    /** Otherwise, use startDate or the current date */
                    const selectedDate = startDate || endDate || new Date();
                    $('#board-start-date').val(formatDateString(selectedDate));
                }
            }

            /** If both start date and due date checkboxes are checked */
            if (this.checked && $('#check-due-date').prop('checked')) {
                const selectedDate = startDate || endDate || new Date();

                /** Logic to handle start and end dates */
                if (!startDate || (selectedDate < startDate && endDate)) {
                    startDate = selectedDate;
                    endDate = null;
                } else if (selectedDate >= startDate && !endDate) {
                    endDate = selectedDate;
                } else {
                    if ($('#board-start-date').val()) {
                        startDate = new Date($('#board-start-date').val());
                    } else {
                        startDate = null;
                    }
                }

                /** Set the start date input field value */
                $('#board-start-date').val(formatDateString(startDate));
            } else if (!this.checked && $('#check-due-date').prop('checked')) {
                /** If start date is unchecked and due date is checked */
                const selectedDueDate = endDate || new Date();
                $(".datepicker").datepicker("setDate", selectedDueDate);
            } else if (this.checked && !$('#check-due-date').prop('checked')) {
                /** If start date is checked and due date is unchecked */
                const selectedStartDate = startDate || new Date();
                $(".datepicker").datepicker("setDate", selectedStartDate);
            } else {
                /** If either start date or due date checkboxes are unchecked */
                const selectedDate = startDate || endDate || new Date();
                $('#board-start-date').val(formatDateString(selectedDate));
                if (selectedDate) {
                    startDate = selectedDate;
                } else if ($('#board-start-date').val()) {
                    startDate = new Date($('#board-start-date').val());
                } else {
                    startDate = null;
                }
            }

            /** Refresh the datepicker display */
            $(".datepicker").datepicker("refresh");
        });

        /** Enable disabled due date depending on checkbox*/
        $('#check-due-date').change(function () {
            $('#board-due-date').prop('disabled', !this.checked);
            $('#board-due-time').prop('disabled', !this.checked);

            /** If due date checkbox is unchecked, reset the end date */
            if (!this.checked) {
                endDate = null;
                console.log(endDate);
            } else {
                const selectedDate = selectedDueDate || endDate || new Date();
                updateDueDateInput(selectedDate);
                $(".datepicker").datepicker("setDate", selectedDate);

            }

            $(".datepicker").datepicker("refresh"); /** Update the datepicker display */
        });

        function updateDueDateInput(selectedDate) {
            const currentDate = new Date();
            const formattedSelectedDate = formatDateString(selectedDate);

            if (currentDate.getDate() !== selectedDate.getDate()) {
                $('#board-due-date').val(formattedSelectedDate);
                $('#board-due-time').val(formatTimeString(selectedDate));
            } else {
                $('#board-due-date').val($('#board-due-date').val());
                $('#board-due-time').val($('#board-due-time').val());
            }
        }

        function initializeDatepicker() {
            $(".datepicker").datepicker({
                prevText: '<i class="fas fa-angle-left"></i>',
                nextText: '<i class="fas fa-angle-right"></i>',
                dateFormat: 'mm/dd/yy', /** Set the date format explicitly */
                defaultDate: selectedDueDate || startDate || endDate || new Date(),
                beforeShowDay: function (date) {
                    if (startDate && date >= startDate && date <= endDate) {
                        return [true, 'ui-range-highlight'];
                    }
                    return [true, ''];
                },
                onSelect: function (dateText, inst) {
                    var selectedDate = new Date(dateText);
                    var selectedTime = new Date();

                    if ($('#check-due-date').prop('checked')) {
                        $('#board-due-date').val(formatDateString(selectedDate));
                    }

                    /*if ($('#check-start-date').prop('checked')) {
                        $('#board-start-date').val(formatDateString(selectedDate));
                    }

                    if ($('#check-start-date').prop('checked') && $('#check-due-date').prop('checked')) {
                        /!** Both start date and due date are checked *!/
                        if (!startDate || (selectedDate < startDate && endDate)) {
                            startDate = selectedDate;
                            endDate = null;
                        } else if (selectedDate >= startDate && !endDate) {
                            endDate = selectedDate;
                        } else {
                            startDate = selectedDate;
                            endDate = null;
                        }
                    } else if (!$('#check-start-date').prop('checked') && $('#check-due-date').prop('checked')) {
                        /!** Only due date is checked *!/
                        startDate = null;
                        endDate = selectedDate;
                    }*/

                    if ($('#check-start-date').prop('checked')) {
                        if ($('#check-due-date').prop('checked')) {
                            if (!startDate || (selectedDate < startDate && endDate)) {
                                startDate = selectedDate;
                                endDate = null;
                            } else if (selectedDate >= startDate && !endDate) {
                                endDate = selectedDate;
                            } else {
                                startDate = selectedDate;
                                endDate = null;
                            }
                        } else {
                            startDate = selectedDate;
                        }
                        $('#board-start-date').val(formatDateString(startDate));
                    } else if (!$('#check-start-date').prop('checked') && $('#check-due-date').prop('checked')) {
                        startDate = null;
                        endDate = selectedDate;
                    }


                    $(".datepicker").datepicker("refresh"); /** Update the datepicker display */

                    /** Format the dates to the desired format (e.g., M/D/YYYY) */
                    var formattedStartDate = startDate ? formatDateString(startDate) : (selectedDate ? formatDateString(selectedDate) : '');
                    var formattedEndDate = selectedDate ? formatDateString(selectedDate) : (endDate ? formatDateString(endDate) : '');
                    var formattedDueTime = selectedTime ? formatTimeString(selectedTime) : (endDate ? formatTimeString(endDate) : '');

                    /** Set the values to the input fields */
                    $("#board-start-date").val(formattedStartDate);
                    $("#board-due-date").val(formattedEndDate);
                    $("#board-due-time").val(formattedDueTime);
                }
            });
        }

        const edit_title = $('#edit_title');

        let originalTitle = edit_title.val();

        $(document).on("hidden.bs.modal", "#modal-board-list-card-id", function () {
            $("#inner-dropdown").css("display", "none");
        });

        $(document).on('click', function (event) {
            $("#inner-dropdown").css("display", "none");
            $("#dropdown-menu").css("display", "none");
        });

        function openPopover() {
            $(".move-pop-list .popover").css("display", "block");
        }

        function closePopover() {
            $(".move-pop-list .popover").css("display", "none");
        }

        $(".openPopoverBtn").on("click", openPopover);

        const loading_div = $('.loading_div');

        autosize();

        function autosize() {
            var text = $('.editableText');

            text.each(function () {
                $(this).attr('rows', 1);
                resize($(this));
            });

            text.on('input', function () {
                resize($(this));
            });

            function resize($text) {
                $text.css('height', 'auto');
                $text.css('height', $text[0].scrollHeight + 'px');
            }
        }

        /** Prepend Comment Function == 0*/
        function prependModalActivityComment(board_list_card_id, user_name, activity) {
            /** If activity type is 0 (comment), append it as a comment */
            var nameAlphabet = $.trim(user_name).split(' ').map(function (word) {
                return word.charAt(0).toUpperCase();
            }).join('').substring(0, 2);
            /**First Letter*/
            var fnl = $.trim(user_name).charAt(0).toLowerCase();
            var colorClass = getColorAccordingToName(fnl);
            var checkid = `{{auth()->user()->id}}`;
            /**create comment prepand on save*/
            $('#modal-activities').prepend(`
				<div class="coment-div new-comments comment-activity" id="activity-main-div-${activity.activity_id}">
                    <div class="por-small" style="cursor: pointer;">
                        <span class="member-avatar ${colorClass}" title="${activity.user_name}">${nameAlphabet}</span>
                    </div>
                    <div class="comment-box">
                        <div class="form-group">
                            <div class="container">
                                <div class="show-comment updt-comment" style="border-radius:10px;padding: 0 12px;cursor:auto;" id="para-${board_list_card_id}-${activity.comment_id}">
			                        <div class="upd-name-div">
			                            <h4 class="upd-name">${activity.user_name}</h4>
			                            <h5 class="upd-date">${activity.comment_created_at}</h5>
			                        </div>
                                    <div class="upd-comment-div">
                                        <p>${activity.comment}</p>
                                    </div>
                                    <div class="upd-crud-list">
                                        <ul class="crud-list">
                                            <li><a href="javascript:void(0);" class="comment-add-reaction">
                                                <span class="material-symbols-outlined">
                                                    add_reaction
                                                </span>
                                            </a></li>
                                    		${checkid == activity.user_id ?
                `<li><a href="javascript:void(0);" class="comment-edit-link" data-id="${activity.comment_id}">Edit</a></li>
											<li><a href="javascript:void(0);" class="comment-delete-link" data-id="${activity.comment_id}" data-activity-id="${activity.activity_id}">Delete</a></li>`
                : ''}
                                        </ul>
                                    </div>
                                </div>
                                <div class="ck-comment-div" id="ck-comment-div-${board_list_card_id}-${activity.comment_id}" style="display: none;">
                                    <textarea id="editor-comment-${board_list_card_id}-${activity.comment_id}" class="CommentckEditor" name="comment"></textarea>
                                    <button type="button" class="ck-button ck-save-button-comment" id="ck-save-button-comment-${board_list_card_id}-${activity.comment_id}" data-id="${activity.comment_id}" disabled>Save</button>
                                    <button type="button" class="ck-close-button-comment btn btn-secondary" id="ck-close-button-comment-${board_list_card_id}-${activity.comment_id}"  data-id="${activity.comment_id}">Discard</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>`);

        }

        /** Prepend Attachment Functions  == 1*/
        function prependModalActivityAttachment(activity) {
            /** If activity type is 0 (comment), append it as a comment */
            var nameAlphabet = $.trim(activity.user_name).split(' ').map(function (word) {
                return word.charAt(0).toUpperCase();
            }).join('').substring(0, 2);
            /**First Letter*/
            var fnl = $.trim(activity.user_name).charAt(0).toLowerCase();
            var colorClass = getColorAccordingToName(fnl);
            $('#modal-activities').prepend(`
			    <div class="coment-div align-items-start attachment-activity" id="activity-main-div-${activity.activity_id}">
			        <div class="por-small" style="cursor: pointer;">
			            <span class="member-avatar ${colorClass}" title="${activity.user_name}">${nameAlphabet}</span>
			        </div>
			        <div class="comment-box attach-margin">
			            <div class="form-group mb-0">
			                <div class="container">
			                    <div class="activity-details">
			                        <div class="activityName">
                                        <span class="user-span">
                                            <h4 class="upd-name user-link">${activity.user_name}</h4>
                                        </span>
                                        <span class="attachment-span"> attached
                                            <a href="${activity.attachment_file_path ?? ''}" class="attachment-link" target="_blank" data-attachment-id="${activity.attachment_id}">
                                                ${activity.attachment_original_name}
                                            </a> to this card
                                        </span>
                                        <span class="time-span">
                                            <a href="javascript:void(0);" class="time-link" data-date="${activity.attachment_created_at}">
                                                ${activity.attachment_created_at}
                                            </a>
                                        </span>
                                    </div>
			                        <a href="${activity.attachment_file_path}" target="_blank" data-attachment-id="${activity.attachment_id}" class="image-link">
			                            <img class="activity-image" src="${activity.attachment_file_path}">
			                        </a><br>
<!--                                    <span class="reply-span">-->
<!--                                        <a href="javascript:;" class="reply-link">Reply</a>-->
<!--                                    </span>-->
			                    </div>
			                </div>
			            </div>
			        </div>
			    </div>
			`);
        }

        function createAttachmentElement(board_list_card_id, activity) {
            const attachmentDiv = $('<div class="attachment-div" id="attachment-main-div-' + activity.attachment_id + '" style="display: none"></div>');
            const thumbnailLink = $('<a class="attachment-thumbnail"></a>').attr('href', `${activity.attachment_file_path ?? ""}`);
            const attachmentDetails = $('<div class="attachment-thumbnail-detail"></div>');

            if (activity.attachment_mime_type) {
                if (activity.attachment_mime_type.startsWith('image/')) {
                    const img = $('<img src="" alt="image">');
                    const imgElement = $('<span class="att-name"></span>').append(img);
                    thumbnailLink.append(imgElement);
                    img.attr('src', activity.attachment_file_path).css('max-width', '100%');
                } else if (activity.attachment_mime_type.startsWith('text/')) {
                    const audioIcon = $('<span class="att-name"><i class="far fa-file-alt"></i></span>');
                    thumbnailLink.append(audioIcon);
                } else if (activity.attachment_mime_type.startsWith('audio/')) {
                    const audioIcon = $('<span class="att-name"><i class="fas fa-volume-up"></i></span>');
                    thumbnailLink.append(audioIcon);
                } else if (activity.attachment_mime_type.startsWith('video/')) {
                    const videoIcon = $('<span class="att-name"><i class="fas fa-video"></i></span>');
                    thumbnailLink.append(videoIcon);
                } else if (activity.attachment_mime_type.startsWith('application/')) {

                    if (activity.attachment_extension) {
                        if (activity.attachment_extension == 'pdf' || activity.attachment_mime_type === 'application/pdf') {
                            const pdfIcon = $('<span class="att-name"><i class="far fa-file-pdf"></i></span>');
                            thumbnailLink.append(pdfIcon);
                        } else if (activity.attachment_extension == 'doc' || activity.attachment_extension == 'docx' || activity.attachment_mime_type === 'application/msword' || activity.attachment_mime_type === 'application/vnd.openxmlformats-officedocument.wordprocessingml.document') {
                            const wordIcon = $('<span class="att-name"><i class="far fa-file-word"></i></span>');
                            thumbnailLink.append(wordIcon);
                        } else if (activity.attachment_extension == 'xls' || activity.attachment_extension == 'xlsx' || activity.attachment_mime_type === 'application/vnd.ms-excel' || activity.attachment_mime_type === 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet') {
                            const excelIcon = $('<span class="att-name"><i class="far fa-file-excel"></i></span>');
                            thumbnailLink.append(excelIcon);
                        } else if (activity.attachment_extension == 'ppt' || activity.attachment_extension == 'pptx' || activity.attachment_mime_type === 'application/vnd.ms-powerpoint' || activity.attachment_mime_type === 'application/vnd.openxmlformats-officedocument.presentationml.presentation') {
                            const powerpointIcon = $('<span class="att-name"><i class="far fa-file-powerpoint"></i></span>');
                            thumbnailLink.append(powerpointIcon);
                        } else if (activity.attachment_extension == 'zip' || activity.attachment_mime_type === 'application/zip') {
                            const powerpointIcon = $('<span class="att-name"><i class="far fa-file-archive"></i></span>');
                            thumbnailLink.append(powerpointIcon);
                        } else {
                            const docxElement = $(`<span class="att-name">file</span>`);
                            thumbnailLink.append(docxElement);
                        }
                    } else {
                        const paperclipIcon = $('<span class="att-name"><i class="fas fa-paperclip"></i></span>');
                        thumbnailLink.append(paperclipIcon);
                    }
                } else {
                    const docxElement = $(`<span class="att-name">${activity.attachment_extension ?? "file"}</span>`);
                    thumbnailLink.append(docxElement);
                }
            } else {
                const docxElement = $(`<span class="att-name">${activity.attachment_extension ?? "file"}</span>`);
                thumbnailLink.append(docxElement);
            }
            thumbnailLink.attr('target', '_blank');
            const fileNameElement = $('<h6 class="attachment-file-name"></h6>').text(activity.attachment_original_name);
            const timeElement = $('<span class="attatchment-time"></span>').text('Added ' + activity.attachment_created_at);


            // const commentLink = $('<span class="attatchment-link"></span>').html('<a href="javascript:void(0);">Comment</a>');
            const commentLink = '';
            const deleteLink = $('<span class="attatchment-link"></span>').html('<a href="javascript:void(0);" class="delete-attachment" data-id="' + activity.attachment_id + '" data-activity-id="' + activity.attachment_activity_id + '">Delete</a>');
            //
            // const editLink = $('<span class="attatchment-link"></span>').html('<a href="javascript:void(0);">Edit</a>');
            const editLink = '';

            attachmentDetails.append(fileNameElement, timeElement, commentLink, deleteLink, editLink);

            // if (activity.attachment_mime_type.startsWith('image/')) {
            //     const makeCoverLink = $('<div class="make-cover"><a href="#" target="_blank"><span class="material-symbols-outlined"> credit_card </span> Make Cover</a></div>'); // Example using a placeholder URL (#)
            //     attachmentDetails.append(makeCoverLink);
            // }
            attachmentDiv.append(thumbnailLink, attachmentDetails);
            return attachmentDiv;
        }

        function appendAttachment(attachmentElement) {
            $('.append-attachments').prepend(attachmentElement);
        }

        /** Prepend Activity Function == 2*/
        function prependModalActivity(board_list_card_id, user_name, activity) {
            /** If activity type is 0 (comment), append it as a comment */
            var nameAlphabet = $.trim(user_name).split(' ').map(function (word) {
                return word.charAt(0).toUpperCase();
            }).join('').substring(0, 2);
            /**First Letter*/
            var fnl = $.trim(user_name).charAt(0).toLowerCase();
            var colorClass = getColorAccordingToName(fnl);

            /**create comment prepand on save*/
            $('#modal-activities').prepend(
                `
				<div class="coment-div new-comments new-activity-move normal-activity" id="activity-main-div-${activity.activity_id}">
                    <div class="por-small" style="cursor: pointer;">
                        <span class="member-avatar ${colorClass}" title="${activity.user_name}">${nameAlphabet}</span>
                    </div>
                    <div class="comment-box">
                        <div class="form-group">
                            <div class="container">
                                <div class="show-activity" style="border-radius:10px;cursor:auto;" id="activity-para-${board_list_card_id}-${activity.activity_id}">
			                        <div class="upd-name-activity">
			                            <div class="upd-name-div">
                                            <h4 class="upd-name">${activity.user_name}</h4>
                                            <p class="upd-name-text mb-0">
											<span class="attachment-span">${activity.activity}
										    ${activity.activity_type == 1 ?
                    `<a href="${activity.attachment_file_path ?? ''}" class="attachment-link" data-attachment-id="${activity.attachment_id}"> ${activity.attachment_original_name} </a>${activity.activity == 'deleted' ? 'from' : 'to'} this card `
                    : ''}
											</span>
										 </p>
                                            <h5 class="upd-date">${activity.created_at}</h5>
                                        </div>

			                        </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>`
            );


        }

        function update_comment_count(board_list_card_id, count) {
            const title = count > 1 ? 'comments' : 'comment';
            $(`#board-list-card-${board_list_card_id} .comment-count`).text(count);
            $(`#board-list-card-${board_list_card_id} .comment-count-div`).attr('title', title);
        }

        function update_attachment_count_and_button(board_list_card_id, count) {
            let $button = $('.js-show-hide-attachments');
            const title = count > 1 ? 'attachments' : 'attachment';
            $(`#board-list-card-${board_list_card_id} .attachment-count`).text(count);
            $(`#board-list-card-${board_list_card_id} .attachment-count-div`).attr('title', title);

            if (count) {
                if (count > 5) {
                    $button.css('display', ' block');
                    $button.text(`View all attachments (${count - 5} hidden)`);
                    $('.attachment-div').hide().slice(0, 5).show();
                    $button.removeClass('show');

                    if ($('.attachment-div').length && $('.attachment-div').length < count) {
                        $button.addClass('show');
                    }
                } else {
                    $('.attachment-div').slideDown();
                    $button.css('display', 'none');
                }
            } else {
                $button.css('display', 'none');
            }
        }

        $('.modal-content').on('dragover', function (event) {
            event.preventDefault();
            $('#drop-zone').css('display', 'block');
        });

        $('#drop-zone').on('dragleave', function (event) {
            event.preventDefault();
            $('#drop-zone').css('display', 'none');
        });
        $('#modal-board-list-card-id').on('drop', function (event) {
            event.preventDefault();
            $('#drop-zone').css('display', 'none');
            const files = event.originalEvent.dataTransfer.files;
            let formData = new FormData();
            uploadFiles(files, formData);
        });

        function uploadFiles(files, formData) {

            let board_list_card_id = $('#modal-board-list-card-id').attr('data-modal-board-list-card-id');
            let url = '{{ route('user.board.list.card.add.attachment','/')}}/' + board_list_card_id;

            Array.from(files).forEach(function (file) {
                formData.append('attachments[]', file);
            });

            AjaxRequestPostPromise(url, formData, null, false, null, false, true, true, false)
                .then((res) => {
                    res.activities.forEach(function (activity) {
                        const attachmentDiv = createAttachmentElement(board_list_card_id, activity);
                        appendAttachment(attachmentDiv);
                        /** Append attachments to make cover image */
                        if (activity && activity.attachment_mime_type) {
                            attachmentsForCover(activity);
                        }
                        if (activity.attachment_mime_type && activity.attachment_mime_type.startsWith('image/')) {
                            prependModalActivityAttachment(activity);
                        } else if (activity.attachment_mime_type && !activity.attachment_mime_type.startsWith('image/')) {
                            prependModalActivity(board_list_card_id, activity.user_name, activity)
                        }
                    })
                    update_attachment_count_and_button(board_list_card_id, res.attachment_count);
                    $('.loading_div').css('display', 'none');
                })
                .catch((error) => {
                    console.error("Error in Ajax request:", error);

                    $('.loading_div').css('display', 'none');
                })
                .finally(() => {
                    loading_div.css('display', 'none');
                });
        }

        /** */
        $('.add-attachment').on('change', function () {
            let formData = new FormData();
            const fileInput = this;
            uploadFiles(fileInput.files, formData);
            $(this).val('');
        });

        /** delete user attachment*/
        $("#modal-board-list-card-id").on("click", ".delete-attachment", function () {
            var url = '{{ route('user.board.list.card.delete.attachment','/')}}/' + $(this).data('id');
            AjaxRequestDeletePromise(url, null, null, false, null, false, true, true)
                .then((res) => {
                    console.log('Attachment deleted successfully:', res);
                    let id = $(this).data('id');
                    let activity_id = $(this).data('activity-id');
                    $("#attachment-main-div-" + id).remove();
                    $("#activity-main-div-" + activity_id).remove();
                    $("#att-cover-image-" + id).remove();
                    loading_div.css('display', 'none');
                    prependModalActivity(res.board_list_card_id, res.user_name, res.activity)
                    update_attachment_count_and_button(res.board_list_card_id, res.attachment_count);
                    createToast('success', 'Attachment deleted successfully.');

                })
                .catch((error) => {
                    console.error("Error in Ajax request:", error);
                    loading_div.css('display', 'none');
                    createToast('error', 'Failed to delete attachment.');
                });
        })

        function getDominantColor(img) {
            const vibrant = new Vibrant(img);
            const swatch = vibrant.swatches()['Vibrant'] || vibrant.swatches()['Muted'] || vibrant.swatches()['DarkVibrant'];
            return swatch ? swatch.getHex() : '#FFFFFF';
        }

        // fetchBackgroundColor();
        function fetchBackgroundColor() {
            $('.image-container img').each(function () {
                var image = $(this);
                var imageUrl = $(this).attr('src');
                if (imageUrl) {
                    var img = new Image();
                    img.onload = function () {
                        image.css('backgroundColor', getDominantColor(img));
                    };
                    img.onerror = function () {
                        console.log('Image not found:', imageUrl);
                    };
                    img.src = imageUrl;
                }
            });
        }

        $(".app-card-add-button").click(function () {
            var board_list_id = $(this).data('board-list-id');

            $("#board-list-card-btn-" + board_list_id).hide();
            $("#add-card-input-" + board_list_id).show();
        });

        $(".add-card-close-btn").click(function () {
            var board_list_id = $(this).data('board-list-id');
            $("#add-card-input-" + board_list_id).hide();
            $("#board-list-card-btn-" + board_list_id).show();
        });

        $(document).on('submit', '#create_board_card_form', function (e) {
            e.preventDefault();

            var board_list_id = $(this).find('input[name="board_list_id"]').val();
            $("#add-card-input-" + board_list_id).hide();
            $("#board-list-card-btn-" + board_list_id).show();
            AjaxRequestPost('{{ route('user.board.list.card.store') }}', $(this).serialize(), 'Card created successfully!', true);
            $("#create_board_card_form")[0].reset();
        });

        function getUrlParameter(name) {
            name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
            var regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
            var results = regex.exec(location.search);
            return results === null ? window.location.href.split('?')[1] : decodeURIComponent(results[1].replace(/\+/g, ' '));
        }

        var modalBoardCardListId = getUrlParameter('modalBoardCardListId') || window.location.hash.substr(1);

        if (modalBoardCardListId !== '') {
            $('#edit-board-list-card-modal').modal('show');
            var url = '{{ route('decrypt','/')}}/' + modalBoardCardListId;
            loading_div.css('display', 'flex');
            AjaxRequestGetPromise(url, null, null, false, null, false)
                .then((res) => {
                    fetchCardModal(atob(res));
                })
                .catch((error) => {
                    console.error("Error in Ajax request:", error);
                    loading_div.css('display', 'none');
                });
        }
        /** Change url on modal close */
        $('#edit-board-list-card-modal').on('hidden.bs.modal', function () {
            // Remove the parameter from the URL
            var currentUrl = window.location.href;
            var newUrl = currentUrl.split('?')[0].split('#')[0];

            // Replace the current state in the browser's history
            history.replaceState(null, null, newUrl);
        });

        /** Append attachments to make cover image */
        function attachmentsForCover(attachment) {
            const attachmentsForCover = $('.attachments-for-cover');
            if (attachment.attachment_mime_type.startsWith('image/')) {
                const img = $('<img alt="image" class="att-cover-images">');
                img.attr('data-id', attachment.attachment_id)
                img.attr('id', 'att-cover-image-' + attachment.attachment_id)
                img.attr('src', attachment.attachment_file_path).css('max-width', '100%');
                attachmentsForCover.prepend(img);
            }
        }

        /** Modal-Fetch => Fetch Details for modal*/
        $(document).on('click', '.board-list-card', function () {

            $("#comment-input").show();
            $('.ck-div').hide();
            if (!$(event.target).closest('.do-not-target').length) {
                var board_list_card_id = $(this).data('board-list-card-id');
                $('#modal-board-list-card-id').attr('data-modal-board-list-card-id', board_list_card_id);
                if (!board_list_card_id) {
                    $('#modal-board-list-card-id').hide();
                }
                var url = '{{ route('encrypt','/')}}/' + btoa(board_list_card_id.toString());
                loading_div.css('display', 'flex');
                AjaxRequestGetPromise(url, null, null, false, null, false)
                    .then((res) => {
                        console.log(res);
                        var separator = window.location.href.includes('#') ? '#' : '?';
                        var newUrl = window.location.href.split(separator)[0] + separator + res;
                        history.pushState(null, null, newUrl);
                    })
                    .catch((error) => {
                        console.error("Error in Ajax request:", error);
                        loading_div.css('display', 'none');
                    });

                fetchCardModal(board_list_card_id);
            }
        });

        /** Modal-Fetch-Function => Fetch Card Modal Function */
        function addCardMember(userName, colorClass, nameAlphabet, cardMemberId) {
            if (typeof cardMemberId == 'undefined') {
                createToast('error', 'An unexpected error occurred. Please try again later.')
            }
            var cardMemberAssignment = $('.card-member-assignment');
            var cardItem = $(
                `<li class="remove-card-member-list assigned-card-members my-2" id="remove-card-member-list-${cardMemberId}" data-id="${cardMemberId}" data-title="${userName}">` +
                '<span class="' + colorClass + '">'
                + nameAlphabet +
                '</span>' +
                '<div class="membr-list-name">'
                + userName +
                '</div>' +
                '</li>'
            );
            cardMemberAssignment.append(cardItem);
        }

        /** Modal-Fetch-Function => Fetch Card Modal Function */

        function fetchCardModal(board_list_card_id) {

            $("#comment-input").show();
            $('.ck-div').hide();
            $('.ck-desc').hide();
            $(".label-dynamic-div").css("display", "none");
            $(".labeldropdown-inner").css("display", "none");
            $(".datepicker").datepicker("destroy");
            $(".datepicker").datepicker("setDate", null);
            $('.append-attachments').empty();
            $('#modal-activities').empty();
            $('#modal-board-list-card-id').attr('data-modal-board-list-card-id', board_list_card_id);
            initializeDatepicker();
            var url = '{{ route('user.board.list.card.edit','/')}}/' + board_list_card_id;
            loading_div.css('display', 'flex');
            AjaxRequestGetPromise(url, null, null, false, null, false)
                .then((res) => {
                    console.log(res);
                    if (res && res.status && res.status === 1 && res.board_list_card_id) {
                        var board_list_card_id = res.board_list_card_id;
                        $('#modal-board-list-card-id').attr('data-modal-board-list-card-id', board_list_card_id);
                        if (res.hasOwnProperty('comment_count')) {
                            update_comment_count(board_list_card_id, res.comment_count);
                        }
                        if (res.hasOwnProperty('attachment_count')) {
                            update_attachment_count_and_button(board_list_card_id, res.attachment_count);
                        }

                        // ['attachment', 'comment'].forEach(type => {
                        //     const count = res[`${type}_count`];
                        //     count && $(`#board-list-card-${res.board_list_card_id} .${type}-count-div`).attr('title', `${type}${count > 1 ? 's' : ''}`)
                        //         .siblings(`.${type}-count`).text(count);
                        // });

                        $('a#board-list-name').text(res.board_list_name);
                        $('#edit_title').val(res.title);

                        // $('#edit_board_list_card_id').val(board_list_card_id);
                        // $('#edit_project_id').selectpicker('val', res.project_id);
                        originalTitle = res.title;
                        autosize();
                        if (res.description) {
                            MyEditor["editor1"].setData(res.description);
                            $('.show-desc').empty().append(`${res.description}`).show();
                            $('.show-desc').show();
                            $('.hide-desc').hide();
                        } else {
                            $('.ck-desc,.show-desc').hide();
                            $('.hide-desc').show();
                        }

                        // $('#edit_assign_to').selectpicker('val', res.user_names);
                        // $('#edit_due_date').val(res.due_date);

                        /** Update Cover Image and Cover Images Size Images*/
                        const modalCoverImages = $('.modal-cover-image');
                        if (res.background_image && res.background_image_path) {
                                modalCoverImages.attr('src', res.background_image_path).css({
                                'display': 'block',
                                'visibility': 'visible'
                            });
                            var img = new Image();
                            img.onload = function () {
                                if (res.cover_background_color) {
                                    modalCoverImages.css('backgroundColor', res.cover_background_color);
                                } else {
                                    modalCoverImages.css('backgroundColor', getDominantColor(img));
                                }
                            }
                            if (res.cover_image_size == 1) {
                                $(".image-size-with-bg").css({'border': '3px solid #0c66e4'});
                            } else {
                                $(".image-size-without-bg").css({'border': '3px solid #0c66e4'});
                            }
                            img.src = modalCoverImages.attr('src');
                        } else {
                            modalCoverImages.attr('src', '').css({'display': 'none', 'visibility': 'hidden'});
                        }

                        /** Update Date */

                        if (res.is_check_start_date) {
                            $('#check-start-date').prop('checked', true);
                            $('#board-start-date').prop('disabled', false);
                            $('#board-start-date').val(res.start_date);
                            startDate = new Date(res.start_date); // Update startDate
                        } else {
                            startDate = null;
                            $('#board-start-date').val('');
                            $('#check-start-date').prop('checked', false);
                        }

                        if (res.is_check_due_date) {
                            $('#check-due-date').prop('checked', true);
                            $('#board-due-date').prop('disabled', false);
                            $('#board-due-time').prop('disabled', false);
                            const parsedDueDate = new Date(res.due_date);
                            $('#board-due-date').val(formatDateString(parsedDueDate));
                            $('#board-due-time').val(res.due_time);
                            endDate = parsedDueDate;
                        } else {
                            $('#check-due-date').prop('checked', false);
                            $('#board-due-date').prop('disabled', true);
                            $('#board-due-time').prop('disabled', true);
                            $('#board-due-date').val('');
                            $('#board-due-time').val('');
                        }

                        if (res.is_check_start_date && startDate) {
                            $(".datepicker").datepicker("setDate", startDate);
                        } else if (res.is_check_due_date && endDate) {
                            $(".datepicker").datepicker("setDate", endDate);
                        }

                        if (res.is_check_due_date && res.is_check_start_date && startDate && endDate && startDate >= endDate) {
                            startDate = new Date(endDate);
                            startDate.setDate(startDate.getDate() - 1);
                            $('#board-start-date').val(formatDateString(startDate));
                        }

                        initializeDatepicker();

                        /** End Update Date*/

                        /** Append assigned members on card */
                        assign_unassign_members_functionality(res);

                        // $('.remove-card-member-list').remove();
                        // var cardMembers = $('.card-members');
                        // /** Only Assigned Members Div */
                        // var targetLi = $('.card-member-list').find('.card-member-list-static');
                        // var authId = res.auth_id;
                        // var assignUsers = res.assign_users;
                        // var userIds = assignUsers.map(function (user) {
                        //     return user.id;
                        // });
                        // var joinOwnElement = $('.join-own');
                        // var authIdTrimmed = authId.slice(0, -3);
                        // var userIdsTrimmed = userIds.map(function (userId) {
                        //     return userId.slice(0, -3);
                        // });
                        // if (Array.isArray(userIds)) {
                        //     if (userIdsTrimmed.includes(authIdTrimmed)) {
                        //         joinOwnElement.addClass('d-none');
                        //     } else {
                        //         joinOwnElement.removeClass('d-none');
                        //     }
                        // } else {
                        //     joinOwnElement.removeClass('d-none');
                        // }
                        // let usCardListMember = $(`#us-card-list-member-${board_list_card_id}`);
                        // usCardListMember.empty();
                        // if (res.hasOwnProperty('assign_users')) {
                        //     res.assign_users.forEach(function (user) {
                        //         cardMembers.removeClass('d-none');
                        //
                        //         var userName = user.name;
                        //         var userId = user.id;
                        //         // For Two letters e.g Dev Michael = DM
                        //         var nameAlphabet = $.trim(userName).split(' ').map(function (word) {
                        //             return word.charAt(0).toUpperCase();
                        //         }).join('').substring(0, 2);
                        //         /**First Letter*/
                        //         var fnl = $.trim(userName).charAt(0).toLowerCase();
                        //         var colorClass = getColorAccordingToName(fnl);
                        //         /** Main Modal Assign Member Profiles*/
                        //         var listItem = $('<li class="remove-card-member-list"><div class="dropdown"><button class="btn btn-secondary dropdown-toggle ' + colorClass + '" type="button" data-toggle="dropdown" title="' + userName + '">' + nameAlphabet + '</button><div class="dropdown-menu profile-card"><div class="pchead"><div class="pc-name"><button class="close-btn-pop"><span class="material-symbols-outlined">close</span></button><div class="short-name"><span class="' + colorClass + '">' + nameAlphabet + '</span></div><div class="short-detail"><h3>' + userName + '</h3><h4>@' + userName.toLowerCase().replace(/\s+/g, '') + '</h4></div></div></div><a href="javascript:void(0);" class="view-profile">View Profile</a><div class="pcfoot"><a href="javascript:void(0);" class="view-profile">Remove from card</a></div></div></div></li>');
                        //         addCardMember(userName, colorClass, nameAlphabet, userId);
                        //
                        //         var htmlContent = `
                        //             <li class="member-card-overlay dropdown">
                        //                 <a href="javascript:void(0);" class="btn btn-secondary dropdown-toggle ${colorClass}" type="button" id="board-card-list-assign-members" title="${userName}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        //                     <p id="board-list-members-${board_list_card_id}">
                        //                         ${nameAlphabet}
                        //                     </p>
                        //                 </a>
                        //                 <div class="dropdown-menu profile-card member-outer" aria-labelledby="board-card-list-assign-members">
                        //                     <div class="pchead">
                        //                         <div class="pc-name">
                        //                             <button class="close-btn-pop">
                        //                                 <span class="material-symbols-outlined">close</span>
                        //                             </button>
                        //                             <div class="short-name">
                        //                                 <span class="${colorClass}">${nameAlphabet}</span>
                        //                             </div>
                        //                             <div class="short-detail">
                        //                                 <h3>${userName}</h3>
                        //                                 <h4>${userName}</h4>
                        //                             </div>
                        //                         </div>
                        //                     </div>
                        //                     <a href="javascript:void(0);" class="view-profile view-pro-out">View Profile</a>
                        //                     <div class="pcfoot">
                        //                         <a href="javascript:void(0);" class="view-profile view-pro-out">Remove from card</a>
                        //                     </div>
                        //                 </div>
                        //             </li>
                        //         `;
                        //
                        //         listItem.insertBefore(targetLi);
                        //         usCardListMember.append(htmlContent);
                        //         // $('.join-own').hide().fadeIn();
                        //     });
                        // }
                        //
                        // $('li.assign-unassign-members').removeClass('tickmark');
                        // userIds.forEach((user_id) => {
                        //     $('li.assign-unassign-members').each(function () {
                        //         if ($(this).attr('data-id').slice(0, -3) === user_id.slice(0, -3)) {
                        //             $(this).addClass('tickmark');
                        //         }
                        //     });
                        // })

                        $('.attachments-for-cover').empty();

                        /** Append Attachments to Card Attachment Section*/
                        $('.append-attachments').empty();
                        res.attachments.forEach((attachment, index) => {
                            const attachmentElement = createAttachmentElement(board_list_card_id, attachment);
                            appendAttachment(attachmentElement);

                            /** Append attachments to make cover image */
                            attachmentsForCover(attachment);
                        });
                        $('.attachment-div').hide().slice(0, 5).show();
                        $('.js-show-hide-attachments').addClass('show');
                        show_hide_attahments();

                        /** Append Activities to Activities Section */
                        $('#modal-activities').empty();
                        res.activities.forEach((activity, index) => {
                            /** If activity type is 0 (comment), append it as comments */
                            if (activity.activity_type === 0) {
                                if (activity.comment_id) {
                                    prependModalActivityComment(board_list_card_id, activity.user_name, activity);
                                }
                                /** If activity type is 1 (attachment), append it as attachments */
                            } else if (activity.activity_type === 1 && activity.attachment_mime_type && activity.attachment_mime_type.startsWith('image/')) {
                                prependModalActivityAttachment(activity);
                            } else {
                                /** If activity type is 2 or else (activity), append it as activities */
                                prependModalActivity(board_list_card_id, activity.user_name, activity)
                            }
                        });

                        document.querySelectorAll('.CommentckEditor').forEach((node, index) => {
                            createCKEditor(node, index);
                        });

                        /** Label for card modal */
                        var assign_labels = res.assign_labels;
                        var label_id = [];
                        if (assign_labels && assign_labels.length > 0) {
                            var selectedLabels = [];
                            var labelClasses = [];

                            for (var i = 0; i < assign_labels.length; i++) {
                                var assign_label_data = assign_labels[i];

                                $('.label-list').removeClass('d-none');

                                var labelClass = assign_label_data.label.color.color_value;
                                var labelText = assign_label_data.label.label_text;
                                label_id.push(assign_label_data.label.id);

                                var newLabel = $(`<label class="form-check-label label-bg-color"  id="form-check-assign-label-${assign_label_data.label.id}" data-label-id="${assign_label_data.label.id}" style="background-color: ${labelClass}"></label>`).text(labelText);
                                selectedLabels.push(newLabel);
                                labelClasses.push(labelClass);

                                if (selectedLabels.length > 0) {
                                    $('.button-label').empty().append(selectedLabels);
                                    $('.label-icon-btn').removeClass('d-none');
                                    $('.label-heading').removeClass('d-none');
                                } else {
                                    $('.button-label').empty();
                                    $('.label-icon-btn').addClass('d-none');
                                    $('.label-heading').addClass('d-none');
                                }

                            }
                        } else {
                            // console.log('empty-labels', labels)
                            $('.label-list').addClass('d-none');
                            loading_div.css('display', 'none');
                        }

                        /*$('.label-input').on('change', function () {
                            var checkedInputs = {};

                            $('.label-input:checked').each(function () {
                                var label = $(this).siblings('label').text();
                                var colorId = $(this).siblings('label').attr('data-color-id');

                                if (checkedInputs[label] && checkedInputs[label] === colorId) {
                                    $(this).parent().remove();
                                } else {
                                    checkedInputs[label] = colorId;
                                }
                            });
                        });*/

                        $('.show-more-label').attr('data-board-card-id', board_list_card_id).addClass('d-none');

                        $(document).on('click', '.show-btn-label', function () {
                            var more_labels = $('.show-more-label');
                            more_labels.removeClass('d-none');
                            var board_list_card_id = more_labels.attr('data-board-card-id');

                            $('.show-more-label[data-card-id="' + board_list_card_id + '"]').toggleClass('d-none');
                        });

                        $('.card-label').attr('data-board-card-id', board_list_card_id);
                        // $('.form-check').each(function (index) {
                        //     $(this).attr('data-board-list-card-id', res.board_list_card_id);
                        // });

                        /***/
                        var labels_data = res.label_data;
                        $('.dynamic-checkbox[data-board-list-card-id="' + board_list_card_id + '"]').remove();

                        var labelsData = $('.labels-data');
                        var showMoreLabel = $('.show-more-label');
                        labelsData.empty();
                        showMoreLabel.empty();

                        if (labels_data && labels_data.length > 0) {
                            labels_data.forEach((label_data, index) => {
                                var assignLabelChecked = label_id.includes(label_data.id) ? 'checked' : "";
                                var htmlContent = $(
                                    `<div class="form-check" data-board-list-card-id="${label_data.board_list_card_id}" data-color-position="${(label_data.color && label_data.color.color_position) ? label_data.color.color_position : 0}"  data-label-id="${label_data.id}" id="label-div-${label_data.board_list_card_id}-${label_data.id}">
                                        <input type="checkbox" data-board-list-card-id="${label_data.board_list_card_id}" data-label-id="${label_data.id}"
                                               class="form-check-label form-check-input label-input"
                                               id="labelCheck-${label_data.board_list_card_id}-${label_data.id}" ${assignLabelChecked}>
                                        <label class="form-check-label" data-color-position="${(label_data.color && label_data.color.color_position) ? label_data.color.color_position : 0}"
                                               data-color-id="${label_data.color_id}"
                                               data-label-id="${label_data.id}"
                                               aria-label="Color:"
                                               for="labelCheck-${label_data.board_list_card_id}-${label_data.id}"
                                               style="background-color: ${(label_data.color && label_data.color.color_value) ? label_data.color.color_value : '#091e420f'}">${label_data.label_text}</label>
                                        <button type="button" class="show-btn dynamiclabelEditBtn" onClick="openlabelDiv()" data-target="popover-new"><i class="far fa-edit"></i></button>
                                    </div>`
                                );
                                if (index < 6) {
                                    labelsData.append(htmlContent);
                                } else {
                                    showMoreLabel.append(htmlContent);
                                }
                            });
                        }
                    } else {
                        console.error("Invalid response format or status is not 1");
                    }
                    $(".show-comment").show();
                    $('.ck-comment-div').hide();
                    loading_div.css('display', 'none');
                })
                .catch((error) => {
                    console.error("Error in Ajax request:", error);
                    loading_div.css('display', 'none');
                });
        }

        $('.activity-detail').on('click', function () {
            var $button = $(this);
            if (!$button.hasClass('show')) {
                $button.text('Hide Details');
                $('.normal-activity,.attachment-activity').show();
            } else {
                $button.text('Show Details');
                $('.normal-activity,.attachment-activity').hide();
            }
            $button.toggleClass('show')
        });

        $('.js-show-hide-attachments').on('click', function () {
            show_hide_attahments();
        });

        function show_hide_attahments() {
            $('.attachment-div:gt(4)').slideUp();
            var $button = $('.js-show-hide-attachments');
            let attachment_length = $('.attachment-div').length;
            if (attachment_length && attachment_length > 5) {
                $button.css('display', 'block');
                if (!$button.hasClass('show')) {
                    $button.text('Show fewer attachments.');
                    $('.attachment-div').slideDown();
                    $button.addClass('show');
                } else {
                    $button.text(`View all attachments (${attachment_length - 5} hidden)`);
                    $('.attachment-div').hide().slice(0, 5).show();
                    $button.removeClass('show');
                }
            } else {
                $button.css('display', 'none');
            }

        }

        /** Modal-Dates-Save-Function => Save Board Start & Due Dates */
        $(document).on('click', '#board-dates-save-btn', function () {
            /** Retrieve checkbox elements and date input elements */
            let check_start_date = $('#check-start-date');
            let check_due_date = $('#check-due-date');
            let board_start_date = $('#board-start-date');
            let board_due_date = $('#board-due-date');
            let board_due_time = $('#board-due-time');
            /** Check if start date and due date checkboxes are checked */
            if (!check_start_date.prop('checked') && !check_due_date.prop('checked')) {
                /** If both checkboxes are unchecked, do nothing */
                return false;
            }

            /** Initializing variable for board list card id */
            const board_list_card_id = $('#modal-board-list-card-id').attr('data-modal-board-list-card-id');

            /** Create FormData to append data for AJAX request */
            const formData = new FormData();
            formData.append("board_list_card_id", board_list_card_id)

            /** Append is_check_start_date and is_check_due_date values */
            formData.append("is_check_start_date", check_start_date.prop('checked') === true ? 1 : 0);
            formData.append("is_check_due_date", check_due_date.prop('checked') == true ? 1 : 0);

            /** Check if only due date checkbox is checked */
            if (!check_start_date.prop('checked') && check_due_date.prop('checked') && isValidDateFormat(board_due_date.val(), board_due_time.val())) {
                /** Append due date and due time to FormData */
                formData.append("due_date", board_due_date.val());
                formData.append("due_time", board_due_time.val());
            }

            /** Check if both start date and due date checkboxes are checked */
            if (check_start_date.prop('checked') && check_due_date.prop('checked') && isValidDateFormat(board_due_date.val(), board_due_time.val())) {
                /** Append due date, and due time to FormData */
                formData.append("due_date", board_due_date.val());
                formData.append("due_time", board_due_time.val());

                /** Check if start date is not greater than due date */
                if (isValidDateFormat(board_start_date.val()) && new Date(board_start_date.val()) < new Date(board_due_date.val())) {
                    /** Append start date to FormData */
                    formData.append("start_date", board_start_date.val());
                }
            }

            /** Perform AJAX request to save the dates */
            var url = '{{ route('user.board.list.card.update.dates') }}';

            AjaxRequestPostPromise(url, formData, 'Dates saved successfully!', false, null, false)
                .then((res) => {
                    /** Handle success, if needed */

                    var usCardListDetail = $(`#us-card-list-detail-${board_list_card_id}`);
                    var htmlContent = $(
                        '<li id="board-list-date-data-' + board_list_card_id + '">' +
                        '<a href="javascript:void(0);" class="ddt-list-clock">' +
                        '<svg class="svg-inline--fa fa-clock" aria-hidden="true" focusable="false" data-prefix="far" data-icon="clock" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" data-fa-i2svg=""><path fill="currentColor" d="M464 256A208 208 0 1 1 48 256a208 208 0 1 1 416 0zM0 256a256 256 0 1 0 512 0A256 256 0 1 0 0 256zM232 120V256c0 8 4 15.5 10.7 20l96 64c11 7.4 25.9 4.4 33.3-6.7s4.4-25.9-6.7-33.3L280 243.2V120c0-13.3-10.7-24-24-24s-24 10.7-24 24z"></path></svg><!-- <i class="far fa-clock"></i> Font Awesome fontawesome.com -->' +
                        '<p id="board-list-date-' + board_list_card_id + '">' + res.data.formatted_date + '</p>' +
                        '</a>' +
                        '</li>'
                    );

                    htmlContent.removeClass('d-none');
                    usCardListDetail.prepend(htmlContent);

                    loading_div.css('display', 'none');
                })
                .catch((error) => {
                    console.error("Error in Ajax request:", error);
                    loading_div.css('display', 'none');
                })
                .finally(() => {
                    loading_div.css('display', 'none');
                })
        });

        function isValidDateFormat(date, time) {
            const dateFormatRegex = /^\d{1,2}\/\d{1,2}\/\d{4}$/;
            const timeFormatRegex = /^(0?[1-9]|1[0-2]):[0-5][0-9] (AM|PM)$/;
            if (date && !dateFormatRegex.test(date)) {
                console.error("Invalid date format:", date);
                return false;
            }
            if (time && !timeFormatRegex.test(time)) {
                console.error("Invalid time format:", time);
                return false;
            }
            return true;
        }

        $(document).on('click', '#board-dates-remove-btn', function () {
            /** Retrieve checkbox elements and date input elements */
            let check_start_date = $('#check-start-date');
            let check_due_date = $('#check-due-date');

            /** Initializing variable for board list card id */
            const board_list_card_id = $('#modal-board-list-card-id').attr('data-modal-board-list-card-id');

            /** Create FormData to append data for AJAX request */
            const formData = new FormData();
            formData.append("board_list_card_id", board_list_card_id)

            /** Append is_check_start_date and is_check_due_date values */
            formData.append("is_check_start_date", check_start_date.prop('checked') === false ? 0 : 1);
            formData.append("is_check_due_date", check_due_date.prop('checked') == false ? 0 : 1);

            /** Perform AJAX request to save the dates */
            var url = '{{ route('user.board.list.card.remove.dates') }}';
            AjaxRequestPostPromise(url, formData, 'Dates remove successfully!', false, null, false)
                .then((res) => {
                    /** Handle success, if needed */
                    console.log(res);

                    if (res.data === null) {
                        $(`#board-list-date-data-${board_list_card_id}`).addClass('d-none');
                    }

                    loading_div.css('display', 'none');
                })
                .catch((error) => {
                    console.error("Error in Ajax request:", error);
                    loading_div.css('display', 'none');
                })
                .finally(() => {
                    loading_div.css('display', 'none');
                })
        });

        /** Make attachments as cover image*/
        $('.attachments-for-cover').on('click', '.att-cover-images', function () {
            var board_list_card_id = $('#modal-board-list-card-id').attr('data-modal-board-list-card-id');

            var url = '{{ route('user.board.list.card.set.attachment.cover.image')}}';
            var formData = new FormData();
            formData.append("board_list_card_id", board_list_card_id)
            formData.append("attachment_id", $(this).attr('data-id'))
            AjaxRequestPostPromise(url, formData, 'Title update successfully!', false, null, false)
                .then((res) => {
                    const modalCoverImages = $('.modal-cover-image');
                    const image1 = $('#cover-image-' + board_list_card_id);
                    if (res.background_image && res.background_image != null) {

                        const img = modalCoverImages[0];
                        if (!modalCoverImages.attr('src')) {
                            modalCoverImages.attr('src', res.background_image);
                        }
                        modalCoverImages.css({
                            'display': 'block',
                            'visibility': 'visible',
                        });
                        let bgColor = getDominantColor(img);
                        image1.attr('src', res.background_image).css({'display': 'block'});

                        modalCoverImages.attr('src', res.background_image).css({
                            'backgroundColor': bgColor
                        });

                        updateBackgroundColor(bgColor);

                    } else {
                        modalCoverImages.attr('src', '').css({'display': 'none', 'visibility': 'hidden'});
                    }
                })
                .catch((error) => {
                    console.error("Error in Ajax request:", error);
                })
                .finally(() => {
                    loading_div.css('display', 'none');
                })
        });

        /** Title-Functionality => Update Title on focus out*/
        edit_title.on('blur', function () {
            var newTitle = $(this).val();
            if (newTitle !== originalTitle) {
                var board_list_card_id = $('#modal-board-list-card-id').attr('data-modal-board-list-card-id');
                var url = '{{ route('user.board.list.card.title.update')}}';
                var formData = new FormData();
                formData.append("board_list_card_id", board_list_card_id)
                formData.append("title", $('#edit_title').val())
                AjaxRequestPostPromise(url, formData, 'Title update successfully!', false, null, false)
                    .then((res) => {
                        console.log('res-title', res)
                        $(`#card-title-${board_list_card_id}`).val(res.title).text(res.title);
                        $('#edit_title').val(res.title);
                        originalTitle = res.title;
                    })
                    .catch((error) => {
                        console.error("Error in Ajax request:", error);
                    })
                    .finally(() => {
                        loading_div.css('display', 'none');
                    })
            }
        });

        /** Assign-Member-Functionality => On Click Assign Unassign Member in member dropdown (Assigned / Unassigned Member) and Update Card Assigned Members by appending and removing them.
         *  Note => Just need to send id from back it will automatically identify */
        $(document).on('click', '.assign-unassign-members, .assigned-card-members , .assign-own-members', function (e) {
            e.preventDefault();
            assign_unassign_members_ajax($(this));
        });

        function assign_unassign_members_functionality(res) {
            var board_list_card_id = res.board_list_card_id;

            /** To remove div there is no need to add board list card id */
            var cardMembers = $('.card-members');
            /** Only Assigned Members Div (Modal) */

            $('.remove-card-member-list').remove();
            /**  Removed All assigned members from modal and modal drop down to append updated list  */

            var targetLi = $('.card-member-list').find('.card-member-list-static');/** Plus icon to assign/unassign member (Modal) */


            /** Now we will check that auth id exists in assigned users if exists hide to join card div*/
            $('.join-own').toggleClass('d-none', res.all_users.some(user => user.id.slice(0, -3) === res?.auth_id?.slice(0, -3) && user.assigned));

            let usCardListMember = $(`#us-card-list-member-${board_list_card_id}`).empty();

            let boardMembersList = $("ul.board-members-list").empty();
            if (res.hasOwnProperty('all_users')) {
                res.all_users.forEach(function (user) {
                    const {name: userName, id: userId, assigned} = user;

                    // For Two letters e.g Dev Michael = DM
                    let nameAlphabet = userName.split(' ').map(word => word[0].toUpperCase()).join('').substring(0, 2);
                    /**First Letter*/
                    let fnl = $.trim(userName).charAt(0).toLowerCase();
                    let colorClass = getColorAccordingToName(fnl);

                    let htmlboardMembersList = `
						<li class="assign-unassign-members" id="assign-member-${userId}" data-id="${userId}" data-title="${userName}">
                        <span class="member-avatar ${colorClass}" title="${userName}">${nameAlphabet}</span>
							<div class="membr-list-name">${userName}</div>
						</li>`;
                    boardMembersList.append(htmlboardMembersList); /** It will be added in all member dropdown card */

                    if (assigned) {

                        cardMembers.removeClass('d-none');
                        let listItem = $(`
                            <li class="remove-card-member-list">
                                <div class="dropdown">
                                    <button class="btn btn-secondary dropdown-toggle ${colorClass}" type="button" data-toggle="dropdown" title="${userName}">
                                        ${nameAlphabet}
                                    </button>
                                    <div class="dropdown-menu profile-card">
                                        <div class="pchead">
                                            <div class="pc-name">
                                                <button class="close-btn-pop">
                                                    <span class="material-symbols-outlined">close</span>
                                                </button>
                                                <div class="short-name">
                                                    <span class="${colorClass}">${nameAlphabet}</span>
                                                </div>
                                                <div class="short-detail">
                                                    <h3>${userName}</h3>
                                                    <h4>@${userName.toLowerCase().replace(/\s+/g, '')}</h4>
                                                </div>
                                            </div>
                                        </div>
                                        <a href="javascript:void(0);" class="view-profile">View Profile</a>
                                        <div class="pcfoot">
                                            <a href="javascript:void(0);" class="view-profile">Remove from card</a>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        `);
                        listItem.insertBefore(targetLi);
                        /** It will be added in modal*/

                        let htmlContent = `
                                            <li class="member-card-overlay dropdown">
                                                <a href="javascript:void(0);" class="btn btn-secondary dropdown-toggle ${colorClass}" type="button" id="dropdown" title="${userName}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <p id="board-list-members-${board_list_card_id}">
                                                        ${nameAlphabet}
                                                    </p>
                                                </a>
                                                <div class="dropdown-menu profile-card member-outer" aria-labelledby="dropdown">
                                                    <div class="pchead">
                                                        <div class="pc-name">
                                                            <button class="close-btn-pop">
                                                                <span class="material-symbols-outlined">close</span>
                                                            </button>
                                                            <div class="short-name">
                                                                <span class="${colorClass}">${nameAlphabet}</span>
                                                            </div>
                                                            <div class="short-detail">
                                                                <h3>${userName}</h3>
                                                                <h4>${userName}</h4>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <a href="javascript:void(0);" class="view-profile view-pro-out">View Profile</a>
                                                    <div class="pcfoot">
                                                        <a href="javascript:void(0);" class="view-profile view-pro-out">Remove from card</a>
                                                    </div>
                                                </div>
                                            </li>
                                        `;
                        usCardListMember.append(htmlContent); /** It will be added in main card */

                        addCardMember(userName, colorClass, nameAlphabet, userId); /** Adding assigned member in assigned member dropdown card members*/
                    }
                });
            }
            const assignedUserIds = res.all_users.filter(user => user.assigned).map(user => user.id.slice(0, -3));

            /** To manage tick marks */
            $('li.assign-unassign-members').removeClass('tickmark').filter(function () {
                let memberId = $(this).data('id');
                let sliceId = memberId.toString().slice(0, -3);
                return assignedUserIds.includes(sliceId);
            }).addClass('tickmark');
        }

        function assign_unassign_members_ajax(field) {
            let board_list_card_id = $('#modal-board-list-card-id').attr('data-modal-board-list-card-id');
            const url = '{{ route('user.board.list.card.assign.unassign.member','/')}}/' + board_list_card_id;
            const formData = new FormData();
            formData.append("value", field.attr('data-id'))

            AjaxRequestPostPromise(url, formData, null, false, null, false, true, true, false)
                .then((res) => {
                    assign_unassign_members_functionality(res);
                })
                .catch((error) => {
                    console.log(error)
                })
                .finally(() => {
                    loading_div.css('display', 'none');
                });
        }

        /** Label Assign Unassign => label selection to store &7 label selection to remove */
        $(document).on('change', '.label-input', function () {
            var labelText = [];
            var selectedLabels = [];
            var labelClasses = [];

            var labelInput = $(this);
            var isChecked = labelInput.is(':checked');
            var labelId = labelInput.attr('data-label-id');

            $('.label-list').removeClass('d-none');

            var assignUrl = '{{ route('user.board.list.label.assign_unassign','/')}}/' + $('#modal-board-list-card-id').attr('data-modal-board-list-card-id');
            var assignValue = {label_id: labelId, is_checked: isChecked};
            var res = AjaxRequestPost(assignUrl, assignValue, 'Label assigned successfully!', false, null, false);

            if (res && res.status && res.status === 1) {
                var [toastType, message, checked] = res.assign === 1 ? ['success', 'Assign label successfully!', true] : ['warning', 'Un-assign label successfully!', false];
                $(`#labelCheck-${res.board_list_card_id}-${labelId}`).prop('checked', checked);

                if (res.success) {
                    message = res.success;
                    toastType = 'success';
                }

                if (res.assign === 1) {
                    createToast(toastType, message);

                    var labelClass = res.label_data.label.color.color_value;
                    labelText = res.label_data.label.label_text;
                    var label_id = res.label_data.label.id;
                    var newLabel = $(`<label class="form-check-label label-bg-color" data-label-id="${label_id}" style="background-color: ${labelClass}" id="form-check-assign-label-${label_id}"></label>`).text(labelText);
                    selectedLabels.push(newLabel);
                    labelClasses.push(labelClass);

                    if (selectedLabels.length > 0) {
                        $('.button-label').append(selectedLabels);
                        $('.label-icon-btn').removeClass('d-none');
                        $('.label-heading').removeClass('d-none');
                    } else {
                        $('.button-label').empty();
                        $('.label-icon-btn').addClass('d-none');
                        $('.label-heading').addClass('d-none');
                    }
                } else {
                    $('.button-label').find(`label[data-label-id="${labelId}"]`).remove();
                    if ($('.button-label').children('label').length === 0) {
                        $('.label-list').addClass('d-none');
                    }
                }
            } else {
                createToast('error', 'Failed to update label');
            }
        });
        /** Label-remove => label selection remove */

        /** create-Label => creation label*/
        var colorId = [];
        var color = [];

        $(document).on('input', '.create-label-text', function () {
            var maxLength = 30;
            var title = $('.create-label-text').val();

            if (title.length > maxLength) {
                title = title.substring(0, maxLength) + '...';
            }

            $('.store-label-text').empty().text(title);
        });

        $(document).on('click', '.dynamiclabelBtn', function () {
            $('.store-label-text').empty().css('background-color', '').attr('data-color-id', 21);
            $('.create-label-text').val('');
        });

        $(document).on('click', '.color-select', function () {
            color = $(this).css('background-color');
            colorId = $(this).attr('data-color-id');
            $('.store-label-text')
                // .empty()
                .css('background-color', color)
                .attr('data-color-id', colorId);
        });

        $(document).on('click', '.remove-color', function () {
            $('.store-label-text').css('background-color', '');
            $('.store-label-text').attr('data-color-id', 21);
        });

        $(document).on('click', '.create-label', function () {
            var label_dynamic_div = $('.label-dynamic-div');
            label_dynamic_div.removeClass('d-none');
            $(".return-move-member").click()
            var storeLabel = $('.store-label-text').first();
            var text = storeLabel.text();
            var color_id = storeLabel.attr('data-color-id');
            var label_id = storeLabel.attr('data-label-id') ?? null;

            if (text.trim().length === 0) {
                createToast('error', 'Label text cannot be empty.');
                return false;
            }

            var url = '{{ route('user.board.list.label.create','/') }}/' + $('#modal-board-list-card-id').attr('data-modal-board-list-card-id');
            var formData = new FormData();
            formData.append('label_text', text);
            formData.append('label_color_id', color_id ?? 21);
            formData.append('label_id', label_id ?? null);
            loading_div.css('display', 'flex');

            AjaxRequestPostPromise(url, formData, null, false, null, false, true, true, false)
                .then((res) => {
                    if (res.status && res.status === 1 && res.success) {

                        if (res.label_id && res.label_id > 0 && res.record == "updated") {
                            $(`#label-div-${res.board_list_card_id}-${res.label_id}`).remove();
                        }
                        var insertIndex = 0;
                        var newLabel = $(`<div class="form-check" data-board-list-card-id="${res.board_list_card_id}" data-color-position="${(res.color_position) ? res.color_position : 0}"  data-label-id="${res.label_id}" id="label-div-${res.board_list_card_id}-${res.label_id}">
                                <input type="checkbox" data-board-list-card-id="${res.board_list_card_id}" data-label-id="${res.label_id}" class="form-check-label form-check-input label-input" id="labelCheck-${res.board_list_card_id}-${res.label_id}" ${res.checked}>
                                <label class="form-check-label" data-color-position="${(res.color_position) ? res.color_position : 0}" data-color-id="${(res.color_id) ? res.color_id : 21}" data-label-id="${res.label_id}" aria-label="Color:" for="labelCheck-${res.board_list_card_id}-${res.label_id}" style="background-color: ${res.color_value ?? null}">${res.label_text ?? null}</label>
                                <button type="button" class="show-btn dynamiclabelEditBtn" onClick="openlabelDiv()" data-target="popover-new"><i class="far fa-edit"></i></button>
                            </div>`);
                        if (res.color_position.includes('_')) {
                            var parts = res.color_position.split('_');
                            var mainPosition = parseFloat(parts[0]);
                            var subPosition = parseFloat(parts[1]);
                        }

                        if ($('.labels-data .form-check').length > 0) {
                            var prevcurrentPositionNumber = 0;
                            $('.labels-data .form-check').each(function (index) {
                                var currentPosition = $(this).find('label').data('color-position');
                                var currentPositionNumber = parseFloat(currentPosition);
                                var resColorPositionNumber = parseFloat(res.color_position.includes('_') ? res.color_position.replace('_', '.') : res.color_position);
                                if (!isNaN(currentPositionNumber) && currentPositionNumber <= resColorPositionNumber) {
                                    insertIndex = index;
                                }
                            });
                        }

                        var formCheckToMove = $('.labels-data .form-check input:not(:checked):last').closest('.form-check');

                        if (res.color_id && res.color_id === 21 && (!res.label_id || (res.label_id && res.label_id < 1))) {
                            $('.labels-data').prepend(newLabel);
                            $('.show-more-label').prepend(formCheckToMove);
                        } else {
                            if (insertIndex < $('.form-check').length) {
                                if (!res.label_id || (res.label_id && res.label_id < 1)) {
                                    $('.show-more-label').prepend(formCheckToMove);
                                }
                                if (subPosition && subPosition === 2) {
                                    $('.labels-data').find('.form-check').eq(insertIndex).after(newLabel);
                                } else {
                                    let form_pos = $('.labels-data').find('.form-check').find('label').eq(insertIndex).attr('data-color-position');
                                    if (parseFloat(form_pos) && parseFloat(form_pos) > mainPosition) {
                                        $('.labels-data').find('.form-check').eq(insertIndex).after(newLabel);
                                    } else {
                                        $('.labels-data').find('.form-check').eq(insertIndex).before(newLabel);
                                    }
                                }
                            } else {
                                $('.show-more-label').append(newLabel);
                            }
                        }
                        if (res.label_id && res.label_id > 0 && res.record == "updated" && res.checked == 'checked') {
                            $(`#form-check-assign-label-${res.label_id}`).text(res.label_text).css('background-color', `#${res.color_value}`);
                        }
                        $(".create-label-text").val('');
                        $('.store-label-text').text('').css('background-color', '').attr('data-color-id', 21);
                        createToast('success', res.success);
                    } else {
                        $(".dynamiclabelBtn").click();
                        createToast('error', 'Failed to manage label in board card record');
                    }
                })
                .catch((error) => {
                    $(".dynamiclabelBtn").click();
                    createToast('error', error);
                })
                .finally(() => {
                    loading_div.css('display', 'none');
                });
        })

        $(document).on('click', '.dynamiclabelEditBtn', function () {
            let label = $(this).closest('.form-check').find('label');
            if (label.length > 0) {
                $(".create-label-text").val(label.text());
                let storeLabel = $(".store-label-text")
                storeLabel.text(label.text());

                $('.store-label-text').css('background-color', label.css('background-color'));
                $('.store-label-text').attr('data-color-id', label.attr('data-color-id'));
                $('.store-label-text').attr('data-label-id', label.attr('data-label-id'));

                $('.labeldropdown-inner').removeClass('show');
                var target = 'popover-new';
                var bodyDiv = document.querySelector('.label-dynamic-div');
                var targetDiv = document.querySelector('.' + target);
                bodyDiv.innerHTML = '';
                var clonedDiv = targetDiv.cloneNode(true);
                clonedDiv.style.display = "block";
                bodyDiv.appendChild(clonedDiv);
            } else {
                $(".return-move-member").click()
                createToast('error', 'Oops! Value not found');
            }
        })

        /** Description-Edit => Fetch Description and show CkEditor */
        $(document).on('click', '.show-desc, .hide-desc', function () {
            loading_div.css('display', 'flex');

            // var board_list_card_id = $('#modal-board-list-card-id').attr('data-modal-board-list-card-id');

            $('.ck-desc').show();
            $('.hide-desc,.show-desc').hide();

            {{--var url = '{{ route('user.board.list.card.get.description','/')}}/' + board_list_card_id;--}}
            {{--var res = AjaxRequestGet(url, null, null, false, null, false);--}}
            {{--if (res && res.description) {--}}
            //     MyEditor["editor1"].setData(res.description);
            const updatedContent = MyEditor["editor1"].getData();
            updateSaveButtonState(updatedContent, '#ck-save-button1');
            // }
            loading_div.css('display', 'none');
        })

        /** Description-Update-CkEditor => On change Description CkEditor update Description CkEditor data*/
        MyEditor["editor1"].model.document.on('change:data', () => {
            const updatedContent = MyEditor["editor1"].getData();
            updateSaveButtonState(updatedContent, '#ck-save-button1');
        });

        /** Description-Save => On Save Description Send Description Data to save and update Description CkEditor and Paragraph  */
        $(document).on('click', '#ck-save-button1', function () {
            var board_list_card_id = $('#modal-board-list-card-id').attr('data-modal-board-list-card-id');

            if (!board_list_card_id || !$.isNumeric(board_list_card_id)) {
                return false;
            }
            loading_div.css('display', 'flex');
            var formData = new FormData();
            formData.append("description", MyEditor["editor1"].getData());
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'POST',
                url: '{{ route('user.board.list.card.update.description','/')}}/' + board_list_card_id,
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                success: function (data) {

                    $('.ck-desc').hide();
                    $('.hide-desc').show();
                    if (data.description) {
                        $('.show-desc').empty().append(`${data.description}`).show();
                        $('.hide-desc').hide();
                    } else {
                        $('.show-desc').empty().hide();
                        $('.hide-desc').show();
                    }
                    loading_div.css('display', 'none');
                },
                error: function (data) {
                    console.log(data);
                    loading_div.css('display', 'none');
                }

            })
            const updatedContent = MyEditor["editor1"].getData();
            updateSaveButtonState(updatedContent, '#ck-save-button1');

        })

        /** Description-Discard => On Discard Description CkEditor hide Description CkEditor , show Paragraph and Update Description Ckeditor to show actual saved data*/
        $(document).on('click', '#ck-close-button1', function () {
            var board_list_card_id = $('#modal-board-list-card-id').attr('data-modal-board-list-card-id');

            $('.ck-desc').hide();

            var url = '{{ route('user.board.list.card.get.description','/')}}/' + board_list_card_id;
            loading_div.css('display', 'flex');
            var res = AjaxRequestGet(url, null, null, false, null, false);
            if (res && res.description) {
                MyEditor["editor1"].setData(res.description);
                const updatedContent = MyEditor["editor1"].getData();
                updateSaveButtonState(updatedContent, '#ck-save-button1');
                $('.show-desc').empty().append(`${updatedContent}`).show();
                $('.hide-desc').hide();
                loading_div.css('display', 'none');
            } else {
                $('.hide-desc').show();
                $('.show-desc').hide();
                loading_div.css('display', 'none');
            }
        })

        /** New-Comment-Edit => On write a new comment CkEditor data set to null which we initialize in CkEditor Script & show Comment CkEditor and hide comment input*/
        $('#comment-input').on('click', function () {
            $("#comment-input").hide();
            $('.ck-div').show();
            MyEditor['editor2'].setData('');

            /** Hide Comment Editors */
            $(".show-comment").show();
            $('.ck-comment-div').hide();
        });

        /** New-Comment-Update-CkEditor => On changing new comment input updating Comment CkEditor data */
        MyEditor["editor2"].model.document.on('change:data', () => {
            const updatedContent = MyEditor["editor2"].getData();
            updateSaveButtonState(updatedContent, '#ck-save-button2');
        });

        /** New-Comment-Save => On Save New-Comment Send New-Comment Data to save and set null to New-Comment CkEditor and Append Comment with functionality and dynamic data */
        $('#ck-save-button2').on('click', function () {
            console.log(MyEditor["editor2"].getData());
            var board_list_card_id = $('#modal-board-list-card-id').attr('data-modal-board-list-card-id');
            if (!board_list_card_id || !$.isNumeric(board_list_card_id)) {
                return false;
            }
            loading_div.css('display', 'flex');
            var formData = new FormData();
            formData.append("comment", MyEditor["editor2"].getData());
            let url = '{{ route('user.board.list.card.add.comment','/')}}/' + board_list_card_id;
            AjaxRequestPostPromise(url, formData, null, false, null, false, true, true)
                .then((res) => {
                    var activity = res.activity;
                    prependModalActivityComment(board_list_card_id, res.user_name, activity);
                    createCKEditor(document.getElementById(`editor-comment-${board_list_card_id}-${activity.comment_id}`), null);
                    $('.ck-comment-div').hide();
                    $('.ck-div').hide();
                    $("#comment-input").show();
                    MyEditor["editor2"].setData('');
                    $('#ck-save-button2').prop('disabled', true);
                    if (res.hasOwnProperty('board_list_card_id') && res.hasOwnProperty('comment_count')) {
                        update_comment_count(board_list_card_id, res.comment_count);
                    }
                })
                .catch((error) => {
                    console.error("Error in Ajax request:", error);
                })
                .finally(() => {
                    loading_div.css('display', 'none');
                })
        });

        /** New-Comment-Discard => On Discard New-Comment CkEditor hide New-Comment CkEditor and show Paragraph comment input */
        $(document).on('click', '#ck-close-button2', function () {
            $("#comment-input").show();
            $('.ck-div').hide();
        })


        /** Change Cover Image size */
        $(document).on('click', '.image-size', function () {
            var board_list_card_id = $('#modal-board-list-card-id').attr('data-modal-board-list-card-id');
            let cover_image_size = $(this).data('id') == 1 ? 1 : 0;
            var formData = new FormData();
            formData.append("board_list_card_id", board_list_card_id);
            formData.append("cover_image_size", cover_image_size);
            var url = '{{ route('user.board.list.card.image.size.update')}}';
            AjaxRequestPostPromise(url, formData, 'Background image size updated successfully!', false, null, false)
                .then((res) => {
                    if (res.cover_image_size && res.cover_image_size == 1) {
                        $("#cover-image-main-div-" + board_list_card_id).css({'background-color': `3px solid #${res.cover_background_color}`});
                        $("#cover-image-" + board_list_card_id).css({
                            'width': '60%',
                            'height': '100%',
                            'background-color': `3px solid #${res.cover_background_color}`
                        });
                        $("#modal-cover-image-size-1").css({'border': '3px solid #0c66e4'});
                        $("#modal-cover-image-size-0").css({'border': 'none'});
                    } else {
                        $("#modal-cover-image-size-1").css({'border': 'none'});
                        $("#modal-cover-image-size-0").css({'border': '3px solid #0c66e4'});

                        $("#cover-image-main-div-" + board_list_card_id).css({'background-color': `transparent`});
                        $("#cover-image-" + board_list_card_id).css({
                            'width': '100%',
                            'height': '200px',
                            'background-color': `transparent`
                        });
                    }
                    loading_div.css('display', 'none');
                })
                .catch((error) => {
                    console.error("Error in Ajax request:", error);
                    loading_div.css('display', 'none');
                });
        })


        /** For Updating all comments ckeditor input :note old structure need to update route*/
        {{--$(document).on('click', '.board-list-card', function () {--}}
        {{--    var instancePattern = /^editor-comment/;--}}
        {{--    for (var instanceId in MyEditor) {--}}
        {{--        if (instancePattern.test(instanceId)) {--}}
        {{--            var editorInstance = Object.keys(MyEditor[instanceId]).length;--}}
        {{--            var board_list_card_id = $('#modal-board-list-card-id').attr('data-modal-board-list-card-id');--}}

        {{--            if (editorInstance > 0 && board_list_card_id > 0) {--}}
        {{--                var url = '{{ route('user.board.list.card.all.comments','/')}}/' + board_list_card_id;--}}
        {{--                var res = AjaxRequestGet(url, null, null, false, null, false);--}}
        {{--                console.log(res);--}}
        {{--            }--}}
        {{--        }--}}
        {{--    }--}}
        {{--});--}}

        $('.modal').on('click', '.comment-edit-link', function () {

            var board_list_card_id = $('#modal-board-list-card-id').attr('data-modal-board-list-card-id');
            var comment_id = $(this).data('id');

            /** First Hide All Comment Editors */
            $(".show-comment").show();
            $('.ck-comment-div').hide();
            $("#comment-input").show();
            $('.ck-div').hide();

            $("#para-" + board_list_card_id + "-" + comment_id).hide();
            $("#ck-comment-div-" + board_list_card_id + "-" + comment_id).show();

            var url = '{{ route('user.board.list.card.get.comment','/')}}/' + comment_id;
            loading_div.css('display', 'flex');
            var res = AjaxRequestGet(url, null, null, false, null, false);
            if (res && res.comment) {
                MyEditor["editor-comment-" + board_list_card_id + "-" + comment_id].setData(res.comment);
                const updatedContent = MyEditor["editor-comment-" + board_list_card_id + "-" + comment_id].getData();
                updateSaveButtonState(updatedContent, '#ck-save-button-comment-' + board_list_card_id + "-" + comment_id);
            }
            MyEditor["editor-comment-" + board_list_card_id + "-" + comment_id].model.document.on('change:data', () => {
                const updatedContent = MyEditor["editor-comment-" + board_list_card_id + "-" + comment_id].getData();
                updateSaveButtonState(updatedContent, '#ck-save-button-comment-' + board_list_card_id + "-" + comment_id);
            });
            loading_div.css('display', 'none');
        })

        function updateSaveButtonState(updatedContent, id) {
            if (updatedContent == null || updatedContent.trim() === '') {
                $(id).prop('disabled', true);
            } else {
                $(id).prop('disabled', false);
            }
        }

        $(document).on('click', '.ck-close-button-comment', function () {
            var board_list_card_id = $('#modal-board-list-card-id').attr('data-modal-board-list-card-id');
            var comment_id = $(this).data('id');
            $("#para-" + board_list_card_id + "-" + comment_id).show();
            $("#ck-comment-div-" + board_list_card_id + "-" + comment_id).hide();
        })

        $(document).on('click', '.ck-save-button-comment', function () {

            var board_list_card_id = $('#modal-board-list-card-id').attr('data-modal-board-list-card-id');
            var comment_id = $(this).data('id');

            if (!board_list_card_id || !$.isNumeric(board_list_card_id) || !comment_id || !$.isNumeric(comment_id)) {
                return false;
            }
            loading_div.css('display', 'flex');
            var formData = new FormData();
            formData.append("comment_id", comment_id);
            formData.append("comment", MyEditor["editor-comment-" + board_list_card_id + "-" + comment_id].getData());
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'POST',
                url: '{{ route('user.board.list.card.update.comment')}}',
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                success: function (data) {
                    if (data && data.status && data.status === 1) {
                        var activity = data.activity;

                        var checkid = `{{auth()->user()->id}}`;
                        MyEditor["editor-comment-" + board_list_card_id + "-" + comment_id].setData(data.activity.comment);
                        $('#ck-save-button-comment-' + board_list_card_id + "-" + comment_id).prop('disabled', true);
                        $("#para-" + board_list_card_id + "-" + comment_id).empty();

                        //     $("#para-" + board_list_card_id + "-" + comment_id).append(`
                        // <p>${data.comment}</p><span><a href="javascript:void(0);" class="comment-edit-link" data-id="${comment_id}">Edit</a></span>
                        // 							<p class="comment-created-at">${data.comment_created_at}</p>`);
                        $("#para-" + board_list_card_id + "-" + comment_id).append(`
                        <div class="upd-name-div">
                            <h4 class="upd-name">${activity.user_name}</h4>
                            <h5 class="upd-date">${activity.comment_created_at}</h5>
                        </div>
                        <div class="upd-comment-div">
                            <p>${activity.comment}</p>
                        </div>
                        <div class="upd-crud-list">
                            <ul class="crud-list">
                                <li><a href="javascript:void(0);" class="comment-add-reaction">
                                                <span class="material-symbols-outlined">
                                                    add_reaction
                                                </span>
                                </a></li>
                                ${checkid == activity.user_id ?
                            `<li><a href="javascript:void(0);" class="comment-edit-link" data-id="${activity.comment_id}">Edit</a></li>
											<li><a href="javascript:void(0);" class="comment-delete-link" data-id="${activity.comment_id}" data-activity-id="${activity.activity_id}">Delete</a></li>`
                            : ''}
                            </ul>
                        </div>
                        `);
                        $("#para-" + board_list_card_id + "-" + comment_id).show();
                        $("#ck-comment-div-" + board_list_card_id + "-" + comment_id).hide();
                        loading_div.css('display', 'none');
                    }
                },
                error: function (data) {
                    console.log(data);
                    loading_div.css('display', 'none');
                }
            });
        });

        /** delete user comment*/
        $("#modal-board-list-card-id").on("click", ".comment-delete-link", function () {
            var url = '{{ route('user.board.list.card.delete.comment','/')}}/' + $(this).data('id');
            AjaxRequestDeletePromise(url, null, 'Poof! Your comment has been deleted!', false, null, true)
                .then((res) => {
                    console.log('Comment deleted successfully:', res);
                    $("#activity-main-div-" + $(this).data('activity-id')).remove();
                    // prependModalActivity(res.board_list_card_id, res.user_name, res.activity)
                    if (res.hasOwnProperty('board_list_card_id') && res.hasOwnProperty('comment_count')) {
                        update_comment_count(res.board_list_card_id, res.comment_count);
                    }
                })
                .catch((error) => {
                    console.error("Error in Ajax request:", error);
                })
                .finally(() => {
                    loading_div.css('display', 'none');
                });
        });

        async function loadImage(event) {

            return new Promise((resolve, reject) => {
                const file = event.target.files[0];
                const reader = new FileReader();
                reader.onload = function (e) {
                    const img = new Image();
                    img.src = e.target.result;

                    img.onload = function () {
                        const modalId = $('#modal-board-list-card-id').attr('data-modal-board-list-card-id');
                        if (typeof modalId != 'undefined') {
                            // if (typeof modalId != 'undefined' && typeof modalId == 'number') {

                            console.log(modalId);
                            const image1 = $('#cover-image-' + modalId);
                            if (!file) {
                                if (currentSrc) {
                                    image1.css({
                                        'display': 'block',
                                        'visibility': 'visible',
                                        'backgroundColor': getDominantColor(img)
                                    });
                                } else {
                                    image1.attr('src', '').css({'display': 'none', 'visibility': 'hidden'});
                                }
                            } else {
                                image1.attr('src', e.target.result).css({
                                    'display': 'block',
                                    'visibility': 'visible',
                                    'backgroundColor': getDominantColor(img)
                                });
                            }
                            const modalCoverImages = $('.modal-cover-image');
                            modalCoverImages.each(function () {
                                const currentSrc = $(this).attr('src');
                                if (!file) {
                                    if (currentSrc) {
                                        $(this).css({
                                            'display': 'block',
                                            'visibility': 'visible',
                                            'backgroundColor': getDominantColor(img)
                                        });
                                    } else {
                                        $(this).attr('src', '').css({'display': 'none', 'visibility': 'hidden'});
                                    }
                                } else {
                                    $(this).attr('src', e.target.result).css({
                                        'display': 'block',
                                        'visibility': 'visible',
                                        'backgroundColor': getDominantColor(img)
                                    });
                                }
                            });
                        }
                        resolve(img);
                    };
                    img.onerror = function (error) {
                        reject(error);
                    };
                }
                reader.readAsDataURL(file);
            });
        }

        $(document).on('change', '#upload-cover-image', async function (event) {
            var board_list_card_id = $('#modal-board-list-card-id').attr('data-modal-board-list-card-id');
            if (!board_list_card_id || this.files[0] == null) {
                return false;
            }

            console.log(board_list_card_id);
            loading_div.css('display', 'flex');

            var formData = new FormData();
            formData.append("cover_image", this.files[0]);

            try {
                const img = await loadImage(event);
                await updateBackgroundColor(getDominantColor(img));
                var url = '{{ route('user.board.list.card.image.update','/')}}/' + board_list_card_id;
                const res = await AjaxRequestPostPromise(url, formData, 'Background color update successfully!', false, null, false)
            } catch (error) {
                console.error("Error in image upload:", error);
            } finally {
                loading_div.css('display', 'none');
            }
        });

        $(document).on('click', '.cover-image-bg-color', function () {
            updateBackgroundColor($(this).data('id'));
        });

        function updateBackgroundColor(cover_background_color) {
            var board_list_card_id = $('#modal-board-list-card-id').attr('data-modal-board-list-card-id');
            if (!board_list_card_id || !$.isNumeric(board_list_card_id)) {
                return false;
            }

            var formData = new FormData();
            formData.append("cover_background_color", cover_background_color);
            var url = '{{ route('user.board.list.card.image.background.color.update','/')}}/' + board_list_card_id;
            AjaxRequestPostPromise(url, formData, 'Background color update successfully!', false, null, false)
                .then((res) => {
                    $(".modal-cover-image").css('backgroundColor', `${res.cover_background_color}`);
                    $(".image-size-with-bg,.image-size-without-bg").css({'border': `3px solid #${res.cover_background_color}`});

                    if (res.cover_image_size && res.cover_image_size == 1) {
                        $("#cover-image-main-div-" + board_list_card_id).css({'background-color': `3px solid #${res.cover_background_color}`});
                        $("#cover-image-" + board_list_card_id).css({
                            'width': '60%',
                            'height': '100%',
                            'background-color': `3px solid #${res.cover_background_color}`
                        });
                    } else {
                        $("#cover-image-main-div-" + board_list_card_id).css({'background-color': `transparent`});
                        $("#cover-image-" + board_list_card_id).css({
                            'width': '100%',
                            'height': '200px',
                            'background-color': `transparent`
                        });
                    }
                    loading_div.css('display', 'none');
                })
                .catch((error) => {
                    console.error("Error in Ajax request:", error);
                    loading_div.css('display', 'none');
                });
        }

        $('#edit_board_card_form').on('submit', function (e) {
            e.preventDefault();
            var board_list_card_id = $('#edit_board_list_card_id').val();
            var url = '{{ route('user.board.list.card.update','/')}}/' + board_list_card_id;
            console.log(this);
            loading_div.css('display', 'flex');
            var formData = new FormData(this);
            var res = AjaxRequestPost(url, formData, 'Card updated successfully!', false, null);
            if (res.status && res.status === 1) {
                $('#edit-modal-content').css('background-image', 'url(assets/images/board-list-card/' + res.background_image + ')');
                $('#edit_board_list_card_id').val(res.board_list_card_id);
                $('#edit_project_id').selectpicker('val', res.project_id);
                $('#edit_title').val(res.title);
                $('#edit_description').text(res.description);
                $('#edit_assign_to').selectpicker('val', res.user_names);
                $('#edit_due_date').val(res.due_date);
            }
            loading_div.css('display', 'none');
        });

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

        // Function to generate a random color class
        function getRandomColorClass() {
            var colorClasses = ['color1', 'color2', 'color3', 'color4', 'color5', 'color6'];
            return colorClasses[Math.floor(Math.random() * colorClasses.length)];
        }

        // Function to get or generate a random color class and store it in local storage
        function getOrGenerateRandomColorClass(itemId) {
            var storedColorClass = localStorage.getItem(itemId);
            if (storedColorClass) {
                return storedColorClass;
            } else {
                var randomColorClass = getRandomColorClass();
                localStorage.setItem(itemId, randomColorClass);
                return randomColorClass;
            }
        }

        // $(document).ready(function() {
        //     // Attach input event to the textarea
        //     $('.editableText').on('input', function() {
        //     // Directly set the text content of the textarea
        //     $('.editableText').text($('.editableText').val());
        //     });
        // });

        // Function to handle dynamic button clicks
        document.querySelectorAll('.dynamicBtn').forEach(button => {
            button.addEventListener('click', function () {
                var target = this.getAttribute('data-target');
                var bodyDiv = document.querySelector('.inner-dropdown');
                var targetDiv = document.querySelector('.' + target);

                //remove all Element in the .body div
                bodyDiv.innerHTML = '';

                // Cloning the target div and appending it to the body div
                var clonedDiv = targetDiv.cloneNode(true);
                clonedDiv.style.display = "block"; // Ensure the cloned div is visible
                bodyDiv.appendChild(clonedDiv);
            });
        });

        var selector = '.move-all-list li';

        $(selector).on('click', function () {
            $(selector).removeClass('move-current');
            $(this).addClass('move-current');
        });

        // Show overlay and content on button click
        $(".main-card-button").click(function () {
            $(".dot-submenu-overlay").fadeIn();
            $(".overlay-div-menu-btn").fadeIn();
            $(".overlay-div-menu-list").fadeIn();
        });

        // Close overlay and content on close button click
        $(".overlay-div-menu-btn").click(function () {
            $(".dot-submenu-overlay").fadeOut();
            $(".overlay-div-menu-btn").fadeOut();
            $(".overlay-div-menu-list").fadeOut();
        });

        // Click function for the "Add Card" button
        $("#addCard").click(function () {
            // Get the card name from the input field
            var cardName = $("#cardName").val();

            // Check if the card name is not empty
            if (cardName.trim() !== "") {
                // Append a new card to the container
                appendCard(cardName);

                // Clear the input field after adding the card
                $("#cardName").val("");
            }
            // document.getElementById("welcomeDiv").style.display = "none";
        });

        $(".board-list-card").click(function (event) {
            if ($(event.target).closest(".do-not-target").length > 0) {
                event.stopPropagation();
            }
        });
    });
</script>

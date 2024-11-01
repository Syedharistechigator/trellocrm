<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
<script>
    // $('.mail-filter')
    // $(document).on('click', 'body .mail-filter', function (e) {
    //     e.stopPropagation();
    // });
    $('#searchFilt').on('hide.bs.dropdown', function (e) {
        if (e.clickEvent) {
            e.preventDefault();
        }

        // $('#searchFilt > a.dropdown-toggle').on('click', function(event) {
        //     $(this).parent().toggleClass('show')//addor remove class
        //     $(this).attr('aria-expanded', $(this).attr('aria-expanded') == 'false' ? 'true' : 'false'); //add true or false
        //     $("div[aria-labelledby=" + $(this).attr('id') + "]").toggleClass('show') //add class/remove
        // });

        // $('body').on('click', function(e) {
        // //check if the click occur outside `searchFilt` tag if yes ..hide menu
        //     if (!$('#searchFilt').is(e.target) &&
        //     $('#searchFilt').has(e.target).length === 0 &&
        //     $('.show').has(e.target).length === 0
        //     ) {
        //     //remove clases and add attr
        //     $('#searchFilt').removeClass('show')
        //     $('#searchFilt > .mail-filter').attr('aria-expanded', 'false');
        //     $("#searchFilt").children('div.dropdown-menu').removeClass('show')
        //     }
        // });

    });


    // $(document).ready(function() {
    //         $('#mailTable').DataTable({
    //             "paging": true,  // Enable pagination
    //             "columns": [
    //                 null, // Placeholder for the first column
    //                 null,
    //                 null,
    //                 { "name": "mailer-name", "orderable": true },
    //                 { "name": "mail-text", "orderable": false },
    //                 { "name": "mail-time", "orderable": true },
    //                 null
    //             ]
    //         });
    //     });


    function openDialog() {
        let chatbox = document.getElementById('chat-box');
        if (chatbox) {
            chatbox.style.display = 'flex'
        }
    }

    function closeDialog() {
        let chatbox = document.getElementById('chat-box');
        if (chatbox) {
            chatbox.style.display = 'none'
        }
    }
</script>


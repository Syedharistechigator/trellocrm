$(function () {
    $('.js-basic-example').DataTable({
        order: [0,'desc'],
    });

    //Exportable table
    $('.js-exportable').DataTable({
        dom: 'Bfrtip',
        order: [0,'desc'],
        buttons: [
            'copy', 'csv', 'excel', 'pdf', 'print'
        ]
    });
});
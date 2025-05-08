$(document).ready(function() {
    $('#myTable').DataTable({
        columnDefs: [
            {
                targets: "ID",
                visible: false, 
                searchable: false 
            },
            {
                targets: 'Action',
                width: '50px'
            }
        ]
    });
});

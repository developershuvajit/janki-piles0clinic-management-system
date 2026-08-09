/**
 * ============================================
 * DATATABLE INIT - Reusable
 * ============================================
 * Usage: 
 *   - Call initDataTable(tableId, options) 
 *   - Or use default: initDataTable('patientsTable')
 * ============================================
 */

function initDataTable(tableId, customOptions) {
    if (typeof $ === 'undefined' || typeof $.fn.DataTable === 'undefined') {
        console.warn('jQuery or DataTables not loaded');
        return;
    }

    // Default options
    var defaults = {
        dom: '<"d-flex flex-wrap align-items-center justify-content-between gap-2 p-2"lBf>t<"d-flex flex-wrap align-items-center justify-content-between gap-2 p-2"ip>',
        buttons: [
            { extend: 'copy', text: '<i class="bi bi-copy"></i> Copy', className: 'btn btn-sm' },
            { extend: 'csv', text: '<i class="bi bi-file-earmark-spreadsheet"></i> CSV', className: 'btn btn-sm' },
            { extend: 'excel', text: '<i class="bi bi-file-earmark-excel"></i> Excel', className: 'btn btn-sm' },
            { extend: 'pdf', text: '<i class="bi bi-file-earmark-pdf"></i> PDF', className: 'btn btn-sm' },
            { extend: 'print', text: '<i class="bi bi-printer"></i> Print', className: 'btn btn-sm' }
        ],
        pageLength: 25,
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
        order: [[1, 'desc']],
        columnDefs: [
            { orderable: false, targets: [0] },
            { searchable: false, targets: [0] }
        ],
        language: {
            search: "Search:",
            lengthMenu: "Show _MENU_",
            info: "_START_ – _END_ of _TOTAL_",
            infoEmpty: "No records found",
            infoFiltered: "(filtered from _MAX_ total)",
            zeroRecords: "No matching records found"
        }
    };

    // Merge custom options
    var options = $.extend(true, {}, defaults, customOptions);

    // Initialize DataTable
    var table = $('#' + tableId).DataTable(options);

    return table;
}

// Auto-init if element exists
$(document).ready(function() {
    if ($('#patientsTable').length) {
        initDataTable('patientsTable', {
            columnDefs: [
                { orderable: false, targets: [0, 2, 9] },
                { searchable: false, targets: [0, 2, 9] }
            ],
            order: [[1, 'desc']]
        });
    }
});
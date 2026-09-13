$(document).ready(function () {


    let allLeaves = [];


    const modalElement =
        document.getElementById(
            'leaveActionModal'
        );


    const leaveModal =
        new bootstrap.Modal(
            modalElement
        );



    /*
    |--------------------------------------------------------------------------
    | Initial Load
    |--------------------------------------------------------------------------
    */

    loadLeaves();



    /*
    |--------------------------------------------------------------------------
    | Filter
    |--------------------------------------------------------------------------
    */

    $('#filterBtn').on(
        'click',
        function () {

            displayLeaves(
                getFilteredLeaves()
            );

        }
    );



    /*
    |--------------------------------------------------------------------------
    | Reset
    |--------------------------------------------------------------------------
    */

    $('#resetFilterBtn').on(
        'click',
        function () {

            $('#statusFilter').val('');

            displayLeaves(
                allLeaves
            );

        }
    );



    /*
    |--------------------------------------------------------------------------
    | Save Action
    |--------------------------------------------------------------------------
    */

    $('#saveLeaveActionBtn').on(
        'click',
        function () {

            updateLeaveStatus();

        }
    );



    /*
    |--------------------------------------------------------------------------
    | Load Leaves
    |--------------------------------------------------------------------------
    */

    function loadLeaves()
    {

        $.ajax({

            url: '../../ajax/leaves.php',

            type: 'GET',

            data: {

                action: 'admin_leaves'

            },

            dataType: 'json',


            beforeSend: function () {

                $('#adminLeaveTableBody').html(`

                    <tr>

                        <td
                            colspan="9"
                            class="text-center py-5"
                        >

                            <div
                                class="spinner-border"
                            ></div>

                            <div class="mt-2 text-muted">

                                Loading leave requests...

                            </div>

                        </td>

                    </tr>

                `);

            },


            success: function (response) {

                if (!response.success) {

                    showMessage(
                        response.message,
                        'danger'
                    );

                    return;
                }


                allLeaves =
                    response.leaves || [];


                displayLeaves(
                    allLeaves
                );

            },


            error: function (xhr) {

                showMessage(
                    getErrorMessage(xhr),
                    'danger'
                );

            }

        });

    }



    /*
    |--------------------------------------------------------------------------
    | Filter Leaves
    |--------------------------------------------------------------------------
    */

    function getFilteredLeaves()
    {

        const status =
            $('#statusFilter').val();


        if (!status) {

            return allLeaves;

        }


        return allLeaves.filter(
            function (leave) {

                return leave.status === status;

            }
        );

    }



    /*
    |--------------------------------------------------------------------------
    | Display Leaves
    |--------------------------------------------------------------------------
    */

    function displayLeaves(leaves)
    {

        const tbody =
            $('#adminLeaveTableBody');


        tbody.empty();


        if (!leaves.length) {

            tbody.html(`

                <tr>

                    <td
                        colspan="9"
                        class="text-center py-5 text-muted"
                    >

                        No leave requests found.

                    </td>

                </tr>

            `);

            return;

        }


        leaves.forEach(
            function (leave, index) {


                const statusBadge =
                    getStatusBadge(
                        leave.status
                    );


                let action = '-';


                if (leave.status === 'pending') {

                    action = `

                        <button
                            type="button"
                            class="btn btn-sm btn-primary processLeaveBtn"
                            data-id="${leave.id}"
                        >

                            Process

                        </button>

                    `;

                }


                tbody.append(`

                    <tr>


                        <td>

                            ${index + 1}

                        </td>



                        <td>

                            <strong>

                                ${escapeHtml(
                                    leave.employee_code
                                )}

                            </strong>

                            <br>

                            ${escapeHtml(
                                leave.first_name
                            )}

                            ${escapeHtml(
                                leave.last_name
                            )}

                        </td>



                        <td>

                            ${escapeHtml(
                                leave.department || '-'
                            )}

                        </td>



                        <td>

                            ${formatLeaveType(
                                leave.leave_type
                            )}

                        </td>



                        <td>

                            ${escapeHtml(
                                leave.start_date
                            )}

                        </td>



                        <td>

                            ${escapeHtml(
                                leave.end_date
                            )}

                        </td>



                        <td>

                            ${escapeHtml(
                                leave.reason
                            )}

                        </td>



                        <td>

                            ${statusBadge}

                        </td>



                        <td>

                            ${action}

                        </td>


                    </tr>

                `);

            }
        );

    }



    /*
    |--------------------------------------------------------------------------
    | Process Button
    |--------------------------------------------------------------------------
    */

    $(document).on(
        'click',
        '.processLeaveBtn',
        function () {

            const leaveId =
                $(this).data('id');


            $('#modalLeaveId')
                .val(leaveId);


            $('#modalStatus')
                .val('approved');


            $('#modalRemarks')
                .val('');


            leaveModal.show();

        }
    );



    /*
    |--------------------------------------------------------------------------
    | Update Status
    |--------------------------------------------------------------------------
    */

    function updateLeaveStatus()
    {

        const leaveId =
            $('#modalLeaveId').val();


        const status =
            $('#modalStatus').val();


        const remarks =
            $('#modalRemarks').val().trim();


        const button =
            $('#saveLeaveActionBtn');


        $.ajax({

            url: '../../ajax/leaves.php?action=update_status',

            type: 'POST',

            data: {

                leave_id: leaveId,

                status: status,

                admin_remarks: remarks

            },

            dataType: 'json',


            beforeSend: function () {

                button
                    .prop('disabled', true)
                    .text('Saving...');

            },


            success: function (response) {

                if (!response.success) {

                    showMessage(
                        response.message,
                        'danger'
                    );

                    return;
                }


                leaveModal.hide();


                showMessage(
                    response.message,
                    'success'
                );


                loadLeaves();

            },


            error: function (xhr) {

                showMessage(
                    getErrorMessage(xhr),
                    'danger'
                );

            },


            complete: function () {

                button
                    .prop('disabled', false)
                    .text('Save');

            }

        });

    }



    /*
    |--------------------------------------------------------------------------
    | Status Badge
    |--------------------------------------------------------------------------
    */

    function getStatusBadge(status)
    {

        switch (status) {

            case 'approved':

                return `
                    <span class="badge bg-success">
                        Approved
                    </span>
                `;


            case 'rejected':

                return `
                    <span class="badge bg-danger">
                        Rejected
                    </span>
                `;


            case 'pending':

                return `
                    <span class="badge bg-warning text-dark">
                        Pending
                    </span>
                `;


            default:

                return `
                    <span class="badge bg-secondary">
                        ${escapeHtml(status)}
                    </span>
                `;

        }

    }



    /*
    |--------------------------------------------------------------------------
    | Leave Type
    |--------------------------------------------------------------------------
    */

    function formatLeaveType(type)
    {

        const types = {

            casual: 'Casual',

            sick: 'Sick',

            annual: 'Annual',

            unpaid: 'Unpaid',

            other: 'Other'

        };


        return escapeHtml(
            types[type] || type
        );

    }



    /*
    |--------------------------------------------------------------------------
    | Message
    |--------------------------------------------------------------------------
    */

    function showMessage(
        message,
        type
    ) {

        $('#adminLeaveMessage')

            .removeClass(
                'd-none alert-success alert-danger alert-warning'
            )

            .addClass(
                'alert-' + type
            )

            .text(message);

    }



    /*
    |--------------------------------------------------------------------------
    | Error Message
    |--------------------------------------------------------------------------
    */

    function getErrorMessage(xhr)
    {

        if (
            xhr.responseJSON &&
            xhr.responseJSON.message
        ) {

            return xhr.responseJSON.message;

        }


        return 'Something went wrong.';

    }



    /*
    |--------------------------------------------------------------------------
    | Escape HTML
    |--------------------------------------------------------------------------
    */

    function escapeHtml(value)
    {

        return $('<div>')
            .text(value ?? '')
            .html();

    }

});
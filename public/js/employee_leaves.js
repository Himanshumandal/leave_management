$(document).ready(function () {


    /*
    |--------------------------------------------------------------------------
    | Load Leaves
    |--------------------------------------------------------------------------
    */

    loadLeaves();



    /*
    |--------------------------------------------------------------------------
    | Submit Leave
    |--------------------------------------------------------------------------
    */

    $('#leaveForm').on(
        'submit',
        function (event) {

            event.preventDefault();

            clearErrors();

            submitLeave();

        }
    );



    /*
    |--------------------------------------------------------------------------
    | Reset Form
    |--------------------------------------------------------------------------
    */

    $('#resetLeaveBtn').on(
        'click',
        function () {

            clearErrors();

            hideMessage();

        }
    );



    /*
    |--------------------------------------------------------------------------
    | Submit Leave
    |--------------------------------------------------------------------------
    */

    function submitLeave()
    {

        const form =
            $('#leaveForm');


        const button =
            $('#submitLeaveBtn');


        const formData =
            form.serialize();


        $.ajax({

            url: '../../ajax/leaves.php?action=store',

            type: 'POST',

            data: formData,

            dataType: 'json',


            /*
            |--------------------------------------------------------------------------
            | Before Send
            |--------------------------------------------------------------------------
            */

            beforeSend: function () {

                button
                    .prop('disabled', true)
                    .text('Submitting...');

            },


            /*
            |--------------------------------------------------------------------------
            | Success
            |--------------------------------------------------------------------------
            */

            success: function (response) {

                if (!response.success) {

                    if (response.errors) {

                        showValidationErrors(
                            response.errors
                        );

                    }


                    showMessage(
                        response.message ||
                        'Unable to submit leave request.',
                        'danger'
                    );

                    return;
                }


                showMessage(
                    response.message ||
                    'Leave request submitted successfully.',
                    'success'
                );


                /*
                |--------------------------------------------------------------------------
                | Reset Form
                |--------------------------------------------------------------------------
                */

                form[0].reset();


                clearErrors();


                /*
                |--------------------------------------------------------------------------
                | Reload Leave List
                |--------------------------------------------------------------------------
                */

                loadLeaves();

            },


            /*
            |--------------------------------------------------------------------------
            | Error
            |--------------------------------------------------------------------------
            */

            error: function (xhr) {

                let message =
                    'Something went wrong.';


                if (
                    xhr.responseJSON &&
                    xhr.responseJSON.message
                ) {

                    message =
                        xhr.responseJSON.message;

                }


                if (
                    xhr.responseJSON &&
                    xhr.responseJSON.errors
                ) {

                    showValidationErrors(
                        xhr.responseJSON.errors
                    );

                }


                showMessage(
                    message,
                    'danger'
                );

            },


            /*
            |--------------------------------------------------------------------------
            | Complete
            |--------------------------------------------------------------------------
            */

            complete: function () {

                button
                    .prop('disabled', false)
                    .text(
                        'Submit Leave Request'
                    );

            }

        });

    }



    /*
    |--------------------------------------------------------------------------
    | Load Employee Leaves
    |--------------------------------------------------------------------------
    */

    function loadLeaves()
    {

        $.ajax({

            url: '../../ajax/leaves.php',

            type: 'GET',

            data: {

                action: 'my_leaves'

            },

            dataType: 'json',


            beforeSend: function () {

                $('#leaveTableBody').html(`

                    <tr>

                        <td
                            colspan="8"
                            class="text-center py-5"
                        >

                            <div
                                class="spinner-border"
                                role="status"
                            ></div>


                            <div class="text-muted mt-2">

                                Loading leave requests...

                            </div>

                        </td>

                    </tr>

                `);

            },


            success: function (response) {

                if (!response.success) {

                    showMessage(
                        response.message ||
                        'Unable to load leaves.',
                        'danger'
                    );

                    return;
                }


                displayLeaves(
                    response.leaves || []
                );

            },


            error: function (xhr) {

                let message =
                    'Unable to load leave requests.';


                if (
                    xhr.responseJSON &&
                    xhr.responseJSON.message
                ) {

                    message =
                        xhr.responseJSON.message;

                }


                showMessage(
                    message,
                    'danger'
                );

            }

        });

    }



    /*
    |--------------------------------------------------------------------------
    | Display Leaves
    |--------------------------------------------------------------------------
    */

    function displayLeaves(leaves)
    {

        const tbody =
            $('#leaveTableBody');


        tbody.empty();


        /*
        |--------------------------------------------------------------------------
        | No Leaves
        |--------------------------------------------------------------------------
        */

        if (!leaves.length) {

            tbody.html(`

                <tr>

                    <td
                        colspan="8"
                        class="text-center text-muted py-5"
                    >

                        No leave requests found.

                    </td>

                </tr>

            `);

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | Create Rows
        |--------------------------------------------------------------------------
        */

        leaves.forEach(
            function (leave, index) {


                const statusBadge =
                    getStatusBadge(
                        leave.status
                    );


                tbody.append(`

                    <tr>


                        <!-- Number -->

                        <td>

                            ${index + 1}

                        </td>



                        <!-- Leave Type -->

                        <td>

                            ${formatLeaveType(
                                leave.leave_type
                            )}

                        </td>



                        <!-- Start Date -->

                        <td>

                            ${escapeHtml(
                                leave.start_date
                            )}

                        </td>



                        <!-- End Date -->

                        <td>

                            ${escapeHtml(
                                leave.end_date
                            )}

                        </td>



                        <!-- Reason -->

                        <td>

                            ${escapeHtml(
                                leave.reason
                            )}

                        </td>



                        <!-- Status -->

                        <td>

                            ${statusBadge}

                        </td>



                        <!-- Admin Remarks -->

                        <td>

                            ${escapeHtml(
                                leave.admin_remarks || '-'
                            )}

                        </td>



                        <!-- Applied -->

                        <td>

                            ${formatDateTime(
                                leave.created_at
                            )}

                        </td>


                    </tr>

                `);

            }
        );

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
    | Leave Type Formatting
    |--------------------------------------------------------------------------
    */

    function formatLeaveType(type)
    {

        const types = {

            casual: 'Casual Leave',

            sick: 'Sick Leave',

            annual: 'Annual Leave',

            unpaid: 'Unpaid Leave',

            other: 'Other'

        };


        return escapeHtml(
            types[type] || type
        );

    }



    /*
    |--------------------------------------------------------------------------
    | Validation Errors
    |--------------------------------------------------------------------------
    */

    function showValidationErrors(errors)
    {

        Object.keys(errors).forEach(
            function (field) {

                const message =
                    Array.isArray(errors[field])
                        ? errors[field][0]
                        : errors[field];


                $('#' + field + 'Error')
                    .text(message);

            }
        );

    }



    /*
    |--------------------------------------------------------------------------
    | Clear Validation Errors
    |--------------------------------------------------------------------------
    */

    function clearErrors()
    {

        $('#leaveTypeError').text('');

        $('#startDateError').text('');

        $('#endDateError').text('');

        $('#reasonError').text('');

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

        $('#leaveMessage')

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
    | Hide Message
    |--------------------------------------------------------------------------
    */

    function hideMessage()
    {

        $('#leaveMessage')

            .addClass('d-none')

            .text('');

    }



    /*
    |--------------------------------------------------------------------------
    | Format Date Time
    |--------------------------------------------------------------------------
    */

    function formatDateTime(value)
    {

        if (!value) {

            return '-';

        }


        const date =
            new Date(
                value.replace(' ', 'T')
            );


        if (isNaN(date.getTime())) {

            return escapeHtml(value);

        }


        return date.toLocaleString();

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
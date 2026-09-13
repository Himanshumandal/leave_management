$(document).ready(function () {


    /*
    |--------------------------------------------------------------------------
    | Default dates
    |--------------------------------------------------------------------------
    */

    const today =
        new Date()
            .toISOString()
            .split('T')[0];


    const firstDay =
        new Date(
            new Date().getFullYear(),
            new Date().getMonth(),
            1
        )
        .toISOString()
        .split('T')[0];


    $('#dateFrom').val(firstDay);

    $('#dateTo').val(today);



    /*
    |--------------------------------------------------------------------------
    | Load employees
    |--------------------------------------------------------------------------
    */

    loadEmployees();



    /*
    |--------------------------------------------------------------------------
    | Load summary automatically
    |--------------------------------------------------------------------------
    */

    loadSummary();



    /*
    |--------------------------------------------------------------------------
    | Search
    |--------------------------------------------------------------------------
    */

    $('#searchSummaryBtn').on(
        'click',
        function () {

            loadSummary();

        }
    );



    /*
    |--------------------------------------------------------------------------
    | Reset
    |--------------------------------------------------------------------------
    */

    $('#resetSummaryBtn').on(
        'click',
        function () {

            $('#dateFrom')
                .val(firstDay);


            $('#dateTo')
                .val(today);


            $('#employeeId')
                .val('');


            loadSummary();

        }
    );



    /*
    |--------------------------------------------------------------------------
    | Load Employees
    |--------------------------------------------------------------------------
    */

    function loadEmployees()
    {

        $.ajax({

            url: '../../ajax/employees.php',

            type: 'GET',

            data: {

                action: 'index'

            },

            dataType: 'json',


            success: function (response) {

                if (!response.success) {

                    return;
                }


                const select =
                    $('#employeeId');


                const employees =
                    response.employees || [];


                employees.forEach(
                    function (employee) {

                        select.append(`

                            <option
                                value="${employee.id}"
                            >

                                ${escapeHtml(
                                    employee.employee_id
                                )}

                                -

                                ${escapeHtml(
                                    employee.first_name
                                )}

                                ${escapeHtml(
                                    employee.last_name
                                )}

                            </option>

                        `);

                    }
                );

            }

        });

    }



    /*
    |--------------------------------------------------------------------------
    | Load Summary
    |--------------------------------------------------------------------------
    */

    function loadSummary()
    {

        const dateFrom =
            $('#dateFrom').val();


        const dateTo =
            $('#dateTo').val();


        const employeeId =
            $('#employeeId').val();


        /*
        |--------------------------------------------------------------------------
        | Validate dates
        |--------------------------------------------------------------------------
        */

        if (!dateFrom || !dateTo) {

            showMessage(
                'Please select both dates.',
                'danger'
            );

            return;
        }


        if (dateFrom > dateTo) {

            showMessage(
                'From date cannot be greater than To date.',
                'danger'
            );

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | AJAX
        |--------------------------------------------------------------------------
        */

        $.ajax({

            url: '../../ajax/attendence.php',

            type: 'GET',

            data: {

                action: 'summary',

                date_from: dateFrom,

                date_to: dateTo,

                employee_id: employeeId

            },

            dataType: 'json',


            /*
            |--------------------------------------------------------------------------
            | Before Send
            |--------------------------------------------------------------------------
            */

            beforeSend: function () {

                $('#searchSummaryBtn')
                    .prop('disabled', true)
                    .text('Loading...');


                showLoading();

            },


            /*
            |--------------------------------------------------------------------------
            | Success
            |--------------------------------------------------------------------------
            */

            success: function (response) {

                if (!response.success) {

                    showMessage(
                        response.message ||
                        'Unable to load summary.',
                        'danger'
                    );

                    return;
                }


                displaySummary(
                    response.summary || []
                );

            },


            /*
            |--------------------------------------------------------------------------
            | Error
            |--------------------------------------------------------------------------
            */

            error: function (xhr) {

                let message =
                    'Unable to load attendance summary.';


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

            },


            /*
            |--------------------------------------------------------------------------
            | Complete
            |--------------------------------------------------------------------------
            */

            complete: function () {

                $('#searchSummaryBtn')
                    .prop('disabled', false)
                    .text('Search');

            }

        });

    }



    /*
    |--------------------------------------------------------------------------
    | Display Summary
    |--------------------------------------------------------------------------
    */

    function displaySummary(records)
    {
        

        const tbody =
            $('#attendanceSummaryBody');


        tbody.empty();


        /*
        |--------------------------------------------------------------------------
        | No Records
        |--------------------------------------------------------------------------
        */

        if (!records.length) {

            tbody.html(`

                <tr>

                    <td
                        colspan="9"
                        class="text-center text-muted py-5"
                    >

                        No attendance data found.

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

        records.forEach(
            function (record, index) {
                const workingDays =
                parseInt(
                    record.working_days || 0
                );

                const present =
                    parseInt(
                        record.present_days || 0
                    );


                const absent =
                    parseInt(
                        record.absent_days || 0
                    );


                const halfDay =
                    parseInt(
                        record.half_days || 0
                    );


                const leave =
                    parseInt(
                        record.leave_days || 0
                    );


                const totalMarked =
                    parseInt(
                        record.total_marked || 0
                    );


                tbody.append(`

                    <tr>

                        <!-- Number -->

                        <td>

                            ${index + 1}

                        </td>


                        <!-- Employee ID -->

                        <td>

                            <strong>

                                ${escapeHtml(
                                    record.employee_id
                                )}

                            </strong>

                        </td>


                        <!-- Name -->

                        <td>

                            ${escapeHtml(
                                record.first_name
                            )}

                            ${escapeHtml(
                                record.last_name
                            )}

                        </td>


                        <!-- Department -->

                        <td>

                            ${escapeHtml(
                                record.department || '-'
                            )}

                        </td>


                        <!-- Working Days -->

                        <td class="text-center">

                            <span
                                class="badge bg-success"
                            >

                                ${workingDays}

                            </span>

                        </td>

                        <!-- Present -->

                        <td class="text-center">

                            <span
                                class="badge bg-success"
                            >

                                ${present}

                            </span>

                        </td>


                        <!-- Absent -->

                        <td class="text-center">

                            <span
                                class="badge bg-danger"
                            >

                                ${absent}

                            </span>

                        </td>


                        <!-- Half Day -->

                        <td class="text-center">

                            <span
                                class="badge bg-warning text-dark"
                            >

                                ${halfDay}

                            </span>

                        </td>


                        <!-- Leave -->

                        <td class="text-center">

                            <span
                                class="badge bg-info text-dark"
                            >

                                ${leave}

                            </span>

                        </td>


                        <!-- Total -->

                        <td class="text-center">

                            <strong>

                                ${totalMarked}

                            </strong>

                        </td>

                    </tr>

                `);

            }
        );

    }



    /*
    |--------------------------------------------------------------------------
    | Loading
    |--------------------------------------------------------------------------
    */

    function showLoading()
    {

        $('#attendanceSummaryBody').html(`

            <tr>

                <td
                    colspan="9"
                    class="text-center py-5"
                >

                    <div
                        class="spinner-border"
                        role="status"
                    ></div>


                    <div class="mt-2 text-muted">

                        Loading summary...

                    </div>

                </td>

            </tr>

        `);

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

        $('#summaryMessage')

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
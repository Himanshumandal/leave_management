$(document).ready(function () {


    /*
    |--------------------------------------------------------------------------
    | Set default dates
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
    | Load attendance automatically
    |--------------------------------------------------------------------------
    */

    loadHistory();



    /*
    |--------------------------------------------------------------------------
    | Search
    |--------------------------------------------------------------------------
    */

    $('#searchAttendanceBtn').on(
        'click',
        function () {

            loadHistory();

        }
    );



    /*
    |--------------------------------------------------------------------------
    | Reset
    |--------------------------------------------------------------------------
    */

    $('#resetAttendanceBtn').on(
        'click',
        function () {

            $('#dateFrom')
                .val(firstDay);


            $('#dateTo')
                .val(today);


            $('#employeeId')
                .val('');


            $('#status')
                .val('');


            loadHistory();

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


                /*
                |--------------------------------------------------------------------------
                | Depending on your existing employee response
                |--------------------------------------------------------------------------
                */

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
    | Load History
    |--------------------------------------------------------------------------
    */

    function loadHistory()
    {

        const dateFrom =
            $('#dateFrom').val();


        const dateTo =
            $('#dateTo').val();
        
        let employeeId='';

        if(role === 'admin'){
            
            employeeId =
             $('#employeeId').val();
        }else if(role === 'employee'){
            employeeId=empid;
        }      


        const status =
            $('#status').val();


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

                action: 'history',

                date_from: dateFrom,

                date_to: dateTo,

                employee_id: employeeId,

                status: status

            },

            dataType: 'json',


            /*
            |--------------------------------------------------------------------------
            | Before Send
            |--------------------------------------------------------------------------
            */

            beforeSend: function () {

                $('#searchAttendanceBtn')
                    .prop('disabled', true)
                    .text('Searching...');


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
                        'Unable to load attendance.',
                        'danger'
                    );

                    return;
                }

                console.log(response);
                


                displayHistory(
                    response.records || []
                );

            },


            /*
            |--------------------------------------------------------------------------
            | Error
            |--------------------------------------------------------------------------
            */

            error: function (xhr) {

                let message =
                    'Unable to load attendance history.';


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

                $('#searchAttendanceBtn')
                    .prop('disabled', false)
                    .text('Search');

            }

        });

    }



    /*
    |--------------------------------------------------------------------------
    | Display History
    |--------------------------------------------------------------------------
    */

    function displayHistory(records)
    {

        const tbody =
            $('#attendanceHistoryBody');


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

                        No attendance records found.

                    </td>

                </tr>

            `);

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | Records
        |--------------------------------------------------------------------------
        */

        records.forEach(
            function (record, index) {


                const statusBadge =
                    getStatusBadge(
                        record.status
                    );


                tbody.append(`

                    <tr>

                        <td>

                            ${index + 1}

                        </td>


                        <td>

                            ${formatDate(
                                record.attendance_date
                            )}

                        </td>


                        <td>

                            <strong>

                                ${escapeHtml(
                                    record.employee_code
                                )}

                            </strong>

                        </td>


                        <td>

                            ${escapeHtml(
                                record.first_name
                            )}

                            ${escapeHtml(
                                record.last_name
                            )}

                        </td>


                        <td>

                            ${escapeHtml(
                                record.department
                            )}

                        </td>


                        <td>

                            ${statusBadge}

                        </td>


                        <td>

                            ${formatTime(
                                record.check_in
                            )}

                        </td>


                        <td>

                            ${formatTime(
                                record.check_out
                            )}

                        </td>


                        <td>

                            ${escapeHtml(
                                record.remarks || '-'
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

        let className =
            'bg-secondary';


        let text =
            status;


        switch (status) {

            case 'present':

                className =
                    'bg-success';

                text =
                    'Present';

                break;


            case 'absent':

                className =
                    'bg-danger';

                text =
                    'Absent';

                break;


            case 'half_day':

                className =
                    'bg-warning text-dark';

                text =
                    'Half Day';

                break;


            case 'leave':

                className =
                    'bg-info text-dark';

                text =
                    'Leave';

                break;

        }


        return `

            <span class="badge ${className}">

                ${text}

            </span>

        `;

    }



    /*
    |--------------------------------------------------------------------------
    | Format Date
    |--------------------------------------------------------------------------
    */

    function formatDate(date)
    {

        if (!date) {

            return '-';

        }


        const parts =
            date.split('-');


        if (parts.length !== 3) {

            return escapeHtml(date);

        }


        return `${parts[2]}-${parts[1]}-${parts[0]}`;

    }



    /*
    |--------------------------------------------------------------------------
    | Format Time
    |--------------------------------------------------------------------------
    */

    function formatTime(time)
    {

        if (!time) {

            return '-';

        }


        return escapeHtml(
            time.substring(0, 5)
        );

    }



    /*
    |--------------------------------------------------------------------------
    | Loading
    |--------------------------------------------------------------------------
    */

    function showLoading()
    {

        $('#attendanceHistoryBody').html(`

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

                        Loading attendance...

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

        $('#historyMessage')

            .removeClass(
                'd-none alert-success alert-danger alert-warning'
            )

            .addClass(
                'alert-' + type
            )

            .text(message);


        if (type === 'success') {

            setTimeout(
                function () {

                    $('#historyMessage')
                        .addClass('d-none');

                },
                3000
            );

        }

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
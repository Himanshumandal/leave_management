$(document).ready(function () {


    /*
    |--------------------------------------------------------------------------
    | Set today's date
    |--------------------------------------------------------------------------
    */

    const today = new Date();

    const year = today.getFullYear();
    const month = String(today.getMonth() + 1).padStart(2, '0');
    const day = String(today.getDate()).padStart(2, '0');

    const formattedToday = `${year}-${month}-${day}`;

    $('#attendanceDate').val(formattedToday);



    /*
    |--------------------------------------------------------------------------
    | Load Attendance Button
    |--------------------------------------------------------------------------
    */

    $('#loadAttendanceBtn').on(
        'click',
        function () {

            loadAttendance();

        }
    );



    /*
    |--------------------------------------------------------------------------
    | Load Attendance
    |--------------------------------------------------------------------------
    */

    function loadAttendance()
    {

        const date =
            $('#attendanceDate').val();


        /*
        |--------------------------------------------------------------------------
        | Date validation
        |--------------------------------------------------------------------------
        */

        if (!date) {

            showMessage(
                'Please select attendance date.',
                'danger'
            );

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | AJAX Request
        |--------------------------------------------------------------------------
        */

        $.ajax({

            url: '../../ajax/attendence.php',

            type: 'GET',

            data: {

                action: 'index',

                date: date

            },

            dataType: 'json',


            /*
            |--------------------------------------------------------------------------
            | Before Send
            |--------------------------------------------------------------------------
            */

            beforeSend: function () {

                $('#loadAttendanceBtn')
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
                        'Unable to load attendance.',
                        'danger'
                    );

                    return;
                }


                displayAttendance(
                    response.employees || []
                );

            },


            /*
            |--------------------------------------------------------------------------
            | Error
            |--------------------------------------------------------------------------
            */

            error: function (xhr) {

                let message =
                    'Unable to load attendance.';


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

                $('#loadAttendanceBtn')
                    .prop('disabled', false)
                    .text('Load Attendance');

            }

        });

    }



    /*
    |--------------------------------------------------------------------------
    | Display Attendance
    |--------------------------------------------------------------------------
    */

    function displayAttendance(employees)
    {

        const tbody =
            $('#attendanceTableBody');


        tbody.empty();


        /*
        |--------------------------------------------------------------------------
        | No employees
        |--------------------------------------------------------------------------
        */

        if (!employees.length) {

            tbody.html(`

                <tr>

                    <td
                        colspan="9"
                        class="text-center text-muted py-4"
                    >

                        No employees found.

                    </td>

                </tr>

            `);

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | Loop employees
        |--------------------------------------------------------------------------
        */

        employees.forEach(
            function (employee, index) {


                const attendance =
                    employee.attendance;


                /*
                |--------------------------------------------------------------------------
                | Existing attendance values
                |--------------------------------------------------------------------------
                */

                const status =
                    attendance
                        ? attendance.status
                        : '';


                const checkIn =
                    attendance
                        ? attendance.check_in || ''
                        : '';


                const checkOut =
                    attendance
                        ? attendance.check_out || ''
                        : '';


                const remarks =
                    attendance
                        ? attendance.remarks || ''
                        : '';


                /*
                |--------------------------------------------------------------------------
                | Detect automatically generated approved leave
                |--------------------------------------------------------------------------
                |
                | When attendance.id is null but status is leave,
                | this is the Leave coming from the approved leave
                | table rather than an attendance record.
                |
                */

                const isApprovedLeave =
                    attendance &&
                    attendance.status === 'leave' &&
                    attendance.id === null;


                /*
                |--------------------------------------------------------------------------
                | Status HTML
                |--------------------------------------------------------------------------
                */

                let statusHtml = '';


                if (isApprovedLeave) {

                    statusHtml = `

                        <div>

                            <span class="badge bg-info text-dark">

                                Leave

                            </span>

                        </div>

                        <div class="small text-muted mt-1">

                            Approved leave

                        </div>

                    `;

                } else {

                    statusHtml = `

                        <select
                            class="form-select attendance-status"
                            data-employee-id="${employee.id}"
                        >

                            <option value="">

                                Select

                            </option>


                            <option
                                value="present"
                                ${status === 'present'
                                    ? 'selected'
                                    : ''}
                            >

                                Present

                            </option>


                            <option
                                value="absent"
                                ${status === 'absent'
                                    ? 'selected'
                                    : ''}
                            >

                                Absent

                            </option>


                            <option
                                value="half_day"
                                ${status === 'half_day'
                                    ? 'selected'
                                    : ''}
                            >

                                Half Day

                            </option>


                            <option
                                value="leave"
                                ${status === 'leave'
                                    ? 'selected'
                                    : ''}
                            >

                                Leave

                            </option>

                        </select>

                    `;

                }


                /*
                |--------------------------------------------------------------------------
                | Save Button
                |--------------------------------------------------------------------------
                */

                let saveButton = '';


                if (isApprovedLeave) {

                    saveButton = `

                        <span class="badge bg-secondary">

                            No Action

                        </span>

                    `;

                } else {

                    saveButton = `

                        <button
                            type="button"
                            class="btn btn-sm btn-primary saveAttendanceBtn"
                            data-employee-id="${employee.id}"
                        >

                            Save

                        </button>

                    `;

                }


                /*
                |--------------------------------------------------------------------------
                | Add Row
                |--------------------------------------------------------------------------
                */

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
                                    employee.employee_id
                                )}

                            </strong>

                        </td>


                        <!-- Employee Name -->

                        <td>

                            ${escapeHtml(
                                employee.first_name
                            )}

                            ${escapeHtml(
                                employee.last_name
                            )}

                        </td>


                        <!-- Department -->

                        <td>

                            ${escapeHtml(
                                employee.department || ''
                            )}

                        </td>


                        <!-- Status -->

                        <td>

                            ${statusHtml}

                        </td>


                        <!-- Check In -->

                        <td>

                            <input
                                type="time"
                                class="form-control attendance-check-in"
                                data-employee-id="${employee.id}"
                                value="${escapeHtml(checkIn)}"
                            >

                        </td>


                        <!-- Check Out -->

                        <td>

                            <input
                                type="time"
                                class="form-control attendance-check-out"
                                data-employee-id="${employee.id}"
                                value="${escapeHtml(checkOut)}"
                            >

                        </td>


                        <!-- Remarks -->

                        <td>

                            <input
                                type="text"
                                class="form-control attendance-remarks"
                                data-employee-id="${employee.id}"
                                value="${escapeHtml(remarks)}"
                                placeholder="Optional"
                            >

                        </td>


                        <!-- Save -->

                        <td>

                            ${saveButton}

                        </td>

                    </tr>

                `);


                /*
                |--------------------------------------------------------------------------
                | Apply initial state
                |--------------------------------------------------------------------------
                */

                applyAttendanceState(
                    employee.id,
                    status,
                    isApprovedLeave
                );

            }
        );

    }



    /*
    |--------------------------------------------------------------------------
    | Attendance Status Change
    |--------------------------------------------------------------------------
    */

    $(document).on(
        'change',
        '.attendance-status',
        function () {

            const status =
                $(this).val();


            const employeeId =
                $(this).data('employee-id');


            applyAttendanceState(
                employeeId,
                status,
                false
            );

        }
    );



    /*
    |--------------------------------------------------------------------------
    | Apply Attendance State
    |--------------------------------------------------------------------------
    */

    function applyAttendanceState(
        employeeId,
        status,
        isApprovedLeave = false
    )
    {

        const checkIn =
            $(
                `.attendance-check-in[data-employee-id="${employeeId}"]`
            );


        const checkOut =
            $(
                `.attendance-check-out[data-employee-id="${employeeId}"]`
            );


        const remarks =
            $(
                `.attendance-remarks[data-employee-id="${employeeId}"]`
            );


        const statusSelect =
            $(
                `.attendance-status[data-employee-id="${employeeId}"]`
            );


        const saveButton =
            $(
                `.saveAttendanceBtn[data-employee-id="${employeeId}"]`
            );


        /*
        |--------------------------------------------------------------------------
        | Approved Leave
        |--------------------------------------------------------------------------
        */

        if (isApprovedLeave) {

            checkIn
                .val('')
                .prop('disabled', true);


            checkOut
                .val('')
                .prop('disabled', true);


            remarks
                .prop('disabled', true);


            statusSelect
                .prop('disabled', true);


            saveButton
                .prop('disabled', true);


            return;
        }


        /*
        |--------------------------------------------------------------------------
        | Enable normal controls
        |--------------------------------------------------------------------------
        */

        statusSelect
            .prop('disabled', false);


        remarks
            .prop('disabled', false);


        saveButton
            .prop('disabled', false);


        /*
        |--------------------------------------------------------------------------
        | Absent / Leave
        |--------------------------------------------------------------------------
        */

        if (
            status === 'absent' ||
            status === 'leave'
        ) {

            checkIn
                .val('')
                .prop('disabled', true);


            checkOut
                .val('')
                .prop('disabled', true);

        }


        /*
        |--------------------------------------------------------------------------
        | Present / Half Day
        |--------------------------------------------------------------------------
        */

        else if (
            status === 'present' ||
            status === 'half_day'
        ) {

            checkIn
                .prop('disabled', false);


            checkOut
                .prop('disabled', false);

        }


        /*
        |--------------------------------------------------------------------------
        | No Status
        |--------------------------------------------------------------------------
        */

        else {

            checkIn
                .val('')
                .prop('disabled', false);


            checkOut
                .val('')
                .prop('disabled', false);

        }

    }



    /*
    |--------------------------------------------------------------------------
    | Save Attendance
    |--------------------------------------------------------------------------
    */

    $(document).on(
        'click',
        '.saveAttendanceBtn',
        function () {

            const button =
                $(this);


            const employeeId =
                button.data('employee-id');


            const date =
                $('#attendanceDate').val();


            /*
            |--------------------------------------------------------------------------
            | Get row values
            |--------------------------------------------------------------------------
            */

            const status =
                $(
                    `.attendance-status[data-employee-id="${employeeId}"]`
                ).val();


            const checkIn =
                $(
                    `.attendance-check-in[data-employee-id="${employeeId}"]`
                ).val();


            const checkOut =
                $(
                    `.attendance-check-out[data-employee-id="${employeeId}"]`
                ).val();


            const remarks =
                $(
                    `.attendance-remarks[data-employee-id="${employeeId}"]`
                ).val();


            /*
            |--------------------------------------------------------------------------
            | Validate Date
            |--------------------------------------------------------------------------
            */

            if (!date) {

                showMessage(
                    'Please select attendance date.',
                    'danger'
                );

                return;
            }


            /*
            |--------------------------------------------------------------------------
            | Validate Status
            |--------------------------------------------------------------------------
            */

            if (!status) {

                showMessage(
                    'Please select attendance status.',
                    'danger'
                );

                return;
            }


            /*
            |--------------------------------------------------------------------------
            | Present / Half Day Validation
            |--------------------------------------------------------------------------
            */

            if (
                status === 'present' ||
                status === 'half_day'
            ) {

                if (!checkIn) {

                    showMessage(
                        'Check-in time is required.',
                        'danger'
                    );

                    return;
                }


                if (!checkOut) {

                    showMessage(
                        'Check-out time is required.',
                        'danger'
                    );

                    return;
                }

            }


            /*
            |--------------------------------------------------------------------------
            | Absent / Leave Validation
            |--------------------------------------------------------------------------
            */

            if (
                status === 'absent' ||
                status === 'leave'
            ) {

                if (
                    checkIn ||
                    checkOut
                ) {

                    showMessage(
                        'Check-in and check-out must be empty for Absent or Leave.',
                        'danger'
                    );

                    return;
                }

            }


            /*
            |--------------------------------------------------------------------------
            | Check-in / Check-out Time
            |--------------------------------------------------------------------------
            */

            if (
                checkIn &&
                checkOut &&
                checkOut <= checkIn
            ) {

                showMessage(
                    'Check-out must be later than check-in.',
                    'danger'
                );

                return;
            }


            /*
            |--------------------------------------------------------------------------
            | AJAX Save
            |--------------------------------------------------------------------------
            */

            $.ajax({

                url: '../../ajax/attendence.php',

                type: 'POST',

                dataType: 'json',

                data: {

                    action: 'save',

                    employee_id:
                        employeeId,

                    attendance_date:
                        date,

                    status:
                        status,

                    check_in:
                        checkIn,

                    check_out:
                        checkOut,

                    remarks:
                        remarks

                },


                /*
                |--------------------------------------------------------------------------
                | Before Send
                |--------------------------------------------------------------------------
                */

                beforeSend: function () {

                    button
                        .prop('disabled', true)
                        .text('Saving...');

                },


                /*
                |--------------------------------------------------------------------------
                | Success
                |--------------------------------------------------------------------------
                */

                success: function (response) {

                    if (response.success) {

                        showMessage(
                            response.message,
                            'success'
                        );


                        /*
                        |--------------------------------------------------------------------------
                        | Reload attendance
                        |--------------------------------------------------------------------------
                        |
                        | This is useful because after saving,
                        | the row may now contain a real attendance ID.
                        |
                        */

                        loadAttendance();

                    } else {

                        showMessage(
                            response.message ||
                            'Unable to save attendance.',
                            'danger'
                        );

                    }

                },


                /*
                |--------------------------------------------------------------------------
                | Error
                |--------------------------------------------------------------------------
                */

                error: function (xhr) {

                    let message =
                        'Unable to save attendance.';


                    /*
                    |--------------------------------------------------------------------------
                    | Validation errors
                    |--------------------------------------------------------------------------
                    */

                    if (
                        xhr.responseJSON &&
                        xhr.responseJSON.errors
                    ) {

                        const errors =
                            xhr.responseJSON.errors;


                        const firstField =
                            Object.keys(errors)[0];


                        if (firstField) {

                            message =
                                Array.isArray(
                                    errors[firstField]
                                )
                                    ? errors[firstField][0]
                                    : errors[firstField];

                        }

                    }


                    /*
                    |--------------------------------------------------------------------------
                    | General error
                    |--------------------------------------------------------------------------
                    */

                    else if (
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


                    /*
                    |--------------------------------------------------------------------------
                    | Approved Leave
                    |--------------------------------------------------------------------------
                    |
                    | If backend catches an approved leave
                    | attempt, reload the table so the UI
                    | immediately reflects Leave.
                    |
                    */

                    if (
                        xhr.responseJSON &&
                        xhr.responseJSON.leave
                    ) {

                        loadAttendance();

                    }

                },


                /*
                |--------------------------------------------------------------------------
                | Complete
                |--------------------------------------------------------------------------
                */

                complete: function () {

                    button
                        .prop('disabled', false)
                        .text('Save');

                }

            });

        }
    );



    /*
    |--------------------------------------------------------------------------
    | Loading State
    |--------------------------------------------------------------------------
    */

    function showLoading()
    {

        $('#attendanceTableBody').html(`

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
    | Show Message
    |--------------------------------------------------------------------------
    */

    function showMessage(
        message,
        type
    )
    {

        $('#attendanceMessage')

            .removeClass(
                'd-none alert-success alert-danger alert-warning'
            )

            .addClass(
                'alert-' + type
            )

            .text(message);


        /*
        |--------------------------------------------------------------------------
        | Hide success message
        |--------------------------------------------------------------------------
        */

        if (type === 'success') {

            setTimeout(
                function () {

                    $('#attendanceMessage')
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
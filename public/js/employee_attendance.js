// =========================================================
// EMPLOYEE TODAY ATTENDANCE
// =========================================================

$(document).ready(function () {


    // =====================================================
    // CONFIGURATION
    // =====================================================

    const AJAX_URL =
        '../../ajax/attendence.php';


    // =====================================================
    // INITIAL LOAD
    // =====================================================

    loadTodayAttendance();


    // =====================================================
    // LOAD TODAY ATTENDANCE
    // =====================================================

    function loadTodayAttendance() {

        showLoading();

        $.ajax({

            url: AJAX_URL,

            type: 'GET',

            data: {
                action: 'today_attendance'
            },

            dataType: 'json',


            // =================================================
            // SUCCESS
            // =================================================

            success: function (response) {


                // ---------------------------------------------
                // INVALID RESPONSE
                // ---------------------------------------------

                if (!response) {

                    showMessage(
                        'Unable to load attendance.',
                        'danger'
                    );

                    return;
                }

                console.log(response);
                


                // ---------------------------------------------
                // API ERROR
                // ---------------------------------------------

                if (!response.success) {

                    showMessage(
                        response.message ||
                        'Unable to load attendance.',
                        'danger'
                    );

                    showNoAttendance();

                    return;
                }


                // ---------------------------------------------
                // EMPLOYEE
                // ---------------------------------------------

                const employee =
                    response.employee || null;


                if (!employee) {

                    showMessage(
                        'Employee information not found.',
                        'danger'
                    );

                    showNoAttendance();

                    return;
                }


                // ---------------------------------------------
                // ATTENDANCE
                // ---------------------------------------------

                const attendance =
                    employee.attendance || null;


                // ---------------------------------------------
                // LEAVE
                // ---------------------------------------------

                const leave =
                    employee.leave || null;


                // ---------------------------------------------
                // EMPLOYEE ENCRYPTED ID
                // ---------------------------------------------

                const encryptedEmployeeId =
                    employee.encrypt_id || '';


                if (encryptedEmployeeId) {

                    $('#attendanceHistoryLink').attr(
                        'href',
                        '../../admin/attendances_history.php?id=' +
                        encodeURIComponent(
                            encryptedEmployeeId
                        )
                    );
                }


                // ---------------------------------------------
                // DATE
                // ---------------------------------------------

                setAttendanceDate(
                    response.date || ''
                );


                // =================================================
                // CHECK ATTENDANCE
                // =================================================

                if (attendance) {

                    /*
                    |--------------------------------------------------------------------------
                    | Attendance Found
                    |--------------------------------------------------------------------------
                    */

                    displayAttendance(
                        attendance
                    );

                    return;
                }


                // =================================================
                // CHECK LEAVE
                // =================================================

                if (leave) {

                    /*
                    |--------------------------------------------------------------------------
                    | Attendance Not Found
                    | Approved Leave Found
                    |--------------------------------------------------------------------------
                    */

                    displayLeave(
                        leave
                    );

                    return;
                }


                // =================================================
                // NOTHING FOUND
                // =================================================

                showNoAttendance();

            },


            // =================================================
            // AJAX ERROR
            // =================================================

            error: function (xhr) {

                console.error(
                    'Today Attendance AJAX Error:',
                    xhr.status,
                    xhr.responseText
                );


                showMessage(
                    'Unable to load today attendance.',
                    'danger'
                );


                showNoAttendance();

            }

        });

    }


    // =========================================================
    // DISPLAY ATTENDANCE
    // =========================================================

    function displayAttendance(attendance) {


        // ---------------------------------------------
        // HIDE LOADING
        // ---------------------------------------------

        $('#attendanceLoading')
            .addClass('d-none');


        // ---------------------------------------------
        // HIDE NO ATTENDANCE
        // ---------------------------------------------

        $('#noAttendance')
            .addClass('d-none');


        // ---------------------------------------------
        // SHOW CONTENT
        // ---------------------------------------------

        $('#attendanceContent')
            .removeClass('d-none');


        // ---------------------------------------------
        // DATE
        // ---------------------------------------------

        setAttendanceDate(
            attendance.attendance_date ||
            attendance.date ||
            ''
        );


        // ---------------------------------------------
        // STATUS
        // ---------------------------------------------

        setAttendanceStatus(
            attendance.status || ''
        );


        // ---------------------------------------------
        // CHECK IN
        // ---------------------------------------------

        $('#checkInTime').text(
            formatTime(
                attendance.check_in
            )
        );


        // ---------------------------------------------
        // CHECK OUT
        // ---------------------------------------------

        $('#checkOutTime').text(
            formatTime(
                attendance.check_out
            )
        );


        // ---------------------------------------------
        // WORKING HOURS
        // ---------------------------------------------

        $('#workingHours').text(

            attendance.working_hours ||

            calculateWorkingHours(
                attendance.check_in,
                attendance.check_out
            )

        );


        // ---------------------------------------------
        // REMARKS
        // ---------------------------------------------

        $('#attendanceRemarks').text(
            attendance.remarks ||
            '--'
        );

    }


    // =========================================================
    // DISPLAY LEAVE
    // =========================================================

    function displayLeave(leave) {


        // ---------------------------------------------
        // HIDE LOADING
        // ---------------------------------------------

        $('#attendanceLoading')
            .addClass('d-none');


        // ---------------------------------------------
        // HIDE NO ATTENDANCE
        // ---------------------------------------------

        $('#noAttendance')
            .addClass('d-none');


        // ---------------------------------------------
        // SHOW CONTENT
        // ---------------------------------------------

        $('#attendanceContent')
            .removeClass('d-none');


        // ---------------------------------------------
        // DATE
        // ---------------------------------------------

        setAttendanceDate(
            leave.start_date || ''
        );


        // ---------------------------------------------
        // STATUS
        // ---------------------------------------------

        setAttendanceStatus(
            'leave'
        );


        // ---------------------------------------------
        // CHECK IN
        // ---------------------------------------------

        $('#checkInTime')
            .text('--');
        $('#checkIn')
            .hide();


        // ---------------------------------------------
        // CHECK OUT
        // ---------------------------------------------

        $('#checkOutTime')
            .text('--');
        $('#checkOut')
            .hide();


        // ---------------------------------------------
        // WORKING HOURS
        // ---------------------------------------------

        $('#workingHours')
            .text('--');
        $('#WorkHours')
            .hide();
        


        // ---------------------------------------------
        // REMARKS
        // ---------------------------------------------

        let leaveRemark = '';


        if (leave.leave_type) {

            leaveRemark =
                'Leave Type: ' +
                formatLeaveType(
                    leave.leave_type
                );

        }


        if (leave.reason) {

            if (leaveRemark) {

                leaveRemark +=
                    ' | ';

            }

            leaveRemark +=
                'Reason: ' +
                leave.reason;
        }


        if (!leaveRemark) {

            leaveRemark =
                'Employee is on approved leave.';

        }


        $('#attendanceRemarks')
            .text(leaveRemark);

    }


    // =========================================================
    // FORMAT LEAVE TYPE
    // =========================================================

    function formatLeaveType(type) {

        if (!type) {

            return '--';

        }


        return String(type)
            .replace(/_/g, ' ')
            .replace(/\b\w/g, function (character) {

                return character.toUpperCase();

            });

    }


    // =========================================================
    // SET ATTENDANCE DATE
    // =========================================================

    function setAttendanceDate(date) {


        // ---------------------------------------------
        // DEFAULT DATE
        // ---------------------------------------------

        const dateObject = date
            ? new Date(
                String(date).replace(' ', 'T')
            )
            : new Date();


        // ---------------------------------------------
        // INVALID DATE
        // ---------------------------------------------

        if (
            isNaN(
                dateObject.getTime()
            )
        ) {

            $('#attendanceDate')
                .text('Today');

            return;

        }


        // ---------------------------------------------
        // DISPLAY DATE
        // ---------------------------------------------

        $('#attendanceDate').text(

            'Today - ' +

            dateObject.toLocaleDateString(
                'en-IN',
                {
                    weekday: 'long',
                    day: '2-digit',
                    month: 'long',
                    year: 'numeric'
                }
            )

        );

    }


    // =========================================================
    // SET ATTENDANCE STATUS
    // =========================================================

    function setAttendanceStatus(status) {


        const badge =
            $('#attendanceStatus');


        // ---------------------------------------------
        // RESET CLASSES
        // ---------------------------------------------

        badge.removeClass(

            'bg-success ' +
            'bg-danger ' +
            'bg-warning ' +
            'bg-info ' +
            'bg-secondary ' +
            'text-dark'

        );


        // ---------------------------------------------
        // NORMALIZE STATUS
        // ---------------------------------------------

        const normalizedStatus =
            String(status)
                .toLowerCase()
                .trim();


        // ---------------------------------------------
        // STATUS
        // ---------------------------------------------

        switch (normalizedStatus) {


            // -----------------------------------------
            // PRESENT
            // -----------------------------------------

            case 'present':

                badge
                    .text('Present')
                    .addClass('bg-success');

                break;


            // -----------------------------------------
            // ABSENT
            // -----------------------------------------

            case 'absent':

                badge
                    .text('Absent')
                    .addClass('bg-danger');

                break;


            // -----------------------------------------
            // LATE
            // -----------------------------------------

            case 'late':

                badge
                    .text('Late')
                    .addClass(
                        'bg-warning text-dark'
                    );

                break;


            // -----------------------------------------
            // HALF DAY
            // -----------------------------------------

            case 'half_day':

            case 'half day':

                badge
                    .text('Half Day')
                    .addClass(
                        'bg-warning text-dark'
                    );

                break;


            // -----------------------------------------
            // LEAVE
            // -----------------------------------------

            case 'leave':

                badge
                    .text('Leave')
                    .addClass('bg-info');

                break;


            // -----------------------------------------
            // DEFAULT
            // -----------------------------------------

            default:

                badge
                    .text(
                        status || 'Unknown'
                    )
                    .addClass(
                        'bg-secondary'
                    );

                break;

        }

    }


    // =========================================================
    // FORMAT TIME
    // =========================================================

    function formatTime(time) {


        // ---------------------------------------------
        // EMPTY TIME
        // ---------------------------------------------

        if (

            !time ||

            time ===
            '0000-00-00 00:00:00' ||

            time ===
            '00:00:00'

        ) {

            return '--';

        }


        // ---------------------------------------------
        // TIME ONLY
        // ---------------------------------------------

        if (

            typeof time === 'string' &&

            /^\d{2}:\d{2}(:\d{2})?$/
                .test(time)

        ) {


            const parts =
                time.split(':');


            let hours =
                parseInt(
                    parts[0],
                    10
                );


            const minutes =
                parts[1];


            const period =
                hours >= 12
                    ? 'PM'
                    : 'AM';


            hours =
                hours % 12 || 12;


            return (

                String(hours)
                    .padStart(2, '0') +

                ':' +

                minutes +

                ' ' +

                period

            );

        }


        // ---------------------------------------------
        // DATETIME
        // ---------------------------------------------

        const date =
            new Date(

                String(time)
                    .replace(
                        ' ',
                        'T'
                    )

            );


        // ---------------------------------------------
        // INVALID DATE
        // ---------------------------------------------

        if (
            isNaN(
                date.getTime()
            )
        ) {

            return time;

        }


        // ---------------------------------------------
        // FORMAT DATETIME
        // ---------------------------------------------

        return date.toLocaleTimeString(
            'en-IN',
            {
                hour: '2-digit',
                minute: '2-digit',
                hour12: true
            }
        );

    }


    // =========================================================
    // CALCULATE WORKING HOURS
    // =========================================================

    function calculateWorkingHours(
        checkIn,
        checkOut
    ) {


        // ---------------------------------------------
        // MISSING TIME
        // ---------------------------------------------

        if (
            !checkIn ||
            !checkOut
        ) {

            return '--';

        }


        // ---------------------------------------------
        // CREATE DATE
        // ---------------------------------------------

        const start =
            new Date(
                `1970-01-01T${checkIn}`
            );


        const end =
            new Date(
                `1970-01-01T${checkOut}`
            );


        // ---------------------------------------------
        // INVALID TIME
        // ---------------------------------------------

        if (

            isNaN(
                start.getTime()
            ) ||

            isNaN(
                end.getTime()
            )

        ) {

            return '--';

        }


        // ---------------------------------------------
        // DIFFERENCE
        // ---------------------------------------------

        const difference =
            end.getTime() -
            start.getTime();


        // ---------------------------------------------
        // INVALID DIFFERENCE
        // ---------------------------------------------

        if (
            difference <= 0
        ) {

            return '--';

        }


        // ---------------------------------------------
        // TOTAL MINUTES
        // ---------------------------------------------

        const totalMinutes =
            Math.floor(
                difference / 60000
            );


        // ---------------------------------------------
        // HOURS
        // ---------------------------------------------

        const hours =
            Math.floor(
                totalMinutes / 60
            );


        // ---------------------------------------------
        // MINUTES
        // ---------------------------------------------

        const minutes =
            totalMinutes % 60;


        // ---------------------------------------------
        // RETURN
        // ---------------------------------------------

        return (

            String(hours)
                .padStart(2, '0') +

            ':' +

            String(minutes)
                .padStart(2, '0')

        );

    }


    // =========================================================
    // SHOW LOADING
    // =========================================================

    function showLoading() {


        // ---------------------------------------------
        // SHOW LOADING
        // ---------------------------------------------

        $('#attendanceLoading')
            .removeClass('d-none');


        // ---------------------------------------------
        // HIDE CONTENT
        // ---------------------------------------------

        $('#attendanceContent')
            .addClass('d-none');


        // ---------------------------------------------
        // HIDE NO ATTENDANCE
        // ---------------------------------------------

        $('#noAttendance')
            .addClass('d-none');


        // ---------------------------------------------
        // RESET STATUS
        // ---------------------------------------------

        $('#attendanceStatus')

            .removeClass(

                'bg-success ' +
                'bg-danger ' +
                'bg-warning ' +
                'bg-info'

            )

            .addClass(
                'bg-secondary'
            )

            .text(
                'Loading...'
            );

    }


    // =========================================================
    // SHOW NO ATTENDANCE
    // =========================================================

    function showNoAttendance() {


        // ---------------------------------------------
        // HIDE LOADING
        // ---------------------------------------------

        $('#attendanceLoading')
            .addClass('d-none');


        // ---------------------------------------------
        // HIDE CONTENT
        // ---------------------------------------------

        $('#attendanceContent')
            .addClass('d-none');


        // ---------------------------------------------
        // SHOW NO ATTENDANCE
        // ---------------------------------------------

        $('#noAttendance')
            .removeClass('d-none');


        // ---------------------------------------------
        // STATUS
        // ---------------------------------------------

        $('#attendanceStatus')

            .removeClass(

                'bg-success ' +
                'bg-danger ' +
                'bg-warning ' +
                'bg-info'

            )

            .addClass(
                'bg-secondary'
            )

            .text(
                'Not Marked'
            );

    }


    // =========================================================
    // SHOW MESSAGE
    // =========================================================

    function showMessage(
        message,
        type = 'danger'
    ) {


        const element =
            $('#attendanceMessage');


        // ---------------------------------------------
        // SHOW MESSAGE
        // ---------------------------------------------

        element

            .removeClass(

                'alert-success ' +
                'alert-danger ' +
                'alert-warning ' +
                'alert-info ' +
                'd-none'

            )

            .addClass(
                'alert-' + type
            )

            .text(
                message
            );

    }


});
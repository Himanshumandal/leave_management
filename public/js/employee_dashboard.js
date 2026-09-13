$(document).ready(function () {

    // =====================================================
    // CONFIGURATION
    // =====================================================

    const AJAX_URL = '../../ajax/dashboard.php';


    // =====================================================
    // CHART INSTANCES
    // =====================================================

    let attendanceTrendChart = null;
    let attendanceStatusChart = null;
    let workingHoursChart = null;


    // =====================================================
    // INITIAL LOAD
    // =====================================================

    loadDashboard();

    loadAttendanceTrend('week');

    loadAttendanceStatus('');

    loadWorkingHours('week');


    // =====================================================
    // FILTER EVENTS
    // =====================================================

    $('#attendanceTrendFilter').on('change', function () {

        const period = $(this).val() || 'week';

        loadAttendanceTrend(period);

    });


    $('#attendanceStatusFilter').on('change', function () {

        const status = $(this).val() || '';

        loadAttendanceStatus(status);

    });


    $('#workingHoursFilter').on('change', function () {

        const period = $(this).val() || 'week';

        loadWorkingHours(period);

    });


    // =====================================================
    // MAIN DASHBOARD
    // =====================================================

    function loadDashboard() {

        setDashboardLoading();

        hideDashboardMessage();


        $.ajax({

            url: AJAX_URL,

            type: 'GET',

            data: {
                action: 'dashboard'
            },

            dataType: 'json',


            success: function (response) {

                console.log('Dashboard Response:', response);


                if (
                    !response ||
                    response.success !== true
                ) {

                    showDashboardMessage(
                        response?.message ||
                        'Unable to load dashboard.',
                        'danger'
                    );

                    setDashboardEmptyState();

                    return;
                }


                const data =
                    response.data || {};


                // =================================================
                // EMPLOYEE
                // =================================================

                updateEmployee(
                    data.employee || {}
                );


                // =================================================
                // STATISTICS
                // =================================================

                updateStatistics(
                    data.statistics || {}
                );


                // =================================================
                // TODAY
                // =================================================

                updateTodayAttendance(
                    data.today || {}
                );


                // =================================================
                // OPTIONAL:
                // If dashboard API already sends attendance_overview,
                // use it as fallback for attendance chart.
                // =================================================

                if (
                    data.attendance_overview &&
                    Array.isArray(
                        data.attendance_overview.labels
                    )
                ) {

                    renderAttendanceTrend(
                        data.attendance_overview
                    );

                }


                hideDashboardMessage();

            },


            error: function (xhr) {

                console.error(
                    'Dashboard AJAX Error:',
                    xhr.status,
                    xhr.responseText
                );


                setDashboardEmptyState();


                showDashboardMessage(
                    'Unable to load dashboard information.',
                    'danger'
                );

            }

        });

    }


    // =====================================================
    // EMPLOYEE INFORMATION
    // =====================================================

    function updateEmployee(employee) {

        setValue(
            '#employeeName',
            employee.name,
            '',
            'Employee'
        );


        setValue(
            '#employeeId',
            employee.employee_id,
            '',
            'Not available'
        );


        setValue(
            '#department',
            employee.department,
            '',
            'Not assigned'
        );


        setValue(
            '#designation',
            employee.designation,
            '',
            'Not assigned'
        );


        setStatusBadge(
            '#employeeStatus',
            employee.status || 'Unknown'
        );

    }


    // =====================================================
    // STATISTICS
    // =====================================================

    function updateStatistics(statistics) {

        setValue(
            '#attendancePercentage',
            statistics.attendance_percentage,
            '%',
            '0%'
        );


        setValue(
            '#presentDays',
            statistics.present_days,
            '',
            '0'
        );


        setValue(
            '#leaveBalance',
            statistics.leave_balance,
            '',
            '0'
        );


        setValue(
            '#pendingRequests',
            statistics.pending_requests,
            '',
            '0'
        );

    }


    // =====================================================
    // TODAY ATTENDANCE
    // =====================================================

    function updateTodayAttendance(today) {

        const status =
            today.status || 'Not Marked';


        setStatusBadge(
            '#todayAttendanceStatus',
            status
        );


        const normalizedStatus =
            String(status)
                .trim()
                .toLowerCase();


        // =================================================
        // LEAVE TODAY
        // =================================================

        if (normalizedStatus === 'leave') {

            $('#checkInTime')
                .text('Not applicable');


            $('#checkOutTime')
                .text('Not applicable');


            $('#workingHours')
                .text('Not applicable');


            $('#todayLeaveSection')
                .removeClass('d-none');


            setValue(
                '#todayLeaveType',
                today.leave_type,
                '',
                'Not available'
            );


            /*
             * Your API response currently contains:
             *
             * remark: "Ok fine !!"
             *
             * It does NOT contain "reason".
             */

            setValue(
                '#todayLeaveReason',
                today.reason,
                '',
                'Not available'
            );


            setValue(
                '#todayLeaveRemark',
                today.remark,
                '',
                'No remark'
            );


            return;

        }


        // =================================================
        // NOT ON LEAVE
        // =================================================

        $('#todayLeaveSection')
            .addClass('d-none');


        setValue(
            '#checkInTime',
            formatTime(today.check_in),
            '',
            'Not checked in'
        );


        setValue(
            '#checkOutTime',
            formatTime(today.check_out),
            '',
            'Not checked out'
        );


        setValue(
            '#workingHours',
            today.working_hours,
            '',
            'Not available'
        );

    }


    // =====================================================
    // 1. ATTENDANCE TREND
    // =====================================================

    function loadAttendanceTrend(period) {

        showChartLoading(
            '#attendanceChartMessage',
            'Loading attendance trend...'
        );


        $.ajax({

            url: AJAX_URL,

            type: 'GET',

            data: {

                action: 'attendance_trend',

                period: period

            },

            dataType: 'json',


            success: function (response) {

                console.log(
                    'Attendance Trend Response:',
                    response
                );


                if (
                    !response ||
                    response.success !== true
                ) {

                    destroyAttendanceTrendChart();


                    showChartMessage(
                        '#attendanceChartMessage',
                        response?.message ||
                        'Unable to load attendance trend.'
                    );

                    return;
                }


                const data =
                    response.data || {};


                renderAttendanceTrend(data);

            },


            error: function (xhr) {

                console.error(
                    'Attendance Trend Error:',
                    xhr.status,
                    xhr.responseText
                );


                destroyAttendanceTrendChart();


                showChartMessage(
                    '#attendanceChartMessage',
                    'Unable to load attendance trend.'
                );

            }

        });

    }


function renderAttendanceTrend(data) {

    const canvas = document.getElementById(
        'attendanceTrendChart'
    );

    if (!canvas) {
        console.error('attendanceTrendChart canvas not found.');
        return;
    }


    // =====================================================
    // API DATA
    // =====================================================

    const labels = Array.isArray(data.labels)
        ? data.labels
        : [];

    const chartData = data.data || {};


    const present = Array.isArray(chartData.present)
        ? chartData.present
        : [];

    const absent = Array.isArray(chartData.absent)
        ? chartData.absent
        : [];

    const halfDay = Array.isArray(chartData.half_day)
        ? chartData.half_day
        : [];

    const leave = Array.isArray(chartData.leave)
        ? chartData.leave
        : [];


    // =====================================================
    // VALIDATION
    // =====================================================

    if (!labels.length) {

        destroyAttendanceTrendChart();

        showChartMessage(
            '#attendanceChartMessage',
            'No attendance data available.'
        );

        return;
    }


    // =====================================================
    // CLEAR MESSAGE
    // =====================================================

    clearChartMessage(
        '#attendanceChartMessage'
    );


    // =====================================================
    // DESTROY OLD CHART
    // =====================================================

    destroyAttendanceTrendChart();


    // =====================================================
    // FIND MAX VALUE
    // =====================================================

    const allValues = [
        ...present,
        ...absent,
        ...halfDay,
        ...leave
    ]
    .map(Number)
    .filter(Number.isFinite);


    const highestValue = allValues.length
        ? Math.max(...allValues)
        : 1;


    // Keep some space above highest point
    const yMax = highestValue <= 1
        ? 1.2
        : Math.ceil(highestValue * 1.2);


    // =====================================================
    // CREATE CHART
    // =====================================================

    attendanceTrendChart = new Chart(canvas, {

        type: 'line',

        data: {

            labels: labels,

            datasets: [

                {
                    label: 'Present',

                    data: normalizeArray(
                        present,
                        labels.length
                    ),

                    borderWidth: 2,

                    tension: 0,

                    stepped: true,

                    fill: false,

                    pointRadius: 4,

                    pointHoverRadius: 6
                },


                {
                    label: 'Absent',

                    data: normalizeArray(
                        absent,
                        labels.length
                    ),

                    borderWidth: 2,

                    tension: 0,

                    stepped: true,

                    fill: false,

                    pointRadius: 4,

                    pointHoverRadius: 6
                },


                {
                    label: 'Half Day',

                    data: normalizeArray(
                        halfDay,
                        labels.length
                    ),

                    borderWidth: 2,

                    tension: 0,

                    stepped: true,

                    fill: false,

                    pointRadius: 4,

                    pointHoverRadius: 6
                },


                {
                    label: 'Leave',

                    data: normalizeArray(
                        leave,
                        labels.length
                    ),

                    borderWidth: 2,

                    tension: 0,

                    stepped: true,

                    fill: false,

                    pointRadius: 4,

                    pointHoverRadius: 6
                }

            ]

        },


        options: {

            responsive: true,

            maintainAspectRatio: false,


            interaction: {

                mode: 'index',

                intersect: false

            },


            plugins: {

                legend: {

                    position: 'top',

                    labels: {

                        padding: 15,

                        usePointStyle: true

                    }

                }

            },


            scales: {

                x: {

                    ticks: {

                        autoSkip: true,

                        maxRotation: 0,

                        minRotation: 0

                    }

                },


                y: {

                    beginAtZero: true,

                    min: 0,

                    max: yMax,

                    ticks: {

                        stepSize: 1,

                        precision: 0

                    },

                    title: {

                        display: true,

                        text: 'Attendance'

                    }

                }

            }

        }

    });

}


    // =====================================================
    // 2. ATTENDANCE STATUS
    // =====================================================

    function loadAttendanceStatus(status) {



        showChartLoading(
            '#leaveChartMessage',
            'Loading attendance status...'
        );


        $.ajax({

            url: AJAX_URL,

            type: 'GET',

            data: {

                action: 'attendance_status',

                status: status

            },

            dataType: 'json',


            success: function (response) {

                console.log(
                    'Attendance Status Response:',
                    response
                );


                if (
                    !response ||
                    response.success !== true
                ) {

                    destroyAttendanceStatusChart();


                    showChartMessage(
                        '#leaveChartMessage',
                        response?.message ||
                        'Unable to load attendance status.'
                    );

                    return;
                }


                renderAttendanceStatus(
                    response.data || {}
                );

            },


            error: function (xhr) {

                console.error(
                    'Attendance Status Error:',
                    xhr.status,
                    xhr.responseText
                );


                destroyAttendanceStatusChart();


                showChartMessage(
                    '#leaveChartMessage',
                    'Unable to load attendance status.'
                );

            }

        });

    }


    // =====================================================
    // RENDER ATTENDANCE STATUS
    // =====================================================

    function renderAttendanceStatus(data) {

        const canvas =
            document.getElementById(
                'leaveStatusChart'
            );


        if (!canvas) {
            return;
        }


        const labels =
            Array.isArray(data.labels)
                ? data.labels
                : [];


        const values =
            Array.isArray(data.values)
                ? data.values
                : [];


        if (
            !labels.length ||
            !values.length
        ) {

            destroyAttendanceStatusChart();


            showChartMessage(
                '#leaveChartMessage',
                'No attendance status data found.'
            );

            return;
        }


        clearChartMessage(
            '#leaveChartMessage'
        );


        destroyAttendanceStatusChart();


        attendanceStatusChart =
            new Chart(canvas, {

                type: 'doughnut',

                data: {

                    labels: labels,

                    datasets: [

                        {

                            data: values,

                            borderWidth: 1

                        }

                    ]

                },


                options: {

                    responsive: true,

                    maintainAspectRatio: false,


                    plugins: {

                        legend: {

                            position: 'bottom'

                        }

                    }

                }

            });

    }


    // =====================================================
    // 3. WORKING HOURS
    // =====================================================

    function loadWorkingHours(period) {

        showChartLoading(
            '#workingHoursChartMessage',
            'Loading working hours...'
        );


        $.ajax({

            url: AJAX_URL,

            type: 'GET',

            data: {

                action: 'working_hours',

                period: period

            },

            dataType: 'json',


            success: function (response) {

                console.log(
                    'Working Hours Response:',
                    response
                );


                if (
                    !response ||
                    response.success !== true
                ) {

                    destroyWorkingHoursChart();


                    showChartMessage(
                        '#workingHoursChartMessage',
                        response?.message ||
                        'Unable to load working hours.'
                    );

                    return;
                }


                renderWorkingHours(
                    response.data || {}
                );

            },


            error: function (xhr) {

                console.error(
                    'Working Hours Error:',
                    xhr.status,
                    xhr.responseText
                );


                destroyWorkingHoursChart();


                showChartMessage(
                    '#workingHoursChartMessage',
                    'Unable to load working hours.'
                );

            }

        });

    }


    // =====================================================
    // RENDER WORKING HOURS
    // =====================================================

    function renderWorkingHours(data) {

        const canvas =
            document.getElementById(
                'workingHoursChart'
            );


        if (!canvas) {
            return;
        }


        const labels =
            Array.isArray(data.labels)
                ? data.labels
                : [];


        const values =
            Array.isArray(data.values)
                ? data.values
                : [];


        if (!labels.length) {

            destroyWorkingHoursChart();


            showChartMessage(
                '#workingHoursChartMessage',
                'No working hours records found for this period.'
            );

            return;
        }


        clearChartMessage(
            '#workingHoursChartMessage'
        );


        destroyWorkingHoursChart();


        workingHoursChart =
            new Chart(canvas, {

                type: 'bar',

                data: {

                    labels: labels,

                    datasets: [

                        {

                            label: 'Working Hours',

                            data: normalizeArray(
                                values,
                                labels.length
                            ),

                            borderWidth: 1

                        }

                    ]

                },


                options: {

                    responsive: true,

                    maintainAspectRatio: false,


                    plugins: {

                        legend: {

                            display: false

                        }

                    },


                    scales: {

                        y: {

                            beginAtZero: true,

                            title: {

                                display: true,

                                text: 'Hours'

                            }

                        }

                    }

                }

            });

    }


    // =====================================================
    // NORMALIZE ARRAY
    // =====================================================

    function normalizeArray(
        array,
        length
    ) {

        const result = [];


        for (
            let i = 0;
            i < length;
            i++
        ) {

            const value =
                array[i];


            if (
                value === null ||
                value === undefined ||
                value === ''
            ) {

                result.push(0);

            } else {

                result.push(
                    Number(value) || 0
                );

            }

        }


        return result;

    }


    // =====================================================
    // DESTROY ATTENDANCE TREND
    // =====================================================

    function destroyAttendanceTrendChart() {

        if (attendanceTrendChart) {

            attendanceTrendChart.destroy();

            attendanceTrendChart = null;

        }

    }


    // =====================================================
    // DESTROY ATTENDANCE STATUS
    // =====================================================

    function destroyAttendanceStatusChart() {

        if (attendanceStatusChart) {

            attendanceStatusChart.destroy();

            attendanceStatusChart = null;

        }

    }


    // =====================================================
    // DESTROY WORKING HOURS
    // =====================================================

    function destroyWorkingHoursChart() {

        if (workingHoursChart) {

            workingHoursChart.destroy();

            workingHoursChart = null;

        }

    }


    // =====================================================
    // VALUE HELPER
    // =====================================================

    function setValue(
        selector,
        value,
        suffix = '',
        emptyText = '--'
    ) {

        const element =
            $(selector);


        if (!element.length) {
            return;
        }


        if (
            value === null ||
            value === undefined ||
            value === ''
        ) {

            element.text(emptyText);

            return;
        }


        element.text(
            String(value) + suffix
        );

    }


    // =====================================================
    // TIME FORMAT
    // =====================================================

    function formatTime(time) {

        if (
            time === null ||
            time === undefined ||
            time === ''
        ) {

            return null;
        }


        return time;

    }


    // =====================================================
    // STATUS BADGE
    // =====================================================

    function setStatusBadge(
        selector,
        status
    ) {

        const element =
            $(selector);


        if (!element.length) {
            return;
        }


        const normalizedStatus =
            String(status)
                .trim()
                .toLowerCase();


        element.removeClass(
            [
                'bg-success',
                'bg-danger',
                'bg-warning',
                'text-dark',
                'bg-secondary',
                'bg-info',
                'bg-primary'
            ].join(' ')
        );


        let badgeClass =
            'bg-secondary';


        switch (normalizedStatus) {

            case 'present':
            case 'active':
            case 'approved':

                badgeClass =
                    'bg-success';

                break;


            case 'absent':
            case 'inactive':
            case 'rejected':

                badgeClass =
                    'bg-danger';

                break;


            case 'pending':
            case 'half day':
            case 'half_day':

                badgeClass =
                    'bg-warning text-dark';

                break;


            case 'leave':

                badgeClass =
                    'bg-info';

                break;


            case 'not marked':

                badgeClass =
                    'bg-secondary';

                break;

        }


        element
            .text(status)
            .addClass(badgeClass);

    }


    // =====================================================
    // CHART LOADING
    // =====================================================

    function showChartLoading(
        selector,
        message
    ) {

        $(selector)

            .removeClass('d-none')

            .removeClass(
                'alert-danger alert-info alert-warning'
            )

            .addClass('alert-info')

            .text(message);

    }


    // =====================================================
    // CHART MESSAGE
    // =====================================================

    function showChartMessage(
        selector,
        message
    ) {

        $(selector)

            .removeClass('d-none')

            .removeClass(
                'alert-danger alert-info alert-warning'
            )

            .addClass('alert-info')

            .text(message);

    }


    // =====================================================
    // CLEAR CHART MESSAGE
    // =====================================================

    function clearChartMessage(selector) {

        $(selector)

            .addClass('d-none')

            .text('');

    }


    // =====================================================
    // DASHBOARD MESSAGE
    // =====================================================

    function showDashboardMessage(
        message,
        type = 'danger'
    ) {

        $('#dashboardMessage')

            .removeClass(
                [
                    'd-none',
                    'alert-success',
                    'alert-danger',
                    'alert-warning',
                    'alert-info'
                ].join(' ')
            )

            .addClass(
                'alert-' + type
            )

            .text(message);

    }


    // =====================================================
    // HIDE DASHBOARD MESSAGE
    // =====================================================

    function hideDashboardMessage() {

        $('#dashboardMessage')

            .addClass('d-none')

            .text('');

    }


    // =====================================================
    // DASHBOARD LOADING
    // =====================================================

    function setDashboardLoading() {

        $('#employeeName')
            .text('Loading...');


        $('#attendancePercentage')
            .text('...');


        $('#presentDays')
            .text('...');


        $('#leaveBalance')
            .text('...');


        $('#pendingRequests')
            .text('...');


        $('#checkInTime')
            .text('Loading...');


        $('#checkOutTime')
            .text('Loading...');


        $('#workingHours')
            .text('Loading...');


        $('#employeeId')
            .text('Loading...');


        $('#department')
            .text('Loading...');


        $('#designation')
            .text('Loading...');


        setStatusBadge(
            '#todayAttendanceStatus',
            'Loading...'
        );


        setStatusBadge(
            '#employeeStatus',
            'Loading...'
        );

    }


    // =====================================================
    // DASHBOARD EMPTY STATE
    // =====================================================

    function setDashboardEmptyState() {

        $('#employeeName')
            .text('Employee');


        $('#attendancePercentage')
            .text('0%');


        $('#presentDays')
            .text('0');


        $('#leaveBalance')
            .text('0');


        $('#pendingRequests')
            .text('0');


        $('#checkInTime')
            .text('Not available');


        $('#checkOutTime')
            .text('Not available');


        $('#workingHours')
            .text('Not available');


        $('#employeeId')
            .text('Not available');


        $('#department')
            .text('Not assigned');


        $('#designation')
            .text('Not assigned');


        setStatusBadge(
            '#todayAttendanceStatus',
            'Not available'
        );


        setStatusBadge(
            '#employeeStatus',
            'Not available'
        );

    }

});

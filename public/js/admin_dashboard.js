// =========================================================
// ADMIN DASHBOARD
// =========================================================

$(document).ready(function () {


    // -----------------------------------------------------
    // CONFIGURATION
    // -----------------------------------------------------

    const AJAX_URL =
        '../../ajax/admin_dashboard.php';


    // -----------------------------------------------------
    // CHART INSTANCES
    // -----------------------------------------------------

    let attendanceOverviewChart = null;

    let todayAttendanceChart = null;

    let departmentEmployeeChart = null;


    // -----------------------------------------------------
    // INITIAL LOAD
    // -----------------------------------------------------

    loadDashboard();

    loadEmployees();

    loadDepartments();

    loadAttendanceOverview();

    loadTodayAttendance();

    loadDepartmentEmployees();



    // =====================================================
    // LOAD DASHBOARD STATISTICS
    // =====================================================

    function loadDashboard() {

        showStatisticsLoading();


        $.ajax({

            url: AJAX_URL,

            type: 'GET',

            data: {
                action: 'dashboard'
            },

            dataType: 'json',


            success: function (response) {

                if (
                    !response ||
                    !response.success
                ) {

                    showMessage(
                        response?.message ||
                        'Unable to load dashboard.',
                        'danger'
                    );

                    return;
                }


                const statistics =
                    response.statistics || {};


                $('#totalEmployees').text(
                    statistics.employees || 0
                );


                $('#pendingLeaves').text(
                    statistics.pending_leaves || 0
                );


                $('#totalDepartments').text(
                    statistics.departments || 0
                );


                $('#totalDesignations').text(
                    statistics.designations || 0
                );

            },


            error: function (xhr) {

                console.error(
                    'Dashboard AJAX Error:',
                    xhr.status,
                    xhr.responseText
                );


                showMessage(
                    'Unable to load dashboard.',
                    'danger'
                );

            }

        });

    }



    // =====================================================
    // LOAD EMPLOYEES
    // =====================================================

    function loadEmployees() {

        $.ajax({

            url: AJAX_URL,

            type: 'GET',

            data: {
                action: 'employees'
            },

            dataType: 'json',


            success: function (response) {

                if (
                    !response ||
                    !response.success
                ) {

                    console.error(
                        response?.message ||
                        'Unable to load employees.'
                    );

                    return;
                }


                const employees =
                    Array.isArray(
                        response.employees
                    )
                        ? response.employees
                        : [];


                const dropdown =
                    $('#attendanceEmployee');


                if (!dropdown.length) {
                    return;
                }


                dropdown.empty();


                dropdown.append(
                    $('<option>', {

                        value: '',

                        text: 'All Employees'

                    })
                );


                employees.forEach(
                    function (employee) {

                        dropdown.append(
                            $('<option>', {

                                value:
                                    employee.id,

                                text:
                                    employee.name

                            })
                        );

                    }
                );

            },


            error: function (xhr) {

                console.error(
                    'Employees AJAX Error:',
                    xhr.responseText
                );

            }

        });

    }



    // =====================================================
    // LOAD DEPARTMENTS
    // =====================================================

    function loadDepartments() {

        $.ajax({

            url: AJAX_URL,

            type: 'GET',

            data: {
                action: 'departments'
            },

            dataType: 'json',


            success: function (response) {

                if (
                    !response ||
                    !response.success
                ) {

                    console.error(
                        response?.message ||
                        'Unable to load departments.'
                    );

                    return;
                }


                const departments =
                    Array.isArray(
                        response.departments
                    )
                        ? response.departments
                        : [];


                populateDepartmentDropdown(
                    '#todayAttendanceDepartment',
                    departments
                );


                populateDepartmentDropdown(
                    '#departmentEmployeeFilter',
                    departments
                );

            },


            error: function (xhr) {

                console.error(
                    'Departments AJAX Error:',
                    xhr.responseText
                );

            }

        });

    }



    // =====================================================
    // POPULATE DEPARTMENT DROPDOWN
    // =====================================================

    function populateDepartmentDropdown(
        selector,
        departments
    ) {

        const dropdown =
            $(selector);


        if (!dropdown.length) {
            return;
        }


        dropdown.empty();


        dropdown.append(
            $('<option>', {

                value: '',

                text: 'All Departments'

            })
        );


        departments.forEach(
            function (department) {

                dropdown.append(
                    $('<option>', {

                        value:
                            department.id,

                        text:
                            department.name

                    })
                );

            }
        );

    }



    // =====================================================
    // ATTENDANCE OVERVIEW
    // =====================================================

    function loadAttendanceOverview() {

        const period =
            $('#attendancePeriod').val() ||
            'week';


        const employeeId =
            $('#attendanceEmployee').val() ||
            '';


        showChartLoading(
            '#attendanceOverviewLoading'
        );


        $.ajax({

            url: AJAX_URL,

            type: 'GET',

            data: {

                action:
                    'attendance_overview',

                period:
                    period,

                employee_id:
                    employeeId

            },

            dataType: 'json',


            success: function (response) {

                hideChartLoading(
                    '#attendanceOverviewLoading'
                );


                if (
                    !response ||
                    !response.success
                ) {

                    showChartMessage(
                        '#attendanceOverviewMessage',

                        response?.message ||
                        'Unable to load attendance overview.'
                    );

                    clearAttendanceOverviewChart();

                    return;
                }


                const labels =
                    Array.isArray(response.labels)
                        ? response.labels
                        : [];


                const data =
                    response.data || {};


                /*
                 * Check whether attendance data actually exists.
                 */

                if (
                    !hasAttendanceOverviewData(
                        labels,
                        data
                    )
                ) {

                    clearAttendanceOverviewChart();


                    showChartMessage(
                        '#attendanceOverviewMessage',

                        getAttendanceOverviewEmptyMessage(
                            period,
                            employeeId
                        )
                    );

                    return;
                }


                hideChartMessage(
                    '#attendanceOverviewMessage'
                );


                renderAttendanceOverviewChart(
                    labels,
                    data
                );

            },


            error: function (xhr) {

                hideChartLoading(
                    '#attendanceOverviewLoading'
                );


                console.error(
                    'Attendance Overview Error:',
                    xhr.responseText
                );


                clearAttendanceOverviewChart();


                showChartMessage(
                    '#attendanceOverviewMessage',

                    'Unable to load attendance overview. Please try again.'
                );

            }

        });

    }



    // =====================================================
    // CHECK ATTENDANCE OVERVIEW DATA
    // =====================================================

    function hasAttendanceOverviewData(
        labels,
        data
    ) {

        if (!labels.length) {
            return false;
        }


        const present =
            Array.isArray(data.present)
                ? data.present
                : [];


        const absent =
            Array.isArray(data.absent)
                ? data.absent
                : [];


        const halfDay =
            Array.isArray(data.half_day)
                ? data.half_day
                : [];


        const leave =
            Array.isArray(data.leave)
                ? data.leave
                : [];


        /*
         * If all attendance arrays are empty,
         * there is nothing useful to display.
         */

        return (
            present.length > 0 ||
            absent.length > 0 ||
            halfDay.length > 0 ||
            leave.length > 0
        );

    }



    // =====================================================
    // ATTENDANCE OVERVIEW EMPTY MESSAGE
    // =====================================================

    function getAttendanceOverviewEmptyMessage(
        period,
        employeeId
    ) {

        if (employeeId) {

            return (
                'No attendance records are available for the selected employee during this period.'
            );

        }


        if (period === 'week') {

            return (
                'No attendance records are available for this week.'
            );

        }


        if (period === 'month') {

            return (
                'No attendance records are available for this month.'
            );

        }


        if (period === 'year') {

            return (
                'No attendance records are available for this year.'
            );

        }


        return (
            'No attendance records are available for the selected period.'
        );

    }



    // =====================================================
    // CLEAR ATTENDANCE OVERVIEW CHART
    // =====================================================

    function clearAttendanceOverviewChart() {

        if (attendanceOverviewChart) {

            attendanceOverviewChart.destroy();

            attendanceOverviewChart = null;

        }


        const canvas =
            document.getElementById(
                'attendanceOverviewChart'
            );


        if (canvas) {

            const context =
                canvas.getContext('2d');


            context.clearRect(
                0,
                0,
                canvas.width,
                canvas.height
            );

        }

    }



    // =====================================================
    // RENDER ATTENDANCE OVERVIEW CHART
    // =====================================================

    function renderAttendanceOverviewChart(
        labels,
        data
    ) {

        const canvas =
            document.getElementById(
                'attendanceOverviewChart'
            );


        if (!canvas) {
            return;
        }


        if (attendanceOverviewChart) {

            attendanceOverviewChart.destroy();

        }


        attendanceOverviewChart =
            new Chart(canvas, {

                type: 'line',

                data: {

                    labels: labels,

                    datasets: [

                        {
                            label: 'Present',

                            data:
                                data.present || [],

                            tension: 0.3,

                            borderWidth: 2,

                            fill: false
                        },


                        {
                            label: 'Absent',

                            data:
                                data.absent || [],

                            tension: 0.3,

                            borderWidth: 2,

                            fill: false
                        },


                        {
                            label: 'Half Day',

                            data:
                                data.half_day || [],

                            tension: 0.3,

                            borderWidth: 2,

                            fill: false
                        },


                        {
                            label: 'Leave',

                            data:
                                data.leave || [],

                            tension: 0.3,

                            borderWidth: 2,

                            fill: false
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

                            position: 'top'

                        }

                    },


                    scales: {

                        y: {

                            beginAtZero: true,

                            ticks: {

                                precision: 0

                            }

                        }

                    }

                }

            });

    }



    // =====================================================
    // TODAY'S ATTENDANCE
    // =====================================================

    function loadTodayAttendance() {

        const departmentId =
            $('#todayAttendanceDepartment').val() ||
            '';


        showChartLoading(
            '#todayAttendanceLoading'
        );


        $.ajax({

            url: AJAX_URL,

            type: 'GET',

            data: {

                action:
                    'today_attendance',

                department_id:
                    departmentId

            },

            dataType: 'json',


            success: function (response) {

                hideChartLoading(
                    '#todayAttendanceLoading'
                );


                if (
                    !response ||
                    !response.success
                ) {

                    clearTodayAttendanceChart();


                    showChartMessage(
                        '#todayAttendanceMessage',

                        response?.message ||
                        'Unable to load today attendance.'
                    );

                    return;
                }


                const attendance =
                    response.attendance || {};


                $('#todayAttendanceTotal')
                    .text(
                        attendance.total || 0
                    );


                /*
                 * Check whether today's attendance
                 * contains any meaningful data.
                 */

                if (
                    !hasTodayAttendanceData(
                        attendance
                    )
                ) {

                    clearTodayAttendanceChart();


                    showChartMessage(
                        '#todayAttendanceMessage',

                        getTodayAttendanceEmptyMessage(
                            departmentId
                        )
                    );

                    return;
                }


                hideChartMessage(
                    '#todayAttendanceMessage'
                );


                renderTodayAttendanceChart(
                    attendance
                );

            },


            error: function (xhr) {

                hideChartLoading(
                    '#todayAttendanceLoading'
                );


                console.error(
                    'Today Attendance Error:',
                    xhr.responseText
                );


                clearTodayAttendanceChart();


                showChartMessage(
                    '#todayAttendanceMessage',

                    'Unable to load today attendance. Please try again.'
                );

            }

        });

    }



    // =====================================================
    // CHECK TODAY ATTENDANCE DATA
    // =====================================================

    function hasTodayAttendanceData(
        attendance
    ) {

        const total =
            Number(attendance.total || 0);


        const present =
            Number(attendance.present || 0);


        const absent =
            Number(attendance.absent || 0);


        const halfDay =
            Number(attendance.half_day || 0);


        const leave =
            Number(attendance.leave || 0);


        const notMarked =
            Number(attendance.not_marked || 0);


        /*
         * If no employees exist in the selected scope,
         * there is nothing to display.
         */

        if (total === 0) {
            return false;
        }


        return (
            present > 0 ||
            absent > 0 ||
            halfDay > 0 ||
            leave > 0 ||
            notMarked > 0
        );

    }



    // =====================================================
    // TODAY ATTENDANCE EMPTY MESSAGE
    // =====================================================

    function getTodayAttendanceEmptyMessage(
        departmentId
    ) {

        if (departmentId) {

            return (
                'No active employees or attendance data are available for the selected department today.'
            );

        }


        return (
            'No attendance data are available for today.'
        );

    }



    // =====================================================
    // CLEAR TODAY ATTENDANCE CHART
    // =====================================================

    function clearTodayAttendanceChart() {

        if (todayAttendanceChart) {

            todayAttendanceChart.destroy();

            todayAttendanceChart = null;

        }


        const canvas =
            document.getElementById(
                'todayAttendanceChart'
            );


        if (canvas) {

            const context =
                canvas.getContext('2d');


            context.clearRect(
                0,
                0,
                canvas.width,
                canvas.height
            );

        }

    }



    // =====================================================
    // RENDER TODAY ATTENDANCE CHART
    // =====================================================

    function renderTodayAttendanceChart(
        attendance
    ) {

        const canvas =
            document.getElementById(
                'todayAttendanceChart'
            );


        if (!canvas) {
            return;
        }


        if (todayAttendanceChart) {

            todayAttendanceChart.destroy();

        }


        todayAttendanceChart =
            new Chart(canvas, {

                type: 'doughnut',

                data: {

                    labels: [

                        'Present',

                        'Absent',

                        'Half Day',

                        'Leave',

                        'Not Marked'

                    ],


                    datasets: [

                        {

                            data: [

                                attendance.present || 0,

                                attendance.absent || 0,

                                attendance.half_day || 0,

                                attendance.leave || 0,

                                attendance.not_marked || 0

                            ],

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
    // DEPARTMENT-WISE EMPLOYEES
    // =====================================================

    function loadDepartmentEmployees() {

        const departmentId =
            $('#departmentEmployeeFilter').val() ||
            '';


        showChartLoading(
            '#departmentEmployeeLoading'
        );


        $.ajax({

            url: AJAX_URL,

            type: 'GET',

            data: {

                action:
                    'department_employees',

                department_id:
                    departmentId

            },

            dataType: 'json',


            success: function (response) {

                hideChartLoading(
                    '#departmentEmployeeLoading'
                );


                if (
                    !response ||
                    !response.success
                ) {

                    clearDepartmentEmployeeChart();


                    showChartMessage(
                        '#departmentEmployeeMessage',

                        response?.message ||
                        'Unable to load department employees.'
                    );

                    return;
                }


                const labels =
                    Array.isArray(response.labels)
                        ? response.labels
                        : [];


                const data =
                    Array.isArray(response.data)
                        ? response.data
                        : [];


                /*
                 * No department data.
                 */

                if (
                    labels.length === 0 ||
                    data.length === 0
                ) {

                    clearDepartmentEmployeeChart();


                    showChartMessage(
                        '#departmentEmployeeMessage',

                        departmentId
                            ? 'No employees are available for the selected department.'
                            : 'No department employee data are available.'
                    );

                    return;
                }


                hideChartMessage(
                    '#departmentEmployeeMessage'
                );


                renderDepartmentEmployeeChart(
                    labels,
                    data
                );

            },


            error: function (xhr) {

                hideChartLoading(
                    '#departmentEmployeeLoading'
                );


                console.error(
                    'Department Employees Error:',
                    xhr.responseText
                );


                clearDepartmentEmployeeChart();


                showChartMessage(
                    '#departmentEmployeeMessage',

                    'Unable to load department employees. Please try again.'
                );

            }

        });

    }



    // =====================================================
    // CLEAR DEPARTMENT EMPLOYEE CHART
    // =====================================================

    function clearDepartmentEmployeeChart() {

        if (departmentEmployeeChart) {

            departmentEmployeeChart.destroy();

            departmentEmployeeChart = null;

        }


        const canvas =
            document.getElementById(
                'departmentEmployeeChart'
            );


        if (canvas) {

            const context =
                canvas.getContext('2d');


            context.clearRect(
                0,
                0,
                canvas.width,
                canvas.height
            );

        }

    }



    // =====================================================
    // RENDER DEPARTMENT EMPLOYEE CHART
    // =====================================================

    function renderDepartmentEmployeeChart(
        labels,
        data
    ) {

        const canvas =
            document.getElementById(
                'departmentEmployeeChart'
            );


        if (!canvas) {
            return;
        }


        if (departmentEmployeeChart) {

            departmentEmployeeChart.destroy();

        }


        departmentEmployeeChart =
            new Chart(canvas, {

                type: 'bar',

                data: {

                    labels: labels,

                    datasets: [

                        {

                            label:
                                'Employees',

                            data:
                                data,

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

                            ticks: {

                                precision: 0

                            }

                        }

                    }

                }

            });

    }



    // =====================================================
    // PERIOD FILTER
    // =====================================================

    $('#attendancePeriod').on(
        'change',
        function () {

            loadAttendanceOverview();

        }
    );



    // =====================================================
    // EMPLOYEE FILTER
    // =====================================================

    $('#attendanceEmployee').on(
        'change',
        function () {

            loadAttendanceOverview();

        }
    );



    // =====================================================
    // TODAY DEPARTMENT FILTER
    // =====================================================

    $('#todayAttendanceDepartment').on(
        'change',
        function () {

            loadTodayAttendance();

        }
    );



    // =====================================================
    // DEPARTMENT EMPLOYEE FILTER
    // =====================================================

    $('#departmentEmployeeFilter').on(
        'change',
        function () {

            loadDepartmentEmployees();

        }
    );



    // =====================================================
    // STATISTICS LOADING
    // =====================================================

    function showStatisticsLoading() {

        $('#totalEmployees').text('...');

        $('#pendingLeaves').text('...');

        $('#totalDepartments').text('...');

        $('#totalDesignations').text('...');

    }



    // =====================================================
    // CHART LOADING
    // =====================================================

    function showChartLoading(selector) {

        $(selector)
            .removeClass('d-none');

    }


    function hideChartLoading(selector) {

        $(selector)
            .addClass('d-none');

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
            .text(message);

    }


    function hideChartMessage(selector) {

        $(selector)
            .addClass('d-none')
            .text('');

    }



    // =====================================================
    // DASHBOARD MESSAGE
    // =====================================================

    function showMessage(
        message,
        type = 'danger'
    ) {

        const element =
            $('#dashboardMessage');


        if (!element.length) {

            console.error(message);

            return;

        }


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
            .text(message);

    }

});

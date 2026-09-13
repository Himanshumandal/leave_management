<?php

Auth::requireLogin();

$pageTitle = 'Employee Dashboard';

require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/sidenav.php';

?>

<main class="main-content">

    <?php require_once __DIR__ . '/../layouts/topbar.php'; ?>


    <div class="dashboard-container p-4">


        <!-- =====================================================
             WELCOME SECTION
        ====================================================== -->

        <div class="welcome-section mb-4">

            <h2 class="mb-1">
                Good morning,
                <span id="employeeName">Loading...</span> 👋
            </h2>

            <p class="text-muted mb-0">
                Here's what's happening with your work today.
            </p>

        </div>


        <!-- =====================================================
             DASHBOARD MESSAGE
        ====================================================== -->

        <div
            id="dashboardMessage"
            class="alert d-none mb-4"
            role="alert"></div>


        <!-- =====================================================
             STATISTICS CARDS
        ====================================================== -->

        <div class="row g-4 mb-4">


            <!-- ATTENDANCE -->

            <div class="col-xl-3 col-md-6">

                <div class="card border-0 shadow-sm h-100">

                    <div class="card-body">

                        <div class="d-flex justify-content-between align-items-start">

                            <div>

                                <p class="text-muted mb-1">
                                    Attendance
                                </p>

                                <h3
                                    id="attendancePercentage"
                                    class="mb-1">
                                    --
                                </h3>

                                <small class="text-muted">
                                    This month
                                </small>

                            </div>

                            <div class="bg-primary-subtle text-primary rounded p-3">

                                <i class="bi bi-calendar-check fs-4"></i>

                            </div>

                        </div>

                    </div>

                </div>

            </div>


            <!-- PRESENT DAYS -->

            <div class="col-xl-3 col-md-6">

                <div class="card border-0 shadow-sm h-100">

                    <div class="card-body">

                        <div class="d-flex justify-content-between align-items-start">

                            <div>

                                <p class="text-muted mb-1">
                                    Present Days
                                </p>

                                <h3
                                    id="presentDays"
                                    class="mb-1">
                                    --
                                </h3>

                                <small class="text-muted">
                                    This month
                                </small>

                            </div>

                            <div class="bg-success-subtle text-success rounded p-3">

                                <i class="bi bi-person-check fs-4"></i>

                            </div>

                        </div>

                    </div>

                </div>

            </div>


            <!-- LEAVE BALANCE -->

            <div class="col-xl-3 col-md-6">

                <div class="card border-0 shadow-sm h-100">

                    <div class="card-body">

                        <div class="d-flex justify-content-between align-items-start">

                            <div>

                                <p class="text-muted mb-1">
                                    Leave Balance
                                </p>

                                <h3
                                    id="leaveBalance"
                                    class="mb-1">
                                    --
                                </h3>

                                <small class="text-muted">
                                    Days remaining
                                </small>

                            </div>

                            <div class="bg-warning-subtle text-warning rounded p-3">

                                <i class="bi bi-calendar-minus fs-4"></i>

                            </div>

                        </div>

                    </div>

                </div>

            </div>


            <!-- PENDING REQUESTS -->

            <div class="col-xl-3 col-md-6">

                <div class="card border-0 shadow-sm h-100">

                    <div class="card-body">

                        <div class="d-flex justify-content-between align-items-start">

                            <div>

                                <p class="text-muted mb-1">
                                    Pending Requests
                                </p>

                                <h3
                                    id="pendingRequests"
                                    class="mb-1">
                                    --
                                </h3>

                                <small class="text-muted">
                                    Awaiting approval
                                </small>

                            </div>

                            <div class="bg-info-subtle text-info rounded p-3">

                                <i class="bi bi-clock-history fs-4"></i>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>


        <!-- =====================================================
             TODAY ATTENDANCE + PROFILE
        ====================================================== -->

        <div class="row g-4 mb-4">


            <!-- TODAY ATTENDANCE -->

            <div class="col-lg-8">

                <div class="card border-0 shadow-sm h-100">

                    <div class="card-body">


                        <div class="d-flex justify-content-between align-items-center mb-4">

                            <div>

                                <h5 class="mb-1">
                                    Today's Attendance
                                </h5>

                                <small class="text-muted">
                                    Your attendance status for today
                                </small>

                            </div>


                            <span
                                id="todayAttendanceStatus"
                                class="badge bg-secondary">
                                Loading...
                            </span>

                        </div>


                        <div class="row g-3">


                            <!-- CHECK IN -->

                            <div class="col-md-4">

                                <div class="border rounded p-3 h-100">

                                    <small class="text-muted d-block">
                                        Check In
                                    </small>

                                    <h5
                                        id="checkInTime"
                                        class="mb-0 mt-1">
                                        --
                                    </h5>

                                </div>

                            </div>


                            <!-- CHECK OUT -->

                            <div class="col-md-4">

                                <div class="border rounded p-3 h-100">

                                    <small class="text-muted d-block">
                                        Check Out
                                    </small>

                                    <h5
                                        id="checkOutTime"
                                        class="mb-0 mt-1">
                                        --
                                    </h5>

                                </div>

                            </div>


                            <!-- WORKING HOURS -->

                            <div class="col-md-4">

                                <div class="border rounded p-3 h-100">

                                    <small class="text-muted d-block">
                                        Working Hours
                                    </small>

                                    <h5
                                        id="workingHours"
                                        class="mb-0 mt-1">
                                        --
                                    </h5>

                                </div>

                            </div>


                            <!-- TODAY LEAVE -->

                            <div
                                id="todayLeaveSection"
                                class="col-12 d-none">

                                <div class="alert alert-info mb-0">

                                    <div class="d-flex align-items-start">

                                        <i class="bi bi-calendar-event fs-4 me-3"></i>

                                        <div>

                                            <strong>
                                                You are on leave today
                                            </strong>

                                            <div class="mt-2">

                                                <div>
                                                    <strong>Leave Type:</strong>
                                                    <span id="todayLeaveType">
                                                        --
                                                    </span>
                                                </div>

                                                <div>
                                                    <strong>Reason:</strong>
                                                    <span id="todayLeaveReason">
                                                        --
                                                    </span>
                                                </div>

                                                <div>
                                                    <strong>Admin Remark:</strong>
                                                    <span id="todayLeaveRemark">
                                                        --
                                                    </span>
                                                </div>

                                            </div>

                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>


            <!-- PROFILE -->

            <div class="col-lg-4">

                <div class="card border-0 shadow-sm h-100">

                    <div class="card-body">

                        <h5 class="mb-4">
                            My Profile
                        </h5>


                        <!-- EMPLOYEE ID -->

                        <div class="border-bottom py-3">

                            <small class="text-muted d-block">
                                Employee ID
                            </small>

                            <strong id="employeeId">
                                --
                            </strong>

                        </div>


                        <!-- DEPARTMENT -->

                        <div class="border-bottom py-3">

                            <small class="text-muted d-block">
                                Department
                            </small>

                            <strong id="department">
                                --
                            </strong>

                        </div>


                        <!-- DESIGNATION -->

                        <div class="border-bottom py-3">

                            <small class="text-muted d-block">
                                Designation
                            </small>

                            <strong id="designation">
                                --
                            </strong>

                        </div>


                        <!-- STATUS -->

                        <div class="py-3">

                            <small class="text-muted d-block">
                                Status
                            </small>

                            <span
                                id="employeeStatus"
                                class="badge bg-secondary">
                                Loading...
                            </span>

                        </div>

                    </div>

                </div>

            </div>

        </div>


        <!-- =====================================================
             GRAPH 1 + GRAPH 2
        ====================================================== -->

        <div class="row g-4 mb-4">


<!-- =====================================================
     ATTENDANCE TREND
====================================================== -->

<div class="col-lg-8">

<div class="card border-0 shadow-sm h-100">

    <div class="card-body">

        <!-- HEADER -->
        <div class="d-flex justify-content-between align-items-center mb-3">

            <div>

                <h5 class="mb-1">
                    Attendance Trend
                </h5>

                <small class="text-muted">
                    Track your attendance over time
                </small>

            </div>


            <!-- FILTER -->
            <div>

                <select
                    id="attendanceTrendFilter"
                    class="form-select form-select-sm"
                    style="min-width: 130px;"
                >

                    <option value="week">
                        This Week
                    </option>

                    <option value="month">
                        This Month
                    </option>

                    <option value="year">
                        This Year
                    </option>

                </select>

            </div>

        </div>


        <!-- MESSAGE -->

        <div
            id="attendanceChartMessage"
            class="alert alert-info d-none mt-3"
        ></div>


        <!-- CHART WRAPPER -->

        <div
            id="attendanceChartContainer"
            style="
                position: relative;
                width: 100%;
                height: 350px;
            "
        >

            <canvas
                id="attendanceTrendChart"
            ></canvas>

        </div>

    </div>

</div>


</div>



            <!-- =================================================
                 LEAVE / ATTENDANCE STATUS
            ================================================== -->

            <!-- =================================================
     LEAVE / ATTENDANCE STATUS
================================================== -->

            <div class="col-lg-4">


                <div class="card border-0 shadow-sm h-100">

                    <div class="card-body">


                        <!-- HEADER -->

                        <div class="d-flex justify-content-between align-items-start mb-3">

                            <div>

                                <h5 class="mb-1">
                                    Attendance Status
                                </h5>

                                <small class="text-muted">
                                    Attendance breakdown
                                </small>

                            </div>


                            <!-- STATUS FILTER -->

                            <div>

                                <select
                                    id="attendanceStatusFilter"
                                    class="form-select form-select-sm"
                                    style="min-width: 130px;">

                                    <option value="all">
                                        All Status
                                    </option>

                                    <option value="present">
                                        Present
                                    </option>

                                    <option value="absent">
                                        Absent
                                    </option>

                                    <option value="leave">
                                        Leave
                                    </option>

                                    <option value="half_day">
                                        Half Day
                                    </option>

                                </select>

                            </div>

                        </div>


                        <!-- MESSAGE -->

                        <div
                            id="leaveChartMessage"
                            class="alert alert-info d-none mt-3"></div>


                        <!-- CHART -->

                        <div
                            id="leaveChartContainer"
                            style="height: 250px;">

                            <canvas
                                id="leaveStatusChart"></canvas>

                        </div>


                    </div>

                </div>

            </div>


        </div>


        <!-- =====================================================
             WORKING HOURS GRAPH
        ====================================================== -->

        <div class="row g-4 mb-4">

            <div class="col-12">

                <div class="card border-0 shadow-sm">

                    <div class="card-body">


                        <!-- HEADER -->

                        <div class="d-flex justify-content-between align-items-center mb-3">

                            <div>

                                <h5 class="mb-1">
                                    Working Hours
                                </h5>

                                <small class="text-muted">
                                    Track your working hours over time
                                </small>

                            </div>


                            <!-- FILTER -->

                            <div>

                                <select
                                    id="workingHoursFilter"
                                    class="form-select form-select-sm">

                                    <option value="week">
                                        This Week
                                    </option>

                                    <option value="month">
                                        This Month
                                    </option>

                                    <option value="year">
                                        This Year
                                    </option>

                                </select>

                            </div>

                        </div>


                        <!-- MESSAGE -->

                        <div
                            id="workingHoursChartMessage"
                            class="alert alert-info d-none"></div>


                        <!-- CHART -->

                        <div
                            id="workingHoursChartContainer"
                            style="height: 300px;">

                            <canvas
                                id="workingHoursChart"></canvas>

                        </div>

                    </div>

                </div>

            </div>

        </div>


        <!-- =====================================================
             QUICK ACTIONS
        ====================================================== -->

        <div class="row g-4">

            <div class="col-12">

                <div class="card border-0 shadow-sm">

                    <div class="card-body">

                        <h5 class="mb-1">
                            Quick Actions
                        </h5>

                        <p class="text-muted mb-4">
                            Quickly access your frequently used options.
                        </p>


                        <div class="row g-3">


                            <!-- ATTENDANCE -->

                            <div class="col-md-4">

                                <a
                                    href="attendance.php"
                                    class="text-decoration-none text-dark">

                                    <div class="border rounded p-3 h-100">

                                        <i class="bi bi-calendar-check fs-3 text-primary"></i>

                                        <h6 class="mt-3 mb-1">
                                            Attendance
                                        </h6>

                                        <small class="text-muted">
                                            View your attendance
                                        </small>

                                    </div>

                                </a>

                            </div>


                            <!-- LEAVE -->

                            <div class="col-md-4">

                                <a
                                    href="leave.php"
                                    class="text-decoration-none text-dark">

                                    <div class="border rounded p-3 h-100">

                                        <i class="bi bi-calendar-plus fs-3 text-warning"></i>

                                        <h6 class="mt-3 mb-1">
                                            Apply Leave
                                        </h6>

                                        <small class="text-muted">
                                            Submit a leave request
                                        </small>

                                    </div>

                                </a>

                            </div>


                            <!-- PROFILE -->

                            <div class="col-md-4">

                                <a
                                    href="profile.php"
                                    class="text-decoration-none text-dark">

                                    <div class="border rounded p-3 h-100">

                                        <i class="bi bi-person fs-3 text-success"></i>

                                        <h6 class="mt-3 mb-1">
                                            My Profile
                                        </h6>

                                        <small class="text-muted">
                                            View your profile
                                        </small>

                                    </div>

                                </a>

                            </div>


                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</main>


<!-- =========================================================
     JAVASCRIPT LIBRARIES
========================================================= -->

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>


<!-- =========================================================
     EMPLOYEE DASHBOARD JS
========================================================= -->

<script src="../../public/js/employee_dashboard.js"></script>


<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
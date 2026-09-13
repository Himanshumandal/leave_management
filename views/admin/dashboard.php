<?php

// require_once '../../config/bootstrap.php';

Auth::requireAdmin();

$pageTitle = 'Dashboard';

require_once __DIR__ . '/../layouts/header.php';

require_once __DIR__ . '/../layouts/sidenav.php'

?>

<main class="main-content">

    <?php require_once __DIR__ . '/../layouts/topbar.php'; ?>


    <div class="dashboard-container p-4">


        <!-- Welcome -->

        <div class="welcome-section mb-4">

            <h2>
                Welcome back, Admin 👋
            </h2>

            <p class="text-muted mb-0">
                Here's what's happening with your organization today.
            </p>

        </div>


        <!-- Statistics -->

        <div class="row g-4 mb-4">


            <!-- Employees -->

            <div class="col-xl-3 col-md-6">

                <div class="stat-card bg-white border rounded-3 p-4 h-100">

                    <div class="d-flex justify-content-between align-items-center">

                        <span>
                            Total Employees
                        </span>

                        <div class="stat-icon bg-primary-subtle text-primary p-2 rounded">

                            <i class="bi bi-people-fill"></i>

                        </div>

                    </div>

                    <h3
                        id="totalEmployees"
                        class="mt-3 mb-1">
                        0
                    </h3>

                    <p class="text-muted mb-0">
                        Active employees
                    </p>

                </div>

            </div>


            <!-- Leave Requests -->

            <div class="col-xl-3 col-md-6">

                <div class="stat-card bg-white border rounded-3 p-4 h-100">

                    <div class="d-flex justify-content-between align-items-center">

                        <span>
                            Leave Requests
                        </span>

                        <div class="stat-icon bg-warning-subtle text-warning p-2 rounded">

                            <i class="bi bi-calendar-check"></i>

                        </div>

                    </div>

                    <!-- Leave Requests -->
                    <h3
                        id="pendingLeaves"
                        class="mt-3 mb-1">
                        0
                    </h3>

                    <p class="text-muted mb-0">
                        Pending requests
                    </p>

                </div>

            </div>


            <!-- Departments -->

            <div class="col-xl-3 col-md-6">

                <div class="stat-card bg-white border rounded-3 p-4 h-100">

                    <div class="d-flex justify-content-between align-items-center">

                        <span>
                            Departments
                        </span>

                        <div class="stat-icon bg-success-subtle text-success p-2 rounded">

                            <i class="bi bi-building"></i>

                        </div>

                    </div>

                    <!-- Departments -->
                    <h3
                        id="totalDepartments"
                        class="mt-3 mb-1">
                        0
                    </h3>

                    <p class="text-muted mb-0">
                        Registered departments
                    </p>

                </div>

            </div>


            <!-- Users -->

            <div class="col-xl-3 col-md-6">

                <div class="stat-card bg-white border rounded-3 p-4 h-100">

                    <div class="d-flex justify-content-between align-items-center">

                        <span>
                            Designations
                        </span>

                        <div class="stat-icon bg-info-subtle text-info p-2 rounded">

                            <i class="bi bi-person-badge-fill"></i>

                        </div>

                    </div>

                    <!-- Total Designation -->
                    <h3
                        id="totalDesignations"
                        class="mt-3 mb-1">
                        0
                    </h3>

                </div>

            </div>

        </div>

        <!-- =========================================================
     GRAPHICAL DASHBOARD
========================================================= -->

        <div class="row g-4 mb-4">


            <!-- =====================================================
         ATTENDANCE OVERVIEW
    ====================================================== -->

            <div class="col-xl-8">

                <div class="content-card bg-white border rounded-3 p-4 h-100">


                    <!-- HEADER -->

                    <div
                        class="d-flex justify-content-between align-items-center mb-4">

                        <div>

                            <h5 class="mb-1">
                                Attendance Overview
                            </h5>

                            <small class="text-muted">
                                Attendance trends over time
                            </small>

                        </div>


                        <!-- FILTERS -->

                        <div class="d-flex gap-2">

                            <!-- PERIOD -->

                            <select
                                id="attendancePeriod"
                                class="form-select form-select-sm">

                                <option value="week">
                                    Week
                                </option>

                                <option value="month">
                                    Month
                                </option>

                                <option value="year">
                                    Year
                                </option>

                            </select>


                            <!-- EMPLOYEE -->

                            <select
                                id="attendanceEmployee"
                                class="form-select form-select-sm">

                                <option value="">
                                    All Employees
                                </option>

                            </select>

                        </div>

                    </div>


                    <!-- CHART -->

                    <div
                        style="
                    position: relative;
                    height: 350px;
                ">

                        <canvas
                            id="attendanceOverviewChart"></canvas>

                    </div>


                    <!-- LOADING -->

                    <div
                        id="attendanceOverviewLoading"
                        class="text-center d-none">

                        <div
                            class="spinner-border spinner-border-sm text-primary"></div>

                        <span class="text-muted ms-2">
                            Loading attendance...
                        </span>

                    </div>


                    <!-- MESSAGE -->

                    <div
                        id="attendanceOverviewMessage"
                        class="alert alert-danger d-none mt-3 mb-0"></div>


                </div>

            </div>



            <!-- =====================================================
         TODAY'S ATTENDANCE
    ====================================================== -->

            <div class="col-xl-4">

                <div class="content-card bg-white border rounded-3 p-4 h-100">


                    <!-- HEADER -->

                    <div
                        class="d-flex justify-content-between align-items-center mb-4">

                        <div>

                            <h5 class="mb-1">
                                Today's Attendance
                            </h5>

                            <small class="text-muted">
                                Employee distribution
                            </small>

                        </div>


                        <!-- FILTER -->

                        <select
                            id="todayAttendanceDepartment"
                            class="form-select form-select-sm"
                            style="width: 150px;">

                            <option value="">
                                All Departments
                            </option>

                        </select>

                    </div>


                    <!-- CHART -->

                    <div
                        style="
                    position: relative;
                    height: 280px;
                ">

                        <canvas
                            id="todayAttendanceChart"></canvas>

                    </div>


                    <!-- TOTAL -->

                    <div
                        class="text-center mt-3">

                        <small class="text-muted">
                            Total Employees
                        </small>

                        <h4
                            id="todayAttendanceTotal"
                            class="mb-0">
                            0
                        </h4>

                    </div>


                    <!-- LOADING -->

                    <div
                        id="todayAttendanceLoading"
                        class="text-center d-none">

                        <div
                            class="spinner-border spinner-border-sm text-primary"></div>

                        <span class="text-muted ms-2">
                            Loading...
                        </span>

                    </div>


                    <!-- MESSAGE -->

                    <div
                        id="todayAttendanceMessage"
                        class="alert alert-danger d-none mt-3 mb-0"></div>


                </div>

            </div>

        </div>



        <!-- =========================================================
     DEPARTMENT-WISE EMPLOYEES
========================================================= -->

        <div class="row g-4 mb-4">


            <div class="col-12">

                <div class="content-card bg-white border rounded-3 p-4">


                    <!-- HEADER -->

                    <div
                        class="d-flex justify-content-between align-items-center mb-4">

                        <div>

                            <h5 class="mb-1">
                                Department-wise Employees
                            </h5>

                            <small class="text-muted">
                                Employee distribution across departments
                            </small>

                        </div>


                        <!-- FILTER -->

                        <select
                            id="departmentEmployeeFilter"
                            class="form-select form-select-sm"
                            style="width: 180px;">

                            <option value="">
                                All Departments
                            </option>

                        </select>

                    </div>


                    <!-- CHART -->

                    <div
                        style="
                    position: relative;
                    height: 350px;
                ">

                        <canvas
                            id="departmentEmployeeChart"></canvas>

                    </div>


                    <!-- LOADING -->

                    <div
                        id="departmentEmployeeLoading"
                        class="text-center d-none">

                        <div
                            class="spinner-border spinner-border-sm text-primary"></div>

                        <span class="text-muted ms-2">
                            Loading employees...
                        </span>

                    </div>


                    <!-- MESSAGE -->

                    <div
                        id="departmentEmployeeMessage"
                        class="alert alert-danger d-none mt-3 mb-0"></div>


                </div>

            </div>

        </div>



        <!-- =========================================================
     BOTTOM SECTION
========================================================= -->

        <div class="row g-4">


            <!-- =====================================================
         QUICK ACTIONS
    ====================================================== -->

            <div class="col-lg-7">

                <div class="content-card bg-white border rounded-3 p-4">

                    <div class="content-card-header mb-3">

                        <h5>
                            Quick Actions
                        </h5>

                    </div>


                    <a
                        href="../../admin/employees.php"
                        class="quick-action d-flex align-items-center gap-3 p-3 border rounded mb-2 text-decoration-none text-dark">

                        <div
                            class="quick-action-icon bg-primary-subtle text-primary p-2 rounded">

                            <i class="bi bi-person-plus"></i>

                        </div>

                        <div>

                            <strong>
                                Add Employee
                            </strong>

                            <small class="d-block text-muted">
                                Create a new employee record
                            </small>

                        </div>

                    </a>


                    <a
                        href="../../admin/leaves.php"
                        class="quick-action d-flex align-items-center gap-3 p-3 border rounded mb-2 text-decoration-none text-dark">

                        <div
                            class="quick-action-icon bg-primary-subtle text-primary p-2 rounded">

                            <i class="bi bi-calendar-plus"></i>

                        </div>

                        <div>

                            <strong>
                                Manage Leave Requests
                            </strong>

                            <small class="d-block text-muted">
                                Review pending employee leave requests
                            </small>

                        </div>

                    </a>


                    <a
                        href="../../admin/attendances.php"
                        class="quick-action d-flex align-items-center gap-3 p-3 border rounded text-decoration-none text-dark">

                        <div
                            class="quick-action-icon bg-primary-subtle text-primary p-2 rounded">

                            <i class="bi bi-file-earmark-bar-graph"></i>

                        </div>

                        <div>

                            <strong>
                                Add Attendances
                            </strong>

                            <small class="d-block text-muted">
                                View and manage attendances
                            </small>

                        </div>

                    </a>

                </div>

            </div>



            <!-- =====================================================
         ACCOUNT INFORMATION
    ====================================================== -->

            <div class="col-lg-5">

                <div class="content-card bg-white border rounded-3 p-4">

                    <div class="content-card-header mb-3">

                        <h5>
                            Account Information
                        </h5>

                    </div>


                    <div class="user-info py-3 border-bottom">

                        <small class="d-block text-muted">
                            User ID
                        </small>

                        <strong>

                            <?= htmlspecialchars(
                                (string) Auth::userId()
                            ) ?>

                        </strong>

                    </div>


                    <div class="user-info py-3 border-bottom">

                        <small class="d-block text-muted">
                            Employee ID
                        </small>

                        <strong>

                            <?= htmlspecialchars(
                                (string) Auth::employeeId()
                            ) ?: 'Not assigned' ?>

                        </strong>

                    </div>


                    <div class="user-info py-3 border-bottom">

                        <small class="d-block text-muted">
                            Role
                        </small>

                        <strong>

                            <span class="badge bg-primary">

                                <?= htmlspecialchars(
                                    (string) Auth::role()
                                ) ?>

                            </span>

                        </strong>

                    </div>


                    <div class="user-info py-3">

                        <small class="d-block text-muted">
                            Account Status
                        </small>

                        <strong>

                            <span class="badge bg-success">
                                Active
                            </span>

                        </strong>

                    </div>

                </div>

            </div>

        </div>

    </div>

</main>


<?php require_once __DIR__ . '/../layouts/footer.php'; ?>

<script
    src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<script
    src="https://cdn.jsdelivr.net/npm/chart.js"></script>


<!-- =========================================================
     EMPLOYEE JAVASCRIPT
========================================================= -->

<script
    src="../../public/js/admin_dashboard.js"></script>


</body>

</html>
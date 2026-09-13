<?php

// Auth::requireAdmin()

$pageTitle = 'Attendance History';


require_once __DIR__.'/../layouts/header.php';

require_once __DIR__.'/../layouts/sidenav.php'

?>

<main class="main-content">

    <?php

    require_once __DIR__.'/../layouts/topbar.php'; ?>


    <div class="container-fluid py-4">


        <!-- ================================= -->
        <!-- HEADER -->
        <!-- ================================= -->

        <div
            class="d-flex justify-content-end align-items-center mb-4"
        >

        
            <div>

                <?php if(Auth::isAdmin()){ ?>

                <a
                    href="../../admin/attendances.php"
                    class="btn btn-primary me-2"
                >
                    Mark Attendance
                </a>



                <a
                    href="../../dashboard.php"
                    class="btn btn-secondary"
                >
                    Dashboard
                </a>
                <?php }elseif(Auth::isEmployee()){ ?>
                    <a
                        href="../../employee/attendance.php"
                        class="btn btn-primary"
                    >
                        Back
                    </a>
                    <a
                        href="../../employee/dashboard.php"
                        class="btn btn-secondary"
                    >
                        Dashboard
                    </a>
                <?php }?>

            </div>

        </div>



        <!-- ================================= -->
        <!-- FILTER CARD -->
        <!-- ================================= -->

        <div class="card mb-4">

            <div class="card-header">

                <h5 class="mb-0">
                    Search Attendance
                </h5>

            </div>


            <div class="card-body">

                <div class="row g-3">


                    <!-- Date From -->

                    <div class="col-md-3">

                        <label
                            for="dateFrom"
                            class="form-label"
                        >
                            From Date
                        </label>


                        <input
                            type="date"
                            id="dateFrom"
                            class="form-control"
                        >

                    </div>



                    <!-- Date To -->

                    <div class="col-md-3">

                        <label
                            for="dateTo"
                            class="form-label"
                        >
                            To Date
                        </label>


                        <input
                            type="date"
                            id="dateTo"
                            class="form-control"
                        >

                    </div>



                    <!-- Employee -->
                    <?php if(Auth::isAdmin()){ ?>

                        <div class="col-md-3">

                            <label
                                for="employeeId"
                                class="form-label"
                            >
                                Employee
                            </label>


                            <select
                                id="employeeId"
                                class="form-select"
                            >

                                <option value="">
                                    All Employees
                                </option>

                            </select>

                        </div>
                    <?php }?>



                    <!-- Status -->

                    <div class="col-md-3">

                        <label
                            for="status"
                            class="form-label"
                        >
                            Status
                        </label>


                        <select
                            id="status"
                            class="form-select"
                        >

                            <option value="">
                                All Status
                            </option>

                            <option value="present">
                                Present
                            </option>

                            <option value="absent">
                                Absent
                            </option>

                            <option value="half_day">
                                Half Day
                            </option>

                            <option value="leave">
                                Leave
                            </option>

                        </select>

                    </div>



                    <!-- Buttons -->

                    <div class="col-12">

                        <button
                            type="button"
                            id="searchAttendanceBtn"
                            class="btn btn-primary"
                        >
                            Search
                        </button>


                        <button
                            type="button"
                            id="resetAttendanceBtn"
                            class="btn btn-secondary"
                        >
                            Reset
                        </button>

                    </div>

                </div>

            </div>

        </div>



        <!-- ================================= -->
        <!-- MESSAGE -->
        <!-- ================================= -->

        <div
            id="historyMessage"
            class="alert d-none"
            role="alert"
        ></div>



        <!-- ================================= -->
        <!-- HISTORY TABLE -->
        <!-- ================================= -->

        <div class="card">

            <div class="card-header">

                <h5 class="mb-0">
                    Attendance Records
                </h5>

            </div>


            <div class="card-body">

                <div class="table-responsive">

                    <table
                        class="table table-bordered table-hover align-middle"
                    >

                        <thead class="table-light">

                            <tr>

                                <th>
                                    #
                                </th>

                                <th>
                                    Date
                                </th>

                                <th>
                                    Employee ID
                                </th>

                                <th>
                                    Employee Name
                                </th>

                                <th>
                                    Department
                                </th>

                                <th>
                                    Status
                                </th>

                                <th>
                                    Check In
                                </th>

                                <th>
                                    Check Out
                                </th>

                                <th>
                                    Remarks
                                </th>

                            </tr>

                        </thead>


                        <tbody id="attendanceHistoryBody">

                            <tr>

                                <td
                                    colspan="9"
                                    class="text-center text-muted py-4"
                                >

                                    Loading attendance...

                                </td>

                            </tr>

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </div>

</main>


<?php 

require_once __DIR__.'/../layouts/footer.php'; ?>


<!-- jQuery -->

<script
    src="https://code.jquery.com/jquery-3.7.1.min.js">
</script>


<!-- Attendance History JS -->

<script>


    const empid = <?= json_encode($empId) ?>;
    const role = <?= json_encode($role) ?>;

  
    
</script>

<script
    src="../../public/js/attendence_history.js">
</script>
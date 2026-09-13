<?php


Auth::requireAdmin();

$pageTitle = 'Attendance Management';


require_once __DIR__.'/../layouts/header.php';

require_once __DIR__.'/../layouts/sidenav.php'

?>

<main class="main-content">

    <?php require_once __DIR__.'/../layouts/topbar.php'; ?>


    <div class="container py-5">


        <!-- ================================= -->
        <!-- PAGE HEADER -->
        <!-- ================================= -->

        <div
            class="d-flex justify-content-end align-items-center mb-4 gap-4"
        >


            <a
                href="../dashboard.php"
                class="btn btn-primary"
            >
                Dashboard
            </a>

            <a
                href="../admin/attendances_summary.php"
                class="btn btn-secondary"
            >
                Summary
            </a>

            <a
                href="../admin/attendances_history.php"
                class="btn btn-success"
            >
                History
            </a>

        </div>



        <!-- ================================= -->
        <!-- DATE FILTER -->
        <!-- ================================= -->

        <div class="card mb-4">

            <div class="card-body">

                <div class="row g-3 align-items-end">


                    <div class="col-md-4">

                        <label
                            for="attendanceDate"
                            class="form-label"
                        >
                            Attendance Date
                        </label>


                        <input
                            type="date"
                            id="attendanceDate"
                            class="form-control"
                        >

                    </div>


                    <div class="col-md-3">

                        <button
                            type="button"
                            id="loadAttendanceBtn"
                            class="btn btn-primary w-100"
                        >
                            Load Attendance
                        </button>

                    </div>

                </div>

            </div>

        </div>



        <!-- ================================= -->
        <!-- MESSAGE -->
        <!-- ================================= -->

        <div
            id="attendanceMessage"
            class="alert d-none"
            role="alert"
        ></div>



        <!-- ================================= -->
        <!-- ATTENDANCE TABLE -->
        <!-- ================================= -->

        <div class="card">

            <div class="card-header">

                <h5 class="mb-0">
                    Employee Attendance
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

                                <th>
                                    Action
                                </th>

                            </tr>

                        </thead>


                        <tbody id="attendanceTableBody">

                            <tr>

                                <td
                                    colspan="9"
                                    class="text-center text-muted py-4"
                                >
                                    Select a date and click
                                    "Load Attendance".
                                </td>

                            </tr>

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </div>

</main>


<?php require_once  __DIR__.'/../layouts/footer.php'; ?>


<!-- ================================= -->
<!-- jQuery -->
<!-- ================================= -->

<script
    src="https://code.jquery.com/jquery-3.7.1.min.js"
></script>


<!-- ================================= -->
<!-- Attendance JavaScript -->
<!-- ================================= -->

<script
    src="../../public/js/attendence.js"
></script>
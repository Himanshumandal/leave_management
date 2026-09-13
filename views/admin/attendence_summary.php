<?php

// require_once '../../config/bootstrap.php';

Auth::requireAdmin();

$pageTitle = 'Attendance Summary';


require_once __DIR__.'/../layouts/header.php';

require_once __DIR__.'/../layouts/sidenav.php'

?>

<main class="main-content">

    <?php require_once __DIR__.'/../layouts/topbar.php'; ?>


    <div class="container py-5">


        <!-- ================================= -->
        <!-- HEADER -->
        <!-- ================================= -->

        <div
            class="d-flex justify-content-end align-items-center mb-4"
        >



            <div>

                <a
                    href="../../admin/attendances.php"
                    class="btn btn-primary me-2"
                >
                    Mark Attendance
                </a>


                <a
                    href="../../admin/attendances_history.php"
                    class="btn btn-secondary me-2"
                >
                    History
                </a>


                <a
                    href="../../dashboard.php"
                    class="btn btn-dark"
                >
                    Dashboard
                </a>

            </div>

        </div>



        <!-- ================================= -->
        <!-- FILTER CARD -->
        <!-- ================================= -->

        <div class="card mb-4">

            <div class="card-header">

                <h5 class="mb-0">
                    Summary Filters
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

                    <div class="col-md-4">

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



                    <!-- Search -->

                    <div class="col-md-2 d-flex align-items-end">

                        <button
                            type="button"
                            id="searchSummaryBtn"
                            class="btn btn-primary w-100"
                        >
                            Search
                        </button>

                    </div>



                    <!-- Reset -->

                    <div class="col-12">

                        <button
                            type="button"
                            id="resetSummaryBtn"
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
            id="summaryMessage"
            class="alert d-none"
            role="alert"
        ></div>



        <!-- ================================= -->
        <!-- SUMMARY TABLE -->
        <!-- ================================= -->

        <div class="card">

            <div class="card-header">

                <h5 class="mb-0">
                    Employee Summary
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

                                <th class="text-center">
                                    Working Days
                                </th>

                                <th class="text-center">
                                    Present
                                </th>

                                <th class="text-center">
                                    Absent
                                </th>

                                <th class="text-center">
                                    Half Day
                                </th>

                                <th class="text-center">
                                    Leave
                                </th>

                                <th class="text-center">
                                    Total Marked
                                </th>

                            </tr>

                        </thead>


                        <tbody id="attendanceSummaryBody">

                            <tr>

                                <td
                                    colspan="10"
                                    class="text-center text-muted py-5"
                                >

                                    Loading summary...

                                </td>

                            </tr>

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </div>

</main>


<?php require_once __DIR__.'/../layouts/footer.php'; ?>


<!-- jQuery -->

<script
    src="https://code.jquery.com/jquery-3.7.1.min.js">
</script>


<!-- Attendance Summary JS -->

<script
    src="../../public/js/attendence_summary.js">
</script>
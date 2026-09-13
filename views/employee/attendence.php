<?php

Auth::requireLogin();

$pageTitle = 'Today Attendance';

require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/sidenav.php';

?>

<main class="main-content">

    <?php require_once __DIR__ . '/../layouts/topbar.php'; ?>


    <div class="container py-5">


        <!-- =========================================================
             PAGE HEADER
        ========================================================== -->

        <div class="d-flex justify-content-between align-items-center mb-4">

            <div>

                <h3 class="mb-1">
                    Today's Attendance
                </h3>

                <p class="text-muted mb-0">
                    View your attendance and leave status for today.
                </p>

            </div>


            <div class="d-flex gap-2">

                <a
                    href="../../employee/dashboard.php"
                    class="btn btn-primary"
                >
                    Dashboard
                </a>


                <a
                    href="#"
                    id="attendanceHistoryLink"
                    class="btn btn-secondary"
                >
                    Attendance History
                </a>

            </div>

        </div>



        <!-- =========================================================
             TODAY DATE & STATUS
        ========================================================== -->

        <div class="card mb-4">

            <div class="card-body">

                <div class="row align-items-center">


                    <!-- DATE -->
                    <div class="col-md-6">

                        <small class="text-muted">
                            Attendance Date
                        </small>

                        <h5
                            id="attendanceDate"
                            class="mb-0 mt-1"
                        >
                            Loading...
                        </h5>

                    </div>


                    <!-- STATUS -->
                    <div class="col-md-6 text-md-end mt-3 mt-md-0">

                        <small class="text-muted">
                            Today's Status
                        </small>

                        <div class="mt-1">

                            <span
                                id="attendanceStatus"
                                class="badge bg-secondary"
                            >
                                Loading...
                            </span>

                        </div>

                    </div>

                </div>

            </div>

        </div>



        <!-- =========================================================
             MESSAGE
        ========================================================== -->

        <div
            id="attendanceMessage"
            class="alert d-none"
            role="alert"
        ></div>



        <!-- =========================================================
             MAIN STATUS CARD
        ========================================================== -->

        <div class="card">

            <div class="card-header">

                <h5
                    id="statusCardTitle"
                    class="mb-0"
                >
                    Today's Status
                </h5>

            </div>


            <div class="card-body">


                <!-- =================================================
                     LOADING
                ================================================== -->

                <div
                    id="attendanceLoading"
                    class="text-center py-5"
                >

                    <div
                        class="spinner-border text-primary"
                        role="status"
                    ></div>

                    <p class="text-muted mt-3 mb-0">
                        Loading today's status...
                    </p>

                </div>



                <!-- =================================================
                     ATTENDANCE CONTENT
                ================================================== -->

                <div
                    id="attendanceContent"
                    class="d-none"
                >

                    <div class="row g-4">


                        <!-- =========================================
                             CHECK IN
                        ========================================== -->

                        <div class="col-md-4" id="checkIn">

                            <div class="card border h-100">

                                <div class="card-body">

                                    <small class="text-muted">
                                        Check In
                                    </small>

                                    <h4
                                        id="checkInTime"
                                        class="mt-2 mb-0"
                                    >
                                        --
                                    </h4>

                                </div>

                            </div>

                        </div>



                        <!-- =========================================
                             CHECK OUT
                        ========================================== -->

                        <div class="col-md-4" id="checkOut">

                            <div class="card border h-100">

                                <div class="card-body">

                                    <small class="text-muted">
                                        Check Out
                                    </small>

                                    <h4
                                        id="checkOutTime"
                                        class="mt-2 mb-0"
                                    >
                                        --
                                    </h4>

                                </div>

                            </div>

                        </div>



                        <!-- =========================================
                             WORKING HOURS
                        ========================================== -->

                        <div class="col-md-4" id="WorkHours">

                            <div class="card border h-100">

                                <div class="card-body">

                                    <small class="text-muted">
                                        Working Hours
                                    </small>

                                    <h4
                                        id="workingHours"
                                        class="mt-2 mb-0"
                                    >
                                        --
                                    </h4>

                                </div>

                            </div>

                        </div>



                        <!-- =========================================
                             REMARKS
                        ========================================== -->

                        <div class="col-12">

                            <div class="card border">

                                <div class="card-body">

                                    <small class="text-muted">
                                        Remarks
                                    </small>

                                    <p
                                        id="attendanceRemarks"
                                        class="mb-0 mt-2"
                                    >
                                        --
                                    </p>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>



                <!-- =================================================
                     LEAVE CONTENT
                ================================================== -->

                <div
                    id="leaveContent"
                    class="d-none"
                >

                    <div class="row g-4">


                        <!-- =========================================
                             LEAVE TYPE
                        ========================================== -->

                        <div class="col-md-4">

                            <div class="card border h-100">

                                <div class="card-body">

                                    <small class="text-muted">
                                        Leave Type
                                    </small>

                                    <h5
                                        id="leaveType"
                                        class="mt-2 mb-0 text-capitalize"
                                    >
                                        --
                                    </h5>

                                </div>

                            </div>

                        </div>



                        <!-- =========================================
                             LEAVE START DATE
                        ========================================== -->

                        <div class="col-md-4">

                            <div class="card border h-100">

                                <div class="card-body">

                                    <small class="text-muted">
                                        Start Date
                                    </small>

                                    <h5
                                        id="leaveStartDate"
                                        class="mt-2 mb-0"
                                    >
                                        --
                                    </h5>

                                </div>

                            </div>

                        </div>



                        <!-- =========================================
                             LEAVE END DATE
                        ========================================== -->

                        <div class="col-md-4">

                            <div class="card border h-100">

                                <div class="card-body">

                                    <small class="text-muted">
                                        End Date
                                    </small>

                                    <h5
                                        id="leaveEndDate"
                                        class="mt-2 mb-0"
                                    >
                                        --
                                    </h5>

                                </div>

                            </div>

                        </div>



                        <!-- =========================================
                             LEAVE STATUS
                        ========================================== -->

                        <div class="col-md-6">

                            <div class="card border h-100">

                                <div class="card-body">

                                    <small class="text-muted">
                                        Leave Status
                                    </small>

                                    <div class="mt-2">

                                        <span
                                            id="leaveStatus"
                                            class="badge bg-secondary"
                                        >
                                            --
                                        </span>

                                    </div>

                                </div>

                            </div>

                        </div>



                        <!-- =========================================
                             LEAVE REASON
                        ========================================== -->

                        <div class="col-md-6">

                            <div class="card border h-100">

                                <div class="card-body">

                                    <small class="text-muted">
                                        Reason
                                    </small>

                                    <p
                                        id="leaveReason"
                                        class="mb-0 mt-2"
                                    >
                                        --
                                    </p>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>



                <!-- =================================================
                     NO ATTENDANCE / NO LEAVE
                ================================================== -->

                <div
                    id="noAttendance"
                    class="d-none text-center py-5"
                >

                    <div class="mb-3">

                        <span
                            class="badge bg-warning text-dark px-3 py-2"
                        >
                            Not Marked
                        </span>

                    </div>


                    <h5>
                        Today's attendance is not available.
                    </h5>


                    <p class="text-muted mb-0">
                        No attendance or approved leave was found
                        for today.
                    </p>

                </div>



                <!-- =================================================
                     ERROR CONTENT
                ================================================== -->

                <div
                    id="attendanceError"
                    class="d-none text-center py-5"
                >

                    <div class="mb-3">

                        <span
                            class="badge bg-danger px-3 py-2"
                        >
                            Error
                        </span>

                    </div>


                    <h5>
                        Unable to load today's status.
                    </h5>


                    <p
                        id="attendanceErrorMessage"
                        class="text-muted mb-0"
                    >
                        Something went wrong.
                    </p>

                </div>


            </div>

        </div>

    </div>

</main>



<?php require_once __DIR__ . '/../layouts/footer.php'; ?>



<!-- =========================================================
     jQuery
========================================================== -->

<script
    src="https://code.jquery.com/jquery-3.7.1.min.js"
></script>



<!-- =========================================================
     Employee Attendance JavaScript
========================================================== -->

<script
    src="../../public/js/employee_attendance.js"
></script>
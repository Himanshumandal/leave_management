<?php

Auth::requireLogin();

$pageTitle = 'Leave Management';

require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/sidenav.php';

?>

<main class="main-content">

    <?php require_once __DIR__ . '/../layouts/topbar.php'; ?>


    <!-- =====================================================
         LEAVE PAGE CONTENT
    ====================================================== -->

    <div class="dashboard-container">




        <!-- =================================================
             MESSAGE
        ================================================== -->

        <div
            id="leaveMessage"
            class="alert d-none mb-4"
            role="alert"
        ></div>



        <!-- =================================================
             APPLY LEAVE
        ================================================== -->

        <div class="row g-4 mb-4">


            <!-- APPLY FORM -->

            <div class="col-lg-8">

                <div class="content-card">


                    <div class="content-card-header mb-4">

                        <div>

                            <h5>
                                Apply for Leave
                            </h5>

                            <small class="text-muted">
                                Submit a new leave request
                            </small>

                        </div>

                    </div>



                    <form id="leaveForm">


                        <!-- LEAVE TYPE -->

                        <div class="mb-4">

                            <label
                                for="leaveType"
                                class="form-label"
                            >
                                Leave Type
                            </label>


                            <select
                                id="leaveType"
                                name="leave_type"
                                class="form-select"
                            >

                                <option value="">
                                    Select Leave Type
                                </option>

                                <option value="casual">
                                    Casual Leave
                                </option>

                                <option value="sick">
                                    Sick Leave
                                </option>

                                <option value="annual">
                                    Annual Leave
                                </option>

                                <option value="unpaid">
                                    Unpaid Leave
                                </option>

                                <option value="other">
                                    Other
                                </option>

                            </select>


                            <div
                                id="leaveTypeError"
                                class="text-danger small mt-1"
                            ></div>

                        </div>



                        <!-- DATES -->

                        <div class="row">


                            <!-- START DATE -->

                            <div class="col-md-6 mb-4">

                                <label
                                    for="startDate"
                                    class="form-label"
                                >
                                    Start Date
                                </label>


                                <input
                                    type="date"
                                    id="startDate"
                                    name="start_date"
                                    class="form-control"
                                >


                                <div
                                    id="startDateError"
                                    class="text-danger small mt-1"
                                ></div>

                            </div>



                            <!-- END DATE -->

                            <div class="col-md-6 mb-4">

                                <label
                                    for="endDate"
                                    class="form-label"
                                >
                                    End Date
                                </label>


                                <input
                                    type="date"
                                    id="endDate"
                                    name="end_date"
                                    class="form-control"
                                >


                                <div
                                    id="endDateError"
                                    class="text-danger small mt-1"
                                ></div>

                            </div>

                        </div>



                        <!-- REASON -->

                        <div class="mb-4">

                            <label
                                for="reason"
                                class="form-label"
                            >
                                Reason
                            </label>


                            <textarea
                                id="reason"
                                name="reason"
                                rows="4"
                                class="form-control"
                                placeholder="Enter reason for leave"
                            ></textarea>


                            <div
                                id="reasonError"
                                class="text-danger small mt-1"
                            ></div>

                        </div>



                        <!-- BUTTONS -->

                        <div>

                            <button
                                type="submit"
                                id="submitLeaveBtn"
                                class="btn btn-primary"
                            >

                                <i class="bi bi-send me-1"></i>

                                Submit Leave Request

                            </button>


                            <button
                                type="reset"
                                id="resetLeaveBtn"
                                class="btn btn-light border ms-2"
                            >

                                Reset

                            </button>

                        </div>


                    </form>

                </div>

            </div>



            <!-- =================================================
                 LEAVE SUMMARY
            ================================================== -->

            <div class="col-lg-4">

                <div class="content-card">

                    <div class="content-card-header mb-4">

                        <div>

                            <h5>
                                Leave Summary
                            </h5>

                            <small class="text-muted">
                                Your current leave information
                            </small>

                        </div>

                    </div>



                    <!-- BALANCE -->

                    <div class="leave-summary-item">

                        <div class="leave-summary-icon bg-warning-subtle text-warning">

                            <i class="bi bi-calendar-minus"></i>

                        </div>


                        <div>

                            <small>
                                Leave Balance
                            </small>

                            <strong>
                                12 Days
                            </strong>

                        </div>

                    </div>



                    <!-- PENDING -->

                    <div class="leave-summary-item">

                        <div class="leave-summary-icon bg-info-subtle text-info">

                            <i class="bi bi-clock-history"></i>

                        </div>


                        <div>

                            <small>
                                Pending Requests
                            </small>

                            <strong>
                                2 Requests
                            </strong>

                        </div>

                    </div>



                    <!-- APPROVED -->

                    <div class="leave-summary-item">

                        <div class="leave-summary-icon bg-success-subtle text-success">

                            <i class="bi bi-check-circle"></i>

                        </div>


                        <div>

                            <small>
                                Approved This Year
                            </small>

                            <strong>
                                8 Days
                            </strong>

                        </div>

                    </div>



                    <div class="leave-info-box">

                        <i class="bi bi-info-circle me-2"></i>

                        Submit your leave request in advance whenever possible.

                    </div>

                </div>

            </div>

        </div>



        <!-- =================================================
             LEAVE HISTORY
        ================================================== -->

        <div class="content-card">


            <div class="content-card-header mb-4">

                <div>

                    <h5>
                        My Leave Requests
                    </h5>

                    <small class="text-muted">
                        View and track your previous leave applications
                    </small>

                </div>

            </div>



            <div class="table-responsive">


                <table class="table leave-table align-middle">

                    <thead>

                        <tr>

                            <th>
                                #
                            </th>

                            <th>
                                Leave Type
                            </th>

                            <th>
                                Start Date
                            </th>

                            <th>
                                End Date
                            </th>

                            <th>
                                Reason
                            </th>

                            <th>
                                Status
                            </th>

                            <th>
                                Admin Remarks
                            </th>

                            <th>
                                Applied On
                            </th>

                        </tr>

                    </thead>


                    <tbody id="leaveTableBody">

                        <tr>

                            <td
                                colspan="8"
                                class="text-center text-muted py-5"
                            >

                                Loading leave requests...

                            </td>

                        </tr>

                    </tbody>

                </table>

            </div>

        </div>


    </div>

</main>
<script
        src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>



<!-- =========================================================
     EMPLOYEE JAVASCRIPT
========================================================= -->

<script
    src="../../public/js/employee_leaves.js"
></script>


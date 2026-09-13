<?php

// require_once '../../config/bootstrap.php';

Auth::requireAdmin();

$pageTitle = 'Leave Management';


require_once __DIR__.'/../layouts/header.php';

require_once __DIR__.'/../layouts/sidenav.php';

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
                    href="../../dashboard.php"
                    class="btn btn-dark"
                >
                    Dashboard
                </a>

            </div>

        </div>



        <!-- ================================= -->
        <!-- MESSAGE -->
        <!-- ================================= -->

        <div
            id="adminLeaveMessage"
            class="alert d-none"
        ></div>



        <!-- ================================= -->
        <!-- FILTER -->
        <!-- ================================= -->

        <div class="card mb-4">

            <div class="card-header">

                <h5 class="mb-0">
                    Leave Filters
                </h5>

            </div>


            <div class="card-body">

                <div class="row g-3 align-items-end">


                    <!-- STATUS -->

                    <div class="col-md-4">

                        <label
                            for="statusFilter"
                            class="form-label"
                        >
                            Status
                        </label>


                        <select
                            id="statusFilter"
                            class="form-select"
                        >

                            <option value="">
                                All
                            </option>

                            <option value="pending">
                                Pending
                            </option>

                            <option value="approved">
                                Approved
                            </option>

                            <option value="rejected">
                                Rejected
                            </option>

                        </select>

                    </div>



                    <!-- FILTER BUTTON -->

                    <div class="col-md-2">

                        <button
                            type="button"
                            id="filterBtn"
                            class="btn btn-primary w-100"
                        >
                            Filter
                        </button>

                    </div>



                    <!-- RESET BUTTON -->

                    <div class="col-md-2">

                        <button
                            type="button"
                            id="resetFilterBtn"
                            class="btn btn-secondary w-100"
                        >
                            Reset
                        </button>

                    </div>

                </div>

            </div>

        </div>



        <!-- ================================= -->
        <!-- LEAVE REQUESTS TABLE -->
        <!-- ================================= -->

        <div class="card">

            <div class="card-header">

                <h5 class="mb-0">
                    Leave Requests
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
                                    Employee
                                </th>

                                <th>
                                    Department
                                </th>

                                <th>
                                    Leave Type
                                </th>

                                <th>
                                    Start
                                </th>

                                <th>
                                    End
                                </th>

                                <th>
                                    Reason
                                </th>

                                <th>
                                    Status
                                </th>

                                <th>
                                    Action
                                </th>

                            </tr>

                        </thead>


                        <tbody id="adminLeaveTableBody">

                            <tr>

                                <td
                                    colspan="9"
                                    class="text-center py-5 text-muted"
                                >

                                    Loading...

                                </td>

                            </tr>

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </div>

</main>



<!-- ================================= -->
<!-- APPROVAL MODAL -->
<!-- ================================= -->

<div
    class="modal fade"
    id="leaveActionModal"
    tabindex="-1"
>

    <div class="modal-dialog">

        <div class="modal-content">


            <div class="modal-header">

                <h5
                    class="modal-title"
                    id="leaveModalTitle"
                >
                    Process Leave
                </h5>


                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                ></button>

            </div>


            <div class="modal-body">


                <input
                    type="hidden"
                    id="modalLeaveId"
                >


                <div class="mb-3">

                    <label class="form-label">
                        Decision
                    </label>


                    <select
                        id="modalStatus"
                        class="form-select"
                    >

                        <option value="approved">
                            Approve
                        </option>

                        <option value="rejected">
                            Reject
                        </option>

                    </select>

                </div>


                <div class="mb-3">

                    <label
                        for="modalRemarks"
                        class="form-label"
                    >
                        Admin Remarks
                    </label>


                    <textarea
                        id="modalRemarks"
                        rows="4"
                        class="form-control"
                        placeholder="Enter remarks..."
                    ></textarea>

                </div>

            </div>


            <div class="modal-footer">

                <button
                    type="button"
                    class="btn btn-secondary"
                    data-bs-dismiss="modal"
                >
                    Cancel
                </button>


                <button
                    type="button"
                    id="saveLeaveActionBtn"
                    class="btn btn-primary"
                >
                    Save
                </button>

            </div>

        </div>

    </div>

</div>



<?php require_once __DIR__.'/../layouts/footer.php'; ?>


<!-- jQuery -->

<script
    src="https://code.jquery.com/jquery-3.7.1.min.js"
>
</script>


<!-- Bootstrap JS -->

<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
>
</script>


<!-- Admin Leave JavaScript -->

<script
    src="../../public/js/admin_leaves.js"
>
</script>


</body>

</html>
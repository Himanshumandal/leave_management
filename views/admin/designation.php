<?php

Auth::requireAdmin();

$pageTitle = 'Designation Management';

require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/sidenav.php';

?>

<main class="main-content">

    <?php require_once __DIR__ . '/../layouts/topbar.php'; ?>


    <div class="container py-5">


        <!-- =====================================================
             PAGE HEADER
        ====================================================== -->

        <div
            class="d-flex justify-content-end align-items-center flex-wrap mb-4"
        >

            <div class="header-actions">

                <button
                    type="button"
                    class="btn btn-primary"
                    id="addDesignationBtn"
                    data-bs-toggle="modal"
                    data-bs-target="#designationModal"
                >

                    <i class="bi bi-plus-lg"></i>

                    Add Designation

                </button>

            </div>

        </div>


        <!-- =====================================================
             MESSAGE
        ====================================================== -->

        <div id="message"></div>


        <!-- =====================================================
             DESIGNATION CARD
        ====================================================== -->

        <div class="card employee-card">


            <!-- =================================================
                 CARD HEADER
            ================================================== -->

            <div class="card-header employee-card-header">

                <div class="row align-items-center g-3">


                    <!-- TITLE -->

                    <div class="col-lg-4">

                        <h5 class="mb-1">
                            Designations
                        </h5>

                        <span class="text-muted small">
                            Manage designations
                        </span>

                    </div>


                    <!-- FILTERS -->

                    <div class="col-lg-8">

                        <div
                            class="row g-4 justify-content-lg-end"
                        >


                            <!-- SEARCH -->

                            <div class="col-md-4">

                                <div class="d-flex justify-content-center align-items-center gap-3 search-box w-100">

                                    <i class="bi bi-search"></i>

                                    <input
                                        type="text"
                                        id="designationSearch"
                                        class="form-control"
                                        placeholder="Search designation..."
                                        autocomplete="off"
                                    >

                                </div>

                            </div>


                            <!-- DEPARTMENT -->

                            <div class="col-md-3">

                                <select
                                    id="designationDepartmentFilter"
                                    class="form-select"
                                >

                                    <option value="">
                                        All Departments
                                    </option>

                                </select>

                            </div>


                            <!-- STATUS -->

                            <div class="col-md-3">

                                <select
                                    id="designationStatusFilter"
                                    class="form-select"
                                >

                                    <option value="">
                                        All Status
                                    </option>

                                    <option value="active">
                                        Active
                                    </option>

                                    <option value="inactive">
                                        Inactive
                                    </option>

                                </select>

                            </div>


                            <!-- RESET -->

                            <div class="col-md-2">

                                <button
                                    type="button"
                                    id="resetDesignationFilters"
                                    class="btn btn-secondary w-100"
                                >

                                    <i
                                        class="bi bi-arrow-counterclockwise me-1"
                                    ></i>

                                    Reset

                                </button>

                            </div>

                        </div>

                    </div>

                </div>

            </div>


            <!-- =================================================
                 TABLE
            ================================================== -->

            <div class="card-body p-0">

                <div class="table-responsive">

                    <table
                        class="table table-bordered table-hover align-middle employee-table mb-0"
                    >

                        <thead class="table-light">

                            <tr>

                                <th>
                                    ID
                                </th>

                                <th>
                                    Designation
                                </th>

                                <th>
                                    Department
                                </th>

                                <th>
                                    Status
                                </th>

                                <th class="text-center">
                                    Actions
                                </th>

                            </tr>

                        </thead>


                        <tbody id="designationTableBody">

                            <tr>

                                <td
                                    colspan="5"
                                    class="text-center text-muted py-5"
                                >

                                    <div
                                        class="spinner-border spinner-border-sm text-primary"
                                        role="status"
                                    >

                                        <span class="visually-hidden">
                                            Loading...
                                        </span>

                                    </div>

                                    <div class="mt-2">

                                        Loading designations...

                                    </div>

                                </td>

                            </tr>

                        </tbody>

                    </table>

                </div>

            </div>


            <!-- =================================================
                 PAGINATION
            ================================================== -->

            <div
                class="employee-pagination-wrapper d-flex justify-content-between align-items-center p-3"
            >

                <div
                    id="designationPaginationInfo"
                    class="text-muted small"
                >
                </div>


                <nav
                    aria-label="Designation pagination"
                >

                    <ul
                        id="designationPagination"
                        class="pagination mb-0"
                    ></ul>

                </nav>

            </div>


            <!-- =================================================
                 FOOTER
            ================================================== -->

            <div
                class="table-footer d-flex justify-content-between align-items-center p-3 border-top"
            >

                <span id="designationCount">
                    Loading designations...
                </span>

                <span>
                    Designation Management
                </span>

            </div>

        </div>

    </div>

</main>


<!-- =========================================================
     ADD DESIGNATION MODAL
========================================================= -->

<div
    class="modal fade"
    id="designationModal"
    tabindex="-1"
    aria-labelledby="designationModalLabel"
    aria-hidden="true"
>

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content">


            <!-- HEADER -->

            <div class="modal-header">

                <div>

                    <h5
                        class="modal-title"
                        id="designationModalLabel"
                    >
                        Add Designation
                    </h5>

                    <small class="text-muted">
                        Create a new designation
                    </small>

                </div>


                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="Close"
                ></button>

            </div>


            <!-- FORM -->

            <form id="designationForm">

                <div class="modal-body">

                    <div
                        id="designationFormMessage"
                        class="alert d-none"
                    ></div>


                    <!-- DESIGNATION -->

                    <div class="mb-3">

                        <label
                            for="designation_name"
                            class="form-label"
                        >

                            Designation Name

                            <span class="text-danger">*</span>

                        </label>

                        <input
                            type="text"
                            class="form-control"
                            id="designation_name"
                            name="name"
                            placeholder="Software Developer"
                        >

                        <div
                            id="designation_nameError"
                            class="field-error"
                        ></div>

                    </div>


                    <!-- DEPARTMENT -->

                    <div class="mb-3">

                        <label
                            for="designation_department"
                            class="form-label"
                        >

                            Department

                            <span class="text-danger">*</span>

                        </label>

                        <select
                            class="form-select"
                            id="designation_department"
                            name="department_id"
                        >

                            <option value="">
                                Select Department
                            </option>

                        </select>

                        <div
                            id="designation_department_idError"
                            class="field-error"
                        ></div>

                    </div>


                    <!-- STATUS -->

                    <div class="mb-3">

                        <label
                            for="designation_status"
                            class="form-label"
                        >

                            Status

                            <span class="text-danger">*</span>

                        </label>

                        <select
                            class="form-select"
                            id="designation_status"
                            name="status"
                        >

                            <option value="">
                                Select Status
                            </option>

                            <option value="active">
                                Active
                            </option>

                            <option value="inactive">
                                Inactive
                            </option>

                        </select>

                        <div
                            id="designation_statusError"
                            class="field-error"
                        ></div>

                    </div>

                </div>


                <!-- FOOTER -->

                <div class="modal-footer">

                    <button
                        type="button"
                        class="btn btn-light border"
                        data-bs-dismiss="modal"
                    >
                        Cancel
                    </button>


                    <button
                        type="submit"
                        class="btn btn-primary"
                        id="saveDesignationBtn"
                    >

                        <i class="bi bi-plus-lg me-1"></i>

                        Add Designation

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>


<!-- =========================================================
     EDIT DESIGNATION MODAL
========================================================= -->

<div
    class="modal fade"
    id="editDesignationModal"
    tabindex="-1"
    aria-hidden="true"
>

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content">


            <!-- HEADER -->

            <div class="modal-header">

                <div>

                    <h5 class="modal-title">
                        Edit Designation
                    </h5>

                    <small class="text-muted">
                        Update designation information
                    </small>

                </div>


                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="Close"
                ></button>

            </div>


            <!-- FORM -->

            <form id="editDesignationForm">

                <div class="modal-body">

                    <div
                        id="editDesignationFormMessage"
                        class="alert d-none"
                    ></div>


                    <input
                        type="hidden"
                        id="edit_designation_id"
                        name="id"
                    >


                    <!-- DESIGNATION -->

                    <div class="mb-3">

                        <label
                            for="edit_designation_name"
                            class="form-label"
                        >

                            Designation Name

                            <span class="text-danger">*</span>

                        </label>

                        <input
                            type="text"
                            class="form-control"
                            id="edit_designation_name"
                            name="name"
                        >

                        <div
                            id="edit_designation_nameError"
                            class="field-error"
                        ></div>

                    </div>


                    <!-- DEPARTMENT -->

                    <div class="mb-3">

                        <label
                            for="edit_designation_department"
                            class="form-label"
                        >

                            Department

                            <span class="text-danger">*</span>

                        </label>

                        <select
                            class="form-select"
                            id="edit_designation_department"
                            name="department_id"
                        >

                            <option value="">
                                Select Department
                            </option>

                        </select>

                        <div
                            id="edit_designation_department_idError"
                            class="field-error"
                        ></div>

                    </div>


                    <!-- STATUS -->

                    <div class="mb-3">

                        <label
                            for="edit_designation_status"
                            class="form-label"
                        >

                            Status

                            <span class="text-danger">*</span>

                        </label>

                        <select
                            class="form-select"
                            id="edit_designation_status"
                            name="status"
                        >

                            <option value="active">
                                Active
                            </option>

                            <option value="inactive">
                                Inactive
                            </option>

                        </select>

                        <div
                            id="edit_designation_statusError"
                            class="field-error"
                        ></div>

                    </div>

                </div>


                <!-- FOOTER -->

                <div class="modal-footer">

                    <button
                        type="button"
                        class="btn btn-light border"
                        data-bs-dismiss="modal"
                    >
                        Cancel
                    </button>


                    <button
                        type="submit"
                        class="btn btn-warning"
                        id="updateDesignationBtn"
                    >

                        <i class="bi bi-check2-circle me-1"></i>

                        Update Designation

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>


<!-- =========================================================
     DELETE DESIGNATION MODAL
========================================================= -->

<div
    class="modal fade"
    id="deleteDesignationModal"
    tabindex="-1"
    aria-labelledby="deleteDesignationModalLabel"
    aria-hidden="true"
>

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content">


            <!-- HEADER -->

            <div class="modal-header">

                <h5
                    class="modal-title"
                    id="deleteDesignationModalLabel"
                >
                    Delete Designation
                </h5>


                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="Close"
                ></button>

            </div>


            <!-- BODY -->

            <div class="modal-body text-center">

                <div class="delete-modal-icon">

                    <i class="bi bi-trash3"></i>

                </div>


                <div class="delete-title">

                    Delete this designation?

                </div>


                <p class="delete-description">

                    You are about to permanently delete

                    <span
                        id="deleteDesignationName"
                        class="delete-employee-name"
                    >
                        this designation
                    </span>.

                    This action cannot be undone.

                </p>


                <div class="delete-warning text-start">

                    <i class="bi bi-exclamation-triangle-fill"></i>

                    All information associated with this designation
                    may be affected.

                </div>


                <input
                    type="hidden"
                    id="delete_designation_id"
                >

            </div>


            <!-- FOOTER -->

            <div class="modal-footer justify-content-end">

                <button
                    type="button"
                    class="btn btn-light border"
                    data-bs-dismiss="modal"
                    id="cancelDeleteDesignationBtn"
                >
                    Cancel
                </button>


                <button
                    type="button"
                    class="btn btn-danger"
                    id="confirmDeleteDesignationBtn"
                >

                    <i class="bi bi-trash3 me-1"></i>

                    Delete Designation

                </button>

            </div>

        </div>

    </div>

</div>


<script
    src="https://code.jquery.com/jquery-3.7.1.min.js"
></script>


<?php require_once __DIR__ . '/../layouts/footer.php'; ?>


<!-- =========================================================
     DESIGNATION JAVASCRIPT
========================================================= -->

<script
    src="../../public/js/designation.js"
></script>


</body>

</html>
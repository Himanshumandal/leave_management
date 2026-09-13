<?php

Auth::requireAdmin();

$pageTitle = 'Department Management';

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
                    id="addDepartmentBtn"
                    data-bs-toggle="modal"
                    data-bs-target="#departmentModal"
                >

                    <i class="bi bi-plus-lg"></i>

                    Add Department

                </button>

            </div>

        </div>


        <!-- =====================================================
             MESSAGE
        ====================================================== -->

        <div id="message"></div>


        <!-- =====================================================
             DEPARTMENT CARD
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
                            Departments
                        </h5>

                        <span class="text-muted small">
                            Manage departments
                        </span>

                    </div>


                    <!-- FILTERS -->

                    <div class="col-lg-8">

                        <div
                            class="row g-4 justify-content-lg-end"
                        >


                            <!-- SEARCH -->

                            <div class="col-md-5">

                                <div class="d-flex justify-content-center align-items-center gap-3 search-box w-100">

                                    <i class="bi bi-search"></i>

                                    <input
                                        type="text"
                                        id="departmentSearch"
                                        class="form-control"
                                        placeholder="Search department..."
                                        autocomplete="off"
                                    >

                                </div>

                            </div>


                            <!-- STATUS -->

                            <div class="col-md-3">

                                <select
                                    id="departmentStatusFilter"
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
                                    id="resetDepartmentFilters"
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


                        <tbody id="departmentTableBody">

                            <tr>

                                <td
                                    colspan="4"
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

                                        Loading departments...

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
                    id="departmentPaginationInfo"
                    class="text-muted small"
                >
                </div>


                <nav
                    aria-label="Department pagination"
                >

                    <ul
                        id="departmentPagination"
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

                <span id="departmentCount">
                    Loading departments...
                </span>

                <span>
                    Department Management
                </span>

            </div>

        </div>

    </div>

</main>


<!-- =========================================================
     ADD DEPARTMENT MODAL
========================================================= -->

<div
    class="modal fade"
    id="departmentModal"
    tabindex="-1"
    aria-labelledby="departmentModalLabel"
    aria-hidden="true"
>

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content">


            <!-- HEADER -->

            <div class="modal-header">

                <div>

                    <h5
                        class="modal-title"
                        id="departmentModalLabel"
                    >
                        Add Department
                    </h5>

                    <small class="text-muted">
                        Create a new department
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

            <form id="departmentForm">

                <div class="modal-body">

                    <div
                        id="departmentFormMessage"
                        class="alert d-none"
                    ></div>


                    <!-- DEPARTMENT -->

                    <div class="mb-3">

                        <label
                            for="department_name"
                            class="form-label"
                        >

                            Department Name

                            <span class="text-danger">*</span>

                        </label>

                        <input
                            type="text"
                            class="form-control"
                            id="department_name"
                            name="department"
                            placeholder="IT"
                        >

                        <div
                            id="department_nameError"
                            class="field-error"
                        ></div>

                    </div>


                    <!-- STATUS -->

                    <div class="mb-3">

                        <label
                            for="department_status"
                            class="form-label"
                        >

                            Status

                            <span class="text-danger">*</span>

                        </label>

                        <select
                            class="form-select"
                            id="department_status"
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
                            id="department_statusError"
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
                        id="saveDepartmentBtn"
                    >

                        <i class="bi bi-plus-lg me-1"></i>

                        Add Department

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>


<!-- =========================================================
     EDIT DEPARTMENT MODAL
========================================================= -->

<div
    class="modal fade"
    id="editDepartmentModal"
    tabindex="-1"
    aria-hidden="true"
>

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content">


            <!-- HEADER -->

            <div class="modal-header">

                <div>

                    <h5 class="modal-title">
                        Edit Department
                    </h5>

                    <small class="text-muted">
                        Update department information
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

            <form id="editDepartmentForm">

                <div class="modal-body">

                    <div
                        id="editDepartmentFormMessage"
                        class="alert d-none"
                    ></div>


                    <input
                        type="hidden"
                        id="edit_department_id"
                        name="id"
                    >


                    <!-- DEPARTMENT -->

                    <div class="mb-3">

                        <label
                            for="edit_department_name"
                            class="form-label"
                        >

                            Department Name

                            <span class="text-danger">*</span>

                        </label>

                        <input
                            type="text"
                            class="form-control"
                            id="edit_department_name"
                            name="department"
                        >

                        <div
                            id="edit_department_nameError"
                            class="field-error"
                        ></div>

                    </div>


                    <!-- STATUS -->

                    <div class="mb-3">

                        <label
                            for="edit_department_status"
                            class="form-label"
                        >

                            Status

                            <span class="text-danger">*</span>

                        </label>

                        <select
                            class="form-select"
                            id="edit_department_status"
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
                            id="edit_department_statusError"
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
                        id="updateDepartmentBtn"
                    >

                        <i class="bi bi-check2-circle me-1"></i>

                        Update Department

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>


<!-- =========================================================
     DELETE DEPARTMENT MODAL
========================================================= -->

<div
    class="modal fade"
    id="deleteDepartmentModal"
    tabindex="-1"
    aria-labelledby="deleteDepartmentModalLabel"
    aria-hidden="true"
>

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content">


            <!-- HEADER -->

            <div class="modal-header">

                <h5
                    class="modal-title"
                    id="deleteDepartmentModalLabel"
                >
                    Delete Department
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

                    Delete this department?

                </div>


                <p class="delete-description">

                    You are about to permanently delete

                    <span
                        id="deleteDepartmentName"
                        class="delete-employee-name"
                    >
                        this department
                    </span>.

                    This action cannot be undone.

                </p>


                <div class="delete-warning text-start">

                    <i class="bi bi-exclamation-triangle-fill"></i>

                    All information associated with this department
                    may be affected.

                </div>


                <input
                    type="hidden"
                    id="delete_department_id"
                >

            </div>


            <!-- FOOTER -->

            <div class="modal-footer justify-content-end">

                <button
                    type="button"
                    class="btn btn-light border"
                    data-bs-dismiss="modal"
                    id="cancelDeleteDepartmentBtn"
                >
                    Cancel
                </button>


                <button
                    type="button"
                    class="btn btn-danger"
                    id="confirmDeleteDepartmentBtn"
                >

                    <i class="bi bi-trash3 me-1"></i>

                    Delete Department

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
     DEPARTMENT JAVASCRIPT
========================================================= -->

<script
    src="../../public/js/department.js"
></script>


</body>

</html>
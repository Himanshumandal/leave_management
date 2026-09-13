<?php

// require_once '../../config/bootstrap.php';

Auth::requireAdmin();

$pageTitle = 'Employee Management';


require_once __DIR__.'/../layouts/header.php';

require_once __DIR__.'/../layouts/sidenav.php';

?>

<main class="main-content">

    <?php require_once __DIR__.'/../layouts/topbar.php'; ?>


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
                    class="btn btn-primary btn-add-employee"
                    id="addEmployeeBtn"
                    data-bs-toggle="modal"
                    data-bs-target="#employeeModal"
                >

                    <i class="bi bi-plus-lg"></i>

                    Add Employee

                </button>

            </div>

        </div>



        <!-- =====================================================
             MESSAGE
        ====================================================== -->

        <div id="message"></div>



        <!-- =====================================================
             EMPLOYEE CARD
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
                            Employees
                        </h5>

                        <span class="text-muted small">
                            View and manage all employees
                        </span>

                    </div>



                    <!-- FILTERS -->

                    <div class="col-lg-8">

                        <div
                            class="row g-4 justify-content-lg-end"
                        >


                            <!-- SEARCH -->

                            <div class="col-md-5">

                                <div class="d-flex justify-content-center align-items-center gap-2 search-box w-100">

                                    <i class="bi bi-search"></i>

                                    <input
                                        type="text"
                                        id="employeeSearch"
                                        class="form-control"
                                        placeholder="Search employee ID, name or email..."
                                        autocomplete="off"
                                    >

                                </div>

                            </div>



                            <!-- DEPARTMENT -->

                            <div class="col-md-3">

                                <select
                                    id="departmentFilter"
                                    class="form-select"
                                >

                                    <option value="">
                                        All Departments
                                    </option>

                                    <option value="IT">
                                        IT
                                    </option>

                                    <option value="HR">
                                        HR
                                    </option>

                                    <option value="Finance">
                                        Finance
                                    </option>

                                    <option value="Marketing">
                                        Marketing
                                    </option>

                                    <option value="Sales">
                                        Sales
                                    </option>

                                </select>

                            </div>



                            <!-- STATUS -->

                            <div class="col-md-2">

                                <select
                                    id="statusFilter"
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
                                    id="resetEmployeeFilters"
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
                                    Employee ID
                                </th>

                                <th>
                                    Employee
                                </th>

                                <th>
                                    Email
                                </th>

                                <th>
                                    Phone
                                </th>

                                <th>
                                    Department
                                </th>

                                <th>
                                    Designation
                                </th>

                                <th>
                                    Joining Date
                                </th>

                                <th>
                                    Status
                                </th>

                                <th class="text-center">
                                    Action
                                </th>

                            </tr>

                        </thead>


                        <tbody id="employeeTableBody">

                            <tr>

                                <td
                                    colspan="10"
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

                                        Loading employees...

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

                <!-- PAGINATION INFO -->

                <div
                    id="employeePaginationInfo"
                    class="text-muted small"
                >
                </div>



                <!-- PAGINATION -->

                <nav
                    aria-label="Employee pagination"
                >

                    <ul
                        id="employeePagination"
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

                <span id="employeeCount">
                    Loading employees...
                </span>

                <span>
                    Employee Management
                </span>

            </div>

        </div>

    </div>

</main>



<!-- =========================================================
     ADD EMPLOYEE MODAL
========================================================= -->

<div
    class="modal fade"
    id="employeeModal"
    tabindex="-1"
    aria-labelledby="employeeModalLabel"
    aria-hidden="true"
>

    <div class="modal-dialog modal-lg modal-dialog-centered">

        <div class="modal-content">


            <!-- HEADER -->

            <div class="modal-header">

                <div>

                    <h5
                        class="modal-title"
                        id="employeeModalLabel"
                    >
                        Add Employee
                    </h5>

                    <small class="text-muted">
                        Create a new employee account
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

            <form id="employeeForm">

                <div class="modal-body">


                    <div
                        id="formMessage"
                        class="alert d-none"
                    ></div>


                    <div class="row">


                        <!-- EMPLOYEE ID -->

                        <div class="col-md-6 mb-3">

                            <label
                                for="employee_id"
                                class="form-label"
                            >

                                Employee ID

                                <span class="text-danger">*</span>

                            </label>


                            <input
                                type="text"
                                class="form-control"
                                id="employee_id"
                                name="employee_id"
                                placeholder="EMP001"
                            >


                            <div
                                id="employee_idError"
                                class="field-error"
                            ></div>

                        </div>



                        <!-- FIRST NAME -->

                        <div class="col-md-6 mb-3">

                            <label
                                for="first_name"
                                class="form-label"
                            >

                                First Name

                                <span class="text-danger">*</span>

                            </label>


                            <input
                                type="text"
                                class="form-control"
                                id="first_name"
                                name="first_name"
                                placeholder="Rahul"
                            >


                            <div
                                id="first_nameError"
                                class="field-error"
                            ></div>

                        </div>



                        <!-- LAST NAME -->

                        <div class="col-md-6 mb-3">

                            <label
                                for="last_name"
                                class="form-label"
                            >

                                Last Name

                                <span class="text-danger">*</span>

                            </label>


                            <input
                                type="text"
                                class="form-control"
                                id="last_name"
                                name="last_name"
                                placeholder="Sharma"
                            >


                            <div
                                id="last_nameError"
                                class="field-error"
                            ></div>

                        </div>



                        <!-- EMAIL -->

                        <div class="col-md-6 mb-3">

                            <label
                                for="email"
                                class="form-label"
                            >

                                Email

                                <span class="text-danger">*</span>

                            </label>


                            <input
                                type="email"
                                class="form-control"
                                id="email"
                                name="email"
                                placeholder="employee@gmail.com"
                            >


                            <div
                                id="emailError"
                                class="field-error"
                            ></div>

                        </div>



                        <!-- PHONE -->

                        <div class="col-md-6 mb-3">

                            <label
                                for="phone"
                                class="form-label"
                            >

                                Phone

                                <span class="text-danger">*</span>

                            </label>


                            <input
                                type="text"
                                class="form-control"
                                id="phone"
                                name="phone"
                                maxlength="15"
                                placeholder="9876543210"
                            >


                            <div
                                id="phoneError"
                                class="field-error"
                            ></div>

                        </div>



                        <!-- DEPARTMENT -->

                        <div class="col-md-6 mb-3">

                            <label
                                for="department"
                                class="form-label"
                            >

                                Department

                                <span class="text-danger">*</span>

                            </label>


                            <select
                                class="form-select"
                                id="department"
                                name="department"
                            >

                                <option value="">
                                    Select Department
                                </option>

                               

                            </select>


                            <div
                                id="departmentError"
                                class="field-error"
                            ></div>

                        </div>



                        <!-- DESIGNATION -->

                        <div class="col-md-6 mb-3">

                            <label
                                for="designation"
                                class="form-label"
                            >

                                Designation

                                <span class="text-danger">*</span>

                            </label>


                            <select
                                class="form-select"
                                id="designation"
                                name="designation"
                            >

                                <option value="">
                                    Select Department First
                                </option>

                            </select>


                            <div
                                id="designationError"
                                class="field-error"
                            ></div>

                        </div>



                        <!-- JOINING DATE -->

                        <div class="col-md-6 mb-3">

                            <label
                                for="joining_date"
                                class="form-label"
                            >

                                Joining Date

                                <span class="text-danger">*</span>

                            </label>


                            <input
                                type="date"
                                class="form-control"
                                id="joining_date"
                                name="joining_date"
                            >


                            <div
                                id="joining_dateError"
                                class="field-error"
                            ></div>

                        </div>



                        <!-- STATUS -->

                        <div class="col-md-6 mb-3">

                            <label
                                for="status"
                                class="form-label"
                            >

                                Status

                                <span class="text-danger">*</span>

                            </label>


                            <select
                                class="form-select"
                                id="status"
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
                                id="statusError"
                                class="field-error"
                            ></div>

                        </div>

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
                        id="saveEmployeeBtn"
                    >

                        <i class="bi bi-person-plus me-1"></i>

                        Add Employee

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>



<!-- =========================================================
     EDIT EMPLOYEE MODAL
========================================================= -->

<div
    class="modal fade"
    id="editEmployeeModal"
    tabindex="-1"
    aria-hidden="true"
>

    <div class="modal-dialog modal-lg modal-dialog-centered">

        <div class="modal-content">


            <!-- HEADER -->

            <div class="modal-header">

                <div>

                    <h5 class="modal-title">
                        Edit Employee
                    </h5>

                    <small class="text-muted">
                        Update employee information
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

            <form id="editEmployeeForm">

                <div class="modal-body">


                    <div
                        id="editFormMessage"
                        class="alert d-none"
                    ></div>


                    <input
                        type="hidden"
                        id="edit_id"
                        name="id"
                    >


                    <div class="row">


                        <!-- EMPLOYEE ID -->

                        <div class="col-md-6 mb-3">

                            <label class="form-label">
                                Employee ID
                            </label>

                            <input
                                type="text"
                                class="form-control"
                                id="edit_employee_id"
                                name="employee_id"
                            >

                            <div
                                id="edit_employee_idError"
                                class="field-error"
                            ></div>

                        </div>



                        <!-- FIRST NAME -->

                        <div class="col-md-6 mb-3">

                            <label class="form-label">
                                First Name
                            </label>

                            <input
                                type="text"
                                class="form-control"
                                id="edit_first_name"
                                name="first_name"
                            >

                            <div
                                id="edit_first_nameError"
                                class="field-error"
                            ></div>

                        </div>



                        <!-- LAST NAME -->

                        <div class="col-md-6 mb-3">

                            <label class="form-label">
                                Last Name
                            </label>

                            <input
                                type="text"
                                class="form-control"
                                id="edit_last_name"
                                name="last_name"
                            >

                            <div
                                id="edit_last_nameError"
                                class="field-error"
                            ></div>

                        </div>



                        <!-- EMAIL -->

                        <div class="col-md-6 mb-3">

                            <label class="form-label">
                                Email
                            </label>

                            <input
                                type="email"
                                class="form-control"
                                id="edit_email"
                                name="email"
                            >

                            <div
                                id="edit_emailError"
                                class="field-error"
                            ></div>

                        </div>



                        <!-- PHONE -->

                        <div class="col-md-6 mb-3">

                            <label class="form-label">
                                Phone
                            </label>

                            <input
                                type="text"
                                class="form-control"
                                id="edit_phone"
                                name="phone"
                                maxlength="15"
                            >

                            <div
                                id="edit_phoneError"
                                class="field-error"
                            ></div>

                        </div>



                        <!-- DEPARTMENT -->

                        <div class="col-md-6 mb-3">

                            <label class="form-label">
                                Department
                            </label>

                            <select
                                class="form-select"
                                id="edit_department"
                                name="department"
                            >

                                <option value="">
                                    Select Department
                                </option>

                                <option value="IT">
                                    IT
                                </option>

                                <option value="HR">
                                    HR
                                </option>

                                <option value="Finance">
                                    Finance
                                </option>

                                <option value="Marketing">
                                    Marketing
                                </option>

                                <option value="Sales">
                                    Sales
                                </option>

                            </select>

                            <div
                                id="edit_departmentError"
                                class="field-error"
                            ></div>

                        </div>



                        <!-- DESIGNATION -->

                        <div class="col-md-6 mb-3">

                            <label class="form-label">
                                Designation
                            </label>

                            <select
                                class="form-select"
                                id="edit_designation"
                                name="designation"
                            >

                                <option value="">
                                    Select Department First
                                </option>

                            </select>

                            <div
                                id="edit_designationError"
                                class="field-error"
                            ></div>

                        </div>



                        <!-- JOINING DATE -->

                        <div class="col-md-6 mb-3">

                            <label class="form-label">
                                Joining Date
                            </label>

                            <input
                                type="date"
                                class="form-control"
                                id="edit_joining_date"
                                name="joining_date"
                            >

                            <div
                                id="edit_joining_dateError"
                                class="field-error"
                            ></div>

                        </div>



                        <!-- STATUS -->

                        <div class="col-md-6 mb-3">

                            <label class="form-label">
                                Status
                            </label>

                            <select
                                class="form-select"
                                id="edit_status"
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
                                id="edit_statusError"
                                class="field-error"
                            ></div>

                        </div>

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
                        id="updateEmployeeBtn"
                    >

                        <i class="bi bi-check2-circle me-1"></i>

                        Update Employee

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>



<!-- =========================================================
     DELETE EMPLOYEE MODAL
========================================================= -->

<div
    class="modal fade"
    id="deleteEmployeeModal"
    tabindex="-1"
    aria-labelledby="deleteEmployeeModalLabel"
    aria-hidden="true"
>

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content">


            <!-- HEADER -->

            <div class="modal-header">

                <h5
                    class="modal-title"
                    id="deleteEmployeeModalLabel"
                >
                    Delete Employee
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

                    Delete this employee?

                </div>


                <p class="delete-description">

                    You are about to permanently delete

                    <span
                        id="deleteEmployeeName"
                        class="delete-employee-name"
                    >
                        this employee
                    </span>.

                    This action cannot be undone.

                </p>


                <div class="delete-warning text-start">

                    <i class="bi bi-exclamation-triangle-fill"></i>

                    All information associated with this employee
                    will be permanently removed.

                </div>


                <input
                    type="hidden"
                    id="delete_id"
                    name="id"
                >

            </div>



            <!-- FOOTER -->

            <div class="modal-footer justify-content-end">


                <button
                    type="button"
                    class="btn btn-light border"
                    data-bs-dismiss="modal"
                    id="cancelDeleteBtn"
                >
                    Cancel
                </button>


                <button
                    type="button"
                    class="btn btn-danger"
                    id="confirmDeleteBtn"
                >

                    <i class="bi bi-trash3 me-1"></i>

                    Delete Employee

                </button>

            </div>

        </div>

    </div>

</div>


<script
        src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<?php require_once __DIR__.'/../layouts/footer.php'; ?>



<!-- =========================================================
     EMPLOYEE JAVASCRIPT
========================================================= -->

<script
    src="../../public/js/employees.js"
></script>


</body>

</html>
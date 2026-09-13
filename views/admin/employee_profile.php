<?php

$pageTitle = 'Employee Profile';

require_once __DIR__ . '/../layouts/header.php';

require_once __DIR__ . '/../layouts/sidenav.php';

?>

<link
    rel="stylesheet"
    href="../../public/css/employees_details.css">

<main class="main-content">

    <?php require_once __DIR__ . '/../layouts/topbar.php'; ?>


    <div class="container py-5">


        <!-- =========================================================
             PAGE HEADER
        ========================================================== -->

        <div class="d-flex justify-content-between align-items-center mb-4">

            <div>

                <div class="page-breadcrumb">

                    <?php if (Auth::isAdmin()) { ?>

                        <i class="bi bi-people"></i>

                        <a
                            href="../../admin/employees.php"
                            class="text-decoration-none text-secondary">
                            Employees
                        </a>

                        <i class="bi bi-chevron-right"></i>

                        Profile

                    <?php } ?>

                    <?php if (Auth::isEmployee()) { ?>

                        <i class="bi bi-people"></i>

                        <a
                            href="../../employee/dashboard.php"
                            class="text-decoration-none text-secondary">
                            Dashboard
                        </a>

                        <i class="bi bi-chevron-right"></i>

                        Profile

                    <?php } ?>

                </div>

            </div>


            <?php if (Auth::isAdmin()) { ?>

                <a
                    href="employees.php"
                    class="btn btn-outline-secondary back-btn">

                    <i class="bi bi-arrow-left"></i>

                    Back to Employees

                </a>

            <?php } ?>

        </div>



        <!-- =========================================================
             GLOBAL ERROR
        ========================================================== -->

        <div
            id="employeeError"
            class="alert alert-danger d-none"
            role="alert">

            <div class="d-flex align-items-center">

                <i class="bi bi-exclamation-triangle-fill me-2"></i>

                <span id="employeeErrorMessage">
                    Unable to load employee.
                </span>

            </div>

        </div>



        <!-- =========================================================
             LOADING
        ========================================================== -->

        <div
            id="employeeLoading"
            class="loading-container">

            <div class="loading-card">

                <div
                    class="spinner-border text-primary"
                    role="status">

                    <span class="visually-hidden">
                        Loading...
                    </span>

                </div>


                <h6 class="mt-3 mb-1">

                    Loading Employee Profile

                </h6>


                <p class="text-muted mb-0">

                    Please wait while we fetch employee information.

                </p>

            </div>

        </div>



        <!-- =========================================================
             PROFILE
        ========================================================== -->

        <div
            id="employeeProfile"
            class="d-none">


            <!-- =====================================================
                 PROFILE HEADER
            ====================================================== -->

            <div class="card profile-card">


                <div class="profile-cover">


                    <div class="profile-content">


                        <!-- Avatar -->
<div id="profileAvatar" class="profile-avatar">

    <span id="avatarPlaceholder">--</span>

    <button
        type="button"
        id="uploadAvatarBtn"
        class="avatar-upload-btn"
        title="Upload profile photo"
    >
        <i class="bi bi-plus-lg"></i>
    </button>

    <input
        type="file"
        id="avatarInput"
        accept="image/jpeg,image/png,image/webp"
        style="display: none;"
    >

</div>


                        <!-- Employee Information -->

                        <div class="profile-main-info">

                            <div
                                id="employeeName"
                                class="employee-name">

                                --

                            </div>


                            <div
                                id="employeeDesignation"
                                class="employee-designation">

                                --

                            </div>


                            <div class="profile-meta">

                                <span>

                                    <i class="bi bi-person-badge"></i>

                                    <span id="employeeId">
                                        --
                                    </span>

                                </span>


                                <span>

                                    <i class="bi bi-building"></i>

                                    <span id="employeeDepartment">
                                        --
                                    </span>

                                </span>

                            </div>


                            <span
                                id="employeeStatus"
                                class="status-badge">

                                --

                            </span>

                        </div>


                        <!-- Action -->

                        <?php if (Auth::isEmployee()) { ?>

                            <div class="profile-action">

                                <button
                                    type="button"
                                    id="editEmployeeBtn"
                                    class="btn btn-light edit-btn">

                                    <i class="bi bi-pencil"></i>

                                    Edit Employee

                                </button>

                            </div>

                        <?php } ?>


                    </div>

                </div>



                <!-- =================================================
                     INFORMATION
                ================================================== -->

                <div class="card-body profile-body">


                    <div class="section-header">

                        <div>

                            <h5>

                                Employee Information

                            </h5>

                            <p>

                                Basic employment and contact details.

                            </p>

                        </div>

                    </div>



                    <div class="row g-4">


                        <!-- Email -->

                        <div class="col-lg-4 col-md-6">

                            <div class="info-item">

                                <div class="info-icon">

                                    <i class="bi bi-envelope"></i>

                                </div>

                                <div>

                                    <div class="info-label">

                                        Email Address

                                    </div>

                                    <div
                                        id="employeeEmail"
                                        class="info-value">

                                        --

                                    </div>

                                </div>

                            </div>

                        </div>



                        <!-- Phone -->

                        <div class="col-lg-4 col-md-6">

                            <div class="info-item">

                                <div class="info-icon">

                                    <i class="bi bi-telephone"></i>

                                </div>

                                <div>

                                    <div class="info-label">

                                        Phone Number

                                    </div>

                                    <div
                                        id="employeePhone"
                                        class="info-value">

                                        --

                                    </div>

                                </div>

                            </div>

                        </div>



                        <!-- Department -->

                        <div class="col-lg-4 col-md-6">

                            <div class="info-item">

                                <div class="info-icon">

                                    <i class="bi bi-building"></i>

                                </div>

                                <div>

                                    <div class="info-label">

                                        Department

                                    </div>

                                    <div
                                        id="employeeDepartmentInfo"
                                        class="info-value">

                                        --

                                    </div>

                                </div>

                            </div>

                        </div>



                        <!-- Designation -->

                        <div class="col-lg-4 col-md-6">

                            <div class="info-item">

                                <div class="info-icon">

                                    <i class="bi bi-briefcase"></i>

                                </div>

                                <div>

                                    <div class="info-label">

                                        Designation

                                    </div>

                                    <div
                                        id="employeeDesignationInfo"
                                        class="info-value">

                                        --

                                    </div>

                                </div>

                            </div>

                        </div>



                        <!-- Joining Date -->

                        <div class="col-lg-4 col-md-6">

                            <div class="info-item">

                                <div class="info-icon">

                                    <i class="bi bi-calendar-event"></i>

                                </div>

                                <div>

                                    <div class="info-label">

                                        Joining Date

                                    </div>

                                    <div
                                        id="employeeJoiningDate"
                                        class="info-value">

                                        --

                                    </div>

                                </div>

                            </div>

                        </div>



                        <!-- Experience -->

                        <div class="col-lg-4 col-md-6">

                            <div class="info-item">

                                <div class="info-icon">

                                    <i class="bi bi-clock-history"></i>

                                </div>

                                <div>

                                    <div class="info-label">

                                        Experience

                                    </div>

                                    <div
                                        id="employeeExperience"
                                        class="info-value">

                                        --

                                    </div>

                                </div>

                            </div>

                        </div>


                    </div>

                </div>

            </div>



            <!-- =====================================================
                 SUMMARY CARDS
            ====================================================== -->

            <div class="row g-4 mb-4">


                <!-- Present -->

                <div class="col-md-3">

                    <div class="card info-card">

                        <div class="card-body">

                            <div class="text-muted">
                                Present
                            </div>

                            <h3
                                class="mt-2"
                                id="presentDays">
                                0
                            </h3>

                        </div>

                    </div>

                </div>



                <!-- Absent -->

                <div class="col-md-3">

                    <div class="card info-card">

                        <div class="card-body">

                            <div class="text-muted">
                                Absent
                            </div>

                            <h3
                                class="mt-2"
                                id="absentDays">
                                0
                            </h3>

                        </div>

                    </div>

                </div>



                <!-- Half Day -->

                <div class="col-md-3">

                    <div class="card info-card">

                        <div class="card-body">

                            <div class="text-muted">
                                Half Days
                            </div>

                            <h3
                                class="mt-2"
                                id="halfDays">
                                0
                            </h3>

                        </div>

                    </div>

                </div>



                <!-- Leave -->

                <div class="col-md-3">

                    <div class="card info-card">

                        <div class="card-body">

                            <div class="text-muted">
                                Approved Leave
                            </div>

                            <h3
                                class="mt-2"
                                id="approvedLeaves">
                                0
                            </h3>

                        </div>

                    </div>

                </div>

            </div>



            <!-- =====================================================
                 TABS
            ====================================================== -->

            <div class="card profile-card">

                <div class="card-body">


                    <ul
                        class="nav nav-tabs"
                        id="employeeTabs">

                        <li class="nav-item">

                            <button
                                class="nav-link active"
                                data-bs-toggle="tab"
                                data-bs-target="#attendanceTab">

                                <i class="bi bi-calendar-check"></i>

                                Attendance

                            </button>

                        </li>


                        <li class="nav-item">

                            <button
                                class="nav-link"
                                data-bs-toggle="tab"
                                data-bs-target="#leaveTab">

                                <i class="bi bi-calendar-x"></i>

                                Leave History

                            </button>

                        </li>

                    </ul>



                    <div
                        class="tab-content pt-4">


                        <!-- =================================================
                             ATTENDANCE
                        ================================================== -->

                        <div
                            class="tab-pane fade show active"
                            id="attendanceTab">

                            <div class="table-responsive">

                                <table
                                    class="table table-hover align-middle">

                                    <thead>

                                        <tr>

                                            <th>
                                                Date
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


                                    <tbody
                                        id="attendanceTable">

                                    </tbody>

                                </table>

                            </div>

                        </div>



                        <!-- =================================================
                             LEAVE
                        ================================================== -->

                        <div
                            class="tab-pane fade"
                            id="leaveTab">

                            <div class="table-responsive">

                                <table
                                    class="table table-hover align-middle">

                                    <thead>

                                        <tr>

                                            <th>
                                                Type
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

                                        </tr>

                                    </thead>


                                    <tbody
                                        id="leaveTable">

                                    </tbody>

                                </table>

                            </div>

                        </div>


                    </div>

                </div>

            </div>


        </div>

    </div>


</main>



<!-- =========================================================
     EDIT EMPLOYEE MODAL
========================================================= -->

<div
    class="modal fade"
    id="editEmployeeModal"
    tabindex="-1"
    aria-hidden="true">

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
                    aria-label="Close"></button>

            </div>



            <!-- FORM -->

            <form id="editEmployeeForm">

                <div class="modal-body">


                    <div
                        id="editFormMessage"
                        class="alert d-none"></div>


                    <!-- DATABASE ID -->

                    <input
                        type="hidden"
                        id="edit_id"
                        name="id">


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
                                name="employee_id">

                            <div
                                id="edit_employee_idError"
                                class="field-error"></div>

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
                                name="first_name">

                            <div
                                id="edit_first_nameError"
                                class="field-error"></div>

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
                                name="last_name">

                            <div
                                id="edit_last_nameError"
                                class="field-error"></div>

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
                                name="email">

                            <div
                                id="edit_emailError"
                                class="field-error"></div>

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
                                maxlength="15">

                            <div
                                id="edit_phoneError"
                                class="field-error"></div>

                        </div>



                        <!-- DEPARTMENT -->

                        <div class="col-md-6 mb-3">

                            <label class="form-label">
                                Department
                            </label>

                            <select
                                class="form-select"
                                id="edit_department"
                                name="department">

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
                                class="field-error"></div>

                        </div>



                        <!-- DESIGNATION -->

                        <div class="col-md-6 mb-3">

                            <label class="form-label">
                                Designation
                            </label>

                            <input
                                type="text"
                                class="form-control"
                                id="edit_designation"
                                name="designation">

                            <div
                                id="edit_designationError"
                                class="field-error"></div>

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
                                name="joining_date">

                            <div
                                id="edit_joining_dateError"
                                class="field-error"></div>

                        </div>



                        <!-- STATUS -->

                        <div class="col-md-6 mb-3">

                            <label class="form-label">
                                Status
                            </label>

                            <select
                                class="form-select"
                                id="edit_status"
                                name="status">

                                <option value="active">
                                    Active
                                </option>

                                <option value="inactive">
                                    Inactive
                                </option>

                            </select>

                            <div
                                id="edit_statusError"
                                class="field-error"></div>

                        </div>


                    </div>

                </div>



                <!-- FOOTER -->

                <div class="modal-footer">

                    <button
                        type="button"
                        class="btn btn-light border"
                        data-bs-dismiss="modal">

                        Cancel

                    </button>


                    <button
                        type="submit"
                        class="btn btn-warning"
                        id="updateEmployeeBtn">

                        <i class="bi bi-check2-circle me-1"></i>

                        Update Employee

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>


<!-- =============================================================
     Employee ID
============================================================= -->

<!-- jQuery -->

<script
    src="https://code.jquery.com/jquery-3.7.1.min.js">
</script>

<script>
    window.employeeId =
        <?= json_encode($employeeId) ?>;
    window.isEmployee =
        <?= json_encode(Auth::isEmployee()) ?>;
</script>

<script src="../../public/js/employee_profile.js"></script>
<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
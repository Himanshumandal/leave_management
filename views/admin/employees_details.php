<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>Employee Details</title>


    <!-- =========================================================
         Bootstrap 5
    ========================================================== -->

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">


    <!-- =========================================================
         Bootstrap Icons
    ========================================================== -->

    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">


    <!-- =========================================================
         Your Common Admin CSS
         
         If your employees.php already has a custom CSS file,
         include the SAME file here.
    ========================================================== -->



</head>


<body class="bg-light">


    <div class="container py-4 py-md-5">


        <!-- =========================================================
         PAGE HEADER
    ========================================================== -->

        <div
            class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">

            <div>

                <div class="d-flex align-items-center gap-2 mb-1">

                    <i
                        class="bi bi-person-vcard fs-3"></i>

                    <h2 class="mb-0 fw-semibold">
                        Employee Details
                    </h2>

                </div>

                <p class="text-muted mb-0">
                    View complete employee information
                </p>

            </div>


            <div>

                <a
                    href="employees.php"
                    class="btn btn-secondary">

                    <i class="bi bi-arrow-left me-1"></i>

                    Back to Employees

                </a>

            </div>

        </div>



        <!-- =========================================================
         LOADING
    ========================================================== -->

        <div
            id="employeeLoading"
            class="card shadow-sm border-0">

            <div
                class="card-body text-center py-5">

                <div
                    class="spinner-border"
                    role="status">

                    <span class="visually-hidden">
                        Loading...
                    </span>

                </div>


                <h6 class="mt-3 mb-1">
                    Loading Employee
                </h6>


                <p class="text-muted mb-0">
                    Please wait while employee information is loaded.
                </p>

            </div>

        </div>



        <!-- =========================================================
         ERROR
    ========================================================== -->

        <div
            id="employeeError"
            class="alert alert-danger d-none shadow-sm"
            role="alert">

            <div class="d-flex align-items-start gap-2">

                <i class="bi bi-exclamation-triangle-fill"></i>

                <div>

                    <strong>
                        Unable to load employee
                    </strong>

                    <div
                        id="employeeErrorMessage"
                        class="mt-1"></div>

                </div>

            </div>

        </div>



        <!-- =========================================================
         EMPLOYEE PROFILE
    ========================================================== -->

        <div
            id="employeeProfile"
            class="d-none">




            <!-- =====================================================
             BASIC INFORMATION
        ====================================================== -->

            <div class="card shadow-sm border-0 mb-4">


                <div class="card-header bg-white py-3">

                    <div
                        class="d-flex align-items-center gap-2">

                        <i class="bi bi-person-lines-fill"></i>

                        <h5 class="mb-0 fw-semibold">
                            Basic Information
                        </h5>

                    </div>

                </div>


                <div class="card-body">


                    <div class="row g-4">


                        <!-- Employee ID -->

                        <div class="col-md-6 col-lg-4">

                            <small class="text-muted d-block mb-1">
                                Employee ID
                            </small>

                            <div
                                id="employeeId"
                                class="fw-semibold">
                                -
                            </div>

                        </div>



                        <!-- Full Name -->

                        <div class="col-md-6 col-lg-4">

                            <small class="text-muted d-block mb-1">
                                Full Name
                            </small>

                            <div
                                id="employeeName"
                                class="fw-semibold">
                                -
                            </div>

                        </div>



                        <!-- Email -->

                        <div class="col-md-6 col-lg-4">

                            <small class="text-muted d-block mb-1">
                                Email
                            </small>

                            <div
                                id="employeeEmail"
                                class="fw-semibold text-break">
                                -
                            </div>

                        </div>



                        <!-- Phone -->

                        <div class="col-md-6 col-lg-4">

                            <small class="text-muted d-block mb-1">
                                Phone
                            </small>

                            <div
                                id="employeePhone"
                                class="fw-semibold">
                                -
                            </div>

                        </div>



                        <!-- Department -->

                        <div class="col-md-6 col-lg-4">

                            <small class="text-muted d-block mb-1">
                                Department
                            </small>

                            <div
                                id="employeeDepartment"
                                class="fw-semibold">
                                -
                            </div>

                        </div>



                        <!-- Designation -->

                        <div class="col-md-6 col-lg-4">

                            <small class="text-muted d-block mb-1">
                                Designation
                            </small>

                            <div
                                id="employeeDesignation"
                                class="fw-semibold">
                                -
                            </div>

                        </div>



                        <!-- Joining Date -->

                        <div class="col-md-6 col-lg-4">

                            <small class="text-muted d-block mb-1">
                                Joining Date
                            </small>

                            <div
                                id="employeeJoiningDate"
                                class="fw-semibold">
                                -
                            </div>

                        </div>



                        <!-- Status -->

                        <div class="col-md-6 col-lg-4">

                            <small class="text-muted d-block mb-1">
                                Status
                            </small>

                            <div
                                id="employeeStatus">
                                -
                            </div>

                        </div>


                    </div>

                </div>

            </div>



            <!-- =====================================================
             ACTIONS
        ====================================================== -->

            <div class="card shadow-sm border-0">


                <div class="card-header bg-white py-3">

                    <div
                        class="d-flex align-items-center gap-2">

                        <i class="bi bi-gear"></i>

                        <h5 class="mb-0 fw-semibold">
                            Actions
                        </h5>

                    </div>

                </div>


                <div class="card-body">

                    <div
                        class="d-flex flex-column flex-sm-row gap-2">


                        <!-- EDIT -->

                        <a
                            href="#"
                            id="editEmployeeLink"
                            class="btn btn-warning">

                            <i
                                class="bi bi-pencil me-1"></i>

                            Edit Employee

                        </a>



                        <!-- BACK -->

                        <a
                            href="employees.php"
                            class="btn btn-secondary">

                            <i
                                class="bi bi-arrow-left me-1"></i>

                            Back to Employees

                        </a>

                    </div>

                </div>

            </div>


        </div>

    </div>



    <!-- =============================================================
     jQuery
============================================================= -->

    <script
        src="https://code.jquery.com/jquery-3.7.1.min.js"></script>



    <!-- =============================================================
     Bootstrap JS
============================================================= -->

    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>



    <!-- =============================================================
     Employee ID
============================================================= -->

    <script>
        window.employeeId =
            <?= json_encode($employeeId) ?>;
    </script>



    <!-- =============================================================
     Employee Details JS
============================================================= -->

    <script
        src="../../public/js/employees_details.js"></script>


</body>

</html>
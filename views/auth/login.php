<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Employee Management System - Login</title>


    <!-- Bootstrap -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >


    <!-- Bootstrap Icons -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
        rel="stylesheet"
    >

    <link rel="stylesheet" href="../../public/css/login.css">

</head>


<body>


<div class="login-wrapper">


    <div class="login-card">


        <div class="row g-0 h-100">


            <!-- =====================================
                 LEFT INFORMATION
            ====================================== -->

            <div class="col-lg-6">


                <div class="login-info">


                    <!-- BRAND -->

                    <div class="brand">

                        <div class="brand-icon">

                            <i class="bi bi-people-fill"></i>

                        </div>

                        <h4>
                            EmployeeHub
                        </h4>

                    </div>


                    <!-- CONTENT -->

                    <div class="info-content">

                        <h1>
                            Manage your workforce
                            with confidence.
                        </h1>


                        <p>

                            A centralized employee management
                            platform designed to simplify
                            attendance, leaves, employee
                            information and workplace management.

                        </p>


                        <!-- FEATURES -->

                        <div class="mt-4">


                            <div class="feature">

                                <div class="feature-icon">

                                    <i class="bi bi-calendar-check"></i>

                                </div>

                                <span>
                                    Track employee attendance
                                </span>

                            </div>


                            <div class="feature">

                                <div class="feature-icon">

                                    <i class="bi bi-calendar-event"></i>

                                </div>

                                <span>
                                    Manage leave requests
                                </span>

                            </div>


                            <div class="feature">

                                <div class="feature-icon">

                                    <i class="bi bi-bar-chart"></i>

                                </div>

                                <span>
                                    Monitor workforce performance
                                </span>

                            </div>


                            <div class="feature">

                                <div class="feature-icon">

                                    <i class="bi bi-shield-check"></i>

                                </div>

                                <span>
                                    Secure employee information
                                </span>

                            </div>


                        </div>

                    </div>


                    <div class="copyright">

                        © <?= date('Y') ?>
                        EmployeeHub. All rights reserved.

                    </div>


                </div>

            </div>


            <!-- =====================================
                 LOGIN FORM
            ====================================== -->

            <div class="col-lg-6">


                <div class="login-form-wrapper">


                    <div class="login-form">


                        <!-- HEADER -->

                        <div class="login-heading">

                            <h2>
                                Welcome back 👋
                            </h2>

                            <p>
                                Sign in to access your employee dashboard.
                            </p>

                        </div>


                        <!-- MESSAGE -->

                        <div
                            id="loginMessage"
                            class="alert d-none"
                            role="alert">
                        </div>


                        <!-- FORM -->

                        <form
                            id="loginForm"
                            novalidate
                        >


                            <!-- EMAIL -->

                            <div class="mb-3">

                                <label
                                    for="email"
                                    class="form-label"
                                >

                                    Email Address

                                </label>


                                <div class="input-group">

                                    <span class="input-group-text">

                                        <i class="bi bi-envelope"></i>

                                    </span>


                                    <input
                                        type="email"
                                        class="form-control"
                                        id="email"
                                        name="email"
                                        placeholder="Enter your email"
                                        autocomplete="email"
                                    >

                                </div>


                                <div
                                    id="emailError"
                                    class="error">
                                </div>

                            </div>


                            <!-- PASSWORD -->

                            <div class="mb-3">

                                <div class="d-flex justify-content-between">

                                    <label
                                        for="password"
                                        class="form-label"
                                    >

                                        Password

                                    </label>


                                    <!-- <a
                                        href="#"
                                        class="small text-decoration-none"
                                    >

                                        Forgot password?

                                    </a> -->

                                </div>


                                <div class="input-group">

                                    <span class="input-group-text">

                                        <i class="bi bi-lock"></i>

                                    </span>


                                    <input
                                        type="password"
                                        class="form-control"
                                        id="password"
                                        name="password"
                                        placeholder="Enter your password"
                                        autocomplete="current-password"
                                    >


                                    <button
                                        type="button"
                                        class="btn btn-outline-secondary"
                                        id="togglePassword"
                                    >

                                        <i
                                            class="bi bi-eye"
                                            id="passwordIcon">
                                        </i>

                                    </button>

                                </div>


                                <div
                                    id="passwordError"
                                    class="error">
                                </div>

                            </div>


                         

                            <!-- LOGIN -->

                            <div class="d-grid">

                                <button
                                    type="submit"
                                    class="btn btn-primary login-btn"
                                    id="loginButton"
                                >

                                    <span id="loginButtonText">

                                        Sign In

                                    </span>


                                    <span
                                        id="loginSpinner"
                                        class="spinner-border spinner-border-sm d-none"
                                        role="status">
                                    </span>

                                </button>

                            </div>


                        </form>


                        <!-- FOOTER -->

                        <div class="text-center mt-4">

                            <small class="text-muted">

                                Need help?
                                Contact your system administrator.

                            </small>

                        </div>


                    </div>

                </div>

            </div>


        </div>

    </div>

</div>


<!-- jQuery -->

<script
    src="https://code.jquery.com/jquery-3.7.1.min.js">
</script>


<!-- Bootstrap -->

<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">
</script>


<!-- SweetAlert -->

<script
    src="https://cdn.jsdelivr.net/npm/sweetalert2@11">
</script>


<!-- Login AJAX -->

<script src="../../public/js/login.js"></script>


</body>

</html>
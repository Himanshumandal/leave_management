<?php

require_once "./core/Autoload.php";


Autoload::register();

try {
    $db = Database::getInstance();

} catch (PDOException $e) {
   
    echo "Database connection failed: "
        . $e->getMessage();
}

?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        EmployeeHub | Leave & Attendance Management
    </title>

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

    <!-- Custom CSS -->
    <link
        rel="stylesheet"
        href="./public/css/homepage.css"
    >

</head>

<body>


<!-- =====================================================
     NAVBAR
===================================================== -->

<nav class="navbar navbar-expand-lg bg-white border-bottom sticky-top">

    <div class="container">

        <a
            class="navbar-brand fw-bold"
            href="index.php"
        >

            <i class="bi bi-people-fill text-primary"></i>

            EmployeeHub

        </a>


        <button
            class="navbar-toggler"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#mainNavbar"
        >

            <span class="navbar-toggler-icon"></span>

        </button>


        <div
            class="collapse navbar-collapse"
            id="mainNavbar"
        >

            <ul class="navbar-nav mx-auto">

                <li class="nav-item">
                    <a
                        class="nav-link"
                        href="#features"
                    >
                        Features
                    </a>
                </li>

                <li class="nav-item">
                    <a
                        class="nav-link"
                        href="#attendance"
                    >
                        Attendance
                    </a>
                </li>

                <li class="nav-item">
                    <a
                        class="nav-link"
                        href="#leave"
                    >
                        Leave Management
                    </a>
                </li>

                <li class="nav-item">
                    <a
                        class="nav-link"
                        href="#about"
                    >
                        About
                    </a>
                </li>

            </ul>


            <div class="d-flex gap-2">

                <a
                    href="/auth/login.php"
                    class="btn btn-outline-primary"
                >
                    Login
                </a>

                <a
                    href="#features"
                    class="btn btn-primary"
                >
                    Explore
                </a>

            </div>

        </div>

    </div>

</nav>



<!-- =====================================================
     HERO SECTION
===================================================== -->

<section class="hero-section">

    <div class="container">

        <div class="row align-items-center min-vh-75">


            <!-- LEFT -->

            <div class="col-lg-6">

                <span class="badge bg-primary-subtle text-primary mb-3 px-3 py-2">

                    Employee Management System

                </span>


                <h1 class="display-4 fw-bold mb-4">

                    Manage your

                    <span class="text-primary">
                        workforce
                    </span>

                    smarter.

                </h1>


                <p class="lead text-muted mb-4">

                    A simple and powerful platform to manage employee
                    attendance, leaves, working hours and employee
                    information from one place.

                </p>


                <div class="d-flex gap-3 flex-wrap">

                    <a
                        href="/auth/login.php"
                        class="btn btn-primary btn-lg px-4"
                    >

                        <i class="bi bi-box-arrow-in-right me-2"></i>

                        Employee Login

                    </a>


                    <a
                        href="#features"
                        class="btn btn-outline-secondary btn-lg px-4"
                    >

                        Explore Features

                    </a>

                </div>


                <div class="row mt-5">

                    <div class="col-4">

                        <h4 class="fw-bold mb-0">
                            24/7
                        </h4>

                        <small class="text-muted">
                            Access
                        </small>

                    </div>


                    <div class="col-4">

                        <h4 class="fw-bold mb-0">
                            100%
                        </h4>

                        <small class="text-muted">
                            Digital
                        </small>

                    </div>


                    <div class="col-4">

                        <h4 class="fw-bold mb-0">
                            1
                        </h4>

                        <small class="text-muted">
                            Platform
                        </small>

                    </div>

                </div>

            </div>


            <!-- RIGHT -->

            <div class="col-lg-6 mt-5 mt-lg-0">

                <div class="dashboard-preview shadow-lg">

                    <div class="preview-header">

                        <span></span>
                        <span></span>
                        <span></span>

                    </div>


                    <div class="p-4">

                        <div class="d-flex justify-content-between mb-4">

                            <div>

                                <small class="text-muted">
                                    Employee Dashboard
                                </small>

                                <h4 class="fw-bold">
                                    Welcome back 👋
                                </h4>

                            </div>

                            <i class="bi bi-person-circle fs-2 text-primary"></i>

                        </div>


                        <div class="row g-3">

                            <div class="col-6">

                                <div class="preview-card">

                                    <small class="text-muted">
                                        Attendance
                                    </small>

                                    <h3 class="text-primary">
                                        94%
                                    </h3>

                                </div>

                            </div>


                            <div class="col-6">

                                <div class="preview-card">

                                    <small class="text-muted">
                                        Present Days
                                    </small>

                                    <h3 class="text-success">
                                        22
                                    </h3>

                                </div>

                            </div>


                            <div class="col-6">

                                <div class="preview-card">

                                    <small class="text-muted">
                                        Leave Balance
                                    </small>

                                    <h3 class="text-warning">
                                        8
                                    </h3>

                                </div>

                            </div>


                            <div class="col-6">

                                <div class="preview-card">

                                    <small class="text-muted">
                                        Pending Requests
                                    </small>

                                    <h3 class="text-info">
                                        2
                                    </h3>

                                </div>

                            </div>

                        </div>


                        <div class="mt-4">

                            <small class="text-muted">
                                Attendance Trend
                            </small>

                            <div class="fake-chart mt-2">

                                <div style="height:40%"></div>
                                <div style="height:70%"></div>
                                <div style="height:55%"></div>
                                <div style="height:85%"></div>
                                <div style="height:65%"></div>
                                <div style="height:90%"></div>
                                <div style="height:75%"></div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</section>



<!-- =====================================================
     FEATURES
===================================================== -->

<section
    id="features"
    class="py-5"
>

    <div class="container">

        <div class="text-center mb-5">

            <span class="text-primary fw-semibold">
                FEATURES
            </span>

            <h2 class="fw-bold mt-2">
                Everything you need
            </h2>

            <p class="text-muted">
                Manage your employees and daily workforce operations
                efficiently.
            </p>

        </div>


        <div class="row g-4">


            <div class="col-md-6 col-lg-4">

                <div class="feature-card h-100">

                    <div class="feature-icon bg-primary-subtle text-primary">

                        <i class="bi bi-calendar-check"></i>

                    </div>

                    <h5>
                        Attendance Management
                    </h5>

                    <p class="text-muted">

                        Track employee check-in, check-out,
                        attendance status and working hours.

                    </p>

                </div>

            </div>


            <div class="col-md-6 col-lg-4">

                <div class="feature-card h-100">

                    <div class="feature-icon bg-warning-subtle text-warning">

                        <i class="bi bi-calendar2-plus"></i>

                    </div>

                    <h5>
                        Leave Management
                    </h5>

                    <p class="text-muted">

                        Employees can apply for leaves while
                        administrators can review and manage requests.

                    </p>

                </div>

            </div>


            <div class="col-md-6 col-lg-4">

                <div class="feature-card h-100">

                    <div class="feature-icon bg-success-subtle text-success">

                        <i class="bi bi-people"></i>

                    </div>

                    <h5>
                        Employee Management
                    </h5>

                    <p class="text-muted">

                        Maintain employee profiles, departments,
                        designations and employment status.

                    </p>

                </div>

            </div>


            <div class="col-md-6 col-lg-4">

                <div class="feature-card h-100">

                    <div class="feature-icon bg-info-subtle text-info">

                        <i class="bi bi-bar-chart"></i>

                    </div>

                    <h5>
                        Dashboard & Reports
                    </h5>

                    <p class="text-muted">

                        Get a clear overview of attendance,
                        leaves and employee statistics.

                    </p>

                </div>

            </div>


            <div class="col-md-6 col-lg-4">

                <div class="feature-card h-100">

                    <div class="feature-icon bg-danger-subtle text-danger">

                        <i class="bi bi-shield-check"></i>

                    </div>

                    <h5>
                        Secure Authentication
                    </h5>

                    <p class="text-muted">

                        Role-based authentication protects employee
                        and administrator functionality.

                    </p>

                </div>

            </div>


            <div class="col-md-6 col-lg-4">

                <div class="feature-card h-100">

                    <div class="feature-icon bg-secondary-subtle text-secondary">

                        <i class="bi bi-cloud"></i>

                    </div>

                    <h5>
                        Cloud Ready
                    </h5>

                    <p class="text-muted">

                        Designed to support cloud storage and
                        scalable deployment.

                    </p>

                </div>

            </div>

        </div>

    </div>

</section>



<!-- =====================================================
     ATTENDANCE SECTION
===================================================== -->

<section
    id="attendance"
    class="py-5 bg-light"
>

    <div class="container">

        <div class="row align-items-center">

            <div class="col-lg-6">

                <span class="text-primary fw-semibold">
                    ATTENDANCE
                </span>

                <h2 class="fw-bold mt-2">
                    Keep track of every working day.
                </h2>

                <p class="text-muted">

                    Employees can easily monitor their attendance,
                    check-in and check-out times, working hours and
                    attendance trends.

                </p>


                <ul class="list-unstyled mt-4">

                    <li class="mb-3">

                        <i class="bi bi-check-circle-fill text-success me-2"></i>

                        Daily attendance tracking

                    </li>

                    <li class="mb-3">

                        <i class="bi bi-check-circle-fill text-success me-2"></i>

                        Present, absent and half-day status

                    </li>

                    <li class="mb-3">

                        <i class="bi bi-check-circle-fill text-success me-2"></i>

                        Attendance trend visualization

                    </li>

                    <li>

                        <i class="bi bi-check-circle-fill text-success me-2"></i>

                        Working hours monitoring

                    </li>

                </ul>

            </div>


            <div class="col-lg-6 mt-4 mt-lg-0">

                <div class="info-panel shadow-sm">

                    <i class="bi bi-calendar-check display-4 text-primary"></i>

                    <h4 class="mt-3">
                        Attendance at a glance
                    </h4>

                    <p class="text-muted">
                        Quickly understand your attendance performance.
                    </p>

                    <div class="progress mt-4">

                        <div
                            class="progress-bar"
                            style="width: 94%;"
                        >
                            94%
                        </div>

                    </div>

                    <small class="text-muted">
                        Monthly attendance
                    </small>

                </div>

            </div>

        </div>

    </div>

</section>



<!-- =====================================================
     LEAVE SECTION
===================================================== -->

<section
    id="leave"
    class="py-5"
>

    <div class="container">

        <div class="row align-items-center">


            <div class="col-lg-6 order-lg-2">

                <span class="text-warning fw-semibold">
                    LEAVE MANAGEMENT
                </span>

                <h2 class="fw-bold mt-2">
                    Leave requests made simple.
                </h2>

                <p class="text-muted">

                    Apply for leave, track your request status and
                    view your remaining leave balance from one place.

                </p>

            </div>


            <div class="col-lg-6 order-lg-1 mt-4 mt-lg-0">

                <div class="leave-demo shadow-sm">

                    <div class="d-flex justify-content-between">

                        <div>

                            <small class="text-muted">
                                Leave Request
                            </small>

                            <h5 class="fw-bold">
                                Casual Leave
                            </h5>

                        </div>

                        <span class="badge bg-warning text-dark">
                            Pending
                        </span>

                    </div>


                    <hr>


                    <div class="row">

                        <div class="col-6">

                            <small class="text-muted">
                                Start Date
                            </small>

                            <p class="fw-semibold">
                                02 Sep
                            </p>

                        </div>

                        <div class="col-6">

                            <small class="text-muted">
                                End Date
                            </small>

                            <p class="fw-semibold">
                                03 Sep
                            </p>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</section>



<!-- =====================================================
     ABOUT
===================================================== -->

<section
    id="about"
    class="py-5 bg-light"
>

    <div class="container text-center">

        <span class="text-primary fw-semibold">
            ABOUT
        </span>

        <h2 class="fw-bold mt-2">
            One platform for everyday employee operations.
        </h2>

        <p class="text-muted mx-auto about-text">

            EmployeeHub brings attendance, leave management,
            employee information and workforce insights together
            in a single easy-to-use system.

        </p>

    </div>

</section>



<!-- =====================================================
     CTA
===================================================== -->

<section class="py-5">

    <div class="container">

        <div class="cta-section text-center">

            <i class="bi bi-people-fill display-5"></i>

            <h2 class="fw-bold mt-3">
                Ready to get started?
            </h2>

            <p class="mb-4">

                Login to access your employee dashboard.

            </p>

            <a
                href="/auth/login.php"
                class="btn btn-light btn-lg px-4"
            >

                <i class="bi bi-box-arrow-in-right me-2"></i>

                Login to Dashboard

            </a>

        </div>

    </div>

</section>



<!-- =====================================================
     FOOTER
===================================================== -->

<footer class="border-top py-4">

    <div class="container">

        <div class="row align-items-center">

            <div class="col-md-6">

                <strong>
                    <i class="bi bi-people-fill text-primary"></i>
                    EmployeeHub
                </strong>

                <small class="text-muted ms-2">
                    Employee Leave & Attendance Management System
                </small>

            </div>


            <div class="col-md-6 text-md-end mt-3 mt-md-0">

                <small class="text-muted">
                    © <?= date('Y') ?> EmployeeHub. All rights reserved.
                </small>

            </div>

        </div>

    </div>

</footer>



<!-- jQuery -->

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>


<!-- Bootstrap -->

<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
></script>


<!-- Homepage JS -->

<script src="./public/js/homepage.js"></script>


</body>

</html>
<?php
/**
 * Common Header
 *
 * Expected variables:
 * $pageTitle
 */

$pageTitle = $pageTitle ?? 'Employee Management System';
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
        <?= htmlspecialchars($pageTitle) ?>
    </title>

    <!-- Google Font -->
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap"
        rel="stylesheet"
    >

    <!-- Bootstrap 5 -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    >
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
    >
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


    <?php if(Auth::isAdmin()){ ?>

    <style>

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: 'Inter', sans-serif;
            background: #f5f7fb;
            color: #1f2937;
        }

        /* =========================
           Sidebar
        ========================= */

        .sidebar {
            width: 250px;
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            background: #111827;
            color: #ffffff;
            padding: 20px 15px;
            z-index: 1000;
        }

        .brand {
            padding: 10px 15px 25px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            margin-bottom: 20px;
        }

        .brand h4 {
            margin: 0;
            font-weight: 700;
        }

        .brand small {
            color: #9ca3af;
        }

        .sidebar-menu {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .sidebar-menu li {
            margin-bottom: 5px;
        }

        .sidebar-menu a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 15px;
            color: #9ca3af;
            text-decoration: none;
            border-radius: 8px;
            transition: 0.2s;
            font-size: 14px;
        }

        .sidebar-menu a:hover {
            background: #1f2937;
            color: #ffffff;
        }

        .sidebar-menu a.active {
            background: #2563eb;
            color: #ffffff;
        }

        .sidebar-menu i {
            font-size: 18px;
        }

        /* =========================
           Main Content
        ========================= */

        .main-content {
            margin-left: 250px;
            min-height: 100vh;
        }

        /* =========================
           Top Navbar
        ========================= */

        .topbar {
            height: 70px;
            background: #ffffff;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 30px;
        }

        .topbar h5 {
            margin: 0;
            font-weight: 600;
        }

        .admin-profile {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .profile-icon {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: #2563eb;
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
        }

        .profile-info {
            line-height: 1.2;
        }

        .profile-info strong {
            font-size: 13px;
        }

        .profile-info small {
            color: #6b7280;
            font-size: 11px;
        }

        /* =========================
           Responsive
        ========================= */

        @media (max-width: 992px) {

            .sidebar {
                width: 220px;
            }

            .main-content {
                margin-left: 220px;
            }

        }

        @media (max-width: 768px) {

            .sidebar {
                position: relative;
                width: 100%;
                height: auto;
            }

            .main-content {
                margin-left: 0;
            }

            .topbar {
                padding: 0 20px;
            }

        }

    </style>
    <?php } elseif(Auth::isEmployee()){ ?>

    <link rel="stylesheet" href="../../public/css/employee_dashboard.css">
    <link rel="stylesheet" href="../../public/css/assistant.css">

    <?php } ?>

</head>

<body>


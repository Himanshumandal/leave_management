<?php

$currentPage = basename($_SERVER['PHP_SELF']);

if(Auth::isAdmin()){

?>


<aside class="sidebar">

    <div class="brand">

        <h4>
            <i class="bi bi-buildings"></i>
            EMS
        </h4>

        <small>
            Employee Management
        </small>

    </div>


    <ul class="sidebar-menu">

        <!-- Dashboard -->
        <li>

            <a
                href="../../dashboard.php"
                class="<?= $currentPage === 'dashboard.php' ? 'active' : '' ?>"
            >

                <i class="bi bi-grid-1x2-fill"></i>

                Dashboard

            </a>

        </li>


        <!-- Employees -->
        <li>

            <a
                href="../../admin/employees.php"
                class="<?= $currentPage === 'employees.php' ? 'active' : '' ?>"
            >

                <i class="bi bi-people-fill"></i>

                Employees

            </a>

        </li>


        <!-- Leave Management -->
        <li>

            <a
                href="../../admin/leaves.php"
                class="<?= $currentPage === 'leaves.php' ? 'active' : '' ?>"
            >

                <i class="bi bi-calendar-check"></i>

                Leave Management

            </a>

        </li>


        <!-- Departments -->
        <li>

            <a
                href="../../admin/department.php"
                class="<?= $currentPage === 'department.php' ? 'active' : '' ?>"
            >

                <i class="bi bi-building"></i>

                Departments

            </a>

        </li>
        <!-- Designation -->
        <li>

            <a
                href="../../admin/designation.php"
                class="<?= $currentPage === 'designation.php' ? 'active' : '' ?>"
            >

                <i class="bi bi-building"></i>

                Designations

            </a>

        </li>


        <!-- Attendance -->
        <li>

            <a
                href="../../admin/attendances.php"
                class="<?= $currentPage === 'attendances.php' ? 'active' : '' ?>"
            >

                <i class="bi bi-person-badge"></i>

                Attendance

            </a>

        </li>


        <!-- Attendance Summary -->
        <li>

            <a
                href="../../admin/attendances_summary.php"
                class="<?= $currentPage === 'attendances_summary.php' ? 'active' : '' ?>"
            >

                <i class="bi bi-bar-chart-fill"></i>

                Attendance Summary

            </a>

        </li>


        <!-- Attendance History  -->
        <li>

            <a
                href="../../admin/attendances_history.php"
                class="<?= $currentPage === 'attendances_history.php' ? 'active' : '' ?>"
            >

                <i class="bi bi-gear-fill"></i>

                Attendance History

            </a>

        </li>


        <!-- Logout -->
        <li class="mt-4">

            <a href="../logout.php">

                <i class="bi bi-box-arrow-right"></i>

                Logout

            </a>

        </li>

    </ul>

</aside>

<?php } elseif(Auth::isEmployee()){  ?>

 <!-- =====================================================
         EMPLOYEE SIDEBAR
    ====================================================== -->

    <div
        id="sidebarOverlay"
        class="sidebar-overlay"
    ></div>


    <aside
        id="employeeSidebar"
        class="employee-sidebar"
    >


        <!-- BRAND -->

        <div class="sidebar-brand">

            <div class="brand-icon">

                <i class="bi bi-grid-1x2-fill"></i>

            </div>

            <span>
                Employee Portal
            </span>

        </div>



        <!-- PROFILE -->

        <div class="sidebar-profile">

            <div class="sidebar-avatar">

                <i class="bi bi-person"></i>

            </div>


            <div class="sidebar-profile-info">

                <strong>
                    <?php print(Auth::name()) ?>
                </strong>

                <span>
                   <?php echo Auth::employeeId() ?>
                </span>

            </div>

        </div>



        <!-- NAVIGATION -->

        <nav class="sidebar-navigation">


            <div class="nav-section-title">
                MAIN
            </div>


            <a
                href="../../employee/dashboard.php"
                class="sidebar-link <?= $currentPage === 'dashboard.php' ? 'active' : '' ?>"
            >

                <i class="bi bi-grid"></i>

                <span>
                    Dashboard
                </span>

            </a>


            <a
                href="../../admin/employee_profile.php"
                class="sidebar-link <?= $currentPage === 'employee_profile.php' ? 'active' : '' ?>"
            >

                <i class="bi bi-person"></i>

                <span>
                    My Profile
                </span>

            </a>

            <a
                href="../../employee/attendance.php"
               class="sidebar-link <?= $currentPage === 'attendance.php' ? 'active' : '' ?>"
            >

                <i class="bi bi-calendar-check"></i>

                <span>
                    Attendance
                </span>

            </a>


            <a
                href="../employee/leaves.php"
                class="sidebar-link <?= $currentPage === 'leaves.php' ? 'active' : '' ?>"
            >

                <i class="bi bi-calendar-minus"></i>

                <span>
                    Leave
                </span>

            </a>
            <a
                href="../../employee/chatui.php"
                class="sidebar-link <?= $currentPage === 'chat.php' ? 'active' : '' ?>"
            >

                <i class="bi bi-calendar-minus"></i>

                <span>
                    chat
                </span>

            </a>


            
        </nav>



        <!-- FOOTER -->

        <div class="sidebar-footer">

            <a
                href="../../logout.php"
                class="sidebar-link logout-link"
            >

                <i class="bi bi-box-arrow-left"></i>

                <span>
                    Logout
                </span>

            </a>

        </div>


    </aside>
<?php } ?>

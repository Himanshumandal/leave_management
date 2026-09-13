<?php

if (Auth::isAdmin()):

?>

    <!-- =====================================================
         ADMIN TOPBAR
    ====================================================== -->

    <header class="topbar">

        <div>

            <h5>
                <?= htmlspecialchars($pageTitle) ?>
            </h5>

        </div>


        <div class="admin-profile">

            <div class="profile-icon">

                <i class="bi bi-person"></i>

            </div>


            <div class="profile-info">

                <strong>
                    Admin
                </strong>

                <small>
                    Administrator
                </small>

            </div>

        </div>

    </header>


<?php

elseif (Auth::isEmployee()):


$imagePath = trim(
    Auth::image()
);

$imageUrl = '';

if ($imagePath) {

    /*
    |--------------------------------------------------------------------------
    | Normalize Image Path
    |--------------------------------------------------------------------------
    */

    $imagePath = str_replace(
        '\\',
        '/',
        $imagePath
    );

    $imagePath = ltrim(
        $imagePath,
        '/'
    );

    /*
    |--------------------------------------------------------------------------
    | Remove public/ if already present
    |--------------------------------------------------------------------------
    |
    | Example:
    |
    | public/images/employees/photo.jpg
    |
    | becomes:
    |
    | images/employees/photo.jpg
    |
    */

    if (str_starts_with($imagePath, 'public/')) {

        $imagePath = substr(
            $imagePath,
            strlen('public/')
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Build Browser URL
    |--------------------------------------------------------------------------
    */

    $imageUrl =
        '/leave_management/public/'
        . $imagePath;
}


?>

<style>
    /* Topbar Profile */
.topbar-profile {
    display: flex;
    align-items: center;
    gap: 10px;
}

/* Avatar container */
.topbar-avatar {
    width: 38px;
    height: 38px;

    min-width: 38px;
    min-height: 38px;

    border-radius: 50%;

    overflow: hidden;

    display: flex;
    align-items: center;
    justify-content: center;

    position: relative;

    background: #2563EB;
}

/* Default Bootstrap icon */
.topbar-avatar i {
    font-size: 16px;
    line-height: 1;
}

/* Uploaded image */
.topbar-avatar-image {
    width: 100%;
    height: 100%;

    display: block;

    object-fit: cover;

    border-radius: 50%;
}
</style>

    <!-- =====================================================
         EMPLOYEE TOPBAR
    ====================================================== -->

    <header class="employee-topbar">


        <div class="d-flex align-items-center gap-3">


            <!-- Mobile Menu -->

            <button
                type="button"
                id="sidebarToggle"
                class="mobile-menu-btn"
            >

                <i class="bi bi-list"></i>

            </button>


            <div>

                <h5 class="mb-0">

                    <?= htmlspecialchars($pageTitle) ?>

                </h5>

                <small class="text-muted">

                    Employee Portal

                </small>

            </div>

        </div>



        <div
            class="d-flex align-items-center gap-3"
        >
            <!-- Profile -->

            

                <div class="topbar-profile">
                    <a href="../../admin/employee_profile.php">
                        <div class="topbar-avatar">

                            <?php if (!empty(Auth::image())) { ?>

                                <img
                                    src="/public/<?= htmlspecialchars(
                                        ltrim(
                                            preg_replace(
                                                '#^public/#',
                                                '',
                                                Auth::image()
                                            ),
                                            '/'
                                        )
                                    ) ?>"
                                    alt="<?= htmlspecialchars(Auth::name()) ?>"
                                    class="topbar-avatar-image"
                                >

                            <?php } else { ?>

                                <i class="bi bi-person"></i>

                            <?php } ?>

                        </div>
                    </a>
                    <div class="d-none d-md-block">

                        <strong>
                            <?= htmlspecialchars(Auth::name()) ?>
                        </strong>

                        <small>
                            <?= htmlspecialchars(Auth::role()) ?>
                        </small>

                    </div>

                </div>
            

        </div>


    </header>

<?php

endif;

?>
$(document).ready(function () {


    // =========================================
    // SHOW / HIDE PASSWORD
    // =========================================

    $('#togglePassword').on('click', function () {

        const password =
            $('#password');

        const icon =
            $('#passwordIcon');


        if (password.attr('type') === 'password') {

            password.attr(
                'type',
                'text'
            );

            icon.removeClass(
                'bi-eye'
            );

            icon.addClass(
                'bi-eye-slash'
            );

        } else {

            password.attr(
                'type',
                'password'
            );

            icon.removeClass(
                'bi-eye-slash'
            );

            icon.addClass(
                'bi-eye'
            );

        }

    });


});

$(document).ready(function () {

    const $form = $('#loginForm');
    const $email = $('#email');
    const $password = $('#password');
    const $emailError = $('#emailError');
    const $passwordError = $('#passwordError');
    const $loginButton = $form.find('button[type="submit"]');

    /*
    |--------------------------------------------------------------------------
    | Clear Errors
    |--------------------------------------------------------------------------
    */

    function clearErrors() {

        $emailError.text('');
        $passwordError.text('');

        $email.removeClass('is-invalid');
        $password.removeClass('is-invalid');

        $('#loginMessage')
            .removeClass('alert-danger alert-success')
            .hide()
            .text('');
    }


    /*
    |--------------------------------------------------------------------------
    | Show Validation Error
    |--------------------------------------------------------------------------
    */

    function showFieldError($field, $errorElement, message) {

        $field.addClass('is-invalid');
        $errorElement.text(message);
    }


    /*
    |--------------------------------------------------------------------------
    | Email Validation
    |--------------------------------------------------------------------------
    */

    function isValidEmail(email) {

        const emailPattern =
            /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        return emailPattern.test(email);
    }


    /*
    |--------------------------------------------------------------------------
    | Client Side Validation
    |--------------------------------------------------------------------------
    */

    function validateForm() {

        let isValid = true;

        const email = $.trim($email.val());
        const password = $password.val();

        clearErrors();


        // Email validation

        if (email === '') {

            showFieldError(
                $email,
                $emailError,
                'Email address is required.'
            );

            isValid = false;

        } else if (!isValidEmail(email)) {

            showFieldError(
                $email,
                $emailError,
                'Please enter a valid email address.'
            );

            isValid = false;
        }


        // Password validation

        if (password === '') {

            showFieldError(
                $password,
                $passwordError,
                'Password is required.'
            );

            isValid = false;

        } else if (password.length < 6) {

            showFieldError(
                $password,
                $passwordError,
                'Password must contain at least 6 characters.'
            );

            isValid = false;
        }


        return isValid;
    }


    /*
    |--------------------------------------------------------------------------
    | Loading State
    |--------------------------------------------------------------------------
    */

    function setLoading(loading) {

        if (loading) {

            $loginButton
                .prop('disabled', true)
                .html(`
                    <span
                        class="spinner-border spinner-border-sm me-2"
                        role="status"
                        aria-hidden="true">
                    </span>
                    Signing in...
                `);

        } else {

            $loginButton
                .prop('disabled', false)
                .html('Login');
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Form Submit
    |--------------------------------------------------------------------------
    */

    $form.on('submit', function (event) {

        event.preventDefault();


        /*
        | Clear previous errors
        */

        clearErrors();


        /*
        | Validate form
        */

        if (!validateForm()) {

            Swal.fire({
                icon: 'warning',
                title: 'Check your details',
                text: 'Please correct the highlighted fields.',
                confirmButtonText: 'OK'
            });

            return;
        }


        /*
        | Get values
        */

        const email = $.trim($email.val());
        const password = $password.val();


        /*
        | Enable loader
        */

        setLoading(true);


        /*
        | AJAX Request
        */

        $.ajax({

            url: '../../ajax/login.php',

            type: 'POST',

            data: {
                email: email,
                password: password
            },

            dataType: 'json',


            /*
            |--------------------------------------------------------------------------
            | Success
            |--------------------------------------------------------------------------
            */

            success: function (response) {

                if (response.success) {

                    Swal.fire({
                        icon: 'success',
                        title: 'Login Successful',
                        text: response.message || 'Welcome back!',
                        showConfirmButton: false,
                        timer: 1500,
                        timerProgressBar: true
                    }).then(function () {

                        window.location.href =
                            response.redirect;

                    });

                } else {

                    setLoading(false);

                    Swal.fire({
                        icon: 'error',
                        title: 'Login Failed',
                        text: response.message ||
                            'Invalid email or password.',
                        confirmButtonText: 'Try Again'
                    });
                }
            },


            /*
            |--------------------------------------------------------------------------
            | AJAX Error
            |--------------------------------------------------------------------------
            */

            error: function (xhr) {

                setLoading(false);

                const response = xhr.responseJSON;


                /*
                | Validation errors from server
                */

                if (response && response.errors) {

                    if (response.errors.email) {

                        showFieldError(
                            $email,
                            $emailError,
                            response.errors.email
                        );
                    }


                    if (response.errors.password) {

                        showFieldError(
                            $password,
                            $passwordError,
                            response.errors.password
                        );
                    }


                    Swal.fire({
                        icon: 'warning',
                        title: 'Validation Error',
                        text: 'Please check your details.',
                        confirmButtonText: 'OK'
                    });

                    return;
                }


                /*
                | Authentication/general error
                */

                if (response && response.message) {

                    Swal.fire({
                        icon: 'error',
                        title: 'Login Failed',
                        text: response.message,
                        confirmButtonText: 'Try Again'
                    });

                    return;
                }


                /*
                | Server error
                */

                if (xhr.status === 500) {

                    Swal.fire({
                        icon: 'error',
                        title: 'Server Error',
                        text: 'Something went wrong on the server. Please try again later.',
                        confirmButtonText: 'OK'
                    });

                    return;
                }


                /*
                | Network error
                */

                Swal.fire({
                    icon: 'error',
                    title: 'Connection Error',
                    text: 'Unable to connect to the server. Please check your internet connection.',
                    confirmButtonText: 'OK'
                });
            },


            /*
            |--------------------------------------------------------------------------
            | Complete
            |--------------------------------------------------------------------------
            */

            complete: function () {

                /*
                | Don't immediately remove loader after
                | successful login because redirect is coming.
                */

                if (!$loginButton.prop('disabled')) {
                    setLoading(false);
                }
            }

        });

    });


    /*
    |--------------------------------------------------------------------------
    | Remove Error When User Starts Typing
    |--------------------------------------------------------------------------
    */

    $email.on('input', function () {

        $email.removeClass('is-invalid');
        $emailError.text('');

    });


    $password.on('input', function () {

        $password.removeClass('is-invalid');
        $passwordError.text('');

    });

});
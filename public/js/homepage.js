$(document).ready(function () {

    // Smooth scrolling

    $('a[href^="#"]').on('click', function (e) {

        const target =
            $(this.getAttribute('href'));

        if (!target.length) {
            return;
        }

        e.preventDefault();

        $('html, body').animate(
            {
                scrollTop:
                    target.offset().top - 70
            },
            500
        );

    });


    // Simple AJAX health check

    function checkSystemStatus() {

        $.ajax({

            url: './ajax/homepage.php',

            type: 'GET',

            data: {
                action: 'status'
            },

            dataType: 'json',

            success: function (response) {

                console.log(
                    'System Status:',
                    response
                );

            },

            error: function (xhr) {

                console.error(
                    'System status error:',
                    xhr.responseText
                );

            }

        });

    }


    checkSystemStatus();

});
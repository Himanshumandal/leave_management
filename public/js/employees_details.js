$(document).ready(function () {

    'use strict';


    /*
    |--------------------------------------------------------------------------
    | Configuration
    |--------------------------------------------------------------------------
    */

    const EmployeeDetails = {

        employeeId: Number(window.employeeId),

        ajaxUrl: '../../ajax/employees.php',


        selectors: {

            loading:
                '#employeeLoading',

            error:
                '#employeeError',

            profile:
                '#employeeProfile',

            employeeId:
                '#employeeId',

            employeeName:
                '#employeeName',

            employeeEmail:
                '#employeeEmail',

            employeePhone:
                '#employeePhone',

            employeeDepartment:
                '#employeeDepartment',

            employeeDesignation:
                '#employeeDesignation',

            employeeJoiningDate:
                '#employeeJoiningDate',

            employeeStatus:
                '#employeeStatus',

            editLink:
                '#editEmployeeLink'

        }

    };



    /*
    |--------------------------------------------------------------------------
    | Initialization
    |--------------------------------------------------------------------------
    */

    function init() {

        if (!validateEmployeeId()) {

            return;
        }


        loadEmployee();

    }



    /*
    |--------------------------------------------------------------------------
    | Validate Employee ID
    |--------------------------------------------------------------------------
    */

    function validateEmployeeId() {

        const employeeId =
            EmployeeDetails.employeeId;


        if (
            !Number.isInteger(employeeId)
            ||
            employeeId <= 0
        ) {

            showError(
                'Invalid employee ID.'
            );

            return false;
        }


        return true;

    }



    /*
    |--------------------------------------------------------------------------
    | Load Employee
    |--------------------------------------------------------------------------
    */

    function loadEmployee() {

        showLoading();


        $.ajax({

            url:
                EmployeeDetails.ajaxUrl,

            type:
                'GET',

            data: {

                action:
                    'show',

                id:
                    EmployeeDetails.employeeId

            },

            dataType:
                'json',


            success:
                handleSuccess,


            error:
                handleError,


            complete:
                hideLoading

        });

    }



    /*
    |--------------------------------------------------------------------------
    | AJAX Success
    |--------------------------------------------------------------------------
    */

    function handleSuccess(response) {

        /*
        |--------------------------------------------------------------------------
        | Check API Response
        |--------------------------------------------------------------------------
        */

        if (
            !response
            ||
            typeof response !== 'object'
        ) {

            showError(
                'Invalid server response.'
            );

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | Check Success
        |--------------------------------------------------------------------------
        */

        if (
            response.success !== true
        ) {

            showError(
                response.message
                ||
                'Employee not found.'
            );

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | Check Employee Data
        |--------------------------------------------------------------------------
        */

        if (
            !response.employee
            ||
            typeof response.employee !== 'object'
        ) {

            showError(
                'Employee information is unavailable.'
            );

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | Display Employee
        |--------------------------------------------------------------------------
        */

        displayEmployee(
            response.employee
        );

    }



    /*
    |--------------------------------------------------------------------------
    | AJAX Error
    |--------------------------------------------------------------------------
    */

    function handleError(xhr) {

        let message =
            'Unable to load employee.';


        /*
        |--------------------------------------------------------------------------
        | HTTP Error
        |--------------------------------------------------------------------------
        */

        if (xhr.status === 404) {

            message =
                'Employee service not found.';

        }


        else if (xhr.status === 401) {

            message =
                'You are not authorized.';

        }


        else if (xhr.status === 403) {

            message =
                'You do not have permission to view this employee.';

        }


        else if (xhr.status === 500) {

            message =
                'Server error. Please try again later.';

        }


        /*
        |--------------------------------------------------------------------------
        | API Error Message
        |--------------------------------------------------------------------------
        */

        if (
            xhr.responseJSON
            &&
            xhr.responseJSON.message
        ) {

            message =
                xhr.responseJSON.message;

        }


        showError(message);

    }



    /*
    |--------------------------------------------------------------------------
    | Display Employee
    |--------------------------------------------------------------------------
    */

    function displayEmployee(employee) {

        /*
        |--------------------------------------------------------------------------
        | Basic Information
        |--------------------------------------------------------------------------
        */

        $(EmployeeDetails.selectors.employeeId)
            .text(
                getValue(
                    employee.employee_id
                )
            );


        $(EmployeeDetails.selectors.employeeName)
            .text(
                buildFullName(
                    employee.first_name,
                    employee.last_name
                )
            );


        $(EmployeeDetails.selectors.employeeEmail)
            .text(
                getValue(
                    employee.email
                )
            );


        $(EmployeeDetails.selectors.employeePhone)
            .text(
                getValue(
                    employee.phone
                )
            );


        $(EmployeeDetails.selectors.employeeDepartment)
            .text(
                getValue(
                    employee.department
                )
            );


        $(EmployeeDetails.selectors.employeeDesignation)
            .text(
                getValue(
                    employee.designation
                )
            );


        $(EmployeeDetails.selectors.employeeJoiningDate)
            .text(
                getValue(
                    employee.joining_date
                )
            );


        /*
        |--------------------------------------------------------------------------
        | Status
        |--------------------------------------------------------------------------
        */

        displayStatus(
            employee.status
        );


        /*
        |--------------------------------------------------------------------------
        | Edit Link
        |--------------------------------------------------------------------------
        */

        setEditLink(
            employee.id
        );


        /*
        |--------------------------------------------------------------------------
        | Show Profile
        |--------------------------------------------------------------------------
        */

        $(EmployeeDetails.selectors.profile)
            .removeClass('d-none');

    }



    /*
    |--------------------------------------------------------------------------
    | Build Full Name
    |--------------------------------------------------------------------------
    */

    function buildFullName(
        firstName,
        lastName
    ) {

        const first =
            getValue(firstName);


        const last =
            getValue(lastName);


        return `${first} ${last}`.trim();

    }



    /*
    |--------------------------------------------------------------------------
    | Get Safe Value
    |--------------------------------------------------------------------------
    */

    function getValue(value) {

        if (
            value === null
            ||
            value === undefined
            ||
            value === ''
        ) {

            return 'N/A';

        }


        return String(value);

    }



    /*
    |--------------------------------------------------------------------------
    | Display Status
    |--------------------------------------------------------------------------
    */

    function displayStatus(status) {

        const normalizedStatus =
            String(
                status ?? ''
            )
            .toLowerCase()
            .trim();


        let badgeClass =
            'bg-secondary';


        switch (normalizedStatus) {

            case 'active':

                badgeClass =
                    'bg-success';

                break;


            case 'inactive':

                badgeClass =
                    'bg-secondary';

                break;


            case 'pending':

                badgeClass =
                    'bg-warning text-dark';

                break;


            case 'terminated':

                badgeClass =
                    'bg-danger';

                break;

        }


        const safeStatus =
            escapeHtml(
                getValue(status)
            );


        $(EmployeeDetails.selectors.employeeStatus)
            .html(`
                <span class="badge ${badgeClass}">
                    ${safeStatus}
                </span>
            `);

    }



    /*
    |--------------------------------------------------------------------------
    | Set Edit Link
    |--------------------------------------------------------------------------
    */

    function setEditLink(employeeId) {

        const id =
            Number(employeeId);


        if (
            !Number.isInteger(id)
            ||
            id <= 0
        ) {

            $(EmployeeDetails.selectors.editLink)
                .addClass('disabled')
                .attr('href', '#');

            return;
        }


        $(EmployeeDetails.selectors.editLink)
            .attr(
                'href',
                `employees.php?edit=${encodeURIComponent(id)}`
            );

    }



    /*
    |--------------------------------------------------------------------------
    | Show Loading
    |--------------------------------------------------------------------------
    */

    function showLoading() {

        $(EmployeeDetails.selectors.loading)
            .removeClass('d-none');


        $(EmployeeDetails.selectors.profile)
            .addClass('d-none');


        $(EmployeeDetails.selectors.error)
            .addClass('d-none');

    }



    /*
    |--------------------------------------------------------------------------
    | Hide Loading
    |--------------------------------------------------------------------------
    */

    function hideLoading() {

        $(EmployeeDetails.selectors.loading)
            .addClass('d-none');

    }



    /*
    |--------------------------------------------------------------------------
    | Show Error
    |--------------------------------------------------------------------------
    */

    function showError(message) {

        const errorMessage =
            message
            ||
            'Something went wrong.';


        $(EmployeeDetails.selectors.error)
            .removeClass('d-none')
            .text(errorMessage);


        $(EmployeeDetails.selectors.profile)
            .addClass('d-none');

    }



    /*
    |--------------------------------------------------------------------------
    | Escape HTML
    |--------------------------------------------------------------------------
    */

    function escapeHtml(value) {

        return $('<div>')
            .text(
                value ?? ''
            )
            .html();

    }



    /*
    |--------------------------------------------------------------------------
    | Start Application
    |--------------------------------------------------------------------------
    */

    init();

});
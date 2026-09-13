$(document).ready(function () {

    // =========================================================
    // CONFIGURATION
    // =========================================================

    const AJAX_URL = '../ajax/employees.php';

    const employeesPerPage = 10;

    let currentEmployeePage = 1;

    let searchTimer = null;

    let selectedEmployeeId = null;

    // =========================================================
    // DEPARTMENT / DESIGNATION
    // =========================================================

    let DEPARTMENTS = [];

    let DEPARTMENT_DESIGNATIONS = {};




    // =========================================================
    // INITIAL LOAD
    // =========================================================

    // loadEmployees(1);
    loadDepartments();

    loadEmployees(1);


    // =========================================================
    // LOAD EMPLOYEES
    // =========================================================

    function loadEmployees(page = 1) {

        currentEmployeePage = page;


        const search =
            $('#employeeSearch')
                .val()
                .trim();


        const department =
            $('#departmentFilter')
                .val() || '';


        const status =
            $('#statusFilter')
                .val() || '';


        showTableLoader();


        $.ajax({

            url: AJAX_URL,

            type: 'GET',

            data: {

                action: 'index',

                search: search,

                department: department,

                status: status,

                page: page,

                per_page: employeesPerPage

            },

            dataType: 'json',


            success: function (response) {

                console.log(
                    'Load employees:',
                    response
                );


                if (
                    !response ||
                    !response.success
                ) {

                    showTableMessage(

                        response?.message ||
                        'Unable to load employees.',

                        'danger'

                    );

                    $('#employeePagination')
                        .empty();

                    $('#employeePaginationInfo')
                        .text('');

                    return;
                }


                const employees =
                    response.employees || [];

                $("#employeeCount").text(`${employees.length} Employees`);
                
                displayEmployees(
                    employees,
                    response.pagination || null
                );

                log


                if (response.pagination) {

                    renderPagination(
                        response.pagination
                    );

                } else {

                    $('#employeePagination')
                        .empty();

                    $('#employeePaginationInfo')
                        .text('');

                }

            },


            error: function (xhr) {

                handleAjaxError(

                    xhr,

                    'Unable to load employees.'

                );

            }

        });

    }


    // =========================================================
    // RENDER PAGINATION
    // =========================================================

    function renderPagination(pagination) {

        const container =
            $('#employeePagination');


        container.empty();


        if (!pagination) {

            $('#employeePaginationInfo')
                .text('');

            return;
        }


        const total =
            parseInt(
                pagination.total,
                10
            ) || 0;


        const totalPages =
            parseInt(
                pagination.total_pages,
                10
            ) || 0;


        const currentPage =
            parseInt(
                pagination.page,
                10
            ) || 1;


        const perPage =
            parseInt(
                pagination.per_page,
                10
            ) || employeesPerPage;


        // -----------------------------------------
        // NO EMPLOYEES
        // -----------------------------------------

        if (
            total === 0 ||
            totalPages === 0
        ) {

            $('#employeePaginationInfo')
                .text('No employees found.');

            return;
        }


        // -----------------------------------------
        // INFORMATION
        // -----------------------------------------

        const start =
            ((currentPage - 1) * perPage) + 1;


        const end =
            Math.min(
                start + perPage - 1,
                total
            );


        $('#employeePaginationInfo')
            .text(
                `Showing ${start}-${end} of ${total}`
            );


        // -----------------------------------------
        // PREVIOUS
        // -----------------------------------------

        const previousDisabled =
            currentPage <= 1;


        container.append(`

            <li
                class="page-item ${previousDisabled
                ? 'disabled'
                : ''
            }"
            >

                <button
                    type="button"
                    class="page-link"
                    data-page="${currentPage - 1}"
                    ${previousDisabled
                ? 'disabled'
                : ''
            }
                >
                    Previous
                </button>

            </li>

        `);


        // -----------------------------------------
        // PAGE NUMBERS
        // -----------------------------------------

        for (
            let page = 1;
            page <= totalPages;
            page++
        ) {

            const active =
                page === currentPage
                    ? 'active'
                    : '';


            container.append(`

                <li
                    class="page-item ${active}"
                >

                    <button
                        type="button"
                        class="page-link"
                        data-page="${page}"
                        ${page === currentPage
                    ? 'aria-current="page"'
                    : ''
                }
                    >
                        ${page}
                    </button>

                </li>

            `);

        }


        // -----------------------------------------
        // NEXT
        // -----------------------------------------

        const nextDisabled =
            currentPage >= totalPages;


        container.append(`

            <li
                class="page-item ${nextDisabled
                ? 'disabled'
                : ''
            }"
            >

                <button
                    type="button"
                    class="page-link"
                    data-page="${currentPage + 1}"
                    ${nextDisabled
                ? 'disabled'
                : ''
            }
                >
                    Next
                </button>

            </li>

        `);

    }


    // =========================================================
    // PAGINATION CLICK
    // =========================================================

    $(document).on(
        'click',
        '#employeePagination .page-link',
        function () {

            const button = $(this);


            if (
                button.prop('disabled') ||
                button
                    .closest('.page-item')
                    .hasClass('disabled')
            ) {

                return;
            }


            const page =
                parseInt(
                    button.attr('data-page'),
                    10
                );


            if (
                !page ||
                page < 1
            ) {

                return;
            }


            loadEmployees(page);

        }
    );


    // =========================================================
    // SEARCH WITH DEBOUNCE
    // =========================================================

    $('#employeeSearch').on(
        'input',
        function () {

            clearTimeout(searchTimer);


            searchTimer =
                setTimeout(
                    function () {

                        loadEmployees(1);

                    },
                    400
                );

        }
    );


    // =========================================================
    // DEPARTMENT FILTER
    // =========================================================

    $('#departmentFilter').on(
        'change',
        function () {

            loadEmployees(1);

        }
    );


    // =========================================================
    // STATUS FILTER
    // =========================================================

    $('#statusFilter').on(
        'change',
        function () {

            loadEmployees(1);

        }
    );


    // =========================================================
    // RESET FILTERS
    // =========================================================

    $('#resetEmployeeFilters').on(
        'click',
        function () {

            $('#employeeSearch')
                .val('');

            $('#departmentFilter')
                .val('');

            $('#statusFilter')
                .val('');


            loadEmployees(1);

        }
    );


    // =========================================================
    // TABLE LOADER
    // =========================================================

    function showTableLoader() {

        $('#employeeTableBody').html(`

            <tr>

                <td
                    colspan="10"
                    class="text-center py-5"
                >

                    <div
                        class="spinner-border text-primary"
                        role="status"
                    >

                        <span
                            class="visually-hidden"
                        >
                            Loading...
                        </span>

                    </div>

                    <div
                        class="mt-3 text-muted"
                    >
                        Loading employees...
                    </div>

                </td>

            </tr>

        `);

    }


    // =========================================================
    // CREATE EMPLOYEE
    // =========================================================

    $('#employeeForm').on(
        'submit',
        function (event) {

            event.preventDefault();


            clearErrors();


            if (!validateForm()) {

                return;
            }


            if (
                $('#saveEmployeeBtn')
                    .prop('disabled')
            ) {

                return;
            }


            Swal.fire({

                title: 'Create Employee?',

                text:
                    'Are you sure you want to create this employee?',

                icon: 'question',

                showCancelButton: true,

                confirmButtonText:
                    'Yes, Create',

                cancelButtonText:
                    'Cancel',

                reverseButtons: true,

                focusCancel: true

            }).then(
                function (result) {

                    if (!result.isConfirmed) {

                        return;
                    }


                    createEmployee();

                }
            );

        }
    );


    // =========================================================
    // CREATE EMPLOYEE AJAX
    // =========================================================

    function createEmployee() {

        setCreateLoading(true);


        $.ajax({

            url: AJAX_URL,

            type: 'POST',

            data:
                $('#employeeForm')
                    .serialize(),

            dataType: 'json',


            success: function (response) {

                console.log(
                    'Create employee:',
                    response
                );


                if (
                    response &&
                    response.success
                ) {

                    Swal.fire({

                        icon: 'success',

                        title:
                            'Employee Created',

                        text:
                            response.message ||
                            'Employee created successfully.',

                        timer: 1800,

                        timerProgressBar: true,

                        showConfirmButton: false

                    }).then(
                        function () {

                            closeCreateModal();

                        }
                    );


                    // New employee

                    loadEmployees(1);


                    return;
                }


                const message =
                    response?.message ||
                    'Unable to create employee.';


                showFormMessage(
                    message,
                    'danger'
                );


                Swal.fire({

                    icon: 'error',

                    title:
                        'Creation Failed',

                    text: message

                });

            },


            error: function (xhr) {

                console.error(
                    'Create AJAX Error:',
                    xhr.responseText
                );


                const response =
                    xhr.responseJSON;


                if (
                    response &&
                    response.errors
                ) {

                    showErrors(
                        response.errors
                    );


                    const message =
                        response.message ||
                        'Please correct the highlighted fields.';


                    showFormMessage(
                        message,
                        'warning'
                    );


                    Swal.fire({

                        icon: 'warning',

                        title:
                            'Validation Failed',

                        text: message

                    });


                    return;
                }


                handleAjaxError(
                    xhr,
                    'Unable to create employee.'
                );

            },


            complete: function () {

                setCreateLoading(false);

            }

        });

    }


    // =========================================================
    // EDIT EMPLOYEE
    // =========================================================

    $(document).on(
        'click',
        '.editEmployeeBtn',
        function (event) {

            event.preventDefault();


            const button =
                $(this);


            const employeeId =
                button.attr('data-id');


            console.log(
                'Edit clicked:',
                employeeId
            );


            if (!employeeId) {

                Swal.fire({

                    icon: 'error',

                    title:
                        'Invalid Employee',

                    text:
                        'Employee ID is missing.'

                });

                return;
            }


            if (
                button.prop('disabled')
            ) {

                return;
            }


            const originalHtml =
                button.html();


            button

                .prop(
                    'disabled',
                    true
                )

                .attr(
                    'aria-busy',
                    'true'
                )

                .html(`

                    <span
                        class="spinner-border spinner-border-sm me-1"
                        role="status"
                    ></span>

                    Loading...

                `);


            $.ajax({

                url: AJAX_URL,

                type: 'GET',

                data: {

                    action: 'show',

                    id: employeeId

                },

                dataType: 'json',


                success: function (response) {

                    console.log(
                        'Edit response:',
                        response
                    );


                    if (
                        !response ||
                        !response.success
                    ) {

                        Swal.fire({

                            icon: 'error',

                            title:
                                'Unable to Load Employee',

                            text:
                                response?.message ||
                                'Employee not found.'

                        });

                        return;
                    }


                    const employee =
                        response.employee;


                    if (!employee) {

                        Swal.fire({

                            icon: 'error',

                            title:
                                'Employee Not Found',

                            text:
                                'Employee information could not be loaded.'

                        });

                        return;
                    }


                    // -----------------------------------------
                    // POPULATE EDIT FORM
                    // -----------------------------------------

                    $('#edit_id')
                        .val(
                            employee.id || ''
                        );


                    $('#edit_employee_id')
                        .val(
                            employee.employee_id || ''
                        );


                    $('#edit_first_name')
                        .val(
                            employee.first_name || ''
                        );


                    $('#edit_last_name')
                        .val(
                            employee.last_name || ''
                        );


                    $('#edit_email')
                        .val(
                            employee.email || ''
                        );


                    $('#edit_phone')
                        .val(
                            employee.phone || ''
                        );
                    
                    const departmentId =
                    employee.department_id || '';


                    const designationId =
                    employee.designation_id || '';


                    $('#edit_department')
                        .val(
                            employee.department_id  || ''
                        );


                    // $('#edit_designation')
                    //     .val(
                    //         employee.designation_id  || ''
                    //     );

                    loadDesignations(

                        departmentId,

                        '#edit_designation',

                        designationId

                    );


                    $('#edit_joining_date')
                        .val(
                            employee.joining_date || ''
                        );


                    $('#edit_status')
                        .val(
                            employee.status || ''
                        );


                    clearEditErrors();


                    // -----------------------------------------
                    // OPEN MODAL
                    // -----------------------------------------

                    const modalElement =
                        document.getElementById(
                            'editEmployeeModal'
                        );


                    if (!modalElement) {

                        Swal.fire({

                            icon: 'error',

                            title:
                                'Modal Error',

                            text:
                                'Edit modal was not found.'

                        });

                        return;
                    }


                    const modal =
                        bootstrap.Modal
                            .getOrCreateInstance(
                                modalElement
                            );


                    modal.show();

                },


                error: function (xhr) {

                    console.error(
                        'Edit AJAX Error:',
                        xhr.responseText
                    );


                    const response =
                        xhr.responseJSON;


                    Swal.fire({

                        icon: 'error',

                        title:
                            'Unable to Load Employee',

                        text:
                            response?.message ||
                            'Unable to load employee. Please try again.'

                    });

                },


                complete: function () {

                    button

                        .prop(
                            'disabled',
                            false
                        )

                        .removeAttr(
                            'aria-busy'
                        )

                        .html(
                            originalHtml
                        );

                }

            });

        }
    );


    // =========================================================
    // UPDATE EMPLOYEE FORM
    // =========================================================

    $('#editEmployeeForm').on(
        'submit',
        function (event) {

            event.preventDefault();


            clearEditErrors();


            if (!validateEditForm()) {

                return;
            }


            if (
                $('#updateEmployeeBtn')
                    .prop('disabled')
            ) {

                return;
            }


            Swal.fire({

                title:
                    'Update Employee?',

                text:
                    'Are you sure you want to save these changes?',

                icon: 'question',

                showCancelButton: true,

                confirmButtonText:
                    'Yes, Update',

                cancelButtonText:
                    'Cancel',

                reverseButtons: true,

                focusCancel: true

            }).then(
                function (result) {

                    if (!result.isConfirmed) {

                        return;
                    }


                    updateEmployee();

                }
            );

        }
    );


    // =========================================================
    // UPDATE EMPLOYEE AJAX
    // =========================================================

    function updateEmployee() {

        const form =
            $('#editEmployeeForm');


        setUpdateLoading(true);


        const formData =
            form.serialize() +
            '&action=update';


        $.ajax({

            url: AJAX_URL,

            type: 'POST',

            data: formData,

            dataType: 'json',


            success: function (response) {

                console.log(
                    'Update response:',
                    response
                );


                if (
                    response &&
                    response.success
                ) {

                    showEditMessage(

                        response.message ||
                        'Employee updated successfully.',

                        'success'

                    );


                    Swal.fire({

                        icon: 'success',

                        title:
                            'Updated Successfully',

                        text:
                            response.message ||
                            'Employee details have been updated.',

                        timer: 1500,

                        timerProgressBar: true,

                        showConfirmButton: false

                    });


                    // Keep current page

                    loadEmployees(
                        currentEmployeePage
                    );


                    setTimeout(
                        function () {

                            closeEditModal();

                        },
                        500
                    );


                    return;
                }


                const message =
                    response?.message ||
                    'Unable to update employee.';


                showEditMessage(
                    message,
                    'danger'
                );


                Swal.fire({

                    icon: 'error',

                    title:
                        'Update Failed',

                    text: message

                });

            },


            error: function (xhr) {

                console.error(
                    'Update AJAX Error:',
                    xhr.responseText
                );


                const response =
                    xhr.responseJSON;


                if (
                    response &&
                    response.errors
                ) {

                    showEditErrors(
                        response.errors
                    );


                    const message =
                        response.message ||
                        'Please correct the highlighted fields.';


                    showEditMessage(
                        message,
                        'warning'
                    );


                    Swal.fire({

                        icon: 'warning',

                        title:
                            'Validation Failed',

                        text: message

                    });


                    return;
                }


                const message =
                    response?.message ||
                    'Something went wrong while updating the employee.';


                showEditMessage(
                    message,
                    'danger'
                );


                Swal.fire({

                    icon: 'error',

                    title:
                        'Server Error',

                    text: message

                });

            },


            complete: function () {

                setUpdateLoading(false);

            }

        });

    }


    // =========================================================
    // OPEN DELETE CONFIRMATION
    // =========================================================

    $(document).on(
        'click',
        '.deleteEmployeeBtn',
        function (event) {

            event.preventDefault();

            event.stopPropagation();


            const button =
                $(this);


            const employeeId =
                $.trim(
                    button.attr('data-id') || ''
                );
            

            const employeeName =
                $.trim(
                    button.attr('data-name') ||
                    'this employee'
                );


            console.log(
                'Delete Employee:',
                employeeId,
                employeeName
            );


            if (!employeeId) {

                Swal.fire({

                    icon: 'error',

                    title:
                        'Invalid Employee',

                    text:
                        'Employee ID is missing.'

                });

                return;
            }


            selectedEmployeeId =
                employeeId;


            const modalElement =
                document.getElementById(
                    'deleteEmployeeModal'
                );


            if (!modalElement) {

                Swal.fire({

                    icon: 'error',

                    title:
                        'Modal Error',

                    text:
                        'Delete employee modal was not found.'

                });

                return;
            }


            $('#deleteEmployeeName')
                .text(
                    employeeName
                );


            const hiddenInput =
                $('#deleteEmployeeId');


            if (hiddenInput.length) {

                hiddenInput.val(
                    employeeId
                );

            }


            $('#deleteFormMessage')

                .addClass('d-none')

                .removeClass(
                    'alert-success alert-danger alert-warning'
                )

                .text('');


            setDeleteLoading(false);


            const modal =
                bootstrap.Modal
                    .getOrCreateInstance(
                        modalElement
                    );


            modal.show();

        }
    );


    // =========================================================
    // CONFIRM DELETE
    // =========================================================

    $(document).on(
        'click',
        '#confirmDeleteBtn',
        function (event) {

            event.preventDefault();

            event.stopPropagation();


            const button =
                $(this);


            if (
                button.prop('disabled')
            ) {

                return;
            }


            const employeeId =
                $.trim(
                    selectedEmployeeId || ''
                );


            if (!employeeId) {

                showDeleteMessage(

                    'Employee ID is missing. Please close and try again.',

                    'danger'

                );

                return;
            }


            setDeleteLoading(true);


            $.ajax({

                url: AJAX_URL,

                type: 'POST',

                data: {

                    action: 'delete',

                    id: employeeId

                },

                dataType: 'json',


                success: function (response) {

                    console.log(
                        'Delete response:',
                        response
                    );


                    if (
                        response &&
                        response.success
                    ) {

                        showDeleteMessage(

                            response.message ||
                            'Employee deleted successfully.',

                            'success'

                        );


                        // -----------------------------------------
                        // REFRESH CURRENT PAGE
                        // -----------------------------------------

                        loadEmployees(
                            currentEmployeePage
                        );


                        // -----------------------------------------
                        // CLOSE MODAL
                        // -----------------------------------------

                        setTimeout(
                            function () {

                                closeDeleteModal();

                            },
                            700
                        );


                        return;
                    }


                    showDeleteMessage(

                        response?.message ||
                        'Unable to delete employee.',

                        'danger'

                    );

                },


                error: function (xhr) {

                    console.error(
                        'Delete AJAX Error:',
                        xhr.status
                    );


                    console.error(
                        'Response:',
                        xhr.responseText
                    );


                    let message =
                        'Unable to delete employee. Please try again.';


                    if (
                        xhr.responseJSON
                    ) {

                        message =
                            xhr.responseJSON.message ||
                            message;

                    }


                    showDeleteMessage(
                        message,
                        'danger'
                    );

                },


                complete: function () {

                    setDeleteLoading(false);

                }

            });

        }
    );


    // =========================================================
    // CREATE FORM VALIDATION
    // =========================================================

    function validateForm() {

        return validateFields({

            prefix: '',

            showError:
                showFieldError

        });

    }


    // =========================================================
    // EDIT FORM VALIDATION
    // =========================================================

    function validateEditForm() {

        return validateFields({

            prefix: 'edit_',

            showError:
                showEditFieldError

        });

    }


    // =========================================================
    // COMMON VALIDATION
    // =========================================================

    function validateFields(options) {

        const prefix =
            options.prefix;


        const showError =
            options.showError;


        let isValid = true;


        // -----------------------------------------
        // GET VALUES
        // -----------------------------------------

        const employeeId =
            $.trim(
                $('#' + prefix + 'employee_id')
                    .val() || ''
            );


        const firstName =
            $.trim(
                $('#' + prefix + 'first_name')
                    .val() || ''
            );


        const lastName =
            $.trim(
                $('#' + prefix + 'last_name')
                    .val() || ''
            );


        const email =
            $.trim(
                $('#' + prefix + 'email')
                    .val() || ''
            );


        const phone =
            $.trim(
                $('#' + prefix + 'phone')
                    .val() || ''
            );


        const department =
            $.trim(
                $('#' + prefix + 'department')
                    .val() || ''
            );


        const designation =
            $.trim(
                $('#' + prefix + 'designation')
                    .val() || ''
            );


        const joiningDate =
            $.trim(
                $('#' + prefix + 'joining_date')
                    .val() || ''
            );


        const status =
            $.trim(
                $('#' + prefix + 'status')
                    .val() || ''
            );


        // -----------------------------------------
        // EMPLOYEE ID
        // -----------------------------------------

        if (!employeeId) {

            showError(
                'employee_id',
                'Employee ID is required.'
            );

            isValid = false;

        } else if (
            employeeId.length < 2
        ) {

            showError(
                'employee_id',
                'Employee ID must contain at least 2 characters.'
            );

            isValid = false;
        }


        // -----------------------------------------
        // FIRST NAME
        // -----------------------------------------

        if (!firstName) {

            showError(
                'first_name',
                'First name is required.'
            );

            isValid = false;

        } else if (
            firstName.length < 2
        ) {

            showError(
                'first_name',
                'First name must contain at least 2 characters.'
            );

            isValid = false;

        } else if (
            !/^[a-zA-Z\s'-]+$/.test(firstName)
        ) {

            showError(
                'first_name',
                'First name contains invalid characters.'
            );

            isValid = false;
        }


        // -----------------------------------------
        // LAST NAME
        // -----------------------------------------

        if (!lastName) {

            showError(
                'last_name',
                'Last name is required.'
            );

            isValid = false;

        } else if (
            lastName.length < 2
        ) {

            showError(
                'last_name',
                'Last name must contain at least 2 characters.'
            );

            isValid = false;

        } else if (
            !/^[a-zA-Z\s'-]+$/.test(lastName)
        ) {

            showError(
                'last_name',
                'Last name contains invalid characters.'
            );

            isValid = false;
        }


        // -----------------------------------------
        // EMAIL
        // -----------------------------------------

        if (!email) {

            showError(
                'email',
                'Email is required.'
            );

            isValid = false;

        } else if (
            !isValidEmail(email)
        ) {

            showError(
                'email',
                'Please enter a valid email address.'
            );

            isValid = false;
        }


        // -----------------------------------------
        // PHONE
        // -----------------------------------------

        if (!phone) {

            showError(
                'phone',
                'Phone number is required.'
            );

            isValid = false;

        } else if (
            !/^[0-9]{10,15}$/.test(phone)
        ) {

            showError(
                'phone',
                'Phone number must contain 10 to 15 digits.'
            );

            isValid = false;
        }


        // -----------------------------------------
        // DEPARTMENT
        // -----------------------------------------

        if (
            department &&
            DEPARTMENT_DESIGNATIONS[department] &&
            !DEPARTMENT_DESIGNATIONS[department].some(
                function (item) {

                    return String(item.id) ===
                        String(designation);

                }
            )
        ) {

            showError(
                'designation',
                'Selected designation does not belong to the selected department.'
            );

            isValid = false;
        }


        // -----------------------------------------
        // JOINING DATE
        // -----------------------------------------

        if (!joiningDate) {

            showError(
                'joining_date',
                'Joining date is required.'
            );

            isValid = false;
        }


        // -----------------------------------------
        // STATUS
        // -----------------------------------------

        if (!status) {

            showError(
                'status',
                'Status is required.'
            );

            isValid = false;
        }


        // -----------------------------------------
        // VALIDATION ALERT
        // -----------------------------------------

        if (!isValid) {

            Swal.fire({

                icon: 'warning',

                title:
                    'Validation Failed',

                text:
                    'Please correct the highlighted fields.',

                confirmButtonText:
                    'OK'

            });

        }


        return isValid;

    }


    // =========================================================
    // EMAIL VALIDATION
    // =========================================================

    function isValidEmail(email) {

        const pattern =
            /^[^\s@]+@[^\s@]+\.[^\s@]+$/;


        return pattern.test(email);

    }


    // =========================================================
    // CREATE FIELD ERROR
    // =========================================================

    function showFieldError(
        field,
        message
    ) {

        $('#' + field + 'Error')
            .text(
                message || ''
            );

    }


    // =========================================================
    // EDIT FIELD ERROR
    // =========================================================

    function showEditFieldError(
        field,
        message
    ) {

        $('#edit_' + field + 'Error')
            .text(
                message || ''
            );

    }


    // =========================================================
    // CREATE BACKEND ERRORS
    // =========================================================

    function showErrors(errors) {

        if (
            !errors ||
            typeof errors !== 'object'
        ) {

            return;
        }


        Object.keys(errors)
            .forEach(
                function (field) {

                    let message =
                        errors[field];


                    if (
                        Array.isArray(message)
                    ) {

                        message =
                            message[0];

                    }


                    $('#' + field + 'Error')
                        .text(
                            message || ''
                        );

                }
            );

    }


    // =========================================================
    // EDIT BACKEND ERRORS
    // =========================================================

    function showEditErrors(errors) {

        if (
            !errors ||
            typeof errors !== 'object'
        ) {

            return;
        }


        Object.keys(errors)
            .forEach(
                function (field) {

                    let message =
                        errors[field];


                    if (
                        Array.isArray(message)
                    ) {

                        message =
                            message[0];

                    }


                    $('#edit_' + field + 'Error')
                        .text(
                            message || ''
                        );

                }
            );

    }


    // =========================================================
    // CLEAR CREATE ERRORS
    // =========================================================

    function clearErrors() {

        $('#employeeForm')

            .find('[id$="Error"]')

            .text('');


        $('#formMessage')

            .stop(true, true)

            .removeClass(
                'alert-success alert-danger alert-warning'
            )

            .addClass('d-none')

            .text('');

    }


    // =========================================================
    // CLEAR EDIT ERRORS
    // =========================================================

    function clearEditErrors() {

        $('#editEmployeeForm')

            .find('[id$="Error"]')

            .text('');


        $('#editFormMessage')

            .stop(true, true)

            .removeClass(
                'alert-success alert-danger alert-warning'
            )

            .addClass('d-none')

            .text('');

    }


    // =========================================================
    // SHOW CREATE FORM MESSAGE
    // =========================================================

    function showFormMessage(
        message,
        type
    ) {

        $('#formMessage')

            .stop(true, true)

            .removeClass(
                'd-none alert-success alert-danger alert-warning'
            )

            .addClass(
                'alert-' + type
            )

            .hide()

            .text(message)

            .fadeIn(200);

    }


    // =========================================================
    // SHOW EDIT FORM MESSAGE
    // =========================================================

    function showEditMessage(
        message,
        type
    ) {

        $('#editFormMessage')

            .stop(true, true)

            .removeClass(
                'd-none alert-success alert-danger alert-warning'
            )

            .addClass(
                'alert-' + type
            )

            .hide()

            .text(message)

            .fadeIn(200);

    }


    // =========================================================
    // SHOW DELETE MESSAGE
    // =========================================================

    function showDeleteMessage(
        message,
        type
    ) {

        $('#deleteFormMessage')

            .stop(true, true)

            .removeClass(
                'd-none alert-success alert-danger alert-warning'
            )

            .addClass(
                'alert-' + type
            )

            .hide()

            .text(message)

            .fadeIn(200);

    }


    // =========================================================
    // CREATE LOADING
    // =========================================================

    function setCreateLoading(
        isLoading
    ) {

        const button =
            $('#saveEmployeeBtn');


        if (!button.length) {

            return;
        }


        if (isLoading) {

            button

                .prop(
                    'disabled',
                    true
                )

                .attr(
                    'aria-busy',
                    'true'
                )

                .html(`

                    <span
                        class="spinner-border spinner-border-sm me-2"
                        role="status"
                    ></span>

                    Creating...

                `);

        } else {

            button

                .prop(
                    'disabled',
                    false
                )

                .removeAttr(
                    'aria-busy'
                )

                .html(`

                    <i
                        class="bi bi-person-plus me-1"
                    ></i>

                    Add Employee

                `);

        }

    }


    // =========================================================
    // UPDATE LOADING
    // =========================================================

    function setUpdateLoading(
        isLoading
    ) {

        const button =
            $('#updateEmployeeBtn');


        if (!button.length) {

            return;
        }


        if (isLoading) {

            if (
                !button.data(
                    'original-html'
                )
            ) {

                button.data(
                    'original-html',
                    button.html()
                );

            }


            button

                .prop(
                    'disabled',
                    true
                )

                .attr(
                    'aria-busy',
                    'true'
                )

                .html(`

                    <span
                        class="spinner-border spinner-border-sm me-2"
                        role="status"
                    ></span>

                    Updating...

                `);


            $('#editEmployeeModal')

                .find(
                    '[data-bs-dismiss="modal"], .btn-close'
                )

                .prop(
                    'disabled',
                    true
                );

        } else {

            button

                .prop(
                    'disabled',
                    false
                )

                .removeAttr(
                    'aria-busy'
                )

                .html(

                    button.data(
                        'original-html'
                    ) ||

                    `

                    <i
                        class="bi bi-check-lg me-1"
                    ></i>

                    Update Employee

                    `

                );


            $('#editEmployeeModal')

                .find(
                    '[data-bs-dismiss="modal"], .btn-close'
                )

                .prop(
                    'disabled',
                    false
                );

        }

    }


    // =========================================================
    // DELETE LOADING
    // =========================================================

    function setDeleteLoading(
        isLoading
    ) {

        const button =
            $('#confirmDeleteBtn');


        if (!button.length) {

            return;
        }


        if (isLoading) {

            if (
                !button.data(
                    'original-html'
                )
            ) {

                button.data(
                    'original-html',
                    button.html()
                );

            }


            button

                .prop(
                    'disabled',
                    true
                )

                .attr(
                    'aria-busy',
                    'true'
                )

                .html(`

                    <span
                        class="spinner-border spinner-border-sm me-2"
                        role="status"
                    ></span>

                    Deleting...

                `);


            $('#deleteEmployeeModal')

                .find(
                    '[data-bs-dismiss="modal"], .btn-close'
                )

                .prop(
                    'disabled',
                    true
                );

        } else {

            button

                .prop(
                    'disabled',
                    false
                )

                .removeAttr(
                    'aria-busy'
                )

                .html(

                    button.data(
                        'original-html'
                    ) ||

                    `

                    <i
                        class="bi bi-trash me-1"
                    ></i>

                    Delete Employee

                    `

                );


            $('#deleteEmployeeModal')

                .find(
                    '[data-bs-dismiss="modal"], .btn-close'
                )

                .prop(
                    'disabled',
                    false
                );

        }

    }


    // =========================================================
    // CLOSE CREATE MODAL
    // =========================================================

    function closeCreateModal() {

        const modalElement =
            document.getElementById(
                'employeeModal'
            );


        if (!modalElement) {

            return;
        }


        const modal =
            bootstrap.Modal
                .getInstance(
                    modalElement
                );


        if (modal) {

            modal.hide();

        }


        const form =
            $('#employeeForm')[0];


        if (form) {

            form.reset();

        }


        clearErrors();


        setCreateLoading(false);

    }


    // =========================================================
    // CLOSE EDIT MODAL
    // =========================================================

    function closeEditModal() {

        const modalElement =
            document.getElementById(
                'editEmployeeModal'
            );


        if (!modalElement) {

            return;
        }


        const modal =
            bootstrap.Modal
                .getInstance(
                    modalElement
                );


        if (modal) {

            modal.hide();

        }

    }


    // =========================================================
    // CLOSE DELETE MODAL
    // =========================================================

    function closeDeleteModal() {

        const modalElement =
            document.getElementById(
                'deleteEmployeeModal'
            );


        if (!modalElement) {

            return;
        }


        const modal =
            bootstrap.Modal
                .getInstance(
                    modalElement
                );


        if (modal) {

            modal.hide();

        }


        selectedEmployeeId = null;


        $('#deleteEmployeeId')
            .val('');


        $('#deleteEmployeeName')
            .text('');


        $('#deleteFormMessage')

            .addClass('d-none')

            .removeClass(
                'alert-success alert-danger alert-warning'
            )

            .text('');

    }


    // =========================================================
    // CREATE MODAL SHOW
    // =========================================================

    $('#employeeModal').on(
        'show.bs.modal',
        function () {

            const form =
                $('#employeeForm')[0];


            if (form) {

                form.reset();

            }


            clearErrors();


            setCreateLoading(false);

        }
    );


    // =========================================================
    // EDIT MODAL SHOW
    // =========================================================

    $('#editEmployeeModal').on(
        'show.bs.modal',
        function () {

            clearEditErrors();

        }
    );


    // =========================================================
    // EDIT MODAL HIDDEN
    // =========================================================

    $('#editEmployeeModal').on(
        'hidden.bs.modal',
        function () {

            const form =
                $('#editEmployeeForm')[0];


            if (form) {

                form.reset();

            }


            clearEditErrors();


            setUpdateLoading(false);

        }
    );


    // =========================================================
    // DELETE MODAL HIDDEN
    // =========================================================

    $('#deleteEmployeeModal').on(
        'hidden.bs.modal',
        function () {

            $('#deleteEmployeeId')
                .val('');


            $('#deleteEmployeeName')
                .text('');


            $('#deleteFormMessage')

                .addClass('d-none')

                .removeClass(
                    'alert-success alert-danger alert-warning'
                )

                .text('');


            selectedEmployeeId = null;


            setDeleteLoading(false);

        }
    );


    // =========================================================
    // DISPLAY EMPLOYEES
    // =========================================================

    function displayEmployees(
        employees,
        pagination = null
    ) {

        const tbody =
            $('#employeeTableBody');


        tbody.empty();


        // -----------------------------------------
        // NO EMPLOYEES
        // -----------------------------------------

        if (
            !employees ||
            employees.length === 0
        ) {

            tbody.html(`

                <tr>

                    <td
                        colspan="10"
                        class="text-center text-muted py-5"
                    >

                        <i
                            class="bi bi-people fs-2 d-block mb-2"
                        ></i>

                        No employees found.

                    </td>

                </tr>

            `);

            return;
        }


        // -----------------------------------------
        // PAGINATION OFFSET
        // -----------------------------------------

        const page =
            pagination
                ? parseInt(
                    pagination.page,
                    10
                ) || 1
                : 1;


        const perPage =
            pagination
                ? parseInt(
                    pagination.per_page,
                    10
                ) || employeesPerPage
                : employeesPerPage;


        const pageOffset =
            (page - 1) * perPage;


        // -----------------------------------------
        // EMPLOYEE ROWS
        // -----------------------------------------

        employees.forEach(
            function (
                employee,
                index
            ) {

                const rowNumber =
                    pageOffset + index + 1;


                // -----------------------------------------
                // STATUS
                // -----------------------------------------

                let statusBadge;


                if (
                    employee.status === 'active'
                ) {

                    statusBadge = `

                        <span
                            class="badge bg-success"
                        >
                            Active
                        </span>

                    `;

                } else {

                    statusBadge = `

                        <span
                            class="badge bg-secondary"
                        >
                            Inactive
                        </span>

                    `;

                }


                // -----------------------------------------
                // FULL NAME
                // -----------------------------------------

                const fullName =
                    `${employee.first_name || ''}
                     ${employee.last_name || ''}`
                        .trim();


                // -----------------------------------------
                // ROW
                // -----------------------------------------

                tbody.append(`

                    <tr>

                        <td>
                            ${rowNumber}
                        </td>


                        <td>

                            <strong>
                                ${escapeHtml(
                    employee.employee_id
                )}
                            </strong>

                        </td>


                        <td>

                            ${escapeHtml(
                    employee.first_name
                )}

                            ${escapeHtml(
                    employee.last_name
                )}

                        </td>


                        <td>

                            ${escapeHtml(
                    employee.email
                )}

                        </td>


                        <td>

                            ${escapeHtml(
                    employee.phone
                )}

                        </td>


                        <td>

                            ${escapeHtml(
                    employee.department
                )}

                        </td>


                        <td>

                            ${escapeHtml(
                    employee.designation
                )}

                        </td>


                        <td>

                            ${escapeHtml(
                    employee.joining_date
                )}

                        </td>


                        <td>

                            ${statusBadge}

                        </td>


                        <td>

                            <div
                                class="d-flex gap-2"
                            >
                                <a
                                    href="../../admin/employee_profile.php?id=${encodeURIComponent(employee.encrypted_id)}"
                                    class="btn btn-sm btn-info"
                                    title="View Employee"
                                >
                                    <i class="bi bi-eye"></i>
                                    View
                                </a>

                                <!-- EDIT -->

                                <button
                                    type="button"
                                    class="btn btn-sm btn-primary editEmployeeBtn"
                                    data-id="${escapeHtml(employee.encrypted_id)}"
                                    title="Edit Employee"
                                >

                                    <i
                                        class="bi bi-pencil"
                                    ></i>

                                    Edit

                                </button>


                                <!-- DELETE -->

                                <button
                                    type="button"
                                    class="btn btn-sm btn-danger deleteEmployeeBtn"
                                    data-id="${escapeHtml(employee.encrypted_id)}"
                                    data-name="${escapeHtml(fullName)}"
                                    title="Delete Employee"
                                >

                                    <i
                                        class="bi bi-trash"
                                    ></i>

                                    Delete

                                </button>

                            </div>

                        </td>

                    </tr>

                `);

            }
        );

    }


    // =========================================================
    // ESCAPE HTML
    // =========================================================

    function escapeHtml(value) {

        if (
            value === null ||
            value === undefined
        ) {

            return '';
        }


        return $('<div>')
            .text(value)
            .html();

    }


    // =========================================================
    // TABLE MESSAGE
    // =========================================================

    function showTableMessage(
        message,
        type
    ) {

        let icon =
            'bi-exclamation-circle';


        if (type === 'danger') {

            icon =
                'bi-exclamation-triangle';

        }


        $('#employeeTableBody').html(`

            <tr>

                <td
                    colspan="10"
                    class="text-center py-5"
                >

                    <i
                        class="bi ${icon} fs-2 text-danger d-block mb-2"
                    ></i>


                    <div
                        class="text-danger"
                    >
                        ${escapeHtml(message)}
                    </div>


                    <button
                        type="button"
                        class="btn btn-sm btn-outline-primary mt-3"
                        id="reloadEmployeesBtn"
                    >

                        <i
                            class="bi bi-arrow-clockwise"
                        ></i>

                        Try Again

                    </button>

                </td>

            </tr>

        `);

    }


    // =========================================================
    // RETRY LOAD EMPLOYEES
    // =========================================================

    $(document).on(
        'click',
        '#reloadEmployeesBtn',
        function () {

            loadEmployees(
                currentEmployeePage
            );

        }
    );


    // =========================================================
    // GENERAL AJAX ERROR
    // =========================================================

    function handleAjaxError(
        xhr,
        defaultMessage
    ) {

        console.error(
            'AJAX Error:',
            xhr.status,
            xhr.responseText
        );


        const response =
            xhr.responseJSON;


        const message =
            response?.message ||
            defaultMessage;


        Swal.fire({

            icon: 'error',

            title:
                'Something Went Wrong',

            text: message,

            confirmButtonText:
                'OK'

        });


        showTableMessage(
            message,
            'danger'
        );

    }

    // =========================================================
    // LOAD DEPARTMENTS AND DESIGNATIONS
    // =========================================================

    // =========================================================
    // LOAD DEPARTMENTS
    // =========================================================

    function loadDepartments() {

        $.ajax({

            url: AJAX_URL,

            type: 'GET',

            data: {
                action: 'departments'
            },

            dataType: 'json',

            success: function (response) {

                console.log(
                    'Departments AJAX response:',
                    response
                );


                if (
                    !response ||
                    !response.success
                ) {

                    console.error(
                        response?.message ||
                        'Unable to load departments.'
                    );

                    return;
                }


                DEPARTMENTS =
                    Array.isArray(response.departments)
                        ? response.departments
                        : [];


                console.log(
                    'Departments:',
                    DEPARTMENTS
                );


                // -----------------------------------------
                // POPULATE DEPARTMENT DROPDOWNS
                // -----------------------------------------

                populateDepartmentDropdown(
                    '#department'
                );


                populateDepartmentDropdown(
                    '#edit_department'
                );


                populateDepartmentFilter(
                    '#departmentFilter'
                );

            },


            error: function (xhr) {

                console.error(
                    'Department AJAX Error:',
                    xhr.status,
                    xhr.responseText
                );

            }

        });

    }



// =========================================================
// POPULATE DEPARTMENT DROPDOWN
// =========================================================

function populateDepartmentDropdown(
    selector,
    selectedDepartment = ''
) {

    const department =
        $(selector);


    if (!department.length) {

        return;
    }


    department.empty();


    department.append(
        $('<option>', {

            value: '',

            text: 'Select Department'

        })
    );


    DEPARTMENTS.forEach(
        function (item) {

            department.append(
                $('<option>', {

                    value: String(
                        item.id
                    ),

                    text: item.name

                })
            );

        }
    );


    if (selectedDepartment !== '') {

        department.val(
            String(
                selectedDepartment
            )
        );

    }

}



// =========================================================
// POPULATE DEPARTMENT FILTER
// =========================================================

function populateDepartmentFilter(
    selector
) {

    const department =
        $(selector);


    if (!department.length) {

        return;
    }


    const currentValue =
        department.val() || '';


    department.empty();


    department.append(
        $('<option>', {

            value: '',

            text: 'All Departments'

        })
    );


    DEPARTMENTS.forEach(
        function (item) {

            department.append(
                $('<option>', {

                    value: String(
                        item.id
                    ),

                    text: item.name

                })
            );

        }
    );


    if (currentValue !== '') {

        department.val(
            String(currentValue)
        );

    }

}



// =========================================================
// LOAD DESIGNATIONS BY DEPARTMENT
// =========================================================

function loadDesignations(
    departmentId,
    selector,
    selectedDesignation = ''
) {

    const designation =
        $(selector);


    if (!designation.length) {

        return;
    }


    // -----------------------------------------
    // NO DEPARTMENT SELECTED
    // -----------------------------------------

    if (
        departmentId === null ||
        departmentId === undefined ||
        departmentId === ''
    ) {

        designation.empty();

        designation.append(
            $('<option>', {

                value: '',

                text: 'Select Department First'

            })
        );


        designation.prop(
            'disabled',
            true
        );


        return;
    }


    // -----------------------------------------
    // SHOW LOADING STATE
    // -----------------------------------------

    designation.empty();

    designation.append(
        $('<option>', {

            value: '',

            text: 'Loading Designations...'

        })
    );


    designation.prop(
        'disabled',
        true
    );


    // -----------------------------------------
    // AJAX REQUEST
    // -----------------------------------------

    $.ajax({

        url: AJAX_URL,

        type: 'GET',

        data: {

            action: 'designations',

            department_id:
                departmentId

        },

        dataType: 'json',


        success: function (response) {

            console.log(
                'Designations AJAX response:',
                response
            );


            designation.empty();


            // -----------------------------------------
            // INVALID RESPONSE
            // -----------------------------------------

            if (
                !response ||
                !response.success
            ) {

                console.error(
                    response?.message ||
                    'Unable to load designations.'
                );


                designation.append(
                    $('<option>', {

                        value: '',

                        text:
                            'Unable to load designations'

                    })
                );


                return;
            }


            // -----------------------------------------
            // DEFAULT OPTION
            // -----------------------------------------

            designation.append(
                $('<option>', {

                    value: '',

                    text: 'Select Designation'

                })
            );


            // -----------------------------------------
            // GET DESIGNATIONS
            // -----------------------------------------

            const designations =
                Array.isArray(
                    response.designations
                )
                    ? response.designations
                    : [];


            // -----------------------------------------
            // NO DESIGNATIONS
            // -----------------------------------------

            if (
                designations.length === 0
            ) {

                designation.empty();

                designation.append(
                    $('<option>', {

                        value: '',

                        text:
                            'No Designations Available'

                    })
                );


                designation.prop(
                    'disabled',
                    true
                );


                return;
            }


            // -----------------------------------------
            // ADD DESIGNATIONS
            // -----------------------------------------

            designations.forEach(
                function (item) {

                    designation.append(
                        $('<option>', {

                            value: String(
                                item.id
                            ),

                            text: item.name

                        })
                    );

                }
            );


            // -----------------------------------------
            // ENABLE DROPDOWN
            // -----------------------------------------

            designation.prop(
                'disabled',
                false
            );


            // -----------------------------------------
            // SELECT DESIGNATION
            // -----------------------------------------

            if (
                selectedDesignation !== ''
            ) {

                designation.val(
                    String(
                        selectedDesignation
                    )
                );

            }

        },


        error: function (xhr) {

            console.error(
                'Designation AJAX Error:',
                xhr.status,
                xhr.responseText
            );


            designation.empty();


            designation.append(
                $('<option>', {

                    value: '',

                    text:
                        'Unable to load designations'

                })
            );


            designation.prop(
                'disabled',
                true
            );

        }

    });

}



// =========================================================
// CREATE EMPLOYEE → DEPARTMENT CHANGE
// =========================================================

$(document).on(
    'change',
    '#department',
    function () {

        const departmentId =
            $(this).val();


        loadDesignations(

            departmentId,

            '#designation'

        );

    }
);



// =========================================================
// EDIT EMPLOYEE → DEPARTMENT CHANGE
// =========================================================

$(document).on(
    'change',
    '#edit_department',
    function () {

        const departmentId =
            $(this).val();


        loadDesignations(

            departmentId,

            '#edit_designation'

        );

    }
);

});
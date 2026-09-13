$(document).ready(function () {

    // =========================================================
    // CONFIGURATION
    // =========================================================

    const AJAX_URL = '../ajax/department.php';

    const departmentsPerPage = 10;

    let currentDepartmentPage = 1;

    let searchTimer = null;

    let selectedDepartmentId = null;


    // =========================================================
    // INITIAL LOAD
    // =========================================================

    loadDepartments(1);


    // =========================================================
    // LOAD DEPARTMENTS
    // =========================================================

    function loadDepartments(page = 1) {

        currentDepartmentPage = page;


        const search =
            $('#departmentSearch')
                .val()
                .trim();


        const status =
            $('#departmentStatusFilter')
                .val() || '';


        showTableLoader();


        $.ajax({

            url: AJAX_URL,

            type: 'GET',

            data: {

                action: 'index',

                search: search,

                status: status,

                page: page,

                per_page: departmentsPerPage

            },

            dataType: 'json',


            success: function (response) {

                console.log(
                    'Load departments:',
                    response
                );


                if (
                    !response ||
                    !response.success
                ) {

                    showTableMessage(

                        response?.message ||
                        'Unable to load departments.',

                        'danger'

                    );


                    $('#departmentPagination')
                        .empty();


                    $('#departmentPaginationInfo')
                        .text('');


                    $('#departmentCount')
                        .text(
                            'Unable to load departments.'
                        );


                    return;
                }


                const departments =
                    response.departments || [];


                displayDepartments(

                    departments,

                    response.pagination || null

                );


                if (response.pagination) {

                    renderPagination(
                        response.pagination
                    );

                } else {

                    $('#departmentPagination')
                        .empty();

                    $('#departmentPaginationInfo')
                        .text('');

                }


                // -----------------------------------------
                // DEPARTMENT COUNT
                // -----------------------------------------

                const total =
                    response.pagination
                        ? parseInt(
                            response.pagination.total,
                            10
                        ) || 0
                        : departments.length;


                $('#departmentCount')
                    .text(
                        `${total} Department${total !== 1 ? 's' : ''}`
                    );

            },


            error: function (xhr) {

                handleAjaxError(

                    xhr,

                    'Unable to load departments.'

                );

            }

        });

    }


    // =========================================================
    // RENDER PAGINATION
    // =========================================================

    function renderPagination(pagination) {

        const container =
            $('#departmentPagination');


        container.empty();


        if (!pagination) {

            $('#departmentPaginationInfo')
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
            ) || departmentsPerPage;


        // -----------------------------------------
        // NO DEPARTMENTS
        // -----------------------------------------

        if (
            total === 0 ||
            totalPages === 0
        ) {

            $('#departmentPaginationInfo')
                .text(
                    'No departments found.'
                );

            return;
        }


        // -----------------------------------------
        // PAGINATION INFORMATION
        // -----------------------------------------

        const start =
            ((currentPage - 1) * perPage) + 1;


        const end =
            Math.min(
                start + perPage - 1,
                total
            );


        $('#departmentPaginationInfo')
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
                class="page-item ${
                    previousDisabled
                        ? 'disabled'
                        : ''
                }"
            >

                <button
                    type="button"
                    class="page-link"
                    data-page="${currentPage - 1}"
                    ${
                        previousDisabled
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
                        ${
                            page === currentPage
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
                class="page-item ${
                    nextDisabled
                        ? 'disabled'
                        : ''
                }"
            >

                <button
                    type="button"
                    class="page-link"
                    data-page="${currentPage + 1}"
                    ${
                        nextDisabled
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
        '#departmentPagination .page-link',
        function () {

            const button =
                $(this);


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


            loadDepartments(page);

        }
    );


    // =========================================================
    // SEARCH WITH DEBOUNCE
    // =========================================================

    $('#departmentSearch').on(
        'input',
        function () {

            clearTimeout(searchTimer);


            searchTimer =
                setTimeout(
                    function () {

                        loadDepartments(1);

                    },
                    400
                );

        }
    );


    // =========================================================
    // STATUS FILTER
    // =========================================================

    $('#departmentStatusFilter').on(
        'change',
        function () {

            loadDepartments(1);

        }
    );


    // =========================================================
    // RESET FILTERS
    // =========================================================

    $('#resetDepartmentFilters').on(
        'click',
        function () {

            $('#departmentSearch')
                .val('');


            $('#departmentStatusFilter')
                .val('');


            loadDepartments(1);

        }
    );


    // =========================================================
    // TABLE LOADER
    // =========================================================

    function showTableLoader() {

        $('#departmentTableBody').html(`

            <tr>

                <td
                    colspan="4"
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

                        Loading departments...

                    </div>

                </td>

            </tr>

        `);

    }


    // =========================================================
    // CREATE DEPARTMENT
    // =========================================================

    $('#departmentForm').on(
        'submit',
        function (event) {

            event.preventDefault();


            clearErrors();


            if (!validateForm()) {

                return;
            }


            if (
                $('#saveDepartmentBtn')
                    .prop('disabled')
            ) {

                return;
            }


            Swal.fire({

                title:
                    'Create Department?',

                text:
                    'Are you sure you want to create this department?',

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


                    createDepartment();

                }
            );

        }
    );


    // =========================================================
    // CREATE DEPARTMENT AJAX
    // =========================================================

    function createDepartment() {

        setCreateLoading(true);
       


        $.ajax({

            url: AJAX_URL,
            
            type: 'POST',

            data:
                $('#departmentForm')
                    .serialize() +
                '&action=store',

            dataType: 'json',


            success: function (response) {

                console.log(
                    'Create department:',
                    response
                );


                if (
                    response &&
                    response.success
                ) {

                    Swal.fire({

                        icon: 'success',

                        title:
                            'Department Created',

                        text:
                            response.message ||
                            'Department created successfully.',

                        timer: 1800,

                        timerProgressBar: true,

                        showConfirmButton: false

                    }).then(
                        function () {

                            closeCreateModal();

                        }
                    );


                    loadDepartments(1);


                    return;
                }


                const message =
                    response?.message ||
                    'Unable to create department.';


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
                    'Create Department AJAX Error:',
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

                    'Unable to create department.'

                );

            },


            complete: function () {

                setCreateLoading(false);

            }

        });

    }


    // =========================================================
    // EDIT DEPARTMENT
    // =========================================================

    $(document).on(
        'click',
        '.editDepartmentBtn',
        function (event) {

            event.preventDefault();


            const button =
                $(this);


            const departmentId =
                button.attr('data-id');

            


            console.log(
                'Edit Department:',
                departmentId
            );


            if (!departmentId) {

                Swal.fire({

                    icon: 'error',

                    title:
                        'Invalid Department',

                    text:
                        'Department ID is missing.'

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

                    id: departmentId

                },

                dataType: 'json',


                success: function (response) {

                    console.log(
                        'Edit department response:',
                        response
                    );


                    if (
                        !response ||
                        !response.success
                    ) {

                        Swal.fire({

                            icon: 'error',

                            title:
                                'Unable to Load Department',

                            text:
                                response?.message ||
                                'Department not found.'

                        });

                        return;
                    }


                    const department =
                        response.department;


                    if (!department) {

                        Swal.fire({

                            icon: 'error',

                            title:
                                'Department Not Found',

                            text:
                                'Department information could not be loaded.'

                        });

                        return;
                    }
                  


                    // -----------------------------------------
                    // POPULATE EDIT FORM
                    // -----------------------------------------

                    $('#edit_department_id')
                        .val(
                            department.id || ''
                        );


                    $('#edit_department_name')
                        .val(
                            department.name ||
                            department.department ||
                            ''
                        );


                    $('#edit_department_status')
                        .val(
                            department.status ||
                            ''
                        );


                    clearEditErrors();


                    // -----------------------------------------
                    // OPEN MODAL
                    // -----------------------------------------

                    const modalElement =
                        document.getElementById(
                            'editDepartmentModal'
                        );


                    if (!modalElement) {

                        Swal.fire({

                            icon: 'error',

                            title:
                                'Modal Error',

                            text:
                                'Edit department modal was not found.'

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
                        'Edit Department AJAX Error:',
                        xhr.responseText
                    );


                    const response =
                        xhr.responseJSON;


                    Swal.fire({

                        icon: 'error',

                        title:
                            'Unable to Load Department',

                        text:
                            response?.message ||
                            'Unable to load department. Please try again.'

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
    // UPDATE DEPARTMENT FORM
    // =========================================================

    $('#editDepartmentForm').on(
        'submit',
        function (event) {

            event.preventDefault();


            clearEditErrors();


            if (!validateEditForm()) {

                return;
            }


            if (
                $('#updateDepartmentBtn')
                    .prop('disabled')
            ) {

                return;
            }


            Swal.fire({

                title:
                    'Update Department?',

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


                    updateDepartment();

                }
            );

        }
    );


    // =========================================================
    // UPDATE DEPARTMENT AJAX
    // =========================================================

    function updateDepartment() {

        const form =
            $('#editDepartmentForm');


        setUpdateLoading(true);


        const formData =
            form.serialize() +
            '&action=update';
        
            console.log(formData);
            


        $.ajax({

            url: AJAX_URL,

            type: 'POST',

            data: formData,

            dataType: 'json',


            success: function (response) {

                console.log(
                    'Update department:',
                    response
                );


                if (
                    response &&
                    response.success
                ) {

                    showEditMessage(

                        response.message ||
                        'Department updated successfully.',

                        'success'

                    );


                    Swal.fire({

                        icon: 'success',

                        title:
                            'Updated Successfully',

                        text:
                            response.message ||
                            'Department details have been updated.',

                        timer: 1500,

                        timerProgressBar: true,

                        showConfirmButton: false

                    });


                    loadDepartments(
                        currentDepartmentPage
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
                    'Unable to update department.';


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
                    'Update Department AJAX Error:',
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
                    'Something went wrong while updating the department.';


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
        '.deleteDepartmentBtn',
        function (event) {

            event.preventDefault();

            event.stopPropagation();


            const button =
                $(this);


            const departmentId =
                $.trim(
                    button.attr('data-id') || ''
                );


            const departmentName =
                $.trim(
                    button.attr('data-name') ||
                    'this department'
                );


            console.log(
                'Delete Department:',
                departmentId,
                departmentName
            );


            if (!departmentId) {

                Swal.fire({

                    icon: 'error',

                    title:
                        'Invalid Department',

                    text:
                        'Department ID is missing.'

                });

                return;
            }


            selectedDepartmentId =
                departmentId;


            const modalElement =
                document.getElementById(
                    'deleteDepartmentModal'
                );


            if (!modalElement) {

                Swal.fire({

                    icon: 'error',

                    title:
                        'Modal Error',

                    text:
                        'Delete department modal was not found.'

                });

                return;
            }


            $('#deleteDepartmentName')
                .text(
                    departmentName
                );


            $('#delete_department_id')
                .val(
                    departmentId
                );


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
        '#confirmDeleteDepartmentBtn',
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


            const departmentId =
                $.trim(
                    selectedDepartmentId || ''
                );


            if (!departmentId) {

                Swal.fire({

                    icon: 'error',

                    title:
                        'Invalid Department',

                    text:
                        'Department ID is missing.'

                });

                return;
            }


            setDeleteLoading(true);


            $.ajax({

                url: AJAX_URL,

                type: 'POST',

                data: {

                    action: 'delete',

                    id: departmentId

                },

                dataType: 'json',


                success: function (response) {

                    console.log(
                        'Delete department:',
                        response
                    );


                    if (
                        response &&
                        response.success
                    ) {

                        Swal.fire({

                            icon: 'success',

                            title:
                                'Department Deleted',

                            text:
                                response.message ||
                                'Department deleted successfully.',

                            timer: 1500,

                            timerProgressBar: true,

                            showConfirmButton: false

                        });


                        loadDepartments(
                            currentDepartmentPage
                        );


                        setTimeout(
                            function () {

                                closeDeleteModal();

                            },
                            500
                        );


                        return;
                    }


                    const message =
                        response?.message ||
                        'Unable to delete department.';


                    Swal.fire({

                        icon: 'error',

                        title:
                            'Delete Failed',

                        text: message

                    });

                },


                error: function (xhr) {

                    console.error(
                        'Delete Department AJAX Error:',
                        xhr.responseText
                    );


                    const response =
                        xhr.responseJSON;


                    const message =
                        response?.message ||
                        'Unable to delete department. Please try again.';


                    Swal.fire({

                        icon: 'error',

                        title:
                            'Delete Failed',

                        text: message

                    });

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

        const department =
            $.trim(
                $('#' + prefix + 'department_name')
                    .val() || ''
            );


        const status =
            $.trim(
                $('#' + prefix + 'department_status')
                    .val() || ''
            );


        // -----------------------------------------
        // DEPARTMENT NAME
        // -----------------------------------------

        if (!department) {

            showError(

                'department_name',

                'Department name is required.'

            );

            isValid = false;

        } else if (
            department.length < 2
        ) {

            showError(

                'department_name',

                'Department name must contain at least 2 characters.'

            );

            isValid = false;

        } else if (
            department.length > 100
        ) {

            showError(

                'department_name',

                'Department name cannot exceed 100 characters.'

            );

            isValid = false;

        } else if (
            !/^[a-zA-Z0-9\s&'().-]+$/.test(
                department
            )
        ) {

            showError(

                'department_name',

                'Department name contains invalid characters.'

            );

            isValid = false;

        }


        // -----------------------------------------
        // STATUS
        // -----------------------------------------

        if (!status) {

            showError(

                'department_status',

                'Status is required.'

            );

            isValid = false;

        } else if (
            !['active', 'inactive'].includes(status)
        ) {

            showError(

                'department_status',

                'Please select a valid status.'

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

        $('#departmentForm')

            .find('[id$="Error"]')

            .text('');


        $('#departmentFormMessage')

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

        $('#editDepartmentForm')

            .find('[id$="Error"]')

            .text('');


        $('#editDepartmentFormMessage')

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

        $('#departmentFormMessage')

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

        $('#editDepartmentFormMessage')

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
            $('#saveDepartmentBtn');


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
                        class="bi bi-plus-lg me-1"
                    ></i>

                    Add Department

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
            $('#updateDepartmentBtn');


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


            $('#editDepartmentModal')

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
                        class="bi bi-check2-circle me-1"
                    ></i>

                    Update Department

                    `

                );


            $('#editDepartmentModal')

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
            $('#confirmDeleteDepartmentBtn');


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


            $('#deleteDepartmentModal')

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
                        class="bi bi-trash3 me-1"
                    ></i>

                    Delete Department

                    `

                );


            $('#deleteDepartmentModal')

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
                'departmentModal'
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
            $('#departmentForm')[0];


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
                'editDepartmentModal'
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
                'deleteDepartmentModal'
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


        selectedDepartmentId = null;


        $('#delete_department_id')
            .val('');


        $('#deleteDepartmentName')
            .text('');


        setDeleteLoading(false);

    }


    // =========================================================
    // CREATE MODAL SHOW
    // =========================================================

    $('#departmentModal').on(
        'show.bs.modal',
        function () {

            const form =
                $('#departmentForm')[0];


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

    $('#editDepartmentModal').on(
        'show.bs.modal',
        function () {

            clearEditErrors();

        }
    );


    // =========================================================
    // EDIT MODAL HIDDEN
    // =========================================================

    $('#editDepartmentModal').on(
        'hidden.bs.modal',
        function () {

            const form =
                $('#editDepartmentForm')[0];


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

    $('#deleteDepartmentModal').on(
        'hidden.bs.modal',
        function () {

            $('#delete_department_id')
                .val('');


            $('#deleteDepartmentName')
                .text('');


            selectedDepartmentId = null;


            setDeleteLoading(false);

        }
    );


    // =========================================================
    // DISPLAY DEPARTMENTS
    // =========================================================

    function displayDepartments(
        departments,
        pagination = null
    ) {

        const tbody =
            $('#departmentTableBody');


        tbody.empty();


        // -----------------------------------------
        // NO DEPARTMENTS
        // -----------------------------------------

        if (
            !departments ||
            departments.length === 0
        ) {

            tbody.html(`

                <tr>

                    <td
                        colspan="4"
                        class="text-center text-muted py-5"
                    >

                        <i
                            class="bi bi-building fs-2 d-block mb-2"
                        ></i>

                        No departments found.

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
                ) || departmentsPerPage
                : departmentsPerPage;


        const pageOffset =
            (page - 1) * perPage;


        // -----------------------------------------
        // DEPARTMENT ROWS
        // -----------------------------------------

        departments.forEach(
            function (
                department,
                index
            ) {

                const rowNumber =
                    pageOffset + index + 1;


                // -----------------------------------------
                // STATUS
                // -----------------------------------------

                let statusBadge;


                if (
                    department.status === 'active'
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
                // DEPARTMENT NAME
                // -----------------------------------------

                const departmentName =
                    department.name ||
                    department.department ||
                    '';


                // -----------------------------------------
                // ROW
                // -----------------------------------------

                tbody.append(`

                    <tr>

                        <!-- ID -->

                        <td>
                            ${rowNumber}
                        </td>


                        <!-- DEPARTMENT -->

                        <td>

                            <strong>
                                ${escapeHtml(
                                    departmentName
                                )}
                            </strong>

                        </td>


                        <!-- STATUS -->

                        <td>

                            ${statusBadge}

                        </td>


                        <!-- ACTIONS -->

                        <td>

                            <div
                                class="d-flex gap-2 justify-content-center"
                            >

                                <!-- EDIT -->

                                <button
                                    type="button"
                                    class="btn btn-sm btn-primary editDepartmentBtn"
                                    data-id="${escapeHtml(
                                        department.encrypted_id
                                    )}"
                                    title="Edit Department"
                                >

                                    <i
                                        class="bi bi-pencil"
                                    ></i>

                                    Edit

                                </button>


                                <!-- DELETE -->

                                <button
                                    type="button"
                                    class="btn btn-sm btn-danger deleteDepartmentBtn"
                                    data-id="${escapeHtml(
                                        department.encrypted_id
                                    )}"
                                    data-name="${escapeHtml(
                                        departmentName
                                    )}"
                                    title="Delete Department"
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


        $('#departmentTableBody').html(`

            <tr>

                <td
                    colspan="4"
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
                        id="reloadDepartmentsBtn"
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
    // RETRY LOAD DEPARTMENTS
    // =========================================================

    $(document).on(
        'click',
        '#reloadDepartmentsBtn',
        function () {

            loadDepartments(
                currentDepartmentPage
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

});
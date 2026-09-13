$(document).ready(function () {

    // =========================================================
    // CONFIGURATION
    // =========================================================

    const CONFIG = {

        ajaxUrl: '../ajax/designation.php',

        departmentAjaxUrl: '../ajax/department.php',

        perPage: 10,

        searchDelay: 400,

        selectors: {

            // -------------------------------------------------
            // Filters
            // -------------------------------------------------

            search: '#designationSearch',

            statusFilter: '#designationStatusFilter',

            departmentFilter: '#designationDepartmentFilter',

            resetFilters: '#resetDesignationFilters',


            // -------------------------------------------------
            // Table
            // -------------------------------------------------

            tableBody: '#designationTableBody',

            pagination: '#designationPagination',

            paginationInfo: '#designationPaginationInfo',

            count: '#designationCount',


            // -------------------------------------------------
            // Create
            // -------------------------------------------------

            createForm: '#designationForm',

            createModal: '#designationModal',

            saveButton: '#saveDesignationBtn',

            createMessage: '#designationFormMessage',

            createDepartment: '#designation_department',


            // -------------------------------------------------
            // Edit
            // -------------------------------------------------

            editForm: '#editDesignationForm',

            editModal: '#editDesignationModal',

            updateButton: '#updateDesignationBtn',

            editMessage: '#editDesignationFormMessage',

            editDepartment: '#edit_designation_department',


            // -------------------------------------------------
            // Delete
            // -------------------------------------------------

            deleteModal: '#deleteDesignationModal',

            confirmDeleteButton: '#confirmDeleteDesignationBtn',

            deleteId: '#delete_designation_id',

            deleteName: '#deleteDesignationName'

        }

    };


    // =========================================================
    // STATE
    // =========================================================

    const state = {

        currentPage: 1,

        searchTimer: null,

        selectedDesignationId: null,

        isLoadingDepartments: false,

        departments: []

    };


    // =========================================================
    // INITIALIZATION
    // =========================================================

    init();


    function init() {

        bindEvents();

        loadDepartments();

        loadDesignations(1);

    }


    // =========================================================
    // EVENT BINDINGS
    // =========================================================

    function bindEvents() {

        // -----------------------------------------------------
        // Search
        // -----------------------------------------------------

        $(CONFIG.selectors.search).on(
            'input',
            handleSearch
        );


        // -----------------------------------------------------
        // Status Filter
        // -----------------------------------------------------

        $(CONFIG.selectors.statusFilter).on(
            'change',
            function () {

                loadDesignations(1);

            }
        );


        // -----------------------------------------------------
        // Department Filter
        // -----------------------------------------------------

        $(CONFIG.selectors.departmentFilter).on(
            'change',
            function () {

                loadDesignations(1);

            }
        );


        // -----------------------------------------------------
        // Reset Filters
        // -----------------------------------------------------

        $(CONFIG.selectors.resetFilters).on(
            'click',
            resetFilters
        );


        // -----------------------------------------------------
        // Create
        // -----------------------------------------------------

        $(CONFIG.selectors.createForm).on(
            'submit',
            handleCreateSubmit
        );


        // -----------------------------------------------------
        // Edit
        // -----------------------------------------------------

        $(document).on(
            'click',
            '.editDesignationBtn',
            handleEditClick
        );


        $(CONFIG.selectors.editForm).on(
            'submit',
            handleUpdateSubmit
        );


        // -----------------------------------------------------
        // Delete
        // -----------------------------------------------------

        $(document).on(
            'click',
            '.deleteDesignationBtn',
            handleDeleteClick
        );


        $(document).on(
            'click',
            CONFIG.selectors.confirmDeleteButton,
            handleDeleteConfirm
        );


        // -----------------------------------------------------
        // Pagination
        // -----------------------------------------------------

        $(document).on(
            'click',
            '#designationPagination .page-link',
            handlePaginationClick
        );


        // -----------------------------------------------------
        // Retry
        // -----------------------------------------------------

        $(document).on(
            'click',
            '#reloadDesignationsBtn',
            function () {

                loadDesignations(
                    state.currentPage
                );

            }
        );


        // -----------------------------------------------------
        // Create Modal
        // -----------------------------------------------------

        $(CONFIG.selectors.createModal).on(
            'show.bs.modal',
            handleCreateModalShow
        );


        // -----------------------------------------------------
        // Edit Modal
        // -----------------------------------------------------

        $(CONFIG.selectors.editModal).on(
            'show.bs.modal',
            function () {

                clearEditErrors();

            }
        );


        $(CONFIG.selectors.editModal).on(
            'hidden.bs.modal',
            handleEditModalHidden
        );


        // -----------------------------------------------------
        // Delete Modal
        // -----------------------------------------------------

        $(CONFIG.selectors.deleteModal).on(
            'hidden.bs.modal',
            handleDeleteModalHidden
        );

    }


    // =========================================================
    // LOAD DEPARTMENTS
    // =========================================================

    function loadDepartments() {

        const departmentFilter =
            $(CONFIG.selectors.departmentFilter);

        const createDepartment =
            $(CONFIG.selectors.createDepartment);

        const editDepartment =
            $(CONFIG.selectors.editDepartment);


        if (state.isLoadingDepartments) {

            return;

        }


        if (
            !departmentFilter.length &&
            !createDepartment.length &&
            !editDepartment.length
        ) {

            return;

        }


        state.isLoadingDepartments = true;


        // -----------------------------------------------------
        // Disable Selects
        // -----------------------------------------------------

        departmentFilter.prop(
            'disabled',
            true
        );

        createDepartment.prop(
            'disabled',
            true
        );

        editDepartment.prop(
            'disabled',
            true
        );


        // -----------------------------------------------------
        // Loading Options
        // -----------------------------------------------------

        departmentFilter.html(`
            <option value="">
                Loading Departments...
            </option>
        `);

        createDepartment.html(`
            <option value="">
                Loading Departments...
            </option>
        `);

        editDepartment.html(`
            <option value="">
                Loading Departments...
            </option>
        `);


        // -----------------------------------------------------
        // AJAX
        // -----------------------------------------------------

        $.ajax({

            url: CONFIG.departmentAjaxUrl,

            type: 'GET',

            data: {

                action: 'index'

            },

            dataType: 'json',

            success: function (response) {



                if (
                    !response ||
                    !response.success
                ) {

                    console.error(
                        'Unable to load departments:',
                        response?.message
                    );

                    state.departments = [];

                    showDepartmentDefaultOptions();

                    return;

                }


                const departments =
                    Array.isArray(
                        response.departments
                    )
                        ? response.departments
                        : [];


                state.departments =
                    departments;


                renderDepartmentSelects(
                    departments
                );

            },


            error: function (xhr) {

                console.error(
                    'Department AJAX Error:',
                    xhr.responseText
                );

                state.departments = [];

                showDepartmentDefaultOptions();

            },


            complete: function () {

                state.isLoadingDepartments = false;


                departmentFilter.prop(
                    'disabled',
                    false
                );

                createDepartment.prop(
                    'disabled',
                    false
                );

                editDepartment.prop(
                    'disabled',
                    false
                );

            }

        });

    }


    // =========================================================
    // RENDER DEPARTMENT SELECTS
    // =========================================================

    function renderDepartmentSelects(
        departments
    ) {

        const departmentFilter =
            $(CONFIG.selectors.departmentFilter);

        const createDepartment =
            $(CONFIG.selectors.createDepartment);

        const editDepartment =
            $(CONFIG.selectors.editDepartment);


        departmentFilter.empty();

        createDepartment.empty();

        editDepartment.empty();


        // -----------------------------------------------------
        // Default Options
        // -----------------------------------------------------

        departmentFilter.append(`
            <option value="">
                All Departments
            </option>
        `);

        createDepartment.append(`
            <option value="">
                Select Department
            </option>
        `);

        editDepartment.append(`
            <option value="">
                Select Department
            </option>
        `);


        // -----------------------------------------------------
        // No Departments
        // -----------------------------------------------------

        if (
            !Array.isArray(departments) ||
            departments.length === 0
        ) {

            return;

        }


        // -----------------------------------------------------
        // Department Options
        // -----------------------------------------------------

        departments.forEach(
            function (department) {

                /*
                |--------------------------------------------------------------------------
                | RAW DEPARTMENT ID
                |--------------------------------------------------------------------------
                |
                | We intentionally use department.id.
                |
                */

                const id =
                    department.id || '';


                const name =
                    department.name ||
                    department.department ||
                    '';


                if (!id || !name) {

                    return;

                }


                // -------------------------------------------------
                // Filter
                // -------------------------------------------------

                departmentFilter.append(`
                    <option value="${escapeHtml(id)}">
                        ${escapeHtml(name)}
                    </option>
                `);


                // -------------------------------------------------
                // Create
                // -------------------------------------------------

                createDepartment.append(`
                    <option value="${escapeHtml(id)}">
                        ${escapeHtml(name)}
                    </option>
                `);


                // -------------------------------------------------
                // Edit
                // -------------------------------------------------

                editDepartment.append(`
                    <option value="${escapeHtml(id)}">
                        ${escapeHtml(name)}
                    </option>
                `);

            }
        );

    }


    // =========================================================
    // DEFAULT DEPARTMENT OPTIONS
    // =========================================================

    function showDepartmentDefaultOptions() {

        const departmentFilter =
            $(CONFIG.selectors.departmentFilter);

        const createDepartment =
            $(CONFIG.selectors.createDepartment);

        const editDepartment =
            $(CONFIG.selectors.editDepartment);


        departmentFilter.html(`
            <option value="">
                All Departments
            </option>
        `);

        createDepartment.html(`
            <option value="">
                Select Department
            </option>
        `);

        editDepartment.html(`
            <option value="">
                Select Department
            </option>
        `);

    }


    // =========================================================
    // LOAD DESIGNATIONS
    // =========================================================

    function loadDesignations(
        page = 1
    ) {

        state.currentPage = page;


        const params = {

            action: 'index',

            search: getSearchValue(),

            status: getStatusFilter(),

            department_id: getDepartmentFilter(),

            page: page,

            per_page: CONFIG.perPage

        };


        showTableLoader();


        $.ajax({

            url: CONFIG.ajaxUrl,

            type: 'GET',

            data: params,

            dataType: 'json',

            success: function (response) {

                // console.log(
                //     'Designations:',
                //     response
                // );


                if (
                    !response ||
                    !response.success
                ) {

                    handleLoadFailure(
                        response?.message ||
                        'Unable to load designations.'
                    );

                    return;

                }


                const designations =
                    Array.isArray(
                        response.designations
                    )
                        ? response.designations
                        : [];


                const pagination =
                    response.pagination || null;


                displayDesignations(
                    designations,
                    pagination
                );


                renderPagination(
                    pagination
                );


                updateDesignationCount(
                    designations,
                    pagination
                );

            },


            error: function (xhr) {

                console.error(
                    'Load Designations AJAX Error:',
                    xhr.responseText
                );


                handleAjaxError(
                    xhr,
                    'Unable to load designations.',
                    true
                );

            }

        });

    }


    // =========================================================
    // GET SEARCH VALUE
    // =========================================================

    function getSearchValue() {

        return $.trim(
            $(CONFIG.selectors.search).val() || ''
        );

    }


    // =========================================================
    // GET STATUS FILTER
    // =========================================================

    function getStatusFilter() {

        return (
            $(CONFIG.selectors.statusFilter).val() ||
            ''
        );

    }


    // =========================================================
    // GET DEPARTMENT FILTER
    // =========================================================

    function getDepartmentFilter() {

        /*
        |--------------------------------------------------------------------------
        | RAW DEPARTMENT ID
        |--------------------------------------------------------------------------
        */

        return (
            $(CONFIG.selectors.departmentFilter).val() ||
            ''
        );

    }


    // =========================================================
    // SEARCH
    // =========================================================

    function handleSearch() {

        clearTimeout(
            state.searchTimer
        );


        state.searchTimer = setTimeout(
            function () {

                loadDesignations(1);

            },
            CONFIG.searchDelay
        );

    }


    // =========================================================
    // RESET FILTERS
    // =========================================================

    function resetFilters() {

        $(CONFIG.selectors.search)
            .val('');

        $(CONFIG.selectors.statusFilter)
            .val('');

        $(CONFIG.selectors.departmentFilter)
            .val('');


        loadDesignations(1);

    }


    // =========================================================
    // PAGINATION
    // =========================================================

    function renderPagination(
        pagination
    ) {

        const container =
            $(CONFIG.selectors.pagination);


        container.empty();


        if (!pagination) {

            $(CONFIG.selectors.paginationInfo)
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
            ) || CONFIG.perPage;


        if (
            total === 0 ||
            totalPages === 0
        ) {

            $(CONFIG.selectors.paginationInfo)
                .text(
                    'No designations found.'
                );

            return;

        }


        const start =
            ((currentPage - 1) * perPage) + 1;


        const end =
            Math.min(
                start + perPage - 1,
                total
            );


        $(CONFIG.selectors.paginationInfo)
            .text(
                `Showing ${start}-${end} of ${total}`
            );


        // -----------------------------------------------------
        // Previous
        // -----------------------------------------------------

        appendPaginationButton({

            page: currentPage - 1,

            text: 'Previous',

            disabled: currentPage <= 1

        });


        // -----------------------------------------------------
        // Page Numbers
        // -----------------------------------------------------

        const pages =
            getPaginationPages(
                currentPage,
                totalPages
            );


        pages.forEach(
            function (page) {

                if (page === '...') {

                    container.append(`

                        <li class="page-item disabled">

                            <span class="page-link">
                                ...
                            </span>

                        </li>

                    `);

                    return;

                }


                container.append(`

                    <li class="page-item ${
                        page === currentPage
                            ? 'active'
                            : ''
                    }">

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
        );


        // -----------------------------------------------------
        // Next
        // -----------------------------------------------------

        appendPaginationButton({

            page: currentPage + 1,

            text: 'Next',

            disabled: currentPage >= totalPages

        });

    }


    // =========================================================
    // PAGINATION BUTTON
    // =========================================================

    function appendPaginationButton(
        options
    ) {

        const container =
            $(CONFIG.selectors.pagination);


        container.append(`

            <li class="page-item ${
                options.disabled
                    ? 'disabled'
                    : ''
            }">

                <button
                    type="button"
                    class="page-link"
                    data-page="${options.page}"
                    ${
                        options.disabled
                            ? 'disabled'
                            : ''
                    }
                >
                    ${escapeHtml(options.text)}
                </button>

            </li>

        `);

    }


    // =========================================================
    // PAGINATION PAGE RANGE
    // =========================================================

    function getPaginationPages(
        currentPage,
        totalPages
    ) {

        if (totalPages <= 7) {

            return Array.from(
                {
                    length: totalPages
                },
                function (_, index) {

                    return index + 1;

                }
            );

        }


        const pages = [];


        pages.push(1);


        if (currentPage > 4) {

            pages.push('...');

        }


        const start =
            Math.max(
                2,
                currentPage - 1
            );


        const end =
            Math.min(
                totalPages - 1,
                currentPage + 1
            );


        for (
            let page = start;
            page <= end;
            page++
        ) {

            pages.push(page);

        }


        if (
            currentPage <
            totalPages - 3
        ) {

            pages.push('...');

        }


        pages.push(totalPages);


        return pages;

    }


    // =========================================================
    // PAGINATION CLICK
    // =========================================================

    function handlePaginationClick(
        event
    ) {

        event.preventDefault();


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


        if (
            page === state.currentPage
        ) {

            return;

        }


        loadDesignations(page);

    }


    // =========================================================
    // UPDATE DESIGNATION COUNT
    // =========================================================

    function updateDesignationCount(
        designations,
        pagination
    ) {

        const total =
            pagination
                ? parseInt(
                    pagination.total,
                    10
                ) || 0
                : designations.length;


        $(CONFIG.selectors.count)
            .text(
                `${total} Designation${
                    total !== 1
                        ? 's'
                        : ''
                }`
            );

    }


    // =========================================================
    // DISPLAY DESIGNATIONS
    // =========================================================

    function displayDesignations(
        designations,
        pagination = null
    ) {

        const tbody =
            $(CONFIG.selectors.tableBody);


        tbody.empty();


        if (
            !Array.isArray(designations) ||
            designations.length === 0
        ) {

            showEmptyTable();

            return;

        }


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
                ) || CONFIG.perPage
                : CONFIG.perPage;


        const offset =
            (page - 1) * perPage;


        designations.forEach(
            function (
                designation,
                index
            ) {

                const rowNumber =
                    offset + index + 1;


                tbody.append(
                    buildDesignationRow(
                        designation,
                        rowNumber
                    )
                );

            }
        );

    }


    // =========================================================
    // BUILD DESIGNATION ROW
    // =========================================================

    function buildDesignationRow(
        designation,
        rowNumber
    ) {

        const designationName =
            designation.name ||
            designation.designation ||
            '';


        const departmentName =
            designation.department_name ||
            designation.department ||
            'N/A';


        const encryptedId =
            designation.encrypted_id ||
            '';


        const statusBadge =
            buildStatusBadge(
                designation.status
            );


        return `

            <tr>

                <td>
                    ${rowNumber}
                </td>


                <td>

                    <strong>
                        ${escapeHtml(
                            designationName
                        )}
                    </strong>

                </td>


                <td>

                    ${escapeHtml(
                        departmentName
                    )}

                </td>


                <td>

                    ${statusBadge}

                </td>


                <td>

                    <div
                        class="d-flex gap-2 justify-content-center"
                    >

                        <button
                            type="button"
                            class="btn btn-sm btn-primary editDesignationBtn"
                            data-id="${escapeHtml(
                                encryptedId
                            )}"
                            title="Edit Designation"
                        >

                            <i
                                class="bi bi-pencil"
                            ></i>

                            Edit

                        </button>


                        <button
                            type="button"
                            class="btn btn-sm btn-danger deleteDesignationBtn"
                            data-id="${escapeHtml(
                                encryptedId
                            )}"
                            data-name="${escapeHtml(
                                designationName
                            )}"
                            title="Delete Designation"
                        >

                            <i
                                class="bi bi-trash"
                            ></i>

                            Delete

                        </button>

                    </div>

                </td>

            </tr>

        `;

    }


    // =========================================================
    // STATUS BADGE
    // =========================================================

    function buildStatusBadge(
        status
    ) {

        if (status === 'active') {

            return `

                <span class="badge bg-success">
                    Active
                </span>

            `;

        }


        return `

            <span class="badge bg-secondary">
                Inactive
            </span>

        `;

    }


    // =========================================================
    // CREATE DESIGNATION
    // =========================================================

    function handleCreateSubmit(
        event
    ) {

        event.preventDefault();


        clearErrors();


        if (!validateForm()) {

            return;

        }


        const button =
            $(CONFIG.selectors.saveButton);


        if (button.prop('disabled')) {

            return;

        }


        Swal.fire({

            title: 'Create Designation?',

            text:
                'Are you sure you want to create this designation?',

            icon: 'question',

            showCancelButton: true,

            confirmButtonText: 'Yes, Create',

            cancelButtonText: 'Cancel',

            reverseButtons: true,

            focusCancel: true

        }).then(
            function (result) {

                if (!result.isConfirmed) {

                    return;

                }


                createDesignation();

            }
        );

    }


    // =========================================================
    // CREATE AJAX
    // =========================================================

    function createDesignation() {

        setCreateLoading(true);


        const formData =
            $(CONFIG.selectors.createForm)
                .serialize() +
            '&action=store';

        console.log(formData);
        

        $.ajax({

            url: CONFIG.ajaxUrl,

            type: 'POST',

            data: formData,

            dataType: 'json',

            success: function (response) {

                if (
                    response &&
                    response.success
                ) {

                    Swal.fire({

                        icon: 'success',

                        title:
                            'Designation Created',

                        text:
                            response.message ||
                            'Designation created successfully.',

                        timer: 1800,

                        timerProgressBar: true,

                        showConfirmButton: false

                    }).then(
                        function () {

                            closeCreateModal();

                        }
                    );


                    loadDesignations(1);

                    return;

                }


                const message =
                    response?.message ||
                    'Unable to create designation.';


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
                    'Create Designation AJAX Error:',
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
                    'Unable to create designation.'
                );

            },


            complete: function () {

                setCreateLoading(false);

            }

        });

    }


    // =========================================================
    // EDIT DESIGNATION
    // =========================================================

    function handleEditClick(
        event
    ) {

        event.preventDefault();


        const button =
            $(this);


        const designationId =
            $.trim(
                button.attr('data-id') || ''
            );


        if (!designationId) {

            showAlert(
                'error',
                'Invalid Designation',
                'Designation ID is missing.'
            );

            return;

        }


        if (button.prop('disabled')) {

            return;

        }


        setButtonLoading(
            button,
            'Loading...'
        );


        $.ajax({

            url: CONFIG.ajaxUrl,

            type: 'GET',

            data: {

                action: 'show',

                id: designationId

            },

            dataType: 'json',

            success: function (response) {

                if (
                    !response ||
                    !response.success
                ) {

                    showAlert(
                        'error',
                        'Unable to Load Designation',
                        response?.message ||
                        'Designation not found.'
                    );

                    return;

                }


                const designation =
                    response.designation;


                if (!designation) {

                    showAlert(
                        'error',
                        'Designation Not Found',
                        'Designation information could not be loaded.'
                    );

                    return;

                }


                /*
                |--------------------------------------------------------------------------
                | Make Sure Departments Are Loaded
                |--------------------------------------------------------------------------
                */

                if (
                    !$(CONFIG.selectors.editDepartment)
                        .find('option')
                        .length
                ) {

                    renderDepartmentSelects(
                        state.departments
                    );

                }


                /*
                |--------------------------------------------------------------------------
                | Populate Form
                |--------------------------------------------------------------------------
                */

                populateEditForm(
                    designation
                );


                clearEditErrors();


                openEditModal();

            },


            error: function (xhr) {

                console.error(
                    'Edit Designation AJAX Error:',
                    xhr.responseText
                );


                const response =
                    xhr.responseJSON;


                showAlert(
                    'error',
                    'Unable to Load Designation',
                    response?.message ||
                    'Unable to load designation. Please try again.'
                );

            },


            complete: function () {

                resetButtonLoading(
                    button
                );

            }

        });

    }


    // =========================================================
    // POPULATE EDIT FORM
    // =========================================================

    function populateEditForm(
        designation
    ) {

        // -----------------------------------------------------
        // Designation ID
        // -----------------------------------------------------

        $(CONFIG.selectors.editForm)
            .find('#edit_designation_id')
            .val(
                designation.encrypted_id ||
                designation.id ||
                ''
            );


        // -----------------------------------------------------
        // Designation Name
        // -----------------------------------------------------

        $(CONFIG.selectors.editForm)
            .find('#edit_designation_name')
            .val(
                designation.name ||
                designation.designation ||
                ''
            );


        // -----------------------------------------------------
        // Department
        // -----------------------------------------------------

        const departmentSelect =
            $(CONFIG.selectors.editDepartment);


        /*
        |--------------------------------------------------------------------------
        | RAW DEPARTMENT ID
        |--------------------------------------------------------------------------
        |
        | Backend response:
        |
        | department_id: 3
        |
        | Dropdown option:
        |
        | <option value="3">Finance</option>
        |
        */

        const departmentId =
            String(
                designation.department_id || ''
            ).trim();







        // -----------------------------------------------------
        // Set Department
        // -----------------------------------------------------

        if (departmentId) {

            departmentSelect.val(
                departmentId
            );


            /*
            |--------------------------------------------------------------------------
            | Verify Selection
            |--------------------------------------------------------------------------
            */

            if (
                departmentSelect.val() !==
                departmentId
            ) {

                console.warn(
                    'Department option not found.',
                    {
                        departmentId:
                            departmentId,

                        availableOptions:
                            departmentSelect
                                .find('option')
                                .map(function () {

                                    return $(this).val();

                                })
                                .get()
                    }
                );

            } else {

 
            }

        } else {

            departmentSelect.val('');

        }


        // -----------------------------------------------------
        // Status
        // -----------------------------------------------------

        $(CONFIG.selectors.editForm)
            .find('#edit_designation_status')
            .val(
                designation.status || ''
            );

    }


    // =========================================================
    // OPEN EDIT MODAL
    // =========================================================

    function openEditModal() {

        const modalElement =
            document.getElementById(
                'editDesignationModal'
            );


        if (!modalElement) {

            showAlert(
                'error',
                'Modal Error',
                'Edit designation modal was not found.'
            );

            return;

        }


        const modal =
            bootstrap.Modal.getOrCreateInstance(
                modalElement
            );


        modal.show();

    }


    // =========================================================
    // UPDATE DESIGNATION
    // =========================================================

    function handleUpdateSubmit(
        event
    ) {

        event.preventDefault();


        clearEditErrors();


        if (!validateEditForm()) {

            return;

        }


        const button =
            $(CONFIG.selectors.updateButton);


        if (button.prop('disabled')) {

            return;

        }


        Swal.fire({

            title: 'Update Designation?',

            text:
                'Are you sure you want to save these changes?',

            icon: 'question',

            showCancelButton: true,

            confirmButtonText: 'Yes, Update',

            cancelButtonText: 'Cancel',

            reverseButtons: true,

            focusCancel: true

        }).then(
            function (result) {

                if (!result.isConfirmed) {

                    return;

                }


                updateDesignation();

            }
        );

    }


    // =========================================================
    // UPDATE AJAX
    // =========================================================

    function updateDesignation() {

        setUpdateLoading(true);


        const formData =
            $(CONFIG.selectors.editForm)
                .serialize() +
            '&action=update';
            




        $.ajax({

            url: CONFIG.ajaxUrl,

            type: 'POST',

            data: formData,

            dataType: 'json',

            success: function (response) {

                if (
                    response &&
                    response.success
                ) {

                    showEditMessage(
                        response.message ||
                        'Designation updated successfully.',
                        'success'
                    );


                    Swal.fire({

                        icon: 'success',

                        title:
                            'Updated Successfully',

                        text:
                            response.message ||
                            'Designation details have been updated.',

                        timer: 1500,

                        timerProgressBar: true,

                        showConfirmButton: false

                    });


                    loadDesignations(
                        state.currentPage
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
                    'Unable to update designation.';


                showEditMessage(
                    message,
                    'danger'
                );


                showAlert(
                    'error',
                    'Update Failed',
                    message
                );

            },


            error: function (xhr) {

                console.error(
                    'Update Designation AJAX Error:',
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
                    'Something went wrong while updating the designation.';


                showEditMessage(
                    message,
                    'danger'
                );


                showAlert(
                    'error',
                    'Server Error',
                    message
                );

            },


            complete: function () {

                setUpdateLoading(false);

            }

        });

    }


    // =========================================================
    // DELETE DESIGNATION
    // =========================================================

    function handleDeleteClick(
        event
    ) {

        event.preventDefault();

        event.stopPropagation();


        const button =
            $(this);


        const designationId =
            $.trim(
                button.attr('data-id') || ''
            );


        const designationName =
            $.trim(
                button.attr('data-name') ||
                'this designation'
            );


        if (!designationId) {

            showAlert(
                'error',
                'Invalid Designation',
                'Designation ID is missing.'
            );

            return;

        }


        state.selectedDesignationId =
            designationId;


        const modalElement =
            document.getElementById(
                'deleteDesignationModal'
            );


        if (!modalElement) {

            showAlert(
                'error',
                'Modal Error',
                'Delete designation modal was not found.'
            );

            return;

        }


        $(CONFIG.selectors.deleteName)
            .text(
                designationName
            );


        $(CONFIG.selectors.deleteId)
            .val(
                designationId
            );


        setDeleteLoading(false);


        const modal =
            bootstrap.Modal.getOrCreateInstance(
                modalElement
            );


        modal.show();

    }


    // =========================================================
    // CONFIRM DELETE
    // =========================================================

    function handleDeleteConfirm(
        event
    ) {

        event.preventDefault();

        event.stopPropagation();


        const button =
            $(this);


        if (button.prop('disabled')) {

            return;

        }


        const designationId =
            $.trim(
                state.selectedDesignationId ||
                $(CONFIG.selectors.deleteId).val() ||
                ''
            );


        if (!designationId) {

            showAlert(
                'error',
                'Invalid Designation',
                'Designation ID is missing.'
            );

            return;

        }


        setDeleteLoading(true);


        $.ajax({

            url: CONFIG.ajaxUrl,

            type: 'POST',

            data: {

                action: 'delete',

                id: designationId

            },

            dataType: 'json',

            success: function (response) {

                console.log(
                    'Delete designation:',
                    response
                );


                if (
                    response &&
                    response.success
                ) {

                    Swal.fire({

                        icon: 'success',

                        title:
                            'Designation Deleted',

                        text:
                            response.message ||
                            'Designation deleted successfully.',

                        timer: 1500,

                        timerProgressBar: true,

                        showConfirmButton: false

                    });


                    loadDesignations(
                        state.currentPage
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
                    'Unable to delete designation.';


                showAlert(
                    'error',
                    'Delete Failed',
                    message
                );

            },


            error: function (xhr) {

                console.error(
                    'Delete Designation AJAX Error:',
                    xhr.responseText
                );


                const response =
                    xhr.responseJSON;


                const message =
                    response?.message ||
                    'Unable to delete designation. Please try again.';


                showAlert(
                    'error',
                    'Delete Failed',
                    message
                );

            },


            complete: function () {

                setDeleteLoading(false);

            }

        });

    }


    // =========================================================
    // CREATE FORM VALIDATION
    // =========================================================

    function validateForm() {

        return validateFields({

            prefix: '',

            showError: showFieldError

        });

    }


    // =========================================================
    // EDIT FORM VALIDATION
    // =========================================================

    function validateEditForm() {

        return validateFields({

            prefix: 'edit_',

            showError: showEditFieldError

        });

    }


    // =========================================================
    // COMMON VALIDATION
    // =========================================================

    function validateFields(
        options
    ) {

        const prefix =
            options.prefix || '';


        const showError =
            options.showError;


        let isValid = true;


        const designation =
            $.trim(
                $(
                    '#' +
                    prefix +
                    'designation_name'
                ).val() || ''
            );


        const department =
            $.trim(
                $(
                    '#' +
                    prefix +
                    'designation_department'
                ).val() || ''
            );


        const status =
            $.trim(
                $(
                    '#' +
                    prefix +
                    'designation_status'
                ).val() || ''
            );


        // -----------------------------------------------------
        // Designation Name
        // -----------------------------------------------------

        if (!designation) {

            showError(
                'designation_name',
                'Designation name is required.'
            );

            isValid = false;

        } else if (
            designation.length < 2
        ) {

            showError(
                'designation_name',
                'Designation name must contain at least 2 characters.'
            );

            isValid = false;

        } else if (
            designation.length > 100
        ) {

            showError(
                'designation_name',
                'Designation name cannot exceed 100 characters.'
            );

            isValid = false;

        } else if (
            !/^[a-zA-Z0-9\s&'().-]+$/.test(
                designation
            )
        ) {

            showError(
                'designation_name',
                'Designation name contains invalid characters.'
            );

            isValid = false;

        }


        // -----------------------------------------------------
        // Department
        // -----------------------------------------------------

        if (!department) {

            showError(
                'designation_department',
                'Department is required.'
            );

            isValid = false;

        }


        // -----------------------------------------------------
        // Status
        // -----------------------------------------------------

        if (!status) {

            showError(
                'designation_status',
                'Status is required.'
            );

            isValid = false;

        } else if (
            ![
                'active',
                'inactive'
            ].includes(status)
        ) {

            showError(
                'designation_status',
                'Please select a valid status.'
            );

            isValid = false;

        }


        // -----------------------------------------------------
        // Validation Alert
        // -----------------------------------------------------

        if (!isValid) {

            Swal.fire({

                icon: 'warning',

                title:
                    'Validation Failed',

                text:
                    'Please correct the highlighted fields.',

                confirmButtonText: 'OK'

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
    // SHOW CREATE BACKEND ERRORS
    // =========================================================

    function showErrors(
        errors
    ) {

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
    // SHOW EDIT BACKEND ERRORS
    // =========================================================

    function showEditErrors(
        errors
    ) {

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

        $(CONFIG.selectors.createForm)
            .find('[id$="Error"]')
            .text('');


        clearMessage(
            CONFIG.selectors.createMessage
        );

    }


    // =========================================================
    // CLEAR EDIT ERRORS
    // =========================================================

    function clearEditErrors() {

        $(CONFIG.selectors.editForm)
            .find('[id$="Error"]')
            .text('');


        clearMessage(
            CONFIG.selectors.editMessage
        );

    }


    // =========================================================
    // CLEAR MESSAGE
    // =========================================================

    function clearMessage(
        selector
    ) {

        $(selector)

            .stop(true, true)

            .removeClass(
                'alert-success alert-danger alert-warning'
            )

            .addClass('d-none')

            .text('');

    }


    // =========================================================
    // CREATE FORM MESSAGE
    // =========================================================

    function showFormMessage(
        message,
        type
    ) {

        showMessage(
            CONFIG.selectors.createMessage,
            message,
            type
        );

    }


    // =========================================================
    // EDIT FORM MESSAGE
    // =========================================================

    function showEditMessage(
        message,
        type
    ) {

        showMessage(
            CONFIG.selectors.editMessage,
            message,
            type
        );

    }


    // =========================================================
    // COMMON FORM MESSAGE
    // =========================================================

    function showMessage(
        selector,
        message,
        type
    ) {

        $(selector)

            .stop(true, true)

            .removeClass(
                'd-none alert-success alert-danger alert-warning'
            )

            .addClass(
                'alert-' + type
            )

            .hide()

            .text(
                message || ''
            )

            .fadeIn(200);

    }


    // =========================================================
    // CREATE LOADING
    // =========================================================

    function setCreateLoading(
        isLoading
    ) {

        const button =
            $(CONFIG.selectors.saveButton);


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

                    Add Designation

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
            $(CONFIG.selectors.updateButton);


        if (!button.length) {

            return;

        }


        if (isLoading) {

            storeOriginalButtonHtml(
                button
            );


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


            disableModalClose(
                CONFIG.selectors.editModal
            );

        } else {

            restoreButton(
                button,
                `

                    <i
                        class="bi bi-check2-circle me-1"
                    ></i>

                    Update Designation

                `
            );


            enableModalClose(
                CONFIG.selectors.editModal
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
            $(CONFIG.selectors.confirmDeleteButton);


        if (!button.length) {

            return;

        }


        if (isLoading) {

            storeOriginalButtonHtml(
                button
            );


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


            disableModalClose(
                CONFIG.selectors.deleteModal
            );

        } else {

            restoreButton(
                button,
                `

                    <i
                        class="bi bi-trash3 me-1"
                    ></i>

                    Delete Designation

                `
            );


            enableModalClose(
                CONFIG.selectors.deleteModal
            );

        }

    }


    // =========================================================
    // GENERIC BUTTON LOADING
    // =========================================================

    function setButtonLoading(
        button,
        text
    ) {

        storeOriginalButtonHtml(
            button
        );


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

                ${escapeHtml(text)}

            `);

    }


    // =========================================================
    // RESET BUTTON LOADING
    // =========================================================

    function resetButtonLoading(
        button
    ) {

        restoreButton(
            button
        );

    }


    // =========================================================
    // STORE ORIGINAL BUTTON HTML
    // =========================================================

    function storeOriginalButtonHtml(
        button
    ) {

        if (
            !button.data('original-html')
        ) {

            button.data(
                'original-html',
                button.html()
            );

        }

    }


    // =========================================================
    // RESTORE BUTTON
    // =========================================================

    function restoreButton(
        button,
        fallbackHtml = ''
    ) {

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
                ) || fallbackHtml
            );

    }


    // =========================================================
    // DISABLE MODAL CLOSE
    // =========================================================

    function disableModalClose(
        selector
    ) {

        $(selector)
            .find(
                '[data-bs-dismiss="modal"], .btn-close'
            )
            .prop(
                'disabled',
                true
            );

    }


    // =========================================================
    // ENABLE MODAL CLOSE
    // =========================================================

    function enableModalClose(
        selector
    ) {

        $(selector)
            .find(
                '[data-bs-dismiss="modal"], .btn-close'
            )
            .prop(
                'disabled',
                false
            );

    }


    // =========================================================
    // CREATE MODAL SHOW
    // =========================================================

    function handleCreateModalShow() {

        const form =
            $(CONFIG.selectors.createForm)[0];


        if (form) {

            form.reset();

        }


        clearErrors();


        $(CONFIG.selectors.createDepartment)
            .val('');


        setCreateLoading(false);

    }


    // =========================================================
    // EDIT MODAL HIDDEN
    // =========================================================

    function handleEditModalHidden() {

        const form =
            $(CONFIG.selectors.editForm)[0];


        if (form) {

            form.reset();

        }


        clearEditErrors();


        setUpdateLoading(false);

    }


    // =========================================================
    // DELETE MODAL HIDDEN
    // =========================================================

    function handleDeleteModalHidden() {

        $(CONFIG.selectors.deleteId)
            .val('');


        $(CONFIG.selectors.deleteName)
            .text('');


        state.selectedDesignationId =
            null;


        setDeleteLoading(false);

    }


    // =========================================================
    // CLOSE CREATE MODAL
    // =========================================================

    function closeCreateModal() {

        const modalElement =
            document.getElementById(
                'designationModal'
            );


        if (!modalElement) {

            return;

        }


        const modal =
            bootstrap.Modal.getInstance(
                modalElement
            );


        if (modal) {

            modal.hide();

        }


        const form =
            $(CONFIG.selectors.createForm)[0];


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
                'editDesignationModal'
            );


        if (!modalElement) {

            return;

        }


        const modal =
            bootstrap.Modal.getInstance(
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
                'deleteDesignationModal'
            );


        if (!modalElement) {

            return;

        }


        const modal =
            bootstrap.Modal.getInstance(
                modalElement
            );


        if (modal) {

            modal.hide();

        }


        state.selectedDesignationId =
            null;


        $(CONFIG.selectors.deleteId)
            .val('');


        $(CONFIG.selectors.deleteName)
            .text('');


        setDeleteLoading(false);

    }


    // =========================================================
    // TABLE LOADER
    // =========================================================

    function showTableLoader() {

        $(CONFIG.selectors.tableBody)
            .html(`

                <tr>

                    <td
                        colspan="5"
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
                            Loading designations...
                        </div>

                    </td>

                </tr>

            `);

    }


    // =========================================================
    // EMPTY TABLE
    // =========================================================

    function showEmptyTable() {

        $(CONFIG.selectors.tableBody)
            .html(`

                <tr>

                    <td
                        colspan="5"
                        class="text-center text-muted py-5"
                    >

                        <i
                            class="bi bi-person-badge fs-2 d-block mb-2"
                        ></i>

                        No designations found.

                    </td>

                </tr>

            `);

    }


    // =========================================================
    // TABLE MESSAGE
    // =========================================================

    function showTableMessage(
        message,
        type = 'danger'
    ) {

        let icon =
            'bi-exclamation-circle';


        if (type === 'danger') {

            icon =
                'bi-exclamation-triangle';

        }


        $(CONFIG.selectors.tableBody)
            .html(`

                <tr>

                    <td
                        colspan="5"
                        class="text-center py-5"
                    >

                        <i
                            class="bi ${icon} fs-2 text-danger d-block mb-2"
                        ></i>


                        <div
                            class="text-danger"
                        >

                            ${escapeHtml(
                                message
                            )}

                        </div>


                        <button
                            type="button"
                            class="btn btn-sm btn-outline-primary mt-3"
                            id="reloadDesignationsBtn"
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
    // LOAD FAILURE
    // =========================================================

    function handleLoadFailure(
        message
    ) {

        showTableMessage(
            message,
            'danger'
        );


        $(CONFIG.selectors.pagination)
            .empty();


        $(CONFIG.selectors.paginationInfo)
            .text('');


        $(CONFIG.selectors.count)
            .text(
                'Unable to load designations.'
            );

    }


    // =========================================================
    // GENERAL AJAX ERROR
    // =========================================================

    function handleAjaxError(
        xhr,
        defaultMessage,
        tableError = false
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


        showAlert(
            'error',
            'Something Went Wrong',
            message
        );


        if (tableError) {

            showTableMessage(
                message,
                'danger'
            );

        }

    }


    // =========================================================
    // SWEETALERT HELPER
    // =========================================================

    function showAlert(
        icon,
        title,
        text
    ) {

        Swal.fire({

            icon: icon,

            title: title,

            text: text,

            confirmButtonText: 'OK'

        });

    }


    // =========================================================
    // ESCAPE HTML
    // =========================================================

    function escapeHtml(
        value
    ) {

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

});
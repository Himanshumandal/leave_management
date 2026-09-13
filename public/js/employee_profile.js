/*
|--------------------------------------------------------------------------
| Employee Details Module
|--------------------------------------------------------------------------
*/

$(document).ready(function () {

    /*
    |--------------------------------------------------------------------------
    | Configuration
    |--------------------------------------------------------------------------
    */

    const CONFIG = {

        apiUrl:
            '../../ajax/employees.php',

        employeeId:
            Number(
                window.employeeId || 0
            ),

        selectors: {

            loading:
                '#employeeLoading',

            profile:
                '#employeeProfile',

            error:
                '#employeeError',

            errorMessage:
                '#employeeErrorMessage',

            avatar:
                '#profileAvatar',

            name:
                '#employeeName',

            designation:
                '#employeeDesignation',

            designationInfo:
                '#employeeDesignationInfo',

            employeeId:
                '#employeeId',

            email:
                '#employeeEmail',

            phone:
                '#employeePhone',

            department:
                '#employeeDepartment',

            departmentInfo:
                '#employeeDepartmentInfo',

            joiningDate:
                '#employeeJoiningDate',

            status:
                '#employeeStatus',

            experience:
                '#employeeExperience',

            experienceSummary:
                '#experienceSummary',

            attendance:
                '#attendanceCount',

            leave:
                '#leaveCount',

            editButton:
                '#editEmployeeBtn',

            attendanceTable:
                '#attendanceTable',

            leaveTable:
                '#leaveTable'
        }
    };


    /*
    |--------------------------------------------------------------------------
    | Cached DOM Elements
    |--------------------------------------------------------------------------
    */

    const DOM = {

        loading:
            $(CONFIG.selectors.loading),

        profile:
            $(CONFIG.selectors.profile),

        error:
            $(CONFIG.selectors.error),

        errorMessage:
            $(CONFIG.selectors.errorMessage),

        avatar:
            $(CONFIG.selectors.avatar),

        name:
            $(CONFIG.selectors.name),

        designation:
            $(CONFIG.selectors.designation),

        designationInfo:
            $(CONFIG.selectors.designationInfo),

        employeeId:
            $(CONFIG.selectors.employeeId),

        email:
            $(CONFIG.selectors.email),

        phone:
            $(CONFIG.selectors.phone),

        department:
            $(CONFIG.selectors.department),

        departmentInfo:
            $(CONFIG.selectors.departmentInfo),

        joiningDate:
            $(CONFIG.selectors.joiningDate),

        status:
            $(CONFIG.selectors.status),

        experience:
            $(CONFIG.selectors.experience),

        experienceSummary:
            $(CONFIG.selectors.experienceSummary),

        attendance:
            $(CONFIG.selectors.attendance),

        leave:
            $(CONFIG.selectors.leave),

        editButton:
            $(CONFIG.selectors.editButton),

        attendanceTable:
            $(CONFIG.selectors.attendanceTable),

        leaveTable:
            $(CONFIG.selectors.leaveTable)
    };


    /*
    |--------------------------------------------------------------------------
    | Initialize
    |--------------------------------------------------------------------------
    */

    init();


    /*
    |--------------------------------------------------------------------------
    | Initialize Application
    |--------------------------------------------------------------------------
    */

    function init() {

        hideError();

        setLoading(true);

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

        if (
            !CONFIG.employeeId ||
            !Number.isInteger(CONFIG.employeeId) ||
            CONFIG.employeeId <= 0
        ) {

            handleLoadError(
                'Invalid employee ID. Please return to the employee list.'
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

        console.log(
            'Loading employee:',
            CONFIG.employeeId
        );


        $.ajax({

            url:
                CONFIG.apiUrl,

            type:
                'GET',

            dataType:
                'json',

            data: {

                action:
                    'show',

                id:
                    CONFIG.employeeId
            },


            beforeSend:
                function () {

                    setLoading(true);

                    hideError();
                },


            success:
                function (response) {

                    console.log(
                        'Employee response:',
                        response
                    );


                    if (
                        !response ||
                        response.success !== true
                    ) {

                        handleLoadError(
                            response?.message ||
                            'Unable to load employee.'
                        );

                        return;
                    }


                    if (
                        !response.employee ||
                        !response.employee.id
                    ) {

                        handleLoadError(
                            'Invalid employee data received from server.'
                        );

                        return;
                    }


                    const employee =
                        response.employee;


                    const attendance =
                        response.attendance || {};


                    const leave =
                        response.leave || {};


                    /*
                    |--------------------------------------------------------------------------
                    | Render Employee
                    |--------------------------------------------------------------------------
                    */

                    renderEmployee(
                        employee
                    );


                    /*
                    |--------------------------------------------------------------------------
                    | Render Attendance
                    |--------------------------------------------------------------------------
                    */

                    renderAttendance(
                        attendance.recent || []
                    );


                    /*
                    |--------------------------------------------------------------------------
                    | Attendance Summary
                    |--------------------------------------------------------------------------
                    */

                    renderAttendanceSummary(
                        attendance.summary || {}
                    );


                    /*
                    |--------------------------------------------------------------------------
                    | Render Leaves
                    |--------------------------------------------------------------------------
                    */

                    renderLeaves(
                        leave.history || []
                    );


                    /*
                    |--------------------------------------------------------------------------
                    | Leave Summary
                    |--------------------------------------------------------------------------
                    */

                    renderLeaveSummary(
                        leave.summary || {}
                    );


                    /*
                    |--------------------------------------------------------------------------
                    | Show Profile
                    |--------------------------------------------------------------------------
                    */

                    DOM.profile
                        .removeClass('d-none');
                },


            error:
                function (xhr) {

                    console.error(
                        'Load employee error:',
                        xhr.responseText
                    );


                    handleLoadError(
                        getAjaxErrorMessage(xhr)
                    );
                },


            complete:
                function () {

                    setLoading(false);
                }

        });
    }


    /*
    |--------------------------------------------------------------------------
    | Render Employee
    |--------------------------------------------------------------------------
    */

    function renderEmployee(employee) {

        const firstName =
            cleanValue(
                employee.first_name
            );


        const lastName =
            cleanValue(
                employee.last_name
            );


        const fullName =
            `${firstName} ${lastName}`.trim()
            ||
            'Unknown Employee';


        /*
        |--------------------------------------------------------------------------
        | Basic Information
        |--------------------------------------------------------------------------
        */

        DOM.name.text(
            fullName
        );


        DOM.designation.text(
            displayValue(
                employee.designation
            )
        );


        DOM.designationInfo.text(
            displayValue(
                employee.designation
            )
        );


        DOM.employeeId.text(
            displayValue(
                employee.employee_id
            )
        );


        DOM.email.text(
            displayValue(
                employee.email
            )
        );


        DOM.phone.text(
            displayValue(
                employee.phone
            )
        );


        DOM.department.text(
            displayValue(
                employee.department
            )
        );


        DOM.departmentInfo.text(
            displayValue(
                employee.department
            )
        );


        DOM.joiningDate.text(
            formatDate(
                employee.joining_date
            )
        );


        /*
 |--------------------------------------------------------------------------
 | Avatar
 |--------------------------------------------------------------------------
 */

        const imagePath = cleanValue(
            employee.image_url
        );

        let imageUrl ='';
        if (imagePath) {

            /*
            |--------------------------------------------------------------------------
            | Cloudinary URL
            |--------------------------------------------------------------------------
            */

            if (
                imagePath.startsWith('http://') ||
                imagePath.startsWith('https://')
            ) {

                // Cloudinary URL
                imageUrl = imagePath;

            } else {

                /*
                |--------------------------------------------------------------------------
                | Local Image URL
                |--------------------------------------------------------------------------
                */

                imageUrl =
                    '/' +
                    imagePath.replace(/^\/+/, '');
            }
        }

        

        const canUploadImage =
            window.isEmployee === true;


        /*
        |--------------------------------------------------------------------------
        | Existing Image
        |--------------------------------------------------------------------------
        */

        if (imageUrl) {

            DOM.avatar.html(`

        <img
            src="${escapeHtml(imageUrl)}"
            alt="${escapeHtml(fullName)}"
            class="profile-avatar-image"
            onerror="this.style.display='none';"
        >

        ${canUploadImage
                    ?
                    `
                <!-- REMOVE BUTTON -->

                <button
                    type="button"
                    id="removeAvatarBtn"
                    class="avatar-upload-btn avatar-remove-btn"
                    title="Remove profile photo"
                >
                    <i class="bi bi-trash"></i>
                </button>

                <!-- File Input -->

                <input
                    type="file"
                    id="avatarInput"
                    accept="image/jpeg,image/png,image/webp"
                    hidden
                >
                `
                    :
                    ''
                }

    `);

        }


        /*
        |--------------------------------------------------------------------------
        | No Image
        |--------------------------------------------------------------------------
        */

        else {

            DOM.avatar.html(`

        <span
            id="avatarPlaceholder"
            class="avatar-initials"
        >
            ${escapeHtml(
                getInitials(
                    firstName,
                    lastName
                )
            )}
        </span>

        ${canUploadImage
                    ?
                    `
                <!-- UPLOAD BUTTON -->

                <button
                    type="button"
                    id="uploadAvatarBtn"
                    class="avatar-upload-btn"
                    title="Upload profile photo"
                >
                    <i class="bi bi-plus-lg"></i>
                </button>

                <!-- File Input -->

                <input
                    type="file"
                    id="avatarInput"
                    accept="image/jpeg,image/png,image/webp"
                    hidden
                >
                `
                    :
                    ''
                }

    `);
        }


        /*
        |--------------------------------------------------------------------------
        | Status
        |--------------------------------------------------------------------------
        */

        renderStatus(
            employee.status
        );


        /*
        |--------------------------------------------------------------------------
        | Experience
        |--------------------------------------------------------------------------
        */

        const experience =
            calculateExperience(
                employee.joining_date
            );


        DOM.experience.text(
            experience
        );


        DOM.experienceSummary.text(
            experience
        );


        /*
        |--------------------------------------------------------------------------
        | Edit Button
        |--------------------------------------------------------------------------
        */

        setupEditButton(
            employee.id
        );
    }

    /*
|--------------------------------------------------------------------------
| Profile Image Upload
|--------------------------------------------------------------------------
*/

    $(document).on('click', '#uploadAvatarBtn', function () {

        $('#avatarInput').click();

    });


    $(document).on('change', '#avatarInput', function () {

        const file = this.files[0];

        if (!file) {
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Validate File Type
        |--------------------------------------------------------------------------
        */

        const allowedTypes = [
            'image/jpeg',
            'image/png',
            'image/webp'
        ];

        if (!allowedTypes.includes(file.type)) {

            showAlert(
                'Invalid Image',
                'Only JPG, PNG and WEBP images are allowed.',
                'error'
            );

            this.value = '';

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | Validate File Size
        |--------------------------------------------------------------------------
        */

        if (file.size > 2 * 1024 * 1024) {

            showAlert(
                'Image Too Large',
                'Profile image must be less than 2 MB.',
                'error'
            );

            this.value = '';

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | Preview Image
        |--------------------------------------------------------------------------
        */

        const reader = new FileReader();

        reader.onload = function (event) {

            DOM.avatar.html(`
            <img
                src="${event.target.result}"
                alt="Profile Photo"
                class="profile-avatar-image"
            >

            <button
                type="button"
                id="uploadAvatarBtn"
                class="avatar-upload-btn"
                title="Change profile photo"
            >
                <i class="bi bi-plus"></i>
            </button>
        `);

        };

        reader.readAsDataURL(file);


        /*
        |--------------------------------------------------------------------------
        | Upload Image
        |--------------------------------------------------------------------------
        */

        uploadProfileImage(file);

    });


    function uploadProfileImage(file) {

        const formData = new FormData();

        formData.append(
            'image',
            file
        );

        formData.append(
            'action',
            'upload_image'
        );


        $.ajax({

            url: CONFIG.apiUrl,

            type: 'POST',

            data: formData,

            processData: false,

            contentType: false,

            dataType: 'json',


            beforeSend: function () {

                $('#uploadAvatarBtn')
                    .prop('disabled', true);

            },


            success: function (response) {

                console.log(
                    'Image upload response:',
                    response
                );


                if (
                    response &&
                    response.success === true
                ) {

                    /*
                    |--------------------------------------------------------------------------
                    | Use S3 URL Returned By Backend
                    |--------------------------------------------------------------------------
                    */

                    if (response.image_url) {

                        DOM.avatar
                            .find('img')
                            .attr(
                                'src',
                                response.image_url
                            );

                    }


                    showAlert(
                        'Success',
                        'Profile image uploaded successfully.',
                        'success'
                    );

                    window.location.reload();

                } else {

                    showAlert(
                        'Upload Failed',
                        response?.message ||
                        'Unable to upload profile image.',
                        'error'
                    );

                }

            },


            error: function (xhr) {

                console.error(
                    'Image upload error:',
                    xhr.responseText
                );


                showAlert(
                    'Upload Failed',
                    'Something went wrong while uploading the image.',
                    'error'
                );

            },


            complete: function () {

                $('#uploadAvatarBtn')
                    .prop('disabled', false);

            }

        });

    }


    /*
    |--------------------------------------------------------------------------
    | Render Status
    |--------------------------------------------------------------------------
    */

    function renderStatus(status) {

        const normalizedStatus =
            cleanValue(
                status
            ).toLowerCase();


        let badgeClass =
            'status-badge';


        let icon =
            'bi-circle-fill';


        switch (normalizedStatus) {

            case 'active':

                badgeClass +=
                    ' status-active';

                icon =
                    'bi-check-circle-fill';

                break;


            case 'inactive':

                badgeClass +=
                    ' status-inactive';

                icon =
                    'bi-pause-circle-fill';

                break;


            case 'terminated':

                badgeClass +=
                    ' status-terminated';

                icon =
                    'bi-x-circle-fill';

                break;


            case 'on_leave':

                badgeClass +=
                    ' status-leave';

                icon =
                    'bi-calendar-x-fill';

                break;


            default:

                badgeClass +=
                    ' status-default';
        }


        DOM.status
            .removeClass()
            .addClass(
                badgeClass
            );


        DOM.status.html(`

            <i
                class="bi ${icon} me-1"
            ></i>

            ${escapeHtml(
            formatStatus(
                normalizedStatus
            )
        )}

        `);
    }


    /*
    |--------------------------------------------------------------------------
    | Attendance Summary
    |--------------------------------------------------------------------------
    */

    function renderAttendanceSummary(summary) {

        const present =
            toNumber(
                summary.present_days
            );


        const absent =
            toNumber(
                summary.absent_days
            );


        const halfDay =
            toNumber(
                summary.half_days
            );


        $('#presentDays')
            .text(
                present
            );


        $('#absentDays')
            .text(
                absent
            );


        $('#halfDays')
            .text(
                halfDay
            );


        DOM.attendance.text(
            present
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Leave Summary
    |--------------------------------------------------------------------------
    */

    function renderLeaveSummary(summary) {

        const approved =
            toNumber(
                summary.approved
            );


        DOM.leave.text(
            approved
        );


        $('#approvedLeaves')
            .text(
                approved
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Attendance Table
    |--------------------------------------------------------------------------
    */

    function renderAttendance(records) {

        if (!DOM.attendanceTable.length) {

            return;
        }


        DOM.attendanceTable.empty();


        if (
            !Array.isArray(records) ||
            records.length === 0
        ) {

            DOM.attendanceTable.html(`

                <tr>

                    <td
                        colspan="5"
                        class="text-center text-muted py-4"
                    >

                        <i
                            class="bi bi-calendar-x fs-4 d-block mb-2"
                        ></i>

                        No attendance records found.

                    </td>

                </tr>

            `);

            return;
        }


        records.forEach(
            function (record) {

                const status =
                    cleanValue(
                        record.status
                    ).toLowerCase();


                const badgeClass =
                    getAttendanceBadgeClass(
                        status
                    );


                const row = `

                    <tr>

                        <td>
                            ${escapeHtml(
                    formatDate(
                        record.attendance_date
                    )
                )}
                        </td>

                        <td>

                            <span
                                class="badge ${badgeClass}"
                            >

                                ${escapeHtml(
                    formatStatus(
                        status
                    )
                )}

                            </span>

                        </td>

                        <td>
                            ${escapeHtml(
                    record.check_in || '-'
                )}
                        </td>

                        <td>
                            ${escapeHtml(
                    record.check_out || '-'
                )}
                        </td>

                        <td>
                            ${escapeHtml(
                    record.remarks || '-'
                )}
                        </td>

                    </tr>

                `;


                DOM.attendanceTable.append(
                    row
                );
            }
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Leave Table
    |--------------------------------------------------------------------------
    */

    function renderLeaves(leaves) {

        if (!DOM.leaveTable.length) {

            return;
        }


        DOM.leaveTable.empty();


        if (
            !Array.isArray(leaves) ||
            leaves.length === 0
        ) {

            DOM.leaveTable.html(`

                <tr>

                    <td
                        colspan="5"
                        class="text-center text-muted py-4"
                    >

                        <i
                            class="bi bi-calendar-check fs-4 d-block mb-2"
                        ></i>

                        No leave records found.

                    </td>

                </tr>

            `);

            return;
        }


        leaves.forEach(
            function (leave) {

                const status =
                    cleanValue(
                        leave.status
                    ).toLowerCase();


                const badgeClass =
                    getLeaveBadgeClass(
                        status
                    );


                const row = `

                    <tr>

                        <td>
                            ${escapeHtml(
                    displayValue(
                        leave.leave_type
                    )
                )}
                        </td>

                        <td>
                            ${escapeHtml(
                    formatDate(
                        leave.start_date
                    )
                )}
                        </td>

                        <td>
                            ${escapeHtml(
                    formatDate(
                        leave.end_date
                    )
                )}
                        </td>

                        <td>
                            ${escapeHtml(
                    leave.reason || '-'
                )}
                        </td>

                        <td>

                            <span
                                class="badge ${badgeClass}"
                            >

                                ${escapeHtml(
                    formatStatus(
                        status
                    )
                )}

                            </span>

                        </td>

                    </tr>

                `;


                DOM.leaveTable.append(
                    row
                );
            }
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Attendance Badge
    |--------------------------------------------------------------------------
    */

    function getAttendanceBadgeClass(status) {

        switch (status) {

            case 'present':
                return 'bg-success';

            case 'absent':
                return 'bg-danger';

            case 'half_day':
                return 'bg-warning text-dark';

            case 'leave':
                return 'bg-info text-dark';

            default:
                return 'bg-secondary';
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Leave Badge
    |--------------------------------------------------------------------------
    */

    function getLeaveBadgeClass(status) {

        switch (status) {

            case 'approved':
                return 'bg-success';

            case 'pending':
                return 'bg-warning text-dark';

            case 'rejected':
                return 'bg-danger';

            default:
                return 'bg-secondary';
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Setup Edit Button
    |--------------------------------------------------------------------------
    */

    function setupEditButton(employeeId) {

        DOM.editButton
            .off('click')
            .on(
                'click',
                function () {

                    const id =
                        Number(
                            employeeId
                        );


                    if (
                        !Number.isInteger(id) ||
                        id <= 0
                    ) {

                        showAlert(
                            'Invalid Employee',
                            'Unable to edit this employee.',
                            'error'
                        );

                        return;
                    }


                    openEditModal(id);
                }
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Open Edit Modal
    |--------------------------------------------------------------------------
    */

    function openEditModal(employeeId) {

        const modalElement =
            document.getElementById(
                'editEmployeeModal'
            );


        if (!modalElement) {

            showAlert(
                'Error',
                'Edit employee modal was not found.',
                'error'
            );

            return;
        }


        $.ajax({

            url:
                CONFIG.apiUrl,

            type:
                'GET',

            dataType:
                'json',

            data: {

                action:
                    'show',

                id:
                    employeeId
            },


            beforeSend:
                function () {

                    $('#editEmployeeForm')
                        .find(
                            'input, select, button'
                        )
                        .prop(
                            'disabled',
                            true
                        );
                },


            success:
                function (response) {

                    console.log(
                        'Edit employee response:',
                        response
                    );


                    if (
                        !response ||
                        response.success !== true ||
                        !response.employee
                    ) {

                        showAlert(
                            'Unable to Load Employee',
                            response?.message ||
                            'Employee information could not be loaded.',
                            'error'
                        );

                        return;
                    }


                    populateEditForm(
                        response.employee
                    );


                    const modal =
                        bootstrap.Modal
                            .getOrCreateInstance(
                                modalElement
                            );


                    modal.show();
                },


            error:
                function (xhr) {

                    console.error(
                        'Edit employee load error:',
                        xhr.responseText
                    );


                    showAlert(
                        'Unable to Load Employee',
                        getAjaxErrorMessage(xhr),
                        'error'
                    );
                },


            complete:
                function () {

                    $('#editEmployeeForm')
                        .find(
                            'input, select, button'
                        )
                        .prop(
                            'disabled',
                            false
                        );
                }

        });
    }


    /*
    |--------------------------------------------------------------------------
    | Populate Edit Form
    |--------------------------------------------------------------------------
    */

    function populateEditForm(employee) {

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


        $('#edit_department')
            .val(
                employee.department || ''
            );


        $('#edit_designation')
            .val(
                employee.designation || ''
            );


        $('#edit_joining_date')
            .val(
                employee.joining_date || ''
            );


        $('#edit_status')
            .val(
                employee.status || 'active'
            );


        clearEditFormErrors();
    }


    /*
    |--------------------------------------------------------------------------
    | Edit Form Submit
    |--------------------------------------------------------------------------
    */

    $('#editEmployeeForm').on(
        'submit',
        function (event) {

            event.preventDefault();


            clearEditFormErrors();


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

                icon:
                    'question',

                showCancelButton:
                    true,

                confirmButtonText:
                    'Yes, Update',

                cancelButtonText:
                    'Cancel',

                reverseButtons:
                    true,

                focusCancel:
                    true

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


    /*
    |--------------------------------------------------------------------------
    | Validate Edit Form
    |--------------------------------------------------------------------------
    */

    function validateEditForm() {

        return validateFields({

            prefix:
                'edit_',

            showError:
                showEditFieldError
        });
    }


    /*
    |--------------------------------------------------------------------------
    | Common Validation
    |--------------------------------------------------------------------------
    */

    function validateFields(options) {

        const prefix =
            options.prefix;


        const showError =
            options.showError;


        let isValid = true;


        /*
        |--------------------------------------------------------------------------
        | Get Values
        |--------------------------------------------------------------------------
        */

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


        /*
        |--------------------------------------------------------------------------
        | Employee ID
        |--------------------------------------------------------------------------
        */

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


        /*
        |--------------------------------------------------------------------------
        | First Name
        |--------------------------------------------------------------------------
        */

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


        /*
        |--------------------------------------------------------------------------
        | Last Name
        |--------------------------------------------------------------------------
        */

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


        /*
        |--------------------------------------------------------------------------
        | Email
        |--------------------------------------------------------------------------
        */

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


        /*
        |--------------------------------------------------------------------------
        | Phone
        |--------------------------------------------------------------------------
        */

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


        /*
        |--------------------------------------------------------------------------
        | Department
        |--------------------------------------------------------------------------
        */

        if (!department) {

            showError(
                'department',
                'Department is required.'
            );

            isValid = false;
        }


        /*
        |--------------------------------------------------------------------------
        | Designation
        |--------------------------------------------------------------------------
        */

        if (!designation) {

            showError(
                'designation',
                'Designation is required.'
            );

            isValid = false;

        } else if (
            designation.length < 2
        ) {

            showError(
                'designation',
                'Designation must contain at least 2 characters.'
            );

            isValid = false;
        }


        /*
        |--------------------------------------------------------------------------
        | Joining Date
        |--------------------------------------------------------------------------
        */

        if (!joiningDate) {

            showError(
                'joining_date',
                'Joining date is required.'
            );

            isValid = false;
        }


        /*
        |--------------------------------------------------------------------------
        | Status
        |--------------------------------------------------------------------------
        */

        if (!status) {

            showError(
                'status',
                'Status is required.'
            );

            isValid = false;
        }


        /*
        |--------------------------------------------------------------------------
        | Validation Alert
        |--------------------------------------------------------------------------
        */

        if (!isValid) {

            Swal.fire({

                icon:
                    'warning',

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


    /*
    |--------------------------------------------------------------------------
    | Email Validation
    |--------------------------------------------------------------------------
    */

    function isValidEmail(email) {

        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/
            .test(email);
    }


    /*
    |--------------------------------------------------------------------------
    | Show Edit Field Error
    |--------------------------------------------------------------------------
    */

    function showEditFieldError(
        field,
        message
    ) {

        $('#edit_' + field + 'Error')
            .text(
                message || ''
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Backend Validation Errors
    |--------------------------------------------------------------------------
    */

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


    /*
    |--------------------------------------------------------------------------
    | Clear Edit Form Errors
    |--------------------------------------------------------------------------
    */

    function clearEditFormErrors() {

        $('#editEmployeeForm .field-error')
            .text('');


        $('#editEmployeeForm .is-invalid')
            .removeClass(
                'is-invalid'
            );


        $('#editFormMessage')
            .stop(true, true)
            .removeClass(
                'alert-success alert-danger alert-warning'
            )
            .addClass(
                'd-none'
            )
            .text('');
    }


    /*
    |--------------------------------------------------------------------------
    | UPDATE EMPLOYEE
    |--------------------------------------------------------------------------
    */

    function updateEmployee() {

        const form =
            $('#editEmployeeForm');


        if (!form.length) {

            showAlert(
                'Error',
                'Update form was not found.',
                'error'
            );

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | Get Database ID
        |--------------------------------------------------------------------------
        */

        const employeeId =
            Number(
                $('#edit_id').val()
            );


        if (
            !Number.isInteger(employeeId) ||
            employeeId <= 0
        ) {

            showAlert(
                'Invalid Employee',
                'Employee ID is invalid.',
                'error'
            );

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | IMPORTANT
        |--------------------------------------------------------------------------
        |
        | Serialize BEFORE disabling form fields.
        |
        | jQuery does not serialize disabled fields.
        |
        */

        const formData =
            form.serialize() +
            '&action=update';


        /*
        |--------------------------------------------------------------------------
        | Debug Form Data
        |--------------------------------------------------------------------------
        */

        console.log(
            '===================================='
        );

        console.log(
            'UPDATE EMPLOYEE FORM DATA'
        );

        console.log(
            '===================================='
        );


        console.log(
            'Raw Form Data:',
            formData
        );


        const params =
            new URLSearchParams(
                formData
            );


        for (
            const [key, value]
            of params.entries()
        ) {

            console.log(
                key + ' :',
                value
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Loading
        |--------------------------------------------------------------------------
        */

        setUpdateLoading(true);


        /*
        |--------------------------------------------------------------------------
        | AJAX Request
        |--------------------------------------------------------------------------
        */

        $.ajax({

            url:
                CONFIG.apiUrl,

            type:
                'POST',

            data:
                formData,

            dataType:
                'json',


            /*
            |--------------------------------------------------------------------------
            | Success
            |--------------------------------------------------------------------------
            */

            success:
                function (response) {

                    console.log(
                        'Update response:',
                        response
                    );


                    if (
                        response &&
                        response.success === true
                    ) {

                        /*
                        |--------------------------------------------------------------------------
                        | Close Modal
                        |--------------------------------------------------------------------------
                        */

                        const modalElement =
                            document.getElementById(
                                'editEmployeeModal'
                            );


                        if (modalElement) {

                            const modal =
                                bootstrap.Modal
                                    .getInstance(
                                        modalElement
                                    );


                            if (modal) {

                                modal.hide();
                            }
                        }


                        /*
                        |--------------------------------------------------------------------------
                        | Success Message
                        |--------------------------------------------------------------------------
                        */

                        Swal.fire({

                            icon:
                                'success',

                            title:
                                'Updated Successfully',

                            text:
                                response.message ||
                                'Employee details have been updated.',

                            timer:
                                1500,

                            timerProgressBar:
                                true,

                            showConfirmButton:
                                false
                        });


                        /*
                        |--------------------------------------------------------------------------
                        | Reload Profile
                        |--------------------------------------------------------------------------
                        */

                        setTimeout(
                            function () {

                                loadEmployee();

                            },
                            500
                        );


                        return;
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | Update Failed
                    |--------------------------------------------------------------------------
                    */

                    const message =
                        response?.message ||
                        'Unable to update employee.';


                    showEditMessage(
                        message,
                        'danger'
                    );


                    Swal.fire({

                        icon:
                            'error',

                        title:
                            'Update Failed',

                        text:
                            message
                    });
                },


            /*
            |--------------------------------------------------------------------------
            | AJAX Error
            |--------------------------------------------------------------------------
            */

            error:
                function (xhr) {

                    console.error(
                        'Update AJAX Error:',
                        xhr.responseText
                    );


                    const response =
                        xhr.responseJSON;


                    /*
                    |--------------------------------------------------------------------------
                    | Validation Errors
                    |--------------------------------------------------------------------------
                    */

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

                            icon:
                                'warning',

                            title:
                                'Validation Failed',

                            text:
                                message
                        });


                        return;
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | Unauthorized
                    |--------------------------------------------------------------------------
                    */

                    if (
                        xhr.status === 401
                    ) {

                        Swal.fire({

                            icon:
                                'warning',

                            title:
                                'Session Expired',

                            text:
                                'Your session has expired. Please login again.'
                        });


                        return;
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | Forbidden
                    |--------------------------------------------------------------------------
                    */

                    if (
                        xhr.status === 403
                    ) {

                        Swal.fire({

                            icon:
                                'error',

                            title:
                                'Access Denied',

                            text:
                                'You are not authorized to update this employee.'
                        });


                        return;
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | Not Found
                    |--------------------------------------------------------------------------
                    */

                    if (
                        xhr.status === 404
                    ) {

                        Swal.fire({

                            icon:
                                'error',

                            title:
                                'Employee Not Found',

                            text:
                                'The employee could not be found.'
                        });


                        return;
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | Server Error
                    |--------------------------------------------------------------------------
                    */

                    const message =
                        response?.message ||
                        'Something went wrong while updating the employee.';


                    showEditMessage(
                        message,
                        'danger'
                    );


                    Swal.fire({

                        icon:
                            'error',

                        title:
                            'Server Error',

                        text:
                            message
                    });
                },


            /*
            |--------------------------------------------------------------------------
            | Complete
            |--------------------------------------------------------------------------
            */

            complete:
                function () {

                    setUpdateLoading(false);
                }

        });
    }


    /*
    |--------------------------------------------------------------------------
    | Update Loading
    |--------------------------------------------------------------------------
    */

    function setUpdateLoading(isLoading) {

        const button =
            $('#updateEmployeeBtn');


        if (!button.length) {

            return;
        }


        if (isLoading) {

            /*
            |--------------------------------------------------------------------------
            | Store Original Button HTML
            |--------------------------------------------------------------------------
            */

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


            /*
            |--------------------------------------------------------------------------
            | Disable Update Button
            |--------------------------------------------------------------------------
            */

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
                        aria-hidden="true"
                    ></span>

                    Updating...

                `);


            /*
            |--------------------------------------------------------------------------
            | Disable Modal Close
            |--------------------------------------------------------------------------
            */

            $('#editEmployeeModal')
                .find(
                    '[data-bs-dismiss="modal"], .btn-close'
                )
                .prop(
                    'disabled',
                    true
                );


            /*
            |--------------------------------------------------------------------------
            | Disable Form Fields
            |--------------------------------------------------------------------------
            */

            $('#editEmployeeForm')
                .find(
                    'input:not([type="hidden"]), select'
                )
                .prop(
                    'disabled',
                    true
                );

        } else {

            /*
            |--------------------------------------------------------------------------
            | Restore Button
            |--------------------------------------------------------------------------
            */

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


            /*
            |--------------------------------------------------------------------------
            | Enable Modal Close
            |--------------------------------------------------------------------------
            */

            $('#editEmployeeModal')
                .find(
                    '[data-bs-dismiss="modal"], .btn-close'
                )
                .prop(
                    'disabled',
                    false
                );


            /*
            |--------------------------------------------------------------------------
            | Enable Form Fields
            |--------------------------------------------------------------------------
            */

            $('#editEmployeeForm')
                .find(
                    'input:not([type="hidden"]), select'
                )
                .prop(
                    'disabled',
                    false
                );
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Show Edit Message
    |--------------------------------------------------------------------------
    */

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
            .text(
                message
            )
            .fadeIn(200);
    }


    /*
    |--------------------------------------------------------------------------
    | Calculate Experience
    |--------------------------------------------------------------------------
    */

    function calculateExperience(joiningDate) {

        if (!joiningDate) {

            return '-';
        }


        const joining =
            parseDate(
                joiningDate
            );


        if (!joining) {

            return '-';
        }


        const today =
            new Date();


        if (
            joining > today
        ) {

            return '-';
        }


        let years =
            today.getFullYear()
            -
            joining.getFullYear();


        let months =
            today.getMonth()
            -
            joining.getMonth();


        let days =
            today.getDate()
            -
            joining.getDate();


        if (days < 0) {

            months--;
        }


        if (months < 0) {

            years--;

            months += 12;
        }


        if (
            years === 0 &&
            months === 0
        ) {

            return 'Less than 1 month';
        }


        const yearText =
            years > 0
                ?
                `${years} ${years === 1 ? 'year' : 'years'}`
                :
                '';


        const monthText =
            months > 0
                ?
                `${months} ${months === 1 ? 'month' : 'months'}`
                :
                '';


        return [
            yearText,
            monthText
        ]
            .filter(Boolean)
            .join(' ');
    }


    /*
    |--------------------------------------------------------------------------
    | Format Date
    |--------------------------------------------------------------------------
    */

    function formatDate(value) {

        if (!value) {

            return '-';
        }


        const date =
            parseDate(
                value
            );


        if (!date) {

            return '-';
        }


        return date.toLocaleDateString(
            'en-IN',
            {

                day:
                    '2-digit',

                month:
                    'short',

                year:
                    'numeric'
            }
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Parse Date
    |--------------------------------------------------------------------------
    */

    function parseDate(value) {

        if (!value) {

            return null;
        }


        if (
            /^\d{4}-\d{2}-\d{2}$/
                .test(value)
        ) {

            const parts =
                value.split('-');


            const date =
                new Date(
                    Number(parts[0]),
                    Number(parts[1]) - 1,
                    Number(parts[2])
                );


            return isNaN(
                date.getTime()
            )
                ?
                null
                :
                date;
        }


        const date =
            new Date(
                value
            );


        return isNaN(
            date.getTime()
        )
            ?
            null
            :
            date;
    }


    /*
    |--------------------------------------------------------------------------
    | Get Initials
    |--------------------------------------------------------------------------
    */

    function getInitials(
        firstName,
        lastName
    ) {

        const first =
            cleanValue(
                firstName
            );


        const last =
            cleanValue(
                lastName
            );


        let initials =
            '';


        if (first) {

            initials +=
                first.charAt(0);
        }


        if (last) {

            initials +=
                last.charAt(0);
        }


        return initials
            ?
            initials
                .substring(0, 2)
                .toUpperCase()
            :
            '?';
    }


    /*
    |--------------------------------------------------------------------------
    | Format Status
    |--------------------------------------------------------------------------
    */

    function formatStatus(status) {

        if (!status) {

            return 'Unknown';
        }


        return String(status)

            .replace(
                /_/g,
                ' '
            )

            .replace(
                /\b\w/g,
                function (letter) {

                    return letter.toUpperCase();
                }
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Clean Value
    |--------------------------------------------------------------------------
    */

    function cleanValue(value) {

        if (
            value === null ||
            value === undefined
        ) {

            return '';
        }


        return String(value)
            .trim();
    }

    function getImageUrl(imageUrl) {

        imageUrl = cleanValue(imageUrl);

        if (!imageUrl) {
            return '';
        }

        /*
        |----------------------------------------------------------------------
        | If already a complete URL
        |----------------------------------------------------------------------
        */

        if (
            imageUrl.startsWith('http://') ||
            imageUrl.startsWith('https://') ||
            imageUrl.startsWith('data:')
        ) {
            return imageUrl;
        }


        imageUrl = imageUrl.replace(/\\/g, '/');

        imageUrl = imageUrl.replace(/^\/+/, '');

        /*
        | Remove "public/" because public is already
        | the web root of the application.
        */

        if (imageUrl.startsWith('public/')) {
            imageUrl = imageUrl.substring(7);
        }

        /*
        | Build URL from application root
        */

        return new URL(
            imageUrl,
            window.location.origin + '/leave_management/public/'
        ).href;
    }


    /*
    |--------------------------------------------------------------------------
    | Display Value
    |--------------------------------------------------------------------------
    */

    function displayValue(value) {

        const cleaned =
            cleanValue(
                value
            );


        return cleaned
            ?
            cleaned
            :
            '-';
    }


    /*
    |--------------------------------------------------------------------------
    | Convert To Number
    |--------------------------------------------------------------------------
    */

    function toNumber(value) {

        const number =
            Number(
                value
            );


        return Number.isFinite(
            number
        )
            ?
            number
            :
            0;
    }


    /*
    |--------------------------------------------------------------------------
    | Escape HTML
    |--------------------------------------------------------------------------
    */

    function escapeHtml(value) {

        return $('<div>')
            .text(
                cleanValue(value)
            )
            .html();
    }


    /*
    |--------------------------------------------------------------------------
    | Loading State
    |--------------------------------------------------------------------------
    */

    function setLoading(isLoading) {

        if (isLoading) {

            DOM.loading
                .removeClass(
                    'd-none'
                );


            DOM.profile
                .addClass(
                    'd-none'
                );

        } else {

            DOM.loading
                .addClass(
                    'd-none'
                );
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Hide Loading
    |--------------------------------------------------------------------------
    */

    function hideLoading() {

        DOM.loading
            .addClass(
                'd-none'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Show Error
    |--------------------------------------------------------------------------
    */

    function showError(message) {

        if (
            DOM.errorMessage.length
        ) {

            DOM.errorMessage.text(
                message
            );

        } else {

            DOM.error.text(
                message
            );
        }


        DOM.error
            .removeClass(
                'd-none'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Hide Error
    |--------------------------------------------------------------------------
    */

    function hideError() {

        DOM.error
            .addClass(
                'd-none'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Handle Load Error
    |--------------------------------------------------------------------------
    */

    function handleLoadError(message) {

        hideLoading();


        DOM.profile
            .addClass(
                'd-none'
            );


        showError(
            message
        );


        showAlert(
            'Unable to Load Employee',
            message,
            'error'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | AJAX Error Message
    |--------------------------------------------------------------------------
    */

    function getAjaxErrorMessage(xhr) {

        if (
            xhr &&
            xhr.responseJSON &&
            xhr.responseJSON.message
        ) {

            return xhr.responseJSON.message;
        }


        if (
            xhr &&
            xhr.status === 400
        ) {

            return 'Invalid request.';
        }


        if (
            xhr &&
            xhr.status === 401
        ) {

            return 'Your session has expired. Please login again.';
        }


        if (
            xhr &&
            xhr.status === 403
        ) {

            return 'You are not authorized to perform this action.';
        }


        if (
            xhr &&
            xhr.status === 404
        ) {

            return 'Employee was not found.';
        }


        if (
            xhr &&
            xhr.status >= 500
        ) {

            return 'A server error occurred. Please try again later.';
        }


        return 'Unable to communicate with the server.';
    }


    /*
    |--------------------------------------------------------------------------
    | SweetAlert
    |--------------------------------------------------------------------------
    */

    function showAlert(
        title,
        text,
        icon
    ) {

        if (
            typeof Swal === 'undefined'
        ) {

            alert(
                `${title}\n\n${text}`
            );

            return;
        }


        Swal.fire({

            icon:
                icon,

            title:
                title,

            text:
                text,

            confirmButtonText:
                'OK',

            confirmButtonColor:
                '#0d6efd',

            allowOutsideClick:
                false
        });
    }

    /*
|--------------------------------------------------------------------------
| Remove Profile Image
|--------------------------------------------------------------------------
*/

$(document).on(
    'click',
    '#removeAvatarBtn',
    function () {

        Swal.fire({

            title: 'Remove Profile Photo?',

            text: 'Are you sure you want to remove your profile photo?',

            icon: 'warning',

            showCancelButton: true,

            confirmButtonText: 'Yes, Remove',

            cancelButtonText: 'Cancel',

            reverseButtons: true,

            focusCancel: true

        }).then(function (result) {

            if (!result.isConfirmed) {
                return;
            }

            removeProfileImage();

        });

    }
);


/*
|--------------------------------------------------------------------------
| Remove Profile Image
|--------------------------------------------------------------------------
*/

function removeProfileImage() {

    $.ajax({

        url: CONFIG.apiUrl,

        type: 'POST',

        dataType: 'json',

        data: {

            action: 'remove_image',
        },

        beforeSend: function () {

            $('#removeAvatarBtn')
                .prop('disabled', true);

        },

        success: function (response) {

            console.log(
                'Remove Image Response:',
                response
            );


            if (
                response &&
                response.success === true
            ) {

                Swal.fire({

                    icon: 'success',

                    title: 'Photo Removed',

                    text:
                        response.message ||
                        'Profile photo removed successfully.',

                    timer: 1500,

                    showConfirmButton: false

                }).then(function () {

                    /*
                    |--------------------------------------------------------------------------
                    | Reload Page
                    |--------------------------------------------------------------------------
                    */

                    window.location.reload();

                });

                return;
            }


            /*
            |--------------------------------------------------------------------------
            | Remove Failed
            |--------------------------------------------------------------------------
            */

            Swal.fire({

                icon: 'error',

                title: 'Remove Failed',

                text:
                    response?.message ||
                    'Unable to remove profile photo.'

            });

        },

        error: function (xhr) {

            console.error(
                'Remove Image Error:',
                xhr.responseText
            );


            let message =
                'Something went wrong while removing the profile photo.';


            if (
                xhr.responseJSON &&
                xhr.responseJSON.message
            ) {

                message =
                    xhr.responseJSON.message;
            }


            Swal.fire({

                icon: 'error',

                title: 'Remove Failed',

                text: message

            });

        },

        complete: function () {

            $('#removeAvatarBtn')
                .prop('disabled', false);

        }

    });

}

});
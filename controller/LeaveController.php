<?php

class LeaveController extends Controller
{

    protected Leave $leaveModel;
    protected Employee $employeeModel;
    protected User $userModel;
    protected PDO $db;


    /*
    |--------------------------------------------------------------------------
    | Constructor
    |--------------------------------------------------------------------------
    */

    public function __construct(PDO $db)
    {
        $this->db = $db;
        $this->employeeModel=new Employee($db);
        $this->userModel=new User($db);
        $this->leaveModel =
            new Leave($db);
    }



    /*
    |--------------------------------------------------------------------------
    | Create Leave Request
    |--------------------------------------------------------------------------
    */

    public function store(): void
    {

        /*
        |--------------------------------------------------------------------------
        | Get logged-in user
        |--------------------------------------------------------------------------
        */

        $userId =
            Auth::userId();


        if (!$userId) {

            $this->json([

                'success' => false,

                'message' =>
                'You must be logged in.'

            ], 401);
        }


        /*
        |--------------------------------------------------------------------------
        | Get User
        |--------------------------------------------------------------------------
        */

        $stmt =
            $this->db->prepare("
                SELECT
                    id,
                    employee_id,
                    role,
                    status

                FROM users

                WHERE id = :id

                LIMIT 1
            ");


        $stmt->execute([

            ':id' => $userId

        ]);


        $user =
            $stmt->fetch(
                PDO::FETCH_ASSOC
            );


        if (!$user) {

            $this->json([

                'success' => false,

                'message' =>
                'User account not found.'

            ], 404);
        }


        /*
        |--------------------------------------------------------------------------
        | Employee Only
        |--------------------------------------------------------------------------
        */

        if ($user['role'] !== 'employee') {

            $this->json([

                'success' => false,

                'message' =>
                'Only employees can submit leave requests.'

            ], 403);
        }


        /*
        |--------------------------------------------------------------------------
        | Account Status
        |--------------------------------------------------------------------------
        */

        if ($user['status'] !== 'active') {

            $this->json([

                'success' => false,

                'message' =>
                'Your account is inactive.'

            ], 403);
        }


        /*
        |--------------------------------------------------------------------------
        | Find Employee
        |--------------------------------------------------------------------------
        |
        | users.employee_id contains something like EMP001.
        |
        | leaves.employee_id stores employees.id.
        |
        */

        $stmt =
            $this->db->prepare("
                SELECT
                    id,
                    employee_id,
                    current_leave_pending,
                    casual_pending,sick_pending,annual_pending

                FROM employees

                WHERE employee_id = :employee_id

                LIMIT 1
            ");


        $stmt->execute([

            ':employee_id' =>
            $user['employee_id']

        ]);


        $employee =
            $stmt->fetch(
                PDO::FETCH_ASSOC
            );


        if (!$employee) {

            $this->json([

                'success' => false,

                'message' =>
                'Employee record not found.'

            ], 404);
        }


        $employeeId =
            (int) $employee['id'];



        /*
        |--------------------------------------------------------------------------
        | Get Form Data
        |--------------------------------------------------------------------------
        */

        $leaveType =
            trim(
                $this->input(
                    'leave_type',
                    ''
                )
            );


        $startDate =
            trim(
                $this->input(
                    'start_date',
                    ''
                )
            );


        $endDate =
            trim(
                $this->input(
                    'end_date',
                    ''
                )
            );


        $reason =
            trim(
                $this->input(
                    'reason',
                    ''
                )
            );



        /*
        |--------------------------------------------------------------------------
        | Validation
        |--------------------------------------------------------------------------
        */

        $data = [

            'leave_type' =>
            $leaveType,

            'start_date' =>
            $startDate,

            'end_date' =>
            $endDate,

            'reason' =>
            $reason

        ];


        $validator =
            new Validator($data);


        $validator
            ->required('leave_type')
            ->required('start_date')
            ->required('end_date')
            ->required('reason');



        /*
        |--------------------------------------------------------------------------
        | Leave Type Validation
        |--------------------------------------------------------------------------
        */

        $allowedLeaveTypes = [

            'casual',

            'sick',

            'annual',

        ];


        if (
            $leaveType !== ''
            &&
            !in_array(
                $leaveType,
                $allowedLeaveTypes,
                true
            )
        ) {

            $validator->addError(
                'leave_type',
                'Invalid leave type.'
            );
        }



        /*
        |--------------------------------------------------------------------------
        | Date Validation
        |--------------------------------------------------------------------------
        */

        $start =
            DateTime::createFromFormat(
                'Y-m-d',
                $startDate
            );


        $end =
            DateTime::createFromFormat(
                'Y-m-d',
                $endDate
            );


        if (
            !$start
            ||
            $start->format('Y-m-d')
            !== $startDate
        ) {

            $validator->addError(
                'start_date',
                'Invalid start date.'
            );
        }


        if (
            !$end
            ||
            $end->format('Y-m-d')
            !== $endDate
        ) {

            $validator->addError(
                'end_date',
                'Invalid end date.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | End Date Must Be After Start Date
        |--------------------------------------------------------------------------
        */

        if (
            $start
            &&
            $end
            &&
            $start > $end
        ) {

            $validator->addError(
                'end_date',
                'End date must be greater than or equal to start date.'
            );
        }



        /*
        |--------------------------------------------------------------------------
        | Reason Length
        |--------------------------------------------------------------------------
        */

        if (
            $reason !== ''
            &&
            strlen($reason) < 5
        ) {

            $validator->addError(
                'reason',
                'Reason must contain at least 5 characters.'
            );
        }



        /*
        |--------------------------------------------------------------------------
        | Return Validation Errors
        |--------------------------------------------------------------------------
        */

        if ($validator->fails()) {

            $this->json([

                'success' =>
                false,

                'message' =>
                'Validation failed.',

                'errors' =>
                $validator->errors()

            ], 422);
        }

        /*
        |--------------------------------------------------------------------------
        | Check Leave Balance
        |--------------------------------------------------------------------------
        */

        $leaveDays = 0;

        if ($start && $end) {

            $leaveDays =
                $start->diff($end)->days + 1;
        }


        if (
            $leaveDays > 0
            &&
            in_array(
                $leaveType,
                $allowedLeaveTypes,
                true
            )
        ) {

            $availableLeave = 0;


            if ($leaveType === 'casual') {

                $availableLeave =
                    (int) $employee['casual_pending'];
            } elseif ($leaveType === 'sick') {

                $availableLeave =
                    (int) $employee['sick_pending'];
            } elseif ($leaveType === 'annual') {

                $availableLeave =
                    (int) $employee['annual_pending'];
            }


            if ($leaveDays > $availableLeave) {

                $this->json([

                    'success' => false,

                    'message' =>
                    ucfirst($leaveType)
                        . ' leave pending is used. '
                        . 'You have only '
                        . $availableLeave
                        . ' day(s) remaining, but you requested '
                        . $leaveDays
                        . ' day(s).'

                ], 422);
            }
        }



        /*
        |--------------------------------------------------------------------------
        | Check Overlapping Leave
        |--------------------------------------------------------------------------
        */

        if (
            $this->leaveModel->hasOverlap(
                $employeeId,
                $startDate,
                $endDate
            )
        ) {

            $this->json([

                'success' =>
                false,

                'message' =>
                'You already have a pending or approved leave for these dates.'

            ], 422);
        }



        /*
        |--------------------------------------------------------------------------
        | Create Leave
        |--------------------------------------------------------------------------
        */

        try {

            $leaveId =
                $this->leaveModel->create([

                    'employee_id' =>
                    $employeeId,

                    'leave_type' =>
                    $leaveType,

                    'start_date' =>
                    $startDate,

                    'end_date' =>
                    $endDate,

                    'reason' =>
                    $reason

                ]);


            $this->json([

                'success' =>
                true,

                'message' =>
                'Leave request submitted successfully.',

                'leave_id' =>
                $leaveId

            ]);
        } catch (PDOException $e) {

            $this->json([

                'success' =>
                false,

                'message' =>
                'Unable to submit leave request.'

            ], 500);
        }
    }



    /*
    |--------------------------------------------------------------------------
    | Employee Leave List
    |--------------------------------------------------------------------------
    */

    public function myLeaves(): void
    {

        $userId =
            Auth::userId();


        if (!$userId) {

            $this->json([

                'success' => false,

                'message' =>
                'You must be logged in.'

            ], 401);
        }


        try {

            /*
            |--------------------------------------------------------------------------
            | Find Employee
            |--------------------------------------------------------------------------
            */

            $stmt =
                $this->db->prepare("
                    SELECT employee_id

                    FROM users

                    WHERE id = :id

                    LIMIT 1
                ");


            $stmt->execute([

                ':id' =>
                $userId

            ]);


            $user =
                $stmt->fetch(
                    PDO::FETCH_ASSOC
                );


            if (!$user) {

                $this->json([

                    'success' => false,

                    'message' =>
                    'User account not found.'

                ], 404);
            }


            /*
            |--------------------------------------------------------------------------
            | Find Employee ID
            |--------------------------------------------------------------------------
            */

            $stmt =
                $this->db->prepare("
                    SELECT id

                    FROM employees

                    WHERE employee_id = :employee_id

                    LIMIT 1
                ");


            $stmt->execute([

                ':employee_id' =>
                $user['employee_id']

            ]);


            $employee =
                $stmt->fetch(
                    PDO::FETCH_ASSOC
                );


            if (!$employee) {

                $this->json([

                    'success' => false,

                    'message' =>
                    'Employee record not found.'

                ], 404);
            }


            /*
            |--------------------------------------------------------------------------
            | Get Leaves
            |--------------------------------------------------------------------------
            */

            $leaves =
                $this->leaveModel
                ->getByEmployee(
                    (int) $employee['id']
                );


            $this->json([

                'success' =>
                true,

                'leaves' =>
                $leaves

            ]);
        } catch (PDOException $e) {

            $this->json([

                'success' =>
                false,

                'message' =>
                'Unable to load leave requests.'

            ], 500);
        }
    }

    /*
|--------------------------------------------------------------------------
| Admin - Get All Leave Requests
|--------------------------------------------------------------------------
*/

    public function adminLeaves(): void
    {
        try {

            $leaves =
                $this->leaveModel->getAll();

            $this->json([

                'success' => true,

                'leaves' => $leaves

            ]);
        } catch (PDOException $e) {

            $this->json([

                'success' => false,

                'message' =>
                'Unable to load leave requests.'

            ], 500);
        }
    }

    /*
|--------------------------------------------------------------------------
| Admin - Approve / Reject Leave
|--------------------------------------------------------------------------
*/

    public function updateStatus(): void
    {
        $leaveId =
            trim(
                $this->input(
                    'leave_id',
                    ''
                )
            );


        $status =
            trim(
                $this->input(
                    'status',
                    ''
                )
            );


        $adminRemarks =
            trim(
                $this->input(
                    'admin_remarks',
                    ''
                )
            );


        /*
    |--------------------------------------------------------------------------
    | Validate
    |--------------------------------------------------------------------------
    */

        $validator =
            new Validator([

                'leave_id' =>
                $leaveId,

                'status' =>
                $status

            ]);


        $validator
            ->required('leave_id')
            ->required('status');


        /*
    |--------------------------------------------------------------------------
    | Leave ID
    |--------------------------------------------------------------------------
    */

        if (
            $leaveId !== ''
            &&
            (
                !ctype_digit($leaveId)
                ||
                (int) $leaveId <= 0
            )
        ) {

            $validator->addError(
                'leave_id',
                'Invalid leave request.'
            );
        }


        /*
    |--------------------------------------------------------------------------
    | Status
    |--------------------------------------------------------------------------
    */

        $allowedStatuses = [

            'approved',

            'rejected'

        ];


        if (
            $status !== ''
            &&
            !in_array(
                $status,
                $allowedStatuses,
                true
            )
        ) {

            $validator->addError(
                'status',
                'Invalid leave status.'
            );
        }


        /*
    |--------------------------------------------------------------------------
    | Remarks
    |--------------------------------------------------------------------------
    */

        if (
            strlen($adminRemarks) > 1000
        ) {

            $validator->addError(
                'admin_remarks',
                'Remarks cannot exceed 1000 characters.'
            );
        }


        /*
    |--------------------------------------------------------------------------
    | Validation Response
    |--------------------------------------------------------------------------
    */

        if ($validator->fails()) {

            $this->json([

                'success' => false,

                'message' =>
                'Validation failed.',

                'errors' =>
                $validator->errors()

            ], 422);
        }


        $leaveId =
            (int) $leaveId;


        /*
    |--------------------------------------------------------------------------
    | Find Leave
    |--------------------------------------------------------------------------
    */

        $leave =
            $this->leaveModel->find(
                $leaveId
            );


        if (!$leave) {

            $this->json([

                'success' => false,

                'message' =>
                'Leave request not found.'

            ], 404);
        }


        /*
    |--------------------------------------------------------------------------
    | Don't Process Already Processed Leave
    |--------------------------------------------------------------------------
    */

        if (
            $leave['status'] !== 'pending'
        ) {

            $this->json([

                'success' => false,

                'message' =>
                'This leave request has already been processed.'

            ], 422);
        }


        /*
    |--------------------------------------------------------------------------
    | Logged-in Admin
    |--------------------------------------------------------------------------
    */

        $adminId =
            Auth::userId();


        try {

            $updated =
                $this->leaveModel->updateStatus(

                    $leaveId,

                    $status,

                    $adminId,

                    $adminRemarks !== ''
                        ? $adminRemarks
                        : null

                );

            if ($status === 'approved') {

                // Calculate total leave days (including start and end date)
                $startDate = new DateTime($leave['start_date']);
                $endDate = new DateTime($leave['end_date']);

                $leaveDays = (int) $startDate->diff($endDate)->days + 1;




                // Subtract from total leave
                $this->leaveModel->updateEmployeeLeaveBalance(
                    $leave['employee_id'],
                    $leaveDays,
                    $leave['leave_type']
                );
            }


            if (!$updated) {

                $this->json([

                    'success' => false,

                    'message' =>
                    'Unable to update leave request.'

                ], 500);
            }


            $message =
                $status === 'approved'
                ? 'Leave request approved successfully.'
                : 'Leave request rejected successfully.';


            $this->json([

                'success' => true,

                'message' => $message

            ]);
        } catch (PDOException $e) {

            $this->json([

                'success' => false,

                'message' =>
                'Unable to update leave request.' . $e->getMessage()

            ], 500);
        }
    }

    /*
|--------------------------------------------------------------------------
| Get Leave Details
|--------------------------------------------------------------------------
|
| This method contains the actual leave calculation.
| It can be used by:
|
| 1. Normal employee API
| 2. AI service API
|
*/

private function getLeaveDetails(int $employeeId): void
{
    // --------------------------------------------------
    // 1. Validate employee ID
    // --------------------------------------------------

    if ($employeeId <= 0) {

        $this->json([
            'success' => false,
            'message' => 'Invalid employee ID.'
        ], 400);
    }


    // --------------------------------------------------
    // 2. Get employee
    // --------------------------------------------------

    $employee = $this->employeeModel->findByid(
        $employeeId
    );


    if (!$employee) {

        $this->json([
            'success' => false,
            'message' => 'Employee not found.'
        ], 404);
    }


    // --------------------------------------------------
    // 3. Get leave history
    // --------------------------------------------------

    try {

        $leaveHistory = $this->leaveModel->getleavehistory(
            $employeeId
        );

        /*
        | No leave history is valid.
        | It means employee has not applied for leave.
        */

        if (!$leaveHistory) {
            $leaveHistory = [];
        }

    } catch (PDOException $e) {

        $this->json([
            'success' => false,
            'message' => 'Unable to fetch leave history.'
        ], 500);
    }


    // --------------------------------------------------
    // 4. Employee information
    // --------------------------------------------------

    $employeeResponse = [

        'employee_id' => (int) $employee['id'],

        'employee_code' =>
            $employee['employee_id'] ?? null,

        'name' => trim(
            ($employee['first_name'] ?? '') . ' ' .
            ($employee['last_name'] ?? '')
        ),

        'department' =>
            $employee['department'] ?? null,

        'designation' =>
            $employee['designation'] ?? null
    ];


    // --------------------------------------------------
    // 5. Leave allocation
    // --------------------------------------------------

    $totalAllocated =
        (int) ($employee['max_total_leave'] ?? 0);

    $casualAllocated =
        (int) ($employee['max_casual_leave'] ?? 0);

    $sickAllocated =
        (int) ($employee['max_sick_leave'] ?? 0);

    $annualAllocated =
        (int) ($employee['max_annual_leave'] ?? 0);


    // --------------------------------------------------
    // 6. Initialize counters
    // --------------------------------------------------

    $casualUsed = 0;
    $sickUsed = 0;
    $annualUsed = 0;

    $casualPending = 0;
    $sickPending = 0;
    $annualPending = 0;

    $leaveHistoryResponse = [];


    // --------------------------------------------------
    // 7. Process leave history
    // --------------------------------------------------

    foreach ($leaveHistory as $leave) {

        $startDate = new DateTime(
            $leave['start_date']
        );

        $endDate = new DateTime(
            $leave['end_date']
        );


        /*
        | Calculate inclusive leave days.
        |
        | Example:
        | 2026-09-10 → 2026-09-12
        |
        | = 3 days
        */

        $days =
            $startDate->diff($endDate)->days + 1;


        // --------------------------------------------------
        // Calculate used and pending
        // --------------------------------------------------

        if ($leave['leave_type'] === 'casual') {

            if ($leave['status'] === 'approved') {
                $casualUsed += $days;
            }

            if ($leave['status'] === 'pending') {
                $casualPending += $days;
            }

        } elseif ($leave['leave_type'] === 'sick') {

            if ($leave['status'] === 'approved') {
                $sickUsed += $days;
            }

            if ($leave['status'] === 'pending') {
                $sickPending += $days;
            }

        } elseif ($leave['leave_type'] === 'annual') {

            if ($leave['status'] === 'approved') {
                $annualUsed += $days;
            }

            if ($leave['status'] === 'pending') {
                $annualPending += $days;
            }
        }


        // --------------------------------------------------
        // Leave history response
        // --------------------------------------------------

        $leaveHistoryResponse[] = [

            'leave_id' =>
                (int) $leave['id'],

            'leave_type' =>
                ucfirst($leave['leave_type']) . ' Leave',

            'start_date' =>
                $leave['start_date'],

            'end_date' =>
                $leave['end_date'],

            'days' =>
                $days,

            'reason' =>
                $leave['reason'] ?? null,

            'status' =>
                $leave['status'],

            'applied_on' =>
                $leave['created_at'] ?? null,

            'approved_on' =>
                $leave['approved_at'] ?? null,
            'hr_comment'=>$leave['admin_remarks'] ?? NULL
        ];
    }


    // --------------------------------------------------
    // 8. Calculate remaining leaves
    // --------------------------------------------------

    $casualRemaining = max(
        0,
        $casualAllocated -
        $casualUsed -
        $casualPending
    );

    $sickRemaining = max(
        0,
        $sickAllocated -
        $sickUsed -
        $sickPending
    );

    $annualRemaining = max(
        0,
        $annualAllocated -
        $annualUsed -
        $annualPending
    );


    // --------------------------------------------------
    // 9. Leave balance
    // --------------------------------------------------

    $leaveBalance = [

        [
            'leave_type' => 'Casual Leave',

            'allocated' =>
                $casualAllocated,

            'used' =>
                $casualUsed,

            'pending' =>
                $casualPending,

            'remaining' =>
                $casualRemaining
        ],

        [
            'leave_type' => 'Sick Leave',

            'allocated' =>
                $sickAllocated,

            'used' =>
                $sickUsed,

            'pending' =>
                $sickPending,

            'remaining' =>
                $sickRemaining
        ],

        [
            'leave_type' => 'Annual Leave',

            'allocated' =>
                $annualAllocated,

            'used' =>
                $annualUsed,

            'pending' =>
                $annualPending,

            'remaining' =>
                $annualRemaining
        ]
    ];


    // --------------------------------------------------
    // 10. Total statistics
    // --------------------------------------------------

    $totalUsed =
        $casualUsed +
        $sickUsed +
        $annualUsed;

    $totalPending =
        $casualPending +
        $sickPending +
        $annualPending;

    $totalRemaining =
        $casualRemaining +
        $sickRemaining +
        $annualRemaining;


    // --------------------------------------------------
    // 11. Final response
    // --------------------------------------------------

    $this->json([

        'success' => true,

        'employee' =>
            $employeeResponse,

        'leave_summary' => [

            'total_allocated' =>
                $totalAllocated,

            'total_used' =>
                $totalUsed,

            'total_pending' =>
                $totalPending,

            'total_remaining' =>
                $totalRemaining
        ],

        'leave_balance' =>
            $leaveBalance,

        'leave_history' =>
            $leaveHistoryResponse
    ]);
}


/*
|--------------------------------------------------------------------------
| Normal Employee Leave Details
|--------------------------------------------------------------------------
|
| Browser/frontend uses this endpoint.
| Employee ID comes from the authenticated session.
|
*/

    public function getLeaves(): void
    {
        $employeeId = (int) Auth::emp_id();


        if (!$employeeId) {

            $this->json([
                'success' => false,
                'message' => 'You must be logged in.'
            ], 401);
        }


        $this->getLeaveDetails(
            $employeeId
        );
    }


    /*
    |--------------------------------------------------------------------------
    | AI Service Leave Details
    |--------------------------------------------------------------------------
    |
    | Python AI service uses this endpoint.
    |
    | Python sends:
    |
    | GET
    | ?action=ai_get_leave
    | &employee_id=5
    |
    */

    public function getLeavesForAI(): void
    {
        $employeeId = (int) (
            $_GET['employee_id'] ?? 0
        );


        if (!$employeeId) {

            $this->json([
                'success' => false,
                'message' => 'Employee ID is required.'
            ], 400);
        }


        $this->getLeaveDetails(
            $employeeId
        );
    }


}

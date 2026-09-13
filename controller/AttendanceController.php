<?php

class AttendanceController extends Controller
{
    protected Attendance $attendanceModel;

    protected Employee $employeeModel;
    protected Leave $leaveModel;

    private PDO $db;


    /*
    |--------------------------------------------------------------------------
    | Constructor
    |--------------------------------------------------------------------------
    */

    public function __construct(PDO $db)
    {
        $this->db = $db;

        $this->attendanceModel =
            new Attendance($db);
        $this->leaveModel = new Leave($db);

        $this->employeeModel =
            new Employee($db);
    }


    /*
    |--------------------------------------------------------------------------
    | Attendance Index
    |--------------------------------------------------------------------------
    |
    | Loads employees for the selected date.
    |
    | Priority:
    |
    | 1. Existing attendance
    | 2. Approved leave
    | 3. No attendance
    |
    */

    public function index(): void
    {
        date_default_timezone_set('Asia/Kolkata');

        $date = trim(
            $this->input(
                'date',
                date('Y-m-d')
            )
        );


        /*
        |--------------------------------------------------------------------------
        | Validate Date
        |--------------------------------------------------------------------------
        */

        $dateObject =
            DateTime::createFromFormat(
                'Y-m-d',
                $date
            );

        if (
            !$dateObject
            ||
            $dateObject->format('Y-m-d') !== $date
        ) {

            $this->json([

                'success' => false,

                'message' =>
                'Invalid attendance date.'

            ], 422);
        }


        /*
        |--------------------------------------------------------------------------
        | Get Employees
        |--------------------------------------------------------------------------
        */

        $employees =
            $this->employeeModel->getAll();


        /*
        |--------------------------------------------------------------------------
        | Attach Attendance / Approved Leave
        |--------------------------------------------------------------------------
        */

        foreach ($employees as &$employee) {

            $employeeId =
                (int) $employee['id'];


            /*
            |--------------------------------------------------------------------------
            | First Check Existing Attendance
            |--------------------------------------------------------------------------
            */

            $attendance =
                $this->attendanceModel
                ->findByEmployeeAndDate(
                    $employeeId,
                    $date
                );


            if ($attendance) {

                /*
                |--------------------------------------------------------------------------
                | Existing Attendance
                |--------------------------------------------------------------------------
                */

                $employee['attendance'] = [

                    'id' =>
                    (int) $attendance['id'],

                    'status' =>
                    $attendance['status'],

                    'check_in' =>
                    $attendance['check_in'],

                    'check_out' =>
                    $attendance['check_out'],

                    'remarks' =>
                    $attendance['remarks']

                ];

                continue;
            }


            /*
            |--------------------------------------------------------------------------
            | No Attendance
            |--------------------------------------------------------------------------
            |
            | Now check approved leave.
            |
            */

            $approvedLeave =
                $this->attendanceModel
                ->findApprovedLeave(
                    $employeeId,
                    $date
                );


            if ($approvedLeave) {

                /*
                |--------------------------------------------------------------------------
                | Automatically Show Approved Leave
                |--------------------------------------------------------------------------
                */

                $employee['attendance'] = [

                    /*
                    | null means this is NOT an attendance
                    | table record.
                    */

                    'id' => null,

                    'status' =>
                    'leave',

                    'check_in' =>
                    null,

                    'check_out' =>
                    null,

                    'remarks' =>
                    'Approved leave',

                    'leave' => [

                        'id' =>
                        (int) $approvedLeave['id'],

                        'leave_type' =>
                        $approvedLeave['leave_type'],

                        'start_date' =>
                        $approvedLeave['start_date'],

                        'end_date' =>
                        $approvedLeave['end_date'],

                        'reason' =>
                        $approvedLeave['reason']

                    ]

                ];
            } else {

                /*
                |--------------------------------------------------------------------------
                | No Attendance And No Approved Leave
                |--------------------------------------------------------------------------
                */

                $employee['attendance'] = null;
            }
        }

        unset($employee);


        /*
        |--------------------------------------------------------------------------
        | JSON Response
        |--------------------------------------------------------------------------
        */

        $this->json([

            'success' =>
            true,

            'date' =>
            $date,

            'employees' =>
            $employees

        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | Save Attendance
    |--------------------------------------------------------------------------
    */

    public function save(): void
    {
        $data = [

            'employee_id' =>
            (int) $this->input(
                'employee_id',
                0
            ),

            'attendance_date' =>
            trim(
                $this->input(
                    'attendance_date',
                    ''
                )
            ),

            'status' =>
            trim(
                $this->input(
                    'status',
                    ''
                )
            ),

            'check_in' =>
            trim(
                $this->input(
                    'check_in',
                    ''
                )
            ),

            'check_out' =>
            trim(
                $this->input(
                    'check_out',
                    ''
                )
            ),

            'remarks' =>
            trim(
                $this->input(
                    'remarks',
                    ''
                )
            )

        ];


        /*
        |--------------------------------------------------------------------------
        | Validator
        |--------------------------------------------------------------------------
        */

        $validator =
            new Validator($data);

        $validator
            ->required('employee_id')
            ->required('attendance_date')
            ->required('status');


        /*
        |--------------------------------------------------------------------------
        | Employee ID Validation
        |--------------------------------------------------------------------------
        */

        if ($data['employee_id'] <= 0) {

            $validator->addError(
                'employee_id',
                'Invalid employee.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Status Validation
        |--------------------------------------------------------------------------
        */

        $allowedStatuses = [

            'present',
            'absent',
            'half_day',
            'leave'

        ];

        if (
            !in_array(
                $data['status'],
                $allowedStatuses,
                true
            )
        ) {

            $validator->addError(
                'status',
                'Invalid attendance status.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Date Validation
        |--------------------------------------------------------------------------
        */

        $dateObject =
            DateTime::createFromFormat(
                'Y-m-d',
                $data['attendance_date']
            );

        if (
            !$dateObject
            ||
            $dateObject->format('Y-m-d')
            !== $data['attendance_date']
        ) {

            $validator->addError(
                'attendance_date',
                'Invalid attendance date.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Prevent Future Attendance
        |--------------------------------------------------------------------------
        */


        if ($dateObject) {



            $today = new DateTime();


            if (
                $dateObject->format('Y-m-d')
                >
                $today->format('Y-m-d')
            ) {

                $validator->addError(
                    'attendance_date',
                    'Attendance cannot be marked for a future date.'
                );
            }
        }



        /*
        |--------------------------------------------------------------------------
        | Validate Check-In
        |--------------------------------------------------------------------------
        */

        $checkInValid = true;

        $checkOutValid = true;


        if ($data['check_in'] !== '') {

            $checkInObject =
                DateTime::createFromFormat(
                    'H:i',
                    $data['check_in']
                );

            if (
                !$checkInObject
                ||
                $checkInObject->format('H:i')
                !== $data['check_in']
            ) {

                $checkInValid = false;

                $validator->addError(
                    'check_in',
                    'Invalid check-in time.'
                );
            }
        }


        /*
        |--------------------------------------------------------------------------
        | Validate Check-Out
        |--------------------------------------------------------------------------
        */

        if ($data['check_out'] !== '') {

            $checkOutObject =
                DateTime::createFromFormat(
                    'H:i',
                    $data['check_out']
                );

            if (
                !$checkOutObject
                ||
                $checkOutObject->format('H:i')
                !== $data['check_out']
            ) {

                $checkOutValid = false;

                $validator->addError(
                    'check_out',
                    'Invalid check-out time.'
                );
            }
        }


        /*
        |--------------------------------------------------------------------------
        | Present / Half Day
        |--------------------------------------------------------------------------
        */

        if (
            $data['status'] === 'present'
            ||
            $data['status'] === 'half_day'
        ) {

            if ($data['check_in'] === '') {

                $validator->addError(
                    'check_in',
                    'Check-in time is required.'
                );
            }

            if ($data['check_out'] === '') {

                $validator->addError(
                    'check_out',
                    'Check-out time is required.'
                );
            }
        }


        /*
        |--------------------------------------------------------------------------
        | Absent / Leave
        |--------------------------------------------------------------------------
        */

        if (
            $data['status'] === 'absent'
            ||
            $data['status'] === 'leave'
        ) {

            if ($data['check_in'] !== '') {

                $validator->addError(
                    'check_in',
                    'Check-in must be empty for this status.'
                );
            }

            if ($data['check_out'] !== '') {

                $validator->addError(
                    'check_out',
                    'Check-out must be empty for this status.'
                );
            }
        }


        /*
        |--------------------------------------------------------------------------
        | Check-Out Must Be Later
        |--------------------------------------------------------------------------
        */

        if (
            $checkInValid
            &&
            $checkOutValid
            &&
            $data['check_in'] !== ''
            &&
            $data['check_out'] !== ''
        ) {

            if (
                $data['check_out']
                <=
                $data['check_in']
            ) {

                $validator->addError(
                    'check_out',
                    'Check-out must be later than check-in.'
                );
            }
        }


        /*
        |--------------------------------------------------------------------------
        | Return Validation Errors
        |--------------------------------------------------------------------------
        */

        if ($validator->fails()) {

            $this->json([

                'success' => false,

                'message' =>
                'Validation failed',

                'errors' =>
                $validator->errors()

            ], 422);
        }


        /*
        |--------------------------------------------------------------------------
        | Check Employee
        |--------------------------------------------------------------------------
        */

        $employee =
            $this->employeeModel
            ->findById(
                $data['employee_id']
            );

        if (!$employee) {

            $this->json([

                'success' => false,

                'message' =>
                'Employee not found.'

            ], 404);
        }


        /*
        |--------------------------------------------------------------------------
        | Check Approved Leave
        |--------------------------------------------------------------------------
        */

        $approvedLeave =
            $this->attendanceModel
            ->findApprovedLeave(
                $data['employee_id'],
                $data['attendance_date']
            );


        /*
        |--------------------------------------------------------------------------
        | Approved Leave Protection
        |--------------------------------------------------------------------------
        */

        if ($approvedLeave) {

            /*
            |--------------------------------------------------------------------------
            | Admin cannot mark another attendance status
            |--------------------------------------------------------------------------
            */

            if ($data['status'] !== 'leave') {

                $this->json([

                    'success' => false,

                    'message' =>
                    'Attendance cannot be marked because this employee is on approved leave.',

                    'leave' => [

                        'id' =>
                        (int) $approvedLeave['id'],

                        'leave_type' =>
                        $approvedLeave['leave_type'],

                        'start_date' =>
                        $approvedLeave['start_date'],

                        'end_date' =>
                        $approvedLeave['end_date']

                    ]

                ], 422);
            }


            /*
            |--------------------------------------------------------------------------
            | Approved Leave Cannot Have Times
            |--------------------------------------------------------------------------
            */

            $data['check_in'] = '';

            $data['check_out'] = '';


            /*
            |--------------------------------------------------------------------------
            | Default Remarks
            |--------------------------------------------------------------------------
            */

            if ($data['remarks'] === '') {

                $data['remarks'] =
                    'Approved leave';
            }
        }


        /*
        |--------------------------------------------------------------------------
        | Find Existing Attendance
        |--------------------------------------------------------------------------
        */

        $existing =
            $this->attendanceModel
            ->findByEmployeeAndDate(
                $data['employee_id'],
                $data['attendance_date']
            );


        try {

            /*
            |--------------------------------------------------------------------------
            | Update Existing Attendance
            |--------------------------------------------------------------------------
            */

            if ($existing) {

                $this->attendanceModel
                    ->update(
                        (int) $existing['id'],
                        $data
                    );

                $this->json([

                    'success' =>
                    true,

                    'message' =>
                    'Attendance updated successfully.',

                    'action' =>
                    'updated'

                ]);
            }


            /*
            |--------------------------------------------------------------------------
            | Create Attendance
            |--------------------------------------------------------------------------
            */

            $id =
                $this->attendanceModel
                ->create($data);

            $this->json([

                'success' =>
                true,

                'message' =>
                'Attendance marked successfully.',

                'action' =>
                'created',

                'id' =>
                $id

            ]);
        } catch (PDOException $e) {

            $this->json([

                'success' =>
                false,

                'message' =>
                'Unable to save attendance.'

            ], 500);
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Attendance History
    |--------------------------------------------------------------------------
    */

    public function history(): void
    {
        $dateFrom =
            trim(
                $this->query(
                    'date_from',
                    ''
                )
            );

        $dateTo =
            trim(
                $this->query(
                    'date_to',
                    ''
                )
            );


        $employeeId = '';
        if (Auth::isAdmin()) {
            $employeeId =
                trim(
                    $this->query(
                        'employee_id',
                        ''
                    )
                );
        } elseif (Auth::isEmployee()) {
            $encypted = trim(
                $this->query(
                    'employee_id',
                    ''
                )
            );
            $employeeId = (string)UrlEncryptor::decrypt($encypted);
        }


        $status =
            trim(
                $this->query(
                    'status',
                    ''
                )
            );


        /*
        |--------------------------------------------------------------------------
        | Default Dates
        |--------------------------------------------------------------------------
        */

        if ($dateFrom === '') {

            $dateFrom =
                date('Y-m-01');
        }

        if ($dateTo === '') {

            $dateTo =
                date('Y-m-d');
        }


        /*
        |--------------------------------------------------------------------------
        | Validator
        |--------------------------------------------------------------------------
        */

        $validator =
            new Validator([

                'date_from' =>
                $dateFrom,

                'date_to' =>
                $dateTo

            ]);

        $validator
            ->required('date_from')
            ->required('date_to');


        /*
        |--------------------------------------------------------------------------
        | Validate Dates
        |--------------------------------------------------------------------------
        */

        $fromDate =
            DateTime::createFromFormat(
                'Y-m-d',
                $dateFrom
            );

        $toDate =
            DateTime::createFromFormat(
                'Y-m-d',
                $dateTo
            );

        if (
            !$fromDate
            ||
            $fromDate->format('Y-m-d')
            !== $dateFrom
        ) {

            $validator->addError(
                'date_from',
                'Invalid start date.'
            );
        }

        if (
            !$toDate
            ||
            $toDate->format('Y-m-d')
            !== $dateTo
        ) {

            $validator->addError(
                'date_to',
                'Invalid end date.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Date Range
        |--------------------------------------------------------------------------
        */

        if (
            $fromDate
            &&
            $toDate
            &&
            $fromDate > $toDate
        ) {

            $validator->addError(
                'date_to',
                'End date must be greater than or equal to start date.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Employee Filter
        |--------------------------------------------------------------------------
        */

        $employeeIdValue = null;

        if ($employeeId !== '') {

            if (
                !ctype_digit($employeeId)
                ||
                (int) $employeeId <= 0
            ) {

                $validator->addError(
                    'employee_id',
                    'Invalid employee.'
                );
            } else {

                $employeeIdValue =
                    (int) $employeeId;
            }
        }


        /*
        |--------------------------------------------------------------------------
        | Status Filter
        |--------------------------------------------------------------------------
        */

        $allowedStatuses = [

            'present',
            'absent',
            'half_day',
            'leave'

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
                'Invalid attendance status.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Validation Response
        |--------------------------------------------------------------------------
        */

        if ($validator->fails()) {

            $this->json([

                'success' =>
                false,

                'message' =>
                'Validation failed',

                'errors' =>
                $validator->errors()

            ], 422);
        }


        /*
        |--------------------------------------------------------------------------
        | Get History
        |--------------------------------------------------------------------------
        */

        try {

            $records =
                $this->attendanceModel
                ->getHistory(
                    $dateFrom,
                    $dateTo,
                    $employeeIdValue,
                    $status !== ''
                        ? $status
                        : null
                );

            $this->json([

                'success' =>
                true,

                'message' =>
                'Attendance history loaded successfully.',

                'records' =>
                $records

            ]);
        } catch (PDOException $e) {

            $this->json([

                'success' =>
                false,

                'message' =>
                'Unable to load attendance history.'

            ], 500);
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Attendance Summary
    |--------------------------------------------------------------------------
    */

    public function summary(): void
    {
        $dateFrom =
            trim(
                $this->query(
                    'date_from',
                    ''
                )
            );

        $dateTo =
            trim(
                $this->query(
                    'date_to',
                    ''
                )
            );

        $employeeId =
            trim(
                $this->query(
                    'employee_id',
                    ''
                )
            );



        /*
        |--------------------------------------------------------------------------
        | Default Dates
        |--------------------------------------------------------------------------
        */

        if ($dateFrom === '') {

            $dateFrom =
                date('Y-m-01');
        }

        if ($dateTo === '') {

            $dateTo =
                date('Y-m-d');
        }


        /*
        |--------------------------------------------------------------------------
        | Validator
        |--------------------------------------------------------------------------
        */

        $validator =
            new Validator([

                'date_from' =>
                $dateFrom,

                'date_to' =>
                $dateTo

            ]);

        $validator
            ->required('date_from')
            ->required('date_to');


        /*
        |--------------------------------------------------------------------------
        | Validate Dates
        |--------------------------------------------------------------------------
        */

        $fromDate =
            DateTime::createFromFormat(
                'Y-m-d',
                $dateFrom
            );

        $toDate =
            DateTime::createFromFormat(
                'Y-m-d',
                $dateTo
            );

        if (
            !$fromDate
            ||
            $fromDate->format('Y-m-d')
            !== $dateFrom
        ) {

            $validator->addError(
                'date_from',
                'Invalid start date.'
            );
        }

        if (
            !$toDate
            ||
            $toDate->format('Y-m-d')
            !== $dateTo
        ) {

            $validator->addError(
                'date_to',
                'Invalid end date.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Date Range
        |--------------------------------------------------------------------------
        */

        if (
            $fromDate
            &&
            $toDate
            &&
            $fromDate > $toDate
        ) {

            $validator->addError(
                'date_to',
                'End date must be greater than or equal to start date.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Employee Filter
        |--------------------------------------------------------------------------
        */

        $employeeIdValue = null;

        if ($employeeId !== '') {

            if (
                !ctype_digit($employeeId)
                ||
                (int) $employeeId <= 0
            ) {

                $validator->addError(
                    'employee_id',
                    'Invalid employee.'
                );
            } else {

                $employeeIdValue =
                    (int) $employeeId;
            }
        }


        /*
        |--------------------------------------------------------------------------
        | Validation Response
        |--------------------------------------------------------------------------
        */

        if ($validator->fails()) {

            $this->json([

                'success' =>
                false,

                'message' =>
                'Validation failed',

                'errors' =>
                $validator->errors()

            ], 422);
        }


        /*
        |--------------------------------------------------------------------------
        | Get Summary
        |--------------------------------------------------------------------------
        */

        try {



            $summary =
                $this->attendanceModel
                ->getSummary(
                    $dateFrom,
                    $dateTo,
                    $employeeIdValue
                );




            /*
            |--------------------------------------------------------------------------
            | Calculate Working Days
            |--------------------------------------------------------------------------
            |
            | Monday - Friday.
            |
            | This is separate from total_marked.
            |
            */

            $workingDays = 0;

            $currentDate =
                new DateTime($dateFrom);

            $endDate =
                new DateTime($dateTo);

            while (
                $currentDate <= $endDate
            ) {

                $dayOfWeek =
                    (int) $currentDate->format('N');

                if ($dayOfWeek <= 5) {

                    $workingDays++;
                }

                $currentDate->modify('+1 day');
            }


            /*
            |--------------------------------------------------------------------------
            | Attach Working Days
            |--------------------------------------------------------------------------
            */

            foreach ($summary as &$record) {

                $record['working_days'] =
                    $workingDays;
            }

            unset($record);


            /*
            |--------------------------------------------------------------------------
            | Response
            |--------------------------------------------------------------------------
            */

            $this->json([

                'success' =>
                true,

                'message' =>
                'Attendance summary loaded successfully.',

                'summary' =>
                $summary

            ]);
        } catch (PDOException $e) {

            $this->json([

                'success' =>
                false,

                'message' =>
                'Unable to load attendance summary.' . $e->getMessage()

            ], 500);
        }
    }


    public function todayAttendence(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Get Logged In Employee ID
        |--------------------------------------------------------------------------
        */

        $employeeId = Auth::employeeId();
 


        if (!$employeeId) {

            $this->json([

                'success' => false,

                'message' =>
                'Employee not found.'

            ], 401);
        }


        /*
        |--------------------------------------------------------------------------
        | Get Employee
        |--------------------------------------------------------------------------
        |
        | Auth::employeeId() returns something like:
        | EMP001
        |
        */

        $employee =
            $this->employeeModel
            ->findByEmployee_id($employeeId);





        if (!$employee) {

            $this->json([

                'success' => false,

                'message' =>
                'Employee record not found.'

            ], 404);
        }


        /*
        |--------------------------------------------------------------------------
        | Today's Date
        |--------------------------------------------------------------------------
        */
        date_default_timezone_set('Asia/Kolkata');

        $today =
            date('Y-m-d');


        /*
        |--------------------------------------------------------------------------
        | Find Today's Attendance
        |--------------------------------------------------------------------------
        */

        $attendance =
            $this->attendanceModel
            ->findByEmployeeAndDate(
                (int) $employee['id'],
                $today
            );


        /*
        |--------------------------------------------------------------------------
        | Prepare Attendance
        |--------------------------------------------------------------------------
        */
           
        if ($attendance) {

            $employee['attendance'] = [

                'id' =>
                (int) $attendance['id'],

                'status' =>
                $attendance['status'],

                'check_in' =>
                $attendance['check_in'],

                'check_out' =>
                $attendance['check_out'],

                'remarks' =>
                $attendance['remarks']

            ];
        } else {
            $leave =
                $this->leaveModel
                ->findApprovedLeaveByEmployeeAndDate(
                    (int) $employee['id'],
                    $today
                );
             

            /*
            |--------------------------------------------------------------------------
            | No Attendance
            |--------------------------------------------------------------------------
            */

            if ($leave) {
                  

                $employee['attendance'] = null;

                $employee['leave'] = [

                    'id' =>
                    (int) $leave['id'],

                    'leave_type' =>
                    $leave['leave_type'],

                    'start_date' =>
                    $leave['start_date'],

                    'end_date' =>
                    $leave['end_date'],

                    'reason' =>
                    $leave['reason'],

                    'status' =>
                    $leave['status'],

                    'admin_remarks' =>
                    $leave['admin_remarks']

                ];
            } else {

                /*
                |--------------------------------------------------------------------------
                | No Attendance And No Leave
                |--------------------------------------------------------------------------
                */

                $employee['attendance'] = null;

                $employee['leave'] = null;
            }
        }
                    

        /*
        |--------------------------------------------------------------------------
        | JSON Response
        |--------------------------------------------------------------------------
        */

        $this->json([

            'success' =>
            true,

            'date' =>
            $today,

            'employee' => [

                'id' =>
                (int) $employee['id'],

                'employee_id' =>
                $employee['employee_id'],

                'first_name' =>
                $employee['first_name'],

                'last_name' =>
                $employee['last_name'],

                'department' =>
                $employee['department'],
                'email' =>
                $employee['email'],

                'designation' =>
                $employee['designation'],

                'attendance' =>
                $employee['attendance']??[],
                'encrypt_id' => UrlEncryptor::encrypt($employee['id']),
                'leave'=>$employee['leave']??[],


            ]

        ]);
    }

    private function getAttendanceAPI(int $employeeId,string $startDate ,string $endDate):void{
        if ($employeeId <= 0) {

            $this->json([
                'success' => false,
                'message' => 'Invalid employee ID.'
            ], 400);
        }

       

        if ($startDate !== '') {

            $date = DateTime::createFromFormat(
                'Y-m-d',
                $startDate
            );

            if (
                !$date ||
                $date->format('Y-m-d') !== $startDate
            ) {

                $this->json([
                    'success' => false,
                    'message' => 'Invalid start date. Use YYYY-MM-DD format.'
                ], 400);
            }
        }


        if ($endDate !== '') {

            $date = DateTime::createFromFormat(
                'Y-m-d',
                $endDate
            );

            if (
                !$date ||
                $date->format('Y-m-d') !== $endDate
            ) {

                $this->json([
                    'success' => false,
                    'message' => 'Invalid end date. Use YYYY-MM-DD format.'
                ], 400);
            }
        }


        /*
        |--------------------------------------------------------------------------
        | Validate Date Range Order
        |--------------------------------------------------------------------------
        */

        if (
            $startDate !== '' &&
            $endDate !== '' &&
            $startDate > $endDate
        ) {

            $this->json([
                'success' => false,
                'message' => 'Start date cannot be greater than end date.'
            ], 400);
        }


        
        
        $employee = $this->employeeModel->findByid(
            $employeeId
        );


        if (!$employee) {

            $this->json([
                'success' => false,
                'message' => 'Employee not found.'
            ], 404);
        }

        

        $attendanceData =
            $this->attendanceModel
                ->getAttendancesindi($employeeId,$startDate,$endDate);

        /*
        |--------------------------------------------------------------------------
        | Employee / Attendance Not Found
        |--------------------------------------------------------------------------
        */

        if (empty($attendanceData)) {
            $this->json([
                'success' => false,
                'message' => 'Employee or attendance information not found.'
            ], 404);
        }


        /*
        |--------------------------------------------------------------------------
        | Return Response
        |--------------------------------------------------------------------------
        */

        $this->json([
            'success' => true,

            'employee' =>
                $attendanceData['employee'],
            'today_attendance' =>
                $attendanceData['today_attendance'],

            'attendance_summary' =>
                $attendanceData['attendance_summary'],

            'attendance_history' =>
                $attendanceData['attendance_history']
        ]);

    }

    public function get_indi_attendance():void{
        $employeeId = (int) Auth::emp_id();
            $startDate = (string) (
            $this->query('start_date')?? 0
            );
            $endDate  = (string) (
            $this->query('end_date') ?? 0
            );


        if (!$employeeId) {

            $this->json([
                'success' => false,
                'message' => 'Employee ID is required.'
            ], 400);
        }


        $this->getAttendanceAPI(
            $employeeId,
            $startDate,
            $endDate
        );

    }

    public function get_indi_attendance_for_AI(): void
    {
        $employeeId = (int) (
            $_GET['employee_id'] ?? 0
        );
        $startDate = (string) (
           $this->query('start_date')?? 0
        );
        $endDate  = (string) (
           $this->query('end_date') ?? 0
        );


        if (!$employeeId) {

            $this->json([
                'success' => false,
                'message' => 'Employee ID is required.'
            ], 400);
        }


        $this->getAttendanceAPI(
            $employeeId,
            $startDate,
            $endDate 
        );
    }
}

<?php

class AdminDashController extends Controller
{
    private PDO $db;

    private Employee $employeeModel;
    private User $userModel;
    private Leave $leaveModel;
    private Attendance $attendanceModel;
    private Department $departmentModel;
    private Designation $designationModel;


    public function __construct(PDO $db)
    {
        $this->db = $db;

        $this->employeeModel =
            new Employee($this->db);

        $this->userModel =
            new User($this->db);

        $this->leaveModel =
            new Leave($this->db);

        $this->attendanceModel =
            new Attendance($this->db);

        $this->departmentModel =
            new Department($this->db);

        $this->designationModel =
            new Designation($this->db);
    }


    // =====================================================
    // ADMIN DASHBOARD
    // =====================================================

    public function index(): void
    {
        $this->dashboard();
    }


    // =====================================================
    // DASHBOARD STATISTICS
    // =====================================================

    public function dashboard(): void
    {
        $totalEmployees =
            $this->employeeModel->count();

        $pendingLeaves =
            $this->leaveModel->countPending();

        $totalDepartments =
            $this->departmentModel->count();

        $totalDesignations =
            $this->designationModel->count();


        $this->json([

            'success' => true,

            'statistics' => [

                'employees' =>
                    $totalEmployees,

                'pending_leaves' =>
                    $pendingLeaves,

                'departments' =>
                    $totalDepartments,

                'designations' =>
                    $totalDesignations

            ]

        ]);
    }


    // =====================================================
    // EMPLOYEES
    // Used for Attendance Employee Filter
    // =====================================================

    public function employees(): void
    {
        $employees =
            $this->employeeModel->getAllActive();


        $result = [];


        foreach ($employees as $employee) {

            $result[] = [

                'id' =>
                    $employee['id'],

                'name' =>
                    trim(
                        ($employee['first_name'] ?? '') .
                        ' ' .
                        ($employee['last_name'] ?? '')
                    )

            ];
        }


        $this->json([

            'success' => true,

            'employees' =>
                $result

        ]);
    }


    // =====================================================
    // DEPARTMENTS
    // Used for Department Filters
    // =====================================================

    public function departments(): void
    {
        $departments =
            $this->departmentModel->getAll();


        $result = [];


        foreach ($departments as $department) {

            $result[] = [

                'id' =>
                    $department['id'],

                'name' =>
                    $department['name']

            ];
        }


        $this->json([

            'success' => true,

            'departments' =>
                $result

        ]);
    }


    // =====================================================
    // ATTENDANCE OVERVIEW
    // =====================================================

    public function attendanceOverview(): void
    {
        $period =
            $this->query(
                'period',
                'week'
            );


        $employeeId =
            trim(
                $this->query(
                    'employee_id',
                    ''
                )
            );


        /*
        |-----------------------------------------------------
        | Validate Period
        |-----------------------------------------------------
        */

        $allowedPeriods = [

            'week',
            'month',
            'year'

        ];


        if (
            !in_array(
                $period,
                $allowedPeriods,
                true
            )
        ) {

            $period = 'week';

        }


        /*
        |-----------------------------------------------------
        | Validate Employee ID
        |-----------------------------------------------------
        */

        if ($employeeId !== '') {

            if (
                !ctype_digit($employeeId) ||
                (int) $employeeId <= 0
            ) {

                $this->json([

                    'success' => false,

                    'message' =>
                        'Invalid employee.'

                ]);

                return;
            }


            $employeeId =
                (int) $employeeId;

        } else {

            $employeeId = null;

        }


        /*
        |-----------------------------------------------------
        | Get Attendance Data
        |-----------------------------------------------------
        */

        $attendance =
            $this->attendanceModel
                ->getOverview(
                    $period,
                    $employeeId
                );


        /*
        |-----------------------------------------------------
        | Response
        |-----------------------------------------------------
        */

        $this->json([

            'success' => true,

            'labels' =>
                $attendance['labels'] ?? [],

            'data' => [

                'present' =>
                    $attendance['present'] ?? [],

                'absent' =>
                    $attendance['absent'] ?? [],

                'leave' =>
                    $attendance['leave'] ?? [],
                'half_day' =>
                    $attendance['half_day'] ?? [],

            ]

        ]);
    }


    // =====================================================
    // TODAY ATTENDANCE
    // =====================================================

    public function todayAttendance(): void
    {
        $departmentId =
            trim(
                $this->query(
                    'department_id',
                    ''
                )
            );


        /*
        |-----------------------------------------------------
        | Validate Department
        |-----------------------------------------------------
        */

        if ($departmentId !== '') {

            if (
                !ctype_digit($departmentId) ||
                (int) $departmentId <= 0
            ) {

                $this->json([

                    'success' => false,

                    'message' =>
                        'Invalid department.'

                ]);

                return;
            }


            $departmentId =
                (int) $departmentId;

        } else {

            $departmentId = null;

        }


        /*
        |-----------------------------------------------------
        | Get Today's Attendance
        |-----------------------------------------------------
        */
        // echo $departmentId;
        // exit;

        $attendance =
            $this->attendanceModel
                ->getTodayDistribution(
                    $departmentId
                );


        /*
        |-----------------------------------------------------
        | Response
        |-----------------------------------------------------
        */

        $this->json([

            'success' => true,

            'attendance' => [

                'total' =>
                    $attendance['total'] ?? 0,

                'present' =>
                    $attendance['present'] ?? 0,

                'absent' =>
                    $attendance['absent'] ?? 0,

                'leave' =>
                    $attendance['leave'] ?? 0,
                

                'not_marked' =>
                    $attendance['not_marked'] ?? 0


            ]

        ]);
    }


    // =====================================================
    // DEPARTMENT-WISE EMPLOYEES
    // =====================================================

    public function departmentEmployees(): void
    {
        $departmentId =
            trim(
                $this->query(
                    'department_id',
                    ''
                )
            );


        /*
        |-----------------------------------------------------
        | Validate Department
        |-----------------------------------------------------
        */

        if ($departmentId !== '') {

            if (
                !ctype_digit($departmentId) ||
                (int) $departmentId <= 0
            ) {

                $this->json([

                    'success' => false,

                    'message' =>
                        'Invalid department.'

                ]);

                return;
            }


            $departmentId =
                (int) $departmentId;

        } else {

            $departmentId = null;

        }


        /*
        |-----------------------------------------------------
        | Get Department Employee Data
        |-----------------------------------------------------
        */

        $employees =
            $this->employeeModel
                ->countByDepartment(
                    $departmentId
                );


        /*
        |-----------------------------------------------------
        | Prepare Chart Data
        |-----------------------------------------------------
        */

        $labels = [];

        $data = [];


        foreach ($employees as $employee) {

            $labels[] =
                $employee['department_name'];

            $data[] =
                (int) $employee['employee_count'];

        }


        /*
        |-----------------------------------------------------
        | Response
        |-----------------------------------------------------
        */

        $this->json([

            'success' => true,

            'labels' =>
                $labels,

            'data' =>
                $data

        ]);
    }
}

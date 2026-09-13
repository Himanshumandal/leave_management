<?php

class EmployeeDashController extends Controller
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

        $this->employeeModel = new Employee($this->db);
        $this->userModel = new User($this->db);
        $this->leaveModel = new Leave($this->db);
        $this->attendanceModel = new Attendance($this->db);
        $this->departmentModel = new Department($this->db);
        $this->designationModel = new Designation($this->db);
    }


    // =====================================================
    // MAIN DASHBOARD
    // =====================================================

    public function index(): void
    {
        $employeeDbId = $this->getEmployeeDbId();


        // -------------------------------------------------
        // EMPLOYEE
        // -------------------------------------------------

        $employeeData = $this->getEmployeeData(
            $employeeDbId
        );
   


        // -------------------------------------------------
        // CURRENT MONTH STATISTICS
        // -------------------------------------------------

        $statistics =
            $this->attendanceModel
                ->getEmployeeMonthlyStatistics(
                    $employeeDbId
                );
        
        
        
     

        // -------------------------------------------------
        // LEAVE STATISTICS
        // -------------------------------------------------

        $leaveStatistics =

            $this->leaveModel
                ->getEmployeeLeaveStatistics(
                    $employeeDbId
                );
    


        // -------------------------------------------------
        // TODAY
        // -------------------------------------------------

        $today =
            $this->attendanceModel
                ->getTodayAttendance(
                    $employeeDbId
                );


        // -------------------------------------------------
        // RESPONSE
        // -------------------------------------------------

        jsonResponse([

            'success' => true,

            'data' => [

                'employee' => $employeeData,


                'statistics' => [

                    'attendance_percentage' =>
                        (float) (
                            $statistics['attendance_percentage']
                            ?? 0
                        ),

                    'present_days' =>
                        (int) (
                            $statistics['present_days']
                            ?? 0
                        ),

                    'absent_days' =>
                        (int) (
                            $statistics['absent_days']
                            ?? 0
                        ),

                    'half_days' =>
                        (int) (
                            $statistics['half_days']
                            ?? 0
                        ),

                    'leave_days' =>
                        (int) (
                            $statistics['leave_days']
                            ?? 0
                        ),

                    'leave_balance' =>
                        (int) (
                            $employeeData['current_leave_pending']
                            ?? 0
                        ),

                    'pending_requests' =>
                        (int) (
                            $leaveStatistics['pending_requests']
                            ?? 0
                        )
                ],


                'today' => $today
            ]
        ]);
    }


    // =====================================================
    // ATTENDANCE TREND
    //
    // week  = current week, every day
    // month = current month, every week
    // year  = current year, every month
    // =====================================================

    public function attendanceTrend(): void
    {
        $employeeDbId = $this->getEmployeeDbId();


        $period = strtolower(
            trim($_GET['period'] ?? 'week')
        );


        $allowedPeriods = [
            'week',
            'month',
            'year'
        ];


        if (!in_array(
            $period,
            $allowedPeriods,
            true
        )) {

            $period = 'week';
        }


        $data =
            $this->attendanceModel
                ->getEmployeeAttendanceTrend(
                    $employeeDbId,
                    $period
                );


        jsonResponse([

            'success' => true,

            'period' => $period,

            'data' => $data

        ]);
    }


    // =====================================================
    // ATTENDANCE STATUS
    //
    // present
    // absent
    // leave
    // half_day
    // =====================================================

    public function attendanceStatus(): void
    {
        $employeeDbId = $this->getEmployeeDbId();


        $status = strtolower(
            trim($_GET['status'] ?? 'present')
        );


        $allowedStatuses = [
            'present',
            'absent',
            'leave',
            'half_day'
        ];


        if (!in_array(
            $status,
            $allowedStatuses,
            true
        )) {

            $status = '';
        }


        $data =
            $this->attendanceModel
                ->getEmployeeAttendanceStatus(
                    $employeeDbId,
                    $status
                );


        jsonResponse([

            'success' => true,

            'status' => $status,

            'data' => $data

        ]);
    }


    // =====================================================
    // WORKING HOURS
    //
    // week  = current week, every day
    // month = current month, every week
    // year  = current year, every month
    // =====================================================

    public function workingHours(): void
    {
        $employeeDbId = $this->getEmployeeDbId();


        $period = strtolower(
            trim($_GET['period'] ?? 'week')
        );


        $allowedPeriods = [
            'week',
            'month',
            'year'
        ];


        if (!in_array(
            $period,
            $allowedPeriods,
            true
        )) {

            $period = 'week';
        }


        $data =
            $this->attendanceModel
                ->getEmployeeWorkingHours(
                    $employeeDbId,
                    $period
                );


        jsonResponse([

            'success' => true,

            'period' => $period,

            'data' => $data

        ]);
    }


    // =====================================================
    // GET EMPLOYEE DATABASE ID
    // =====================================================

    private function getEmployeeDbId(): int
    {
        $employeeId = Auth::employeeId();


        $employee =
            $this->employeeModel
                ->findByEmployee_id(
                    $employeeId
                );


        if (!$employee) {

            jsonResponse([

                'success' => false,

                'message' => 'Employee not found.'

            ], 404);
        }


        return (int) $employee['id'];
    }


    // =====================================================
    // EMPLOYEE DATA
    // =====================================================

    private function getEmployeeData(
        int $employeeDbId
    ): array {

    

        $employee =
            $this->employeeModel
                ->findById(
                    $employeeDbId
                );

   



        if (!$employee) {

            return [];
        }


        return [

            'name' =>
                $employee['name']
                ?? 'Employee',

            'employee_id' =>
                $employee['employee_id']
                ?? null,

            'department' =>
                $employee['department']
                ?? null,

            'designation' =>
                $employee['designation']
                ?? null,

            'status' =>
                $employee['status']
                ?? null,
            'max_total_leave' =>
                $employee['max_total_leave']
                ?? null,

            'max_casual_leave' =>
                $employee['max_casual_leave']
                ?? null,

            'max_sick_leave' =>
                $employee['max_sick_leave']
                ?? null,

            'max_annual_leave' =>
                $employee['max_annual_leave']
                ?? null,

            'current_leave_pending' =>
                $employee['current_leave_pending']
                ?? null,

            'casual_pending' =>
                $employee['casual_pending']
                ?? null,

            'sick_pending' =>
                $employee['sick_pending']
                ?? null,

            'annual_pending' =>
                $employee['annual_pending']
                ?? null

        ];
    }
}

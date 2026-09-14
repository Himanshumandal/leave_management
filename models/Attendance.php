<?php

class Attendance extends Model
{

    /**
     * Get employee attendance statistics for current month.
     */
public function getEmployeeMonthlyStatistics(
    int $employeeId
): array {



    $sql = "
        SELECT
            COUNT(*) AS total_days,

            SUM(
                CASE
                    WHEN status = 'present'
                    THEN 1
                    ELSE 0
                END
            ) AS present_days,

            SUM(
                CASE
                    WHEN status = 'absent'
                    THEN 1
                    ELSE 0
                END
            ) AS absent_days,

            SUM(
                CASE
                    WHEN status IN ('half_day', 'half day')
                    THEN 1
                    ELSE 0
                END
            ) AS half_days,

            SUM(
                CASE
                    WHEN status = 'leave'
                    THEN 1
                    ELSE 0
                END
            ) AS leave_days

        FROM attendance

        WHERE employee_id = :employee_id

        AND MONTH(attendance_date) = MONTH(CURDATE())

        AND YEAR(attendance_date) = YEAR(CURDATE())
    ";

    $stmt = $this->db->prepare($sql);

    $stmt->execute([
        ':employee_id' => $employeeId
    ]);

    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    $totalDays = (int) ($result['total_days'] ?? 0);
    $presentDays = (int) ($result['present_days'] ?? 0);
    $absentDays = (int) ($result['absent_days'] ?? 0);
    $halfDays = (int) ($result['half_days'] ?? 0);
    $leaveDays = (int) ($result['leave_days'] ?? 0);

    /*
    |--------------------------------------------------------------------------
    | Attendance Percentage
    |--------------------------------------------------------------------------
    |
    | Half day = 0.5 attendance
    |
    */

    $attendancePercentage = 0;

    if ($totalDays > 0) {
        $attendancePercentage = (
            ($presentDays + ($halfDays * 0.5))
            / $totalDays
        ) * 100;
    }

    return [

        // Total attendance records
        'total_days' => $totalDays,

        // Present
        'present_days' => $presentDays,

        // Absent
        'absent_days' => $absentDays,

        // Half day
        'half_days' => $halfDays,

        // Leave
        'leave_days' => $leaveDays,

        // Percentage
        'attendance_percentage' => round(
            $attendancePercentage,
            2
        )
    ];
}

    public function getEmployeeAttendanceOverview(
        int $employeeId,
        string $period = 'week'
    ): array {

        $startDate = null;


        // =====================================================
        // DATE RANGE
        // =====================================================

        if ($period === 'week') {

            $startDate =
                date(
                    'Y-m-d',
                    strtotime('-6 days')
                );
        } elseif ($period === 'month') {

            $startDate =
                date('Y-m-01');
        } elseif ($period === 'year') {

            $startDate =
                date('Y-01-01');
        } else {

            $period = 'week';

            $startDate =
                date(
                    'Y-m-d',
                    strtotime('-6 days')
                );
        }


        $sql = "
        SELECT
            attendance_date,
            status

        FROM attendance

        WHERE employee_id = :employee_id

        AND attendance_date >= :start_date

        AND attendance_date <= CURDATE()

        ORDER BY attendance_date ASC
    ";


        $stmt =
            $this->db->prepare($sql);

        $stmt->execute([
            ':employee_id' => $employeeId,
            ':start_date' => $startDate
        ]);


        $rows =
            $stmt->fetchAll(PDO::FETCH_ASSOC);


        $labels = [];

        $present = [];

        $absent = [];

        $halfDay = [];

        $leave = [];


        foreach ($rows as $row) {

            $labels[] =
                date(
                    'd M',
                    strtotime($row['attendance_date'])
                );


            $present[] =
                strtolower($row['status']) === 'present'
                ? 1
                : 0;


            $absent[] =
                strtolower($row['status']) === 'absent'
                ? 1
                : 0;


            $halfDay[] =
                in_array(
                    strtolower($row['status']),
                    ['half day', 'half_day'],
                    true
                )
                ? 1
                : 0;


            $leave[] =
                strtolower($row['status']) === 'leave'
                ? 1
                : 0;
        }


        return [

            'labels' => $labels,

            'data' => [

                'present' => $present,

                'absent' => $absent,

                'half_day' => $halfDay,

                'leave' => $leave
            ]
        ];
    }

    // =====================================================
    // ATTENDANCE TREND
    //
    // week  = current week, every day
    // month = current month, every week
    // year  = current year, every month
    // =====================================================

    public function getEmployeeAttendanceTrend(
        int $employeeId,
        string $period = 'week'
    ): array {

        $period = strtolower(trim($period));

        // -------------------------------------------------
        // WEEK
        // Monday -> Sunday
        // -------------------------------------------------

        if ($period === 'week') {

            $startDate = date(
                'Y-m-d',
                strtotime('monday this week')
            );

            $endDate = date(
                'Y-m-d',
                strtotime('sunday this week')
            );


            // Current week labels
            $labels = [];

            $present = [];
            $absent = [];
            $halfDay = [];
            $leave = [];


            // ---------------------------------------------
            // Attendance records
            // ---------------------------------------------

            $sql = "
            SELECT
                attendance_date,
                status
            FROM attendance
            WHERE employee_id = :employee_id
            AND attendance_date BETWEEN :start_date AND :end_date
            ORDER BY attendance_date ASC
        ";


            $stmt = $this->db->prepare($sql);

            $stmt->execute([

                ':employee_id' => $employeeId,

                ':start_date' => $startDate,

                ':end_date' => $endDate

            ]);


            $records = $stmt->fetchAll(
                PDO::FETCH_ASSOC
            );


            // Create date indexed records
            $attendance = [];

            foreach ($records as $record) {

                $attendance[$record['attendance_date']] = strtolower(
                    trim(
                        $record['status']
                    )
                );
            }


            // ---------------------------------------------
            // Approved leaves
            // ---------------------------------------------

            $leaveSql = "
            SELECT
                start_date,
                end_date,
                leave_type
            FROM leaves
            WHERE employee_id = :employee_id
            AND status = 'approved'
            AND start_date <= :end_date
            AND end_date >= :start_date
        ";


            $leaveStmt = $this->db->prepare(
                $leaveSql
            );


            $leaveStmt->execute([

                ':employee_id' => $employeeId,

                ':start_date' => $startDate,

                ':end_date' => $endDate

            ]);


            $leaveRecords =
                $leaveStmt->fetchAll(
                    PDO::FETCH_ASSOC
                );


            $approvedLeaves = [];


            foreach ($leaveRecords as $leaveRecord) {

                $leaveStart =
                    new DateTime(
                        $leaveRecord['start_date']
                    );

                $leaveEnd =
                    new DateTime(
                        $leaveRecord['end_date']
                    );


                while (
                    $leaveStart <= $leaveEnd
                ) {

                    $date =
                        $leaveStart->format('Y-m-d');


                    $approvedLeaves[$date] = true;


                    $leaveStart->modify('+1 day');
                }
            }


            // ---------------------------------------------
            // Generate EVERY day of current week
            // ---------------------------------------------

            $current =
                new DateTime($startDate);

            $end =
                new DateTime($endDate);


            while ($current <= $end) {

                $date =
                    $current->format('Y-m-d');


                $labels[] =
                    $current->format('d M');


                // Leave has priority
                if (
                    isset(
                        $approvedLeaves[$date]
                    )
                ) {

                    $present[] = 0;
                    $absent[] = 0;
                    $halfDay[] = 0;
                    $leave[] = 1;
                } else {

                    $status =
                        $attendance[$date]
                        ?? null;


                    $present[] =
                        $status === 'present'
                        ? 1
                        : 0;


                    $absent[] =
                        $status === 'absent'
                        ? 1
                        : 0;


                    $halfDay[] =
                        in_array(
                            $status,
                            [
                                'half day',
                                'half_day'
                            ],
                            true
                        )
                        ? 1
                        : 0;


                    $leave[] = 0;
                }


                $current->modify('+1 day');
            }


            return [

                'labels' => $labels,

                'data' => [

                    'present' => $present,

                    'absent' => $absent,

                    'half_day' => $halfDay,

                    'leave' => $leave

                ]

            ];
        }


        // =================================================
        // MONTH
        //
        // Current month
        // Group by week
        // =================================================

        if ($period === 'month') {

            $startDate =
                date(
                    'Y-m-01'
                );

            $endDate =
                date(
                    'Y-m-t'
                );


            // ---------------------------------------------
            // Attendance
            // ---------------------------------------------

            $sql = "
            SELECT
                attendance_date,
                status
            FROM attendance
            WHERE employee_id = :employee_id
            AND attendance_date BETWEEN :start_date AND :end_date
            ORDER BY attendance_date ASC
        ";


            $stmt = $this->db->prepare($sql);

            $stmt->execute([

                ':employee_id' => $employeeId,

                ':start_date' => $startDate,

                ':end_date' => $endDate

            ]);


            $records = $stmt->fetchAll(
                PDO::FETCH_ASSOC
            );


            // ---------------------------------------------
            // Prepare week buckets
            // ---------------------------------------------

            $weeks = [];


            $start =
                new DateTime($startDate);

            $end =
                new DateTime($endDate);


            while ($start <= $end) {

                $day =
                    (int) $start->format('j');


                $weekNumber =
                    (int) ceil($day / 7);


                if (!isset($weeks[$weekNumber])) {

                    $weeks[$weekNumber] = [

                        'present' => 0,

                        'absent' => 0,

                        'half_day' => 0,

                        'leave' => 0

                    ];
                }


                $start->modify('+1 day');
            }


            // ---------------------------------------------
            // Attendance into week buckets
            // ---------------------------------------------

            foreach ($records as $record) {

                $date =
                    new DateTime(
                        $record['attendance_date']
                    );


                $day =
                    (int) $date->format('j');


                $weekNumber =
                    (int) ceil($day / 7);


                $status =
                    strtolower(
                        trim(
                            $record['status']
                        )
                    );


                if ($status === 'present') {

                    $weeks[$weekNumber]['present']++;
                } elseif ($status === 'absent') {

                    $weeks[$weekNumber]['absent']++;
                } elseif (
                    in_array(
                        $status,
                        [
                            'half day',
                            'half_day'
                        ],
                        true
                    )
                ) {

                    $weeks[$weekNumber]['half_day']++;
                } elseif ($status === 'leave') {

                    $weeks[$weekNumber]['leave']++;
                }
            }


            $labels = [];

            $present = [];

            $absent = [];

            $halfDay = [];

            $leave = [];


            foreach ($weeks as $weekNumber => $values) {

                $labels[] =
                    'Week ' . $weekNumber;


                $present[] =
                    $values['present'];


                $absent[] =
                    $values['absent'];


                $halfDay[] =
                    $values['half_day'];


                $leave[] =
                    $values['leave'];
            }


            return [

                'labels' => $labels,

                'data' => [

                    'present' => $present,

                    'absent' => $absent,

                    'half_day' => $halfDay,

                    'leave' => $leave

                ]

            ];
        }


        // =================================================
        // YEAR
        //
        // Current year
        // Group by month
        // =================================================

        $startDate =
            date('Y-01-01');

        $endDate =
            date('Y-12-31');


        $sql = "
        SELECT
            attendance_date,
            status
        FROM attendance
        WHERE employee_id = :employee_id
        AND attendance_date BETWEEN :start_date AND :end_date
        AND attendance_date <= CURDATE()
        ORDER BY attendance_date ASC
    ";


        $stmt = $this->db->prepare($sql);

        $stmt->execute([

            ':employee_id' => $employeeId,

            ':start_date' => $startDate,

            ':end_date' => $endDate

        ]);


        $records = $stmt->fetchAll(
            PDO::FETCH_ASSOC
        );


        // ---------------------------------------------
        // Create all 12 months
        // ---------------------------------------------

        $months = [];

        for ($month = 1; $month <= 12; $month++) {

            $months[$month] = [

                'present' => 0,

                'absent' => 0,

                'half_day' => 0,

                'leave' => 0

            ];
        }


        // ---------------------------------------------
        // Put attendance into months
        // ---------------------------------------------

        foreach ($records as $record) {

            $date =
                new DateTime(
                    $record['attendance_date']
                );


            $month =
                (int) $date->format('n');


            $status =
                strtolower(
                    trim(
                        $record['status']
                    )
                );


            if ($status === 'present') {

                $months[$month]['present']++;
            } elseif ($status === 'absent') {

                $months[$month]['absent']++;
            } elseif (
                in_array(
                    $status,
                    [
                        'half day',
                        'half_day'
                    ],
                    true
                )
            ) {

                $months[$month]['half_day']++;
            } elseif ($status === 'leave') {

                $months[$month]['leave']++;
            }
        }


        $labels = [];

        $present = [];

        $absent = [];

        $halfDay = [];

        $leave = [];


        for ($month = 1; $month <= 12; $month++) {

            $labels[] =
                date(
                    'M',
                    mktime(
                        0,
                        0,
                        0,
                        $month,
                        1
                    )
                );


            $present[] =
                $months[$month]['present'];


            $absent[] =
                $months[$month]['absent'];


            $halfDay[] =
                $months[$month]['half_day'];


            $leave[] =
                $months[$month]['leave'];
        }


        return [

            'labels' => $labels,

            'data' => [

                'present' => $present,

                'absent' => $absent,

                'half_day' => $halfDay,

                'leave' => $leave

            ]

        ];
    }



    // =====================================================
    // ATTENDANCE STATUS
    //
    // Shows total count of selected status
    // Only records up to today
    // =====================================================

    public function getEmployeeAttendanceStatus(
        int $employeeId,
        string $status
    ): array {

        $status = strtolower(
            trim($status)
        );


        $allowedStatuses = [

            'present',
            'absent',
            'leave',
            'half_day'

        ];


        // =====================================================
        // ALL STATUS
        // =====================================================

        if ($status === '') {

            // ---------------------------------------------
            // Present / Absent / Half Day
            // ---------------------------------------------

            $sql = "
        SELECT
            status,
            COUNT(*) AS total
        FROM attendance
        WHERE employee_id = :employee_id
        AND attendance_date <= CURDATE()
        AND status IN (
            'present',
            'absent',
            'half_day'
        )
        GROUP BY status
    ";


            $stmt =
                $this->db->prepare($sql);


            $stmt->execute([

                ':employee_id' =>
                $employeeId

            ]);


            $attendanceRows =
                $stmt->fetchAll(
                    PDO::FETCH_ASSOC
                );


            // ---------------------------------------------
            // Default values
            // ---------------------------------------------

            $present = 0;

            $absent = 0;

            $halfDay = 0;


            // ---------------------------------------------
            // Assign attendance counts
            // ---------------------------------------------

            foreach ($attendanceRows as $row) {

                $rowStatus =
                    strtolower(
                        trim(
                            $row['status']
                        )
                    );


                $count =
                    (int) $row['total'];


                switch ($rowStatus) {

                    case 'present':

                        $present =
                            $count;

                        break;


                    case 'absent':

                        $absent =
                            $count;

                        break;


                    case 'half_day':

                        $halfDay =
                            $count;

                        break;
                }
            }


            // ---------------------------------------------
            // Approved Leave
            // ---------------------------------------------

            $leaveSql = "
        SELECT
            COALESCE(
                SUM(
                    DATEDIFF(
                        LEAST(
                            end_date,
                            CURDATE()
                        ),
                        start_date
                    ) + 1
                ),
                0
            ) AS total
        FROM leaves
        WHERE employee_id = :employee_id
        AND status = 'approved'
        AND start_date <= CURDATE()
    ";


            $leaveStmt =
                $this->db->prepare(
                    $leaveSql
                );


            $leaveStmt->execute([

                ':employee_id' =>
                $employeeId

            ]);


            $leave =
                (int) $leaveStmt->fetchColumn();


            // ---------------------------------------------
            // Return ALL
            // ---------------------------------------------

            return [

                'labels' => [

                    'Present',

                    'Absent',

                    'Leave',

                    'Half Day'

                ],

                'values' => [

                    $present,

                    $absent,

                    $leave,

                    $halfDay

                ]

            ];
        }


        // =====================================================
        // SPECIFIC STATUS VALIDATION
        // =====================================================

        if (
            !in_array(
                $status,
                $allowedStatuses,
                true
            )
        ) {

            return [

                'labels' => [],

                'values' => []

            ];
        }


        // =====================================================
        // PRESENT / ABSENT / HALF DAY
        // =====================================================

        if ($status !== 'leave') {

            $sql = "
        SELECT COUNT(*)
        FROM attendance
        WHERE employee_id = :employee_id
        AND status = :status
        AND attendance_date <= CURDATE()
    ";


            $stmt =
                $this->db->prepare($sql);


            $stmt->execute([

                ':employee_id' =>
                $employeeId,

                ':status' =>
                $status

            ]);


            $total =
                (int) $stmt->fetchColumn();
        }


        // =====================================================
        // LEAVE
        // =====================================================

        else {

            $sql = "
        SELECT
            COALESCE(
                SUM(
                    DATEDIFF(
                        LEAST(
                            end_date,
                            CURDATE()
                        ),
                        start_date
                    ) + 1
                ),
                0
            )
        FROM leaves
        WHERE employee_id = :employee_id
        AND status = 'approved'
        AND start_date <= CURDATE()
    ";


            $stmt =
                $this->db->prepare($sql);


            $stmt->execute([

                ':employee_id' =>
                $employeeId

            ]);


            $total =
                (int) $stmt->fetchColumn();
        }


        // =====================================================
        // LABEL
        // =====================================================

        $label =
            ucfirst(
                str_replace(
                    '_',
                    ' ',
                    $status
                )
            );


        // =====================================================
        // RETURN SPECIFIC STATUS
        // =====================================================

        return [

            'labels' => [

                $label

            ],

            'values' => [

                $total

            ]

        ];
    }



    // =====================================================
    // WORKING HOURS
    //
    // week  = current week, every day
    // month = current month, every week
    // year  = current year, every month
    // =====================================================

    public function getEmployeeWorkingHours(
        int $employeeId,
        string $period = 'week'
    ): array {

        $period =
            strtolower(
                trim($period)
            );


        // =================================================
        // WEEK
        // =================================================

        if ($period === 'week') {

            $startDate =
                date(
                    'Y-m-d',
                    strtotime('monday this week')
                );

            $endDate =
                date(
                    'Y-m-d',
                    strtotime('sunday this week')
                );


            $sql = "
            SELECT
                attendance_date,
                check_in,
                check_out
            FROM attendance
            WHERE employee_id = :employee_id
            AND attendance_date BETWEEN :start_date AND :end_date
            AND attendance_date <= CURDATE()
            AND check_in IS NOT NULL
            AND check_out IS NOT NULL
            ORDER BY attendance_date ASC
        ";


            $stmt =
                $this->db->prepare($sql);


            $stmt->execute([

                ':employee_id' =>
                $employeeId,

                ':start_date' =>
                $startDate,

                ':end_date' =>
                $endDate

            ]);


            $records =
                $stmt->fetchAll(
                    PDO::FETCH_ASSOC
                );


            $attendance = [];


            foreach ($records as $record) {

                $checkIn =
                    strtotime(
                        $record['check_in']
                    );

                $checkOut =
                    strtotime(
                        $record['check_out']
                    );


                if (
                    !$checkIn ||
                    !$checkOut ||
                    $checkOut <= $checkIn
                ) {

                    continue;
                }


                $attendance[$record['attendance_date']] =
                    round(
                        (
                            $checkOut -
                            $checkIn
                        ) / 3600,
                        2
                    );
            }


            $labels = [];

            $values = [];


            $current =
                new DateTime($startDate);

            $end =
                new DateTime($endDate);


            while ($current <= $end) {

                $date =
                    $current->format('Y-m-d');


                $labels[] =
                    $current->format('d M');


                // Future days = 0
                if ($date > date('Y-m-d')) {

                    $values[] = 0;
                } else {

                    $values[] =
                        $attendance[$date]
                        ?? 0;
                }


                $current->modify('+1 day');
            }


            return [

                'labels' => $labels,

                'values' => $values

            ];
        }


        // =================================================
        // MONTH
        //
        // Current month
        // Group by week
        // =================================================

        if ($period === 'month') {

            $startDate =
                date('Y-m-01');

            $endDate =
                date('Y-m-t');


            $sql = "
            SELECT
                attendance_date,
                check_in,
                check_out
            FROM attendance
            WHERE employee_id = :employee_id
            AND attendance_date BETWEEN :start_date AND :end_date
            AND attendance_date <= CURDATE()
            AND check_in IS NOT NULL
            AND check_out IS NOT NULL
            ORDER BY attendance_date ASC
        ";


            $stmt =
                $this->db->prepare($sql);


            $stmt->execute([

                ':employee_id' =>
                $employeeId,

                ':start_date' =>
                $startDate,

                ':end_date' =>
                $endDate

            ]);


            $records =
                $stmt->fetchAll(
                    PDO::FETCH_ASSOC
                );


            $weeks = [];


            // ---------------------------------------------
            // Create all weeks
            // ---------------------------------------------

            $start =
                new DateTime($startDate);

            $end =
                new DateTime($endDate);


            while ($start <= $end) {

                $day =
                    (int) $start->format('j');


                $weekNumber =
                    (int) ceil(
                        $day / 7
                    );


                if (!isset(
                    $weeks[$weekNumber]
                )) {

                    $weeks[$weekNumber] = 0;
                }


                $start->modify('+1 day');
            }


            // ---------------------------------------------
            // Calculate hours
            // ---------------------------------------------

            foreach ($records as $record) {

                $checkIn =
                    strtotime(
                        $record['check_in']
                    );

                $checkOut =
                    strtotime(
                        $record['check_out']
                    );


                if (
                    !$checkIn ||
                    !$checkOut ||
                    $checkOut <= $checkIn
                ) {

                    continue;
                }


                $hours =
                    (
                        $checkOut -
                        $checkIn
                    ) / 3600;


                $date =
                    new DateTime(
                        $record['attendance_date']
                    );


                $day =
                    (int) $date->format('j');


                $weekNumber =
                    (int) ceil(
                        $day / 7
                    );


                $weeks[$weekNumber] +=
                    round(
                        $hours,
                        2
                    );
            }


            $labels = [];

            $values = [];


            foreach (
                $weeks
                as $weekNumber => $hours
            ) {

                $labels[] =
                    'Week ' . $weekNumber;


                $values[] =
                    round(
                        $hours,
                        2
                    );
            }


            return [

                'labels' => $labels,

                'values' => $values

            ];
        }


        // =================================================
        // YEAR
        // =================================================

        $startDate =
            date('Y-01-01');

        $endDate =
            date('Y-12-31');


        $sql = "
        SELECT
            attendance_date,
            check_in,
            check_out
        FROM attendance
        WHERE employee_id = :employee_id
        AND attendance_date BETWEEN :start_date AND :end_date
        AND attendance_date <= CURDATE()
        AND check_in IS NOT NULL
        AND check_out IS NOT NULL
        ORDER BY attendance_date ASC
    ";


        $stmt =
            $this->db->prepare($sql);


        $stmt->execute([

            ':employee_id' =>
            $employeeId,

            ':start_date' =>
            $startDate,

            ':end_date' =>
            $endDate

        ]);


        $records =
            $stmt->fetchAll(
                PDO::FETCH_ASSOC
            );


        // ---------------------------------------------
        // Create 12 months
        // ---------------------------------------------

        $months = [];

        for (
            $month = 1;
            $month <= 12;
            $month++
        ) {

            $months[$month] = 0;
        }


        // ---------------------------------------------
        // Calculate monthly hours
        // ---------------------------------------------

        foreach ($records as $record) {

            $checkIn =
                strtotime(
                    $record['check_in']
                );

            $checkOut =
                strtotime(
                    $record['check_out']
                );


            if (
                !$checkIn ||
                !$checkOut ||
                $checkOut <= $checkIn
            ) {

                continue;
            }


            $hours =
                (
                    $checkOut -
                    $checkIn
                ) / 3600;


            $date =
                new DateTime(
                    $record['attendance_date']
                );


            $month =
                (int) $date->format('n');


            $months[$month] +=
                round(
                    $hours,
                    2
                );
        }


        $labels = [];

        $values = [];


        for (
            $month = 1;
            $month <= 12;
            $month++
        ) {

            $labels[] =
                date(
                    'M',
                    mktime(
                        0,
                        0,
                        0,
                        $month,
                        1
                    )
                );


            $values[] =
                round(
                    $months[$month],
                    2
                );
        }


        return [

            'labels' => $labels,

            'values' => $values

        ];
    }








    public function getTodayAttendance(
        int $employeeId
    ): array {

        // =====================================================
        // CHECK APPROVED LEAVE FIRST
        // =====================================================

        $leaveSql = "
        SELECT
            leave_type,
            reason,
            admin_remarks,
            start_date,
            end_date

        FROM leaves

        WHERE employee_id = :employee_id

        AND status = 'approved'

        AND CURDATE()
            BETWEEN start_date AND end_date

        ORDER BY id DESC

        LIMIT 1
    ";


        $leaveStmt =
            $this->db->prepare($leaveSql);

        $leaveStmt->execute([
            ':employee_id' => $employeeId
        ]);


        $leave =
            $leaveStmt->fetch(PDO::FETCH_ASSOC);


        // =====================================================
        // EMPLOYEE IS ON LEAVE
        // =====================================================

        if ($leave) {

            return [

                'status' => 'Leave',

                'check_in' => null,

                'check_out' => null,

                'working_hours' => null,
                'reason' => $leave['reason'],

                'remark' =>
                $leave['admin_remarks']
                    ?: $leave['reason']
                    ?: null,

                'leave_type' =>
                $leave['leave_type'] ?? null
            ];
        }


        // =====================================================
        // NORMAL ATTENDANCE
        // =====================================================

        $sql = "
        SELECT

            status,
            check_in,
            check_out

        FROM attendance

        WHERE employee_id = :employee_id

        AND attendance_date = CURDATE()

        LIMIT 1
    ";


        $stmt =
            $this->db->prepare($sql);

        $stmt->execute([
            ':employee_id' => $employeeId
        ]);


        $attendance =
            $stmt->fetch(PDO::FETCH_ASSOC);


        // =====================================================
        // NOT MARKED
        // =====================================================

        if (!$attendance) {

            return [

                'status' => 'Not Marked',

                'check_in' => null,

                'check_out' => null,

                'working_hours' => null,

                'remark' => null,

                'leave_type' => null
            ];
        }


        // =====================================================
        // WORKING HOURS
        // =====================================================

        $workingHours = null;


        if (
            !empty($attendance['check_in']) &&
            !empty($attendance['check_out'])
        ) {

            $workingHours =
                $this->calculateWorkingHours(
                    $attendance['check_in'],
                    $attendance['check_out']
                );
        }


        return [

            'status' =>
            $attendance['status']
                ?? 'Not Marked',

            'check_in' =>
            $attendance['check_in']
                ?? null,

            'check_out' =>
            $attendance['check_out']
                ?? null,

            'working_hours' =>
            $workingHours,

            'remark' => null,

            'leave_type' => null
        ];
    }

    /**
     * Get current month's daily attendance for employee.
     */
    public function getEmployeeMonthlyAttendanceGraph(
        int $employeeId
    ): array {

        $sql = "
        SELECT
            attendance_date,
            status

        FROM attendance

        WHERE employee_id = :employee_id

        AND MONTH(attendance_date) = MONTH(CURDATE())

        AND YEAR(attendance_date) = YEAR(CURDATE())

        ORDER BY attendance_date ASC
    ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            ':employee_id' => $employeeId
        ]);

        $records = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $labels = [];
        $present = [];
        $absent = [];
        $halfDay = [];
        $leave = [];

        foreach ($records as $record) {

            $labels[] = date(
                'd M',
                strtotime($record['attendance_date'])
            );

            $status = strtolower(
                trim($record['status'])
            );

            $present[] = $status === 'present' ? 1 : 0;

            $absent[] = $status === 'absent' ? 1 : 0;

            $halfDay[] =
                in_array(
                    $status,
                    ['half_day', 'half day'],
                    true
                )
                ? 1
                : 0;

            $leave[] =
                $status === 'leave'
                ? 1
                : 0;
        }

        return [
            'labels' => $labels,
            'present' => $present,
            'absent' => $absent,
            'half_day' => $halfDay,
            'leave' => $leave
        ];
    }





    /**
     * =====================================================
     * CURRENT MONTH PRESENT DAYS
     * =====================================================
     */
    public function getCurrentMonthPresentDays(
        int $employeeId
    ): int {

        $sql = "
            SELECT COUNT(*)

            FROM attendance

            WHERE employee_id = :employee_id

            AND MONTH(attendance_date) = MONTH(CURDATE())

            AND YEAR(attendance_date) = YEAR(CURDATE())

            AND status = 'Present'
        ";


        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            ':employee_id' => $employeeId
        ]);


        return (int) $stmt->fetchColumn();
    }


    /**
     * =====================================================
     * CURRENT MONTH ATTENDANCE %
     * =====================================================
     */
    public function getCurrentMonthAttendancePercentage(
        int $employeeId
    ): float {

        $sql = "
            SELECT

                COUNT(
                    CASE
                        WHEN status = 'Present'
                        THEN 1
                    END
                ) AS present_days,

                COUNT(*) AS total_days

            FROM attendance

            WHERE employee_id = :employee_id

            AND MONTH(attendance_date) = MONTH(CURDATE())

            AND YEAR(attendance_date) = YEAR(CURDATE())

            AND attendance_date <= CURDATE()
        ";


        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            ':employee_id' => $employeeId
        ]);


        $result =
            $stmt->fetch(PDO::FETCH_ASSOC);


        $present =
            (int) ($result['present_days'] ?? 0);


        $total =
            (int) ($result['total_days'] ?? 0);


        if ($total === 0) {
            return 0;
        }


        return round(
            ($present / $total) * 100,
            2
        );
    }


    /**
     * =====================================================
     * WORKING HOURS
     * =====================================================
     */
    private function calculateWorkingHours(
        string $checkIn,
        string $checkOut
    ): string {

        $start =
            new DateTime($checkIn);

        $end =
            new DateTime($checkOut);


        if ($end <= $start) {
            return '0h 0m';
        }


        $difference =
            $start->diff($end);


        return sprintf(
            '%dh %dm',
            ($difference->days * 24)
                + $difference->h,
            $difference->i
        );
    }

    public function getOverview(
        string $period = 'week',
        ?int $employeeId = null
    ): array {

        // =====================================================
        // TODAY
        // =====================================================

        $today = date('Y-m-d');


        // =====================================================
        // DATE RANGE
        // IMPORTANT:
        // Never allow the range to go beyond today.
        // =====================================================

        switch ($period) {

            case 'month':

                $startDate = date('Y-m-01');

                // Do NOT use date('Y-m-t')
                $endDate = $today;

                break;


            case 'year':

                $startDate = date('Y-01-01');

                // Do NOT use date('Y-12-31')
                $endDate = $today;

                break;


            case 'week':

            default:

                $startDate = date(
                    'Y-m-d',
                    strtotime('monday this week')
                );

                // Do NOT use Sunday
                $endDate = $today;

                break;
        }


        // =====================================================
        // ATTENDANCE DATA
        // =====================================================

        $sql = "
            SELECT
                employee_id,
                attendance_date,
                status

            FROM attendance

            WHERE attendance_date BETWEEN
                :start_date
                AND
                :end_date
        ";


        // =====================================================
        // EMPLOYEE FILTER
        // =====================================================

        if ($employeeId !== null) {

            $sql .= "
                AND employee_id = :employee_id
            ";
        }


        $sql .= "
            ORDER BY attendance_date ASC
        ";


        // =====================================================
        // PREPARE
        // =====================================================

        $stmt = $this->db->prepare($sql);


        // =====================================================
        // BIND DATES
        // =====================================================

        $stmt->bindValue(
            ':start_date',
            $startDate
        );

        $stmt->bindValue(
            ':end_date',
            $endDate
        );


        // =====================================================
        // BIND EMPLOYEE
        // =====================================================

        if ($employeeId !== null) {

            $stmt->bindValue(
                ':employee_id',
                $employeeId,
                PDO::PARAM_INT
            );
        }


        // =====================================================
        // EXECUTE
        // =====================================================

        $stmt->execute();


        // =====================================================
        // FETCH ATTENDANCE
        // =====================================================

        $attendanceRows = $stmt->fetchAll(
            PDO::FETCH_ASSOC
        );


        // =====================================================
        // APPROVED LEAVE DATA
        //
        // Only leaves that overlap with:
        // startDate -> today
        //
        // Future part of a leave is automatically ignored.
        // =====================================================

        $leaveSql = "
            SELECT
                employee_id,
                start_date,
                end_date

            FROM leaves

            WHERE status = 'approved'

            AND start_date <= :end_date

            AND end_date >= :start_date
        ";


        // =====================================================
        // EMPLOYEE FILTER FOR LEAVE
        // =====================================================

        if ($employeeId !== null) {

            $leaveSql .= "
                AND employee_id = :employee_id
            ";
        }


        // =====================================================
        // PREPARE LEAVE QUERY
        // =====================================================

        $leaveStmt = $this->db->prepare(
            $leaveSql
        );


        // =====================================================
        // BIND LEAVE DATES
        // =====================================================

        $leaveStmt->bindValue(
            ':start_date',
            $startDate
        );

        $leaveStmt->bindValue(
            ':end_date',
            $endDate
        );


        // =====================================================
        // BIND EMPLOYEE
        // =====================================================

        if ($employeeId !== null) {

            $leaveStmt->bindValue(
                ':employee_id',
                $employeeId,
                PDO::PARAM_INT
            );
        }


        // =====================================================
        // EXECUTE LEAVE QUERY
        // =====================================================

        $leaveStmt->execute();


        // =====================================================
        // FETCH LEAVES
        // =====================================================

        $leaveRows = $leaveStmt->fetchAll(
            PDO::FETCH_ASSOC
        );


        // =====================================================
        // PREPARE EMPLOYEE + DATE STATUS
        //
        // Key:
        // employee_id + date
        //
        // Example:
        // 5_2026-08-27
        // =====================================================

        $statusByEmployeeDate = [];


        // =====================================================
        // ADD ATTENDANCE
        // =====================================================

        foreach ($attendanceRows as $row) {

            $key =
                $row['employee_id']
                . '_'
                . $row['attendance_date'];


            $statusByEmployeeDate[$key] = [

                'employee_id' =>
                (int) $row['employee_id'],

                'date' =>
                $row['attendance_date'],

                'status' =>
                $row['status']
            ];
        }


        // =====================================================
        // ADD APPROVED LEAVES
        //
        // IMPORTANT:
        // Leave is also restricted to today.
        // =====================================================

        foreach ($leaveRows as $leave) {

            $employeeIdForLeave =
                (int) $leave['employee_id'];


            $leaveStart =
                new DateTime(
                    $leave['start_date']
                );


            $leaveEnd =
                new DateTime(
                    $leave['end_date']
                );


            // =================================================
            // PERIOD START
            // =================================================

            $periodStart =
                new DateTime(
                    $startDate
                );


            // =================================================
            // PERIOD END = TODAY
            // =================================================

            $periodEnd =
                new DateTime(
                    $endDate
                );


            // =================================================
            // If leave started before selected period,
            // start from period start.
            // =================================================

            if ($leaveStart < $periodStart) {

                $leaveStart =
                    clone $periodStart;
            }


            // =================================================
            // IMPORTANT:
            // If leave extends into the future,
            // stop it at TODAY.
            // =================================================

            if ($leaveEnd > $periodEnd) {

                $leaveEnd =
                    clone $periodEnd;
            }


            // =================================================
            // Include end date
            // =================================================

            $leaveEnd->modify('+1 day');


            // =================================================
            // CREATE DATE PERIOD
            // =================================================

            $datePeriod = new DatePeriod(

                $leaveStart,

                new DateInterval('P1D'),

                $leaveEnd
            );


            // =================================================
            // ADD LEAVE STATUS
            // =================================================

            foreach ($datePeriod as $date) {

                $dateString =
                    $date->format('Y-m-d');


                // Safety check:
                // Never add future dates.

                if ($dateString > $today) {

                    continue;
                }


                $key =
                    $employeeIdForLeave
                    . '_'
                    . $dateString;


                // =================================================
                // APPROVED LEAVE HAS PRIORITY
                // =================================================

                $statusByEmployeeDate[$key] = [

                    'employee_id' =>
                    $employeeIdForLeave,

                    'date' =>
                    $dateString,

                    'status' =>
                    'leave'
                ];
            }
        }


        // =====================================================
        // PREPARE DAILY CHART DATA
        // =====================================================

        $dailyData = [];


        foreach (
            $statusByEmployeeDate
            as $record
        ) {

            $date =
                $record['date'];


            $status =
                $record['status'];


            // =================================================
            // Safety:
            // Never process future dates.
            // =================================================

            if ($date > $today) {

                continue;
            }


            if (!isset($dailyData[$date])) {

                $dailyData[$date] = [

                    'present' => 0,

                    'absent' => 0,

                    'half_day' => 0,

                    'leave' => 0

                ];
            }


            // =================================================
            // STATUS
            // =================================================

            switch ($status) {

                case 'present':

                    $dailyData[$date]['present']++;

                    break;


                case 'absent':

                    $dailyData[$date]['absent']++;

                    break;


                case 'half_day':

                    $dailyData[$date]['half_day']++;

                    break;


                case 'leave':

                    $dailyData[$date]['leave']++;

                    break;
            }
        }


        // =====================================================
        // CREATE COMPLETE DATE RANGE
        //
        // startDate -> TODAY
        //
        // This prevents future dates from appearing.
        // =====================================================

        $labels = [];

        $present = [];

        $absent = [];

        $halfDay = [];

        $leave = [];


        $start =
            new DateTime(
                $startDate
            );


        $end =
            new DateTime(
                $today
            );


        // Include today
        $end->modify('+1 day');


        $periodDates = new DatePeriod(

            $start,

            new DateInterval('P1D'),

            $end
        );


        // =====================================================
        // BUILD CHART
        // =====================================================

        foreach ($periodDates as $date) {

            $dateString =
                $date->format('Y-m-d');


            // Extra safety
            if ($dateString > $today) {

                continue;
            }


            $labels[] =
                $dateString;


            $present[] =
                $dailyData[$dateString]['present']
                ?? 0;


            $absent[] =
                $dailyData[$dateString]['absent']
                ?? 0;


            $halfDay[] =
                $dailyData[$dateString]['half_day']
                ?? 0;


            $leave[] =
                $dailyData[$dateString]['leave']
                ?? 0;
        }


        // =====================================================
        // RETURN
        // =====================================================

        return [

            'labels' =>
            $labels,

            'present' =>
            $present,

            'absent' =>
            $absent,

            'half_day' =>
            $halfDay,

            'leave' =>
            $leave

        ];
    }





    public function getTodayDistribution(
        ?int $departmentId = null
    ): array {

        // =====================================================
        // TODAY
        // =====================================================

        $today = date('Y-m-d');


        // =====================================================
        // QUERY
        // =====================================================

        $sql = "
        SELECT

            COUNT(e.id) AS total,


            -- =============================================
            -- PRESENT
            -- =============================================

            SUM(
                CASE

                    -- Approved leave has priority
                    WHEN l.employee_id IS NOT NULL
                    THEN 0

                    WHEN a.status = 'present'
                    THEN 1

                    ELSE 0

                END
            ) AS present,


            -- =============================================
            -- ABSENT
            -- =============================================

            SUM(
                CASE

                    -- Approved leave has priority
                    WHEN l.employee_id IS NOT NULL
                    THEN 0

                    WHEN a.status = 'absent'
                    THEN 1

                    ELSE 0

                END
            ) AS absent,


            -- =============================================
            -- HALF DAY
            -- =============================================

            SUM(
                CASE

                    -- Approved leave has priority
                    WHEN l.employee_id IS NOT NULL
                    THEN 0

                    WHEN a.status = 'half_day'
                    THEN 1

                    ELSE 0

                END
            ) AS half_day,


            -- =============================================
            -- LEAVE
            -- =============================================

            SUM(
                CASE

                    -- Approved leave from leaves table
                    WHEN l.employee_id IS NOT NULL
                    THEN 1

                    -- Attendance manually marked as leave
                    WHEN a.status = 'leave'
                    THEN 1

                    ELSE 0

                END
            ) AS leave_count,


            -- =============================================
            -- NOT MARKED
            -- =============================================

            SUM(
                CASE

                    -- Employee is on approved leave
                    WHEN l.employee_id IS NOT NULL
                    THEN 0

                    -- No attendance record
                    WHEN a.id IS NULL
                    THEN 1

                    ELSE 0

                END
            ) AS not_marked


        FROM employees e


        -- =================================================
        -- TODAY'S ATTENDANCE
        -- =================================================

        LEFT JOIN attendance a

            ON a.employee_id = e.id

            AND a.attendance_date = :attendance_today


        -- =================================================
        -- APPROVED LEAVE FOR TODAY
        --
        -- start_date <= today
        -- end_date   >= today
        --
        -- Therefore:
        -- Future leave does NOT count.
        -- =================================================

        LEFT JOIN leaves l

            ON l.employee_id = e.id

            AND l.status = 'approved'

            AND l.start_date <= :leave_today_start

            AND l.end_date >= :leave_today_end


        WHERE e.status = 'active'
    ";


        // =====================================================
        // DEPARTMENT FILTER
        // =====================================================

        if ($departmentId !== null) {

            $sql .= "
            AND e.department_id = :department_id
        ";
        }


        // =====================================================
        // PREPARE
        // =====================================================

        $stmt = $this->db->prepare($sql);


        // =====================================================
        // BIND TODAY
        //
        // IMPORTANT:
        // We use DIFFERENT parameter names.
        //
        // This prevents HY093.
        // =====================================================

        $stmt->bindValue(
            ':attendance_today',
            $today
        );

        $stmt->bindValue(
            ':leave_today_start',
            $today
        );

        $stmt->bindValue(
            ':leave_today_end',
            $today
        );


        // =====================================================
        // BIND DEPARTMENT
        // =====================================================

        if ($departmentId !== null) {

            $stmt->bindValue(
                ':department_id',
                $departmentId,
                PDO::PARAM_INT
            );
        }


        // =====================================================
        // EXECUTE
        // =====================================================

        $stmt->execute();


        // =====================================================
        // FETCH
        // =====================================================

        $result = $stmt->fetch(
            PDO::FETCH_ASSOC
        );


        // =====================================================
        // RETURN
        // =====================================================

        return [

            'total' =>
            (int) (
                $result['total'] ?? 0
            ),

            'present' =>
            (int) (
                $result['present'] ?? 0
            ),

            'absent' =>
            (int) (
                $result['absent'] ?? 0
            ),

            'half_day' =>
            (int) (
                $result['half_day'] ?? 0
            ),

            'leave' =>
            (int) (
                $result['leave_count'] ?? 0
            ),

            'not_marked' =>
            (int) (
                $result['not_marked'] ?? 0
            )

        ];
    }



    /*
    |--------------------------------------------------------------------------
    | Find Attendance By Employee And Date
    |--------------------------------------------------------------------------
    */

    public function findByEmployeeAndDate(
        int $employeeId,
        string $date
    ): ?array {

        $sql = "
            SELECT

                id,
                employee_id,
                attendance_date,
                status,
                check_in,
                check_out,
                remarks,
                created_at,
                updated_at

            FROM attendance

            WHERE employee_id = :employee_id

            AND attendance_date = :attendance_date

            LIMIT 1
        ";

        $stmt =
            $this->db->prepare($sql);

        $stmt->execute([

            ':employee_id' =>
            $employeeId,

            ':attendance_date' =>
            $date

        ]);

        $result =
            $stmt->fetch(
                PDO::FETCH_ASSOC
            );

        return $result ?: null;
    }


    /*
    |--------------------------------------------------------------------------
    | Find Approved Leave For Employee And Date
    |--------------------------------------------------------------------------
    |
    | Checks whether the employee has an approved leave
    | covering the selected attendance date.
    |
    */

    public function findApprovedLeave(
        int $employeeId,
        string $date
    ): ?array {

        $sql = "
            SELECT

                id,
                employee_id,
                leave_type,
                start_date,
                end_date,
                reason,
                status

            FROM leaves

            WHERE employee_id = :employee_id

            AND start_date <= :start_date

            AND end_date >= :end_date

            AND status = 'approved'

            LIMIT 1
        ";

        $stmt =
            $this->db->prepare($sql);

        $stmt->execute([

            ':employee_id' =>
            $employeeId,

            ':start_date' =>
            $date,
            ':end_date' => $date

        ]);

        $result =
            $stmt->fetch(
                PDO::FETCH_ASSOC
            );

        return $result ?: null;
    }


    /*
    |--------------------------------------------------------------------------
    | Create Attendance
    |--------------------------------------------------------------------------
    */

    public function create(
        array $data
    ): int {

        $idStmt = $this->db->query(
            "SELECT COALESCE(MAX(id), 0) + 1 FROM attendance"
        );

        $id = (int) $idStmt->fetchColumn();

        $sql = "
            INSERT INTO attendance
            (
                id,
                employee_id,
                attendance_date,
                status,
                check_in,
                check_out,
                remarks
            )
            VALUES
            (
                :id,
                :employee_id,
                :attendance_date,
                :status,
                :check_in,
                :check_out,
                :remarks
            )
        ";

        $stmt =
            $this->db->prepare($sql);

        $stmt->execute([
            ':id' => $id,
            ':employee_id' =>
            $data['employee_id'],

            ':attendance_date' =>
            $data['attendance_date'],

            ':status' =>
            $data['status'],

            ':check_in' =>
            $data['check_in'] !== ''
                ? $data['check_in']
                : null,

            ':check_out' =>
            $data['check_out'] !== ''
                ? $data['check_out']
                : null,

            ':remarks' =>
            $data['remarks'] !== ''
                ? $data['remarks']
                : null

        ]);

        return (int)
        $this->db->lastInsertId();
    }


    /*
    |--------------------------------------------------------------------------
    | Update Attendance
    |--------------------------------------------------------------------------
    */

    public function update(
        int $id,
        array $data
    ): bool {

        $sql = "
            UPDATE attendance

            SET

                status = :status,

                check_in = :check_in,

                check_out = :check_out,

                remarks = :remarks

            WHERE id = :id
        ";

        $stmt =
            $this->db->prepare($sql);

        return $stmt->execute([

            ':status' =>
            $data['status'],

            ':check_in' =>
            $data['check_in'] !== ''
                ? $data['check_in']
                : null,

            ':check_out' =>
            $data['check_out'] !== ''
                ? $data['check_out']
                : null,

            ':remarks' =>
            $data['remarks'] !== ''
                ? $data['remarks']
                : null,

            ':id' =>
            $id

        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | Get Attendance History
    |--------------------------------------------------------------------------
    */

    public function getHistory(
        string $dateFrom,
        string $dateTo,
        ?int $employeeId = null,
        ?string $status = null
    ): array {

        $sql = "
            SELECT

            /* Attendance */
            a.id AS attendance_id,
            a.employee_id,
            a.attendance_date,
            a.status,
            a.check_in,
            a.check_out,
            a.remarks,

            /* Employee */
            e.id AS employee_db_id,
            e.employee_id AS employee_code,
            e.first_name,
            e.last_name,

            /* Department */
            e.department_id,
            d.name AS department

        FROM attendance a

            INNER JOIN employees e
                ON e.id = a.employee_id

            LEFT JOIN departments d
                ON d.id = e.department_id

            WHERE a.attendance_date
                BETWEEN :date_from AND :date_to
        ";


        $params = [

            ':date_from' =>
            $dateFrom,

            ':date_to' =>
            $dateTo

        ];


        /*
        |--------------------------------------------------------------------------
        | Employee Filter
        |--------------------------------------------------------------------------
        */

        if ($employeeId !== null) {

            $sql .= "
                AND a.employee_id = :employee_id
            ";

            $params[':employee_id'] =
                $employeeId;
        }


        /*
        |--------------------------------------------------------------------------
        | Status Filter
        |--------------------------------------------------------------------------
        */

        if (
            $status !== null
            &&
            $status !== ''
        ) {

            $sql .= "
                AND a.status = :status
            ";

            $params[':status'] =
                $status;
        }


        /*
        |--------------------------------------------------------------------------
        | Sorting
        |--------------------------------------------------------------------------
        */

        $sql .= "
            ORDER BY

                a.attendance_date DESC,

                e.first_name ASC
        ";

        $stmt =
            $this->db->prepare($sql);

        $stmt->execute($params);

        return $stmt->fetchAll(
            PDO::FETCH_ASSOC
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Get Employee Attendance Summary
    |--------------------------------------------------------------------------
    |
    | total_marked = actual records in attendance table.
    |
    */

    public function getSummary(
        string $dateFrom,
        string $dateTo,
        ?int $employeeId = null
    ): array {

        $sql = "
            SELECT

                e.id,
                e.employee_id,
                e.first_name,
                e.last_name,
                e.department_id,
                d.name AS department,

                COUNT(a.id) AS total_marked,

                SUM(
                    CASE
                        WHEN a.status = 'present'
                        THEN 1
                        ELSE 0
                    END
                ) AS present_days,

                SUM(
                    CASE
                        WHEN a.status = 'absent'
                        THEN 1
                        ELSE 0
                    END
                ) AS absent_days,

                SUM(
                    CASE
                        WHEN a.status = 'half_day'
                        THEN 1
                        ELSE 0
                    END
                ) AS half_days,

                SUM(
                    CASE
                        WHEN a.status = 'leave'
                        THEN 1
                        ELSE 0
                    END
                ) AS leave_days

            FROM employees e

            LEFT JOIN attendance a

                ON a.employee_id = e.id
                AND a.attendance_date
                    BETWEEN :date_from AND :date_to
            LEFT JOIN departments d
                ON d.id = e.department_id

            WHERE 1 = 1
        ";

        $params = [

            ':date_from' =>
            $dateFrom,

            ':date_to' =>
            $dateTo


        ];


        /*
        |--------------------------------------------------------------------------
        | Employee Filter
        |--------------------------------------------------------------------------
        */

        if ($employeeId !== null) {

            $sql .= "
                AND e.id = :employee_id
            ";

            $params[':employee_id'] =
                $employeeId;
        }


        /*
        |--------------------------------------------------------------------------
        | Group
        |--------------------------------------------------------------------------
        */

        $sql .= "
            GROUP BY

                e.id,
                e.employee_id,
                e.first_name,
                e.last_name,
                e.department_id

            ORDER BY

                e.first_name ASC
        ";

        $stmt =
            $this->db->prepare($sql);

        $stmt->execute($params);

        return $stmt->fetchAll(
            PDO::FETCH_ASSOC
        );
    }

    public function getAttendancesindi(
        int $employeeId,
        string $startDate = '',
        string $endDate = ''
    ): array {

        /*
        |--------------------------------------------------------------------------
        | Validate Date Format
        |--------------------------------------------------------------------------
        */

        if ($startDate !== '') {

            $date = DateTime::createFromFormat(
                'Y-m-d',
                $startDate
            );

            if (
                !$date ||
                $date->format('Y-m-d') !== $startDate
            ) {
                return [];
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
                return [];
            }
        }


        /*
        |--------------------------------------------------------------------------
        | Validate Date Range
        |--------------------------------------------------------------------------
        */

        if (
            $startDate !== '' &&
            $endDate !== '' &&
            $startDate > $endDate
        ) {
            return [];
        }


        /*
        |--------------------------------------------------------------------------
        | Check Whether A Date Range Was Requested
        |--------------------------------------------------------------------------
        */

        $isDateRangeRequested =
            ($startDate !== '' || $endDate !== '');


        /*
        |--------------------------------------------------------------------------
        | Dynamic Date Condition
        |--------------------------------------------------------------------------
        */

        $dateCondition = '';

        $summaryParams = [
            ':employee_id' => $employeeId
        ];


        if (
            $startDate !== '' &&
            $endDate !== ''
        ) {

            $dateCondition = "
                AND a.attendance_date
                BETWEEN :start_date AND :end_date
            ";

            $summaryParams[':start_date'] = $startDate;
            $summaryParams[':end_date'] = $endDate;

        } elseif ($startDate !== '') {

            $dateCondition = "
                AND a.attendance_date >= :start_date
            ";

            $summaryParams[':start_date'] = $startDate;

        } elseif ($endDate !== '') {

            $dateCondition = "
                AND a.attendance_date <= :end_date
            ";

            $summaryParams[':end_date'] = $endDate;
        }


        /*
        |--------------------------------------------------------------------------
        | Employee + Attendance Summary
        |--------------------------------------------------------------------------
        */

        $summarySql = "
            SELECT

                e.id,
                e.employee_id,
                e.first_name,
                e.last_name,
                e.department_id,
                e.designation_id,

                d.name AS department,
                desi.name AS designation,

                COUNT(a.id) AS total_marked,

                SUM(
                    CASE
                        WHEN a.status = 'present'
                        THEN 1
                        ELSE 0
                    END
                ) AS present_days,

                SUM(
                    CASE
                        WHEN a.status = 'absent'
                        THEN 1
                        ELSE 0
                    END
                ) AS absent_days,

                SUM(
                    CASE
                        WHEN a.status = 'half_day'
                        THEN 1
                        ELSE 0
                    END
                ) AS half_days,

                SUM(
                    CASE
                        WHEN a.status = 'leave'
                        THEN 1
                        ELSE 0
                    END
                ) AS leave_days

            FROM employees e

            LEFT JOIN attendance a
                ON a.employee_id = e.id
                $dateCondition

            LEFT JOIN departments d
                ON d.id = e.department_id

            LEFT JOIN designations desi
                ON desi.id = e.designation_id

            WHERE e.id = :employee_id

            GROUP BY

                e.id,
                e.employee_id,
                e.first_name,
                e.last_name,
                e.department_id,
                e.designation_id,
                d.name,
                desi.name
        ";


        $stmt = $this->db->prepare($summarySql);

        $stmt->execute($summaryParams);

        $summary = $stmt->fetch(PDO::FETCH_ASSOC);


        /*
        |--------------------------------------------------------------------------
        | Employee Not Found
        |--------------------------------------------------------------------------
        */

        if (!$summary) {
            return [];
        }


        /*
        |--------------------------------------------------------------------------
        | Convert Summary Values
        |--------------------------------------------------------------------------
        */

        $totalMarked = (int) $summary['total_marked'];

        $presentDays = (int) $summary['present_days'];

        $absentDays = (int) $summary['absent_days'];

        $halfDays = (int) $summary['half_days'];

        $leaveDays = (int) $summary['leave_days'];


        /*
        |--------------------------------------------------------------------------
        | Date Range Summary
        |--------------------------------------------------------------------------
        |
        | If a specific date/month/week/year was requested:
        |
        | 0 present  -> "Not Marked"
        | 0 absent   -> "Not Marked"
        | 0 half day -> "Not Marked"
        | 0 leave    -> "Not Marked"
        |
        | But when no date range is provided, preserve the old
        | overall summary behavior and return numeric 0 values.
        |
        */

        if ($isDateRangeRequested) {

            $presentDaysResponse =
                $presentDays > 0
                    ? $presentDays
                    : 'Not Marked';

            $absentDaysResponse =
                $absentDays > 0
                    ? $absentDays
                    : 'Not Marked';

            $halfDaysResponse =
                $halfDays > 0
                    ? $halfDays
                    : 'Not Marked';

            $leaveDaysResponse =
                $leaveDays > 0
                    ? $leaveDays
                    : 'Not Marked';

        } else {

            /*
            |--------------------------------------------------------------------------
            | Overall Summary
            |--------------------------------------------------------------------------
            */

            $presentDaysResponse = $presentDays;

            $absentDaysResponse = $absentDays;

            $halfDaysResponse = $halfDays;

            $leaveDaysResponse = $leaveDays;
        }


        /*
        |--------------------------------------------------------------------------
        | Today's Attendance
        |--------------------------------------------------------------------------
        |
        | This is ALWAYS today's attendance.
        | It is independent of start_date/end_date.
        |
        */

        $todaySql = "
            SELECT

                id,
                employee_id,
                attendance_date,
                status,
                check_in,
                check_out,
                remarks,
                created_at,
                updated_at

            FROM attendance

            WHERE employee_id = :employee_id

            AND attendance_date = CURDATE()

            LIMIT 1
        ";


        $stmt = $this->db->prepare($todaySql);

        $stmt->execute([
            ':employee_id' => $employeeId
        ]);


        $todayAttendance = $stmt->fetch(PDO::FETCH_ASSOC);


        /*
        |--------------------------------------------------------------------------
        | Today's Attendance Response
        |--------------------------------------------------------------------------
        */

        if ($todayAttendance) {

            $todayAttendanceData = [

                'id' =>
                    (int) $todayAttendance['id'],

                'employee_id' =>
                    (int) $todayAttendance['employee_id'],

                'attendance_date' =>
                    $todayAttendance['attendance_date'],

                'marked' => true,

                'status' =>
                    $todayAttendance['status'],

                'check_in' =>
                    $todayAttendance['check_in'],

                'check_out' =>
                    $todayAttendance['check_out'],

                'remarks' =>
                    $todayAttendance['remarks'],

                'created_at' =>
                    $todayAttendance['created_at'],

                'updated_at' =>
                    $todayAttendance['updated_at']
            ];

        } else {

            $todayAttendanceData = [

                'id' => 'Not Marked',

                'employee_id' => 'Not Marked',

                'attendance_date' =>
                    date('Y-m-d'),

                'marked' => 'Not Marked',

                'status' => 'Not Marked',

                'check_in' => 'Not Marked',

                'check_out' => 'Not Marked',

                'remarks' => 'Not Marked',

                'created_at' => 'Not Marked',

                'updated_at' => 'Not Marked'
            ];
        }


        /*
        |--------------------------------------------------------------------------
        | Attendance History
        |--------------------------------------------------------------------------
        */

        $historySql = "
            SELECT

                id,
                employee_id,
                attendance_date,
                status,
                check_in,
                check_out,
                remarks,
                created_at,
                updated_at

            FROM attendance

            WHERE employee_id = :employee_id
        ";


        $historyParams = [
            ':employee_id' => $employeeId
        ];


        /*
        |--------------------------------------------------------------------------
        | Apply Date Filter To History
        |--------------------------------------------------------------------------
        */

        if (
            $startDate !== '' &&
            $endDate !== ''
        ) {

            $historySql .= "
                AND attendance_date
                BETWEEN :start_date AND :end_date
            ";

            $historyParams[':start_date'] = $startDate;

            $historyParams[':end_date'] = $endDate;

        } elseif ($startDate !== '') {

            $historySql .= "
                AND attendance_date >= :start_date
            ";

            $historyParams[':start_date'] = $startDate;

        } elseif ($endDate !== '') {

            $historySql .= "
                AND attendance_date <= :end_date
            ";

            $historyParams[':end_date'] = $endDate;
        }


        $historySql .= "
            ORDER BY attendance_date DESC
        ";


        $stmt = $this->db->prepare($historySql);

        $stmt->execute($historyParams);

        $attendanceHistory =
            $stmt->fetchAll(PDO::FETCH_ASSOC);


        /*
        |--------------------------------------------------------------------------
        | Return Final Attendance Data
        |--------------------------------------------------------------------------
        */

        return [

            'employee' => [

                'id' =>
                    (int) $summary['id'],

                'employee_id' =>
                    $summary['employee_id'],

                'first_name' =>
                    $summary['first_name'],

                'last_name' =>
                    $summary['last_name'],

                'department' =>
                    $summary['department'],

                'designation' =>
                    $summary['designation']
            ],


            'today_attendance' =>
                $todayAttendanceData,


            'attendance_summary' => [

                'total_marked' =>
                    $totalMarked,

                'present_days' =>
                    $presentDaysResponse,

                'absent_days' =>
                    $absentDaysResponse,

                'half_days' =>
                    $halfDaysResponse,

                'leave_days' =>
                    $leaveDaysResponse
            ],


            'attendance_history' =>
                $attendanceHistory
        ];
    }

    /*
|--------------------------------------------------------------------------
| Employee Attendance Summary
|--------------------------------------------------------------------------
*/

    public function getEmployeeSummary(int $employeeId): array
    {
        $sql = "
        SELECT
            COUNT(*) AS total_days,

            SUM(
                CASE
                    WHEN status = 'present'
                    THEN 1
                    ELSE 0
                END
            ) AS present_days,

            SUM(
                CASE
                    WHEN status = 'absent'
                    THEN 1
                    ELSE 0
                END
            ) AS absent_days,

            SUM(
                CASE
                    WHEN status = 'half_day'
                    THEN 1
                    ELSE 0
                END
            ) AS half_days,

            SUM(
                CASE
                    WHEN status = 'leave'
                    THEN 1
                    ELSE 0
                END
            ) AS leave_days

        FROM attendance

        WHERE employee_id = :employee_id
    ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            ':employee_id' => $employeeId
        ]);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return [
            'total_days' =>
            (int) ($result['total_days'] ?? 0),

            'present_days' =>
            (int) ($result['present_days'] ?? 0),

            'absent_days' =>
            (int) ($result['absent_days'] ?? 0),

            'half_days' =>
            (int) ($result['half_days'] ?? 0),

            'leave_days' =>
            (int) ($result['leave_days'] ?? 0)
        ];
    }


    /*
|--------------------------------------------------------------------------
| Recent Employee Attendance
|--------------------------------------------------------------------------
*/

    public function getEmployeeRecent(
        int $employeeId,
        int $limit = 10
    ): array {

        $limit = max(
            1,
            min(50, $limit)
        );

        $sql = "
        SELECT
            id,
            attendance_date,
            status,
            check_in,
            check_out,
            remarks

        FROM attendance

        WHERE employee_id = :employee_id

        ORDER BY attendance_date DESC

        LIMIT $limit
    ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            ':employee_id' => $employeeId
        ]);

        return $stmt->fetchAll(
            PDO::FETCH_ASSOC
        );
    }
}

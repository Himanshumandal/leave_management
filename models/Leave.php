<?php

class Leave extends Model
{

    public function updateEmployeeLeaveBalance(
        int $employeeId,
        int $leaveDays,
        string $leaveType
    ): bool {

        $column = null;

        if ($leaveType === 'casual') {

            $column = 'casual_pending';

        } elseif ($leaveType === 'sick') {

            $column = 'sick_pending';

        } elseif ($leaveType === 'annual') {

            $column = 'annual_pending';

        } else {

            return false;
        }


        $sql = "
            UPDATE employees
            SET
                current_leave_pending =
                    current_leave_pending - :total_leave_days,

                {$column} =
                    {$column} - :type_leave_days

            WHERE id = :employee_id
        ";


        $stmt =
            $this->db->prepare($sql);


        return $stmt->execute([

            ':total_leave_days' =>
                $leaveDays,

            ':type_leave_days' =>
                $leaveDays,

            ':employee_id' =>
                $employeeId

        ]);
    }

    public function getPendingLeaveCount(
        int $employeeId
    ): int {

        $sql = "
            SELECT COUNT(*)

            FROM leaves

            WHERE employee_id = :employee_id

            AND status = 'Pending'
        ";


        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            ':employee_id' => $employeeId
        ]);


        return (int) $stmt->fetchColumn();
    }

    /**
     * Get employee leave statistics.
     */
    public function getEmployeeLeaveStatistics(
        int $employeeId
    ): array {

        /*
    |--------------------------------------------------------------------------
    | Get Approved Leave Statistics
    |--------------------------------------------------------------------------
    */

        $sql = "
        SELECT
            leave_type,

            SUM(
                DATEDIFF(end_date, start_date) + 1
            ) AS total_days

        FROM leaves

        WHERE employee_id = :employee_id

        AND status = 'approved'

        GROUP BY leave_type

        ORDER BY leave_type ASC
    ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            ':employee_id' => $employeeId
        ]);

        $records = $stmt->fetchAll(PDO::FETCH_ASSOC);


        /*
    |--------------------------------------------------------------------------
    | Prepare Leave Chart Data
    |--------------------------------------------------------------------------
    */

        $labels = [];
        $data = [];

        $totalLeaveDays = 0;


        foreach ($records as $record) {

            $labels[] = ucfirst(
                str_replace(
                    '_',
                    ' ',
                    $record['leave_type']
                )
            );

            $days = (int) $record['total_days'];

            $data[] = $days;

            $totalLeaveDays += $days;
        }


        /*
    |--------------------------------------------------------------------------
    | Leave Balance
    |--------------------------------------------------------------------------
    |
    | Currently every employee has 12 leaves.
    |
    */

        $totalAllowedLeaves = 12;

        $leaveBalance = max(
            0,
            $totalAllowedLeaves - $totalLeaveDays
        );


        /*
    |--------------------------------------------------------------------------
    | Pending Leave Requests
    |--------------------------------------------------------------------------
    */

        $pendingSql = "
        SELECT
            COUNT(*) AS pending_requests

        FROM leaves

        WHERE employee_id = :employee_id

        AND status = 'pending'
    ";

        $pendingStmt = $this->db->prepare($pendingSql);

        $pendingStmt->execute([
            ':employee_id' => $employeeId
        ]);

        $pendingResult = $pendingStmt->fetch(PDO::FETCH_ASSOC);

        $pendingRequests = (int) (
            $pendingResult['pending_requests'] ?? 0
        );


        /*
    |--------------------------------------------------------------------------
    | Return Statistics
    |--------------------------------------------------------------------------
    */

        return [

            // Chart
            'labels' => $labels,

            'data' => $data,

            // Total approved leave days
            'total_days' => $totalLeaveDays,

            // Current leave balance
            'leave_balance' => $leaveBalance,

            // Number of pending requests
            'pending_requests' => $pendingRequests
        ];
    }


    /**
     * =====================================================
     * LEAVE BALANCE
     * =====================================================
     */
    // public function getLeaveBalance(
    //     int $employeeId
    // ): int {

    //     /*
    //      * Adjust this according to your
    //      * existing leave policy/table.
    //      */

    //     $totalLeave = 12;


    //     $sql = "
    //         SELECT COALESCE(
    //             SUM(total_days),
    //             0
    //         )

    //         FROM leaves

    //         WHERE employee_id = :employee_id

    //         AND status = 'Approved'

    //         AND YEAR(start_date) = YEAR(CURDATE())
    //     ";


    //     $stmt = $this->db->prepare($sql);

    //     $stmt->execute([
    //         ':employee_id' => $employeeId
    //     ]);


    //     $usedLeave =
    //         (int) $stmt->fetchColumn();


    //     return max(
    //         0,
    //         $totalLeave - $usedLeave
    //     );
    // }

    /*
    |--------------------------------------------------------------------------
    | Create Leave Request
    |--------------------------------------------------------------------------
    */

    public function create(array $data): int
    {
        $idStmt = $this->db->query(
            "SELECT COALESCE(MAX(id), 0) + 1 FROM leaves"
        );

        $id = (int) $idStmt->fetchColumn();

        $sql = "
            INSERT INTO leaves
            (
                id,
                employee_id,
                leave_type,
                start_date,
                end_date,
                reason,
                status
            )
            VALUES
            (
                :id,
                :employee_id,
                :leave_type,
                :start_date,
                :end_date,
                :reason,
                'pending'
            )
        ";


        $stmt =
            $this->db->prepare($sql);


        $stmt->execute([
            ':id' => $id,
            ':employee_id' =>
            $data['employee_id'],

            ':leave_type' =>
            $data['leave_type'],

            ':start_date' =>
            $data['start_date'],

            ':end_date' =>
            $data['end_date'],

            ':reason' =>
            $data['reason']

        ]);


        return (int)
        $this->db->lastInsertId();
    }



    /*
    |--------------------------------------------------------------------------
    | Get Employee Leave Requests
    |--------------------------------------------------------------------------
    */

    public function getByEmployee(
        int $employeeId
    ): array {

        $sql = "
            SELECT

                id,
                leave_type,
                start_date,
                end_date,
                reason,
                status,
                admin_remarks,
                approved_at,
                created_at,
                updated_at

            FROM leaves

            WHERE employee_id = :employee_id

            ORDER BY created_at DESC
        ";


        $stmt =
            $this->db->prepare($sql);


        $stmt->execute([

            ':employee_id' =>
            $employeeId

        ]);


        return $stmt->fetchAll(
            PDO::FETCH_ASSOC
        );
    }



    /*
    |--------------------------------------------------------------------------
    | Find Leave
    |--------------------------------------------------------------------------
    */

    public function find(
        int $id
    ): ?array {

        $sql = "
            SELECT

                l.*,

                e.employee_id AS employee_code,

                e.first_name,

                e.last_name,

                e.department

            FROM leaves l

            INNER JOIN employees e
                ON e.id = l.employee_id

            WHERE l.id = :id

            LIMIT 1
        ";


        $stmt =
            $this->db->prepare($sql);


        $stmt->execute([

            ':id' =>
            $id

        ]);


        $leave =
            $stmt->fetch(
                PDO::FETCH_ASSOC
            );


        return $leave ?: null;
    }



    /*
    |--------------------------------------------------------------------------
    | Get All Leave Requests
    |--------------------------------------------------------------------------
    */

    public function getAll(): array
    {

        $sql = "
            SELECT

                l.id,

                l.leave_type,

                l.start_date,

                l.end_date,

                l.reason,

                l.status,

                l.admin_remarks,

                l.approved_at,

                l.created_at,

                e.employee_id AS employee_code,

                e.first_name,

                e.last_name,

                e.department

            FROM leaves l

            INNER JOIN employees e
                ON e.id = l.employee_id

            ORDER BY l.created_at DESC
        ";


        $stmt =
            $this->db->query($sql);


        return $stmt->fetchAll(
            PDO::FETCH_ASSOC
        );
    }



    /*
    |--------------------------------------------------------------------------
    | Check Overlapping Leave
    |--------------------------------------------------------------------------
    */

    public function hasOverlap(
        int $employeeId,
        string $startDate,
        string $endDate
    ): bool {

        $sql = "
            SELECT id

            FROM leaves

            WHERE employee_id = :employee_id

            AND status IN (
                'pending',
                'approved'
            )

            AND start_date <= :end_date

            AND end_date >= :start_date

            LIMIT 1
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


        return (bool)
        $stmt->fetch(
            PDO::FETCH_ASSOC
        );
    }



    /*
    |--------------------------------------------------------------------------
    | Update Leave Status
    |--------------------------------------------------------------------------
    */

    public function updateStatus(
        int $id,
        string $status,
        ?int $approvedBy,
        ?string $adminRemarks
    ): bool {

        $sql = "
            UPDATE leaves

            SET

                status = :status,

                approved_by = :approved_by,

                admin_remarks = :admin_remarks,

                approved_at =
                    CASE
                        WHEN :status_for_date IN
                            ('approved', 'rejected')
                        THEN CURRENT_TIMESTAMP
                        ELSE NULL
                    END

            WHERE id = :id
        ";


        $stmt =
            $this->db->prepare($sql);


        return $stmt->execute([

            ':status' =>
            $status,

            ':status_for_date' =>
            $status,

            ':approved_by' =>
            $approvedBy,

            ':admin_remarks' =>
            $adminRemarks,

            ':id' =>
            $id

        ]);
    }

    public function countPending(): int
    {
        $sql = "
        SELECT COUNT(*)
        FROM leaves
        WHERE status = :status
    ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            'status' => 'pending'
        ]);

        return (int) $stmt->fetchColumn();
    }

    public function findApprovedLeaveByEmployeeAndDate(
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
                status,
                admin_remarks,
                approved_by,
                approved_at
            FROM leaves
            WHERE employee_id = :employee_id
              AND status = 'approved'
              AND :date BETWEEN start_date AND end_date
            LIMIT 1
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            ':employee_id' => $employeeId,
            ':date' => $date
        ]);

        $leave = $stmt->fetch(PDO::FETCH_ASSOC);

        return $leave ?: null;
    }


     public function getleavehistory(int $id): array
    {

        $sql = "
            SELECT

                id,
                employee_id,
                leave_type,

                start_date,

                end_date,

                reason,

                status,

                admin_remarks,

                approved_at,

                created_at
              

            FROM leaves
            where employee_id = :id
        ";


        $stmt =
            $this->db->prepare($sql);


        $stmt->execute([

            ':id' =>
            $id

        ]);


        $leave =
            $stmt->fetchAll(
                PDO::FETCH_ASSOC
            );

        


        return $leave ?: null;
    }
}

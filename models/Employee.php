<?php

class Employee extends Model
{
    public function consumeChatRequest(int $employeeId): bool
    {
        $sql = "
            UPDATE employees
            SET max_ai_chat = max_ai_chat - 1
            WHERE id = ?
            AND max_ai_chat > 0
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            $employeeId
        ]);

        return $stmt->rowCount() === 1;
    }
    public function countByDepartment(
        ?int $departmentId = null
    ): array {

        $sql = "
            SELECT
                d.id AS department_id,
                d.name AS department_name,
                COUNT(e.id) AS employee_count

            FROM departments d

            LEFT JOIN employees e
                ON e.department_id = d.id
                AND e.status = 'active'
        ";


        if ($departmentId !== null) {

            $sql .= "
                WHERE d.id = :department_id
            ";
        }


        $sql .= "
            GROUP BY
                d.id,
                d.name

            ORDER BY
                d.name ASC
        ";


        $stmt =
            $this->db->prepare($sql);


        if ($departmentId !== null) {

            $stmt->bindValue(
                ':department_id',
                $departmentId,
                PDO::PARAM_INT
            );
        }


        $stmt->execute();


        return $stmt->fetchAll(
            PDO::FETCH_ASSOC
        );
    }
    public function getAllActive(): array
    {
        $sql = "
                SELECT
                    id,
                    first_name,
                    last_name
                FROM employees
                WHERE status = 'active'
                ORDER BY first_name ASC
            ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function count(): int
    {
        $sql = "
            SELECT COUNT(*)
            FROM employees
        ";

        return (int) $this->db
            ->query($sql)
            ->fetchColumn();
    }

    public function findByUserId(
        int $userId
    ): ?array {

        $sql = "
            SELECT
                e.id,
                e.employee_id,
                e.first_name,
                e.last_name,
                e.status,

                CONCAT(
                    e.first_name,
                    ' ',
                    e.last_name
                ) AS name,

                d.name AS department_name,

                des.name AS designation_name

            FROM employees e

            LEFT JOIN departments d
                ON d.id = e.department_id

            LEFT JOIN designations des
                ON des.id = e.designation_id

            WHERE e.user_id = :user_id

            LIMIT 1
        ";


        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            ':user_id' => $userId
        ]);


        $employee =
            $stmt->fetch(PDO::FETCH_ASSOC);


        return $employee ?: null;
    }

    public function getDepartmentEmployeeCount(
        int $departmentId
    ): array {

        $sql = "
        SELECT
            COUNT(*) AS total

        FROM employees

        WHERE department_id = :department_id

        AND status = 'active'
    ";


        $stmt =
            $this->db->prepare($sql);

        $stmt->execute([
            ':department_id' => $departmentId
        ]);


        $result =
            $stmt->fetch(PDO::FETCH_ASSOC);


        return [

            'labels' => [
                'My Department'
            ],

            'data' => [
                (int) ($result['total'] ?? 0)
            ]
        ];
    }



    public function findByEmployee_id(string $empId): array
    {

        $sql = " SELECT
                id,
                employee_id,
                first_name,
                last_name,
                email,
                phone,
                department,
                designation,
                joining_date,
                status
            FROM employees where employee_id=:empId LIMIT 1
          ";
        $stmt = $this->db->prepare($sql);

        $stmt->execute([':empId' => $empId]);

        $employee = $stmt->fetch();

        return $employee ?: null;
    }

    public function  getAll(): array
    {

        $sql = "
           SELECT
            e.id,
            e.employee_id,
            e.first_name,
            e.last_name,
            e.email,
            e.phone,

            e.department_id,
            d.name AS department,

            e.designation_id,
            dg.name AS designation,

            e.joining_date,
            e.status

        FROM employees e

        INNER JOIN departments d
            ON d.id = e.department_id

        INNER JOIN designations dg
            ON dg.id = e.designation_id

        ORDER BY e.id DESC
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create(array $data): bool
    {

        // $idStmt = $this->db->query(
        //     "SELECT COALESCE(MAX(id), 0) + 1 FROM employees"
        // );

        // $id = (int) $idStmt->fetchColumn();

        $sql = "
            INSERT INTO employees
            (
                id,
                employee_id,
                first_name,
                last_name,
                email,
                phone,
                department_id,
                designation_id,
                joining_date,
                status,
                max_total_leave,
                max_casual_leave,
                max_sick_leave,
                max_annual_leave,
                current_leave_pending,
                casual_pending,
                sick_pending,
                annual_pending
            )
            VALUES
            (
                (SELECT COALESCE(MAX(id), 0) + 1 FROM employees),
                :employee_id,
                :first_name,
                :last_name,
                :email,
                :phone,
                :department_id,
                :designation_id,
                :joining_date,
                :status,
                18,
                6,
                6,
                6,
                18,
                6,
                6,
                6
            )
        ";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute(
            [
                ':id' => $id,
                ':employee_id' => $data['employee_id'],
                ':first_name' => $data['first_name'],
                ':last_name' => $data['last_name'],
                ':email' => $data['email'],
                ':phone' => $data['phone'],
                ':department_id' => $data['department_id'],
                ':designation_id' => $data['designation_id'],
                ':joining_date' => $data['joining_date'],
                ':status' => $data['status']
            ]
        );
    }

    public function delete(int $id): bool
    {
        $sql = "
            DELETE FROM employees
            WHERE id = :id
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            ':id' => $id
        ]);

        return $stmt->rowCount() > 0;
    }

    public function existsByEmployeeId(string $empId): bool
    {
        $sql = "
         SELECT id FROM employees Where employee_id = :employee_id
         LIMIT 1        
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            ':employee_id' => $empId
        ]);

        return $stmt->fetch() !== false;
    }

    public function existsByEmail(string $email): bool
    {
        $sql = "SELECT id FROM employees WHERE email= :email";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([":email" => $email]);
        return $stmt->fetch() != false;
    }

    public function findByid(int $id): ?array
    {
        $sql = "
        SELECT

            e.id,
            e.employee_id,
            e.first_name,
            e.last_name,
            e.email,
            e.phone,

            e.department_id,
            d.name AS department,

            e.designation_id,
            dg.name AS designation,

            e.joining_date,
            e.status,
            e.max_total_leave,
            e.max_casual_leave,
            e.max_sick_leave,
            e.max_annual_leave,
            e.current_leave_pending,
            e.casual_pending,
            e.sick_pending,
            e.annual_pending,
            e.annual_pending

        FROM employees e

        INNER JOIN departments d
            ON d.id = e.department_id

        INNER JOIN designations dg
            ON dg.id = e.designation_id

        WHERE e.id = :id

        LIMIT 1
    ";


        $stmt = $this->db->prepare($sql);

        $stmt->execute([':id' => $id]);

        $employee = $stmt->fetch();

        return $employee ?: null;
    }

    public function update(array $data, int $id): bool
    {
        $sql = "
            UPDATE employees
            SET
                employee_id = :employee_id,
                first_name = :first_name,
                last_name = :last_name,
                email = :email,
                phone = :phone,
                department_id = :department_id,
                designation_id = :designation_id,
                joining_date = :joining_date,
                status = :status
            WHERE id = :id
        ";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':employee_id' => $data['employee_id'],
            ':first_name' => $data['first_name'],
            ':last_name' => $data['last_name'],
            ':email' => $data['email'],
            ':phone' => $data['phone'],
            ':department_id' => $data['department_id'],
            ':designation_id' => $data['designation_id'],
            ':joining_date' => $data['joining_date'],
            ':status' => $data['status'],
            ':id' => $id
        ]);
    }

    public function getPaginated(array $filters): array
    {
        /*
    |--------------------------------------------------------------------------
    | WHERE CONDITIONS
    |--------------------------------------------------------------------------
    */

        $where = [];
        $params = [];


        /*
    |--------------------------------------------------------------------------
    | SEARCH
    |--------------------------------------------------------------------------
    */

        if (!empty($filters['search'])) {

            $where[] = "
            (
                e.employee_id LIKE :search_employee_id
                OR e.first_name LIKE :search_first_name
                OR e.last_name LIKE :search_last_name
                OR e.email LIKE :search_email
            )
        ";

            $search =
                '%' . $filters['search'] . '%';

            $params[':search_employee_id'] =
                $search;

            $params[':search_first_name'] =
                $search;

            $params[':search_last_name'] =
                $search;

            $params[':search_email'] =
                $search;
        }


        /*
    |--------------------------------------------------------------------------
    | DEPARTMENT FILTER
    |--------------------------------------------------------------------------
    */

        if (!empty($filters['department'])) {

            $where[] =
                'e.department_id = :department';

            $params[':department'] =
                $filters['department'];
        }


        /*
    |--------------------------------------------------------------------------
    | STATUS FILTER
    |--------------------------------------------------------------------------
    */

        if (!empty($filters['status'])) {

            $where[] =
                'e.status = :status';

            $params[':status'] =
                $filters['status'];
        }


        /*
    |--------------------------------------------------------------------------
    | BUILD WHERE CLAUSE
    |--------------------------------------------------------------------------
    */

        $whereSql = '';

        if (!empty($where)) {

            $whereSql =
                'WHERE ' .
                implode(
                    ' AND ',
                    $where
                );
        }


        /*
    |--------------------------------------------------------------------------
    | PAGINATION
    |--------------------------------------------------------------------------
    */

        $page =
            max(
                1,
                (int) (
                    $filters['page'] ?? 1
                )
            );


        $perPage =
            max(
                1,
                min(
                    50,
                    (int) (
                        $filters['per_page'] ?? 10
                    )
                )
            );


        /*
    |--------------------------------------------------------------------------
    | COUNT TOTAL RECORDS
    |--------------------------------------------------------------------------
    */

        $countSql = "
        SELECT COUNT(*)

        FROM employees e

        LEFT JOIN departments d
            ON d.id = e.department_id

        LEFT JOIN designations ds
            ON ds.id = e.designation_id

        $whereSql
    ";


        $countStmt =
            $this->db->prepare(
                $countSql
            );


        /*
    |--------------------------------------------------------------------------
    | BIND COUNT PARAMETERS
    |--------------------------------------------------------------------------
    */

        foreach (
            $params as $key => $value
        ) {

            $countStmt->bindValue(
                $key,
                $value,
                PDO::PARAM_STR
            );
        }


        /*
    |--------------------------------------------------------------------------
    | EXECUTE COUNT
    |--------------------------------------------------------------------------
    */

        $countStmt->execute();


        $total =
            (int) $countStmt->fetchColumn();


        /*
    |--------------------------------------------------------------------------
    | TOTAL PAGES
    |--------------------------------------------------------------------------
    */

        $totalPages =
            $total > 0
            ? (int) ceil(
                $total / $perPage
            )
            : 0;


        /*
    |--------------------------------------------------------------------------
    | HANDLE INVALID PAGE
    |--------------------------------------------------------------------------
    */

        if (
            $totalPages > 0 &&
            $page > $totalPages
        ) {

            $page =
                $totalPages;
        }


        /*
    |--------------------------------------------------------------------------
    | OFFSET
    |--------------------------------------------------------------------------
    */

        $offset =
            ($page - 1) * $perPage;


        /*
    |--------------------------------------------------------------------------
    | GET EMPLOYEES
    |--------------------------------------------------------------------------
    */

        $sql = "
        SELECT

            e.id,

            e.employee_id,

            e.first_name,

            e.last_name,

            e.email,

            e.phone,


            /*
            |--------------------------------------------------------------
            | Department
            |--------------------------------------------------------------
            */

            e.department_id,

            d.name AS department,


            /*
            |--------------------------------------------------------------
            | Designation
            |--------------------------------------------------------------
            */

            e.designation_id,

            ds.name AS designation,


            /*
            |--------------------------------------------------------------
            | Other employee information
            |--------------------------------------------------------------
            */

            e.joining_date,

            e.status


        FROM employees e


        /*
        |--------------------------------------------------------------------------
        | DEPARTMENT JOIN
        |--------------------------------------------------------------------------
        */

        LEFT JOIN departments d
            ON d.id = e.department_id


        /*
        |--------------------------------------------------------------------------
        | DESIGNATION JOIN
        |--------------------------------------------------------------------------
        */

        LEFT JOIN designations ds
            ON ds.id = e.designation_id


        /*
        |--------------------------------------------------------------------------
        | FILTERS
        |--------------------------------------------------------------------------
        */

        $whereSql


        /*
        |--------------------------------------------------------------------------
        | ORDER
        |--------------------------------------------------------------------------
        */

        ORDER BY e.id DESC


        /*
        |--------------------------------------------------------------------------
        | PAGINATION
        |--------------------------------------------------------------------------
        */

        LIMIT :limit

        OFFSET :offset
    ";


        /*
    |--------------------------------------------------------------------------
    | PREPARE
    |--------------------------------------------------------------------------
    */

        $stmt =
            $this->db->prepare(
                $sql
            );


        /*
    |--------------------------------------------------------------------------
    | BIND FILTER PARAMETERS
    |--------------------------------------------------------------------------
    */

        foreach (
            $params as $key => $value
        ) {

            $stmt->bindValue(
                $key,
                $value,
                PDO::PARAM_STR
            );
        }


        /*
    |--------------------------------------------------------------------------
    | BIND PAGINATION
    |--------------------------------------------------------------------------
    */

        $stmt->bindValue(
            ':limit',
            $perPage,
            PDO::PARAM_INT
        );


        $stmt->bindValue(
            ':offset',
            $offset,
            PDO::PARAM_INT
        );


        /*
    |--------------------------------------------------------------------------
    | EXECUTE
    |--------------------------------------------------------------------------
    */

        $stmt->execute();


        /*
    |--------------------------------------------------------------------------
    | RETURN
    |--------------------------------------------------------------------------
    */

        return [

            'data' =>
            $stmt->fetchAll(
                PDO::FETCH_ASSOC
            ),

            'total' =>
            $total,

            'page' =>
            $page,

            'per_page' =>
            $perPage,

            'total_pages' =>
            $totalPages
        ];
    }
}

<?php

class Designation extends Model
{
    public function count(): int
    {
        $sql = "
            SELECT COUNT(*)
            FROM designations
        ";

        return (int) $this->db
            ->query($sql)
            ->fetchColumn();
    }
    /*
    |--------------------------------------------------------------------------
    | Get Active Designations By Department
    |--------------------------------------------------------------------------
    */

    public function getByDepartment(
        int $departmentId
    ): array {

        $sql = "
            SELECT
                id,
                department_id,
                name,
                status

            FROM designations

            WHERE department_id = :department_id

            AND status = 'active'

            ORDER BY name ASC
        ";


        $stmt = $this->db->prepare($sql);


        $stmt->execute([
            ':department_id' => $departmentId
        ]);


        return $stmt->fetchAll(
            PDO::FETCH_ASSOC
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Find Designation By ID
    |--------------------------------------------------------------------------
    */

    public function findById(
        int $id
    ): ?array {

         $sql = "
            SELECT

                ds.id,

                ds.department_id,

                ds.name,

                ds.status,

                ds.created_at,

                ds.updated_at,

                d.name AS department_name

            FROM designations ds

            INNER JOIN departments d
                ON d.id = ds.department_id

            WHERE ds.id = :id

            LIMIT 1
        ";


        $stmt = $this->db->prepare($sql);


        $stmt->execute([
            ':id' => $id
        ]);


        $designation =
            $stmt->fetch(
                PDO::FETCH_ASSOC
            );


        return $designation ?: null;
    }


    /*
    |--------------------------------------------------------------------------
    | Check Designation By Name
    |--------------------------------------------------------------------------
    |
    | Used when creating a new designation.
    |
    */

    public function existsByName(
        int $departmentId,
        string $name
    ): bool {

        $sql = "
            SELECT
                id

            FROM designations

            WHERE department_id = :department_id

            AND name = :name

            LIMIT 1
        ";


        $stmt = $this->db->prepare($sql);


        $stmt->execute([

            ':department_id' =>
                $departmentId,

            ':name' =>
                trim($name)

        ]);


        return
            $stmt->fetch(
                PDO::FETCH_ASSOC
            ) !== false;
    }


    /*
    |--------------------------------------------------------------------------
    | Check Designation By Name And Department
    |--------------------------------------------------------------------------
    |
    | Used for both:
    |
    | CREATE
    | UPDATE
    |
    | During update, the current designation ID can be excluded.
    |
    */

    public function existsByNameAndDepartment(
        string $name,
        int $departmentId,
        ?int $excludeId = null
    ): bool {

        $sql = "
            SELECT
                id

            FROM designations

            WHERE department_id = :department_id

            AND LOWER(TRIM(name)) =
                LOWER(TRIM(:name))
        ";


        $params = [

            ':department_id' =>
                $departmentId,

            ':name' =>
                trim($name)

        ];


        /*
        |--------------------------------------------------------------------------
        | EXCLUDE CURRENT DESIGNATION
        |--------------------------------------------------------------------------
        */

        if ($excludeId !== null) {

            $sql .= "
                AND id != :exclude_id
            ";


            $params[':exclude_id'] =
                $excludeId;
        }


        $sql .= "
            LIMIT 1
        ";


        $stmt = $this->db->prepare($sql);


        $stmt->execute($params);


        return
            $stmt->fetch(
                PDO::FETCH_ASSOC
            ) !== false;
    }


    /*
    |--------------------------------------------------------------------------
    | Get Paginated Designations
    |--------------------------------------------------------------------------
    */

    public function getPaginated(
        array $filters
    ): array {

        /*
        |--------------------------------------------------------------------------
        | FILTER VALUES
        |--------------------------------------------------------------------------
        */

        $search =
            trim(
                $filters['search'] ?? ''
            );


        $status =
            trim(
                $filters['status'] ?? ''
            );


        $departmentId =
            $filters['department_id'] ?? null;


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

        if ($search !== '') {

            $where[] = "
                ds.name LIKE :search
            ";


            $params[':search'] =
                '%' .
                $search .
                '%';
        }


        /*
        |--------------------------------------------------------------------------
        | STATUS FILTER
        |--------------------------------------------------------------------------
        */

        if ($status !== '') {

            $where[] =
                'ds.status = :status';


            $params[':status'] =
                $status;
        }


        /*
        |--------------------------------------------------------------------------
        | DEPARTMENT FILTER
        |--------------------------------------------------------------------------
        */

        if (
            $departmentId !== null &&
            $departmentId !== '' &&
            is_numeric($departmentId)
        ) {

            $where[] =
                'ds.department_id = :department_id';


            $params[':department_id'] =
                (int) $departmentId;
        }


        /*
        |--------------------------------------------------------------------------
        | BUILD WHERE SQL
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
        | COUNT TOTAL
        |--------------------------------------------------------------------------
        */

        $countSql = "
            SELECT
                COUNT(*)

            FROM designations ds

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

        $this->bindFilterParameters(
            $countStmt,
            $params
        );


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
            ($page - 1) *
            $perPage;


        /*
        |--------------------------------------------------------------------------
        | GET DESIGNATIONS
        |--------------------------------------------------------------------------
        */

        $sql = "
            SELECT

                ds.id,

                ds.department_id,

                ds.name,

                ds.status,

                d.name AS department_name

            FROM designations ds

            INNER JOIN departments d
                ON d.id = ds.department_id

            $whereSql

            ORDER BY ds.id DESC

            LIMIT :limit

            OFFSET :offset
        ";


        $stmt =
            $this->db->prepare(
                $sql
            );


        /*
        |--------------------------------------------------------------------------
        | BIND FILTER PARAMETERS
        |--------------------------------------------------------------------------
        */

        $this->bindFilterParameters(
            $stmt,
            $params
        );


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


        $designations =
            $stmt->fetchAll(
                PDO::FETCH_ASSOC
            );


        /*
        |--------------------------------------------------------------------------
        | RETURN
        |--------------------------------------------------------------------------
        */

        return [

            'data' =>
                $designations,

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


    /*
    |--------------------------------------------------------------------------
    | Create Designation
    |--------------------------------------------------------------------------
    */

    public function create(
        array $data
    ): int|false {

        $sql = "
            INSERT INTO designations
            (
                department_id,
                name,
                status,
                created_at,
                updated_at
            )

            VALUES
            (
                :department_id,
                :name,
                :status,
                NOW(),
                NOW()
            )
        ";


        $stmt =
            $this->db->prepare(
                $sql
            );


        $success =
            $stmt->execute([

                ':department_id' =>
                    (int) $data['department_id'],

                ':name' =>
                    trim(
                        $data['name']
                    ),

                ':status' =>
                    $data['status']

            ]);


        if (!$success) {

            return false;
        }


        return
            (int) $this->db->lastInsertId();
    }


    /*
    |--------------------------------------------------------------------------
    | Update Designation
    |--------------------------------------------------------------------------
    */

    public function update(
        int $id,
        array $data
    ): bool {

        $sql = "
            UPDATE designations

            SET

                department_id =
                    :department_id,

                name =
                    :name,

                status =
                    :status,

                updated_at =
                    NOW()

            WHERE id = :id
        ";


        $stmt =
            $this->db->prepare(
                $sql
            );


        return $stmt->execute([

            ':department_id' =>
                (int) $data['department_id'],

            ':name' =>
                trim(
                    $data['name']
                ),

            ':status' =>
                $data['status'],

            ':id' =>
                $id

        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | Delete Designation
    |--------------------------------------------------------------------------
    */

    public function delete(
        int $id
    ): bool {

        $sql = "
            DELETE FROM designations

            WHERE id = :id
        ";


        $stmt =
            $this->db->prepare(
                $sql
            );


        return $stmt->execute([

            ':id' => $id

        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | Bind Filter Parameters
    |--------------------------------------------------------------------------
    |
    | Reusable helper for getPaginated().
    |
    */

    private function bindFilterParameters(
        PDOStatement $stmt,
        array $params
    ): void {

        foreach (
            $params as $key => $value
        ) {

            if (
                $key === ':department_id'
            ) {

                $stmt->bindValue(
                    $key,
                    (int) $value,
                    PDO::PARAM_INT
                );

                continue;
            }


            $stmt->bindValue(
                $key,
                $value,
                PDO::PARAM_STR
            );
        }
    }
}
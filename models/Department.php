<?php

class Department extends Model
{
    public function count(): int
    {
        $sql = "
            SELECT COUNT(*)
            FROM departments
        ";

        return (int) $this->db
            ->query($sql)
            ->fetchColumn();
    }

    /*
    |--------------------------------------------------------------------------
    | Get Active Departments
    |--------------------------------------------------------------------------
    */

    public function getAll(): array
    {
        $sql = "
            SELECT
                id,
                name,
                status
            FROM departments
            WHERE status = 'active'
            ORDER BY name ASC
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    /*
    |--------------------------------------------------------------------------
    | Find Department By ID
    |--------------------------------------------------------------------------
    */

    public function findById(int $id): ?array
    {
        $sql = "
            SELECT
                id,
                name,
                status
            FROM departments
            WHERE id = :id
            LIMIT 1
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            ':id' => $id
        ]);

        $department = $stmt->fetch(
            PDO::FETCH_ASSOC
        );

        return $department ?: null;
    }


    /*
    |--------------------------------------------------------------------------
    | Check Department Name
    |--------------------------------------------------------------------------
    */

    public function existsByName(
        string $name
    ): bool {

        $sql = "
            SELECT id
            FROM departments
            WHERE name = :name
            LIMIT 1
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            ':name' => $name
        ]);

        return $stmt->fetch(
            PDO::FETCH_ASSOC
        ) !== false;
    }


    /*
    |--------------------------------------------------------------------------
    | Create Department
    |--------------------------------------------------------------------------
    */

    public function create(
        array $data
    ): bool {

        $sql = "
            INSERT INTO departments (
                name,
                status
            )
            VALUES (
                :name,
                :status
            )
        ";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([

            ':name' =>
                $data['name'],

            ':status' =>
                $data['status']

        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | Update Department
    |--------------------------------------------------------------------------
    */

    public function update(
        array $data,
        int $id
    ): bool {

        $sql = "
            UPDATE departments
            SET
                name = :name,
                status = :status
            WHERE id = :id
        ";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([

            ':name' =>
                $data['name'],

            ':status' =>
                $data['status'],

            ':id' =>
                $id

        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | Delete Department
    |--------------------------------------------------------------------------
    */

    public function delete(
        int $id
    ): bool {

        /*
        |----------------------------------------------------------------------
        | Delete Related Designations First
        |----------------------------------------------------------------------
        */

        $designationSql = "
            DELETE FROM designations
            WHERE department_id = :department_id
        ";

        $designationStmt =
            $this->db->prepare(
                $designationSql
            );

        $designationStmt->execute([
            ':department_id' => $id
        ]);


        /*
        |----------------------------------------------------------------------
        | Delete Department
        |----------------------------------------------------------------------
        */

        $departmentSql = "
            DELETE FROM departments
            WHERE id = :id
        ";

        $departmentStmt =
            $this->db->prepare(
                $departmentSql
            );

        $departmentStmt->execute([
            ':id' => $id
        ]);


        /*
        |----------------------------------------------------------------------
        | Check Department Deletion
        |----------------------------------------------------------------------
        */

        return $departmentStmt->rowCount() > 0;
    }


    /*
    |--------------------------------------------------------------------------
    | Get Paginated Departments
    |--------------------------------------------------------------------------
    */

    public function getPaginated(
        array $filters
    ): array {

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

        if (
            !empty($filters['search'])
        ) {

            $where[] = "
                d.name LIKE :search_name
            ";

            $params[':search_name'] =
                '%' .
                $filters['search'] .
                '%';
        }


        /*
        |--------------------------------------------------------------------------
        | STATUS FILTER
        |--------------------------------------------------------------------------
        */

        if (
            !empty($filters['status'])
        ) {

            $where[] =
                'd.status = :status';

            $params[':status'] =
                $filters['status'];
        }


        /*
        |--------------------------------------------------------------------------
        | BUILD WHERE
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
        | COUNT TOTAL
        |--------------------------------------------------------------------------
        */

        $countSql = "
            SELECT COUNT(*)
            FROM departments d
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
        | GET DEPARTMENTS
        |--------------------------------------------------------------------------
        */

        $sql = "
            SELECT
                d.id,
                d.name,
                d.status
            FROM departments d
            $whereSql
            ORDER BY d.id DESC
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


        $departments =
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
                $departments,

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
    | Get Designations By Department
    |--------------------------------------------------------------------------
    |
    | Keep this method if another part of your application
    | needs to load designations for a selected department.
    |
    */

    public function getDesignations(
        int $departmentId
    ): array {

        $sql = "
            SELECT
                ds.id,
                ds.name,
                ds.status,
                ds.department_id
            FROM designations ds
            WHERE ds.department_id = :department_id
            ORDER BY ds.id DESC
        ";


        $stmt =
            $this->db->prepare(
                $sql
            );


        $stmt->bindValue(
            ':department_id',
            $departmentId,
            PDO::PARAM_INT
        );


        $stmt->execute();


        return $stmt->fetchAll(
            PDO::FETCH_ASSOC
        );
    }
}
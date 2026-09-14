<?php

class User extends Model
{
    public function removeimg(int $id):bool{
        $sql = "
            UPDATE users
            SET image_url = NULL,
            cloudinary_image_url = NULL,
            cloudinary_public_id = NULL
            WHERE id = :id
        ";

        $stmt=$this->db->prepare($sql);

        return $stmt->execute([
            ':id'=>$id
        ]);
    }
    public function updateUser(
        string $employeeId,
        array $data
    ): bool
    {
        $sql = "
            UPDATE users
            SET 
             email = :email,
             employee_id= :empid,
             status= :status
            WHERE employee_id = :employee_id
        ";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':email' => $data['email'],
            ':empid' => $data['empid'],
            ':status' => $data['status'],
            ':employee_id' => $employeeId
        ]);
    }

    public function deleteByEmployeeId(string $employeeId): bool
    {
        $sql = "
            DELETE FROM users
            WHERE employee_id = :employee_id
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            ':employee_id' => $employeeId
        ]);

        return $stmt->rowCount() > 0;
    }

    public function existsByEmail(string $email): bool
    {
        $sql = "
            SELECT id
            FROM users
            WHERE email = :email
            LIMIT 1
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            ':email' => $email
        ]);

        return $stmt->fetch() !== false;
    }

    public function findByEmail(string $email): ?array
    {
        $sql = "
            SELECT *
            FROM users
            WHERE email = :email
            LIMIT 1
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            ':email' => $email
        ]);

        $user = $stmt->fetch();

        return $user ?: null;
    }
    public function findById(int $id): ?array
    {
        $sql = "
            SELECT *
            FROM users
            WHERE id = :id
            LIMIT 1
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            ':id' => $id
        ]);

        $user = $stmt->fetch();

        return $user ?: null;
    }

    public function uploadImage(
        int $id,
        string $imageUrl,
        string $cloudinaryImageUrl,
        string $cloudinaryPublicId
    ): bool {

        $sql = "
            UPDATE users
            SET
                image_url = :image_url,
                cloudinary_image_url = :cloudinary_image_url,
                cloudinary_public_id = :cloudinary_public_id
            WHERE id = :id
        ";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':image_url' => $imageUrl,
            ':cloudinary_image_url' => $cloudinaryImageUrl,
            ':cloudinary_public_id' => $cloudinaryPublicId,
            ':id' => $id
        ]);
    }

    public function create(array $data): bool
    {
        $idStmt = $this->db->query(
            "SELECT COALESCE(MAX(id), 0) + 1 FROM users"
        );

        $id = (int) $idStmt->fetchColumn();

        $sql = "
            INSERT INTO users
            (
                id,
                employee_id,
                email,
                password,
                role,
                status
            )
            VALUES
            (
                :id,
                :employee_id,
                :email,
                :password,
                :role,
                :status
            )
        ";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':id' => $id,
            ':employee_id' => $data['employee_id'],
            ':email' => $data['email'],
            ':password' => $data['password'],
            ':role' => $data['role'],
            ':status' => $data['status']
        ]);
    }
}
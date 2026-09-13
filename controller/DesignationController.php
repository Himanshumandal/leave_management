<?php

class DesignationController extends Controller
{
    private Designation $designationModel;

    public function __construct(PDO $db)
    {
        $this->designationModel = new Designation($db);
    }

    /*
    |--------------------------------------------------------------------------
    | INDEX
    |--------------------------------------------------------------------------
    */

    public function index(): void
    {

     

        $filters = [
            'search' => trim(
                $this->query(
                    'search',
                    ''
                )
            ),

            'status' => trim(
                $this->query(
                    'status',
                    ''
                )
            ),

            'department_id' =>  $this->query(
                'department_id',
                ''
            ),

            'page' => max(
                1,
                (int) $this->query(
                    'page',
                    1
                )
            ),

            'per_page' => max(
                1,
                (int) $this->query(
                    'per_page',
                    10
                )
            )
        ];

        $result = $this->designationModel
            ->getPaginated(
                $filters
            );

        foreach ($result['data'] as &$designation) {

            $designation['encrypted_id'] =
                UrlEncryptor::encrypt(
                    (int) $designation['id']
                );

            $designation['encrypted_department_id'] =
                UrlEncryptor::encrypt(
                    (int) $designation['department_id']
                );
        }

        unset($designation);

        $this->json([
            'success' => true,

            'designations' =>
                $result['data'],

            'pagination' => [

                'total' =>
                    $result['total'],

                'page' =>
                    $result['page'],

                'per_page' =>
                    $result['per_page'],

                'total_pages' =>
                    $result['total_pages']
            ]
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | STORE
    |--------------------------------------------------------------------------
    */

    public function store(): void
    {
        $name = trim(
            $this->input(
                'name',
                ''
            )
        );

        $encryptedDepartmentId = trim(
            $this->input(
                'department_id',
                ''
            )
        );

        $status = trim(
            $this->input(
                'status',
                ''
            )
        );

        /*
        |--------------------------------------------------------------------------
        | VALIDATION
        |--------------------------------------------------------------------------
        */

        $errors = [];

        if ($name === '') {

            $errors['designation_name'] =
                'Designation name is required.';

        } elseif (mb_strlen($name) < 2) {

            $errors['designation_name'] =
                'Designation name must contain at least 2 characters.';

        } elseif (mb_strlen($name) > 100) {

            $errors['designation_name'] =
                'Designation name cannot exceed 100 characters.';

        } elseif (
            !preg_match(
                "/^[a-zA-Z0-9\s&'().-]+$/",
                $name
            )
        ) {

            $errors['designation_name'] =
                'Designation name contains invalid characters.';
        }


        if ($encryptedDepartmentId === '') {

            $errors['designation_department'] =
                'Department is required.';

        }


        if ($status === '') {

            $errors['designation_status'] =
                'Status is required.';

        } elseif (
            !in_array(
                $status,
                ['active', 'inactive'],
                true
            )
        ) {

            $errors['designation_status'] =
                'Please select a valid status.';
        }


        /*
        |--------------------------------------------------------------------------
        | DEPARTMENT ID
        |--------------------------------------------------------------------------
        */

        $departmentId = null;

        if ($encryptedDepartmentId !== '') {

            $departmentId =$encryptedDepartmentId;

            if (
                !$departmentId ||
                !is_numeric($departmentId)
            ) {

                $errors['designation_department'] =
                    'Invalid department selected.';
            }
        }


        /*
        |--------------------------------------------------------------------------
        | RETURN VALIDATION ERRORS
        |--------------------------------------------------------------------------
        */

        if (!empty($errors)) {

            $this->json([
                'success' => false,

                'message' =>
                    'Please correct the highlighted fields.',

                'errors' => $errors

            ], 422);

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | CHECK DUPLICATE
        |--------------------------------------------------------------------------
        */

        if (
            $this->designationModel->existsByNameAndDepartment(
                $name,
                (int) $departmentId
            )
        ) {

            $this->json([
                'success' => false,

                'message' =>
                    'This designation already exists in the selected department.',

                'errors' => [

                    'designation_name' =>
                        'This designation already exists in the selected department.'
                ]

            ], 422);

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | CREATE
        |--------------------------------------------------------------------------
        */

        try {

            $designationId =
                $this->designationModel->create([
                    'name' => $name,
                    'department_id' => (int) $departmentId,
                    'status' => $status
                ]);

            if (!$designationId) {

                $this->json([
                    'success' => false,

                    'message' =>
                        'Unable to create designation.'
                ], 500);

                return;
            }


            $this->json([
                'success' => true,

                'message' =>
                    'Designation created successfully.',

                'id' =>
                    UrlEncryptor::encrypt(
                        (int) $designationId
                    )
            ]);

        } catch (PDOException $e) {

            error_log(
                'Designation Store Error: ' .
                $e->getMessage()
            );

            $this->json([
                'success' => false,

                'message' =>
                    'Unable to create designation.'
            ], 500);
        }
    }


    /*
    |--------------------------------------------------------------------------
    | SHOW
    |--------------------------------------------------------------------------
    */

    public function show(): void
    {
        $encryptedId = trim(
            $this->query(
                'id',
                ''
            )
        );

        // echo $encryptedId;
        // exit;

        if ($encryptedId === '') {

            $this->json([
                'success' => false,

                'message' =>
                    'Designation ID is required.'
            ], 400);

            return;
        }


        $id = UrlEncryptor::decrypt(
            $encryptedId
        );


        if (
            !$id ||
            !is_numeric($id)
        ) {

            $this->json([
                'success' => false,

                'message' =>
                    'Invalid designation ID.'
            ], 400);

            return;
        }


        $designation =
            $this->designationModel->findById(
                (int) $id
            );


        if (!$designation) {

            $this->json([
                'success' => false,

                'message' =>
                    'Designation not found.'
            ], 404);

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | ENCRYPT IDS
        |--------------------------------------------------------------------------
        */

        $designation['encrypted_id'] =
            UrlEncryptor::encrypt(
                (int) $designation['id']
            );

        $designation['encrypted_department_id'] =
            UrlEncryptor::encrypt(
                (int) $designation['department_id']
            );


        /*
        |--------------------------------------------------------------------------
        | RESPONSE
        |--------------------------------------------------------------------------
        */

        $this->json([
            'success' => true,

            'designation' =>
                $designation
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | UPDATE
    |--------------------------------------------------------------------------
    */

    public function update(): void
    {
        $encryptedId = trim(
            $this->input(
                'id',
                ''
            )
        );


        $name = trim(
            $this->input(
                'name',
                ''
            )
        );

        $encryptedDepartmentId = trim(
            $this->input(
                'department_id',
                ''
            )
        );

        $status = trim(
            $this->input(
                'status',
                ''
            )
        );


        /*
        |--------------------------------------------------------------------------
        | VALIDATION
        |--------------------------------------------------------------------------
        */


        $errors = [];


        if ($encryptedId === '') {

            $errors['designation_id'] =
                'Designation ID is required.';
        }


        if ($name === '') {

            $errors['designation_name'] =
                'Designation name is required.';

        } elseif (mb_strlen($name) < 2) {

            $errors['designation_name'] =
                'Designation name must contain at least 2 characters.';

        } elseif (mb_strlen($name) > 100) {

            $errors['designation_name'] =
                'Designation name cannot exceed 100 characters.';

        } elseif (
            !preg_match(
                "/^[a-zA-Z0-9\s&'().-]+$/",
                $name
            )
        ) {

            $errors['designation_name'] =
                'Designation name contains invalid characters.';
        }

   


        if ($encryptedDepartmentId === '') {

            $errors['designation_department'] =
                'Department is required.';
        }


        if ($status === '') {

            $errors['designation_status'] =
                'Status is required.';

        } elseif (
            !in_array(
                $status,
                ['active', 'inactive'],
                true
            )
        ) {

            $errors['designation_status'] =
                'Please select a valid status.';
        }


        /*
        |--------------------------------------------------------------------------
        | DECRYPT DESIGNATION ID
        |--------------------------------------------------------------------------
        */

        $designationId = null;

        if ($encryptedId !== '') {

            $designationId =
                UrlEncryptor::decrypt(
                    $encryptedId
                );

            if (
                !$designationId ||
                !is_numeric($designationId)
            ) {

                $errors['designation_id'] =
                    'Invalid designation ID.';
            }
        }


        /*
        |--------------------------------------------------------------------------
        | DECRYPT DEPARTMENT ID
        |--------------------------------------------------------------------------
        */

        $departmentId = null;

        if ($encryptedDepartmentId !== '') {

            $departmentId =$encryptedDepartmentId;

            if (
                !$departmentId ||
                !is_numeric($departmentId)
            ) {

                $errors['designation_department'] =
                    'Invalid department selected.';
            }
        }


        /*
        |--------------------------------------------------------------------------
        | VALIDATION RESPONSE
        |--------------------------------------------------------------------------
        */

        if (!empty($errors)) {

            $this->json([
                'success' => false,

                'message' =>
                    'Please correct the highlighted fields.',

                'errors' => $errors

            ], 422);

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | CHECK DESIGNATION EXISTS
        |--------------------------------------------------------------------------
        */

        $existing =
            $this->designationModel->findById(
                (int) $designationId
            );


        if (!$existing) {

            $this->json([
                'success' => false,

                'message' =>
                    'Designation not found.'
            ], 404);

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | DUPLICATE CHECK
        |--------------------------------------------------------------------------
        */

        if (
            $this->designationModel->existsByNameAndDepartment(
                $name,
                (int) $departmentId,
                (int) $designationId
            )
        ) {

            $this->json([
                'success' => false,

                'message' =>
                    'This designation already exists in the selected department.',

                'errors' => [

                    'designation_name' =>
                        'This designation already exists in the selected department.'
                ]

            ], 422);

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | UPDATE
        |--------------------------------------------------------------------------
        */

        try {

            $updated =
                $this->designationModel->update(
                    (int) $designationId,
                    [
                        'name' => $name,
                        'department_id' =>
                            (int) $departmentId,
                        'status' => $status
                    ]
                );


            if (!$updated) {

                $this->json([
                    'success' => false,

                    'message' =>
                        'No changes were made.'
                ]);

                return;
            }


            $this->json([
                'success' => true,

                'message' =>
                    'Designation updated successfully.'
            ]);

        } catch (PDOException $e) {

            error_log(
                'Designation Update Error: ' .
                $e->getMessage()
            );

            $this->json([
                'success' => false,

                'message' =>
                    'Unable to update designation.'
            ], 500);
        }
    }


    /*
    |--------------------------------------------------------------------------
    | DELETE
    |--------------------------------------------------------------------------
    */

    public function destroy(): void
    {
        $encryptedId = trim(
            $this->input(
                'id',
                ''
            )
        );


        if ($encryptedId === '') {

            $this->json([
                'success' => false,

                'message' =>
                    'Designation ID is required.'
            ], 400);

            return;
        }


        $designationId =
            UrlEncryptor::decrypt(
                $encryptedId
            );


        if (
            !$designationId ||
            !is_numeric($designationId)
        ) {

            $this->json([
                'success' => false,

                'message' =>
                    'Invalid designation ID.'
            ], 400);

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | CHECK EXISTS
        |--------------------------------------------------------------------------
        */

        $designation =
            $this->designationModel->findById(
                (int) $designationId
            );


        if (!$designation) {

            $this->json([
                'success' => false,

                'message' =>
                    'Designation not found.'
            ], 404);

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | DELETE
        |--------------------------------------------------------------------------
        */

        try {

            $deleted =
                $this->designationModel->delete(
                    (int) $designationId
                );


            if (!$deleted) {

                $this->json([
                    'success' => false,

                    'message' =>
                        'Unable to delete designation.'
                ], 500);

                return;
            }


            $this->json([
                'success' => true,

                'message' =>
                    'Designation deleted successfully.'
            ]);

        } catch (PDOException $e) {

            /*
            |--------------------------------------------------------------------------
            | FOREIGN KEY / RELATED RECORD
            |--------------------------------------------------------------------------
            */

            if ((int) $e->errorInfo[1] === 1451) {

                $this->json([
                    'success' => false,

                    'message' =>
                        'This designation cannot be deleted because it is being used by other records.'
                ], 409);

                return;
            }


            error_log(
                'Designation Delete Error: ' .
                $e->getMessage()
            );

            $this->json([
                'success' => false,

                'message' =>
                    'Unable to delete designation.'
            ], 500);
        }
    }
}
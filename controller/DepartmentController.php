
<?php

class DepartmentController extends Controller
{

    private Department $departmentModel;


    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->departmentModel = new Department($db);
        $this->db = $db;
    }

    public function index(): void
    {
        /*
    |--------------------------------------------------------------------------
    | FILTERS
    |--------------------------------------------------------------------------
    */

        $filters = [

            'search' =>
            trim(
                $this->query(
                    'search',
                    ''
                )
            ),

            'status' =>
            trim(
                $this->query(
                    'status',
                    ''
                )
            ),

            'page' =>
            (int) $this->query(
                'page',
                1
            ),

            'per_page' =>
            (int) $this->query(
                'per_page',
                10
            )
        ];


        /*
    |--------------------------------------------------------------------------
    | GET PAGINATED DEPARTMENTS
    |--------------------------------------------------------------------------
    */

        $result =
            $this->departmentModel
            ->getPaginated(
                $filters
            );


        /*
    |--------------------------------------------------------------------------
    | ENCRYPT DEPARTMENT ID
    |--------------------------------------------------------------------------
    */

        foreach (
            $result['data'] as &$department
        ) {

            $department['encrypted_id'] =
                UrlEncryptor::encrypt(
                    (int) $department['id']
                );
        }

        unset($department);


        /*
    |--------------------------------------------------------------------------
    | JSON RESPONSE
    |--------------------------------------------------------------------------
    */

        $this->json([

            'success' =>
            true,

            'departments' =>
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

     public function store(): void
    {
        $data = [

            'name' => trim(
                $this->input('department', '')
            ),

            'status' => trim(
                $this->input('status', '')
            )
        ];
    
       


        /*
        |--------------------------------------------------------------------------
        | Validation
        |--------------------------------------------------------------------------
        */

        $validator = new Validator($data);

        $validator
            ->required('name')
            ->required('status');


        /*
        |--------------------------------------------------------------------------
        | Return Validation Errors
        |--------------------------------------------------------------------------
        */

        if ($validator->fails()) {

            $this->json([

                'success' => false,

                'message' => 'Validation failed',

                'errors' => $validator->errors()

            ], 422);
        }


        /*
        |--------------------------------------------------------------------------
        | Validate Status
        |--------------------------------------------------------------------------
        */

        if (
            !in_array(
                $data['status'],
                ['active', 'inactive'],
                true
            )
        ) {

            $validator->addError(
                'status',
                'Please select a valid status.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Check Duplicate Department
        |--------------------------------------------------------------------------
        */

        if (
            $this->departmentModel->existsByName(
                $data['name']
            )
        ) {

            $this->json([

                'success' => false,

                'message' => 'Validation failed',

                'errors' => [

                    'name' =>
                        'Department name already exists.'

                ]

            ], 422);
        }


        /*
        |--------------------------------------------------------------------------
        | Transaction
        |--------------------------------------------------------------------------
        */

        try {

            $this->db->beginTransaction();


            /*
            | Create Department
            */

            $created =
                $this->departmentModel->create(
                    $data
                );


            if (!$created) {

                throw new Exception(
                    'Department creation failed.'
                );
            }


            /*
            | Commit
            */

            $this->db->commit();


            /*
            | Response
            */

            $this->json([

                'success' => true,

                'message' =>
                    'Department created successfully.'

            ]);

        } catch (\Exception $e) {

            if (
                $this->db->inTransaction()
            ) {

                $this->db->rollBack();
            }


            $this->json([

                'success' => false,

                'message' =>
                    'Unable to create department.'

            ], 500);
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Show Department
    |--------------------------------------------------------------------------
    */

    public function show(): void
    {
        $encryptedId =
            $this->query(
                'id',
                ''
            );


        /*
        |--------------------------------------------------------------------------
        | Decrypt ID
        |--------------------------------------------------------------------------
        */

        $id =
            UrlEncryptor::decrypt(
                $encryptedId
            );


        /*
        |--------------------------------------------------------------------------
        | Validate ID
        |--------------------------------------------------------------------------
        */

        if ($id <= 0) {

            $this->json([

                'success' => false,

                'message' =>
                    'Invalid department ID.'

            ], 422);
        }


        /*
        |--------------------------------------------------------------------------
        | Find Department
        |--------------------------------------------------------------------------
        */

        $department =
            $this->departmentModel
                ->findById($id);


        /*
        |--------------------------------------------------------------------------
        | Department Not Found
        |--------------------------------------------------------------------------
        */

        if (!$department) {

            $this->json([

                'success' => false,

                'message' =>
                    'Department not found.'

            ], 404);
        }


        /*
        |--------------------------------------------------------------------------
        | Response
        |--------------------------------------------------------------------------
        */

        $this->json([

            'success' => true,

            'department' =>
                $department

        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | Update Department
    |--------------------------------------------------------------------------
    */

    public function update(): void
    {
        
        $id =
            $this->input(
                'id',
                ''
            );
        


        /*
        |--------------------------------------------------------------------------
        | Validate ID
        |--------------------------------------------------------------------------
        */

        if ($id <= 0) {

            $this->json([

                'success' => false,

                'message' =>
                    'Invalid department ID.'

            ], 422);
        }


        /*
        |--------------------------------------------------------------------------
        | Find Existing Department
        |--------------------------------------------------------------------------
        */

        $department =
            $this->departmentModel
                ->findById($id);


        if (!$department) {

            $this->json([

                'success' => false,

                'message' =>
                    'Department not found.'

            ], 404);
        }


        /*
        |--------------------------------------------------------------------------
        | Request Data
        |--------------------------------------------------------------------------
        */

        $data = [

            'name' => trim(
                $this->input('department', '')
            ),

            'status' => trim(
                $this->input('status', '')
            )
        ];


        /*
        |--------------------------------------------------------------------------
        | Validation
        |--------------------------------------------------------------------------
        */

        $validator = new Validator($data);

        $validator
            ->required('name')
            ->required('status');


        /*
        |--------------------------------------------------------------------------
        | Return Validation Errors
        |--------------------------------------------------------------------------
        */

        if ($validator->fails()) {

            $this->json([

                'success' => false,

                'message' => 'Validation failed',

                'errors' =>
                    $validator->errors()

            ], 422);
        }


        /*
        |--------------------------------------------------------------------------
        | Validate Status
        |--------------------------------------------------------------------------
        */

        if (
            !in_array(
                $data['status'],
                ['active', 'inactive'],
                true
            )
        ) {

            $validator->addError(
                'status',
                'Please select a valid status.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Check Duplicate Department Name
        |--------------------------------------------------------------------------
        |
        | The current department must be ignored.
        |
        */

        if (
            strtolower($data['name'])
            !==
            strtolower($department['name'])
        ) {

            if (
                $this->departmentModel->existsByName(
                    $data['name']
                )
            ) {

                $this->json([

                    'success' => false,

                    'message' =>
                        'Validation failed',

                    'errors' => [

                        'name' =>
                            'Department name already exists.'

                    ]

                ], 422);
            }
        }


        /*
        |--------------------------------------------------------------------------
        | Transaction
        |--------------------------------------------------------------------------
        */

        try {

            $this->db->beginTransaction();


            /*
            | Update Department
            */

            $updated =
                $this->departmentModel->update(
                    $data,
                    $id
                );


            if (!$updated) {

                throw new Exception(
                    'Department update failed.'
                );
            }


            /*
            | Commit
            */

            $this->db->commit();


            /*
            | Response
            */

            $this->json([

                'success' => true,

                'message' =>
                    'Department updated successfully.'

            ]);

        } catch (\Exception $e) {

            if (
                $this->db->inTransaction()
            ) {

                $this->db->rollBack();
            }


            $this->json([

                'success' => false,

                'message' =>
                    'Unable to update department.'

            ], 500);
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Destroy Department
    |--------------------------------------------------------------------------
    */

    public function destroy(): void
    {
        $encryptedId =
            $this->input(
                'id',
                ''
            );
        


        /*
        |--------------------------------------------------------------------------
        | Decrypt ID
        |--------------------------------------------------------------------------
        */

        $id =
            UrlEncryptor::decrypt(
                $encryptedId
            );


        /*
        |--------------------------------------------------------------------------
        | Validate ID
        |--------------------------------------------------------------------------
        */

        if ($id <= 0) {

            $this->json([

                'success' => false,

                'message' =>
                    'Invalid department ID.'

            ], 422);
        }


        /*
        |--------------------------------------------------------------------------
        | Find Department
        |--------------------------------------------------------------------------
        */

        $department =
            $this->departmentModel
                ->findById($id);


        if (!$department) {

            $this->json([

                'success' => false,

                'message' =>
                    'Department not found.'

            ], 404);
        }


        /*
        |--------------------------------------------------------------------------
        | Transaction
        |--------------------------------------------------------------------------
        */

        try {

            $this->db->beginTransaction();


            /*
            | Delete Department
            */

            $deleted =
                $this->departmentModel
                    ->delete($id);


            if (!$deleted) {

                throw new Exception(
                    'Department deletion failed.'
                );
            }


            /*
            | Commit
            */

            $this->db->commit();


            /*
            | Response
            */

            $this->json([

                'success' => true,

                'message' =>
                    'Department deleted successfully.'

            ]);

        } catch (\Exception $e) {

            if (
                $this->db->inTransaction()
            ) {

                $this->db->rollBack();
            }


            $this->json([

                'success' => false,

                'message' =>
                    'Unable to delete department.'

            ], 500);
        }
    }
}

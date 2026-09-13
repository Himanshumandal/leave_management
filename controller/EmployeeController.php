
<?php



class EmployeeController extends Controller
{

    private Employee $employeeModel;

    private User $userModel;
    private PDO $db;
    private MailService $mailService;
    private Attendance $attendanceModel;
    private CloudinaryService $cloudService;
    private Department $departmentModel;

    private Designation $designationModel;

    private Leave $leaveModel;

    public function __construct(PDO $db)
    {
        $this->employeeModel = new Employee($db);
        $this->attendanceModel =
            new Attendance($db);

        $this->leaveModel =
            new Leave($db);

        $this->departmentModel = new Department($db);
        $this->designationModel = new Designation($db);
        $this->userModel = new User($db);
        $this->db = $db;
        $this->mailService = new MailService();
        $this->cloudService = new CloudinaryService();
    }
    public function index(): void
    {
        $filters = [

            'search' =>
            trim(
                $this->query(
                    'search',
                    ''
                )
            ),

            'department' =>
            trim(
                $this->query(
                    'department',
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


        $result =
            $this->employeeModel
            ->getPaginated(
                $filters
            );

        foreach ($result['data'] as &$employee) {

            $employee['encrypted_id'] =
                UrlEncryptor::encrypt(
                    (int) $employee['id']
                );
        }



        $this->json([

            'success' => true,

            'employees' =>
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

            'employee_id' => trim(
                $this->input('employee_id', '')
            ),

            'first_name' => trim(
                $this->input('first_name', '')
            ),

            'last_name' => trim(
                $this->input('last_name', '')
            ),

            'email' => trim(
                $this->input('email', '')
            ),

            'phone' => trim(
                $this->input('phone', '')
            ),

            'department_id' =>
            (int) $this->input(
                'department',
                0
            ),

            'designation_id' =>
            (int) $this->input(
                'designation',
                0
            ),

            'joining_date' => trim(
                $this->input('joining_date', '')
            ),

            'status' => trim(
                $this->input('status', '')
            )
        ];



        $validator = new Validator($data);

        $validator
            ->required('employee_id')
            ->required('first_name')
            ->required('last_name')
            ->required('email')
            ->email('email')
            ->required('phone')
            ->required('department_id')
            ->required('designation_id')
            ->required('joining_date')
            ->required('status');

        if ($validator->fails()) {

            $this->json([

                'success' => false,

                'message' => 'Validation failed',

                'errors' => $validator->errors()

            ], 422);
        }

        if ($this->employeeModel->existsByEmployeeId(
            $data['employee_id']
        )) {

            $this->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => [
                    'employee_id' => 'Employee ID already exists.'
                ]
            ], 422);
        }


        if ($this->userModel->existsByEmail(
            $data['email']
        )) {

            $this->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => [
                    'email' =>
                    'Email is already registered.'
                ]
            ], 422);
        }

        $temporaryPassword = bin2hex(
            random_bytes(5)
        );

        if ($data['department_id'] <= 0) {

            $validator->addError(
                'department_id',
                'Please select a department.'
            );
        }

        if ($data['designation_id'] <= 0) {

            $validator->addError(
                'designation_id',
                'Please select a designation.'
            );
        }

        $department =
            $this->departmentModel->findById(
                $data['department_id']
            );

        if (!$department) {

            $validator->addError(
                'department_id',
                'Invalid department.'
            );
        }

        $designation =
            $this->designationModel->findById(
                $data['designation_id']
            );

        if (!$designation) {

            $validator->addError(
                'designation_id',
                'Invalid designation.'
            );
        }




        // 4. Hash password for database

        $hashedPassword = password_hash(
            $temporaryPassword,
            PASSWORD_DEFAULT
        );

        try {
            $this->db->beginTransaction();


            $employeeCreated = $this->employeeModel->create(
                $data
            );


            if (!$employeeCreated) {

                throw new Exception(
                    'Employee creation failed.'
                );
            }

            $userCreated = $this->userModel->create([

                'employee_id' => $data['employee_id'],

                'email' => $data['email'],

                'password' => $hashedPassword,

                'role' => 'employee',

                'status' => $data['status']

            ]);


            if (!$userCreated) {

                throw new Exception(
                    'User account creation failed.'
                );
            }

            try {

                $this->mailService
                    ->sendEmployeeCredentials(
                        $data['email'],
                        $data['first_name'],
                        $temporaryPassword
                    );
            } catch (\Exception $mailError) {

                $this->json([

                    'success' => true,

                    'message' =>
                    'Employee created, but email could not be sent.' . $mailError->getMessage()

                ]);
            }

            $this->db->commit();

            $this->json([

                'success' => true,

                'message' =>
                'Employee created successfully and login credentials sent.'

            ]);
        } catch (\Exception $e) {
            if ($this->db->inTransaction()) {

                $this->db->rollBack();
            }


            $this->json([

                'success' => false,

                'message' =>
                'Unable to create employee.' . $e->getMessage()

            ], 500);
        }
    }

    // public function show(): void
    // {
    //     $id = (int) $this->query('id', 0);


    //     if ($id <= 0) {

    //         $this->json([
    //             'success' => false,
    //             'message' => 'Invalid  ID.'
    //         ], 422);
    //     }

    //     $employee = $this->employeeModel->findByid($id);

    //     if (!$employee) {

    //         $this->json([
    //             'success' => false,
    //             'message' => 'Employee not found.'
    //         ], 404);
    //     }

    //     $this->json([
    //         'success' => true,
    //         'employee' => $employee
    //     ]);
    // }

    public function show(): void
    {
        /*
    |--------------------------------------------------------------------------
    | Get Employee ID
    |--------------------------------------------------------------------------
    */

        $encryptedId = $this->query('id', '');

        $id = '';

        if (UrlEncryptor::isEncrypted($encryptedId)) {

            $id = UrlEncryptor::decrypt($encryptedId);
        } else {

            $id = $encryptedId;
        }


        /*
    |--------------------------------------------------------------------------
    | Validate ID
    |--------------------------------------------------------------------------
    */

        if ($id <= 0) {

            $this->json([
                'success' => false,
                'message' => 'Invalid employee ID.'
            ], 422);

            return;
        }


        /*
    |--------------------------------------------------------------------------
    | Get Employee
    |--------------------------------------------------------------------------
    */

        $employee =
            $this->employeeModel->findById($id);


        if (!$employee) {

            $this->json([
                'success' => false,
                'message' => 'Employee not found.'
            ], 404);

            return;
        }


        /*
    |--------------------------------------------------------------------------
    | Get User
    |--------------------------------------------------------------------------
    */

        $user =
            $this->userModel->findByEmail(
                $employee['email']
            );


        if (!$user) {

            $this->json([
                'success' => false,
                'message' => 'User not found.'
            ], 404);

            return;
        }


        /*
    |--------------------------------------------------------------------------
    | Employee Image
    |--------------------------------------------------------------------------
    |
    | Priority:
    |
    | 1. Cloudinary
    | 2. Local image
    |
    */

        $imageUrl = null;


        /*
    |--------------------------------------------------------------------------
    | Cloudinary Image
    |--------------------------------------------------------------------------
    */

        $cloudinaryImageUrl =
            trim(
                $user['cloudinary_image_url'] ?? ''
            );


        if ($cloudinaryImageUrl !== '') {

            /*
        |--------------------------------------------------------------
        | Check if Cloudinary image is accessible
        |--------------------------------------------------------------
        */

            try {

                $headers = @get_headers(
                    $cloudinaryImageUrl,
                    true
                );


                if (
                    $headers !== false &&
                    isset($headers[0]) &&
                    strpos(
                        $headers[0],
                        '200'
                    ) !== false
                ) {

                    $imageUrl =
                        $cloudinaryImageUrl;
                }
            } catch (Throwable $e) {

                $imageUrl = null;
            }
        }


        /*
    |--------------------------------------------------------------------------
    | Local Image Fallback
    |--------------------------------------------------------------------------
    */

        if (!$imageUrl) {

            $localImageUrl =
                trim(
                    $user['image_url'] ?? ''
                );


            if ($localImageUrl !== '') {

                /*
            |--------------------------------------------------------------
            | Convert DB path to filesystem path
            |--------------------------------------------------------------
            */

                $localFile =
                    dirname(__DIR__)
                    . DIRECTORY_SEPARATOR
                    . str_replace(
                        ['/', '\\'],
                        DIRECTORY_SEPARATOR,
                        ltrim(
                            $localImageUrl,
                            '/\\'
                        )
                    );


                /*
            |--------------------------------------------------------------
            | Check local file
            |--------------------------------------------------------------
            */

                if (is_file($localFile)) {

                    $imageUrl =
                        '/' .
                        ltrim(
                            str_replace(
                                '\\',
                                '/',
                                $localImageUrl
                            ),
                            '/'
                        );
                }
            }
        }


        /*
    |--------------------------------------------------------------------------
    | Set Employee Image
    |--------------------------------------------------------------------------
    */

        $employee['image_url'] = $imageUrl;


        /*
    |--------------------------------------------------------------------------
    | Attendance Summary
    |--------------------------------------------------------------------------
    */

        $attendanceSummary =
            $this->attendanceModel
            ->getEmployeeSummary($id);


        /*
    |--------------------------------------------------------------------------
    | Recent Attendance
    |--------------------------------------------------------------------------
    */

        $recentAttendance =
            $this->attendanceModel
            ->getEmployeeRecent(
                $id,
                10
            );


        /*
    |--------------------------------------------------------------------------
    | Leave History
    |--------------------------------------------------------------------------
    */

        $leaveHistory =
            $this->leaveModel
            ->getByEmployee($id);


        /*
    |--------------------------------------------------------------------------
    | Leave Summary
    |--------------------------------------------------------------------------
    */

        $leaveSummary = [

            'total' =>
            count($leaveHistory),

            'approved' =>
            0,

            'pending' =>
            0,

            'rejected' =>
            0
        ];


        foreach ($leaveHistory as $leave) {

            if (
                isset($leave['status'])
            ) {

                if (
                    $leave['status'] === 'approved'
                ) {

                    $leaveSummary['approved']++;
                } elseif (
                    $leave['status'] === 'pending'
                ) {

                    $leaveSummary['pending']++;
                } elseif (
                    $leave['status'] === 'rejected'
                ) {

                    $leaveSummary['rejected']++;
                }
            }
        }


        /*
    |--------------------------------------------------------------------------
    | Response
    |--------------------------------------------------------------------------
    */

        $this->json([

            'success' => true,

            'employee' => $employee,

            'attendance' => [

                'summary' =>
                $attendanceSummary,

                'recent' =>
                $recentAttendance
            ],

            'leave' => [

                'summary' =>
                $leaveSummary,

                'history' =>
                $leaveHistory
            ]

        ]);
    }
    public function update(): void
    {

        $id = (int) $this->input('id', 0);

        if ($id <= 0) {

            $this->json([
                'success' => false,
                'message' => 'Invalid employee ID.'
            ], 422);
        }


        $employee = $this->employeeModel->findById($id);

        if (!$employee) {

            $this->json([
                'success' => false,
                'message' => 'Employee not found.'
            ], 404);
        }

        $data = [

            'employee_id' => trim(
                $this->input('employee_id', '')
            ),

            'first_name' => trim(
                $this->input('first_name', '')
            ),

            'last_name' => trim(
                $this->input('last_name', '')
            ),

            'email' => trim(
                $this->input('email', '')
            ),

            'phone' => trim(
                $this->input('phone', '')
            ),

            'department_id' => trim(
                $this->input('department', '')
            ),

            'designation_id' => trim(
                $this->input('designation', '')
            ),

            'joining_date' => trim(
                $this->input('joining_date', '')
            ),

            'status' => trim(
                $this->input('status', '')
            )
        ];



        $validator = new Validator($data);

        $validator
            ->required('employee_id')
            ->required('first_name')
            ->required('last_name')
            ->required('email')
            ->email('email')
            ->required('phone')
            ->required('department_id')
            ->required('designation_id')
            ->required('joining_date')
            ->required('status');


        if ($validator->fails()) {

            $this->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Check duplicate employee ID,
        // but ignore the current employee.

        if (
            $data['employee_id'] !== $employee['employee_id']
            &&
            $this->employeeModel->existsByEmployeeId(
                $data['employee_id']
            )
        ) {

            $this->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => [
                    'employee_id' =>
                    'Employee ID already exists.'
                ]
            ], 422);
        }


        // Check email against employees.

        if (
            $data['email'] !== $employee['email']
            &&
            $this->employeeModel->existsByEmail(
                $data['email']
            )
        ) {

            $this->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => [
                    'email' =>
                    'Email already exists.'
                ]
            ], 422);
        }


        // Check email against users.

        if (
            $data['email'] !== $employee['email']
            &&
            $this->userModel->existsByEmail(
                $data['email']
            )
        ) {

            $this->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => [
                    'email' =>
                    'Email is already registered.'
                ]
            ], 422);
        }


        if ($data['department_id'] <= 0) {

            $validator->addError(
                'department_id',
                'Please select a department.'
            );
        }

        if ($data['designation_id'] <= 0) {

            $validator->addError(
                'designation_id',
                'Please select a designation.'
            );
        }

        $department =
            $this->departmentModel->findById(
                $data['department_id']
            );

        if (!$department) {

            $validator->addError(
                'department_id',
                'Invalid department.'
            );
        }

        $designation =
            $this->designationModel->findById(
                $data['designation_id']
            );

        if (!$designation) {

            $validator->addError(
                'designation_id',
                'Invalid designation.'
            );
        }

        try {
            $this->db->beginTransaction();


            $updated = $this->employeeModel->update(
                $data,
                $id
            );


            if (!$updated) {

                throw new Exception(
                    'Employee update failed.'
                );
            }


            // Update login email if employee email changed.



            $updatevalue = [
                "email" => $data['email'],
                "empid" => $data['employee_id'],
                "status" => $data['status']
            ];


            $this->userModel->updateUser(
                $employee['employee_id'],
                $updatevalue
            );

            $this->db->commit();


            $this->json([
                'success' => true,
                'message' => 'Employee updated successfully.'
            ]);
        } catch (\Exception $e) {
            if ($this->db->inTransaction()) {

                $this->db->rollBack();
            }


            $this->json([
                'success' => false,
                'message' => 'Unable to update employee.' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(): void
    {
        $encryptedId =
            $this->input(
                'id',
                ''
            );
        $id =
            UrlEncryptor::decrypt(
                $encryptedId
            );

        if ($id <= 0) {

            $this->json([
                'success' => false,
                'message' => 'Invalid employee ID.'
            ], 422);
        }

        $employee = $this->employeeModel->findById($id);

        if (!$employee) {

            $this->json([
                'success' => false,
                'message' => 'Employee not found.'
            ], 404);
        }

        try {
            $this->db->beginTransaction();
            // Delete login account first
            $this->userModel->deleteByEmployeeId(
                $employee['employee_id']
            );

            // Delete employee
            $deleted = $this->employeeModel->delete($id);

            if (!$deleted) {

                throw new Exception(
                    'Employee deletion failed.'
                );
            }

            $this->db->commit();

            $this->json([
                'success' => true,
                'message' => 'Employee and login account deleted successfully.'
            ]);
        } catch (\Exception $e) {

            if ($this->db->beginTransaction()) {
                $this->db->rollBack();
            }
            $this->json([
                'success' => false,
                'message' => 'Unable to delete employee.'
            ], 500);
            //throw $th;
        }
    }

    public function departments(): void
    {
        $departments =
            $this->departmentModel->getAll();

        $this->json([

            'success' => true,

            'departments' =>
            $departments

        ]);
    }

    public function designations(): void
    {
        $departmentId =
            (int) $this->query(
                'department_id',
                0
            );

        if ($departmentId <= 0) {

            $this->json([

                'success' => false,

                'message' =>
                'Invalid department.'

            ], 422);
        }

        $department =
            $this->departmentModel->findById(
                $departmentId
            );

        if (!$department) {

            $this->json([

                'success' => false,

                'message' =>
                'Department not found.'

            ], 404);
        }

        $designations =
            $this->designationModel
            ->getByDepartment(
                $departmentId
            );

        $this->json([

            'success' => true,

            'designations' =>
            $designations

        ]);
    }

    // Image Upload

    // Image Upload

    public function uploadImage(): void
    {
        /*
    |--------------------------------------------------------------------------
    | Employee Authentication
    |--------------------------------------------------------------------------
    */

        if (!Auth::isEmployee()) {

            $this->json([
                'success' => false,
                'message' => 'Only employees can upload profile images.'
            ], 403);

            return;
        }


        /*
    |--------------------------------------------------------------------------
    | Get Logged In User
    |--------------------------------------------------------------------------
    */

        $userId = Auth::userId();

        $user = $this->userModel->findById($userId);

        if (!$user) {

            $this->json([
                'success' => false,
                'message' => 'User not found.'
            ], 404);

            return;
        }


        /*
    |--------------------------------------------------------------------------
    | Check Uploaded File
    |--------------------------------------------------------------------------
    */

        if (
            !isset($_FILES['image']) ||
            !is_array($_FILES['image'])
        ) {

            $this->json([
                'success' => false,
                'message' => 'Please select an image.'
            ], 400);

            return;
        }


        $file = $_FILES['image'];


        /*
    |--------------------------------------------------------------------------
    | Upload Error
    |--------------------------------------------------------------------------
    */

        if ($file['error'] !== UPLOAD_ERR_OK) {

            $this->json([
                'success' => false,
                'message' => 'Unable to upload the image.'
            ], 400);

            return;
        }


        /*
    |--------------------------------------------------------------------------
    | Validate Temporary File
    |--------------------------------------------------------------------------
    */

        if (
            empty($file['tmp_name']) ||
            !is_uploaded_file($file['tmp_name'])
        ) {

            $this->json([
                'success' => false,
                'message' => 'Invalid uploaded file.'
            ], 400);

            return;
        }


        /*
    |--------------------------------------------------------------------------
    | Validate File Size
    |--------------------------------------------------------------------------
    */

        if ($file['size'] > 2 * 1024 * 1024) {

            $this->json([
                'success' => false,
                'message' => 'Profile image must be less than 2 MB.'
            ], 400);

            return;
        }


        /*
    |--------------------------------------------------------------------------
    | Detect MIME Type
    |--------------------------------------------------------------------------
    */

        $finfo = new finfo(FILEINFO_MIME_TYPE);

        $mimeType = $finfo->file(
            $file['tmp_name']
        );


        /*
    |--------------------------------------------------------------------------
    | Allowed Image Types
    |--------------------------------------------------------------------------
    */

        $allowedTypes = [

            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/webp' => 'webp'

        ];


        if (!isset($allowedTypes[$mimeType])) {

            $this->json([
                'success' => false,
                'message' => 'Only JPG, PNG and WEBP images are allowed.'
            ], 400);

            return;
        }


        /*
    |--------------------------------------------------------------------------
    | Local Image Directory
    |--------------------------------------------------------------------------
    */

        $imageDirectory =
            dirname(__DIR__)
            . DIRECTORY_SEPARATOR
            . 'public'
            . DIRECTORY_SEPARATOR
            . 'images'
            . DIRECTORY_SEPARATOR
            . 'employees';


        /*
    |--------------------------------------------------------------------------
    | Create Directory
    |--------------------------------------------------------------------------
    */

        if (!is_dir($imageDirectory)) {

            if (!mkdir($imageDirectory, 0755, true)) {

                $this->json([
                    'success' => false,
                    'message' => 'Unable to create image directory.'
                ], 500);

                return;
            }
        }


        /*
    |--------------------------------------------------------------------------
    | Generate Unique File Name
    |--------------------------------------------------------------------------
    */

        $extension = $allowedTypes[$mimeType];

        $fileName =
            'employee_'
            . $user['employee_id']
            . '_'
            . bin2hex(random_bytes(8))
            . '.'
            . $extension;


        /*
    |--------------------------------------------------------------------------
    | Local Destination
    |--------------------------------------------------------------------------
    */

        $destination =
            $imageDirectory
            . DIRECTORY_SEPARATOR
            . $fileName;


        /*
    |--------------------------------------------------------------------------
    | Save Image Locally
    |--------------------------------------------------------------------------
    */

        if (
            !move_uploaded_file(
                $file['tmp_name'],
                $destination
            )
        ) {

            $this->json([
                'success' => false,
                'message' => 'Unable to save profile image locally.'
            ], 500);

            return;
        }


        /*
    |--------------------------------------------------------------------------
    | Local Image URL
    |--------------------------------------------------------------------------
    */

        $localImageUrl =
            'public/images/employees/'
            . $fileName;


        /*
    |--------------------------------------------------------------------------
    | Upload To Cloudinary
    |--------------------------------------------------------------------------
    */

        try {

            $cloudinaryResult =
                $this->cloudService->uploadImage(
                    $destination
                );


            /*
        |--------------------------------------------------------------------------
        | Get Cloudinary URL
        |--------------------------------------------------------------------------
        */

            $cloudinaryImageUrl =
                $cloudinaryResult['secure_url'] ?? null;


            /*
        |--------------------------------------------------------------------------
        | Get Cloudinary Public ID
        |--------------------------------------------------------------------------
        */

            $cloudinaryPublicId =
                $cloudinaryResult['public_id'] ?? null;
        } catch (Throwable $e) {

            /*
        |--------------------------------------------------------------------------
        | Cloudinary Failed
        |--------------------------------------------------------------------------
        */

            if (is_file($destination)) {
                unlink($destination);
            }


            $this->json([
                'success' => false,
                'message' => 'Unable to upload image to Cloudinary.'
            ], 500);

            return;
        }


        /*
    |--------------------------------------------------------------------------
    | Validate Cloudinary Response
    |--------------------------------------------------------------------------
    */

        if (
            empty($cloudinaryImageUrl) ||
            empty($cloudinaryPublicId)
        ) {

            if (is_file($destination)) {
                unlink($destination);
            }

            $this->json([
                'success' => false,
                'message' => 'Cloudinary upload information is incomplete.'
            ], 500);

            return;
        }


        /*
    |--------------------------------------------------------------------------
    | Update Database
    |--------------------------------------------------------------------------
    */

        $updated =
            $this->userModel->uploadImage(
                $user['id'],
                $localImageUrl,
                $cloudinaryImageUrl,
                $cloudinaryPublicId
            );


        /*
    |--------------------------------------------------------------------------
    | Database Update Failed
    |--------------------------------------------------------------------------
    */

        if (!$updated) {

            /*
        |--------------------------------------------------------------------------
        | Remove Local File
        |--------------------------------------------------------------------------
        */

            if (is_file($destination)) {
                unlink($destination);
            }


            $this->json([
                'success' => false,
                'message' => 'Image uploaded but could not be saved in database.'
            ], 500);

            return;
        }


        /*
    |--------------------------------------------------------------------------
    | Session
    |--------------------------------------------------------------------------
    */

        $_SESSION['image_url'] =
            $localImageUrl;


        /*
    |--------------------------------------------------------------------------
    | Success Response
    |--------------------------------------------------------------------------
    */

        $this->json([
            'success' => true,

            'message' =>
            'Profile image uploaded successfully.',

            'image_url' =>
            '/' . $localImageUrl,

            'cloudinary_image_url' =>
            $cloudinaryImageUrl,

            'cloudinary_public_id' =>
            $cloudinaryPublicId
        ]);

        return;
    }


    public function removeImage(): void
    {
        /*
    |--------------------------------------------------------------------------
    | Authentication
    |--------------------------------------------------------------------------
    */

        $userId = Auth::userId();

        if (!$userId) {

            $this->json([
                'success' => false,
                'message' => 'User is not authenticated.'
            ], 401);

            return;
        }


        /*
    |--------------------------------------------------------------------------
    | Find User
    |--------------------------------------------------------------------------
    */

        $user = $this->userModel->findById($userId);

        if (!$user) {

            $this->json([
                'success' => false,
                'message' => 'User not found.'
            ], 404);

            return;
        }


        /*
    |--------------------------------------------------------------------------
    | Get Local Image Path
    |--------------------------------------------------------------------------
    */

        $localImagePath = trim(
            $user['image_url'] ?? ''
        );


        /*
    |--------------------------------------------------------------------------
    | Get Cloudinary Public ID
    |--------------------------------------------------------------------------
    */

        $cloudinaryPublicId = trim(
            $user['cloudinary_public_id'] ?? ''
        );


        /*
    |--------------------------------------------------------------------------
    | Check If Any Image Exists
    |--------------------------------------------------------------------------
    */

        if (
            $localImagePath === '' &&
            $cloudinaryPublicId === ''
        ) {

            $this->json([
                'success' => false,
                'message' => 'No profile image found.'
            ], 404);

            return;
        }


        /*
    |--------------------------------------------------------------------------
    | Delete Cloudinary Image
    |--------------------------------------------------------------------------
    */

        if ($cloudinaryPublicId !== '') {

            try {

                $deleted =
                    $this->cloudService->deleteImage(
                        $cloudinaryPublicId
                    );


                /*
            |--------------------------------------------------------------
            | Cloudinary Delete Failed
            |--------------------------------------------------------------
            */

                if (!$deleted) {

                    $this->json([
                        'success' => false,
                        'message' =>
                        'Unable to delete profile image from Cloudinary.'
                    ], 500);

                    return;
                }
            } catch (Throwable $e) {

                error_log(
                    'Cloudinary delete error: '
                        . $e->getMessage()
                );

                $this->json([
                    'success' => false,
                    'message' =>
                    'Unable to delete profile image from Cloudinary.'
                ], 500);

                return;
            }
        }


        /*
    |--------------------------------------------------------------------------
    | Delete Local Image
    |--------------------------------------------------------------------------
    */

        if ($localImagePath !== '') {

            /*
        |--------------------------------------------------------------
        | Normalize Path
        |--------------------------------------------------------------
        */

            $localImagePath =
                str_replace(
                    '\\',
                    '/',
                    $localImagePath
                );

            $localImagePath =
                ltrim(
                    $localImagePath,
                    '/'
                );


            /*
        |--------------------------------------------------------------
        | Project Root
        |--------------------------------------------------------------
        */

            $projectRoot = dirname(__DIR__);


            /*
        |--------------------------------------------------------------
        | Absolute Local File Path
        |--------------------------------------------------------------
        */

            $filePath =
                $projectRoot
                . DIRECTORY_SEPARATOR
                . str_replace(
                    '/',
                    DIRECTORY_SEPARATOR,
                    $localImagePath
                );


            /*
        |--------------------------------------------------------------
        | Delete Local File
        |--------------------------------------------------------------
        */

            if (is_file($filePath)) {

                if (!unlink($filePath)) {

                    /*
                |----------------------------------------------------------
                | Important:
                | Cloudinary has already been deleted.
                | Do not continue silently.
                |----------------------------------------------------------
                */

                    $this->json([
                        'success' => false,
                        'message' =>
                        'Cloudinary image deleted, but local image could not be deleted.'
                    ], 500);

                    return;
                }
            }
        }


        /*
    |--------------------------------------------------------------------------
    | Remove Image Information From Database
    |--------------------------------------------------------------------------
    */

        $removed =
            $this->userModel->removeimg(
                $userId
            );


        if (!$removed) {

            $this->json([
                'success' => false,
                'message' =>
                'Images deleted, but database update failed.'
            ], 500);

            return;
        }


        /*
    |--------------------------------------------------------------------------
    | Clear Session
    |--------------------------------------------------------------------------
    */

        $_SESSION['image_url'] = '';


        /*
    |--------------------------------------------------------------------------
    | Success
    |--------------------------------------------------------------------------
    */

        $this->json([
            'success' => true,
            'message' =>
            'Profile image removed successfully.'
        ]);

        return;
    }
}

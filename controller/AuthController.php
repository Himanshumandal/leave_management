<?php

   class AuthController extends Controller{
        private User $userModel;
        private Employee $employeeModel;

        public function __construct(PDO $db)
        {
            $this->userModel = new User($db);
            $this->employeeModel = new Employee($db);
        }

        public function login():void
        {
            // Get Input
           
            $email=trim($this->input('email',''));
            $password=trim($this->input('password',''));
            
        

                // Validate input
                $validator = new Validator([
                    'email' => $email,
                    'password' => $password
                ]);

                $validator
                    ->required('email')
                    ->email('email')
                    ->required('password')
                    ->minLength('password', 6);

                
                     // If validation fails
            if ($validator->fails()) {

                $this->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Find user
            $user = $this->userModel->findByEmail($email);
            $employee=[];
            if($user['role']==='employee'){

                $employee=$this->employeeModel->findByEmployee_id($user['employee_id']);
                if (!$employee) {

                    $this->json([
                        'success' => false,
                        'message' => 'Invalid Employee'
                    ], 401);
                }
            }

            // echo "<pre>";
            // print_r($employee);
            // echo "</pre>";

            // User doesn't exist
            if (!$user) {

                $this->json([
                    'success' => false,
                    'message' => 'Invalid email or password'
                ], 401);
            }
           

             // Verify password
            if (!password_verify($password, $user['password'])) {

                $this->json([
                    'success' => false,
                    'message' => 'Invalid email or password'
                ], 401);
            }

            // Check account status
            if ($user['status'] !== 'active') {

                $this->json([
                    'success' => false,
                    'message' => 'Your account is inactive'
                ], 403);
            }

            // Login
            $data = [

                'user' => $user,

                'employee' => $employee

            ];
            Auth::login($data);

            $redirect='';
            if(Auth::isAdmin()){
                $redirect='../../dashboard.php';
            }
            else{
                $redirect="../../employee/dashboard.php";
            }

            // Success response
            $this->json([
                'success' => true,
                'message' => 'Login successful',
                'redirect' => $redirect
            ]);
        }

        public function logout(): void
        {
            Auth::logout();

            $this->json([
                'success' => true,
                'message' => 'Logout successful',
                'redirect' => 'login.php'
            ]);
        }
   }
?>
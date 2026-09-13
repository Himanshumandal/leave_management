<?php

    class Auth{

        public static function login(array $data):void{
            session_regenerate_id(true);

            $_SESSION['user_id'] = $data['user']['id'] ;
            $_SESSION['role'] = $data['user']['role'] ;
            $_SESSION['image_url']=$data['user']['image_url'] ??'';
            $_SESSION['employee_id'] = $data['user']['employee_id'];
            $_SESSION['status'] = $data['user']['status'];
            $_SESSION['emp_id']=$data['employee']['id'] ?? '';
            $_SESSION['first_name'] = $data['employee']['first_name'] ??'';
            $_SESSION['last_name'] = $data['employee']['last_name']??'';
            $_SESSION['email'] = $data['employee']['email']??'';
            $_SESSION['department'] = $data['employee']['department']??'';
            $_SESSION['designation'] = $data['employee']['designation']??'';
            $_SESSION['logged_in'] = true;
        }

        public static function name():string{
            return $_SESSION['first_name']." ".$_SESSION['last_name'] ?? "Employee";
        }
        public static function image():string{
            return $_SESSION['image_url']??'';
        }

        public static function logout():void{
             
            $_SESSION=[];

            if(ini_get('session.use.cookies')){
                $params = session_get_cookie_params();

                setcookie(
                    session_name(),
                    '',
                    time() - 42000,
                    $params['path'],
                    $params['domain'],
                    $params['secure'],
                    $params['httponly']
                );
            }

            session_destroy();
        }

        public static function check(): bool
        {
            return isset($_SESSION['logged_in'])
                && $_SESSION['logged_in'] === true;
        }

        public static function userId(): ?int
        {
            return isset($_SESSION['user_id'])
                ? (int) $_SESSION['user_id']
                : null;
        }
        public static function emp_id(): ?int
        {
            return isset($_SESSION['emp_id'])
                ? (int) $_SESSION['emp_id']
                : null;
        }

        /**
         * Get employee ID
         */
        public static function employeeId(): ?string
        {
            return isset($_SESSION['employee_id'])
                ? $_SESSION['employee_id']
                : null;
        }

        public static function role():?string{
            return $_SESSION['role'] ?? null;
        }

        public static function isAdmin(): bool
        {
            return self::role() === 'admin';
        }

        /**
         * Check whether current user is employee
         */
        public static function isEmployee(): bool
        {
            return self::role() === 'employee';
        }


        public static function requireLogin(): void
        {
            if (!self::check()) {

                header('Location: ./auth/login.php');

                exit;
            }
        }


        public static function requireAdmin(): void
        {
            self::requireLogin();

            if (!self::isAdmin()) {

                // http_response_code(403);
                header("Location: ../admin/page.php");

                exit;
            }
        }

        public static function csrfToken(): string
        {
            if (empty($_SESSION['csrf_token'])) {
                $_SESSION['csrf_token'] = bin2hex(
                    random_bytes(32)
                );
            }

            return $_SESSION['csrf_token'];
        }

        public static function verifyCsrfToken(
            ?string $token
        ): bool {

            if (
                empty($token) ||
                empty($_SESSION['csrf_token'])
            ) {
                return false;
            }

            return hash_equals(
                $_SESSION['csrf_token'],
                $token
            );
        }

    }


?>
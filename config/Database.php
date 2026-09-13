
<?php 

    class Database{
        private static ?PDO $instance=null;

        private function __construct() {
        }

        public static function getInstance(): PDO {

            if(self::$instance === null){
                  $host =
                    env(
                        'DB_HOST',
                        'localhost'
                    );

                $dbname =
                    env(
                        'DB_NAME',
                        'leavemanagementsystem'
                    );

                $username =
                    env(
                        'DB_USERNAME',
                        'root'
                    );

                $password =
                    env(
                        'DB_PASSWORD',
                        ''
                    );


                $dbs="mysql:host=$host;dbname=$dbname;charset=utf8mb4";

                self::$instance=new PDO($dbs,$username,$password,[

                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]);
            }

            return self::$instance;
        }


    }
?>
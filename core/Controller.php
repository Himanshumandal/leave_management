
<?php 

    class Controller{

        protected function json(array $data,int $statusCode=200):void{
            http_response_code($statusCode);

            header("Content-Type: application/json");

            echo json_encode($data);

            exit;
        }

        protected function redirect(string $url):void{
            header("Location:".$url);
            exit;
        }
        protected function input(
            string $key,
            mixed $default = null
        ): mixed {
            return $_POST[$key] ?? $default;
        }

        protected function query(
            string $key,
            mixed $default = null
        ): mixed {
            return $_GET[$key] ?? $default;
        }

    }

?>
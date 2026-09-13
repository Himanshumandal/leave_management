<?php

    class Validator{
        private array $data;

        private array $errors = [];

        public function __construct(array $data)
        {
           $this->data=$data;
        }

        public function required(string $field):self{

            if (
                !isset($this->data[$field]) ||
                trim((string) $this->data[$field]) === ''
            ) {
                $this->errors[$field] = "$field is required";
            }

            return $this;
        }

        public function email(string $field):self{
            $value=$this->data[$field] ?? '';

            if($value !== '' && !filter_var($value,FILTER_VALIDATE_EMAIL)){
                $this->errors[$field] = "$field must be a valid email";

            }

            return $this;
        }

        public function minLength(string $field,int $length):self{
            $value = $this->data[$field] ?? '';

            if (
                $value !== '' &&
                strlen($value) < $length
            ) {
                $this->errors[$field] =
                    "$field must be at least $length characters";
            }

            return $this;
        }

        public function fails(): bool
        {
            return !empty($this->errors);
        }

        public function errors(): array
        {
            return $this->errors;
        }

        public function addError(
            string $field,
            string $message
        ): self {

            $this->errors[$field][] = $message;

            return $this;
        }
    }

?>
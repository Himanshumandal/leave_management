<?php

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

class MailService
{
    private PHPMailer $mail;

    public function __construct()
    {
        $config = require __DIR__ . '/../config/Mail.php';

        $this->mail = new PHPMailer(true);

        $this->mail->isSMTP();

        $this->mail->Host = $config['host'];

        $this->mail->SMTPAuth = true;

        $this->mail->Username = $config['username'];

        $this->mail->Password = $config['password'];

        $this->mail->Port = $config['port'];

        $this->mail->SMTPSecure = $config['encryption'];

        $this->mail->setFrom(
            $config['from_email'],
            $config['from_name']
        );

        $this->mail->isHTML(true);

        $this->mail->CharSet = 'UTF-8';
    }


    public function sendEmployeeCredentials(
        string $email,
        string $name,
        string $password
    ): bool {

        $this->mail->addAddress($email, $name);

        $this->mail->Subject =
            'Your Employee Management System Account';

        $this->mail->Body = "
            <h2>Welcome to Employee Management System</h2>

            <p>Hello {$name},</p>

            <p>
                Your employee account has been created.
            </p>

            <p>
                <strong>Login Email:</strong>
                {$email}
            </p>

            <p>
                <strong>Temporary Password:</strong>
                {$password}
            </p>

            <p>
                Please change your password after your first login.
            </p>

            <p>
                Regards,<br>
                Employee Management System
            </p>
        ";

        $this->mail->AltBody = "
            Welcome to Employee Management System.

            Login Email: {$email}

            Temporary Password: {$password}

            Please change your password after your first login.
        ";

        return $this->mail->send();
    }
}
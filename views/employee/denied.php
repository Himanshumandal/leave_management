<?php


$page = $_SESSION['error_page'] ?? [
    'code'    => 404,
    'title'   => 'Page Not Found',
    'message' => 'The page you are looking for could not be found.',
    'icon'    => 'bi-file-earmark-x'
];

// Remove message after reading
unset($_SESSION['error_page']);

http_response_code($page['code']);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= htmlspecialchars($page['code']) ?> - <?= htmlspecialchars($page['title']) ?></title>

    <!-- Bootstrap -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <!-- Bootstrap Icons -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
        rel="stylesheet"
    >

    <style>
        body {
            min-height: 100vh;
            background: #f8f9fa;
        }

        .error-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }

        .error-card {
            max-width: 500px;
            width: 100%;
            text-align: center;
            background: #fff;
            border: 1px solid #e9ecef;
            border-radius: 16px;
            padding: 45px 30px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.06);
        }

        .error-icon {
            width: 75px;
            height: 75px;
            margin: 0 auto 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background: #f1f3f5;
            color: #495057;
            font-size: 30px;
        }

        .error-code {
            font-size: 60px;
            font-weight: 700;
            line-height: 1;
        }

        .error-title {
            margin-top: 15px;
            font-size: 24px;
            font-weight: 600;
        }

        .error-message {
            color: #6c757d;
            margin: 10px 0 25px;
        }
    </style>
</head>

<body>

<div class="error-wrapper">

    <div class="error-card">

        <div class="error-icon">
            <i class="bi <?= htmlspecialchars($page['icon']) ?>"></i>
        </div>

        <div class="error-code">
            <?= htmlspecialchars($page['code']) ?>
        </div>

        <h1 class="error-title">
            <?= htmlspecialchars($page['title']) ?>
        </h1>

        <p class="error-message">
            <?= htmlspecialchars($page['message']) ?>
        </p>

    </div>

</div>

</body>
</html>

<?php
session_start();
require_once 'config/db.php';

$error = '';

if (!isset($_SESSION['user_id']) && isset($_COOKIE['remember_user'])) {
    $cookie_user_id = intval($_COOKIE['remember_user']);

    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$cookie_user_id]);
        $user = $stmt->fetch();

        if ($user) {
            $clean_role = strtolower(trim($user['role']));

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_role'] = $clean_role;
        }
    } catch (PDOException $e) {
    }
}

if (isset($_SESSION['user_id'])) {
    $session_role = strtolower(trim($_SESSION['user_role'] ?? ''));

    if ($session_role === 'admin') {
        header("Location: admin/dashboard.php");
    } else {
        header("Location: index.php");
    }
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $error = "Please fill in all fields!";
    } else {
        try {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {

                $clean_role = strtolower(trim($user['role']));

                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_role'] = $clean_role;

                if (isset($_POST['remember_me'])) {
                    setcookie(
                        'remember_user',
                        $user['id'],
                        time() + (86400 * 30),
                        "/"
                    );
                }

                if ($clean_role === 'admin') {
                    header("Location: admin/dashboard.php");
                } else {
                    header("Location: index.php");
                }

                exit;
            } else {
                $error = "Invalid email or password!";
            }
        } catch (PDOException $e) {
            $error = "Something went wrong: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>BLOG MANAGEMENT</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <style>
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #e38787, #1244c1);
        }

        .login-card {
            border: none;
            border-radius: 20px;
            overflow: hidden;
        }

        .card-header {
            padding: 25px;
        }

        .logo-icon {
            font-size: 50px;
        }

        .form-control {
            border-radius: 10px;
            padding: 12px;
        }

        .btn-login {
            border-radius: 10px;
            padding: 12px;
            font-size: 18px;
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100">

            <div class="col-md-6 col-lg-5">

                <div class="card shadow-lg login-card">

                    <div class="card-header bg-primary text-white text-center">
                        <i class="bi bi-shield-lock-fill logo-icon"></i>
                        <h3 class="mt-2 mb-0">System Login</h3>
                        <small>Sign in to continue</small>
                    </div>

                    <div class="card-body p-4">

                        <?php if (!empty($error)): ?>
                            <div class="alert alert-danger alert-dismissible fade show">
                                <?php echo htmlspecialchars($error); ?>
                                <button type="button"
                                    class="btn-close"
                                    data-bs-dismiss="alert">
                                </button>
                            </div>
                        <?php endif; ?>

                        <form action="login.php" method="POST">

                            <div class="mb-3">
                                <label class="form-label">
                                    <i class="bi bi-envelope-fill"></i>
                                    Email Address
                                </label>

                                <input type="email"
                                    name="email"
                                    class="form-control"
                                    placeholder="Enter your email"
                                    required
                                    value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">
                                    <i class="bi bi-lock-fill"></i>
                                    Password
                                </label>

                                <input type="password"
                                    name="password"
                                    class="form-control"
                                    placeholder="Enter your password"
                                    required>
                            </div>

                            <div class="form-check mb-4">
                                <input class="form-check-input"
                                    type="checkbox"
                                    name="remember_me"
                                    id="remember_me">

                                <label class="form-check-label"
                                    for="remember_me">
                                    Remember Me
                                </label>
                            </div>

                            <div class="d-grid">
                                <button type="submit"
                                    class="btn btn-primary btn-login">
                                    <i class="bi bi-box-arrow-in-right"></i>
                                    Login
                                </button>
                            </div>

                        </form>

                    </div>

                    <div class="card-footer text-center bg-light py-3">
                        New author?
                        <a href="register.php"
                            class="text-decoration-underline fw-semibold">
                            Register here
                        </a>
                    </div>

                </div>

            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>
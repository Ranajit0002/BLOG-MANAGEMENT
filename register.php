<?php
session_start();

if (isset($_SESSION['user_id'])) {
    if ($_SESSION['user_role'] !== 'admin') {
        header("Location: index.php");
        exit;
    }
}

require_once 'config/db.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($name) || empty($email) || empty($password)) {
        $error = "All fields are required!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format!";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters long!";
    } else {
        try {
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);

            if ($stmt->fetch()) {
                $error = "This email is already registered!";
            } else {
                $count_stmt = $pdo->query("SELECT COUNT(*) FROM users");
                $user_count = $count_stmt->fetchColumn();

                $role = ($user_count == 0) ? 'admin' : 'author';

                $hashed_password = password_hash($password, PASSWORD_BCRYPT);

                $insert_stmt = $pdo->prepare(
                    "INSERT INTO users (name, email, password, role)
                     VALUES (?, ?, ?, ?)"
                );

                $insert_stmt->execute([
                    $name,
                    $email,
                    $hashed_password,
                    $role
                ]);

                $success = "Registration successful as " .
                    ucfirst($role) .
                    "! You can now log in.";
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <style>
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #e38787, #1244c1);
        }

        .register-card {
            border: none;
            border-radius: 20px;
            overflow: hidden;
        }

        .card-header {
            padding: 25px;
        }

        .form-control {
            padding: 12px;
            border-radius: 10px;
        }

        .btn-register {
            padding: 12px;
            font-size: 18px;
            border-radius: 10px;
        }

        .logo-icon {
            font-size: 50px;
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100">

            <div class="col-md-6 col-lg-5">

                <div class="card shadow-lg register-card">

                    <div class="card-header bg-primary text-white text-center">
                        <i class="bi bi-person-plus-fill logo-icon"></i>
                        <h3 class="mt-2 mb-0">System Registration</h3>
                        <small>Create your account</small>
                    </div>

                    <div class="card-body p-4">

                        <?php if (!empty($error)): ?>
                            <div class="alert alert-danger alert-dismissible fade show">
                                <?php echo htmlspecialchars($error); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($success)): ?>
                            <div class="alert alert-success alert-dismissible fade show">
                                <?php echo htmlspecialchars($success); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <form action="register.php" method="POST">

                            <div class="mb-3">
                                <label class="form-label">
                                    <i class="bi bi-person-fill"></i>
                                    Full Name
                                </label>
                                <input
                                    type="text"
                                    name="name"
                                    class="form-control"
                                    placeholder="Enter your full name"
                                    required
                                    value="<?php echo isset($name) ? htmlspecialchars($name) : ''; ?>">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">
                                    <i class="bi bi-envelope-fill"></i>
                                    Email Address
                                </label>
                                <input
                                    type="email"
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
                                <input
                                    type="password"
                                    name="password"
                                    class="form-control"
                                    placeholder="Enter password"
                                    required>

                                <small class="text-muted">
                                    Password must contain at least 6 characters.
                                </small>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-register">
                                    <i class="bi bi-person-check-fill"></i>
                                    Register
                                </button>
                            </div>

                        </form>

                    </div>

                    <div class="card-footer text-center bg-light py-3">
                        Already have an account?
                        <a href="login.php" class="text-decoration-underline fw-semibold">
                            Login here
                        </a>
                    </div>

                </div>

            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>
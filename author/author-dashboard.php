<?php
require_once '../includes/auth.php';
checkAccess('author');
require_once '../config/db.php';

$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT COUNT(*) as total, 
    SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) as approved,
    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending 
    FROM blogs WHERE user_id = ?");
$stmt->execute([$user_id]);
$stats = $stmt->fetch();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>BLOG MANAGEMENT</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">

    <style>
        body {
            background: #f4f6f9;
        }

        .hero-section {
            background: linear-gradient(135deg, #0d6efd, #6610f2);
            color: white;
            border-radius: 20px;
            padding: 40px;
            margin-bottom: 30px;
        }

        .stat-card {
            border: none;
            border-radius: 20px;
            transition: 0.3s;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-icon {
            font-size: 40px;
            opacity: 0.2;
            position: absolute;
            right: 20px;
            top: 20px;
        }

        .welcome-card {
            border-radius: 20px;
            border: none;
        }

        .action-btn {
            min-width: 200px;
        }
    </style>
</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow sticky-top">

        <div class="container">

            <a href="author-dashboard.php"
                class="navbar-brand fw-bold">
                <i class="fa-solid fa-pen-nib me-2"></i>
                Author Panel
            </a>

            <button class="navbar-toggler"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#navbarMenu">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse"
                id="navbarMenu">

                <ul class="navbar-nav me-auto">

                    <li class="nav-item">
                        <a class="nav-link active"
                            href="author-dashboard.php">
                            Dashboard
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link"
                            href="create-blog.php">
                            Create Blog
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link"
                            href="my-blogs.php">
                            My Blogs
                        </a>
                    </li>

                </ul>

                <div class="d-flex align-items-center gap-3">

                    <span class="text-light">
                        Hello,
                        <strong>
                            <?php echo htmlspecialchars($_SESSION['user_name']); ?>
                        </strong>
                    </span>

                    <a href="../index.php"
                        class="btn btn-outline-light btn-sm">
                        Home
                    </a>

                    <a href="../logout.php"
                        class="btn btn-danger btn-sm">
                        Logout
                    </a>

                </div>

            </div>

        </div>

    </nav>

    <div class="container py-4">

        <div class="hero-section shadow">

            <div class="row align-items-center">

                <div class="col-md-8">

                    <h2 class="fw-bold mb-2">
                        Welcome,
                        <?php echo htmlspecialchars($_SESSION['user_name']); ?>
                    </h2>

                    <p class="mb-0 fs-5">
                        Manage your articles, track approvals, and publish new content.
                    </p>

                </div>

                <div class="col-md-4 text-md-end mt-3 mt-md-0">

                    <i class="fa-solid fa-pen-fancy fa-4x opacity-75"></i>

                </div>

            </div>

        </div>

        <div class="row g-4 mb-4">

            <div class="col-md-4">

                <div class="card stat-card bg-primary text-white shadow position-relative">

                    <div class="card-body p-4">

                        <i class="fa-solid fa-file-lines stat-icon"></i>

                        <h1 class="fw-bold">
                            <?php echo (int)$stats['total']; ?>
                        </h1>

                        <h5>Total Blogs</h5>

                        <p class="mb-0">
                            Articles created by you.
                        </p>

                    </div>

                </div>

            </div>

            <div class="col-md-4">

                <div class="card stat-card bg-success text-white shadow position-relative">

                    <div class="card-body p-4">

                        <i class="fa-solid fa-circle-check stat-icon"></i>

                        <h1 class="fw-bold">
                            <?php echo (int)$stats['approved']; ?>
                        </h1>

                        <h5>Approved Blogs</h5>

                        <p class="mb-0">
                            Published and visible posts.
                        </p>

                    </div>

                </div>

            </div>

            <div class="col-md-4">

                <div class="card stat-card bg-warning text-dark shadow position-relative">

                    <div class="card-body p-4">

                        <i class="fa-solid fa-clock stat-icon"></i>

                        <h1 class="fw-bold">
                            <?php echo (int)$stats['pending']; ?>
                        </h1>

                        <h5>Pending Review</h5>

                        <p class="mb-0">
                            Awaiting admin approval.
                        </p>

                    </div>

                </div>

            </div>

        </div>

        <div class="card welcome-card shadow-lg">

            <div class="card-body text-center py-5">

                <div class="mb-4">
                    <i class="fa-solid fa-book-open fa-4x text-primary"></i>
                </div>

                <h2 class="fw-bold mb-3">
                    Welcome to Your Writing Workspace
                </h2>

                <p class="text-muted fs-5 mb-4">
                    Create engaging content, manage your published articles,
                    and monitor approval status from the admin panel.
                </p>

                <div class="d-flex flex-column flex-md-row justify-content-center gap-3">

                    <a href="create-blog.php"
                        class="btn btn-success btn-lg action-btn">

                        <i class="fa-solid fa-pen me-2"></i>
                        Write New Blog

                    </a>

                    <a href="my-blogs.php"
                        class="btn btn-primary btn-lg action-btn">

                        <i class="fa-solid fa-list me-2"></i>
                        View My Blogs

                    </a>

                </div>

            </div>

        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>
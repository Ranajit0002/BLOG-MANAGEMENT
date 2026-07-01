<?php
require_once '../includes/auth.php';
checkAccess('author');
require_once '../config/db.php';

$user_id = $_SESSION['user_id'];
$success = '';
$error = '';

if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $blog_id = (int)$_GET['id'];

    try {
        $check = $pdo->prepare("SELECT id FROM blogs WHERE id = ? AND user_id = ?");
        $check->execute([$blog_id, $user_id]);
        $blog = $check->fetch();

        if ($blog) {
            $del = $pdo->prepare("UPDATE blogs SET status = 'hidden' WHERE id = ?");
            $del->execute([$blog_id]);
            $success = "Blog article removed successfully.";
        } else {
            $error = "Unauthorized action or blog post not found.";
        }
    } catch (PDOException $e) {
        $error = "Database Error: " . $e->getMessage();
    }
}

$my_blogs = [];
try {
    $stmt = $pdo->prepare("SELECT b.*, c.category_name FROM blogs b 
        JOIN categories c ON b.category_id = c.id 
        WHERE b.user_id = ? AND b.status != 'hidden' ORDER BY b.id DESC");
    $stmt->execute([$user_id]);
    $my_blogs = $stmt->fetchAll();
} catch (PDOException $e) {
    $error = "Error loading blogs: " . $e->getMessage();
}
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
            background: linear-gradient(135deg, #198754, #0d6efd);
            color: white;
            border-radius: 20px;
            padding: 35px;
            margin-bottom: 30px;
        }

        .blog-card {
            border: none;
            border-radius: 20px;
            overflow: hidden;
        }

        .blog-image {
            width: 90px;
            height: 70px;
            object-fit: cover;
            border-radius: 12px;
        }

        .table tbody tr:hover {
            background: #f8f9fa;
        }

        .stat-card {
            border: none;
            border-radius: 20px;
            transition: 0.3s;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .action-btn {
            min-width: 95px;
        }

        .table td,
        .table th {
            vertical-align: middle;
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
                        <a class="nav-link"
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
                        <a class="nav-link active"
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
                        <i class="fa-solid fa-book-open me-2"></i>
                        My Blog Articles
                    </h2>

                    <p class="mb-0">
                        Manage your articles, monitor approval status,
                        and update your content.
                    </p>

                </div>

                <div class="col-md-4 text-md-end mt-3 mt-md-0">

                    <a href="author-dashboard.php"
                        class="btn btn-light">

                        <i class="fa-solid fa-arrow-left me-2"></i>
                        Dashboard

                    </a>

                </div>

            </div>

        </div>

        <?php if (!empty($success)): ?>

            <div class="alert alert-success alert-dismissible fade show shadow-sm">

                <i class="fa-solid fa-circle-check me-2"></i>

                <?php echo htmlspecialchars($success); ?>

                <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert">
                </button>

            </div>

        <?php endif; ?>

        <?php if (!empty($error)): ?>

            <div class="alert alert-danger alert-dismissible fade show shadow-sm">

                <i class="fa-solid fa-circle-exclamation me-2"></i>

                <?php echo htmlspecialchars($error); ?>

                <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert">
                </button>

            </div>

        <?php endif; ?>

        <div class="row mb-4 g-4">

            <div class="col-md-4">

                <div class="card stat-card bg-primary text-white shadow">

                    <div class="card-body text-center">

                        <i class="fa-solid fa-file-lines fa-2x mb-3"></i>

                        <h2>
                            <?php echo count($my_blogs); ?>
                        </h2>

                        <p class="mb-0">
                            Total Blogs
                        </p>

                    </div>

                </div>

            </div>

            <div class="col-md-4">

                <div class="card stat-card bg-success text-white shadow">

                    <div class="card-body text-center">

                        <i class="fa-solid fa-circle-check fa-2x mb-3"></i>

                        <h2>
                            <?php
                            echo count(array_filter($my_blogs, function ($b) {
                                return strtolower($b['status']) == 'approved';
                            }));
                            ?>
                        </h2>

                        <p class="mb-0">
                            Approved
                        </p>

                    </div>

                </div>

            </div>

            <div class="col-md-4">

                <div class="card stat-card bg-warning text-dark shadow">

                    <div class="card-body text-center">

                        <i class="fa-solid fa-clock fa-2x mb-3"></i>

                        <h2>
                            <?php
                            echo count(array_filter($my_blogs, function ($b) {
                                return strtolower($b['status']) == 'pending';
                            }));
                            ?>
                        </h2>

                        <p class="mb-0">
                            Pending
                        </p>

                    </div>

                </div>

            </div>

        </div>

        <div class="card blog-card shadow-lg">

            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">

                <h4 class="mb-0 fw-bold">
                    Blog List
                </h4>

                <a href="create-blog.php"
                    class="btn btn-success">

                    <i class="fa-solid fa-plus me-2"></i>
                    New Blog

                </a>

            </div>

            <div class="card-body p-0">

                <div class="table-responsive">

                    <table class="table table-hover align-middle mb-0">

                        <thead class="table-dark text-center">

                            <tr>
                                <th>Thumbnail</th>
                                <th class="text-start">Title</th>
                                <th>Category</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>

                        </thead>

                        <tbody>

                            <?php if (empty($my_blogs)): ?>

                                <tr>

                                    <td colspan="5"
                                        class="text-center py-5 text-muted">

                                        <i class="fa-solid fa-folder-open fa-3x d-block mb-3"></i>

                                        No blogs found.

                                    </td>

                                </tr>

                            <?php else: ?>

                                <?php foreach ($my_blogs as $post): ?>

                                    <?php
                                    $blogStatus = strtolower(trim($post['status']));
                                    ?>

                                    <tr>

                                        <td class="text-center">

                                            <img src="../assets/images/<?php echo htmlspecialchars($post['thumbnail']); ?>"
                                                class="blog-image shadow-sm border">

                                        </td>

                                        <td>

                                            <strong>
                                                <?php echo htmlspecialchars($post['title']); ?>
                                            </strong>

                                        </td>

                                        <td class="text-center">

                                            <span class="badge bg-secondary">
                                                <?php echo htmlspecialchars($post['category_name']); ?>
                                            </span>

                                        </td>

                                        <td class="text-center">

                                            <?php if ($blogStatus === 'approved'): ?>

                                                <span class="badge bg-success px-3 py-2">
                                                    <i class="fa-solid fa-check me-1"></i>
                                                    Approved
                                                </span>

                                            <?php elseif ($blogStatus === 'pending'): ?>

                                                <span class="badge bg-warning text-dark px-3 py-2">
                                                    <i class="fa-solid fa-clock me-1"></i>
                                                    Pending
                                                </span>

                                            <?php else: ?>

                                                <span class="badge bg-danger px-3 py-2">
                                                    <i class="fa-solid fa-xmark me-1"></i>
                                                    Rejected
                                                </span>

                                            <?php endif; ?>

                                        </td>

                                        <td class="text-center">

                                            <a href="edit-blog.php?id=<?php echo $post['id']; ?>"
                                                class="btn btn-warning btn-sm action-btn me-1">

                                                <i class="fa-solid fa-pen"></i>

                                            </a>

                                            <a href="my-blogs.php?action=delete&id=<?php echo $post['id']; ?>"
                                                class="btn btn-danger btn-sm action-btn"
                                                onclick="return confirm('Delete this blog?')">

                                                <i class="fa-solid fa-trash"></i>

                                            </a>

                                        </td>

                                    </tr>

                                <?php endforeach; ?>

                            <?php endif; ?>

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>
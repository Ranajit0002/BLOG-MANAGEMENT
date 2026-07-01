<?php
require_once '../includes/auth.php';
checkAccess('admin');
require_once '../config/db.php';

$success = '';
$error = '';

if (isset($_GET['action']) && $_GET['action'] === 'revoke' && isset($_GET['id'])) {
    $blog_id = (int)$_GET['id'];

    $checkStatus = $pdo->prepare("SELECT status FROM blogs WHERE id = ?");
    $checkStatus->execute([$blog_id]);
    $current_status = $checkStatus->fetchColumn();

    if ($current_status === 'hidden') {
        $error = "Aborted. This blog was already deleted by its author and cannot be revoked.";
    } else {
        $stmt = $pdo->prepare("UPDATE blogs SET status = 'pending' WHERE id = ?");
        if ($stmt->execute([$blog_id])) {
            $success = "Blog access privileges revoked. Article moved back to the Pending Review queue.";
        } else {
            $error = "Failed to revoke the article.";
        }
    }
}

if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $blog_id = (int)$_GET['id'];

    $check = $pdo->prepare("SELECT thumbnail FROM blogs WHERE id = ?");
    $check->execute([$blog_id]);
    $blog = $check->fetch();

    if ($blog) {
        $filepath = "../uploads/blog-images/" . $blog['thumbnail'];
        if (file_exists($filepath)) {
            unlink($filepath);
        }

        $del = $pdo->prepare("DELETE FROM blogs WHERE id = ?");
        if ($del->execute([$blog_id])) {
            $success = "Blog article and local assets permanently deleted from the database.";
        } else {
            $error = "Failed to permanently delete the article.";
        }
    }
}

$stmt = $pdo->query("SELECT b.*, c.category_name, u.name as author_name FROM blogs b JOIN categories c ON b.category_id = c.id JOIN users u ON b.user_id = u.id WHERE b.status IN ('approved', 'hidden') ORDER BY b.id DESC");
$approved_blogs = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>BLOG MANAGEMENT</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">

    <style>
        body {
            background: #e9e9e9;
        }

        .navbar-brand {
            font-weight: 700;
        }

        .page-header {
            background: linear-gradient(135deg, #737b78, #588fe2, #64e464);
            color: white;
            border-radius: 20px;
            padding: 30px;
            margin-bottom: 25px;
        }

        .blog-thumbnail {
            width: 90px;
            height: 65px;
            object-fit: cover;
            border-radius: 10px;
        }

        .table>tbody>tr:hover {
            background: #f8f9fa;
        }

        .action-btn {
            min-width: 110px;
        }

        .modal-image {
            width: 100%;
            max-height: 350px;
            object-fit: cover;
            border-radius: 15px;
        }

        .content-box {
            white-space: pre-line;
            line-height: 1.8;
            font-size: 15px;
        }

        .status-live {
            background: #198754;
        }

        .status-hidden {
            background: #dc3545;
        }
    </style>
</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow sticky-top">
        <div class="container-fluid">

            <a class="navbar-brand" href="dashboard.php">
                <i class="fa-solid fa-user-shield me-2"></i>
                Admin Panel
            </a>

            <button class="navbar-toggler"
                data-bs-toggle="collapse"
                data-bs-target="#navbarMenu">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse"
                id="navbarMenu">

                <ul class="navbar-nav me-auto">

                    <li class="nav-item">
                        <a class="nav-link"
                            href="categories.php">
                            Categories
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link"
                            href="pending-blogs.php">
                            Pending
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link active"
                            href="approved-blogs.php">
                            Approved
                        </a>
                    </li>

                </ul>

                <div class="d-flex align-items-center gap-3">

                    <span class="text-light">
                        Welcome,
                        <strong>
                            <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Admin'); ?>
                        </strong>
                    </span>

                    <a href="dashboard.php"
                        class="btn btn-outline-light btn-sm">
                        Dashboard
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

        <div class="page-header shadow">

            <div class="row align-items-center">

                <div class="col-md-8">
                    <h2 class="fw-bold mb-2">
                        <i class="fa-solid fa-circle-check me-2"></i>
                        Approved Blogs Management
                    </h2>

                    <p class="mb-0">
                        Manage approved articles, revoke approval, and permanently remove blogs.
                    </p>
                </div>

                <div class="col-md-4 text-md-end mt-3 mt-md-0">
                    <span class="badge bg-light text-dark fs-6 p-3">
                        Total Blogs:
                        <?php echo count($approved_blogs); ?>
                    </span>
                </div>

            </div>

        </div>

        <?php if (!empty($success)): ?>
            <div class="alert alert-success shadow-sm">
                <i class="fa-solid fa-circle-check me-2"></i>
                <?php echo $success; ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger shadow-sm">
                <i class="fa-solid fa-circle-exclamation me-2"></i>
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <div class="card border-0 shadow-lg rounded-4">

            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold">
                    Approved Articles
                </h5>
            </div>

            <div class="card-body p-0">

                <div class="table-responsive">

                    <table class="table table-hover align-middle mb-0">

                        <thead class="table-dark text-center">

                            <tr>
                                <th>Image</th>
                                <th>Title</th>
                                <th>Author</th>
                                <th>Category</th>
                                <th>Status</th>
                                <th>Preview</th>
                                <th>Revoke</th>
                                <th>Delete</th>
                            </tr>

                        </thead>

                        <tbody>

                            <?php if (empty($approved_blogs)): ?>

                                <tr>
                                    <td colspan="8"
                                        class="text-center py-5 text-muted">
                                        <i class="fa-solid fa-folder-open fa-3x mb-3 d-block"></i>
                                        No approved blogs found.
                                    </td>
                                </tr>

                            <?php else: ?>

                                <?php foreach ($approved_blogs as $post): ?>

                                    <tr class="text-center">

                                        <td>
                                            <img src="../assets/images/<?php echo htmlspecialchars($post['thumbnail']); ?>"
                                                class="blog-thumbnail border shadow-sm">
                                        </td>

                                        <td class="text-start fw-semibold">
                                            <?php echo htmlspecialchars($post['title']); ?>
                                        </td>

                                        <td>
                                            <span class="badge bg-info text-dark">
                                                <?php echo htmlspecialchars($post['author_name']); ?>
                                            </span>
                                        </td>

                                        <td>
                                            <span class="badge bg-secondary">
                                                <?php echo htmlspecialchars($post['category_name']); ?>
                                            </span>
                                        </td>

                                        <td>

                                            <?php if ($post['status'] === 'hidden'): ?>

                                                <span class="badge status-hidden">
                                                    Deleted by Author
                                                </span>

                                            <?php else: ?>

                                                <span class="badge status-live">
                                                    Live
                                                </span>

                                            <?php endif; ?>

                                        </td>

                                        <td>

                                            <button class="btn btn-primary btn-sm action-btn"
                                                data-bs-toggle="modal"
                                                data-bs-target="#blogModal<?php echo $post['id']; ?>">

                                                <i class="fa-solid fa-eye me-1"></i>
                                                View

                                            </button>

                                        </td>

                                        <td>

                                            <?php if ($post['status'] === 'hidden'): ?>

                                                <button class="btn btn-warning btn-sm action-btn"
                                                    disabled>
                                                    Disabled
                                                </button>

                                            <?php else: ?>

                                                <a href="approved-blogs.php?action=revoke&id=<?php echo $post['id']; ?>"
                                                    class="btn btn-warning btn-sm action-btn"
                                                    onclick="return confirm('Move this blog back to pending review?')">

                                                    Revoke

                                                </a>

                                            <?php endif; ?>

                                        </td>

                                        <td>

                                            <a href="approved-blogs.php?action=delete&id=<?php echo $post['id']; ?>"
                                                class="btn btn-danger btn-sm action-btn"
                                                onclick="return confirm('Permanently delete this blog?')">

                                                Delete

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

    <?php foreach ($approved_blogs as $post): ?>

        <div class="modal fade"
            id="blogModal<?php echo $post['id']; ?>"
            tabindex="-1">

            <div class="modal-dialog modal-xl modal-dialog-scrollable">

                <div class="modal-content border-0 shadow">

                    <div class="modal-header bg-dark text-white">

                        <h5 class="modal-title">
                            <?php echo htmlspecialchars($post['title']); ?>
                        </h5>

                        <button type="button"
                            class="btn-close btn-close-white"
                            data-bs-dismiss="modal">
                        </button>

                    </div>

                    <div class="modal-body">

                        <div class="mb-4">

                            <img src="../assets/images/<?php echo htmlspecialchars($post['thumbnail']); ?>"
                                class="modal-image shadow">

                        </div>

                        <div class="mb-3">

                            <span class="badge bg-info text-dark me-2">
                                Author:
                                <?php echo htmlspecialchars($post['author_name']); ?>
                            </span>

                            <span class="badge bg-secondary me-2">
                                <?php echo htmlspecialchars($post['category_name']); ?>
                            </span>

                            <?php if ($post['status'] === 'hidden'): ?>

                                <span class="badge bg-danger">
                                    Hidden
                                </span>

                            <?php else: ?>

                                <span class="badge bg-success">
                                    Approved
                                </span>

                            <?php endif; ?>

                        </div>

                        <div class="card border-0 shadow-sm">

                            <div class="card-body content-box">

                                <?php
                                echo !empty($post['content'])
                                    ? htmlspecialchars($post['content'])
                                    : htmlspecialchars($post['description']);
                                ?>

                            </div>

                        </div>

                    </div>

                    <div class="modal-footer">

                        <button class="btn btn-secondary"
                            data-bs-dismiss="modal">
                            Close
                        </button>

                    </div>

                </div>

            </div>

        </div>

    <?php endforeach; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>
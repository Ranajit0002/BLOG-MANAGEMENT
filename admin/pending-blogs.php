<?php
require_once '../includes/auth.php';
checkAccess('admin');
require_once '../config/db.php';

$success_msg = '';
$error_msg = '';

if (isset($_GET['id']) && isset($_GET['status'])) {
    $id = (int)$_GET['id'];
    $status = $_GET['status'];

    if (in_array($status, ['approved', 'rejected'])) {
        try {
            $stmt = $pdo->prepare("UPDATE blogs SET status = ? WHERE id = ? AND status = 'pending'");
            $stmt->execute([$status, $id]);

            if ($stmt->rowCount() > 0) {
                $success_msg = "Content operational status flagged as: " . ucfirst($status) . ".";
            } else {
                $error_msg = "Action unavailable. This post may have been deleted or already processed.";
            }
        } catch (PDOException $e) {
            $error_msg = "Database Error: " . $e->getMessage();
        }
    }
}

$pending_blogs = [];
try {
    $stmt = $pdo->query("SELECT b.*, u.name as author_name, c.category_name FROM blogs b 
        JOIN users u ON b.user_id = u.id 
        JOIN categories c ON b.category_id = c.id 
        WHERE b.status = 'pending' ORDER BY b.id DESC");
    $pending_blogs = $stmt->fetchAll();
} catch (PDOException $e) {
    $error_msg = "Error loading queue: " . $e->getMessage();
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
            background: #e9e9e9;
        }

        .page-header {
            background: linear-gradient(135deg, #ffc107, #fd7e14);
            color: white;
            border-radius: 20px;
            padding: 30px;
            margin-bottom: 25px;
        }

        .blog-image {
            width: 90px;
            height: 65px;
            object-fit: cover;
            border-radius: 10px;
        }

        .table tbody tr:hover {
            background: #f8f9fa;
        }

        .preview-image {
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

        .action-btn {
            min-width: 100px;
        }
    </style>
</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow sticky-top">

        <div class="container-fluid">

            <a href="dashboard.php"
                class="navbar-brand fw-bold">
                <i class="fa-solid fa-user-shield me-2"></i>
                Admin Panel
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
                            href="categories.php">
                            Categories
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link active"
                            href="pending-blogs.php">
                            Pending
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link"
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
                        <i class="fa-solid fa-clock me-2"></i>
                        Pending Review Queue
                    </h2>

                    <p class="mb-0">
                        Review, approve, or reject submitted blog articles.
                    </p>

                </div>

                <div class="col-md-4 text-md-end mt-3 mt-md-0">

                    <span class="badge bg-light text-dark fs-6 p-3">
                        Pending:
                        <?php echo count($pending_blogs); ?>
                    </span>

                </div>

            </div>

        </div>

        <?php if (!empty($success_msg)): ?>

            <div class="alert alert-success shadow-sm">
                <i class="fa-solid fa-circle-check me-2"></i>
                <?php echo $success_msg; ?>
            </div>

        <?php endif; ?>

        <?php if (!empty($error_msg)): ?>

            <div class="alert alert-danger shadow-sm">
                <i class="fa-solid fa-circle-exclamation me-2"></i>
                <?php echo $error_msg; ?>
            </div>

        <?php endif; ?>

        <div class="card border-0 shadow-lg rounded-4">

            <div class="card-header bg-white py-3">

                <h5 class="mb-0 fw-bold">
                    Pending Articles
                </h5>

            </div>

            <div class="card-body p-0">

                <div class="table-responsive">

                    <table class="table table-hover align-middle mb-0">

                        <thead class="table-warning text-center">

                            <tr>
                                <th>Image</th>
                                <th class="text-start">Article</th>
                                <th>Author</th>
                                <th>Category</th>
                                <th>Preview</th>
                                <th>Actions</th>
                            </tr>

                        </thead>

                        <tbody>

                            <?php if (empty($pending_blogs)): ?>

                                <tr>

                                    <td colspan="6"
                                        class="text-center py-5 text-muted">

                                        <i class="fa-solid fa-check-circle fa-3x text-success d-block mb-3"></i>

                                        No articles are waiting for review.

                                    </td>

                                </tr>

                            <?php else: ?>

                                <?php foreach ($pending_blogs as $post): ?>

                                    <tr>

                                        <td class="text-center">

                                            <img src="../assets/images/<?php echo htmlspecialchars($post['thumbnail']); ?>"
                                                class="blog-image shadow-sm border">

                                        </td>

                                        <td>

                                            <strong>
                                                <?php echo htmlspecialchars($post['title']); ?>
                                            </strong>

                                            <div class="small text-muted mt-1">

                                                <?php echo htmlspecialchars(substr($post['description'], 0, 100)); ?>...

                                            </div>

                                        </td>

                                        <td class="text-center">

                                            <span class="badge bg-info text-dark">
                                                <?php echo htmlspecialchars($post['author_name']); ?>
                                            </span>

                                        </td>

                                        <td class="text-center">

                                            <span class="badge bg-secondary">
                                                <?php echo htmlspecialchars($post['category_name']); ?>
                                            </span>

                                        </td>

                                        <td class="text-center">

                                            <button class="btn btn-primary btn-sm action-btn"
                                                data-bs-toggle="modal"
                                                data-bs-target="#previewModal<?php echo $post['id']; ?>">

                                                <i class="fa-solid fa-eye me-1"></i>
                                                View

                                            </button>

                                        </td>

                                        <td>

                                            <div class="d-flex justify-content-center gap-2">

                                                <a href="pending-blogs.php?status=approved&id=<?php echo $post['id']; ?>"
                                                    class="btn btn-success btn-sm action-btn"
                                                    onclick="return confirm('Approve this article?')">

                                                    <i class="fa-solid fa-check me-1"></i>
                                                    Approve

                                                </a>

                                                <a href="pending-blogs.php?status=rejected&id=<?php echo $post['id']; ?>"
                                                    class="btn btn-danger btn-sm action-btn"
                                                    onclick="return confirm('Reject this article?')">

                                                    <i class="fa-solid fa-xmark me-1"></i>
                                                    Reject

                                                </a>

                                            </div>

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

    <?php foreach ($pending_blogs as $post): ?>

        <div class="modal fade"
            id="previewModal<?php echo $post['id']; ?>"
            tabindex="-1">

            <div class="modal-dialog modal-xl modal-dialog-scrollable">

                <div class="modal-content border-0 shadow">

                    <div class="modal-header bg-dark text-white">

                        <h5 class="modal-title">
                            <i class="fa-solid fa-file-lines text-warning me-2"></i>
                            Blog Preview
                        </h5>

                        <button type="button"
                            class="btn-close btn-close-white"
                            data-bs-dismiss="modal">
                        </button>

                    </div>

                    <div class="modal-body">

                        <h3 class="fw-bold mb-3">
                            <?php echo htmlspecialchars($post['title']); ?>
                        </h3>

                        <div class="mb-3">

                            <span class="badge bg-info text-dark me-2">
                                Author:
                                <?php echo htmlspecialchars($post['author_name']); ?>
                            </span>

                            <span class="badge bg-secondary">
                                <?php echo htmlspecialchars($post['category_name']); ?>
                            </span>

                        </div>

                        <div class="mb-4">

                            <img src="../assets/images/<?php echo htmlspecialchars($post['thumbnail']); ?>"
                                class="preview-image shadow">

                        </div>

                        <div class="card shadow-sm border-0">

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

                        <a href="pending-blogs.php?status=approved&id=<?php echo $post['id']; ?>"
                            class="btn btn-success">

                            <i class="fa-solid fa-check me-1"></i>
                            Approve

                        </a>

                        <a href="pending-blogs.php?status=rejected&id=<?php echo $post['id']; ?>"
                            class="btn btn-danger">

                            <i class="fa-solid fa-xmark me-1"></i>
                            Reject

                        </a>

                    </div>

                </div>

            </div>

        </div>

    <?php endforeach; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>
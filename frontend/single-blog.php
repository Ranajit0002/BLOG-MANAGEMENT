<?php
session_start();
require_once '../config/db.php';

$slug = isset($_GET['slug']) ? trim($_GET['slug']) : null;

if (!$slug) {
    header("Location: ../index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_comment'])) {
    if (!isset($_SESSION['user_id'])) {
        header("Location: ../login.php");
        exit();
    }

    $blog_id = (int)$_POST['blog_id'];
    $comment_text = trim($_POST['comment_text']);

    if (!empty($comment_text) && $blog_id > 0) {
        $stmt = $pdo->prepare("INSERT INTO comments (blog_id, user_id, comment_text) VALUES (?, ?, ?)");
        $stmt->execute([$blog_id, $_SESSION['user_id'], $comment_text]);
    }

    header("Location: single-blog.php?slug=" . urlencode($slug));
    exit();
}

$query = "SELECT b.*, u.name as author_name, c.category_name 
          FROM blogs b 
          LEFT JOIN users u ON b.user_id = u.id 
          LEFT JOIN categories c ON b.category_id = c.id 
          WHERE b.slug = ? AND b.status = 'approved'";

try {
    $stmt = $pdo->prepare($query);
    $stmt->execute([$slug]);
    $post = $stmt->fetch();
} catch (PDOException $e) {
    die("<div style='text-align:center; margin-top:100px; font-family:sans-serif;'><h2>Database Error</h2><p>" . htmlspecialchars($e->getMessage()) . "</p></div>");
}

if (!$post) {
    die("<div style='text-align:center; margin-top:100px; font-family:sans-serif;'>
    <h2>404: Article Not Found</h2>
    <p>This post may be unapproved or removed.</p>
    <a href='../index.php'>Return Home</a></div>");
}

$comments = [];
$is_logged_in = isset($_SESSION['user_id']);

if ($is_logged_in) {
    $comment_query = "SELECT c.*, u.name as commenter_name 
                      FROM comments c 
                      JOIN users u ON c.user_id = u.id 
                      WHERE c.blog_id = ? 
                      ORDER BY c.id DESC";
    $comment_stmt = $pdo->prepare($comment_query);
    $comment_stmt->execute([$post['id']]);
    $comments = $comment_stmt->fetchAll();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($post['title']); ?></title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">

    <style>
        body {
            background: #f8f9fa;
        }

        .hero-image {
            height: 450px;
            object-fit: cover;
        }

        .article-content {
            font-size: 18px;
            line-height: 1.9;
            white-space: pre-line;
        }

        .comment-card {
            transition: 0.3s;
        }

        .comment-card:hover {
            transform: translateY(-3px);
        }

        .author-avatar {
            width: 55px;
            height: 55px;
            font-size: 18px;
        }

        .locked-overlay {
            background: rgba(255, 255, 255, 0.95);
        }
    </style>
</head>

<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow sticky-top">
        <div class="container">

            <a class="navbar-brand fw-bold fs-3"
                href="../index.php">
                BLOG MANAGEMENT
            </a>

            <div>

                <a href="../index.php"
                    class="btn btn-outline-light me-2">
                    All Articles
                </a>

            </div>

        </div>
    </nav>

    <div class="container py-5">

        <!-- Category & Date -->
        <div class="mb-3">

            <span class="badge bg-primary px-3 py-2">
                <?php echo htmlspecialchars($post['category_name'] ?? 'Uncategorized'); ?>
            </span>

            <span class="text-muted ms-3">
                <i class="fa fa-calendar"></i>
                <?php echo date('F d, Y', strtotime($post['created_at'])); ?>
            </span>

        </div>

        <!-- Title -->
        <h1 class="display-4 fw-bold mb-4">
            <?php echo htmlspecialchars($post['title']); ?>
        </h1>

        <!-- Author -->
        <div class="d-flex align-items-center border-bottom pb-4 mb-4">

            <div class="author-avatar rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold me-3">
                <?php echo strtoupper(substr($post['author_name'] ?? 'AU', 0, 2)); ?>
            </div>

            <div>
                <h6 class="mb-0 fw-bold">
                    <?php echo htmlspecialchars($post['author_name']); ?>
                </h6>
                <small class="text-muted">
                    Verified Contributor
                </small>
            </div>

        </div>

        <!-- Image -->
        <div class="card shadow-sm border-0 mb-5 overflow-hidden">
            <img src="../assets/images/<?php echo htmlspecialchars($post['thumbnail']); ?>"
                class="card-img-top hero-image"
                alt="Blog Image">
        </div>

        <!-- Article -->
        <div class="card shadow-sm border-0 mb-5">
            <div class="card-body p-4 position-relative">

                <?php if ($is_logged_in): ?>

                    <div class="article-content">
                        <?php echo htmlspecialchars($post['description']); ?>
                    </div>

                <?php else: ?>

                    <div class="article-content text-muted">
                        <?php echo htmlspecialchars(substr($post['description'], 0, 250)); ?>...
                    </div>

                    <div class="text-center mt-5 p-4 border rounded bg-light">

                        <div class="mb-3">
                            <i class="fa-solid fa-lock fa-3x text-primary"></i>
                        </div>

                        <h3>Continue Reading</h3>

                        <p class="text-muted">
                            Login to read the complete article.
                        </p>

                        <a href="../login.php"
                            class="btn btn-primary me-2">
                            Login
                        </a>

                        <a href="../register.php"
                            class="btn btn-outline-primary">
                            Register
                        </a>

                    </div>

                <?php endif; ?>

            </div>
        </div>

        <!-- Back Button -->
        <?php if ($is_logged_in): ?>

            <div class="text-center mb-5">
                <a href="../index.php"
                    class="btn btn-primary btn-lg">
                    <i class="fa fa-arrow-left"></i>
                    Back to Articles
                </a>
            </div>

        <?php endif; ?>

        <!-- Comments -->
        <div class="card shadow-sm border-0">

            <div class="card-header bg-white">
                <h3 class="mb-0">
                    Comments
                </h3>
            </div>

            <div class="card-body">

                <?php if ($is_logged_in): ?>

                    <!-- Comment Form -->
                    <form method="POST" class="mb-5">

                        <input type="hidden"
                            name="blog_id"
                            value="<?php echo $post['id']; ?>">

                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                Add Comment
                            </label>

                            <textarea name="comment_text"
                                rows="4"
                                class="form-control" style="resize: none;"
                                placeholder="Write your comment..."
                                required></textarea>
                        </div>

                        <button type="submit"
                            name="add_comment"
                            class="btn btn-primary">
                            <i class="fa fa-paper-plane"></i>
                            Post Comment
                        </button>

                    </form>

                <?php else: ?>

                    <div class="alert alert-warning text-center">

                        <i class="fa fa-lock me-2"></i>

                        Login to view and post comments.

                        <div class="mt-3">
                            <a href="../login.php"
                                class="btn btn-dark">
                                Login
                            </a>
                        </div>

                    </div>

                <?php endif; ?>

                <!-- Comments List -->
                <?php if ($is_logged_in): ?>

                    <h5 class="mb-4">
                        <?php echo count($comments); ?>
                        Comments
                    </h5>

                    <?php if (empty($comments)): ?>

                        <div class="alert alert-secondary">
                            No comments yet.
                        </div>

                    <?php else: ?>

                        <?php foreach ($comments as $comment): ?>

                            <div class="card comment-card mb-3 shadow-sm">

                                <div class="card-body">

                                    <div class="d-flex justify-content-between mb-2">

                                        <strong>
                                            <?php echo htmlspecialchars($comment['commenter_name']); ?>
                                        </strong>

                                        <small class="text-muted">
                                            <?php echo date('M d, Y', strtotime($comment['created_at'])); ?>
                                        </small>

                                    </div>

                                    <p class="mb-0">
                                        <?php echo htmlspecialchars($comment['comment_text']); ?>
                                    </p>

                                </div>

                            </div>

                        <?php endforeach; ?>

                    <?php endif; ?>

                <?php endif; ?>

            </div>

        </div>

    </div>

    <!-- Footer -->
    <?php include '../includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>
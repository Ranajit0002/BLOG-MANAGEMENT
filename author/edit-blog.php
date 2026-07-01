<?php
require_once '../includes/auth.php';
checkAccess('author');
require_once '../config/db.php';

$error = '';
$success = '';
$user_id = $_SESSION['user_id'];
$blog_id = $_GET['id'] ?? null;

if (!$blog_id) {
    header("Location: my-blogs.php");
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM blogs WHERE id = ? AND user_id = ?");
$stmt->execute([$blog_id, $user_id]);
$post = $stmt->fetch();

if (!$post) {
    die("Unauthorized configuration or invalid post reference!");
}

$cat_stmt = $pdo->query("SELECT * FROM categories ORDER BY category_name ASC");
$categories = $cat_stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $category_id = $_POST['category_id'];
    $description = trim($_POST['description']);
    $thumbnail_name = $post['thumbnail'];

    if (empty($title) || empty($category_id) || empty($description)) {
        $error = "All textual entries must be completed.";
    } else {
        if (!empty($_FILES['thumbnail']['name'])) {
            $thumbnail = $_FILES['thumbnail'];
            $file_ext = strtolower(pathinfo($thumbnail['name'], PATHINFO_EXTENSION));
            $allowed_exts = ['jpg', 'jpeg', 'png', 'webp', 'avif'];

            if (in_array($file_ext, $allowed_exts) && $thumbnail['size'] <= 2 * 1024 * 1024) {
                if (file_exists("../uploads/blog-images/" . $post['thumbnail'])) {
                    unlink("../uploads/blog-images/" . $post['thumbnail']);
                }

                $thumbnail_name = uniqid('img_', true) . '.' . $file_ext;
                move_uploaded_file($thumbnail['tmp_name'], "../uploads/blog-images/" . $thumbnail_name);
            } else {
                $error = "File asset failure: Confirm correct picture configuration under 2MB.";
            }
        }

        if (empty($error)) {
            try {
                $update = $pdo->prepare("UPDATE blogs SET title = ?, category_id = ?, description = ?, thumbnail = ?, status = 'pending' WHERE id = ?");
                $update->execute([$title, $category_id, $description, $thumbnail_name, $blog_id]);

                $success = "Post has reverted to pending status for validation screening.";

                $post['title'] = $title;
                $post['category_id'] = $category_id;
                $post['description'] = $description;
                $post['thumbnail'] = $thumbnail_name;
            } catch (PDOException $e) {
                $error = "Database Modification Error: " . $e->getMessage();
            }
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
            padding: 35px;
            margin-bottom: 30px;
        }

        .edit-card {
            border: none;
            border-radius: 20px;
        }

        .form-control,
        .form-select {
            border-radius: 12px;
            padding: 12px;
        }

        textarea {
            resize: none;
        }

        .image-preview {
            max-width: 220px;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.15);
        }

        .upload-box {
            border: 2px dashed #ced4da;
            border-radius: 15px;
            padding: 25px;
            background: #fafafa;
            text-align: center;
        }

        .upload-box:hover {
            border-color: #0d6efd;
        }

        .form-label {
            font-weight: 600;
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
                        <i class="fa-solid fa-pen-to-square me-2"></i>
                        Edit Blog Post
                    </h2>

                    <p class="mb-0">
                        Update your article details and submit it again for admin review.
                    </p>

                </div>

                <div class="col-md-4 text-md-end mt-3 mt-md-0">

                    <a href="my-blogs.php"
                        class="btn btn-light">
                        <i class="fa-solid fa-arrow-left me-2"></i>
                        Back to My Blogs
                    </a>

                </div>

            </div>

        </div>

        <?php if (!empty($error)): ?>

            <div class="alert alert-danger shadow-sm">
                <i class="fa-solid fa-circle-exclamation me-2"></i>
                <?php echo htmlspecialchars($error); ?>
            </div>

        <?php endif; ?>

        <?php if (!empty($success)): ?>

            <div class="alert alert-success shadow-sm">
                <i class="fa-solid fa-circle-check me-2"></i>
                <?php echo htmlspecialchars($success); ?>
            </div>

        <?php endif; ?>

        <div class="card edit-card shadow-lg">

            <div class="card-header bg-white py-3">

                <h4 class="mb-0 fw-bold">
                    Blog Information
                </h4>

            </div>

            <div class="card-body p-4">

                <form action="edit-blog.php?id=<?php echo $blog_id; ?>"
                    method="POST"
                    enctype="multipart/form-data">

                    <div class="mb-4">

                        <label class="form-label">
                            Blog Title
                        </label>

                        <input type="text"
                            name="title"
                            class="form-control form-control-lg"
                            value="<?php echo htmlspecialchars($post['title']); ?>"
                            required>

                    </div>

                    <div class="mb-4">

                        <label class="form-label">
                            Category
                        </label>

                        <select name="category_id"
                            class="form-select"
                            required>

                            <?php foreach ($categories as $cat): ?>

                                <option value="<?php echo $cat['id']; ?>"
                                    <?php echo ($cat['id'] == $post['category_id']) ? 'selected' : ''; ?>>

                                    <?php echo htmlspecialchars($cat['category_name']); ?>

                                </option>

                            <?php endforeach; ?>

                        </select>

                    </div>

                    <div class="mb-4">

                        <label class="form-label">
                            Current Thumbnail
                        </label>

                        <div class="text-center mb-3">

                            <img src="../assets/images/<?php echo htmlspecialchars($post['thumbnail']); ?>"
                                class="img-fluid image-preview">

                        </div>

                    </div>

                    <div class="mb-4">

                        <label class="form-label">
                            Replace Thumbnail (Optional)
                        </label>

                        <div class="upload-box">

                            <i class="fa-solid fa-image fa-3x text-primary mb-3"></i>

                            <p class="text-muted mb-3">
                                Upload JPG, JPEG, PNG, WEBP or AVIF images (Maximum 2MB)
                            </p>

                            <input type="file"
                                name="thumbnail"
                                class="form-control"
                                accept="image/*">

                        </div>

                    </div>

                    <div class="mb-4">

                        <label class="form-label">
                            Blog Content
                        </label>

                        <textarea name="description"
                            rows="10"
                            class="form-control"
                            required><?php echo htmlspecialchars($post['description']); ?></textarea>

                    </div>

                    <div class="alert alert-warning">

                        <i class="fa-solid fa-triangle-exclamation me-2"></i>

                        After updating, your blog will return to
                        <strong>Pending Review</strong>
                        status and must be approved by an administrator.

                    </div>

                    <div class="d-flex flex-column flex-md-row gap-3">

                        <button type="submit"
                            class="btn btn-primary btn-lg flex-fill">

                            <i class="fa-solid fa-floppy-disk me-2"></i>
                            Save Changes

                        </button>

                        <a href="my-blogs.php"
                            class="btn btn-outline-secondary btn-lg flex-fill">

                            <i class="fa-solid fa-list me-2"></i>
                            My Blogs

                        </a>

                    </div>

                </form>

            </div>

        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>
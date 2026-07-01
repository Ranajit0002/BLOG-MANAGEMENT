<?php
require_once '../includes/auth.php';
checkAccess('author');
require_once '../config/db.php';

$error = '';
$success = '';

$cat_stmt = $pdo->query("SELECT * FROM categories ORDER BY category_name ASC");
$categories = $cat_stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $category_id = $_POST['category_id'];
    $description = trim($_POST['description']);
    $user_id = $_SESSION['user_id'];

    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title))) . '-' . time();

    $thumbnail = $_FILES['thumbnail'];

    $target_dir = dirname(__DIR__) . "/assets/images/";

    if (empty($title) || empty($category_id) || empty($description) || empty($thumbnail['name'])) {
        $error = "All form details and a thumbnail image are required!";
    } elseif ($thumbnail['error'] !== UPLOAD_ERR_OK) {
        switch ($thumbnail['error']) {
            case UPLOAD_ERR_INI_SIZE:
                $error = "The uploaded file exceeds the 'upload_max_filesize' directive in your server's php.ini config.";
                break;
            case UPLOAD_ERR_PARTIAL:
                $error = "The file was only partially uploaded. Please try again.";
                break;
            case UPLOAD_ERR_NO_FILE:
                $error = "No file was uploaded.";
                break;
            case UPLOAD_ERR_NO_TMP_DIR:
                $error = "Missing a temporary folder on the server to process uploads.";
                break;
            default:
                $error = "System upload error code: " . $thumbnail['error'];
                break;
        }
    } else {
        $file_ext = strtolower(pathinfo($thumbnail['name'], PATHINFO_EXTENSION));
        $allowed_exts = ['jpg', 'jpeg', 'png', 'webp', 'avif'];

        if (!in_array($file_ext, $allowed_exts)) {
            $error = "Invalid file type! Allowed types: JPG, JPEG, PNG, WEBP, AVIF.";
        } elseif ($thumbnail['size'] > 2 * 1024 * 1024) {
            $error = "Image size too large! Maximum limit is 2MB.";
        } else {
            $new_file_name = uniqid('img_', true) . '.' . $file_ext;
            $target_file = $target_dir . $new_file_name;

            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0755, true);
            }

            if (move_uploaded_file($thumbnail['tmp_name'], $target_file)) {
                try {
                    $stmt = $pdo->prepare("INSERT INTO blogs (user_id, category_id, title, slug, thumbnail, description, status) VALUES (?, ?, ?, ?, ?, ?, 'pending')");
                    $stmt->execute([$user_id, $category_id, $title, $slug, $new_file_name, $description]);

                    $success = "Blog article submitted successfully! It is now pending administrator approval.";

                    $title = $category_id = $description = '';
                } catch (PDOException $e) {
                    $error = "Database Error: Failed to save post. " . $e->getMessage();
                }
            } else {
                $error = "Unable to save your uploaded image to the system directory. Path details: " . htmlspecialchars($target_dir);
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

        .hero-banner {
            background: linear-gradient(135deg, #198754, #20c997);
            color: white;
            border-radius: 20px;
            padding: 35px;
            margin-bottom: 30px;
        }

        .blog-card {
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

        .upload-box {
            border: 2px dashed #ced4da;
            border-radius: 15px;
            padding: 25px;
            text-align: center;
            background: #fafafa;
        }

        .upload-box:hover {
            border-color: #198754;
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
                        <a class="nav-link active"
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

        <div class="hero-banner shadow">

            <div class="row align-items-center">

                <div class="col-md-8">

                    <h2 class="fw-bold mb-2">
                        <i class="fa-solid fa-pen-to-square me-2"></i>
                        Create New Blog
                    </h2>

                    <p class="mb-0">
                        Write your article and submit it for administrator review.
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

        <div class="card blog-card shadow-lg">

            <div class="card-header bg-white py-3">

                <h4 class="mb-0 fw-bold">
                    Article Details
                </h4>

            </div>

            <div class="card-body p-4">

                <form action="create-blog.php"
                    method="POST"
                    enctype="multipart/form-data">

                    <div class="mb-4">

                        <label class="form-label">
                            Blog Title
                        </label>

                        <input type="text"
                            name="title"
                            class="form-control form-control-lg"
                            placeholder="Enter blog title"
                            required
                            value="<?php echo isset($title) ? htmlspecialchars($title) : ''; ?>">

                    </div>

                    <div class="mb-4">

                        <label class="form-label">
                            Category
                        </label>

                        <select name="category_id"
                            class="form-select"
                            required>

                            <option value="">
                                Select Category
                            </option>

                            <?php foreach ($categories as $cat): ?>

                                <option value="<?php echo $cat['id']; ?>"
                                    <?php echo (isset($category_id) && $category_id == $cat['id']) ? 'selected' : ''; ?>>

                                    <?php echo htmlspecialchars($cat['category_name']); ?>

                                </option>

                            <?php endforeach; ?>

                        </select>

                    </div>

                    <div class="mb-4">

                        <label class="form-label">
                            Thumbnail Image
                        </label>

                        <div class="upload-box">

                            <i class="fa-solid fa-image fa-3x text-success mb-3"></i>

                            <p class="text-muted mb-3">
                                Upload JPG, PNG, JPEG, WEBP or AVIF images (Max: 2MB)
                            </p>

                            <input type="file"
                                name="thumbnail"
                                class="form-control"
                                accept="image/*"
                                required>

                        </div>

                    </div>

                    <div class="mb-4">

                        <label class="form-label">
                            Blog Content
                        </label>

                        <textarea name="description"
                            rows="12"
                            class="form-control"
                            placeholder="Write your article here..."
                            required><?php echo isset($description) ? htmlspecialchars($description) : ''; ?></textarea>

                    </div>

                    <div class="d-flex flex-column flex-md-row gap-3">

                        <button type="submit"
                            class="btn btn-success btn-lg flex-fill">

                            <i class="fa-solid fa-paper-plane me-2"></i>
                            Submit for Review

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

        <div class="card shadow mt-4 border-0">

            <div class="card-body">

                <h5 class="mb-3">
                    <i class="fa-solid fa-lightbulb text-warning me-2"></i>
                    Writing Tips
                </h5>

                <ul class="mb-0 text-muted">
                    <li>Use a clear and descriptive title.</li>
                    <li>Select the correct category.</li>
                    <li>Upload a high-quality thumbnail image.</li>
                    <li>Break long content into paragraphs.</li>
                    <li>Review your content before submission.</li>
                </ul>

            </div>

        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>
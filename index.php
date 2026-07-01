<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'config/db.php';

$cat_stmt = $pdo->query("SELECT * FROM categories ORDER BY category_name ASC");
$categories = $cat_stmt->fetchAll();

$selected_category = $_GET['category'] ?? null;
$search_query = trim($_GET['search'] ?? '');

$conditions = ["b.status = 'approved'"];
$params = [];

$current_category_name = '';
if (!empty($selected_category)) {
    $conditions[] = "c.slug = ?";
    $params[] = $selected_category;
}

if ($search_query !== '') {
    $conditions[] = "(b.title LIKE ? OR b.description LIKE ? OR u.name LIKE ? OR c.category_name LIKE ?)";
    $wildcard = '%' . $search_query . '%';

    $params[] = $wildcard; // b.title
    $params[] = $wildcard; // b.description
    $params[] = $wildcard; // u.name
    $params[] = $wildcard; // c.category_name
}

$query = "SELECT b.*, u.name as author_name, c.category_name, c.slug as cat_slug 
          FROM blogs b 
          JOIN users u ON b.user_id = u.id 
          JOIN categories c ON b.category_id = c.id 
          WHERE " . implode(" AND ", $conditions) . " 
          ORDER BY b.id DESC";

$blogs = [];
try {
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $blogs = $stmt->fetchAll();
} catch (PDOException $e) {
    echo "<div style='background: #fee2e2; color: #991b1b; padding: 20px; margin: 20px; border-radius: 8px; font-family: monospace;'>";
    echo "<strong>Database Query Error:</strong> " . htmlspecialchars($e->getMessage()) . "<br><br>";
    echo "<strong>Generated SQL:</strong> " . htmlspecialchars($query);
    echo "</div>";
    exit;
}

if (!empty($selected_category)) {
    foreach ($categories as $cat) {
        if ($cat['slug'] === $selected_category) {
            $current_category_name = $cat['category_name'];
            break;
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
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">

    <style>
        body {
            background: #f8f9fa;
        }

        .hero-section {
            background: linear-gradient(90deg, #f56a6a, #2752a8, #3a5da3, #0f8989);
            color: white;
            padding: 100px 0;
        }

        .blog-card {
            transition: 0.3s;
            border: none;
        }

        .blog-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }

        .blog-img {
            height: 220px;
            object-fit: cover;
        }

        .category-badge {
            position: absolute;
            top: 15px;
            left: 15px;
        }

        .search-box {
            margin-top: -35px;
            position: relative;
            z-index: 10;
        }
    </style>
</head>

<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top shadow">
        <div class="container">
            <a class="navbar-brand fw-bold fs-3" href="index.php">
                BLOG-MANAGEMENT
            </a>

            <button class="navbar-toggler" data-bs-toggle="collapse"
                data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse justify-content-end"
                id="navbarNav">

                <?php if (isset($_SESSION['user_id'])): ?>

                    <a href="<?php echo ($_SESSION['user_role'] === 'admin')
                                    ? 'admin/dashboard.php'
                                    : 'author/author-dashboard.php'; ?>"
                        class="btn btn-primary">
                        Dashboard
                    </a>

                <?php else: ?>

                    <a href="login.php"
                        class="btn btn-outline-light me-2">
                        Login
                    </a>

                    <a href="register.php"
                        class="btn btn-warning">
                        Register
                    </a>

                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- Hero -->
    <section class="hero-section text-center">
        <div class="container">
            <h2 class="display-4 fw-extrabold text-capitalize mt-4">
                Perspectives from independent writers.
            </h2>

            <p class="lead mt-3">
                Explore tutorials, operational reviews, and technical articles.
            </p>
        </div>
    </section>

    <!-- Search -->
    <div class="container search-box">
        <div class="card shadow border-0 rounded-4">
            <div class="card-body">

                <form method="GET" action="index.php">

                    <?php if (!empty($selected_category)): ?>
                        <input type="hidden"
                            name="category"
                            value="<?php echo htmlspecialchars($selected_category); ?>">
                    <?php endif; ?>

                    <div class="input-group">

                        <span class="input-group-text bg-white">
                            <i class="fa fa-search"></i>
                        </span>

                        <input type="text"
                            name="search"
                            class="form-control"
                            placeholder="Search articles, authors, topics..."
                            value="<?php echo htmlspecialchars($search_query); ?>">

                        <button class="btn btn-primary">
                            Search
                        </button>

                    </div>
                </form>

            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="container py-5">

        <?php if ($search_query !== '' || !empty($selected_category)): ?>
            <div class="alert alert-info text-center">
                Filters are currently active.
                <a href="index.php" class="fw-bold">
                    Clear Filters
                </a>
            </div>
        <?php endif; ?>

        <!-- Categories -->
        <div class="text-center mb-5">

            <a href="index.php"
                class="btn <?php echo !$selected_category ? 'btn-primary' : 'btn-outline-primary'; ?> rounded-pill mb-2">
                All Topics
            </a>

            <?php foreach ($categories as $cat): ?>

                <?php
                $cat_url = "index.php?category=" . urlencode($cat['slug']);

                if ($search_query !== '') {
                    $cat_url .= "&search=" . urlencode($search_query);
                }
                ?>

                <a href="<?php echo $cat_url; ?>"
                    class="btn <?php echo ($selected_category === $cat['slug'])
                                    ? 'btn-primary'
                                    : 'btn-outline-secondary'; ?> rounded-pill mb-2">
                    <?php echo htmlspecialchars($cat['category_name']); ?>
                </a>

            <?php endforeach; ?>

        </div>

        <!-- Blog Grid -->
        <?php if (empty($blogs)): ?>

            <div class="card shadow-sm border-0 p-5 text-center">
                <i class="fa fa-folder-open fa-4x text-secondary mb-3"></i>
                <h4>No Articles Found</h4>
                <p class="text-muted">
                    No published articles match your search.
                </p>

                <a href="index.php"
                    class="btn btn-primary">
                    Clear Filters
                </a>
            </div>

        <?php else: ?>

            <div class="row g-4">

                <?php foreach ($blogs as $post): ?>

                    <div class="col-md-6 col-lg-4">

                        <div class="card h-100 shadow-sm blog-card">

                            <div class="position-relative">

                                <img src="assets/images/<?php echo htmlspecialchars($post['thumbnail']); ?>"
                                    class="card-img-top blog-img"
                                    alt="Blog Image">

                                <span class="badge bg-primary category-badge">
                                    <?php echo htmlspecialchars($post['category_name']); ?>
                                </span>

                            </div>

                            <div class="card-body d-flex flex-column">

                                <h5 class="card-title fw-bold">
                                    <?php echo htmlspecialchars($post['title']); ?>
                                </h5>

                                <p class="card-text text-muted">
                                    <?php echo htmlspecialchars(substr($post['description'], 0, 140)); ?>...
                                </p>

                                <div class="mt-auto pt-3 border-top">

                                    <small class="text-muted">
                                        By
                                        <strong>
                                            <?php echo htmlspecialchars($post['author_name']); ?>
                                        </strong>
                                    </small>

                                    <br>

                                    <small class="text-muted">
                                        <?php echo date('M d, Y', strtotime($post['created_at'])); ?>
                                    </small>

                                </div>

                            </div>

                            <div class="card-footer bg-white border-0">

                                <a href="frontend/single-blog.php?slug=<?php echo $post['slug']; ?>"
                                    class="btn btn-outline-primary w-100">
                                    Read More
                                </a>

                            </div>

                        </div>

                    </div>

                <?php endforeach; ?>

            </div>

        <?php endif; ?>

    </div>

    <!-- Footer -->
   <?php include './includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>
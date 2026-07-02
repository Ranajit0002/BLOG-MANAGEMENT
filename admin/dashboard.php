<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

require_once '../config/db.php';

$pending_count = 0;
$approved_count = 0;
$category_count = 0;
$author_count = 0;
$success_msg = '';

if (isset($_GET['action']) && $_GET['action'] === 'delete_author' && isset($_GET['id'])) {
    $author_id = (int)$_GET['id'];

    try {
        $del = $pdo->prepare("DELETE FROM users WHERE id = ? AND role = 'author'");
        if ($del->execute([$author_id])) {
            $success_msg = "Author profile permanently removed from the system registry.";
        }
    } catch (PDOException $e) {
        $success_msg = "Could not delete author. Ensure their blogs are purged or reassigned first.";
    }
}

try {
    $stmt = $pdo->query("SELECT COUNT(*) FROM blogs WHERE status = 'pending'");
    $pending_count = $stmt->fetchColumn();
} catch (PDOException $e) {
}

try {
    $stmt = $pdo->query("SELECT COUNT(*) FROM blogs WHERE status = 'approved'");
    $approved_count = $stmt->fetchColumn();
} catch (PDOException $e) {
}

try {
    $stmt = $pdo->query("SELECT COUNT(*) FROM categories");
    $category_count = $stmt->fetchColumn();
} catch (PDOException $e) {
}

try {
    $stmt = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'author'");
    $author_count = $stmt->fetchColumn();
} catch (PDOException $e) {
}

$search_query = trim($_GET['search'] ?? '');

$authors = [];
try {
    if ($search_query !== '') {
        $query = "SELECT u.id, u.name, u.email, u.created_at, COUNT(b.id) as total_posts 
                  FROM users u LEFT JOIN blogs b ON u.id = b.user_id 
                  WHERE u.role = 'author' AND (u.name LIKE ? OR u.email LIKE ? OR u.id LIKE ?)
                  GROUP BY u.id 
                  ORDER BY u.id ASC";
        $stmt = $pdo->prepare($query);
        $wildcard = '%' . $search_query . '%';
        $stmt->execute([$wildcard, $wildcard, $wildcard]);
    } else {
        $query = "SELECT u.id, u.name, u.email, u.created_at, COUNT(b.id) as total_posts 
                  FROM users u 
                  LEFT JOIN blogs b ON u.id = b.user_id 
                  WHERE u.role = 'author' 
                  GROUP BY u.id 
                  ORDER BY u.id ASC";
        $stmt = $pdo->query($query);
    }
    $authors = $stmt->fetchAll();
} catch (PDOException $e) {
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
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <style>
        body {
            background: #e9e9e9;
            overflow-x: hidden;
        }

        .sidebar {
            min-height: 100vh;
            background: #212529;
            box-shadow: 0 0 20px rgba(0, 0, 0, .15);
        }

        .sidebar .nav-link {
            color: rgba(255, 255, 255, .75);
            padding: 12px 16px;
            border-radius: 10px;
            transition: .3s;
        }

        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background: #0d6efd;
            color: white;
        }

        .dashboard-header {
            background: white;
            border-radius: 20px;
            padding: 25px;
            box-shadow: 0 0 15px rgba(0, 0, 0, .08);
        }

        .stat-card {
            border: none;
            border-radius: 20px;
            color: white;
            transition: .3s;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .pending {
            background: linear-gradient(45deg, #ff9800, #ffc107);
        }

        .approved {
            background: linear-gradient(45deg, #198754, #20c997);
        }

        .category {
            background: linear-gradient(45deg, #0d6efd, #6610f2);
        }

        .author {
            background: linear-gradient(45deg, #0dcaf0, #17a2b8);
        }

        .author-avatar {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            background: #0d6efd;
            color: white;
            font-size: 28px;
        }

        .table tbody tr:hover {
            background: #f8f9fa;
        }

        .card,
        .modal-content {
            border: none;
            border-radius: 20px;
        }

        @media (max-width: 768px) {
            .sidebar {
                min-height: auto;
            }
        }
    </style>
</head>

<body>

    <div class="container-fluid">
        <div class="row">

            <nav class="col-lg-2 col-md-3 sidebar p-4">

                <h3 class="text-center text-white mb-4">
                    <i class="fa-solid fa-shield-halved me-2"></i>
                    Admin Panel
                </h3>

                <ul class="nav flex-column gap-2">

                    <li class="nav-item">
                        <a class="nav-link active" href="dashboard.php">
                            <i class="fa-solid fa-gauge me-2"></i>
                            Dashboard
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="categories.php">
                            <i class="fa-solid fa-list me-2"></i>
                            Categories
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="pending-blogs.php">
                            <i class="fa-solid fa-clock me-2"></i>
                            Pending Blogs
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="approved-blogs.php">
                            <i class="fa-solid fa-circle-check me-2"></i>
                            Approved Blogs
                        </a>
                    </li>

                    <hr class="text-secondary">

                    <li class="nav-item">
                        <a href="../index.php" class="nav-link">
                            <i class="fa-solid fa-house me-2"></i>
                            Home
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="../logout.php" class="nav-link text-danger">
                            <i class="fa-solid fa-right-from-bracket me-2"></i>
                            Logout
                        </a>
                    </li>

                </ul>

            </nav>

            <main class="col-lg-10 col-md-9 py-4 px-4">

                <div class="dashboard-header mb-4">

                    <div class="d-flex justify-content-between align-items-center flex-wrap">

                        <div>
                            <h2 class="fw-bold mb-1">
                                Welcome,
                                <?php echo htmlspecialchars($_SESSION['user_name']); ?>
                            </h2>

                            <p class="text-muted mb-0">
                                Blog Management Administration Dashboard
                            </p>
                        </div>

                        <a href="../register.php" class="btn btn-warning">
                            <i class="fa-solid fa-user-plus me-1"></i>
                            Add Author
                        </a>

                    </div>

                </div>

                <?php if (!empty($success_msg)): ?>
                    <div class="alert alert-success alert-dismissible fade show shadow-sm">
                        <i class="fa-solid fa-circle-check me-2"></i>
                        <?php echo htmlspecialchars($success_msg); ?>
                        <button class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <div class="row g-4 mb-4">

                    <div class="col-md-6 col-xl-3">
                        <div class="card stat-card pending shadow">
                            <div class="card-body">
                                <h6>Pending Blogs</h6>
                                <h1><?php echo $pending_count; ?></h1>
                                <i class="fa-solid fa-clock fa-2x"></i>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 col-xl-3">
                        <div class="card stat-card approved shadow">
                            <div class="card-body">
                                <h6>Approved Blogs</h6>
                                <h1><?php echo $approved_count; ?></h1>
                                <i class="fa-solid fa-check fa-2x"></i>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 col-xl-3">
                        <div class="card stat-card category shadow">
                            <div class="card-body">
                                <h6>Categories</h6>
                                <h1><?php echo $category_count; ?></h1>
                                <i class="fa-solid fa-layer-group fa-2x"></i>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 col-xl-3">
                        <div class="card stat-card author shadow">
                            <div class="card-body">
                                <h6>Authors</h6>
                                <h1><?php echo $author_count; ?></h1>
                                <i class="fa-solid fa-users fa-2x"></i>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="card shadow">

                    <div class="card-header bg-dark text-white py-3">

                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">

                            <h4 class="mb-0">
                                <i class="fa-solid fa-users me-2"></i>
                                Author Registry
                            </h4>

                            <form action="dashboard.php" method="GET" class="d-flex">

                                <input type="text"
                                    name="search"
                                    class="form-control me-2"
                                    placeholder="Search author..."
                                    value="<?php echo htmlspecialchars($search_query); ?>">

                                <button class="btn btn-info">
                                    Search
                                </button>

                            </form>

                        </div>

                    </div>

                    <div class="card-body p-0">

                        <div class="table-responsive">

                            <table class="table table-hover align-middle mb-0">

                                <thead class="table-light">
                                    <tr class="text-center">
                                        <th>ID</th>
                                        <th class="text-start">Name</th>
                                        <th class="text-start">Email</th>
                                        <th>Posts</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>

                                <tbody>

                                    <?php if (empty($authors)): ?>

                                        <tr>
                                            <td colspan="5" class="text-center py-5 text-muted">
                                                No authors found.
                                            </td>
                                        </tr>

                                    <?php else: ?>

                                        <?php foreach ($authors as $user): ?>

                                            <tr class="text-center">

                                                <td><?php echo $user['id']; ?></td>

                                                <td class="text-start fw-semibold">
                                                    <?php echo htmlspecialchars($user['name']); ?>
                                                </td>

                                                <td class="text-start">
                                                    <?php echo htmlspecialchars($user['email']); ?>
                                                </td>

                                                <td>
                                                    <span class="badge bg-primary">
                                                        <?php echo $user['total_posts']; ?>
                                                    </span>
                                                </td>

                                                <td>

                                                    <button class="btn btn-sm btn-outline-primary"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#authorModal<?php echo $user['id']; ?>">
                                                        View
                                                    </button>

                                                    <a href="dashboard.php?action=delete_author&id=<?php echo $user['id']; ?>"
                                                        class="btn btn-sm btn-outline-danger"
                                                        onclick="return confirm('Delete this author?')">
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

            </main>

        </div>
    </div>

    <?php foreach ($authors as $user): ?>

        <div class="modal fade"
            id="authorModal<?php echo $user['id']; ?>"
            tabindex="-1">

            <div class="modal-dialog modal-dialog-centered">

                <div class="modal-content">

                    <div class="modal-header bg-primary text-white">

                        <h5 class="modal-title">
                            Author Details
                        </h5>

                        <button class="btn-close btn-close-white"
                            data-bs-dismiss="modal">
                        </button>

                    </div>

                    <div class="modal-body text-center">

                        <div class="author-avatar d-inline-flex
                    justify-content-center
                    align-items-center mb-3">

                            <i class="fa-solid fa-user"></i>

                        </div>

                        <h4>
                            <?php echo htmlspecialchars($user['name']); ?>
                        </h4>

                        <span class="badge bg-secondary mb-3">
                            Author
                        </span>

                        <hr>

                        <p>
                            <strong>Email:</strong><br>
                            <?php echo htmlspecialchars($user['email']); ?>
                        </p>

                        <p>
                            <strong>Joined:</strong><br>
                            <?php echo date(
                                'd M Y h:i A',
                                strtotime($user['created_at'])
                            ); ?>
                        </p>

                        <p>
                            <strong>Total Posts:</strong>
                            <span class="badge bg-primary">
                                <?php echo $user['total_posts']; ?>
                            </span>
                        </p>

                    </div>

                </div>

            </div>

        </div>

    <?php endforeach; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>
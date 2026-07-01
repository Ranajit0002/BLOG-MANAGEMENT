<?php
require_once '../includes/auth.php';
checkAccess('admin');

require_once '../config/db.php';

$error = '';
$success = '';

$edit_mode = false;
$edit_id = '';
$edit_name = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['action']) && $_POST['action'] === 'update') {
        $edit_id = $_POST['category_id'];
        $category_name = trim($_POST['category_name']);
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $category_name)));

        if (empty($category_name)) {
            $error = "Category name cannot be empty!";
        } else {
            try {
                $stmt = $pdo->prepare("UPDATE categories SET category_name = ?, slug = ? WHERE id = ?");
                $stmt->execute([$category_name, $slug, $edit_id]);
                $success = "Category updated successfully!";
            } catch (PDOException $e) {
                $error = "Error updating category (it may already exist).";
            }
        }
    } else {
        $category_name = trim($_POST['category_name']);
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $category_name)));

        if (empty($category_name)) {
            $error = "Category name cannot be empty!";
        } else {
            try {
                $stmt = $pdo->prepare("INSERT INTO categories (category_name, slug) VALUES (?, ?)");
                $stmt->execute([$category_name, $slug]);
                $success = "New category added!";
            } catch (PDOException $e) {
                $error = "Category already exists!";
            }
        }
    }
}

if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    try {
        $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
        $stmt->execute([$delete_id]);
        $success = "Category deleted successfully!";
    } catch (PDOException $e) {
        $error = "Cannot delete category! It contains active blogs.";
    }
}

if (isset($_GET['edit'])) {
    $edit_mode = true;
    $edit_id = $_GET['edit'];

    $stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
    $stmt->execute([$edit_id]);
    $cat = $stmt->fetch();
    if ($cat) {
        $edit_name = $cat['category_name'];
    }
}

$search_query = trim($_GET['search'] ?? '');

if ($search_query !== '') {
    $stmt = $pdo->prepare("SELECT * FROM categories WHERE category_name LIKE ? OR slug LIKE ? ORDER BY id DESC");
    $wildcard = '%' . $search_query . '%';
    $stmt->execute([$wildcard, $wildcard]);
} else {
    $stmt = $pdo->query("SELECT * FROM categories ORDER BY id DESC");
}
$categories = $stmt->fetchAll();
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

        .dashboard-header {
            background: linear-gradient(135deg, #0d6efd, #6610f2);
            color: white;
            border-radius: 20px;
            padding: 30px;
            margin-bottom: 25px;
        }

        .card {
            border: none;
            border-radius: 18px;
        }

        .table tbody tr:hover {
            background: #f8f9fa;
        }

        .search-box {
            max-width: 320px;
        }

        .table td,
        .table th {
            vertical-align: middle;
        }
    </style>
</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow sticky-top">
        <div class="container-fluid">

            <a href="dashboard.php" class="navbar-brand fw-bold">
                <i class="fa-solid fa-user-shield me-2"></i>
                Admin Panel
            </a>

            <button class="navbar-toggler"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#navbarMenu">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarMenu">

                <ul class="navbar-nav me-auto">

                    <li class="nav-item">
                        <a class="nav-link active"
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
                            <?php echo htmlspecialchars($_SESSION['user_name']); ?>
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

        <div class="dashboard-header shadow">

            <div class="row align-items-center">

                <div class="col-md-8">
                    <h2 class="fw-bold mb-2">
                        <i class="fa-solid fa-folder-tree me-2"></i>
                        Category Management
                    </h2>

                    <p class="mb-0">
                        Create, update, search, and manage blog categories.
                    </p>
                </div>

                <div class="col-md-4 text-md-end mt-3 mt-md-0">

                    <span class="badge bg-light text-dark fs-6 p-3">
                        Total:
                        <?php echo count($categories); ?>
                    </span>

                </div>

            </div>

        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger shadow-sm">
                <i class="fa-solid fa-circle-exclamation me-2"></i>
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <div class="alert alert-success shadow-sm">
                <i class="fa-solid fa-circle-check me-2"></i>
                <?php echo $success; ?>
            </div>
        <?php endif; ?>

        <div class="row">

            <div class="col-lg-4 mb-4">

                <div class="card shadow-lg">

                    <div class="card-header py-3 <?php echo $edit_mode ? 'bg-warning text-dark' : 'bg-primary text-white'; ?>">

                        <h5 class="mb-0">

                            <i class="fa-solid fa-pen-to-square me-2"></i>

                            <?php echo $edit_mode ? 'Edit Category' : 'Add Category'; ?>

                        </h5>

                    </div>

                    <div class="card-body">

                        <form action="categories.php" method="POST">

                            <?php if ($edit_mode): ?>
                                <input type="hidden"
                                    name="action"
                                    value="update">

                                <input type="hidden"
                                    name="category_id"
                                    value="<?php echo $edit_id; ?>">
                            <?php endif; ?>

                            <div class="mb-3">

                                <label class="form-label fw-semibold">
                                    Category Name
                                </label>

                                <input type="text"
                                    name="category_name"
                                    class="form-control form-control-lg"
                                    required
                                    value="<?php echo htmlspecialchars($edit_name); ?>">

                            </div>

                            <button type="submit"
                                class="btn <?php echo $edit_mode ? 'btn-warning' : 'btn-primary'; ?> w-100">

                                <?php echo $edit_mode ? 'Update Category' : 'Save Category'; ?>

                            </button>

                            <?php if ($edit_mode): ?>

                                <a href="categories.php"
                                    class="btn btn-secondary w-100 mt-2">
                                    Cancel
                                </a>

                            <?php endif; ?>

                        </form>

                    </div>

                </div>

            </div>

            <div class="col-lg-8">

                <div class="card shadow-lg">

                    <div class="card-header bg-white py-3">

                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">

                            <h5 class="mb-0 fw-bold">
                                Existing Categories
                            </h5>

                            <form action="categories.php"
                                method="GET"
                                class="search-box w-100">

                                <div class="input-group">

                                    <input type="text"
                                        name="search"
                                        value="<?php echo htmlspecialchars($search_query); ?>"
                                        class="form-control"
                                        placeholder="Search category...">

                                    <?php if ($search_query !== ''): ?>

                                        <a href="categories.php"
                                            class="btn btn-secondary">
                                            <i class="fa-solid fa-xmark"></i>
                                        </a>

                                    <?php endif; ?>

                                    <button class="btn btn-primary">
                                        Search
                                    </button>

                                </div>

                            </form>

                        </div>

                    </div>

                    <?php if ($search_query !== ''): ?>

                        <div class="alert alert-info rounded-0 mb-0 border-0">

                            Searching:
                            <strong>
                                <?php echo htmlspecialchars($search_query); ?>
                            </strong>

                            <a href="categories.php"
                                class="float-end text-decoration-none fw-bold">
                                Reset
                            </a>

                        </div>

                    <?php endif; ?>

                    <div class="table-responsive">

                        <table class="table table-hover align-middle mb-0">

                            <thead class="table-dark">

                                <tr>
                                    <th>#</th>
                                    <th>Category</th>
                                    <th>Slug</th>
                                    <th class="text-end">Actions</th>
                                </tr>

                            </thead>

                            <tbody>

                                <?php if (empty($categories)): ?>

                                    <tr>

                                        <td colspan="4"
                                            class="text-center py-5 text-muted">

                                            <i class="fa-solid fa-folder-open fa-3x d-block mb-3"></i>

                                            No categories found.

                                        </td>

                                    </tr>

                                <?php else: ?>

                                    <?php
                                    $sno = 1;

                                    foreach ($categories as $category):
                                    ?>

                                        <tr>

                                            <td>
                                                <?php echo $sno++; ?>
                                            </td>

                                            <td>

                                                <div class="d-flex align-items-center gap-3">

                                                    <strong>
                                                        <?php echo htmlspecialchars($category['category_name']); ?>
                                                    </strong>

                                                </div>

                                            </td>

                                            <td>

                                                <code class="text-primary">
                                                    <?php echo htmlspecialchars($category['slug']); ?>
                                                </code>

                                            </td>

                                            <td class="text-end">

                                                <a href="categories.php?edit=<?php echo $category['id']; ?>"
                                                    class="btn btn-sm btn-warning">

                                                    <i class="fa-solid fa-pen"></i>

                                                </a>

                                                <a href="categories.php?delete=<?php echo $category['id']; ?>"
                                                    class="btn btn-sm btn-danger"
                                                    onclick="return confirm('Delete this category?')">

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

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>
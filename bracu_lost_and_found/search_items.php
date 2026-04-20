<?php
session_start();
include 'db.php';

// Fetch filters from GET request, or set default empty values
$category = $_GET['category'] ?? '';
$location = $_GET['location'] ?? '';
$date = $_GET['date'] ?? '';
$status = $_GET['status'] ?? '';

// Prepare dynamic SQL
$sql = "SELECT * FROM reported_items WHERE approved = 'Approved'";
$params = [];
$types = "";

// Add filters if provided
if (!empty($category)) {
    $sql .= " AND item_category = ?";
    $params[] = $category;
    $types .= "s";
}
if (!empty($location)) {
    $sql .= " AND location = ?";
    $params[] = $location;
    $types .= "s";
}
if (!empty($date)) {
    $sql .= " AND date = ?";
    $params[] = $date;
    $types .= "s";
}
if (!empty($status)) {
    $sql .= " AND status = ?";
    $params[] = $status;
    $types .= "s";
}

// Prepare and execute SQL statement with or without parameters
$stmt = $conn->prepare($sql);
if ($params) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$results = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Search Items - BRACU Lost and Found Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="container mt-5">
    <h2 class="text-center mb-4">Search Lost/Found Items</h2>

    <form method="GET" class="row g-3 mb-4">
        <!-- Category Filter -->
        <div class="col-md-3">
            <label for="category" class="form-label">Category</label>
            <select name="category" id="category" class="form-select">
                <option value="">All Categories</option>
                <option value="Cards and Documents" <?= $category === 'Cards and Documents' ? 'selected' : '' ?>>Cards and Documents</option>
                <option value="Bags & Wallets" <?= $category === 'Bags & Wallets' ? 'selected' : '' ?>>Bags & Wallets</option>
                <option value="Electronics & Gadgets" <?= $category === 'Electronics & Gadgets' ? 'selected' : '' ?>>Electronics & Gadgets</option>
                <option value="Accessories" <?= $category === 'Accessories' ? 'selected' : '' ?>>Accessories</option>
                <option value="Clothing" <?= $category === 'Clothing' ? 'selected' : '' ?>>Clothing</option>
                <option value="Keys" <?= $category === 'Keys' ? 'selected' : '' ?>>Keys</option>
                <option value="Vehicles" <?= $category === 'Vehicles' ? 'selected' : '' ?>>Vehicles</option>
                <option value="Others" <?= $category === 'Others' ? 'selected' : '' ?>>Others</option>
            </select>
        </div>

        <!-- Location Filter -->
        <div class="col-md-3">
            <label for="location" class="form-label">Location</label>
            <select name="location" id="location" class="form-select">
                <option value="">All Locations</option>
                <option value="Library" <?= $location === 'Library' ? 'selected' : '' ?>>Library</option>
                <option value="Cafeteria" <?= $location === 'Cafeteria' ? 'selected' : '' ?>>Cafeteria</option>
                <option value="Rooftop" <?= $location === 'Rooftop' ? 'selected' : '' ?>>Rooftop</option>
                <option value="Classroom" <?= $location === 'Classroom' ? 'selected' : '' ?>>Classroom</option>
                <option value="Lab" <?= $location === 'Lab' ? 'selected' : '' ?>>Lab</option>
                <option value="Multipurpose Hall" <?= $location === 'Multipurpose Hall' ? 'selected' : '' ?>>Multipurpose Hall</option>
                <option value="Auditorium" <?= $location === 'Auditorium' ? 'selected' : '' ?>>Auditorium</option>
                <option value="Others" <?= $location === 'Others' ? 'selected' : '' ?>>Others</option>
            </select>
        </div>

        <!-- Status Filter -->
        <div class="col-md-3">
            <label for="status" class="form-label">Status</label>
            <select name="status" id="status" class="form-select">
                <option value="">All</option>
                <option value="lost" <?= $status === 'lost' ? 'selected' : '' ?>>Lost</option>
                <option value="found" <?= $status === 'found' ? 'selected' : '' ?>>Found</option>
            </select>
        </div>

        <!-- Date Filter -->
        <div class="col-md-3">
            <label for="date" class="form-label">Date</label>
            <input type="date" name="date" id="date" class="form-control" value="<?= htmlspecialchars($date) ?>">
        </div>

        <div class="col-12 text-center">
            <button type="submit" class="btn btn-primary px-5">Search</button>
        </div>
    </form>

    <!-- Display Results -->
    <?php if ($results->num_rows > 0): ?>
        <div class="row">
            <?php while ($item = $results->fetch_assoc()): ?>
                <div class="col-md-6 mb-4">
                    <div class="card shadow-sm">
                        <?php if (!empty($item['image_url']) && file_exists($item['image_url'])): ?>
                            <img src="<?= $item['image_url'] ?>" class="card-img-top" alt="Item Image" style="max-height: 250px; object-fit: contain;">
                        <?php endif; ?>
                        <div class="card-body">
                            <p class="card-text"><strong>Item ID:</strong> <?= htmlspecialchars($item['id']) ?></p>
                            <h5 class="card-title"><?= htmlspecialchars($item['item_category']) ?> (<?= htmlspecialchars($item['status']) ?>)</h5>
                            <p class="card-text"><strong>Description:</strong> <?= htmlspecialchars($item['item_description']) ?></p>
                            <p class="card-text"><strong>Location:</strong> <?= htmlspecialchars($item['location']) ?> - <?= htmlspecialchars($item['location_details']) ?></p>
                            <p class="card-text"><strong>Date:</strong> <?= htmlspecialchars($item['date']) ?></p>
                            <p class="card-text"><strong>Reported by:</strong> <?= htmlspecialchars($item['username']) ?></p>
                            <p class="card-text"><strong>Reporter Details:</strong> <?= nl2br(htmlspecialchars($item['reporter_details'])) ?></p>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div class="alert alert-warning text-center">No items found matching the selected filters.</div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

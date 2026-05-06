<?php
session_start();
require_once '../config.php';

// Check Login
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit();
}

// Handle Delete Image
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    
    // Get image path to unlink
    $query = $conn->query("SELECT image_path FROM gallery WHERE id = $delete_id");
    if ($query && $row = $query->fetch_assoc()) {
        $file_path = "../" . $row['image_path'];
        if (file_exists($file_path)) {
            unlink($file_path);
        }
    }
    
    $conn->query("DELETE FROM gallery WHERE id = $delete_id");
    header("Location: gallery.php?msg=deleted");
    exit();
}

// Handle Multiple Delete
if (isset($_POST['bulk_delete']) && isset($_POST['image_ids'])) {
    foreach ($_POST['image_ids'] as $id) {
        $id = intval($id);
        $query = $conn->query("SELECT image_path FROM gallery WHERE id = $id");
        if ($query && $row = $query->fetch_assoc()) {
            $file_path = "../" . $row['image_path'];
            if (file_exists($file_path)) unlink($file_path);
        }
        $conn->query("DELETE FROM gallery WHERE id = $id");
    }
    header("Location: gallery.php?msg=bulk_deleted");
    exit();
}

// Handle Image Upload
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['gallery_image'])) {
    $category = $conn->real_escape_string($_POST['category']);
    $target_dir = "../uploads/gallery/";
    if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
    
    $total_files = count($_FILES['gallery_image']['name']);
    $success_count = 0;
    
    for ($i = 0; $i < $total_files; $i++) {
        if ($_FILES['gallery_image']['error'][$i] == 0) {
            $image_name = time() . "_" . $i . "_" . basename($_FILES['gallery_image']['name'][$i]);
            $target_file = $target_dir . $image_name;
            
            if (move_uploaded_file($_FILES['gallery_image']['tmp_name'][$i], $target_file)) {
                $image_path = "uploads/gallery/" . $image_name;
                $conn->query("INSERT INTO gallery (image_path, category) VALUES ('$image_path', '$category')");
                $success_count++;
            }
        }
    }
    
    if ($success_count > 0) {
        $success = "$success_count images uploaded successfully to $category!";
    } else {
        $error = "Failed to upload images.";
    }
}

// Fetch Gallery Images
$images = $conn->query("SELECT * FROM gallery ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gallery Management - Admin</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Inter', sans-serif; }
        body { background: #0a0a0a; color: #fff; display: flex; }
        
        /* Sidebar Styles */
        .sidebar { width: 260px; background: #111; padding: 30px 20px; border-right: 1px solid #222; min-height: 100vh; position: sticky; top: 0; }
        .logo-container { margin-bottom: 40px; text-align: center; }
        .logo-container img { max-width: 150px; }
        .nav-links a { display: block; padding: 12px 15px; color: #888; text-decoration: none; border-radius: 8px; margin-bottom: 5px; transition: 0.3s; }
        .nav-links a:hover, .nav-links a.active { background: #1a1a1a; color: #ff6600; }
        .nav-links a i { margin-right: 10px; }
        
        /* Main Content */
        .main-content { flex: 1; padding: 40px; }
        header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        h1 { font-size: 24px; font-weight: 600; }
        
        .card { background: #111; border: 1px solid #222; border-radius: 12px; padding: 25px; margin-bottom: 30px; }
        .form-grid { display: grid; grid-template-columns: 1fr 1fr auto; gap: 20px; align-items: end; }
        
        label { display: block; margin-bottom: 8px; font-size: 14px; color: #aaa; }
        select, input[type="file"] { width: 100%; padding: 12px; background: #1a1a1a; border: 1px solid #333; color: #fff; border-radius: 8px; outline: none; }
        .btn { padding: 12px 25px; border-radius: 8px; border: none; cursor: pointer; font-weight: 500; transition: 0.3s; text-decoration: none; display: inline-block; }
        .btn-primary { background: #ff6600; color: #fff; }
        .btn-primary:hover { background: #e65c00; }
        .btn-danger { background: #dc2626; color: #fff; }
        .btn-secondary { background: #333; color: #fff; }

        /* Gallery Grid */
        .gallery-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 20px; }
        .gallery-item { position: relative; background: #1a1a1a; border-radius: 8px; overflow: hidden; border: 1px solid #222; }
        .gallery-item img { width: 100%; height: 150px; object-fit: cover; }
        .gallery-item .info { padding: 10px; font-size: 12px; display: flex; justify-content: space-between; align-items: center; }
        .gallery-item .category-tag { background: #333; padding: 2px 8px; border-radius: 4px; color: #ff6600; }
        .gallery-item .delete-overlay { position: absolute; top: 5px; right: 5px; opacity: 0; transition: 0.3s; }
        .gallery-item:hover .delete-overlay { opacity: 1; }
        .checkbox-container { position: absolute; top: 5px; left: 5px; z-index: 2; }
        
        .alert { padding: 15px; border-radius: 8px; margin-bottom: 20px; }
        .alert-success { background: rgba(34, 197, 94, 0.1); border: 1px solid #22c55e; color: #4ade80; }
        .alert-error { background: rgba(239, 68, 68, 0.1); border: 1px solid #ef4444; color: #f87171; }
        
        .bulk-actions { margin-bottom: 20px; display: flex; align-items: center; gap: 15px; }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="logo-container">
            <img src="../images/logo.png" alt="Netcoder Logo">
        </div>
        <nav class="nav-links">
            <a href="admin.php">Dashboard & Blogs</a>
            <a href="gallery.php" class="active">Gallery Management</a>
            <a href="logout.php">Logout</a>
        </nav>
    </div>

    <div class="main-content">
        <header>
            <h1>Gallery Management</h1>
            <div class="user-info">Admin Panel</div>
        </header>

        <?php if(isset($success)) echo "<div class='alert alert-success'>$success</div>"; ?>
        <?php if(isset($error)) echo "<div class='alert alert-error'>$error</div>"; ?>

        <!-- Upload Form -->
        <div class="card">
            <h3 style="margin-bottom: 20px; color: #ff6600;">Upload New Photos</h3>
            <form method="POST" enctype="multipart/form-data" class="form-grid">
                <div>
                    <label>Select Category</label>
                    <select name="category" required>
                        <option value="Seminar">Seminar</option>
                        <option value="Workshops">Workshops</option>
                        <option value="Session">Session</option>
                        <option value="Activities">Activities</option>
                        <option value="Campus">Campus</option>
                    </select>
                </div>
                <div>
                    <label>Choose Images (Multiple allowed)</label>
                    <input type="file" name="gallery_image[]" multiple required accept="image/*">
                </div>
                <div>
                    <button type="submit" class="btn btn-primary">Upload Photos</button>
                </div>
            </form>
        </div>

        <!-- Gallery List -->
        <form method="POST">
            <div class="bulk-actions">
                <label style="display: inline-flex; align-items: center; cursor: pointer; color: #fff;">
                    <input type="checkbox" id="selectAll" style="margin-right: 10px;"> Select All
                </label>
                <button type="submit" name="bulk_delete" class="btn btn-danger" style="padding: 8px 15px; font-size: 14px;" onclick="return confirm('Delete selected images?')">Delete Selected</button>
            </div>

            <div class="gallery-grid">
                <?php if($images && $images->num_rows > 0): ?>
                    <?php while($row = $images->fetch_assoc()): ?>
                        <div class="gallery-item">
                            <div class="checkbox-container">
                                <input type="checkbox" name="image_ids[]" value="<?php echo $row['id']; ?>" class="img-checkbox">
                            </div>
                            <img src="../<?php echo $row['image_path']; ?>" alt="Gallery Image">
                            <div class="info">
                                <span class="category-tag"><?php echo $row['category']; ?></span>
                                <a href="gallery.php?delete_id=<?php echo $row['id']; ?>" class="btn-danger" style="padding: 2px 8px; border-radius: 4px; font-size: 10px; text-decoration: none;" onclick="return confirm('Delete this image?')">Delete</a>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p style="color: #666; grid-column: 1/-1; text-align: center; padding: 40px;">No images in gallery yet.</p>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <script>
        document.getElementById('selectAll').addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.img-checkbox');
            checkboxes.forEach(cb => cb.checked = this.checked);
        });
    </script>
</body>
</html>

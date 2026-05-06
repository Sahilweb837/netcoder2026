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
        $_SESSION['success_msg'] = "$success_count images uploaded successfully to $category!";
    } else {
        $_SESSION['error_msg'] = "Failed to upload images.";
    }
    header("Location: gallery.php");
    exit();
}

$success = $_SESSION['success_msg'] ?? null;
if (!$success) {
    if (isset($_GET['msg'])) {
        if ($_GET['msg'] == 'deleted') $success = "Image deleted successfully.";
        if ($_GET['msg'] == 'bulk_deleted') $success = "Selected images deleted successfully.";
    }
}
$error = isset($_SESSION['error_msg']) ? $_SESSION['error_msg'] : null;
unset($_SESSION['success_msg'], $_SESSION['error_msg']);

// Fetch Gallery Images
$images = $conn->query("SELECT * FROM gallery ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gallery Manager - Netcoder</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap');
        
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Outfit', sans-serif; }
        body { background: #fcfcfc; color: #333; display: flex; min-height: 100vh; }
        
        /* Sidebar Styles */
        .sidebar { width: 280px; background: #fff; padding: 30px 20px; border-right: 1px solid #eee; position: sticky; top: 0; height: 100vh; display: flex; flex-direction: column; }
        .logo-container { margin-bottom: 40px; text-align: center; }
        .logo-container img { max-width: 160px; }
        .nav-links p { color: #aaa; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1.5px; margin: 25px 0 12px 15px; font-weight: 700; }
        .nav-links a { display: flex; align-items: center; padding: 14px 18px; color: #555; text-decoration: none; border-radius: 12px; margin-bottom: 8px; transition: 0.3s cubic-bezier(0.4, 0, 0.2, 1); gap: 12px; font-weight: 500; }
        .nav-links a i { width: 20px; font-size: 1.2rem; transition: 0.3s; }
        .nav-links a:hover { background: #fff5f0; color: #ff6600; transform: translateX(5px); }
        .nav-links a.active { background: #ff6600; color: #fff; box-shadow: 0 10px 20px rgba(255, 102, 0, 0.2); }
        
        .main-content { flex: 1; padding: 50px; max-width: 1300px; margin: 0 auto; width: 100%; }
        
        /* Header */
        .header-flex { display: flex; justify-content: space-between; align-items: center; margin-bottom: 45px; }
        .header-title h1 { font-size: 2.2rem; color: #1a1a1a; font-weight: 700; }
        .header-title p { color: #888; }

        /* Upload Section */
        .upload-card { background: #fff; padding: 40px; border-radius: 24px; border: 1px solid #eee; box-shadow: 0 4px 12px rgba(0,0,0,0.02); margin-bottom: 50px; }
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 25px; margin-bottom: 25px; align-items: end; }
        
        label { display: block; margin-bottom: 12px; font-size: 0.95rem; color: #444; font-weight: 600; }
        input, select { width: 100%; padding: 15px 20px; background: #f9f9f9; border: 2px solid #eee; color: #1a1a1a; border-radius: 12px; outline: none; transition: 0.3s; font-size: 1rem; }
        input:focus, select:focus { 
            border-color: #ff6600; 
            background: #fff; 
            box-shadow: 0 0 0 4px rgba(255, 102, 0, 0.15); 
            transform: translateY(-1px);
        }
        
        .upload-btn { background: #ff6600; color: white; border: none; padding: 16px 35px; cursor: pointer; border-radius: 12px; font-weight: 700; transition: 0.3s; font-size: 1.1rem; display: flex; align-items: center; gap: 10px; justify-content: center; box-shadow: 0 10px 25px rgba(255, 102, 0, 0.25); width: 100%; }
        .upload-btn:hover { background: #e65c00; transform: translateY(-2px); box-shadow: 0 15px 35px rgba(255, 102, 0, 0.35); }

        /* Gallery Grid */
        .gallery-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 20px; margin-top: 30px; }
        .photo-card { background: #fff; border: 1px solid #eee; border-radius: 16px; overflow: hidden; position: relative; transition: 0.3s; box-shadow: 0 2px 8px rgba(0,0,0,0.01); }
        .photo-card:hover { transform: translateY(-5px); box-shadow: 0 15px 30px rgba(0,0,0,0.05); border-color: #ff660033; }
        .photo-card img { width: 100%; height: 140px; object-fit: cover; border-bottom: 1px solid #f5f5f5; }
        .photo-info { padding: 12px; display: flex; justify-content: space-between; align-items: center; }
        .photo-info span { font-size: 0.75rem; font-weight: 700; color: #ff6600; background: #fff5f0; padding: 3px 8px; border-radius: 12px; }
        
        .checkbox-wrapper { position: absolute; top: 10px; left: 10px; z-index: 2; background: rgba(255,255,255,0.8); padding: 4px; border-radius: 8px; line-height: 0; backdrop-filter: blur(4px); }
        .photo-checkbox { width: 18px; height: 18px; cursor: pointer; accent-color: #ff6600; }

        /* Bulk Actions */
        .bulk-actions { background: #fff; padding: 20px 30px; border-radius: 20px; border: 1px solid #eee; margin-bottom: 30px; display: flex; justify-content: space-between; align-items: center; position: sticky; top: 20px; z-index: 100; box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
        .delete-btn { background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; padding: 10px 20px; border-radius: 10px; font-weight: 700; cursor: pointer; transition: 0.3s; display: flex; align-items: center; gap: 8px; font-size: 0.9rem; }
        .delete-btn:hover { background: #dc2626; color: #fff; }

        .select-all-label { display: flex; align-items: center; gap: 10px; font-weight: 600; color: #555; cursor: pointer; }

        .alert { padding: 18px 25px; margin-bottom: 35px; border-radius: 16px; display: flex; align-items: center; gap: 15px; font-weight: 600; }
        .success { background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; }
        .error { background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; }
        
        .single-delete { color: #dc2626; text-decoration: none; font-size: 1.1rem; transition: 0.3s; }
        .single-delete:hover { transform: scale(1.2); }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="logo-container">
            <img src="../images/logo.png" alt="Netcoder Logo">
        </div>
        <nav class="nav-links">
            <p>Admin Controls</p>
            <a href="admin.php"><i class="fa-solid fa-grid-2"></i> Dashboard</a>
            <a href="gallery.php" class="active"><i class="fa-solid fa-images"></i> Gallery Manager</a>
            <p>Authentication</p>
            <a href="logout.php"><i class="fa-solid fa-arrow-right-from-bracket"></i> Sign Out</a>
        </nav>
    </div>

    <div class="main-content">
        <div class="header-flex">
            <div class="header-title">
                <h1>Gallery Management</h1>
                <p>Upload campus photos and organize categories.</p>
            </div>
        </div>

        <?php if(isset($success)) echo "<div class='alert success'><i class='fa-solid fa-check-circle'></i> $success</div>"; ?>
        <?php if(isset($error)) echo "<div class='alert error'><i class='fa-solid fa-exclamation-circle'></i> $error</div>"; ?>
        <?php if(isset($_GET['msg']) && $_GET['msg']=='deleted') echo "<div class='alert success'><i class='fa-solid fa-check-circle'></i> Image deleted successfully.</div>"; ?>

        <div class="upload-card">
            <form method="POST" enctype="multipart/form-data">
                <div class="form-grid">
                    <div>
                        <label><i class="fa-solid fa-tags"></i> Select Category</label>
                        <select name="category" required>
                            <option value="Seminar">Seminar</option>
                            <option value="Workshops">Workshops</option>
                            <option value="Session">Session</option>
                            <option value="Activities">Activities</option>
                            <option value="Campus">Campus</option>
                        </select>
                    </div>
                    <div>
                        <label><i class="fa-solid fa-images"></i> Choose Images (Multiple allowed)</label>
                        <input type="file" name="gallery_image[]" multiple required accept="image/*">
                    </div>
                </div>
                <button type="submit" class="upload-btn">
                    <i class="fa-solid fa-cloud-arrow-up"></i> Upload to Gallery
                </button>
            </form>
        </div>

        <form method="POST">
            <div class="bulk-actions">
                <label class="select-all-label">
                    <input type="checkbox" id="selectAll" class="photo-checkbox"> Select All
                </label>
                <button type="submit" name="bulk_delete" class="delete-btn" onclick="return confirm('Delete selected images?')">
                    <i class="fa-solid fa-trash-can"></i> Delete Selected
                </button>
            </div>

            <div class="gallery-grid">
                <?php if($images && $images->num_rows > 0): ?>
                    <?php while($row = $images->fetch_assoc()): ?>
                        <div class="photo-card">
                            <div class="checkbox-wrapper">
                                <input type="checkbox" name="image_ids[]" value="<?php echo $row['id']; ?>" class="img-checkbox photo-checkbox">
                            </div>
                            <img src="../<?php echo $row['image_path']; ?>" alt="Gallery Image">
                            <div class="photo-info">
                                <span><?php echo $row['category']; ?></span>
                                <a href="gallery.php?delete_id=<?php echo $row['id']; ?>" class="single-delete" onclick="return confirm('Delete this image?')" title="Delete">
                                    <i class="fa-solid fa-circle-minus"></i>
                                </a>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div style="grid-column: 1/-1; text-align: center; padding: 60px; background: #fff; border-radius: 24px; border: 1px dashed #ddd;">
                        <i class="fa-solid fa-images" style="font-size: 3rem; color: #eee; margin-bottom: 15px; display: block;"></i>
                        <p style="color: #999; font-weight: 500;">No images found in your gallery.</p>
                    </div>
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

 <?php
session_start();
require_once '../config.php';

// --- AUTO-FIX DATABASE (Self-Healing) ---
try {
    // 1. Ensure 'slug' exists
    $checkSlug = $conn->query("SHOW COLUMNS FROM blogs LIKE 'slug'");
    if ($checkSlug->num_rows == 0) {
        $conn->query("ALTER TABLE blogs ADD COLUMN slug VARCHAR(255) NOT NULL AFTER title");
    }

    // 2. Ensure 'main_content' exists
    $checkContent = $conn->query("SHOW COLUMNS FROM blogs LIKE 'main_content'");
    if ($checkContent->num_rows == 0) {
        $checkOldContent = $conn->query("SHOW COLUMNS FROM blogs LIKE 'content'");
        if ($checkOldContent->num_rows > 0) {
            $conn->query("ALTER TABLE blogs CHANGE content main_content TEXT");
        } else {
            $conn->query("ALTER TABLE blogs ADD COLUMN main_content TEXT AFTER excerpt");
        }
    }

    // 3. Ensure 'main_image' exists
    $checkImage = $conn->query("SHOW COLUMNS FROM blogs LIKE 'main_image'");
    if ($checkImage->num_rows == 0) {
        $checkOldImage = $conn->query("SHOW COLUMNS FROM blogs LIKE 'image_path'");
        if ($checkOldImage->num_rows > 0) {
            $conn->query("ALTER TABLE blogs CHANGE image_path main_image VARCHAR(255)");
        } else {
            $conn->query("ALTER TABLE blogs ADD COLUMN main_image VARCHAR(255) AFTER author");
        }
    }
    
    // 4. Ensure 'date_posted' exists
    $checkDate = $conn->query("SHOW COLUMNS FROM blogs LIKE 'date_posted'");
    if ($checkDate->num_rows == 0) {
        $conn->query("ALTER TABLE blogs ADD COLUMN date_posted DATE DEFAULT CURRENT_DATE");
    }

    // 5. Create 'blog_sections' table
    $conn->query("CREATE TABLE IF NOT EXISTS blog_sections (
        id INT AUTO_INCREMENT PRIMARY KEY,
        blog_id INT NOT NULL,
        section_title VARCHAR(255),
        section_content TEXT,
        section_image VARCHAR(255),
        FOREIGN KEY (blog_id) REFERENCES blogs(id) ON DELETE CASCADE
    )");

} catch (Exception $e) {
    die("Database Fix Error: " . $e->getMessage());
}
// --- END AUTO-FIX ---

// Check Login
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit();
}

// Initialize Edit Variables
$is_editing = false;
$edit_id = 0;
$title = $slug = $excerpt = $content = $author = $tags = '';
$current_image = '';
$existing_sections = [];

// --- HANDLE DELETE BLOG ---
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    
    // Delete Main Image
    $query = $conn->query("SELECT main_image FROM blogs WHERE id = $delete_id");
    if ($query && $row = $query->fetch_assoc()) {
        if(!empty($row['main_image']) && file_exists("../" . $row['main_image'])) unlink("../" . $row['main_image']);
    }

    // Delete Section Images
    $sec_query = $conn->query("SELECT section_image FROM blog_sections WHERE blog_id = $delete_id");
    while($sec_row = $sec_query->fetch_assoc()) {
        if(!empty($sec_row['section_image']) && file_exists("../" . $sec_row['section_image'])) unlink("../" . $sec_row['section_image']);
    }

    $conn->query("DELETE FROM blogs WHERE id = $delete_id");
    header("Location: admin.php?msg=deleted");
    exit();
}

// --- HANDLE DELETE SINGLE SECTION (During Edit) ---
if (isset($_GET['delete_section_id']) && isset($_GET['edit_id'])) {
    $sec_id = intval($_GET['delete_section_id']);
    $eid = intval($_GET['edit_id']);
    
    // Get image to unlink
    $q = $conn->query("SELECT section_image FROM blog_sections WHERE id = $sec_id");
    if ($r = $q->fetch_assoc()) {
        if(!empty($r['section_image']) && file_exists("../" . $r['section_image'])) unlink("../" . $r['section_image']);
    }
    
    $conn->query("DELETE FROM blog_sections WHERE id = $sec_id");
    header("Location: admin.php?edit_id=" . $eid . "&msg=section_deleted");
    exit();
}

// --- HANDLE FETCH FOR EDIT ---
if (isset($_GET['edit_id'])) {
    $is_editing = true;
    $edit_id = intval($_GET['edit_id']);
    $query = $conn->query("SELECT * FROM blogs WHERE id = $edit_id");
    
    if ($row = $query->fetch_assoc()) {
        $title = $row['title'];
        $slug = $row['slug'];
        $excerpt = $row['excerpt'];
        $content = $row['main_content'];
        $author = $row['author'];
        $tags = $row['tags'];
        $current_image = $row['main_image'];
        
        // Fetch Sections
        $sec_res = $conn->query("SELECT * FROM blog_sections WHERE blog_id = $edit_id");
        while($s = $sec_res->fetch_assoc()) {
            $existing_sections[] = $s;
        }
    }
}

// --- HANDLE FORM SUBMISSION (CREATE & UPDATE) ---
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title_in = $conn->real_escape_string($_POST['title']);
    $content_in = $conn->real_escape_string($_POST['content']); 
    $excerpt_in = $conn->real_escape_string($_POST['excerpt']);
    $author_in = $conn->real_escape_string($_POST['author']);
    $tags_in = $conn->real_escape_string($_POST['tags']);
    
    // Slug Generation
    if (!empty($_POST['slug'])) {
        $slug_in = strtolower(str_replace(' ', '-', preg_replace('/[^a-zA-Z0-9\s-]/', '', $_POST['slug'])));
    } else {
        $slug_in = strtolower(str_replace(' ', '-', preg_replace('/[^a-zA-Z0-9\s-]/', '', $title_in)));
    }
    
    // Handle Main Image Upload
    $target_dir = "../uploads/blog_images/";
    if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
    
    $final_image_path = $_POST['current_image_path'] ?? ''; // Default to existing image

    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $image_name = time() . '_main_' . basename($_FILES['image']['name']);
        $target_file = $target_dir . $image_name;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
            $final_image_path = "uploads/blog_images/" . $image_name;
        }
    }

    if (isset($_POST['update_mode']) && $_POST['update_mode'] == '1') {
        // --- UPDATE LOGIC ---
        $blog_id_update = intval($_POST['blog_id']);
        
        $stmt = $conn->prepare("UPDATE blogs SET title=?, slug=?, main_content=?, excerpt=?, main_image=?, author=?, tags=? WHERE id=?");
        $stmt->bind_param("sssssssi", $title_in, $slug_in, $content_in, $excerpt_in, $final_image_path, $author_in, $tags_in, $blog_id_update);
        
        if ($stmt->execute()) {
            $blog_id = $blog_id_update; // Set for section saving
            $success = "Blog updated successfully!";
            // Refresh variables
            $is_editing = false; $title = ''; $content = ''; $current_image = '';
        } else {
            $error = "Update failed: " . $stmt->error;
        }

    } else {
        // --- CREATE LOGIC ---
        // Unique Slug Check only for Create
        $checkSlug = $conn->query("SELECT id FROM blogs WHERE slug = '$slug_in'");
        if($checkSlug->num_rows > 0) $slug_in .= '-' . time();

        $stmt = $conn->prepare("INSERT INTO blogs (title, slug, main_content, excerpt, main_image, author, tags, date_posted) VALUES (?, ?, ?, ?, ?, ?, ?, CURDATE())");
        $stmt->bind_param("sssssss", $title_in, $slug_in, $content_in, $excerpt_in, $final_image_path, $author_in, $tags_in);
        
        if ($stmt->execute()) {
            $blog_id = $conn->insert_id;
            $success = "Blog created successfully!";
        } else {
            $error = "Create failed: " . $stmt->error;
        }
    }

    // --- SAVE NEW SECTIONS (Common for Create & Update) ---
    if (isset($blog_id) && isset($_POST['section_title'])) {
        $section_titles = $_POST['section_title'];
        $section_contents = $_POST['section_content'];
        
        for ($i = 0; $i < count($section_titles); $i++) {
            $s_title = $conn->real_escape_string($section_titles[$i]);
            $s_content = $conn->real_escape_string($section_contents[$i]);
            $s_image_path = '';

            if (isset($_FILES['section_image']['name'][$i]) && $_FILES['section_image']['error'][$i] == 0) {
                $s_img_name = time() . "_sec{$i}_" . basename($_FILES['section_image']['name'][$i]);
                $s_target = $target_dir . $s_img_name;
                if (move_uploaded_file($_FILES['section_image']['tmp_name'][$i], $s_target)) {
                    $s_image_path = "uploads/blog_images/" . $s_img_name;
                }
            }

            if(!empty($s_title) || !empty($s_content) || !empty($s_image_path)){
                $sec_stmt = $conn->prepare("INSERT INTO blog_sections (blog_id, section_title, section_content, section_image) VALUES (?, ?, ?, ?)");
                $sec_stmt->bind_param("isss", $blog_id, $s_title, $s_content, $s_image_path);
                $sec_stmt->execute();
            }
        }
    }
}

// Fetch Blogs for List
$blogs = $conn->query("SELECT * FROM blogs ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
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
        .nav-links a.active i { color: #fff; }
        
        .main-content { flex: 1; padding: 50px; max-width: 1300px; margin: 0 auto; width: 100%; }
        
        /* Dashboard Header */
        .header-flex { display: flex; justify-content: space-between; align-items: center; margin-bottom: 45px; }
        .header-title h1 { font-size: 2.2rem; color: #1a1a1a; font-weight: 700; }
        .header-title p { color: #888; margin-top: 5px; }

        /* Dashboard Cards */
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 25px; margin-bottom: 50px; }
        .stat-card { background: #fff; border: 1px solid #eee; padding: 30px; border-radius: 24px; display: flex; align-items: center; gap: 20px; transition: 0.4s; box-shadow: 0 4px 12px rgba(0,0,0,0.02); }
        .stat-card:hover { transform: translateY(-5px); box-shadow: 0 20px 40px rgba(0,0,0,0.05); border-color: #ff660044; }
        .stat-icon { width: 64px; height: 64px; border-radius: 18px; background: #fff5f0; color: #ff6600; display: flex; align-items: center; justify-content: center; font-size: 1.8rem; }
        .stat-info h3 { font-size: 1.8rem; font-weight: 800; color: #1a1a1a; }
        .stat-info p { color: #888; font-size: 0.95rem; font-weight: 500; }

        /* Form Controls */
        .form-container { background: #fff; padding: 40px; border-radius: 24px; border: 1px solid #eee; box-shadow: 0 4px 12px rgba(0,0,0,0.02); }
        label { display: block; margin-bottom: 12px; font-size: 0.95rem; color: #444; font-weight: 600; }
        input, textarea, select { width: 100%; padding: 15px 20px; margin-bottom: 25px; background: #f9f9f9; border: 2px solid #eee; color: #1a1a1a; border-radius: 12px; outline: none; transition: 0.3s; font-size: 1rem; }
        input:focus, textarea:focus, select:focus { 
            border-color: #ff6600; 
            background: #fff; 
            box-shadow: 0 0 0 4px rgba(255, 102, 0, 0.15); 
            transform: translateY(-1px);
        }
        
        .submit-btn { background: #ff6600; color: white; border: none; padding: 16px 35px; cursor: pointer; border-radius: 12px; font-weight: 700; transition: 0.3s; width: 100%; font-size: 1.1rem; box-shadow: 0 10px 25px rgba(255, 102, 0, 0.25); }
        .submit-btn:hover { background: #e65c00; transform: translateY(-2px); box-shadow: 0 15px 35px rgba(255, 102, 0, 0.35); }
        
        .add-sec-btn { background: #fdfdfd; color: #ff6600; border: 2px dashed #ff660044; padding: 20px; width: 100%; cursor: pointer; margin-bottom: 35px; border-radius: 16px; font-weight: 700; display: flex; align-items: center; justify-content: center; gap: 10px; transition: 0.3s; }
        .add-sec-btn:hover { background: #fff5f0; border-color: #ff6600; }
        
        .content-section { border: 2px solid #f0f0f0; padding: 30px; margin-bottom: 30px; position: relative; border-radius: 20px; background: #fbfbfb; }
        .existing-section { border: 1px solid #eee; background: #fff; padding: 18px 25px; margin-bottom: 15px; border-radius: 15px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 5px rgba(0,0,0,0.01); }
        
        .alert { padding: 18px 25px; margin-bottom: 35px; border-radius: 16px; display: flex; align-items: center; gap: 15px; font-weight: 600; }
        .success { background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; }
        .error { background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; }
        
        .blog-item { padding: 25px; background: #fff; border: 1px solid #eee; border-radius: 20px; margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center; transition: 0.3s; box-shadow: 0 2px 8px rgba(0,0,0,0.01); }
        .blog-item:hover { border-color: #ff660044; transform: translateY(-3px); box-shadow: 0 10px 25px rgba(0,0,0,0.04); }
        .blog-info h4 { font-size: 1.15rem; color: #1a1a1a; margin-bottom: 4px; }
        .blog-info p { color: #888; font-size: 0.85rem; }

        .actions a { padding: 12px 20px; border-radius: 10px; text-decoration: none; font-size: 0.9rem; margin-left: 12px; font-weight: 700; transition: 0.3s; display: inline-flex; align-items: center; gap: 8px; }
        .edit-btn { background: #eff6ff; color: #2563eb; }
        .delete-btn { background: #fef2f2; color: #dc2626; }
        .edit-btn:hover { background: #2563eb; color: #fff; }
        .delete-btn:hover { background: #dc2626; color: #fff; }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="logo-container">
            <img src="../images/logo.png" alt="Netcoder Logo">
        </div>
        <nav class="nav-links">
            <p>Admin Controls</p>
            <a href="admin.php" class="active"><i class="fa-solid fa-grid-2"></i> Dashboard</a>
            <a href="gallery.php"><i class="fa-solid fa-images"></i> Gallery Manager</a>
            <p>Authentication</p>
            <a href="logout.php"><i class="fa-solid fa-arrow-right-from-bracket"></i> Sign Out</a>
        </nav>
    </div>

    <div class="main-content">
        <div class="header-flex">
            <div class="header-title">
                <h1><?php echo $is_editing ? 'Modify Content' : 'Publish New Blog'; ?></h1>
                <p>Welcome to your control center, update your audience today.</p>
            </div>
            <a href="admin.php" style="text-decoration:none; display:flex; align-items:center; gap:10px; background: #ff6600; color:white; padding:12px 24px; border-radius:14px; font-weight:700; box-shadow: 0 8px 15px rgba(255,102,0,0.2);"> <i class="fa-solid fa-plus"></i> Reset Form</a>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon"><i class="fa-solid fa-file-invoice"></i></div>
                <div class="stat-info">
                    <h3><?php echo $blogs->num_rows; ?></h3>
                    <p>Total Articles</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fa-solid fa-images"></i></div>
                <div class="stat-info">
                    <h3><?php 
                        $g_count = $conn->query("SELECT COUNT(*) as total FROM gallery")->fetch_assoc();
                        echo $g_count['total']; 
                    ?></h3>
                    <p>Gallery Photos</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fa-solid fa-bolt"></i></div>
                <div class="stat-info">
                    <h3>Verified</h3>
                    <p>Site Integrity</p>
                </div>
            </div>
        </div>

        <?php if(isset($success)) echo "<div class='alert success'><i class='fa-solid fa-check-circle'></i> $success</div>"; ?>
        <?php if(isset($error)) echo "<div class='alert error'><i class='fa-solid fa-exclamation-circle'></i> $error</div>"; ?>
        <?php if(isset($_GET['msg']) && $_GET['msg']=='section_deleted') echo "<div class='alert success'><i class='fa-solid fa-check-circle'></i> Section removed successfully.</div>"; ?>

        <div class="form-container">
            <form method="POST" enctype="multipart/form-data">
                
                <?php if($is_editing): ?>
                    <input type="hidden" name="update_mode" value="1">
                    <input type="hidden" name="blog_id" value="<?php echo $edit_id; ?>">
                    <input type="hidden" name="current_image_path" value="<?php echo $current_image; ?>">
                <?php endif; ?>

                <label>Blog Title *</label>
                <input type="text" name="title" value="<?php echo htmlspecialchars($title); ?>" required id="titleInput" onkeyup="generateSlug()">

                <label>Slug (URL) - Optional</label>
                <input type="text" name="slug" value="<?php echo htmlspecialchars($slug); ?>" id="slugInput">

                <label>Excerpt (Short Description)</label>
                <textarea name="excerpt" rows="3"><?php echo htmlspecialchars($excerpt); ?></textarea>

                <label>Main Content *</label>
                <textarea name="content" rows="6" required><?php echo htmlspecialchars($content); ?></textarea>

                <label>Main Image</label>
                <?php if($is_editing && !empty($current_image)): ?>
                    <div style="margin-bottom:10px;">
                        <img src="../<?php echo $current_image; ?>" style="height:80px; border-radius:4px;">
                        <br><small style="color:#888;">Current Image</small>
                    </div>
                <?php endif; ?>
                <input type="file" name="image">

                <?php if($is_editing && !empty($existing_sections)): ?>
                    <h3 style="margin: 20px 0;">Existing Content Sections</h3>
                    <?php foreach($existing_sections as $es): ?>
                        <div class="existing-section">
                            <div>
                                <strong><?php echo htmlspecialchars($es['section_title']); ?></strong><br>
                                <small><?php echo substr(htmlspecialchars($es['section_content']), 0, 50); ?>...</small>
                            </div>
                            <a href="admin.php?edit_id=<?php echo $edit_id; ?>&delete_section_id=<?php echo $es['id']; ?>" 
                               onclick="return confirm('Delete this section?')" 
                               style="color:red; text-decoration:none;">Delete</a>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>

                <h3 style="margin: 20px 0;">Add New Content Sections</h3>
                <div id="sections-container"></div>
                <button type="button" class="add-sec-btn" onclick="addSection()">+ Add Another Section (Image & Text)</button>

                <label>Author</label>
                <input type="text" name="author" value="<?php echo !empty($author) ? htmlspecialchars($author) : 'Admin'; ?>">
                
                <label>Tags</label>
                <input type="text" name="tags" value="<?php echo htmlspecialchars($tags); ?>">

                <button type="submit" class="submit-btn"><?php echo $is_editing ? 'Update Blog' : 'Publish Blog'; ?></button>
                <?php if($is_editing): ?>
                    <a href="admin.php" class="btn-cancel">Cancel Edit</a>
                <?php endif; ?>
            </form>
        </div>

        <div style="margin-top: 60px;">
            <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 30px;">
                <div style="width: 5px; height: 30px; background: #ff6600; border-radius: 5px;"></div>
                <h2 style="font-size: 1.6rem; color: #1a1a1a;">Manage Existing Blogs</h2>
            </div>
            
            <?php if($blogs && $blogs->num_rows > 0): ?>
                <?php while($row = $blogs->fetch_assoc()): ?>
                    <div class="blog-item">
                        <div class="blog-info">
                            <h4><?php echo htmlspecialchars($row['title']); ?></h4>
                            <p><i class="fa-solid fa-link" style="font-size: 0.75rem;"></i> <?php echo $row['slug']; ?></p>
                        </div>
                        <div class="actions">
                            <a href="admin.php?edit_id=<?php echo $row['id']; ?>" class="edit-btn">
                                <i class="fa-solid fa-pen-to-square"></i> Edit
                            </a>
                            <a href="admin.php?delete_id=<?php echo $row['id']; ?>" class="delete-btn" onclick="return confirm('Are you sure you want to delete this blog?');">
                                <i class="fa-solid fa-trash-can"></i> Delete
                            </a>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div style="text-align: center; padding: 50px; background: #fff; border-radius: 20px; border: 1px dashed #ddd;">
                    <p style="color: #999;">No blogs published yet. Start by writing your first post!</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        function generateSlug() {
            // Optional auto-fill
        }
        function addSection() {
            const container = document.getElementById('sections-container');
            const div = document.createElement('div');
            div.className = 'content-section';
            div.innerHTML = `
                <button type="button" class="remove-btn" onclick="this.parentElement.remove()" style="position: absolute; top: 15px; right: 15px; background: #dc2626; color: white; border: none; width: 30px; height: 30px; border-radius: 50%; cursor: pointer; display: flex; align-items: center; justify-content: center; font-weight: 700;">&times;</button>
                <div style="margin-bottom: 15px; display: flex; align-items: center; gap: 10px; color: #ff6600;">
                    <i class="fa-solid fa-layer-group"></i>
                    <h4 style="margin:0;">New Section Content</h4>
                </div>
                <label>Section Title</label>
                <input type="text" name="section_title[]" placeholder="Enter section heading...">
                <label>Section Content</label>
                <textarea name="section_content[]" rows="4" placeholder="Enter section body text..."></textarea>
                <label>Section Image (Optional)</label>
                <input type="file" name="section_image[]">
            `;
            container.appendChild(div);
            // Scroll to the new section smoothly
            div.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    </script>
</body>
</html>

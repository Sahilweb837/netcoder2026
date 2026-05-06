<?php
require_once 'config.php';

// Fetch categories for filtering
$category_res = $conn->query("SELECT DISTINCT category FROM gallery");
$categories = [];
if ($category_res) {
    while ($row = $category_res->fetch_assoc()) {
        $categories[] = $row['category'];
    }
}

// Fetch all images
$images_res = $conn->query("SELECT * FROM gallery ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="keywords"
        content="Netcoder Technology gallery, IT training photos, student activities, workshops and events, training institute images, Dharamshala, campus life, student projects, technology workshops, educational events">
    <meta name="description"
        content="Explore the Netcoder Technology gallery to see highlights from our IT training programs, student projects, workshops, and events in Dharamshala. Discover the vibrant learning community we foster">
    <title>Gallery | Netcoder Technology - Explore Our IT Training Moments</title>
    <!-- canonical tag -->
    <link rel="canonical" href="https://www.netcoder.in/gallery/" />
    <!-- favicon -->
    <link rel="shortcut icon" href="images/net-coder-logo icon.png">
    <!-- css -->
    <link rel="stylesheet" href="style.css">
    <!-- font awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
        .gallery-filter {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 15px;
            margin: 40px 0;
        }
        .filter-btn {
            padding: 10px 25px;
            border-radius: 30px;
            border: 2px solid #ff6600;
            background: transparent;
            color: #fff;
            cursor: pointer;
            font-weight: 600;
            transition: 0.3s;
        }
        .filter-btn.active, .filter-btn:hover {
            background: #ff6600;
            color: #fff;
        }
        .gallery-grid-dynamic {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            padding: 20px 0;
        }
        .gallery-item {
            position: relative;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            aspect-ratio: 4/3;
            transition: 0.4s;
        }
        .gallery-item:hover {
            transform: translateY(-10px);
        }
        .gallery-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: 0.5s;
        }
        .gallery-item:hover img {
            transform: scale(1.1);
        }
        .gallery-item .overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            padding: 20px;
            background: linear-gradient(transparent, rgba(0,0,0,0.8));
            color: #fff;
            opacity: 0;
            transition: 0.3s;
        }
        .gallery-item:hover .overlay {
            opacity: 1;
        }
        .hidden {
            display: none;
        }
    </style>
</head>

<body>
    <header class="main-header">
        <nav class="main-nav">
            <div class="brand-logo">
                <a href="index.html">
                    <img src="./images/logo.png">
                </a>
            </div>
            <div class="menu-wrapper">
                <div class="menu-item">
                    <div class="menu-link">
                        <i class="fa-solid fa-building-columns"></i>
                        On-Campus Courses <span class="icon-arrow"></span>
                    </div>
                    <!-- Mega Menu (Truncated for brevity, should match original) -->
                </div>
                <a href="services.html" class="menu-link">Services</a>
                <a href="gallery.php" class="menu-link active">Gallery</a>
                <a href="blog.php" class="menu-link">Blog</a>
            </div>
            <div class="right-menu">
                <div class="menu-toggle" id="openMenu">
                    <span></span><span></span><span></span>
                </div>
                <a href="contact.html" class="color-btn">Contact</a>
            </div>
        </nav>
    </header>

    <!-- hero section -->
    <section class="page-hero">
        <ul class="circles">
            <?php for($i=0; $i<10; $i++) echo "<li></li>"; ?>
        </ul>
        <div class="page-title">
            <div>GALLERY</div>
            <p>HOME / <b>Gallery</b></p>
        </div>
    </section>

    <div class="container">
        <!-- Filter Buttons -->
        <div class="gallery-filter">
            <button class="filter-btn active" data-filter="all">All Photos</button>
            <?php foreach($categories as $cat): ?>
                <button class="filter-btn" data-filter="<?php echo strtolower($cat); ?>"><?php echo $cat; ?></button>
            <?php endforeach; ?>
        </div>

        <!-- Dynamic Gallery Grid -->
        <div class="gallery-grid-dynamic" id="galleryGrid">
            <?php if($images_res && $images_res->num_rows > 0): ?>
                <?php while($img = $images_res->fetch_assoc()): ?>
                    <div class="gallery-item" data-category="<?php echo strtolower($img['category']); ?>">
                        <img src="<?php echo $img['image_path']; ?>" alt="Gallery Image">
                        <div class="overlay">
                            <h4><?php echo $img['category']; ?></h4>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <!-- Fallback to static images if DB is empty -->
                <div class="gallery-item" data-category="campus"><img src="images/netcoder classroom.jpg"></div>
                <div class="gallery-item" data-category="campus"><img src="images/netcoder lab.jpg"></div>
                <div class="gallery-item" data-category="seminar"><img src="images/Event-Netcoder.jpg"></div>
                <div class="gallery-item" data-category="activities"><img src="images/Award- netcoder.jpg"></div>
            <?php endif; ?>
        </div>
    </div>

    <footer>
        <!-- Footer content (Matches original) -->
        <div class="copyright">
            <div class="container">
                <p>Copyright &copy;2026 All rights reserved by &hearts; <a href="index.html">Netcoder Technology</a></p>
            </div>
        </div>
    </footer>

    <script>
        // Filtering Logic
        const filterBtns = document.querySelectorAll('.filter-btn');
        const galleryItems = document.querySelectorAll('.gallery-item');

        filterBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                // Remove active class from all buttons
                filterBtns.forEach(b => b.classList.remove('active'));
                // Add active class to clicked button
                btn.classList.add('active');

                const filterValue = btn.getAttribute('data-filter');

                galleryItems.forEach(item => {
                    if (filterValue === 'all' || item.getAttribute('data-category') === filterValue) {
                        item.classList.remove('hidden');
                    } else {
                        item.classList.add('hidden');
                    }
                });
            });
        });
    </script>
    <script src="main.js"></script>
</body>
</html>

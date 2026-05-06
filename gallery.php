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
        :root {
            --primary: #ff6600;
            --bg-light: #ffffff;
            --bg-secondary: #f8f9fa;
            --text-dark: #1a1a1a;
            --text-muted: #666;
            --card-border: #eee;
        }

        body {
            background-color: var(--bg-light);
            color: var(--text-dark);
        }

        .gallery-section {
            padding: 80px 0;
            background: var(--bg-light);
        }

        .gallery-filter {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 12px;
            margin-bottom: 60px;
        }

        .filter-btn {
            padding: 12px 28px;
            border-radius: 50px;
            border: 2px solid #eee;
            background: #fff;
            color: var(--text-muted);
            cursor: pointer;
            font-weight: 600;
            transition: 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.02);
        }

        .filter-btn i { font-size: 0.8rem; }

        .filter-btn.active, .filter-btn:hover {
            background: var(--primary);
            border-color: var(--primary);
            color: #fff;
            box-shadow: 0 8px 25px rgba(255, 102, 0, 0.25);
            transform: translateY(-3px);
        }

        .gallery-grid-dynamic {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 30px;
        }

        .gallery-item {
            position: relative;
            border-radius: 24px;
            overflow: hidden;
            background: #fff;
            aspect-ratio: 1/1;
            cursor: pointer;
            transition: 0.5s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid var(--card-border);
            box-shadow: 0 10px 30px rgba(0,0,0,0.03);
        }

        .gallery-item:hover {
            transform: translateY(-10px);
            border-color: var(--primary);
            box-shadow: 0 25px 50px rgba(0,0,0,0.08);
        }

        .gallery-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: 0.8s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .gallery-item:hover img {
            transform: scale(1.1);
            filter: brightness(0.8);
        }

        .gallery-item .overlay {
            position: absolute;
            inset: 0;
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
            padding: 30px;
            background: linear-gradient(to top, rgba(0,0,0,0.8) 0%, transparent 60%);
            opacity: 0;
            transition: 0.4s;
        }

        .gallery-item:hover .overlay {
            opacity: 1;
        }

        .gallery-item .overlay h4 {
            color: #fff;
            font-size: 1.3rem;
            margin-bottom: 5px;
            transform: translateY(20px);
            transition: 0.4s 0.1s;
            font-weight: 700;
        }

        .gallery-item:hover .overlay h4 {
            transform: translateY(0);
        }

        .gallery-item .overlay .category-pill {
            display: inline-block;
            background: var(--primary);
            color: #fff;
            padding: 5px 14px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1px;
            width: fit-content;
        }

        .gallery-item .view-icon {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) scale(0);
            background: #fff;
            color: var(--primary);
            width: 65px;
            height: 65px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.6rem;
            transition: 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            z-index: 2;
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }

        .gallery-item:hover .view-icon {
            transform: translate(-50%, -50%) scale(1);
        }

        /* Lightbox */
        .lightbox {
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.92);
            z-index: 9999;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 40px;
            backdrop-filter: blur(15px);
        }

        .lightbox.active { display: flex; }

        .lightbox-content {
            max-width: 90%;
            max-height: 90vh;
            position: relative;
        }

        .lightbox-content img {
            max-width: 100%;
            max-height: 90vh;
            border-radius: 15px;
            box-shadow: 0 0 60px rgba(0,0,0,0.4);
        }

        .lightbox-close {
            position: absolute;
            top: -50px;
            right: 0;
            color: #fff;
            font-size: 3rem;
            cursor: pointer;
            transition: 0.3s;
        }

        .lightbox-close:hover { color: var(--primary); transform: rotate(90deg); }

        .hidden { display: none; }
    </style>
</head>

<body>
    <!-- Header code... -->

    <!-- hero section -->
    <section class="page-hero">
        <ul class="circles">
            <?php for($i=0; $i<10; $i++) echo "<li></li>"; ?>
        </ul>
        <div class="page-title">
            <h1 style="color: #fff; font-size: 3.5rem; letter-spacing: 2px;">GALLERY</h1>
            <p>HOME / <b style="color: var(--primary);">GALLERY MOMENTS</b></p>
        </div>
    </section>

    <section class="gallery-section">
        <div class="container">
            <!-- Filter Buttons -->
            <div class="gallery-filter">
                <button class="filter-btn active" data-filter="all"><i class="fa-solid fa-layer-group"></i> All Photos</button>
                <button class="filter-btn" data-filter="seminar"><i class="fa-solid fa-microphone"></i> Seminar</button>
                <button class="filter-btn" data-filter="workshops"><i class="fa-solid fa-laptop-code"></i> Workshops</button>
                <button class="filter-btn" data-filter="session"><i class="fa-solid fa-users"></i> Sessions</button>
                <button class="filter-btn" data-filter="activities"><i class="fa-solid fa-gamepad"></i> Activities</button>
                <button class="filter-btn" data-filter="campus"><i class="fa-solid fa-school"></i> Campus</button>
            </div>

        <!-- Combined Gallery Grid (Static + Dynamic) -->
        <div class="gallery-grid-dynamic" id="galleryGrid">
            <!-- Static Photos (Always Shown) -->
            <div class="gallery-item" data-category="campus" onclick="openLightbox(this)">
                <div class="view-icon"><i class="fa-solid fa-expand"></i></div>
                <img src="images/netcoder classroom.jpg">
                <div class="overlay">
                    <span class="category-pill">Campus</span>
                    <h4>Classroom Environment</h4>
                </div>
            </div>
            <div class="gallery-item" data-category="campus" onclick="openLightbox(this)">
                <div class="view-icon"><i class="fa-solid fa-expand"></i></div>
                <img src="images/netcoder lab.jpg">
                <div class="overlay">
                    <span class="category-pill">Campus</span>
                    <h4>IT Lab Facility</h4>
                </div>
            </div>
            <div class="gallery-item" data-category="seminar" onclick="openLightbox(this)">
                <div class="view-icon"><i class="fa-solid fa-expand"></i></div>
                <img src="images/Event-Netcoder.jpg">
                <div class="overlay">
                    <span class="category-pill">Seminar</span>
                    <h4>Tech Events</h4>
                </div>
            </div>
            <div class="gallery-item" data-category="activities" onclick="openLightbox(this)">
                <div class="view-icon"><i class="fa-solid fa-expand"></i></div>
                <img src="images/Award- netcoder.jpg">
                <div class="overlay">
                    <span class="category-pill">Activities</span>
                    <h4>Award Ceremonies</h4>
                </div>
            </div>
            <div class="gallery-item" data-category="activities" onclick="openLightbox(this)">
                <div class="view-icon"><i class="fa-solid fa-expand"></i></div>
                <img src="images/Internship-Students.jpg">
                <div class="overlay">
                    <span class="category-pill">Activities</span>
                    <h4>Student Internships</h4>
                </div>
            </div>
            <div class="gallery-item" data-category="workshops" onclick="openLightbox(this)">
                <div class="view-icon"><i class="fa-solid fa-expand"></i></div>
                <img src="images/Workshop-Netcoder.jpg">
                <div class="overlay">
                    <span class="category-pill">Workshops</span>
                    <h4>Professional Workshops</h4>
                </div>
            </div>
            <div class="gallery-item" data-category="session" onclick="openLightbox(this)">
                <div class="view-icon"><i class="fa-solid fa-expand"></i></div>
                <img src="images/06.jpg">
                <div class="overlay">
                    <span class="category-pill">Session</span>
                    <h4>Training Sessions</h4>
                </div>
            </div>

            <!-- Dynamic Photos from Database -->
            <?php if($images_res && $images_res->num_rows > 0): ?>
                <?php while($img = $images_res->fetch_assoc()): ?>
                    <div class="gallery-item" data-category="<?php echo strtolower($img['category']); ?>" onclick="openLightbox(this)">
                        <div class="view-icon"><i class="fa-solid fa-expand"></i></div>
                        <img src="<?php echo $img['image_path']; ?>" alt="Gallery Image">
                        <div class="overlay">
                            <span class="category-pill"><?php echo $img['category']; ?></span>
                            <h4>Dynamic Upload</h4>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php endif; ?>
        </div>
    </section>

    <!-- Lightbox Modal -->
    <div class="lightbox" id="lightbox" onclick="closeLightbox()">
        <div class="lightbox-content" onclick="event.stopPropagation()">
            <span class="lightbox-close" onclick="closeLightbox()">&times;</span>
            <img id="lightboxImg" src="" alt="Preview">
        </div>
    </div>

    <footer>
        <!-- Footer content... -->
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
                filterBtns.forEach(b => b.classList.remove('active'));
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

        // Lightbox Logic
        function openLightbox(element) {
            const imgSrc = element.querySelector('img').src;
            document.getElementById('lightboxImg').src = imgSrc;
            document.getElementById('lightbox').classList.add('active');
            document.body.style.overflow = 'hidden'; // Stop scrolling
        }

        function closeLightbox() {
            document.getElementById('lightbox').classList.remove('active');
            document.body.style.overflow = 'auto'; // Restore scrolling
        }
    </script>
    <script src="main.js"></script>
</body>
</html>

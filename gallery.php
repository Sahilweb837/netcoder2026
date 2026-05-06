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
            --primary: #ff5532;
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
            padding: 50px 0;
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
            transform: translate(-50%, -50%) scale(1)  ;
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

        .single-delete { color: #dc2626; text-decoration: none; font-size: 1.1rem; transition: 0.3s; }
        .single-delete:hover { transform: scale(1.2); }

        /* Mobile Dropdown for Filter */
        .gallery-filter-mobile {
            display: none;
            width: 100%;
            margin-bottom: 30px;
        }
        .gallery-filter-mobile select {
            width: 100%;
            padding: 12px 20px;
            border-radius: 10px;
            border: 2px solid var(--primary);
            background: #fff;
            color: #333;
            font-weight: 600;
            outline: none;
            cursor: pointer;
        }

        @media (max-width: 768px) {
            .gallery-filter { display: none; }
            .gallery-filter-mobile { display: block; }
        }

        .hidden { display: none; }
    </style>
</head>

<body>
    <header class="main-header">
        <nav class="main-nav">

            <!-- LOGO -->
            <div class="brand-logo">
                <a href="index.php">
                    <img src="./images/logo.png">
                </a>
            </div>

            <!-- MENU -->
            <div class="menu-wrapper">

                <!-- ON CAMPUS -->
                <div class="menu-item">
                    <div class="menu-link">
                        <i class="fa-solid fa-building-columns"></i>
                        On-Campus Courses <span class="icon-arrow"></span>
                    </div>

                    <div class="mega-box">
                        <div class="mega-grid">

                            <!-- COL 1 -->
                            <div class="mega-col">
                                <h4>Design & Multimedia Courses</h4>
                                <a href="foundation-graphic.php">Graphic Designing</a>
                                <a href="web-designing.php">Web Designing</a>
                                <a href="ui&ux.php">UI & UX Design</a>
                                <a href="animation.php">2D/3D Animation</a>
                                <a href="motion-graphics.php">Motion Graphics</a>
                                <a href="graphic-and-web-designing.php">Graphics & Web Designing</a>
                                <a href="digital-content-creator.php">Digital Content Creator</a>
                                <a href="autocad.php">Auto CAD</a>
                            </div>

                            <!-- COL 2 -->
                            <div class="mega-col">
                                <h4>CMS & Web Technologies Courses</h4>
                                <a href="web-development.php">Web Development</a>
                                <a href="fullstack-web-development.php">Full Stack Development</a>
                                <a href="mern-stack.php">MERN Stack</a>
                                <a href="mean-stack.php">MEAN Stack</a>
                                <a href="php-training.php">PHP Training</a>
                                <a href="wordpress.php">WordPress</a>

                                <h4>Digital Marketing Courses</h4>
                                <a href="digital-marketing.php">Digital Marketing</a>
                                <a href="social-media-marketing.php">Social Media Marketing</a>
                                <a href="seo-course.php">SEO Course</a>
                            </div>

                            <!-- COL 3 -->
                            <div class="mega-col">
                                <h4>Professional Training Courses</h4>
                                <a href="business-analytics.php">Data Science & Business Analytics</a>
                                <a href="machine-learning.php">Data Science & Machine Learning</a>
                                <a href="data-analytics.php">Data Analytics</a>
                                <a href="cyber-security.php">Complete Cyber Security Course</a>
                                <a href="ethical-hacking.php">Ethical Hacking</a>
                                <a href="software-engineering.php">Software Engineering With Python</a>

                                <h4>Additional Courses</h4>
                                <a href="system-design.php">System Design & Operating Systems</a>
                                <a href="data-structures.php">Algorithm & Data Structures In Python</a>
                                <a href="devops-course.php">DevOps Course</a>

                            </div>



                        </div>
                    </div>
                </div>

                <!-- ONLINE -->
                <div class="menu-item">
                    <div class="menu-link">
                        <i class="fa-solid fa-laptop"></i>
                        Online Courses<span class="icon-arrow"></span>
                    </div>

                    <div class="mega-box small">
                        <h4 class="underlineheading">Online Professional Courses</h4>

                        <div class="mega-grid small-grid">

                            <div class="mega-col">
                                <a href="web&app-online.php">Full Stack & Apps</a>
                                <a href="digital-marketing-online.php">Digital Marketing</a>
                                <a href="data-science-online.php">Data Science</a>
                            </div>

                            <div class="mega-col">
                                <a href="data-analytics-online.php">Data Analytics</a>
                                <a href="Ethical-hacking-online.php">Ethical Hacking</a>
                            </div>

                        </div>
                    </div>
                </div>

                <a href="services.php" class="menu-link">Services</a>
            </div>

            <!-- RIGHT -->
            <div class="right-menu">
                <div class="menu-toggle" id="openMenu">
                    <span></span><span></span><span></span>
                </div>
                <a href="contact.php" class="color-btn">Contact</a>
            </div>

        </nav>
    </header>
    <!-- side menu -->
    <div class="side-menu" id="mobileMenu">

        <div class="close-icon" id="closeMenu">✕</div>

        <div class="side-links">

            <a href="index.php">Home</a>
            <a href="about.php">About</a>

            <div class="side-item course-campus">
                <div class="menu-link">
                    <i class="fa-solid fa-building-columns"></i>
                    On-Campus Courses <span class="icon-arrow"></span>
                </div>

                <!-- side dropdom my all courses are not visible  -->
                <div class="side-dropdown">

                    <!-- COL 1 -->
                    <div class="side-col">
                        <h4>Design & Multimedia</h4>
                        <a href="foundation-graphic.php">Graphic Designing</a>
                        <a href="web-designing.php">Web Designing</a>
                        <a href="ui&ux.php">UI & UX Design</a>
                        <a href="animation.php">2D/3D Animation</a>
                        <a href="motion-graphics.php">Motion Graphics</a>
                        <a href="graphic-and-web-designing.php">Graphics & Web Designing</a>
                        <a href="digital-content-creator.php">Digital Content Creator</a>
                        <a href="autocad.php">Auto Cad</a>
                    </div>

                    <!-- COL 2 -->
                    <div class="mega-col">
                        <h4>CMS & Web Tech</h4>
                        <a href="web-development.php">Web Development</a>
                        <a href="fullstack-web-development.php">Full Stack Development</a>
                        <a href="mern-stack.php">MERN Stack</a>
                        <a href="mean-stack.php">MEAN Stack</a>
                        <a href="php-training.php">PHP Training</a>
                        <a href="wordpress.php">WordPress</a>
                    </div>

                    <!-- COL 3 -->
                    <div class="mega-col">
                        <h4>Professional Courses</h4>
                        <a href="business-analytics.php">Data Science & Business Analytics</a>
                        <a href="machine-learning.php">Data Science & Machine Learning</a>
                                <a href="data-analytics.php">Data Analytics</a>
                        <a href="cyber-security.php">Complete Cyber Security Course</a>
                        <a href="ethical-hacking.php">Ethical Hacking</a>
                        <a href="software-engineering.php">Software Engineering With Python</a>
                    </div>

                    <!-- COL 4 -->
                    <div class="mega-col">
                        <h4 class="bottomh4">Additional Courses</h4>
                        <a href="system-design.php">System Design & Operating Systems</a>
                        <a href="data-structures.php">Algorithm & Data Structures In Python</a>
                        <a href="devops-course.php">DevOps Course</a>


                        <h4 class="mt">Digital Marketing</h4>
                        <a href="digital-marketing.php">Digital Marketing</a>
                        <a href="social-media-marketing.php">Social Media Marketing</a>
                        <a href="seo-course.php">SEO Course</a>
                    </div>

                </div>
            </div>
            <!-- ONLINE -->
            <div class="side-item course-online">
                <div class="side-title"><i class="fa-solid fa-laptop"></i>
                    Online Courses<span class="icon-arrow"></span></div>
                <div class="side-dropdown">

                      <a href="web&app-online.php">Full Stack & Apps</a>
                    <a href="digital-marketing-online.php">Digital Marketing</a>
                    <a href="data-science-online.php">Data Science</a>
                    <a href="data-analytics-online.php">Data Analytics</a>
                    <a href="Ethical-hacking-online.php">Ethical Hacking</a>
                </div>
            </div>
            <a href="industrial-training.php">Industrial Training</a>

            <a href="gallery.php">Gallery</a>
            <a href="blog.php">Blog</a>
            <a href="career.php">Career</a>

            <a href="contact.php">Contact</a>

        </div>
    </div>

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

            <!-- Mobile Filter Dropdown -->
            <div class="gallery-filter-mobile">
                <select id="mobileFilter">
                    <option value="all">All Photos</option>
                    <option value="seminar">Seminar</option>
                    <option value="workshops">Workshops</option>
                    <option value="session">Sessions</option>
                    <option value="activities">Activities</option>
                    <option value="campus">Campus</option>
                </select>
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
                            <h4><?php echo htmlspecialchars($img['category']); ?> Moment</h4>
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

 <!-- footer -->
    <footer>
        <div class="container">
            <div class="newsletter">
                <h2><span>Keep Up With Our Latest Updates</span></h2>
                <p>Stay connected with our latest news and updates. Be the
                    first to know about new courses,
                    exclusive
                    offers, and exciting announcements by subscribing to our
                    newsletter.</p>
                <form action>
                    <input type="email" name="newsletter" id="newsletter" placeholder="Email Address">
                    <input type="submit" value="Subscribe">
                </form>
            </div>
            <!-- footer quick links -->
            <div class="foot-links">
                <ul>
                    <li>
                        <h5>Quick Links</h5>
                    </li>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="gallery.php">Gallery</a></li>
                    <li><a href="career.php">Carrer</a></li>
                    <li><a href="privacy-policy.php">Privacy & Policy</a></li>
                </ul>
                <!--  -->
                <ul>
                    <li>
                        <h5>Top Courses</h5>
                    </li>
                    <li><a href="fullstack-web-development.php">Full Stack
                            Development</a></li>
                    <li><a href="graphic-and-web-designing.php">Graphics &
                            Web Designing</a></li>
                    <li><a href="mern-stack.php">MERN Stack</a></li>
                    <li><a href="mean-stack.php">MEAN Stack</a></li>
                    <li><a href="software-engineering.php">Python
                            Course</a></li>
                </ul>
                <!--  -->
                <ul>
                    <li>
                        <h5>Features</h5>
                    </li>
                    <li><a href="foundation-graphic.php">Graphic
                            Designing</a></li>
                    <li><a href="animation.php">Animation</a></li>
                    <li><a href="ui&ux.php">UI & UX Design</a></li>
                    <li><a href="digital-marketing.php">Digital
                            Marketing</a></li>
                    <li><a href="digital-content-creator.php">Content
                            Creation</a></li>
                </ul>
                <!--  -->
                <!--  -->
                <ul>
                    <li>
                        <h5>Professional Training</h5>
                    </li>
                    <li><a href="machine-learning.php">Data Science &
                            Machine Learning</a></li>
                    <li><a href="business-analytics.php">Data Science &
                            Business Analytics</a></li>
                    <li><a href="system-design.php">System Design &
                            Operating System</a></li>
                    <li><a href="cyber-security.php">Cyber
                            Security</a></li>
                    <li><a href="ethical-hacking.php">Ethical
                            Hacking</a></li>
                </ul>
                <!--  -->
            </div>
            <!-- footer quick links end -->
            <!--  -->
        </div>
        <div class="copyright">
            <div class="container">
                <p>Copyright &copy;2026 All rights reserved by &hearts; <a href="index.php">Netcoder Technology</a></p>
                <div class="social-icons">
                    <a href="https://www.facebook.com/netcodertechnology/">
                        <svg width="20" height="20" viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M36.2008 63.8002H28.4008C26.4008 63.8002 24.8008 62.1002 24.8008 60.0002V36.2002H18.4008C16.4008 36.2002 14.8008 34.5002 14.8008 32.4002V25.5002C14.8008 23.4002 16.4008 21.7002 18.4008 21.7002H24.6008V15.4002C24.6008 6.30019 30.0008 0.200195 38.0008 0.200195H44.0008C46.0008 0.200195 47.6008 1.9002 47.6008 4.0002V12.1002C47.6008 14.2002 46.0008 15.9002 44.0008 15.9002H39.9008C39.8008 15.9002 39.8008 15.9002 39.7008 15.9002C39.7008 16.0002 39.7008 16.1002 39.7008 16.2002V21.6002H45.4008C46.6008 21.7002 47.6008 22.2002 48.3008 23.0002C49.0008 23.9002 49.3008 25.1002 49.1008 26.2002L47.9008 33.0002C47.7008 34.8002 46.2008 36.1002 44.3008 36.1002H39.7008V60.0002C39.7008 62.0002 38.1008 63.8002 36.2008 63.8002ZM26.5008 32.7002C27.5008 32.7002 28.3008 33.5002 28.3008 34.5002V60.0002C28.3008 60.2002 28.4008 60.3002 28.4008 60.3002H36.2008C36.2008 60.3002 36.3008 60.2002 36.3008 60.0002V34.3002C36.3008 33.3002 37.1008 32.5002 38.1008 32.5002H44.4008C44.4008 32.5002 44.5008 32.5002 44.5008 32.4002V32.3002L45.7008 25.6002C45.7008 25.4002 45.7008 25.3002 45.6008 25.2002C45.6008 25.2002 45.5008 25.1002 45.4008 25.1002H38.0008C37.0008 25.1002 36.2008 24.3002 36.2008 23.3002V16.2002C36.2008 14.4002 36.5008 12.4002 39.9008 12.4002H44.0008C44.0008 12.4002 44.1008 12.3002 44.1008 12.1002V4.1002C44.1008 3.9002 44.0008 3.8002 44.0008 3.8002H38.1008C32.1008 3.8002 28.2008 8.4002 28.2008 15.5002V23.6002C28.2008 24.6002 27.4008 25.4002 26.4008 25.4002H18.4008C18.4008 25.4002 18.3008 25.5002 18.3008 25.7002V32.6002C18.3008 32.8002 18.4008 32.9002 18.4008 32.9002L26.5008 32.7002Z" />
                        </svg>
                    </a>
                    <a href="https://www.instagram.com/netcodertechnology/">
                        <svg width="20" height="20" viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M32.0016 17.5996C24.1016 17.5996 17.6016 23.9996 17.6016 31.9996C17.6016 39.8996 24.0016 46.3996 32.0016 46.3996C39.9016 46.3996 46.3016 39.9996 46.3016 31.9996C46.3016 24.0996 39.9016 17.5996 32.0016 17.5996ZM32.0016 41.8996C26.6016 41.8996 22.1016 37.4996 22.1016 31.9996C22.1016 26.5996 26.5016 22.0996 32.0016 22.0996C37.4016 22.0996 41.8016 26.4996 41.8016 31.9996C41.8016 37.3996 37.4016 41.8996 32.0016 41.8996Z" />
                            <path
                                d="M47 11.5996C45 11.5996 43.5 13.1996 43.5 15.0996C43.5 16.9996 45.1 18.5996 47 18.5996C48.9 18.5996 50.5 16.9996 50.5 15.0996C50.5 13.1996 49 11.5996 47 11.5996Z" />
                            <path
                                d="M46.9008 1.7998H17.1008C8.60078 1.7998 1.80078 8.5998 1.80078 17.0998V46.9998C1.80078 55.3998 8.70078 62.2998 17.1008 62.2998H47.0008C55.4008 62.2998 62.3008 55.3998 62.3008 46.9998V17.0998C62.3008 8.5998 55.4008 1.7998 46.9008 1.7998ZM57.8008 46.8998C57.8008 52.8998 53.0008 57.6998 47.0008 57.6998H17.1008C11.1008 57.6998 6.30078 52.8998 6.30078 46.8998V17.0998C6.30078 11.0998 11.2008 6.2998 17.1008 6.2998H46.9008C52.9008 6.2998 57.7008 11.1998 57.7008 17.0998V46.8998H57.8008Z" />
                        </svg>
                    </a>
                    <a href="https://www.youtube.com/@netcodertechnology">
                        <svg width="22" height="16" viewBox="0 0 22 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M1 8.14286C1 6.73619 1.06762 5.40952 1.15619 4.29714C1.28952 2.61714 2.62095 1.32571 4.3019 1.20857C5.91714 1.09524 8.12381 1 11 1C13.8762 1 16.0829 1.09524 17.6981 1.20857C19.379 1.32571 20.7105 2.6181 20.8438 4.29714C20.9324 5.41048 21 6.73524 21 8.14286C21 9.6 20.9276 10.9705 20.8343 12.1076C20.7772 12.8843 20.4401 13.6139 19.8854 14.1606C19.3308 14.7074 18.5965 15.0341 17.819 15.08C16.1876 15.1914 13.9048 15.2857 11 15.2857C8.09524 15.2857 5.81238 15.1914 4.18095 15.08C3.40351 15.0341 2.66919 14.7074 2.11457 14.1606C1.55994 13.6139 1.22276 12.8843 1.16571 12.1076C1.05716 10.7888 1.00188 9.46614 1 8.14286Z"
                                stroke-linejoin="round" />
                            <path d="M9.0957 10.9994V5.28516L14.3338 8.1423L9.0957 10.9994Z" stroke-linejoin="round" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </footer>

    <script src="main.js"></script>

    <script>
        // Filtering Logic
        const filterBtns = document.querySelectorAll('.filter-btn');
        const galleryItems = document.querySelectorAll('.gallery-item');

        // Dropdown filter for mobile
        const mobileFilter = document.getElementById('mobileFilter');
        if(mobileFilter){
            mobileFilter.addEventListener('change', function(){
                const filter = this.value;
                filterGallery(filter);
            });
        }

        function filterGallery(filter){
            galleryItems.forEach(item => {
                if (filter === 'all' || item.getAttribute('data-category') === filter) {
                    item.classList.remove('hidden');
                } else {
                    item.classList.add('hidden');
                }
            });
        }

        filterBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                filterBtns.forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                const filterValue = btn.getAttribute('data-filter');
                filterGallery(filterValue);
            });
        });

        // Lightbox Logic
        function openLightbox(element) {
            const imgSrc = element.querySelector('img').src;
            const lightbox = document.getElementById('lightbox');
            const lightboxImg = document.getElementById('lightboxImg');
            
            lightboxImg.src = imgSrc;
            lightbox.classList.add('active');
            document.body.style.overflow = 'hidden'; // Stop scrolling
        }

        function closeLightbox() {
            document.getElementById('lightbox').classList.remove('active');
            document.body.style.overflow = 'auto'; // Restore scrolling
        }

        // Close lightbox on escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') closeLightbox();
        });
    </script>
</body>
</html>

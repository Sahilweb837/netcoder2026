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

        /* Footer Custom Styles */
        footer {
            background: #111 !important;
            color: #fff !important;
            padding: 80px 0 0 !important;
            margin-top: 100px !important;
        }

        .newsletter {
            text-align: center;
            margin-bottom: 60px;
            padding-bottom: 60px;
            border-bottom: 1px solid #222;
        }

        .newsletter h2 { font-size: 2.2rem; margin-bottom: 15px; color: #fff !important; font-weight: 700; }
        .newsletter h2 span { color: var(--primary) !important; }
        .newsletter p { color: #888 !important; margin-bottom: 30px; max-width: 650px; margin-left: auto; margin-right: auto; line-height: 1.6; }
        
        .newsletter form { display: flex; max-width: 550px; margin: 0 auto; gap: 10px; }
        .newsletter input[type="email"] { background: #1a1a1a !important; border: 1px solid #333 !important; color: #fff !important; border-radius: 12px !important; padding: 15px 25px !important; flex: 1; outline: none; }
        .newsletter input[type="submit"] { background: var(--primary) !important; color: #fff !important; border: none !important; padding: 15px 35px !important; border-radius: 12px !important; font-weight: 700 !important; cursor: pointer; transition: 0.3s !important; }
        .newsletter input[type="submit"]:hover { background: #e65c00 !important; transform: translateY(-3px) !important; box-shadow: 0 10px 20px rgba(255,102,0,0.2) !important; }

        .foot-links {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 40px;
            padding-bottom: 60px;
        }

        .foot-links h5 { color: #fff !important; font-size: 1.2rem !important; margin-bottom: 30px !important; font-weight: 700 !important; position: relative; }
        .foot-links h5::after { content: ''; position: absolute; bottom: -10px; left: 0; width: 40px; height: 3px; background: var(--primary); border-radius: 2px; }
        .foot-links ul { list-style: none !important; padding: 0 !important; }
        .foot-links li { margin-bottom: 15px !important; }
        .foot-links a { color: #aaa !important; text-decoration: none !important; transition: 0.3s !important; font-size: 0.95rem !important; font-weight: 500 !important; }
        .foot-links a:hover { color: var(--primary) !important; padding-left: 8px !important; }

        .copyright {
            background: #0a0a0a !important;
            padding: 30px 0 !important;
            border-top: 1px solid #222;
        }

        .copyright .container {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .copyright p { color: #666 !important; font-size: 0.9rem !important; }
        .copyright a { color: var(--primary) !important; text-decoration: none !important; font-weight: 700 !important; }

        .social-icons { display: flex; gap: 15px; }
        .social-icons a { width: 42px; height: 42px; background: #1a1a1a; display: flex; align-items: center; justify-content: center; border-radius: 12px; transition: 0.3s; border: 1px solid #222; }
        .social-icons a:hover { background: var(--primary); transform: translateY(-5px); border-color: var(--primary); }
        .social-icons svg { fill: #fff !important; width: 20px; height: 20px; }

        @media (max-width: 768px) {
            .newsletter h2 { font-size: 1.8rem; }
            .newsletter form { flex-direction: column; }
            .copyright .container { flex-direction: column; gap: 25px; text-align: center; }
            .foot-links { text-align: center; }
            .foot-links h5::after { left: 50%; transform: translateX(-50%); }
            .social-icons { justify-content: center; }
        }
    </style>
</head>

<body>
    <header class="main-header">
        <nav class="main-nav">

            <!-- LOGO -->
            <div class="brand-logo">
                <a href="index.html">
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
                                <a href="foundation-graphic.html">Graphic Designing</a>
                                <a href="web-designing.html">Web Designing</a>
                                <a href="ui&ux.html">UI & UX Design</a>
                                <a href="animation.html">2D/3D Animation</a>
                                <a href="motion-graphics.html">Motion Graphics</a>
                                <a href="graphic-and-web-designing.html">Graphics & Web Designing</a>
                                <a href="digital-content-creator.html">Digital Content Creator</a>
                                <a href="autocad.html">Auto CAD</a>
                            </div>

                            <!-- COL 2 -->
                            <div class="mega-col">
                                <h4>CMS & Web Technologies Courses</h4>
                                <a href="web-development.html">Web Development</a>
                                <a href="fullstack-web-development.html">Full Stack Development</a>
                                <a href="mern-stack.html">MERN Stack</a>
                                <a href="mean-stack.html">MEAN Stack</a>
                                <a href="php-training.html">PHP Training</a>
                                <a href="wordpress.html">WordPress</a>

                                <h4>Digital Marketing Courses</h4>
                                <a href="digital-marketing.html">Digital Marketing</a>
                                <a href="social-media-marketing.html">Social Media Marketing</a>
                                <a href="seo-course.html">SEO Course</a>
                            </div>

                            <!-- COL 3 -->
                            <div class="mega-col">
                                <h4>Professional Training Courses</h4>
                                <a href="business-analytics.html">Data Science & Business Analytics</a>
                                <a href="machine-learning.html">Data Science & Machine Learning</a>
                                <a href="data-analytics.html">Data Analytics</a>
                                <a href="cyber-security.html">Complete Cyber Security Course</a>
                                <a href="ethical-hacking.html">Ethical Hacking</a>
                                <a href="software-engineering.html">Software Engineering With Python</a>

                                <h4>Additional Courses</h4>
                                <a href="system-design.html">System Design & Operating Systems</a>
                                <a href="data-structures.html">Algorithm & Data Structures In Python</a>
                                <a href="devops-course.html">DevOps Course</a>

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
                                <a href="web&app-online.html">Full Stack & Apps</a>
                                <a href="digital-marketing-online.html">Digital Marketing</a>
                                <a href="data-science-online.html">Data Science</a>
                            </div>

                            <div class="mega-col">
                                <a href="data-analytics-online.html">Data Analytics</a>
                                <a href="Ethical-hacking-online.html">Ethical Hacking</a>
                            </div>

                        </div>
                    </div>
                </div>

                <a href="services.html" class="menu-link">Services</a>
            </div>

            <!-- RIGHT -->
            <div class="right-menu">
                <div class="menu-toggle" id="openMenu">
                    <span></span><span></span><span></span>
                </div>
                <a href="contact.html" class="color-btn">Contact</a>
            </div>

        </nav>
    </header>
    <!-- side menu -->
    <div class="side-menu" id="mobileMenu">

        <div class="close-icon" id="closeMenu">✕</div>

        <div class="side-links">

            <a href="index.html">Home</a>
            <a href="about.html">About</a>

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
                        <a href="foundation-graphic.html">Graphic Designing</a>
                        <a href="web-designing.html">Web Designing</a>
                        <a href="ui&ux.html">UI & UX Design</a>
                        <a href="animation.html">2D/3D Animation</a>
                        <a href="motion-graphics.html">Motion Graphics</a>
                        <a href="graphic-and-web-designing.html">Graphics & Web Designing</a>
                        <a href="digital-content-creator.html">Digital Content Creator</a>
                        <a href="autocad.html">Auto Cad</a>
                    </div>

                    <!-- COL 2 -->
                    <div class="mega-col">
                        <h4>CMS & Web Tech</h4>
                        <a href="web-development.html">Web Development</a>
                        <a href="fullstack-web-development.html">Full Stack Development</a>
                        <a href="mern-stack.html">MERN Stack</a>
                        <a href="mean-stack.html">MEAN Stack</a>
                        <a href="php-training.html">PHP Training</a>
                        <a href="wordpress.html">WordPress</a>
                    </div>

                    <!-- COL 3 -->
                    <div class="mega-col">
                        <h4>Professional Courses</h4>
                        <a href="business-analytics.html">Data Science & Business Analytics</a>
                        <a href="machine-learning.html">Data Science & Machine Learning</a>
                                <a href="data-analytics.html">Data Analytics</a>
                        <a href="cyber-security.html">Complete Cyber Security Course</a>
                        <a href="ethical-hacking.html">Ethical Hacking</a>
                        <a href="software-engineering.html">Software Engineering With Python</a>
                    </div>

                    <!-- COL 4 -->
                    <div class="mega-col">
                        <h4 class="bottomh4">Additional Courses</h4>
                        <a href="system-design.html">System Design & Operating Systems</a>
                        <a href="data-structures.html">Algorithm & Data Structures In Python</a>
                        <a href="devops-course.html">DevOps Course</a>


                        <h4 class="mt">Digital Marketing</h4>
                        <a href="digital-marketing.html">Digital Marketing</a>
                        <a href="social-media-marketing.html">Social Media Marketing</a>
                        <a href="seo-course.html">SEO Course</a>
                    </div>

                </div>
            </div>
            <!-- ONLINE -->
            <div class="side-item course-online">
                <div class="side-title"><i class="fa-solid fa-laptop"></i>
                    Online Courses<span class="icon-arrow"></span></div>
                <div class="side-dropdown">

                      <a href="web&app-online.html">Full Stack & Apps</a>
                    <a href="digital-marketing-online.html">Digital Marketing</a>
                    <a href="data-science-online.html">Data Science</a>
                    <a href="data-analytics-online.html">Data Analytics</a>
                    <a href="Ethical-hacking-online.html">Ethical Hacking</a>
                </div>
            </div>
            <a href="industrial-training.html">Industrial Training</a>

            <a href="gallery.php">Gallery</a>
            <a href="blog.php">Blog</a>
            <a href="career.html">Career</a>

            <a href="contact.html">Contact</a>

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
                    <li><a href="index.html">Home</a></li>
                    <li><a href="gallery.php">Gallery</a></li>
                    <li><a href="career.html">Carrer</a></li>
                    <li><a href="privacy-policy.html">Privacy & Policy</a></li>
                </ul>
                <!--  -->
                <ul>
                    <li>
                        <h5>Top Courses</h5>
                    </li>
                    <li><a href="fullstack-web-development.html">Full Stack
                            Development</a></li>
                    <li><a href="graphic-and-web-designing.html">Graphics &
                            Web Designing</a></li>
                    <li><a href="mern-stack.html">MERN Stack</a></li>
                    <li><a href="mean-stack.html">MEAN Stack</a></li>
                    <li><a href="software-engineering.html">Python
                            Course</a></li>
                </ul>
                <!--  -->
                <ul>
                    <li>
                        <h5>Features</h5>
                    </li>
                    <li><a href="foundation-graphic.html">Graphic
                            Designing</a></li>
                    <li><a href="animation.html">Animation</a></li>
                    <li><a href="ui&ux.html">UI & UX Design</a></li>
                    <li><a href="digital-marketing.html">Digital
                            Marketing</a></li>
                    <li><a href="digital-content-creator.html">Content
                            Creation</a></li>
                </ul>
                <!--  -->
                <!--  -->
                <ul>
                    <li>
                        <h5>Professional Training</h5>
                    </li>
                    <li><a href="machine-learning.html">Data Science &
                            Machine Learning</a></li>
                    <li><a href="business-analytics.html">Data Science &
                            Business Analytics</a></li>
                    <li><a href="system-design.html">System Design &
                            Operating System</a></li>
                    <li><a href="cyber-security.html">Cyber
                            Security</a></li>
                    <li><a href="ethical-hacking.html">Ethical
                            Hacking</a></li>
                </ul>
                <!--  -->
            </div>
            <!-- footer quick links end -->
            <!--  -->
        </div>
        <div class="copyright">
            <div class="container">
                <p>Copyright &copy;2026 All rights reserved by &hearts; <a href="index.html">Netcoder Technology</a></p>
                <div class="social-icons">
                    <a href="https://www.facebook.com/netcodertechnology/">
                        <svg width="20" height="20" viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M36.2008 63.8002H28.4008C26.4008 63.8002 24.8008 62.1002 24.8008 60.0002V36.2002H18.4008C16.4008 36.2002 14.8008 34.5002 14.8008 32.4002V25.5002C14.8008 23.4002 16.4008 21.7002 18.4008 21.7002H24.6008V15.4002C24.6008 6.30019 30.0008 0.200195 38.0008 0.200195H44.0008C46.0008 0.200195 47.6008 1.9002 47.6008 4.0002V12.1002C47.6008 14.2002 46.0008 15.9002 44.0008 15.9002H39.9008C39.8008 15.9002 39.8008 15.9002 39.7008 15.9002C39.7008 16.0002 39.7008 16.1002 39.7008 16.2002V21.6002H45.4008C46.6008 21.7002 47.6008 22.2002 48.3008 23.0002C49.0008 23.9002 49.3008 25.1002 49.1008 26.2002L47.9008 33.0002C47.7008 34.8002 46.2008 36.1002 44.3008 36.1002H39.7008V60.0002C39.7008 62.0002 38.1008 63.8002 36.2008 63.8002ZM26.5008 32.7002C27.5008 32.7002 28.3008 33.5002 28.3008 34.5002V60.0002C28.3008 60.2002 28.4008 60.3002 28.4008 60.3002H36.2008C36.2008 60.3002 36.3008 60.2002 36.3008 60.0002V34.3002C36.3008 33.3002 37.1008 32.5002 38.1008 32.5002H44.4008C44.4008 32.5002 44.5008 32.5002 44.5008 32.4002V32.3002L45.7008 25.6002C45.7008 25.4002 45.7008 25.3002 45.6008 25.2002C45.6008 25.2002 45.5008 25.1002 45.4008 25.1002H38.0008C37.0008 25.1002 36.2008 24.3002 36.2008 23.3002V16.2002C36.2008 14.4002 36.5008 12.4002 39.9008 12.4002H44.0008C44.0008 12.4002 44.1008 12.3002 44.1008 12.1002V4.1002C44.1008 3.9002 44.0008 3.8002 44.0008 3.8002H38.1008C32.1008 3.8002 28.2008 8.4002 28.2008 15.5002V23.6002C28.2008 24.6002 27.4008 25.4002 26.4008 25.4002H18.4008C18.4008 25.4002 18.3008 25.5002 18.3008 25.7002V32.6002C18.3008 32.8002 18.4008 32.9002 18.4008 32.9002L26.5008 32.7002Z" />
                    </svg>
                </div>
            </div>
        </div>
    </footer>

    <script src="main.js"></script>

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

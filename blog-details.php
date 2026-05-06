 <?php
require_once 'config.php';

// 1. Get the slug from the URL
if (isset($_GET['slug'])) {
    $slug = $conn->real_escape_string($_GET['slug']);

    // 2. Fetch Main Blog Post
    $sql = "SELECT * FROM blogs WHERE slug = '$slug'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $blog = $result->fetch_assoc();
        $blog_id = $blog['id'];

        // 3. Fetch Additional Content Sections
        $section_sql = "SELECT * FROM blog_sections WHERE blog_id = $blog_id";
        $sections = $conn->query($section_sql);
    } else {
        // Redirect to blog list if slug not found
        header("Location: blog.php");
        exit();
    }
} else {
    header("Location: blog.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($blog['title']); ?> | Netcoder Technology</title>
    <meta name="description" content="<?php echo htmlspecialchars($blog['excerpt']); ?>">
     <!-- font awesomme linked her -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="shortcut icon" href="images/net-coder-logo icon.png">
    <link rel="stylesheet" href="style.css"> <style>
        /* --- Blog Detail Specific Styles --- */
        .blog-detail-page {
            background-color: #f4f7f6;
            padding: 60px 0;
            min-height: 100vh;
        }

        .blog-content-wrapper {
            max-width: 850px;
            margin: 0 auto;
            background: #fff;
            padding: 50px;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.04);
        }

        /* Back Button */
        .back-btn {
            display: inline-flex;
            align-items: center;
            text-decoration: none;
            color: #666;
            font-weight: 500;
            margin-bottom: 35px;
            transition: all 0.3s ease;
            background: #fff;
            padding: 10px 20px;
            border-radius: 50px;
            border: 1px solid #eee;
            font-size: 0.95rem;
        }
        .back-btn:hover {
            background: #ff5532;
            color: #fff;
            border-color: #ff5532;
            transform: translateX(-5px);
            box-shadow: 0 5px 15px rgba(255, 85, 50, 0.2);
        }
        .back-btn svg { margin-right: 10px; }

        /* Blog Header */
        .article-header {
            margin-bottom: 30px;
        }
        .article-header h1 {
            font-size: 2.8rem;
            color: #1a1a1a;
            margin-bottom: 20px;
            line-height: 1.2;
            font-weight: 800;
            letter-spacing: -0.5px;
        }
        .article-meta {
            color: #888;
            font-size: 0.9rem;
            margin-bottom: 0;
            padding-bottom: 25px;
            border-bottom: 1px solid #f0f0f0;
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }
        .article-meta span { display: inline-flex; align-items: center; }
        .article-meta svg { width: 16px; height: 16px; margin-right: 8px; color: #ff5532; }

        /* Images */
        .main-featured-image {
            width: 100%;
            height: auto;
            max-height: 550px;
            object-fit: cover;
            border-radius: 12px;
            margin-bottom: 40px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.08);
        }

        /* Content Typography */
        .article-body {
            font-size: 1.15rem;
            line-height: 1.9;
            color: #333;
            text-align: justify;
            text-justify: inter-word;
        }
        .article-body p { margin-bottom: 25px; }
        
        /* Dynamic Sections */
        .content-block {
            margin-top: 60px;
            padding-top: 40px;
            border-top: 1px solid #f0f0f0;
        }
        .content-block h2 {
            color: #1a1a1a;
            font-size: 2.2rem;
            margin-bottom: 25px;
            font-weight: 700;
            line-height: 1.3;
        }
        .section-image-wrapper {
            margin: 35px 0;
            text-align: center;
        }
        .section-image {
            width: 100%;
            max-height: 500px;
            object-fit: cover;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.06);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .blog-detail-page { padding: 30px 0; }
            .blog-content-wrapper { padding: 30px 20px; border-radius: 0; }
            .article-header h1 { font-size: 2rem; }
            .article-body { text-align: left; font-size: 1.05rem; }
            .content-block h2 { font-size: 1.8rem; }
        }
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


    <div class="blog-detail-page">
        <div class="container">
            <div class="blog-content-wrapper">
                
                <a href="blog.php" class="back-btn">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="19" y1="12" x2="5" y2="12"></line>
                        <polyline points="12 19 5 12 12 5"></polyline>
                    </svg>
                    Back to All Blogs
                </a>

                <article>
                    <header class="article-header">
                        <h1><?php echo htmlspecialchars($blog['title']); ?></h1>
                        
                        <div class="article-meta">
                            <span>
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                                <?php echo htmlspecialchars($blog['author']); ?>
                            </span>
                            <span>
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                                <?php echo date('F d, Y', strtotime($blog['date_posted'])); ?>
                            </span>
                            <?php if(!empty($blog['tags'])): ?>
                            <span>
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path><line x1="7" y1="7" x2="7.01" y2="7"></line></svg>
                                <?php echo htmlspecialchars($blog['tags']); ?>
                            </span>
                            <?php endif; ?>
                        </div>
                    </header>

                    <?php if (!empty($blog['main_image'])): ?>
                        <img src="<?php echo htmlspecialchars($blog['main_image']); ?>" alt="<?php echo htmlspecialchars($blog['title']); ?>" class="main-featured-image">
                    <?php endif; ?>

                    <div class="article-body">
                        <?php echo nl2br(trim($blog['main_content'])); ?>
                    </div>

                    <?php while($sec = $sections->fetch_assoc()): ?>
                        <div class="content-block">
                            <?php if (!empty($sec['section_title'])): ?>
                                <h2><?php echo htmlspecialchars($sec['section_title']); ?></h2>
                            <?php endif; ?>

                            <?php if (!empty($sec['section_image'])): ?>
                                <div class="section-image-wrapper">
                                    <img src="<?php echo htmlspecialchars($sec['section_image']); ?>" alt="Section visual" class="section-image">
                                </div>
                            <?php endif; ?>

                            <?php if (!empty(trim($sec['section_content']))): ?>
                                <div class="article-body">
                                    <?php echo nl2br(htmlspecialchars(trim($sec['section_content']))); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endwhile; ?>

                </article>
            </div>
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
</body>
</html>

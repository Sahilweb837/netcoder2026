<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Page Not Found</title>
        <!-- favicon -->
        <link rel="shortcut icon" href="images/net-coder-logo icon.png">
        <!-- css -->
        <link rel="stylesheet" href="style.css">
        <!-- font awesomme linked her -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
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
        <!-- hero section start -->
        <div class="container error-404">
            <img src="images/error404.png" alt>
            
            <div class="error-404-btn">
                <a href="index.php" onclick="openBox()"
                    class="color-btn">Back To Home</a>
            </div>
        </div>
 
              <script src="main.js"></script>
    </body>
</html>

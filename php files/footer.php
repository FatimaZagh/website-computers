<footer style="width:100%; left: 0; bottom: 0; width: 100%; margin-top:100px; margin-bottom:0px; background-color: #222; color: #ccc; padding-top: 40px; padding-bottom: 20px;">
    <!-- Added background-color, color, and padding directly to footer for better encapsulation -->
    <div class="footer-content"> <!-- Renamed class from "footer" to avoid conflict if "footer" is a global style -->
        <div class="container">
            <div class="row">
                <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 footer-column" style="margin-top:20px;"> <!-- Added sm-12 and a common class -->
                    <h3 style="color: #fff; margin-bottom: 20px;">Computer Garage</h3>
                    <p style="font-size: 0.9rem; line-height: 1.6;">
                        Your premier destination for high-performance PCs, cutting-edge components, and essential tech accessories. We're passionate about building systems that power your work, play, and creativity.
                    </p>
                </div>
                <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 footer-column" style="margin-top:20px;">
                    <h3 style="color: #fff; margin-bottom: 20px;">About Us</h3>
                    <p style="font-size: 0.9rem; line-height: 1.6;">
                        Founded by tech enthusiasts, Computer Garage is committed to providing top-quality products, expert advice, and unparalleled customer service. We believe everyone deserves a great computing experience.
                        <br><a href="about-us.php" style="color: #00aeff; text-decoration: none; margin-top:10px; display:inline-block;">Learn More ></a>
                    </p>
                </div>
                <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 footer-column" style="margin-top:20px;">
                    <h3 style="color: #fff; margin-bottom: 10px;">Connect With Us</h3> <!-- Adjusted margin -->
                    <ul class="social_icon" style="list-style:none; padding-left: 0; margin-top:10px;">
                        <li style="margin-bottom: 10px;">
                            <a href="https://www.instagram.com/fatima_zaghlol/" target="_blank" class="slink" style="text-decoration:none; color:#ccc; font-size:15px; display: flex; align-items: center; transition: color 0.3s ease, transform 0.3s ease;" onmouseover="this.style.color='#E4405F'; this.style.transform='translateX(5px)'" onmouseout="this.style.color='#ccc'; this.style.transform='translateX(0)'">
                                <img src="assets/insta_ico.png" style="padding-right:10px; width: 38px; height: auto; transition: transform 0.3s ease;" alt="Instagram">Instagram
                            </a>
                        </li>
                        <li style="margin-bottom: 10px;">
                            <a href="https://snapchat.com/t/xHscW6ge" target="_blank" class="slink" style="text-decoration:none; color:#ccc; font-size:15px; display: flex; align-items: center; transition: color 0.3s ease, transform 0.3s ease;" onmouseover="this.style.color='#FFFC00'; this.style.transform='translateX(5px)'" onmouseout="this.style.color='#ccc'; this.style.transform='translateX(0)'">
                                <img src="assets/snap_ico.png" style="padding-right:10px; width: 38px; height: auto; transition: transform 0.3s ease;" alt="Snapchat">Snapchat
                            </a>
                        </li>
                        <li style="margin-bottom: 10px;">
                            <a href="https://www.facebook.com/profile.php?id=100009277933766" target="_blank" class="slink" style="text-decoration:none; color:#ccc; font-size:15px; display: flex; align-items: center; transition: color 0.3s ease, transform 0.3s ease;" onmouseover="this.style.color='#1877F2'; this.style.transform='translateX(5px)'" onmouseout="this.style.color='#ccc'; this.style.transform='translateX(0)'">
                                <img src="assets/fb_ico.png" style="padding-right:10px; width: 38px; height: auto; transition: transform 0.3s ease;" alt="Facebook">Facebook
                            </a>
                        </li>
                        <li>
                            <a href="https://x.com/FatimaZaghlol" target="_blank" class="slink" style="text-decoration:none; color:#ccc; font-size:15px; display: flex; align-items: center; transition: color 0.3s ease, transform 0.3s ease;" onmouseover="this.style.color='#1DA1F2'; this.style.transform='translateX(5px)'" onmouseout="this.style.color='#ccc'; this.style.transform='translateX(0)'">
                                <img src="assets/twitter_ico.png" style="padding-right:10px; width: 38px; height: auto; transition: transform 0.3s ease;" alt="Twitter">Twitter
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 footer-column" style="margin-top:20px;">
                    <h3 style="color: #fff; margin-bottom: 20px;">Quick Links</h3>
                    <ul style="list-style:none; padding-left: 0; font-size: 0.9rem;">
                        <li style="margin-bottom: 8px;"><a href="showcat.php" style="color:#ccc; text-decoration:none; transition: color 0.3s ease;" onmouseover="this.style.color='#00aeff'" onmouseout="this.style.color='#ccc'">All Products</a></li>
                        <li style="margin-bottom: 8px;"><a href="contactus.php" style="color:#ccc; text-decoration:none; transition: color 0.3s ease;" onmouseover="this.style.color='#00aeff'" onmouseout="this.style.color='#ccc'">Contact Support</a></li>
                        <?php if(isset($_SESSION['pname'])): ?>
                        <li style="margin-bottom: 8px;"><a href="myorders.php" style="color:#ccc; text-decoration:none; transition: color 0.3s ease;" onmouseover="this.style.color='#00aeff'" onmouseout="this.style.color='#ccc'">My Orders</a></li>
                        <li style="margin-bottom: 8px;"><a href="userprofile.php" style="color:#ccc; text-decoration:none; transition: color 0.3s ease;" onmouseover="this.style.color='#00aeff'" onmouseout="this.style.color='#ccc'">My Profile</a></li>
                        <?php else: ?>
                        <li style="margin-bottom: 8px;"><a href="Login.php" style="color:#ccc; text-decoration:none; transition: color 0.3s ease;" onmouseover="this.style.color='#00aeff'" onmouseout="this.style.color='#ccc'">Login</a></li>
                        <li style="margin-bottom: 8px;"><a href="signup.php" style="color:#ccc; text-decoration:none; transition: color 0.3s ease;" onmouseover="this.style.color='#00aeff'" onmouseout="this.style.color='#ccc'">Sign Up</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>
        <div class="copyright" style="margin-top: 40px; border-top: 1px solid #444; padding-top: 20px;">
            <div class="container">
                <div class="row">
                    <!-- <hr color="white"width="50%;">  Replaced with border-top on .copyright -->
                    <div class="col-md-12">
                        <p align="center" style="font-size: 0.85rem; margin-bottom: 0;">
                            © <?php echo date("Y"); ?> Computer Garage. All Rights Reserved. Design by <a href="https://www.instagram.com/fatima_zaghlol/" target="_blank" style="color: #00aeff; text-decoration:none;">@fatima_zaghlol</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>

<!-- Back to Top Button -->
<button id="backToTopBtn" onclick="scrollToTop()" style="display: none; position: fixed; bottom: 20px; right: 20px; z-index: 1000; background: #007bff; color: white; border: none; border-radius: 50%; width: 50px; height: 50px; font-size: 18px; cursor: pointer; box-shadow: 0 4px 8px rgba(0,0,0,0.3); transition: all 0.3s ease;" title="Back to Top">
    <i class="fas fa-arrow-up"></i>
</button>

<script>
// Back to Top Button Functionality
window.onscroll = function() {
    const backToTopBtn = document.getElementById("backToTopBtn");
    if (document.body.scrollTop > 300 || document.documentElement.scrollTop > 300) {
        backToTopBtn.style.display = "block";
    } else {
        backToTopBtn.style.display = "none";
    }
};

function scrollToTop() {
    document.body.scrollTop = 0; // For Safari
    document.documentElement.scrollTop = 0; // For Chrome, Firefox, IE and Opera
}

// Add hover effect
document.addEventListener('DOMContentLoaded', function() {
    const backToTopBtn = document.getElementById("backToTopBtn");
    if (backToTopBtn) {
        backToTopBtn.addEventListener('mouseenter', function() {
            this.style.background = '#0056b3';
            this.style.transform = 'scale(1.1)';
        });

        backToTopBtn.addEventListener('mouseleave', function() {
            this.style.background = '#007bff';
            this.style.transform = 'scale(1)';
        });
    }
});
</script>
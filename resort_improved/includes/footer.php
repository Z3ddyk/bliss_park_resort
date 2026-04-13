    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-col">
                    <h4>🏝️ Bliss Park Resort</h4>
                    <p>Experience luxury and tranquility at Kenya's finest resort destination.</p>
                </div>
                <div class="footer-col">
                    <h4>Quick Links</h4>
                    <ul>
                        <li><a href="<?php echo isset($is_admin) ? '../' : ''; ?>rooms.php">Our Rooms</a></li>
                        <li><a href="<?php echo isset($is_admin) ? '../' : ''; ?>about.php">About Us</a></li>
                        <li><a href="<?php echo isset($is_admin) ? '../' : ''; ?>contact.php">Contact Us</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4>Contact Info</h4>
                    <p>📍 Diani Beach, Kwale County, Kenya</p>
                    <p>📞 +254 700 123 456</p>
                    <p>📧 info@blisspark.com</p>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> Bliss Park Resort. All Rights Reserved.</p>
            </div>
        </div>
    </footer>

    <script src="<?php echo isset($is_admin) ? '../' : ''; ?>assets/js/script.js"></script>
</body>
</html>

    <footer>
        <div class="footer-content">
            <div class="footer-section">
                <h3>About PakUni</h3>
                <p>Simplifying university applications for students across Pakistan</p>
            </div>
            <div class="footer-section">
                <h3>Quick Links</h3>
                <ul>
                    <li><a href="<?php echo $baseUrl; ?>/about.php">About Us</a></li>
                    <li><a href="<?php echo $baseUrl; ?>/contact.php">Contact</a></li>
                    <li><a href="<?php echo $baseUrl; ?>/faq.php">FAQ</a></li>
                </ul>
            </div>
            <div class="footer-section">
                <h3>Contact Us</h3>
                <p>Email: info@pakuni.com</p>
                <p>Phone: +92 300 1234567</p>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; <?php echo date('Y'); ?> PakUni. All rights reserved.</p>
        </div>
    </footer>

    <?php if (isset($additionalScripts)): ?>
        <?php foreach ($additionalScripts as $script): ?>
            <script src="<?php echo $baseUrl . $script; ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html> 
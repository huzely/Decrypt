</main>
<footer class="site-footer">
    <?php if (!empty($settings['banner'])): ?>
        <img src="<?= base_url('public/' . $settings['banner']) ?>" alt="Banner" class="banner">
    <?php endif; ?>
    <div class="footer-text"><?= App\Core\Security::escape($settings['footer_text'] ?? '') ?></div>
</footer>
<script src="<?= base_url('public/assets/js/main.js') ?>"></script>
</body>
</html>

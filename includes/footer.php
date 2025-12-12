<?php $settings = get_settings(); ?>
</main>
<footer class="site-footer">
    <p><?= escape_html($settings['footer_text'] ?? ''); ?></p>
</footer>
<script src="/assets/js/main.js"></script>
<script>fetch('/track_view.php',{method:'POST'}).catch(()=>{});</script>
</body>
</html>

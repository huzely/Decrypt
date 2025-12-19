    </main>
    <footer class="site-footer">
        <p><?= htmlspecialchars($settings['footer_text'] ?? '© ' . date('Y'), ENT_QUOTES); ?></p>
    </footer>
    <script>window.TRACK_TOKEN="<?= htmlspecialchars(csrf_token(), ENT_QUOTES); ?>";</script>
    <script src="/assets/js/app.js" defer></script>
</body>
</html>

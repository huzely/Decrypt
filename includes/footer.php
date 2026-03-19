    <?php if ($middleAds): $ad = $middleAds[0]; ?>
        <a class="banner-ad" href="<?php echo esc($ad['link']); ?>" target="_blank" rel="noreferrer"><img src="<?php echo esc($ad['image']); ?>" alt="Middle ad"></a>
    <?php endif; ?>
    <?php if ($footerAds): $ad = $footerAds[0]; ?>
        <a class="banner-ad" href="<?php echo esc($ad['link']); ?>" target="_blank" rel="noreferrer"><img src="<?php echo esc($ad['image']); ?>" alt="Footer ad"></a>
    <?php endif; ?>
</main>
<?php if (!empty($settings['popup_ads_enabled']) && $popupAds): $popup = $popupAds[0]; ?>
    <div class="popup-overlay" id="popupAd" hidden>
        <div class="popup-card">
            <button type="button" class="popup-close" data-close-popup>&times;</button>
            <a href="<?php echo esc($popup['link']); ?>" target="_blank" rel="noreferrer"><img src="<?php echo esc($popup['image']); ?>" alt="Popup ad"></a>
        </div>
    </div>
<?php endif; ?>
<script src="<?php echo BASE_URL; ?>assets/js/main.js"></script>
</body>
</html>

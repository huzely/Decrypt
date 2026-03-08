        </div>
        <aside class="col-lg-3">
            <?php foreach ($sidebarAds as $ad): ?>
                <div class="mb-3 ad-slot"><?= $ad['ad_code'] ?></div>
            <?php endforeach; ?>
        </aside>
    </div>
</div>

<footer class="mt-4 p-3 bg-black text-center">
    <?php foreach ((new Ad())->activeByPosition('footer') as $ad): ?>
        <div class="ad-slot mb-2"><?= $ad['ad_code'] ?></div>
    <?php endforeach; ?>
    <small>&copy; <?= date('Y') ?> GENZMOVIE - Streaming platform</small>
</footer>

<?php if (!empty($popupAds[0])): ?>
<div class="modal fade" id="popupAdModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered"><div class="modal-content bg-dark"><?= $popupAds[0]['ad_code'] ?></div></div>
</div>
<?php endif; ?>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="/genzmovie/assets/js/app.js"></script>
<?php if (!empty($popupAds[0])): ?>
<script>setTimeout(()=>new bootstrap.Modal(document.getElementById('popupAdModal')).show(),2000);</script>
<?php endif; ?>
</body>
</html>

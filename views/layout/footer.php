        </main>
        <!-- Footer -->
        <footer class="text-center py-2 text-muted small border-top">
            <?= APP_NAME ?> v<?= APP_VERSION ?> &copy; <?= date('Y') ?>
        </footer>
    </div><!-- .content-area -->
</div><!-- .wrapper -->

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<!-- App JS -->
<script src="<?= BASE_URL ?>/public/js/app.js"></script>
<?php if (isset($extraJs)): ?>
    <?php foreach ((array)$extraJs as $js): ?>
        <script src="<?= BASE_URL ?>/public/js/<?= $js ?>"></script>
    <?php endforeach; ?>
<?php endif; ?>
</body>
</html>

<?php
/**
 * Footer مشترك
 */
?>
        </div><!-- /page-content -->
    </main><!-- /main-content -->

    <!-- JavaScript -->
    <script src="<?= BASE_URL ?>assets/js/app.js"></script>
    <?php if (isset($extraJS)): ?>
    <script src="<?= BASE_URL ?>assets/js/<?= $extraJS ?>"></script>
    <?php endif; ?>
    <?php if (isset($inlineJS)): ?>
    <script><?= $inlineJS ?></script>
    <?php endif; ?>
</body>
</html>

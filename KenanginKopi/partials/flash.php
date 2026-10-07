<?php require_once __DIR__ . '/../config/helpers.php'; ?>

<?php foreach (getFlash() as $f): ?>
<div class="flash <?php echo $f['type']; ?>"><?php echo htmlspecialchars($f['msg']); ?></div>
<?php endforeach; ?>


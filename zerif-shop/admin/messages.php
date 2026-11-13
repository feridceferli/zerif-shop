<?php
require __DIR__ . '/_init.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $id = (int)($_POST['id'] ?? 0);
    if (($_POST['action'] ?? '') === 'read') {
        db()->prepare('UPDATE messages SET is_read = 1 WHERE id = ?')->execute([$id]);
    } elseif (($_POST['action'] ?? '') === 'delete') {
        db()->prepare('DELETE FROM messages WHERE id = ?')->execute([$id]);
        flash('success', 'Mesaj silindi.');
    }
    redirect(url('admin/messages.php'));
}
$msgs = db()->query('SELECT * FROM messages ORDER BY is_read ASC, id DESC')->fetchAll();
admin_header('Mesajlar');
?>
<?php if (!$msgs): ?><div class="panel"><p class="muted">Mesaj yoxdur.</p></div><?php endif; ?>
<?php foreach ($msgs as $m): ?>
    <div class="panel message <?= $m['is_read'] ? 'dim' : 'unread' ?>">
        <div class="panel-head">
            <div><strong><?= e($m['name']) ?></strong>
                <?php if ($m['phone']): ?> · <a href="tel:<?= e($m['phone']) ?>"><?= e($m['phone']) ?></a><?php endif; ?>
                <?php if ($m['email']): ?> · <a href="mailto:<?= e($m['email']) ?>"><?= e($m['email']) ?></a><?php endif; ?></div>
            <small class="muted"><?= e(date('d.m.Y H:i', strtotime($m['created_at']))) ?></small>
        </div>
        <p><?= nl2br(e($m['message'])) ?></p>
        <div class="actions">
            <?php if (!$m['is_read']): ?>
                <form method="post"><?= csrf_field() ?><input type="hidden" name="action" value="read"><input type="hidden" name="id" value="<?= $m['id'] ?>"><button class="btn btn-sm btn-ghost" type="submit">Oxundu</button></form>
            <?php endif; ?>
            <form method="post" data-confirm="Mesaj silinsin?"><?= csrf_field() ?><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= $m['id'] ?>"><button class="btn btn-sm btn-danger" type="submit">Sil</button></form>
        </div>
    </div>
<?php endforeach; ?>
<?php admin_footer(); ?>

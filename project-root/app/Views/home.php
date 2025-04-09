<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="main-content container">
    <h1 class="welcome-title">Welcome to SongBookOnline.com</h1>
    <p class="welcome-subtitle">A simple interface for managing your chord charts in ChordPro format</p>
    <div class="action-buttons">
        <?php if (session()->get('isLoggedIn')): ?>
            <a href="<?= base_url('songs') ?>" class="btn btn-primary">Browse Songs</a>
        <?php else: ?>
            <a href="<?= base_url('auth/login') ?>" class="btn btn-primary">Login</a>
            <a href="<?= base_url('auth/register') ?>" class="btn btn-success">Sign Up</a>
        <?php endif; ?>
    </div>
</div>
<?= $this->endSection() ?> 
<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="main-content container">
    <h1 class="welcome-title">Welcome to Chordpro</h1>
    <p class="welcome-subtitle">A simple interface for managing your chord charts in ChordPro format</p>
    <div class="action-buttons">
        <a href="<?= base_url('songs') ?>" class="btn btn-primary">Browse Songs</a>
        <a href="<?= base_url('songs/create') ?>" class="btn btn-success">Create New Song</a>
    </div>
</div>
<?= $this->endSection() ?> 
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container">
        <a class="navbar-brand" href="<?= base_url() ?>">Chordpro</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="<?= base_url('songs') ?>">Browse Songs</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= base_url('songs/create') ?>">Create New Song</a>
                </li>
            </ul>
            <?php if (current_url() == base_url('songs/create') || strpos(current_url(), '/songs/create/') !== false): ?>
                <button class="btn btn-success" id="saveButton">Save Song</button>
            <?php endif; ?>
        </div>
    </div>
</nav> 
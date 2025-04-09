<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container">
        <a class="navbar-brand" href="<?= base_url() ?>">SongBookOnline.com</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <?php if (session()->get('isLoggedIn')): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('songs') ?>">Browse Songs</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('songs/create') ?>">Create New Song</a>
                    </li>
                <?php endif; ?>
            </ul>
            <ul class="navbar-nav">
                <?php if (session()->get('isLoggedIn')): ?>
                    <li class="nav-item">
                        <span class="nav-link">Welcome, <?= session()->get('username') ?></span>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('auth/logout') ?>">Logout</a>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('auth/login') ?>">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('auth/register') ?>">Register</a>
                    </li>
                <?php endif; ?>
            </ul>
            <?php 
            $currentUrl = current_url();
            $isCreatePage = strpos($currentUrl, '/songs/create') !== false;
            if ($isCreatePage && session()->get('isLoggedIn')): 
            ?>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-primary" id="previewButton">Preview</button>
                    <button class="btn btn-success" id="saveButton">Save Song</button>
                </div>
            <?php endif; ?>
        </div>
    </div>
</nav> 
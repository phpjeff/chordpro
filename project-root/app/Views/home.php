<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chordpro</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="/assets/css/styles.css?v=<?= time() ?>" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container">
            <a class="navbar-brand" href="#">Chordpro</a>
        </div>
    </nav>

    <div class="main-content container">
        <h1 class="welcome-title">Welcome to Chordpro</h1>
        <p class="welcome-subtitle">A simple interface for managing your chord charts in ChordPro format</p>
        <div class="action-buttons">
            <a href="<?= base_url('songs') ?>" class="btn btn-primary">Browse Songs</a>
            <a href="<?= base_url('songs/create') ?>" class="btn btn-success">Create New Song</a>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Song List - ChordPro</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="/assets/css/styles.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container">
            <a class="navbar-brand" href="/">ChordPro</a>
            <div class="d-flex">
                <a href="/songs/create" class="btn btn-success">
                    <i class="fas fa-plus"></i> New Song
                </a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h1 class="mb-4">Song List</h1>
        
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Song Title</th>
                        <th>Key</th>
                        <th>BPM</th>
                        <th>Time</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($songs)): ?>
                    <tr>
                        <td colspan="5" class="text-center">No songs found. <a href="/songs/create">Create your first song</a></td>
                    </tr>
                    <?php else: ?>
                        <?php foreach ($songs as $song): ?>
                        <tr>
                            <td><?= esc($song['title']) ?></td>
                            <td><?= esc($song['original_key']) ?></td>
                            <td><?= esc($song['bpm']) ?? '-' ?></td>
                            <td><?= esc($song['time']) ?? '-' ?></td>
                            <td>
                                <a href="/songs/create/<?= $song['id'] ?>" class="btn btn-sm btn-primary" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button class="btn btn-sm btn-danger delete-song" data-id="<?= $song['id'] ?>" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Delete Song</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this song?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDelete">Delete</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Delete Song JavaScript -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
        let songToDelete = null;

        // Add click handlers to delete buttons
        document.querySelectorAll('.delete-song').forEach(button => {
            button.addEventListener('click', function() {
                songToDelete = this.dataset.id;
                deleteModal.show();
            });
        });

        // Handle delete confirmation
        document.getElementById('confirmDelete').addEventListener('click', async function() {
            if (!songToDelete) return;

            try {
                const response = await fetch(`/songs/delete/${songToDelete}`, {
                    method: 'DELETE'
                });

                if (response.ok) {
                    // Reload the page to show updated list
                    window.location.reload();
                } else {
                    alert('Failed to delete song');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Failed to delete song');
            }

            deleteModal.hide();
        });
    });
    </script>
</body>
</html> 
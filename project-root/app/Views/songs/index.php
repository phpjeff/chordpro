<?= $this->extend('layouts/main') ?>

<?= $this->section('styles') ?>
    <!-- Font Awesome for icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container">
    <div class="row">
        <div class="col-12">
            <h1 class="mb-4 mt-4">Song List</h1>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="table-responsive w-100">
                <table class="table table-hover songs-table">
                    <thead>
                        <tr>
                            <th class="py-3">Song Title</th>
                            <th class="py-3">Key</th>
                            <th class="py-3">BPM</th>
                            <th class="py-3">Time</th>
                            <th class="py-3">Actions</th>
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
                                    <a href="/songs/preview/<?= $song['id'] ?>" class="btn btn-sm btn-outline-primary" title="Preview" target="_blank">
                                        <i class="fas fa-eye"></i>
                                    </a>
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
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
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
<?= $this->endSection() ?> 
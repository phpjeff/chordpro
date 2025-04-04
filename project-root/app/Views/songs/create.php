<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
    <?php if ($error): ?>
        <div class="alert alert-danger m-3" role="alert">
            <?= $error ?>
            <a href="/songs" class="alert-link">Return to song list</a>
        </div>
    <?php else: ?>
        <div class="container-fluid content-wrapper">
            <div class="toolbar">
                <div class="row align-items-center">
                    <div class="col-12 mb-2">
                        <div class="input-group">
                            <span class="input-group-text">Song Title</span>
                            <input type="text" class="form-control" id="songTitle" placeholder="Enter song title" value="<?= $song ? esc($song['title']) : '' ?>">
                            <?php if ($song): ?>
                                <input type="hidden" id="songId" value="<?= $song['id'] ?>">
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="row align-items-center">
                    <div class="col-2">
                        <div class="input-group">
                            <span class="input-group-text">Original Key</span>
                            <select class="form-select" id="originalKey">
                                <?php
                                $keys = ['A', 'A#', 'B', 'C', 'C#', 'D', 'D#', 'E', 'F', 'F#', 'G', 'G#'];
                                foreach ($keys as $key):
                                    $selected = ($song && $song['original_key'] === $key) ? 'selected' : ($key === 'C' ? 'selected' : '');
                                ?>
                                    <option value="<?= $key ?>" <?= $selected ?>><?= $key ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-2">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="transposeChords">
                            <label class="form-check-label" for="transposeChords">Transpose</label>
                        </div>
                    </div>
                    <div class="col-2">
                        <div class="input-group">
                            <span class="input-group-text">BPM</span>
                            <input type="number" class="form-control" id="bpmInput" value="<?= $song ? esc($song['bpm']) : '100' ?>">
                        </div>
                    </div>
                    <div class="col-2">
                        <div class="input-group">
                            <span class="input-group-text">Time</span>
                            <input type="text" class="form-control" id="timeInput" value="<?= $song ? esc($song['time']) : '4/4' ?>">
                        </div>
                    </div>
                    <div class="col-2 offset-2">
                        <div class="form-check form-switch float-end">
                            <input class="form-check-input" type="checkbox" id="autoRefresh" checked>
                            <label class="form-check-label" for="autoRefresh">Auto-refresh</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="editor-container">
                <div class="editor-pane">
                    <textarea id="editor" class="form-control"><?= $song ? esc($song['chordpro']) : '' ?></textarea>
                </div>
                <div class="preview-pane">
                    <div id="preview" class="preview-content"></div>
                </div>
            </div>
        </div>
    <?php endif; ?>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
    <script src="/assets/js/editor.js"></script>
<?= $this->endSection() ?>
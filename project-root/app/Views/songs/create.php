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
                    <div class="col-6 mb-2">
                        <div class="input-group">
                            <span class="input-group-text">Artist</span>
                            <input type="text" class="form-control" id="artistInput" placeholder="Enter artist name" value="<?= $song && isset($song['artist']) ? esc($song['artist']) : '' ?>">
                        </div>
                    </div>
                    <div class="col-6 mb-2">
                        <div class="input-group">
                            <span class="input-group-text">Copyright</span>
                            <input type="text" class="form-control" id="copyrightInput" placeholder="Enter copyright information" value="<?= $song && isset($song['copyright']) ? esc($song['copyright']) : '' ?>">
                        </div>
                    </div>
                </div>
                <div class="row align-items-center">
                    <div class="col-2">
                        <div class="input-group">
                            <span class="input-group-text">Original Key</span>
                            <select class="form-select" id="originalKey">
                                <?php
                                $naturalKeys = ['A', 'B', 'C', 'D', 'E', 'F', 'G'];
                                $sharpKeys = ['A#', 'C#', 'D#', 'F#', 'G#'];
                                $flatKeys = ['Bb', 'Db', 'Eb', 'Gb', 'Ab'];
                                ?>
                                <optgroup label="Natural">
                                    <?php foreach ($naturalKeys as $key): ?>
                                        <?php $selected = ($song && $song['original_key'] === $key) ? 'selected' : ($key === 'C' ? 'selected' : ''); ?>
                                        <option value="<?= $key ?>" <?= $selected ?>><?= $key ?></option>
                                    <?php endforeach; ?>
                                </optgroup>
                                <optgroup label="Sharp Keys">
                                    <?php foreach ($sharpKeys as $key): ?>
                                        <?php $selected = ($song && $song['original_key'] === $key) ? 'selected' : ''; ?>
                                        <option value="<?= $key ?>" <?= $selected ?>><?= $key ?></option>
                                    <?php endforeach; ?>
                                </optgroup>
                                <optgroup label="Flat Keys">
                                    <?php foreach ($flatKeys as $key): ?>
                                        <?php $selected = ($song && $song['original_key'] === $key) ? 'selected' : ''; ?>
                                        <option value="<?= $key ?>" <?= $selected ?>><?= $key ?></option>
                                    <?php endforeach; ?>
                                </optgroup>
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
                            <span class="input-group-text">Capo</span>
                            <input type="number" class="form-control" id="capoInput" min="0" max="12" value="<?= $song && isset($song['capo']) ? esc($song['capo']) : '' ?>" placeholder="0">
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
                    <div class="col-2">
                        <button type="button" class="btn btn-outline-secondary" id="pageBreakButton" title="Insert Page Break (Ctrl+Enter)">
                            <i class="fas fa-file-alt"></i> Page Break
                        </button>
                    </div>
                    <div class="col-2">
                        <div class="form-check form-switch float-end">
                            <input class="form-check-input" type="checkbox" id="autoRefresh" checked>
                            <label class="form-check-label" for="autoRefresh">Auto-refresh</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="editor-container">
                <div class="editor-pane">
                    <div id="diatonicChords" class="btn-group mb-2" role="group" aria-label="Diatonic chords"></div>
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
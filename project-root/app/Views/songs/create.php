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
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const editor = document.getElementById('editor');
        const preview = document.getElementById('preview');
        const originalKey = document.getElementById('originalKey');
        const diatonicChords = document.getElementById('diatonicChords');
        const autoRefresh = document.getElementById('autoRefresh');

        // Define chord qualities for each scale degree in major keys
        const chordQualities = ['', 'm', 'm', '', '', 'm', 'dim'];
        
        // Function to get diatonic chords for a given key
        function getDiatonicChords(key) {
            const majorScale = ['C', 'C#', 'D', 'D#', 'E', 'F', 'F#', 'G', 'G#', 'A', 'A#', 'B'];
            const keyIndex = majorScale.indexOf(key);
            const scaleNotes = [];
            
            // Generate the major scale starting from the given key
            for (let i = 0; i < 7; i++) {
                const intervals = [0, 2, 4, 5, 7, 9, 11]; // Major scale intervals
                const noteIndex = (keyIndex + intervals[i]) % 12;
                scaleNotes.push(majorScale[noteIndex]);
            }
            
            // Return the diatonic chords with their qualities
            return scaleNotes.map((note, index) => note + chordQualities[index]);
        }

        // Function to update diatonic chord buttons
        function updateDiatonicChords() {
            const key = originalKey.value;
            const chords = getDiatonicChords(key);
            
            // Clear existing buttons
            diatonicChords.innerHTML = '';
            
            // Create new buttons for each chord
            chords.forEach((chord, index) => {
                const button = document.createElement('button');
                button.type = 'button';
                button.className = 'btn btn-outline-secondary btn-sm';
                button.textContent = chord;
                button.addEventListener('click', () => insertChordAtCursor(chord));
                diatonicChords.appendChild(button);
            });
        }

        // Function to insert chord at cursor position
        function insertChordAtCursor(chord) {
            const start = editor.selectionStart;
            const end = editor.selectionEnd;
            const text = editor.value;
            
            // Insert the chord with brackets
            const newText = text.substring(0, start) + '[' + chord + ']' + text.substring(end);
            editor.value = newText;
            
            // Move cursor after the inserted chord
            const newCursorPos = start + chord.length + 2;
            editor.setSelectionRange(newCursorPos, newCursorPos);
            editor.focus();
            
            // Trigger the input event to update preview
            editor.dispatchEvent(new Event('input'));
        }

        // Update chord buttons when key changes
        originalKey.addEventListener('change', updateDiatonicChords);

        // Initialize chord buttons
        updateDiatonicChords();
    });
    </script>
<?= $this->endSection() ?>
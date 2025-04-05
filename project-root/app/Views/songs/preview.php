<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Song Preview' ?></title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Preview styles -->
    <link href="<?= base_url('assets/css/preview.css') ?>" rel="stylesheet">
</head>
<body>
    <div class="transpose-controls no-print">
        <div class="input-group">
            <span class="input-group-text">Transpose To</span>
            <select class="form-select" id="transposeKey">
                <?php
                $keys = ['A', 'A#', 'B', 'C', 'C#', 'D', 'D#', 'E', 'F', 'F#', 'G', 'G#'];
                foreach ($keys as $key):
                    echo "<option value=\"{$key}\">{$key}</option>";
                endforeach;
                ?>
            </select>
        </div>
        <button class="btn btn-primary mt-2 w-100" id="transposeButton">Transpose</button>
    </div>
    
    <div class="print-controls no-print">
        <button class="btn btn-primary" onclick="window.print()">Print</button>
        <button class="btn btn-secondary" onclick="closePreview()">Close</button>
    </div>
    <div class="preview-content" id="preview">
        <?= $content ?>
    </div>

    <script>
    function closePreview() {
        // Try to close the window first
        if (window.close()) {
            return;
        }
        // If window.close() fails, try to go back or to the songs list
        if (window.history.length > 1) {
            window.history.back();
        } else {
            window.location.href = '/songs';
        }
    }

    // Transpose functionality
    document.addEventListener('DOMContentLoaded', function() {
        // Get all chord elements
        const chords = document.querySelectorAll('.chord');
        const transposeKeySelect = document.getElementById('transposeKey');
        const transposeButton = document.getElementById('transposeButton');
        
        // Get the original key from the preview content
        const keyMatch = document.querySelector('.preview-meta')?.textContent.match(/Key: ([A-G]#?)/);
        if (!keyMatch || !keyMatch[1]) {
            // If no key found, disable transpose functionality
            transposeButton.disabled = true;
            transposeKeySelect.disabled = true;
            return;
        }
        
        const originalKey = keyMatch[1];
        
        // Set the transpose key to the original key initially
        transposeKeySelect.value = originalKey;
        
        // Transpose button click handler
        transposeButton.addEventListener('click', function() {
            const targetKey = transposeKeySelect.value;
            
            // Calculate semitones to transpose
            const keys = ['A', 'A#', 'B', 'C', 'C#', 'D', 'D#', 'E', 'F', 'F#', 'G', 'G#'];
            const originalIndex = keys.indexOf(originalKey);
            const targetIndex = keys.indexOf(targetKey);
            const semitones = targetIndex - originalIndex;
            
            // Transpose each chord
            chords.forEach(chord => {
                const originalChord = chord.textContent;
                const transposedChord = transposeChord(originalChord, semitones);
                chord.textContent = transposedChord;
            });
            
            // Update the key in the preview header
            const keyElement = document.querySelector('.preview-meta');
            if (keyElement) {
                keyElement.textContent = keyElement.textContent.replace(/Key: [A-G]#?/, `Key: ${targetKey}`);
            }
        });
        
        // Function to transpose a chord
        function transposeChord(chord, semitones) {
            // Extract the root note and any modifiers
            const match = chord.match(/^([A-G]#?)(.*)$/);
            if (!match) return chord;
            
            const root = match[1];
            const modifiers = match[2];
            
            // Define the chromatic scale
            const chromaticScale = ['A', 'A#', 'B', 'C', 'C#', 'D', 'D#', 'E', 'F', 'F#', 'G', 'G#'];
            
            // Find the current position in the scale
            let currentIndex = chromaticScale.indexOf(root);
            if (currentIndex === -1) return chord;
            
            // Calculate the new position
            let newIndex = (currentIndex + semitones) % 12;
            if (newIndex < 0) newIndex += 12;
            
            // Get the new root note
            const newRoot = chromaticScale[newIndex];
            
            // Return the transposed chord
            return newRoot + modifiers;
        }
    });
    </script>
</body>
</html> 
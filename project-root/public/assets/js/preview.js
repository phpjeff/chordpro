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
    
    // Define the chromatic scales
    const chromaticScaleWithSharps = ['A', 'A#', 'B', 'C', 'C#', 'D', 'D#', 'E', 'F', 'F#', 'G', 'G#'];
    const chromaticScaleWithFlats = ['A', 'Bb', 'B', 'C', 'Db', 'D', 'Eb', 'E', 'F', 'Gb', 'G', 'Ab'];
    
    // Function to normalize key notation
    function normalizeKey(key) {
        if (key.includes('b')) {
            const index = chromaticScaleWithFlats.indexOf(key);
            return index !== -1 ? chromaticScaleWithSharps[index] : key;
        }
        return key;
    }
    
    // Function to get current key from preview content
    function getCurrentKey() {
        const keyMatch = document.querySelector('.preview-meta')?.textContent.match(/Key - ([A-G][#b]?)/);
        return keyMatch ? keyMatch[1] : null;
    }
    
    // Get the original key from the preview content
    let currentKey = getCurrentKey();
    if (!currentKey) {
        // If no key found, disable transpose functionality
        transposeKeySelect.disabled = true;
        return;
    }
    
    // Set the transpose key to the current key initially
    transposeKeySelect.value = currentKey;
    
    // Function to get equivalent flat notation
    function getEquivalentFlat(sharpNote) {
        const index = chromaticScaleWithSharps.indexOf(sharpNote);
        return index !== -1 ? chromaticScaleWithFlats[index] : sharpNote;
    }
    
    // Function to get equivalent sharp notation
    function getEquivalentSharp(flatNote) {
        const index = chromaticScaleWithFlats.indexOf(flatNote);
        return index !== -1 ? chromaticScaleWithSharps[index] : flatNote;
    }
    
    // Function to transpose a chord
    function transposeChord(chord, semitones, useFlats) {
        // Extract the root note and any modifiers
        const match = chord.match(/^([A-G][b#]?)(.*)$/);
        if (!match) return chord;
        
        const [_, root, modifiers] = match;
        
        // Convert root to sharp notation for calculation
        const normalizedRoot = root.includes('b') ? getEquivalentSharp(root) : root;
        const currentIndex = chromaticScaleWithSharps.indexOf(normalizedRoot);
        const newIndex = (currentIndex + semitones + 12) % 12;
        
        // Use flats or sharps based on the target key's notation
        let newRoot;
        if (useFlats) {
            newRoot = chromaticScaleWithFlats[newIndex];
        } else {
            newRoot = chromaticScaleWithSharps[newIndex];
        }
        
        // If it's a natural note, always use the natural notation
        if (['A', 'B', 'C', 'D', 'E', 'F', 'G'].includes(newRoot)) {
            return newRoot + modifiers;
        }
        
        return newRoot + modifiers;
    }
    
    // Function to perform transposition
    function transposeToKey(targetKey) {
        // Calculate semitones to transpose using current key
        const normalizedCurrentKey = normalizeKey(currentKey);
        const normalizedTargetKey = normalizeKey(targetKey);
        
        const fromIndex = chromaticScaleWithSharps.indexOf(normalizedCurrentKey);
        const toIndex = chromaticScaleWithSharps.indexOf(normalizedTargetKey);
        const semitones = (toIndex - fromIndex + 12) % 12;
        
        // Determine if we should use flats based on the target key
        const useFlats = targetKey.includes('b');
        
        // Transpose each chord
        chords.forEach(chord => {
            const originalChord = chord.textContent;
            const transposedChord = transposeChord(originalChord, semitones, useFlats);
            chord.textContent = transposedChord;
        });
        
        // Update the key in the preview header
        const keyElement = document.querySelector('.preview-meta');
        if (keyElement) {
            keyElement.textContent = keyElement.textContent.replace(/Key - [A-G][#b]?/, `Key - ${targetKey}`);
        }
        
        // Update the current key for next transposition
        currentKey = targetKey;
    }
    
    // Transpose on key selection change
    transposeKeySelect.addEventListener('change', function() {
        const targetKey = this.value;
        transposeToKey(targetKey);
    });
}); 
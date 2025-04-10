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
    const capoValueInput = document.getElementById('capoValue');
    
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

    // Function to get current capo value from preview content
    function getCurrentCapo() {
        const capoElement = document.querySelector('.preview-capo');
        if (capoElement) {
            const capoMatch = capoElement.textContent.match(/Capo (\d+)/);
            return capoMatch ? parseInt(capoMatch[1]) : 0;
        }
        return 0;
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

    // Set the initial capo value
    capoValueInput.value = getCurrentCapo();
    
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
        // Check if the chord has a bass note (e.g., D/F#)
        const bassMatch = chord.match(/^([A-G][b#]?)\/([A-G][b#]?)(.*)$/);
        if (bassMatch) {
            // This is a chord with a bass note
            const [_, root, bass, modifiers] = bassMatch;
            
            // Transpose the root note
            const normalizedRoot = root.includes('b') ? getEquivalentSharp(root) : root;
            const rootIndex = chromaticScaleWithSharps.indexOf(normalizedRoot);
            const newRootIndex = (rootIndex + semitones + 12) % 12;
            
            // Transpose the bass note
            const normalizedBass = bass.includes('b') ? getEquivalentSharp(bass) : bass;
            const bassIndex = chromaticScaleWithSharps.indexOf(normalizedBass);
            const newBassIndex = (bassIndex + semitones + 12) % 12;
            
            // Use flats or sharps based on the target key's notation
            let newRoot, newBass;
            if (useFlats) {
                newRoot = chromaticScaleWithFlats[newRootIndex];
                newBass = chromaticScaleWithFlats[newBassIndex];
            } else {
                newRoot = chromaticScaleWithSharps[newRootIndex];
                newBass = chromaticScaleWithSharps[newBassIndex];
            }
            
            // If it's a natural note, always use the natural notation
            if (['A', 'B', 'C', 'D', 'E', 'F', 'G'].includes(newRoot)) {
                newRoot = newRoot;
            }
            if (['A', 'B', 'C', 'D', 'E', 'F', 'G'].includes(newBass)) {
                newBass = newBass;
            }
            
            return newRoot + '/' + newBass + modifiers;
        }
        
        // Handle regular chords (no bass note)
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
    
    // Function to update capo display
    function updateCapoDisplay() {
        const capoValue = parseInt(capoValueInput.value) || 0;
        let capoElement = document.querySelector('.preview-capo');
        const previewHeader = document.querySelector('.preview-header');
        
        if (capoValue > 0) {
            if (!capoElement) {
                // Create capo element if it doesn't exist
                capoElement = document.createElement('div');
                capoElement.className = 'col-2 preview-capo';
                
                // Find the title element to insert after
                const titleElement = document.querySelector('.preview-title');
                if (titleElement) {
                    titleElement.classList.remove('col-12');
                    titleElement.classList.add('col-10');
                    titleElement.after(capoElement);
                } else if (previewHeader) {
                    previewHeader.appendChild(capoElement);
                }
            }
            capoElement.textContent = `Capo ${capoValue}`;
        } else if (capoElement) {
            // Remove capo element and restore title width
            const titleElement = document.querySelector('.preview-title');
            if (titleElement) {
                titleElement.classList.remove('col-10');
                titleElement.classList.add('col-12');
            }
            capoElement.remove();
        }
    }
    
    // Transpose on key selection change
    transposeKeySelect.addEventListener('change', function() {
        const targetKey = this.value;
        transposeToKey(targetKey);
    });
    
    // Update capo display on value change
    capoValueInput.addEventListener('change', function() {
        // Ensure the value is within the valid range
        let value = parseInt(this.value) || 0;
        value = Math.max(0, Math.min(12, value));
        this.value = value;
        
        updateCapoDisplay();
    });
    
    // Initialize capo display
    updateCapoDisplay();
}); 
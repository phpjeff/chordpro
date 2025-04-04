document.addEventListener('DOMContentLoaded', function() {
    const editor = document.getElementById('editor');
    const preview = document.getElementById('preview');
    const autoRefresh = document.getElementById('autoRefresh');
    const songTitle = document.getElementById('songTitle');
    const bpmInput = document.getElementById('bpmInput');
    const timeInput = document.getElementById('timeInput');
    const originalKey = document.getElementById('originalKey');
    const transposeChords = document.getElementById('transposeChords');
    const songId = document.getElementById('songId');
    const saveButton = document.getElementById('saveButton');
    const previewButton = document.getElementById('previewButton');
    
    // Define the chromatic scale with sharps
    const chromaticScale = ['A', 'A#', 'B', 'C', 'C#', 'D', 'D#', 'E', 'F', 'F#', 'G', 'G#'];
    
    // Default values for new songs
    const DEFAULT_KEY = 'C';
    const DEFAULT_TEMPO = '100';
    const DEFAULT_TIME = '4/4';
    
    // Function to get the semitone difference between two notes
    function getSemitoneDifference(fromNote, toNote) {
        const fromIndex = chromaticScale.indexOf(fromNote);
        const toIndex = chromaticScale.indexOf(toNote);
        return (toIndex - fromIndex + 12) % 12;
    }
    
    // Function to transpose a chord
    function transposeChord(chord, semitones) {
        // Extract the root note and any modifiers
        const match = chord.match(/^([A-G]#?)(.*)/);
        if (!match) return chord;
        
        const [_, root, modifiers] = match;
        const currentIndex = chromaticScale.indexOf(root);
        const newIndex = (currentIndex + semitones + 12) % 12;
        return chromaticScale[newIndex] + modifiers;
    }
    
    // Function to transpose all chords in the content
    function transposeContent(content, fromKey, toKey) {
        const semitones = getSemitoneDifference(fromKey, toKey);
        return content.replace(/\[([^\]]+)\]/g, (match, chord) => {
            return `[${transposeChord(chord, semitones)}]`;
        });
    }

    // Function to save or update the song
    async function saveSong() {
        const data = {
            title: songTitle.value,
            original_key: originalKey.value,
            bpm: bpmInput.value,
            time: timeInput.value,
            chordpro: editor.value
        };

        if (songId) {
            data.id = songId.value;
        }

        try {
            const response = await fetch('/songs/save', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams(data)
            });

            const result = await response.json();

            if (result.success) {
                // Show success message
                alert(result.message);
                
                // If this was a new song, update the URL to edit mode
                if (!songId && result.id) {
                    window.history.pushState({}, '', `/songs/create/${result.id}`);
                    location.reload();
                }
            } else {
                // Show error message
                alert('Error: ' + (result.errors ? Object.values(result.errors).join('\n') : result.message));
            }
        } catch (error) {
            alert('Error saving song: ' + error.message);
        }
    }

    // Add keyboard shortcut for saving (Ctrl+S or Cmd+S)
    document.addEventListener('keydown', function(e) {
        if ((e.ctrlKey || e.metaKey) && e.key === 's') {
            e.preventDefault();
            saveSong();
        }
    });

    // Add save button click handler
    if (saveButton) {
        saveButton.addEventListener('click', saveSong);
    }
    
    // Initialize new song if needed
    initializeNewSong();
    
    function parseChordPro(text) {
        // Basic ChordPro parsing
        let html = '<div class="preview-header">';
        
        // Parse metadata
        const title = text.match(/{title:\s*(.+)}/);
        const key = text.match(/{key:\s*(.+)}/);
        const tempo = text.match(/{tempo:\s*([^}]+)}/);
        const time = text.match(/{time:\s*(.+)}/);
        
        if (title) {
            html += `<div class="preview-title">${title[1]}</div>`;
        }
        
        let meta = [];
        if (key) meta.push(`Key: ${key[1]}`);
        if (tempo) meta.push(`${tempo[1]} bpm`);
        if (time) meta.push(`${time[1]}`);
        
        if (meta.length > 0) {
            html += `<div class="preview-meta">${meta.join(', ')}</div>`;
        }
        
        html += '</div>';

        // Parse content
        const lines = text.split('\n');
        let inVerse = false;
        
        for (const line of lines) {
            if (line.trim() === '') {
                html += '<br>';
                continue;
            }
            
            if (line.match(/{.*}/)) continue; // Skip metadata lines
            
            if (line.startsWith('[Verse') || line.startsWith('[Chorus') || line.startsWith('[Bridge')) {
                if (inVerse) html += '</div>';
                // Remove square brackets from section names
                const sectionName = line.replace(/^\[(.*)\]$/, '$1');
                html += `<div class="verse"><div class="section-name">${sectionName}</div>`;
                inVerse = true;
                continue;
            }
            
            // Parse chords and lyrics
            let processedLine = line;
            const chordRegex = /\[([^\]]+)\]/g;
            
            // Replace each chord with a positioned span
            processedLine = processedLine.replace(chordRegex, (match, chord) => {
                return `<span class="chord">${chord}</span>`;
            });

            html += `<div class="line">${processedLine}</div>`;
        }
        
        if (inVerse) html += '</div>';
        return html;
    }

    function updatePreview() {
        const content = editor.value;
        preview.innerHTML = parseChordPro(content);
    }

    editor.addEventListener('input', function() {
        if (autoRefresh.checked) {
            updatePreview();
        }
    });

    autoRefresh.addEventListener('change', function() {
        if (this.checked) {
            updatePreview();
        }
    });

    // Function to update metadata in the correct order
    function updateMetadata(type, value) {
        let content = editor.value;
        const metadataOrder = ['title', 'key', 'tempo', 'time'];
        
        // Remove existing metadata line if it exists
        content = content.replace(new RegExp(`{${type}:[^}]*}\n?`), '');
        
        // Split content into metadata and song content
        const lines = content.split('\n');
        const metadata = [];
        const songContent = [];
        let isMetadata = true;
        
        for (const line of lines) {
            if (isMetadata && line.match(/^{.*}/)) {
                metadata.push(line);
            } else {
                isMetadata = false;
                if (line.trim()) {
                    songContent.push(line);
                }
            }
        }
        
        // Create new metadata object
        const metadataObj = {};
        metadata.forEach(line => {
            const match = line.match(/^{(\w+):\s*([^}]*)}/);
            if (match) {
                // Remove 'bpm' from tempo if it exists
                if (match[1] === 'tempo') {
                    metadataObj[match[1]] = match[2].replace(/\s*bpm$/, '').trim();
                } else {
                    metadataObj[match[1]] = match[2];
                }
            }
        });
        
        // Add or update the new metadata
        metadataObj[type] = value;
        
        // Build metadata in correct order
        const orderedMetadata = [];
        metadataOrder.forEach(key => {
            if (metadataObj[key]) {
                orderedMetadata.push(`{${key}: ${metadataObj[key]}}`);
            }
        });
        
        // Combine metadata and content
        editor.value = [...orderedMetadata, '', ...songContent].join('\n').trim() + '\n';
        
        if (autoRefresh.checked) {
            updatePreview();
        }
    }

    songTitle.addEventListener('input', function() {
        updateMetadata('title', this.value);
    });

    bpmInput.addEventListener('input', function() {
        updateMetadata('tempo', this.value);
    });

    timeInput.addEventListener('input', function() {
        updateMetadata('time', this.value);
    });

    originalKey.addEventListener('change', function() {
        const newKey = this.value;
        const content = editor.value;
        const keyMatch = content.match(/{key:\s*([^}]*)}/);
        
        updateMetadata('key', newKey);
        
        if (keyMatch && transposeChords.checked) {
            const oldKey = keyMatch[1].trim();
            editor.value = transposeContent(editor.value, oldKey, newKey);
        }
        
        if (autoRefresh.checked) {
            updatePreview();
        }
    });

    // Initial preview
    updatePreview();

    // Initialize new song if editor is empty
    function initializeNewSong() {
        if (!editor.value.trim()) {
            // Set default values in inputs
            originalKey.value = DEFAULT_KEY;
            bpmInput.value = DEFAULT_TEMPO;
            timeInput.value = DEFAULT_TIME;

            // Initialize metadata in editor
            const initialMetadata = [
                '{title: }',
                `{key: ${DEFAULT_KEY}}`,
                `{tempo: ${DEFAULT_TEMPO}}`,
                `{time: ${DEFAULT_TIME}}`,
                '',
                ''
            ].join('\n');
            
            editor.value = initialMetadata;
            updatePreview();
        }
    }

    // Add preview button click handler
    if (previewButton) {
        previewButton.addEventListener('click', function() {
            // Get the current preview content
            const previewContent = preview.innerHTML;
            
            // Create a form to post the content
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '/songs/preview';
            form.target = '_blank'; // Open in new window
            
            // Add the content as a hidden field
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'content';
            input.value = previewContent;
            form.appendChild(input);
            
            // Add the title as a hidden field
            const titleInput = document.createElement('input');
            titleInput.type = 'hidden';
            titleInput.name = 'title';
            titleInput.value = songTitle.value || 'Song Preview';
            form.appendChild(titleInput);
            
            // Submit the form
            document.body.appendChild(form);
            form.submit();
            document.body.removeChild(form);
        });
    }
}); 
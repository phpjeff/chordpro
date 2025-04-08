document.addEventListener('DOMContentLoaded', function() {
    const editor = document.getElementById('editor');
    const preview = document.getElementById('preview');
    const autoRefresh = document.getElementById('autoRefresh');
    const songTitle = document.getElementById('songTitle');
    const artistInput = document.getElementById('artistInput');
    const copyrightInput = document.getElementById('copyrightInput');
    const bpmInput = document.getElementById('bpmInput');
    const timeInput = document.getElementById('timeInput');
    const originalKey = document.getElementById('originalKey');
    const transposeChords = document.getElementById('transposeChords');
    const songId = document.getElementById('songId');
    const saveButton = document.getElementById('saveButton');
    const previewButton = document.getElementById('previewButton');
    const diatonicChords = document.getElementById('diatonicChords');
    const capoInput = document.getElementById('capoInput');
    
    // Define the chromatic scales with separate sharp and flat versions
    const chromaticScaleWithSharps = ['A', 'A#', 'B', 'C', 'C#', 'D', 'D#', 'E', 'F', 'F#', 'G', 'G#'];
    const chromaticScaleWithFlats = ['A', 'Bb', 'B', 'C', 'Db', 'D', 'Eb', 'E', 'F', 'Gb', 'G', 'Ab'];
    const naturalKeys = ['A', 'B', 'C', 'D', 'E', 'F', 'G'];
    
    // Default values for new songs
    const DEFAULT_KEY = 'C';
    const DEFAULT_TEMPO = '100';
    const DEFAULT_TIME = '4/4';
    
    // Define chord qualities for each scale degree in major keys
    const chordQualities = ['', 'm', 'm', '', '', 'm', 'dim'];
    
    // Function to normalize key notation
    function normalizeKey(key) {
        // Convert flat notation to sharp notation for internal calculations
        if (key.includes('b')) {
            const index = chromaticScaleWithFlats.indexOf(key);
            return index !== -1 ? chromaticScaleWithSharps[index] : key;
        }
        return key;
    }
    
    // Function to get the equivalent flat notation for a sharp note
    function getEquivalentFlat(sharpNote) {
        const index = chromaticScaleWithSharps.indexOf(sharpNote);
        return index !== -1 ? chromaticScaleWithFlats[index] : sharpNote;
    }
    
    // Function to get the equivalent sharp notation for a flat note
    function getEquivalentSharp(flatNote) {
        const index = chromaticScaleWithFlats.indexOf(flatNote);
        return index !== -1 ? chromaticScaleWithSharps[index] : flatNote;
    }
    
    // Function to get the semitone difference between two notes
    function getSemitoneDifference(fromNote, toNote) {
        // Normalize both notes to sharp notation for comparison
        const normalizedFromNote = normalizeKey(fromNote);
        const normalizedToNote = normalizeKey(toNote);
        
        const fromIndex = chromaticScaleWithSharps.indexOf(normalizedFromNote);
        const toIndex = chromaticScaleWithSharps.indexOf(normalizedToNote);
        return (toIndex - fromIndex + 12) % 12;
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
        if (naturalKeys.includes(newRoot)) {
            return newRoot + modifiers;
        }
        
        return newRoot + modifiers;
    }
    
    // Function to get diatonic chords for a given key
    function getDiatonicChords(key) {
        const normalizedKey = normalizeKey(key);
        const keyIndex = chromaticScaleWithSharps.indexOf(normalizedKey);
        const scaleNotes = [];
        
        // Generate the major scale starting from the given key
        for (let i = 0; i < 7; i++) {
            const intervals = [0, 2, 4, 5, 7, 9, 11]; // Major scale intervals
            const noteIndex = (keyIndex + intervals[i]) % 12;
            
            // Use the same notation style as the original key
            let note;
            if (key.includes('b')) {
                note = chromaticScaleWithFlats[noteIndex];
            } else {
                note = chromaticScaleWithSharps[noteIndex];
            }
            scaleNotes.push(note);
        }
        
        // Return the diatonic chords with their qualities
        return scaleNotes.map((note, index) => note + chordQualities[index]);
    }
    
    // Function to update diatonic chord buttons
    function updateDiatonicChords() {
        if (!diatonicChords) return;
        
        const key = originalKey.value;
        const chords = getDiatonicChords(key);
        
        // Clear existing buttons
        diatonicChords.innerHTML = '';
        
        // Create new buttons for each chord
        chords.forEach((chord, index) => {
            const button = document.createElement('button');
            button.type = 'button';
            button.className = 'btn btn-outline-secondary btn-sm';
            button.textContent = `${chord}`;
            button.title = `Insert ${chord} (Ctrl+${index + 1})`;
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
    
    // Function to transpose all chords in the content
    function transposeContent(content, fromKey, toKey) {
        const semitones = getSemitoneDifference(fromKey, toKey);
        // Determine if we should use flats based on the target key
        const useFlats = toKey.includes('b');
        
        return content.replace(/\[([^\]]+)\]/g, (match, chord) => {
            return `[${transposeChord(chord, semitones, useFlats)}]`;
        });
    }

    // Function to show a notification
    function showNotification(message, isError = false) {
        // Remove any existing notification
        const existingNotification = document.querySelector('.notification');
        if (existingNotification) {
            existingNotification.remove();
        }
        
        // Create new notification
        const notification = document.createElement('div');
        notification.className = `notification${isError ? ' error' : ''}`;
        notification.textContent = message;
        
        // Add to document
        document.body.appendChild(notification);
        
        // Show notification with animation
        setTimeout(() => {
            notification.classList.add('show');
        }, 10);
        
        // Hide notification after 5 seconds
        setTimeout(() => {
            notification.classList.remove('show');
            // Remove from DOM after animation completes
            setTimeout(() => {
                notification.remove();
            }, 300);
        }, 5000);
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
                // Show success notification
                showNotification(result.message);
                
                // If this was a new song, update the URL to edit mode
                if (!songId && result.id) {
                    window.history.pushState({}, '', `/songs/create/${result.id}`);
                    location.reload();
                }
            } else {
                // Show error notification
                showNotification('Error: ' + (result.errors ? Object.values(result.errors).join('\n') : result.message), true);
            }
        } catch (error) {
            showNotification('Error saving song: ' + error.message, true);
        }
    }

    // Add keyboard shortcut for saving (Ctrl+S or Cmd+S)
    document.addEventListener('keydown', function(e) {
        if ((e.ctrlKey || e.metaKey) && e.key === 's') {
            e.preventDefault();
            saveSong().then(() => {
                // After successful save, update the input fields to reflect any changes in the editor
                populateMetadataFields();
            });
        }
    });

    // Add save button click handler
    if (saveButton) {
        saveButton.addEventListener('click', async function() {
            await saveSong();
            // After successful save, update the input fields to reflect any changes in the editor
            populateMetadataFields();
        });
    }
    
    // Initialize new song if needed
    initializeNewSong();
    
    // Extract metadata from ChordPro content and populate input fields
    function populateMetadataFields() {
        const content = editor.value;
        
        // Extract all metadata lines first
        const metadataLines = content.split('\n').filter(line => line.match(/^{.*}/));
        
        // Extract title
        const titleMatch = metadataLines.find(line => line.match(/^{title:\s*(.+)}/));
        if (titleMatch && songTitle) {
            const match = titleMatch.match(/^{title:\s*(.+)}/);
            songTitle.value = match[1].trim();
        }
        
        // Extract artist in both formats
        const artistMatch = metadataLines.find(line => line.match(/^{artist:\s*(.+)}/));
        const metaArtistMatch = metadataLines.find(line => line.match(/^{meta:\s*artist\s+(.+)}/));
        if (artistMatch && artistInput) {
            const match = artistMatch.match(/^{artist:\s*(.+)}/);
            artistInput.value = match[1].trim();
        } else if (metaArtistMatch && artistInput) {
            const match = metaArtistMatch.match(/^{meta:\s*artist\s+(.+)}/);
            artistInput.value = match[1].trim();
        }
        
        // Extract copyright in both formats
        const copyrightMatch = metadataLines.find(line => line.match(/^{copyright:\s*(.+)}/));
        const metaCopyrightMatch = metadataLines.find(line => line.match(/^{meta:\s*copyright\s+(.+)}/));
        if (copyrightMatch && copyrightInput) {
            const match = copyrightMatch.match(/^{copyright:\s*(.+)}/);
            copyrightInput.value = match[1].trim();
        } else if (metaCopyrightMatch && copyrightInput) {
            const match = metaCopyrightMatch.match(/^{meta:\s*copyright\s+(.+)}/);
            copyrightInput.value = match[1].trim();
        }
        
        // Extract key
        const keyMatch = metadataLines.find(line => line.match(/^{key:\s*(.+)}/));
        if (keyMatch && originalKey) {
            const match = keyMatch.match(/^{key:\s*(.+)}/);
            originalKey.value = match[1].trim();
            updateDiatonicChords();
        }
        
        // Extract tempo
        const tempoMatch = metadataLines.find(line => line.match(/^{tempo:\s*([^}]+)}/));
        if (tempoMatch && bpmInput) {
            const match = tempoMatch.match(/^{tempo:\s*([^}]+)}/);
            bpmInput.value = match[1].trim().replace(/\s*bpm$/, '');
        }
        
        // Extract time
        const timeMatch = metadataLines.find(line => line.match(/^{time:\s*(.+)}/));
        if (timeMatch && timeInput) {
            const match = timeMatch.match(/^{time:\s*(.+)}/);
            timeInput.value = match[1].trim();
        }

        // Extract capo in both formats
        const capoMatch = metadataLines.find(line => line.match(/^{capo:\s*(.+)}/));
        const metaCapoMatch = metadataLines.find(line => line.match(/^{meta:\s*capo\s+(.+)}/));
        if (capoMatch && capoInput) {
            const match = capoMatch.match(/^{capo:\s*(.+)}/);
            capoInput.value = match[1].trim();
        } else if (metaCapoMatch && capoInput) {
            const match = metaCapoMatch.match(/^{meta:\s*capo\s+(.+)}/);
            capoInput.value = match[1].trim();
        }
    }
    
    // Call populateMetadataFields if editor has content (existing song)
    if (editor.value.trim()) {
        populateMetadataFields();
    }
    
    function parseChordPro(text) {
        // Basic ChordPro parsing
        let html = '<div class="preview-header">';
        
        // Parse metadata
        const title = text.match(/{title:\s*(.+)}/);
        const key = text.match(/{key:\s*(.+)}/);
        const tempo = text.match(/{tempo:\s*([^}]+)}/);
        const time = text.match(/{time:\s*(.+)}/);
        const ccli = text.match(/{ccli:\s*(.+)}/);
        const ccliLicense = text.match(/{ccli_license:\s*(.+)}/);
        
        // Parse capo in both formats
        let capo = null;
        const capoMatch = text.match(/{capo:\s*(.+)}/);
        const metaCapoMatch = text.match(/{meta:\s*capo\s+(.+)}/);
        if (capoMatch) {
            capo = capoMatch[1];
        } else if (metaCapoMatch) {
            capo = metaCapoMatch[1];
        }

        // Parse artist in both formats
        let artist = null;
        const artistMatch = text.match(/{artist:\s*(.+)}/);
        const metaArtistMatch = text.match(/{meta:\s*artist\s+(.+)}/);
        if (artistMatch) {
            artist = artistMatch[1];
        } else if (metaArtistMatch) {
            artist = metaArtistMatch[1];
        }

        // Parse copyright in both formats
        let copyright = null;
        const copyrightMatch = text.match(/{copyright:\s*(.+)}/);
        const metaCopyrightMatch = text.match(/{meta:\s*copyright\s+(.+)}/);
        if (copyrightMatch) {
            copyright = copyrightMatch[1];
        } else if (metaCopyrightMatch) {
            copyright = metaCopyrightMatch[1];
        }

        // Parse header in both formats
        let header = null;
        const headerMatch = text.match(/{header:\s*(.+)}/);
        const metaHeaderMatch = text.match(/{meta:\s*header\s+(.+)}/);
        if (headerMatch) {
            header = headerMatch[1];
        }

        // Parse footer in both formats
        let footer = null;
        const footerMatch = text.match(/{footer:\s*(.+)}/);
        const metaFooterMatch = text.match(/{meta:\s*footer\s+(.+)}/);
        if (footerMatch) {
            footer = footerMatch[1];
        }

        // Group metadata for displaying in the preview
        let songMeta = [];
        if (key) songMeta.push(`Key: ${key[1]}`);
        if (tempo) songMeta.push(`${tempo[1]} bpm`);
        if (time) songMeta.push(`${time[1]}`);

        let headerMeta = [];
        if (artist) headerMeta.push(`${artist}`);
        if (header) headerMeta.push(`${header}`);
        
        let footerMeta = [];
        if (ccli) footerMeta.push(`CCLI Song # ${ccli[1]}`);
        if (copyright) footerMeta.push(`${copyright}`);
        if (footer) footerMeta.push(`${footer}`);
        if (ccliLicense) footerMeta.push(`CCLI License # ${ccliLicense[1]}`);

        if (title) {
            html += `<div class="preview-title">${title[1]}</div>`;
        }

        if (capo) {
            html += `<div class="preview-capo">Capo ${capo}</div>`;
        }

        if (headerMeta.length > 0) {
            html += `<div class="preview-headermeta">${headerMeta.join('<br />')}</div>`;
        }
        
        if (songMeta.length > 0) {
            html += `<div class="preview-meta">${songMeta.join(' | ')}</div>`;
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
            
            // Check for page break directives
            if (line.trim() === '{new_page}' || line.trim() === '{np}') {
                html += '<div class="new-page"></div>';
                continue;
            }
            
            if (line.match(/{.*}/)) continue; // Skip metadata lines
            
            // Check for section tags
            const sectionTags = [
                'Verse 1', 'Verse 2', 'Verse 3', 'Verse 4', 'Verse 5',
                'Chorus', 'Chorus 1', 'Chorus 2', 'Tag:', 'Bridge',
                'Pre-Chorus', 'Post-Chorus', 'Intro', 'Outro',
                'Interlude', 'Ending:'
            ];

            if (sectionTags.some(tag => line.startsWith(tag))) {
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

        if (footerMeta.length > 0) {
            html += `<div class="preview-footer">${footerMeta.join('<br />')}</div>`;
        }

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
        const metadataOrder = ['title', 'artist', 'key', 'capo', 'tempo', 'time', 'copyright'];
        
        // Remove existing metadata line if it exists
        content = content.replace(new RegExp(`{${type}:[^}]*}\n?`), '');
        content = content.replace(new RegExp(`{meta:\\s*${type}\\s+[^}]*}\n?`), '');
        
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
            
            // Check for meta format
            const metaMatch = line.match(/^{meta:\s*(\w+)\s+([^}]*)}/);
            if (metaMatch) {
                metadataObj[metaMatch[1]] = metaMatch[2];
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

    artistInput.addEventListener('input', function() {
        updateMetadata('artist', this.value);
    });

    copyrightInput.addEventListener('input', function() {
        updateMetadata('copyright', this.value);
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
            // Update diatonic chords to match the new key's notation
            updateDiatonicChords();
        }
        
        if (autoRefresh.checked) {
            updatePreview();
        }
        
        // Update diatonic chord buttons when key changes
        updateDiatonicChords();
    });

    // Add capo input event listener
    capoInput.addEventListener('input', function() {
        // Only update if the value is empty or a valid number between 0 and 12
        const value = this.value.trim();
        if (value === '' || (parseInt(value) >= 0 && parseInt(value) <= 12)) {
            // If value is 0 or empty, remove the capo metadata
            if (value === '0' || value === '') {
                // Remove existing capo metadata line if it exists
                let content = editor.value;
                content = content.replace(/{capo:[^}]*}\n?/, '');
                content = content.replace(/{meta:\s*capo\s+[^}]*}\n?/, '');
                editor.value = content;
            } else {
                updateMetadata('capo', value);
            }
            
            if (autoRefresh.checked) {
                updatePreview();
            }
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
                '{artist: }',
                `{key: ${DEFAULT_KEY}}`,
                `{tempo: ${DEFAULT_TEMPO}}`,
                `{time: ${DEFAULT_TIME}}`,
                '{copyright: }',
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
            const songId = document.getElementById('songId')?.value;
            
            if (songId) {
                // If we have a song ID, open the preview in a new window
                window.open(`/songs/preview/${songId}`, '_blank');
            } else {
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
            }
        });
    }
    
    // Function to insert a page break at the cursor position
    function insertPageBreak() {
        const start = editor.selectionStart;
        const end = editor.selectionEnd;
        const text = editor.value;
        
        // Insert the page break directive without an extra line break
        const newText = text.substring(0, start) + '{new_page}' + text.substring(end);
        editor.value = newText;
        
        // Move cursor after the inserted page break
        const newCursorPos = start + '{new_page}'.length;
        editor.setSelectionRange(newCursorPos, newCursorPos);
        editor.focus();
        
        // Trigger the input event to update preview
        editor.dispatchEvent(new Event('input'));
    }
    
    // Add keyboard shortcut for inserting page break (Ctrl+Enter or Cmd+Enter)
    document.addEventListener('keydown', function(e) {
        if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') {
            e.preventDefault();
            insertPageBreak();
        }
        
        // Add keyboard shortcuts for diatonic chords (Ctrl+1 through Ctrl+7)
        if ((e.ctrlKey || e.metaKey) && e.key >= '1' && e.key <= '7') {
            e.preventDefault();
            const key = originalKey.value;
            const chords = getDiatonicChords(key);
            const index = parseInt(e.key) - 1; // Convert 1-7 to 0-6 index
            
            if (index >= 0 && index < chords.length) {
                insertChordAtCursor(chords[index]);
            }
        }
    });
    
    // Add page break button click handler
    const pageBreakButton = document.getElementById('pageBreakButton');
    if (pageBreakButton) {
        pageBreakButton.addEventListener('click', insertPageBreak);
    }
    
    // Initialize diatonic chord buttons
    updateDiatonicChords();
}); 
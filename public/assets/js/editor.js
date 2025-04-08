// Add to the top with other const declarations
const capoInput = document.getElementById('capoInput');

// Add to the populateMetadataFields function
// Extract capo in both formats
const capoMatch = content.match(/{capo:\s*(.+)}/);
const metaCapoMatch = content.match(/{meta:\s*capo\s+(.+)}/);
if (capoMatch && capoInput) {
    capoInput.value = capoMatch[1].trim();
} else if (metaCapoMatch && capoInput) {
    capoInput.value = metaCapoMatch[1].trim();
}

// Add to the updateMetadata function's metadataOrder array
const metadataOrder = ['title', 'artist', 'key', 'tempo', 'time', 'copyright', 'capo'];

// Add event listener for capo input
capoInput.addEventListener('input', function() {
    // Only update if the value is empty or a valid number
    if (this.value === '' || (parseInt(this.value) >= 0 && parseInt(this.value) <= 12)) {
        updateMetadata('capo', this.value);
    }
});

// Add to the initializeNewSong function's initialMetadata array
const initialMetadata = [
    '{title: }',
    '{artist: }',
    `{key: ${DEFAULT_KEY}}`,
    `{tempo: ${DEFAULT_TEMPO}}`,
    `{time: ${DEFAULT_TIME}}`,
    '{copyright: }',
    '{capo: }',
    '',
    ''
].join('\n'); 

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
    const capoInput = document.getElementById('capoInput');  // Add this line
    // ... rest of the variable declarations ...

    // Add this with the other metadata extraction in populateMetadataFields function
    // Extract capo in both formats
    const capoMatch = content.match(/{capo:\s*(.+)}/);
    const metaCapoMatch = content.match(/{meta:\s*capo\s+(.+)}/);
    if (capoMatch && capoInput) {
        capoInput.value = capoMatch[1].trim();
    } else if (metaCapoMatch && capoInput) {
        capoInput.value = metaCapoMatch[1].trim();
    }

    // Add this with the other metadata order
    const metadataOrder = ['title', 'artist', 'key', 'tempo', 'time', 'copyright', 'capo'];

    // Add this with the other event listeners
    capoInput.addEventListener('input', function() {
        // Only update if the value is empty or a valid number
        if (this.value === '' || (parseInt(this.value) >= 0 && parseInt(this.value) <= 12)) {
            updateMetadata('capo', this.value);
        }
    });
}); 
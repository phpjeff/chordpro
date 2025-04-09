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
        <div class="input-group mb-2">
            <span class="input-group-text">Transpose To</span>
            <select class="form-select" id="transposeKey">
                <?php
                $naturalKeys = ['A', 'B', 'C', 'D', 'E', 'F', 'G'];
                $sharpKeys = ['A#', 'C#', 'D#', 'F#', 'G#'];
                $flatKeys = ['Bb', 'Db', 'Eb', 'Gb', 'Ab'];
                ?>
                <optgroup label="Natural">
                    <?php foreach ($naturalKeys as $key): ?>
                        <option value="<?= $key ?>"><?= $key ?></option>
                    <?php endforeach; ?>
                </optgroup>
                <optgroup label="Sharp Keys">
                    <?php foreach ($sharpKeys as $key): ?>
                        <option value="<?= $key ?>"><?= $key ?></option>
                    <?php endforeach; ?>
                </optgroup>
                <optgroup label="Flat Keys">
                    <?php foreach ($flatKeys as $key): ?>
                        <option value="<?= $key ?>"><?= $key ?></option>
                    <?php endforeach; ?>
                </optgroup>
            </select>
        </div>
        <div class="input-group">
            <span class="input-group-text">Capo</span>
            <input type="number" class="form-control" id="capoValue" min="0" max="12" value="0">
        </div>
    </div>
    
    <div class="print-controls no-print">
        <button class="btn btn-primary" onclick="window.print()">Print</button>
        <button class="btn btn-secondary" onclick="closePreview()">Close</button>
    </div>
    <div class="preview-content" id="preview">
        <?= $content ?>
    </div>

    <!-- Preview JavaScript -->
    <script src="<?= base_url('assets/js/preview.js') ?>"></script>
</body>
</html> 
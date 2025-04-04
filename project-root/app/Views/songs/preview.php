<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Song Preview' ?></title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @media print {
            @page {
                margin: 1.5cm;
            }
            body {
                font-size: 12pt;
            }
            .preview-content {
                max-width: none !important;
            }
            .no-print {
                display: none !important;
            }
        }
        body {
            background: white;
            font-family: monospace;
        }
        .preview-content {
            max-width: 800px;
            margin: 2rem auto;
            padding: 0 1rem;
        }
        .preview-header {
            text-align: left;
            margin-bottom: 2rem;
            font-family: system-ui, -apple-system, sans-serif;
        }
        .preview-title {
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }
        .preview-meta {
            color: #666;
            font-style: italic;
        }
        .verse {
            margin-bottom: 1.5rem;
        }
        .section-name {
            font-weight: bold;
            margin-bottom: 0.5rem;
            color: #666;
            font-family: system-ui, -apple-system, sans-serif;
        }
        .line {
            margin-bottom: 0.5rem;
            position: relative;
            padding-top: 1.5em;
            white-space: pre;
            line-height: 1.5;
            font-size: 14px;
            color: #333;
        }
        .chord {
            position: absolute;
            top: 0;
            color: #000;
            font-weight: 900;
            font-size: 14px;
            line-height: 1;
            white-space: pre;
        }
        .print-controls {
            position: fixed;
            top: 1rem;
            right: 1rem;
            background: white;
            padding: 1rem;
            border-radius: 0.5rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            font-family: system-ui, -apple-system, sans-serif;
        }
    </style>
</head>
<body>
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
    </script>
</body>
</html> 
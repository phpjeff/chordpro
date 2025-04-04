<?php

namespace App\Controllers;

use App\Models\SongModel;
use CodeIgniter\HTTP\ResponseInterface;

class Songs extends BaseController
{
    protected $songModel;

    public function __construct()
    {
        $this->songModel = new SongModel();
    }

    public function index()
    {
        $data['songs'] = $this->songModel->findAll();
        return view('songs/index', $data);
    }

    public function create($id = null)
    {
        $data = [
            'song' => null,
            'title' => 'Create New Song',
            'error' => null
        ];

        if ($id !== null) {
            $song = $this->songModel->find($id);
            if ($song) {
                $data['song'] = $song;
                $data['title'] = 'Edit Song: ' . $song['title'];
            } else {
                $data['error'] = 'Song not found';
            }
        }

        return view('songs/create', $data);
    }

    public function save()
    {
        // Validate request
        if (!$this->request->is('post')) {
            return $this->response->setStatusCode(405)->setJSON([
                'success' => false,
                'message' => 'Method not allowed'
            ]);
        }

        // Get the POST data
        $data = [
            'title' => $this->request->getPost('title'),
            'original_key' => $this->request->getPost('original_key'),
            'bpm' => $this->request->getPost('bpm'),
            'time' => $this->request->getPost('time'),
            'chordpro' => $this->request->getPost('chordpro')
        ];

        // If we're updating an existing song, add the ID
        $id = $this->request->getPost('id');
        if ($id) {
            $data['id'] = $id;
        }

        // Try to save the song
        if ($this->songModel->save($data)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => $id ? 'Song updated successfully' : 'Song saved successfully',
                'id' => $id ?? $this->songModel->getInsertID()
            ]);
        }

        // If validation failed, return the errors
        return $this->response->setStatusCode(400)->setJSON([
            'success' => false,
            'message' => 'Validation failed',
            'errors' => $this->songModel->errors()
        ]);
    }

    public function delete($id = null)
    {
        if ($id === null) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => 'No song ID provided'
            ]);
        }

        if ($this->songModel->delete($id)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Song deleted successfully'
            ]);
        }

        return $this->response->setStatusCode(500)->setJSON([
            'success' => false,
            'message' => 'Failed to delete song'
        ]);
    }

    public function preview($id = null)
    {
        if ($id) {
            // Fetch song data by ID
            $songModel = new \App\Models\SongModel();
            $song = $songModel->find($id);
            
            if (!$song) {
                return redirect()->to('/songs');
            }
            
            // Parse the ChordPro content
            $content = $this->parseChordPro($song['chordpro']);
            
            return view('songs/preview', [
                'content' => $content,
                'title' => $song['title']
            ]);
        }
        
        // Handle POST request with direct content
        $content = $this->request->getPost('content');
        $title = $this->request->getPost('title');
        
        if (!$content) {
            return redirect()->to('/songs');
        }
        
        return view('songs/preview', [
            'content' => $content,
            'title' => $title
        ]);
    }
    
    private function parseChordPro($text)
    {
        // Basic ChordPro parsing
        $html = '<div class="preview-header">';
        
        // Parse metadata
        $title = preg_match('/{title:\s*(.+)}/', $text, $matches) ? $matches[1] : null;
        $key = preg_match('/{key:\s*(.+)}/', $text, $matches) ? $matches[1] : null;
        $tempo = preg_match('/{tempo:\s*([^}]+)}/', $text, $matches) ? $matches[1] : null;
        $time = preg_match('/{time:\s*(.+)}/', $text, $matches) ? $matches[1] : null;
        
        if ($title) {
            $html .= "<div class=\"preview-title\">{$title}</div>";
        }
        
        $meta = [];
        if ($key) $meta[] = "Key: {$key}";
        if ($tempo) $meta[] = "{$tempo} bpm";
        if ($time) $meta[] = "{$time}";
        
        if (!empty($meta)) {
            $html .= "<div class=\"preview-meta\">" . implode(', ', $meta) . "</div>";
        }
        
        $html .= '</div>';
        
        // Parse content
        $lines = explode("\n", $text);
        $inVerse = false;
        
        foreach ($lines as $line) {
            if (trim($line) === '') {
                $html .= '<br>';
                continue;
            }
            
            if (preg_match('/{.*}/', $line)) continue; // Skip metadata lines
            
            if (preg_match('/^\[(Verse|Chorus|Bridge).*\]$/', $line, $matches)) {
                if ($inVerse) $html .= '</div>';
                $sectionName = $matches[1];
                $html .= "<div class=\"verse\"><div class=\"section-name\">{$sectionName}</div>";
                $inVerse = true;
                continue;
            }
            
            // Parse chords and lyrics
            $processedLine = $line;
            $processedLine = preg_replace('/\[([^\]]+)\]/', '<span class="chord">$1</span>', $processedLine);
            
            $html .= "<div class=\"line\">{$processedLine}</div>";
        }
        
        if ($inVerse) $html .= '</div>';
        return $html;
    }
} 
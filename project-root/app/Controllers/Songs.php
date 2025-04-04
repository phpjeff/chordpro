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

    public function preview()
    {
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
} 
<?php

namespace App\Models;

use CodeIgniter\Model;

class SongModel extends Model
{
    protected $table = 'songs';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $allowedFields = ['title', 'original_key', 'bpm', 'time', 'chordpro', 'user_id'];
    
    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'timestamp';
    protected $updatedField = 'update_timestamp';
    
    // Validation
    protected $validationRules = [
        'title' => 'required|min_length[3]|max_length[255]',
        'original_key' => 'required|max_length[7]',
        'bpm' => 'permit_empty|integer|greater_than[0]|less_than[300]',
        'time' => 'permit_empty|max_length[5]',
        'chordpro' => 'required',
        'user_id' => 'required|integer'
    ];
    
    protected $validationMessages = [
        'title' => [
            'required' => 'A song title is required',
            'min_length' => 'The title must be at least 3 characters long',
            'max_length' => 'The title cannot exceed 255 characters'
        ],
        'original_key' => [
            'required' => 'Please select a key',
            'max_length' => 'Key cannot exceed 7 characters'
        ],
        'bpm' => [
            'integer' => 'BPM must be a whole number',
            'greater_than' => 'BPM must be greater than 0',
            'less_than' => 'BPM must be less than 300'
        ],
        'chordpro' => [
            'required' => 'ChordPro content is required'
        ],
        'user_id' => [
            'required' => 'User ID is required',
            'integer' => 'User ID must be a valid integer'
        ]
    ];
    
    protected $skipValidation = false;
    protected $cleanValidationRules = true;
} 
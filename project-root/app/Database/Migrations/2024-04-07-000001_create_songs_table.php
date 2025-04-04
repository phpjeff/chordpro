<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSongsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'title' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'original_key' => [
                'type' => 'VARCHAR',
                'constraint' => 7,
            ],
            'bpm' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
            ],
            'time' => [
                'type' => 'VARCHAR',
                'constraint' => 5,
                'null' => true,
            ],
            'chordpro' => [
                'type' => 'TEXT',
            ],
            'timestamp' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'update_timestamp' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('songs');
    }

    public function down()
    {
        $this->forge->dropTable('songs');
    }
} 
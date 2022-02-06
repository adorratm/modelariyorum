<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class OauthAccessTokens extends Migration
{
    public function up()
    {
        // Create Table
        $this->forge->addField([
            'access_token' => [
                'type' => 'VARCHAR',
                'constraint' => 40
            ]
        ]);
    }

    public function down()
    {
        // Drop Table
        $this->forge->dropTable("oauth_access_tokens",true);
    }
}

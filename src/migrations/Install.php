<?php

namespace nilsenpaul\bitlyconnect\migrations;

use craft\db\Migration;

class Install extends Migration
{
    public function safeUp()
    {
        if (!$this->db->tableExists('{{%bitlyconnect_links}}')) {
            $this->createTable('{{%bitlyconnect_links}}', [
                'id' => $this->primaryKey(),
                'longUrl' => $this->string()->notNull(),
                'bitlyId' => $this->string()->notNull(),
                'link' => $this->string()->notNull(),
                'group' => $this->string(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid' => $this->uid()
            ]);

            $this->addForeignKey(
                $this->db->getForeignKeyName('{{%bitlyconnect_links}}', 'id'),
                '{{%bitlyconnect_links}}',
                'id',
                '{{%elements}}',
                'id',
                'CASCADE',
                null
            );
        }
    }

    public function safeDown()
    {
        $this->dropTableIfExists('{{%bitlyconnect_links}}');
    }
}

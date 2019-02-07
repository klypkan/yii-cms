<?php

use yii\db\Schema;
use yii\db\Migration;

class m181005_190102_permalinks_init extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%permalinks}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
            'route' => $this->string()->notNull(),
            'site_id' => $this->integer()->notNull(),
        ], $tableOptions);
        $this->createIndex(
            'idx-permalinks-site_id',
            'permalinks',
            'site_id'
        );
        $this->addForeignKey(
            'fk-permalinks-site_id',
            'permalinks',
            'site_id',
            'sites',
            'id',
            'CASCADE'
        );
        $this->createIndex(
            'idx-permalinks-name-site_id',
            'permalinks',
            ['name','site_id'],
            true
        );
    }

    public function down()
    {
        $this->dropIndex(
            'idx-permalinks-site_id',
            'permalinks'
        );
        $this->dropForeignKey(
            'fk-permalinks-site_id',
            'permalinks'
        );
        $this->dropIndex(
            'idx-permalinks-name-site_id',
            'permalinks'
        );
        $this->dropTable('{{%permalinks}}');
    }
}

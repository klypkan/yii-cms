<?php

use yii\db\Schema;
use yii\db\Migration;

class m181005_190103_posts_init extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%posts}}', [
            'id' => $this->primaryKey(),
            'type' => $this->smallInteger()->notNull(),
            'status' => $this->smallInteger()->notNull(),
            'date' => $this->dateTime(),
            'title' => $this->string()->notNull(),
            'content' => $this->text(),
            'permalink_id' => $this->integer(),
            'parent_id' => $this->integer(),
            'site_id' => $this->integer()->notNull(),
        ], $tableOptions);
        $this->createIndex(
            'idx-posts-permalink_id',
            'posts',
            'permalink_id',
            true
        );
        $this->addForeignKey(
            'fk-posts-permalink_id',
            'posts',
            'permalink_id',
            'permalinks',
            'id',
            'CASCADE'
        );
        $this->createIndex(
            'idx-posts-site_id',
            'posts',
            'site_id'
        );
        $this->addForeignKey(
            'fk-posts-site_id',
            'posts',
            'site_id',
            'sites',
            'id',
            'CASCADE'
        );
    }

    public function down()
    {
        $this->dropIndex(
            'idx-posts-permalink_id',
            'posts'
        );
        $this->dropForeignKey(
            'fk-posts-permalink_id',
            'posts'
        );
        $this->dropIndex(
            'idx-posts-site_id',
            'posts'
        );
        $this->dropForeignKey(
            'fk-posts-site_id',
            'posts'
        );
        $this->dropTable('{{%posts}}');
    }
}

<?php

use yii\db\Schema;
use yii\db\Migration;

class m181005_190104_meta_init extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%post_meta}}', [
            'id' => $this->primaryKey(),
            'type' => $this->smallInteger()->notNull(),
            'name' => $this->string()->notNull(),
            'value' => $this->text(),
            'description' => $this->text(),
            'post_meta_order' => $this->integer()->notNull(),
            'count' => $this->integer(),
            'parent_id' => $this->integer(),
            'site_id' => $this->integer()->notNull()
        ], $tableOptions);
        $this->createIndex(
            'idx-post_meta-site_id',
            'post_meta',
            'site_id'
        );
        $this->addForeignKey(
            'fk-post_meta-site_id',
            'post_meta',
            'site_id',
            'sites',
            'id',
            'CASCADE'
        );
        $this->createIndex(
            'idx-post_meta-type-name-parent_id-site_id',
            'post_meta',
            ['type','name', 'parent_id', 'site_id'],
            true
        );

        $this->createTable('{{%post_meta_relationships}}', [
            'id' => $this->primaryKey(),
            'post_meta_id' => $this->integer()->notNull(),
            'post_id' => $this->integer()->notNull()
        ], $tableOptions);
        $this->createIndex(
            'idx-post_meta_relationships-post_meta_id',
            'post_meta_relationships',
            'post_meta_id'
        );
        $this->addForeignKey(
            'fk-post_meta_relationships-post_meta_id',
            'post_meta_relationships',
            'post_meta_id',
            'post_meta',
            'id',
            'CASCADE'
        );
        $this->createIndex(
            'idx-post_meta_relationships-post_id',
            'post_meta_relationships',
            'post_id'
        );
        $this->addForeignKey(
            'fk-post_meta_relationships-post_id',
            'post_meta_relationships',
            'post_id',
            'posts',
            'id',
            'CASCADE'
        );
    }

    public function down()
    {
        $this->dropIndex(
            'idx-post_meta-site_id',
            'post_meta'
        );
        $this->dropForeignKey(
            'fk-post_meta-site_id',
            'post_meta'
        );
        $this->dropIndex(
            'idx-post_meta-type-name-parent_id-site_id',
            'post_meta'
        );
        $this->dropTable('{{%post_meta}}');

        $this->dropIndex(
            'idx-post_meta_relationships-post_meta_id',
            'post_meta_relationships'
        );
        $this->dropForeignKey(
            'fk-post_meta_relationships-post_meta_id',
            'post_meta_relationships'
        );
        $this->dropIndex(
            'idx-post_meta_relationships-post_id',
            'post_meta_relationships'
        );
        $this->dropForeignKey(
            'fk-post_meta_relationships-post_id',
            'post_meta_relationships'
        );
        $this->dropTable('{{%post_meta_relationships}}');
    }
}

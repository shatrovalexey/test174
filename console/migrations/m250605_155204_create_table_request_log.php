<?php

use yii\db\Migration;

class m250605_155204_create_table_request_log extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%request_log}}', [
            'id' => $this->primaryKey(),
            'request_id' => $this->integer()->notNull(),
            'request' => $this->text(),
            'response' => $this->text(),
            'status' => $this->string()->notNull()->defaultValue('new'),
            'created_at' => $this->timestamp()->notNull()->defaultExpression('now()'),
            'updated_at' => $this->timestamp()->notNull()->defaultExpression('now()'),
        ]);

        $this->createIndex('{{%idx-request_log-status}}', '{{%request_log}}', 'status');
        $this->addForeignKey('{{%fk-request_log-request_id}}', '{{%request_log}}', 'request_id', '{{%request}}', 'id', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('{{%idx-request_log-status}}', '{{%request_log}}');
        $this->dropTable('{{%request_log}}');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250605_155204_create_table_request_log cannot be reverted.\n";

        return false;
    }
    */
}

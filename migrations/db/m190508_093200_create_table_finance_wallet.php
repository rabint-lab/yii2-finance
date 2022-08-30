<?php

use yii\db\Migration;

class m190508_093200_create_table_finance_wallet extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%finance_wallet}}', [
            'id' => $this->integer(11)->notNull()->append('AUTO_INCREMENT PRIMARY KEY')->comment('شناسه'),
            'created_at' => $this->integer(11)->notNull()->comment('زمان درخواست'),
            'user_id' => $this->integer(11)->comment('ذینفغ'),
            'amount' => $this->integer(11)->notNull()->comment('مبلغ'),
            'transactioner' => $this->integer(11)->comment('انجام دهنده'),
            'transactioner_ip' => $this->string(255)->notNull()->comment('آی پی انجام دهده'),
            'bank_transaction_id' => $this->integer(11)->Null()->unique()->comment('شناسه تراکنش بانک'),
            'description' => $this->string(255)->notNull()->comment('توضیحات'),
            'metadata' => $this->string(255)->notNull()->comment('اطلاعات متا'),
        ], $tableOptions);

        $this->addForeignKey('fk_finance_walet_transactioner_user_id', '{{%finance_wallet}}', 'transactioner', '{{%user}}', 'id', 'SET NULL', 'CASCADE');
        $this->addForeignKey('fk_finance_walet_user_user_id', '{{%finance_wallet}}', 'user_id', '{{%user}}', 'id', 'SET NULL', 'CASCADE');

        //ALTER TABLE `finance_wallet` ADD `bank_transaction_id` INT NULL DEFAULT NULL AFTER `transactioner_ip`, ADD UNIQUE `bank_transaction_id_uqcheck` (`bank_transaction_id`);
    }

    public function down()
    {
        $this->dropTable('{{%finance_wallet}}');
    }
}

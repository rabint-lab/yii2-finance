<?php

use yii\db\Migration;

class m190508_093200_create_table_finance_wallet_connection extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%finance_wallet_connection}}', [
            'id' => $this->integer(11)->notNull()->append('AUTO_INCREMENT PRIMARY KEY')->comment('شناسه'),
            'user_id' => $this->integer(11)->comment('کاربر'),
            'provider' => $this->integer(11)->comment('ارايه دهنده'),
            'expire_date' => $this->integer(11)->unsigned()->comment('تاریخ انقضاء'),
            'created_at' => $this->integer(11)->unsigned()->notNull()->comment('زمان درخواست'),
        ], $tableOptions);

        $this->addForeignKey('fk_finance_walet_user_user_id', '{{%finance_wallet_connection}}', 'user_id', '{{%user}}', 'id', 'CASCADE', 'CASCADE');

        //ALTER TABLE `finance_wallet` ADD `bank_transaction_id` INT NULL DEFAULT NULL AFTER `transactioner_ip`, ADD UNIQUE `bank_transaction_id_uqcheck` (`bank_transaction_id`);
    }

    public function down()
    {
        $this->dropTable('{{%finance_wallet_connection}}');
    }
}

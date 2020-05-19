<?php

use yii\db\Migration;

class m190508_093200_create_table_finance_transactions extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%finance_transactions}}', [
            'id' => $this->integer(11)->notNull()->append('AUTO_INCREMENT PRIMARY KEY')->comment('شناسه'),
            'created_at' => $this->integer(11)->notNull()->comment('زمان درخواست'),
            'transactioner' => $this->integer(11)->comment('انجام دهنده'),
            'amount' => $this->integer(11)->notNull()->comment('مبلغ'),
            'status' => $this->integer(11)->notNull()->comment('وضعیت'),
            'gateway' => $this->integer(11)->comment('درگاه'),
            'gateway_reciept' => $this->string(255)->comment('رسید درگاه'),
            'gateway_meta' => $this->string(255)->comment('اطلاعات درگاه'),
            'transactioner_ip' => $this->string(255)->notNull()->comment('آی پی انجام دهنده'),
            'internal_reciept' => $this->string(255)->notNull()->comment('سفره داخلی'),
            'token' => $this->string(255)->notNull()->comment('کلید'),
            'return_url' => $this->string(255)->notNull()->comment('لینک بازگشت'),
            'additional_rows' => $this->text()->notNull()->comment('اطلاعات فاکتور'),
            'metadata' => $this->string(255)->comment('متادیتا'),
        ], $tableOptions);

        $this->addForeignKey('fk_finance_transaction_user_id', '{{%finance_transactions}}', 'transactioner', '{{%user}}', 'id', 'SET NULL', 'CASCADE');
    }

    public function down()
    {
        $this->dropTable('{{%finance_transactions}}');
    }
}

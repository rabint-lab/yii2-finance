<?php

use yii\db\Migration;

/**
 * Class m211015_171721_add_tabel_offline_pay
 */
class m211015_171721_add_tabel_offline_pay extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%finance_offline_pay}}', [
            'id' => $this->integer(11)->notNull()->append('AUTO_INCREMENT PRIMARY KEY')->comment('شناسه'),
            'user_id' => $this->integer(11),
            'transaction_id' => $this->integer(11),
            'callback' => $this->text(),
            'status' => $this->integer(11)->defaultValue(0)->comment('وضعیت'),
            'amount' => $this->string(50)->comment('مبلغ'),
            'image' => $this->string(255)->comment('تصویر فیش'),
            'date_pay' => $this->integer(11)->comment('تاریخ پرداخت'),
            'tracking_cod' => $this->string(45)->comment('شماره پیگیری'),
            'description' => $this->text()->comment('توضیحات'),
            'created_at' => $this->integer(11)->comment('تاریخ ایجاد'),
            'updated_at' => $this->integer(11)->comment('تاریخ تایید'),
        ]);
        $this->addForeignKey('fk_finance_offline_pay_transactioner_user_id', '{{%finance_offline_pay}}', 'user_id', '{{%user}}', 'id', 'SET NULL', 'CASCADE');
        $this->addForeignKey('fk_finance_offline_pay_transaction_id', '{{%finance_offline_pay}}', 'transaction_id', '{{%finance_transactions}}', 'id', 'SET NULL', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%finance_offline_pay}}');
        echo "m211015_171721_add_tabel_offline_pay cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211015_171721_add_tabel_offline_pay cannot be reverted.\n";

        return false;
    }
    */
}

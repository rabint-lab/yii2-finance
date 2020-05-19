<?php

use yii\db\Migration;

class m190508_093200_create_table_finance_draft extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%finance_draft}}', [
            'id' => $this->integer(11)->notNull()->append('AUTO_INCREMENT PRIMARY KEY')->comment('شناسه'),
            'user_id' => $this->integer(11)->comment('کاربر'),
            'checker_id' => $this->integer(11)->comment('بررسی کننده'),
            'title' => $this->string(255)->comment('عنوان'),
            'bank' => $this->string(45)->notNull()->comment('بانک'),
            'form_id' => $this->string(45)->notNull()->comment('شماره فیش'),
            'created_at' => $this->integer(11)->comment('تاریخ ایجاد'),
            'updated_at' => $this->string(45)->comment('تاریخ تایید'),
            'status' => $this->tinyInteger(1)->comment('وضعیت'),
            'description' => $this->text()->comment('توضیحات'),
            'check_url' => $this->string(255)->comment('تصویر فیش'),
        ], $tableOptions);

        $this->addForeignKey('fk_finance_draft_user1', '{{%finance_draft}}', 'user_id', '{{%user}}', 'id', 'SET NULL', 'CASCADE');
        $this->addForeignKey('fk_finance_draft_user2', '{{%finance_draft}}', 'checker_id', '{{%user}}', 'id', 'SET NULL', 'CASCADE');
    }

    public function down()
    {
        $this->dropTable('{{%finance_draft}}');
    }
}

<?php

use yii\db\Migration;

/**
 * Class m211015_171721_add_tabel_offline_pay
 */
class m220803_171721_seed_create_account_user extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('{{%user}}', [
            'username' => 'account',
            'email' => 'account@example.com',
            'password_hash' => Yii::$app->getSecurity()->generatePasswordHash(Yii::$app->getSecurity()->generateRandomString(32)),
            'auth_key' => Yii::$app->getSecurity()->generateRandomString(),
            'access_token' => Yii::$app->getSecurity()->generateRandomString(40),
            'status' => \rabint\user\models\User::STATUS_NOT_ACTIVE,
            'created_at' => time(),
            'updated_at' => time()
        ]);
        $this->update('{{%user}}', ['id' => '0'],['username' => 'account']);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('{{%user}}', [
            'id' => ['0']
        ]);
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

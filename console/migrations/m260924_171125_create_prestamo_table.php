<?php

use yii\db\Migration;

/**
 * Crea la tabla `prestamo`, donde cada registro pertenece a un usuario (user_id).
 */
class m260924_171125_create_prestamo_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%prestamo}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'nombre' => $this->string(150)->notNull(),
            'fecha_primera_cuota' => $this->date()->notNull(),
            'monto_cuota' => $this->decimal(12, 2)->notNull(),
            'cantidad_cuotas' => $this->integer()->notNull(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ]);

        $this->createIndex(
            'idx-prestamo-user_id',
            '{{%prestamo}}',
            'user_id'
        );

        $this->addForeignKey(
            'fk-prestamo-user_id',
            '{{%prestamo}}',
            'user_id',
            '{{%user}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-prestamo-user_id', '{{%prestamo}}');
        $this->dropIndex('idx-prestamo-user_id', '{{%prestamo}}');
        $this->dropTable('{{%prestamo}}');
    }
}

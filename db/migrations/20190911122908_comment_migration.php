<?php

use Phinx\Migration\AbstractMigration;

class CommentMigration extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    addCustomColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Any other destructive changes will result in an error when trying to
     * rollback the migration.
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change()
    {
        $table = $this->table('comment',['id' => false, 'primary_key' => ['id_comment']]);
        $table->addColumn('id_comment', 'integer', ['identity' => true])
                ->addColumn('body', 'string', ['limit' => 160])
                ->addColumn('parent', 'integer', ['null'=> true, 'comment' => 
                'en caso de que sea un comentario hijo, contendre el id del comentario
                padre'])
                ->addColumn('id_post', 'integer')
                ->addColumn('id_user', 'integer')
                ->addColumn('date_created','datetime')

                ->addForeignKey('id_post', //Atributo de la tabla 'commment'
                'post', // tabla hacia donde hace referencia
                'id_post', // atributo de la tabla 'post'
                ['delete'=> 'NO_ACTION', 'update'=> 'NO_ACTION'])
                ->addForeignKey('id_user', //Atributo de la tabla 'comment'
                'user', // tabla hacia donde hace referencia
                'id_user', // atributo de la tabla 'user'
                ['delete'=> 'NO_ACTION', 'update'=> 'NO_ACTION'])
                
                ->create();
    }
}

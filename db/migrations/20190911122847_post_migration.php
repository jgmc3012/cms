<?php

use Phinx\Migration\AbstractMigration;

class PostMigration extends AbstractMigration
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
        // create the table
        $table = $this->table('post',['id' => false, 'primary_key' => ['id_post']]);
        $table->addColumn('id_post', 'integer', ['identity' => true])
                ->addColumn('body', 'text')
                ->addColumn('id_owner','integer')
                ->addColumn('title','string',['limit' => 45])
                ->addColumn('date_created','datetime')
                ->addColumn('date_modified','datetime')
                ->addColumn('published','binary')
                ->addColumn('visits','integer', ['default' => 0])
                ->addForeignKey('id_owner', //Atributo de la tabla post
                                'user', // tabla hacia donde hace referencia
                                'id_user', // atributo de la tabla user
                                ['delete'=> 'NO_ACTION', 'update'=> 'NO_ACTION'])

                ->create();

    }
}

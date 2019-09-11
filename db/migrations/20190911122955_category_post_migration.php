<?php

use Phinx\Migration\AbstractMigration;

class CategoryPostMigration extends AbstractMigration
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
        $table = $this->table('category_post',['id' => false, 'primary_key' => ['id_post',
            'id_category']]);
        $table->addColumn('id_post', 'integer')
                ->addColumn('id_category', 'integer')  

                ->addForeignKey('id_post', //Atributo de la tabla 'category_post'
                'post', // tabla hacia donde hace referencia
                'id_post', // atributo de la tabla 'post'
                ['delete'=> 'NO_ACTION', 'update'=> 'NO_ACTION'])
                ->addForeignKey('id_category', //Atributo de la tabla 'category_post'
                'category', // tabla hacia donde hace referencia
                'id_category', // atributo de la tabla 'category'
                ['delete'=> 'NO_ACTION', 'update'=> 'NO_ACTION'])
                
                ->create();

    }
}

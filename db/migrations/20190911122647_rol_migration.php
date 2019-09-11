<?php

use Phinx\Migration\AbstractMigration;

class RolMigration extends AbstractMigration
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
        $table = $this->table('rol',['id' => false, 'primary_key' => ['id_rol']]);
        $table->addColumn('id_rol', 'integer')
                ->addColumn('name_rol', 'string', ['limit' => 20])
                
                ->create();
        $rows = [
            [
                'id_rol'    => 1,
                'name_rol'  => 'root'
            ],
            [
                'id_rol'    => 2,
                'name_rol'  => 'admin'
            ],
            [
                'id_rol'    => 3,
                'name_rol'  => 'editor'
            ],
            [
                'id_rol'    => 4,
                'name_rol'  => 'author'
            ],
            [
                'id_rol'    => 5,
                'name_rol'  => 'collabolator'
            ],
            [
                'id_rol'    => 6,
                'name_rol'  => 'subscriber'
            ]
        ];

        $this->table('rol')->insert($rows)->save();

        
    }
}

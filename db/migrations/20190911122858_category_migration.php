<?php

use Phinx\Migration\AbstractMigration;

class CategoryMigration extends AbstractMigration
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
        $table = $this->table('category',['id' => false, 'primary_key' => ['id_category']]);
        $table->addColumn('id_category', 'integer', ['identity' => true])
                ->addColumn('name', 'string',['limit' => 15])
                ->addColumn('category_active', 'binary',['default' => 1])
                ->addColumn('category_description', 'string',['limit' => 160,'null' => true])
                ->addColumn('category_background', 'string',['limit' => 40,'null' => true])
                ->addIndex(['name'], [
                    'unique' => true,
                    'name' => 'idx_categories_name'])

                ->create();
    }
}

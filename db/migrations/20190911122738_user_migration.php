<?php

use Phinx\Migration\AbstractMigration;

class UserMigration extends AbstractMigration
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
        $table = $this->table('user',['id' => false, 'primary_key' => ['id_user']]);
        $table->addColumn('id_user', 'integer', ['identity' => true])
                ->addColumn('first_name', 'string',['limit' => 20])
                ->addColumn('last_name','string',['limit' => 20])
                ->addColumn('id_rol', 'integer')
                ->addColumn('nickname', 'string',['limit' => 25])
                ->addColumn('email', 'string',['limit' => 30])
                ->addColumn('password', 'string',['limit' => 255])
                ->addColumn('avatar', 'string',['limit' => 40])
                ->addColumn('access_admin', 'binary', ['comment' => 'indica si el
                usuario esta o no habilidato para ingresar en el tablero de
                administradores', 'default' => 1])

                ->addIndex(['email'], [
                    'unique' => true,
                    'name' => 'idx_users_email'])
                ->addIndex(['nickname'], [
                    'unique' => true,
                    'name' => 'idx_users_nickname'])
                ->addForeignKey('id_rol', //Atributo de la tabla user
                                'rol', // tabla hacia donde hace referencia
                                'id_rol', // atributo de la tabla rol
                                ['delete'=> 'NO_ACTION', 'update'=> 'NO_ACTION'])

                ->create();

        $singleRow = [
            'id_user'       => 1,
            'first_name'    => 'super',
            'last_name'     => 'usuario',
            'id_rol'        => '1',
            'nickname'     => 'root',
            'email'         => 'root@cms.com',
            'password'      => password_hash('SuperPass', PASSWORD_DEFAULT),
            'avatar'        => '/img/users/default.png',
            'access_admin'  => 1
        ];

        $table = $this->table('user');
        $table->insert($singleRow);
        $table->saveData();
        
    }
}

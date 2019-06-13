<?php
namespace App\Controllers;

use App\Models\UserModel;
use Illuminate\Database\Capsule\Manager;
use Psr\Http\Message\ResponseInterface;
use Respect\Validation\{Rules,Validator};
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Diactoros\Response\RedirectResponse;
use Zend\Diactoros\ServerRequest;

/**
 *
 */
class UserController extends BaseController
{
  private $stringValidator;

  function __construct() {
    parent::__construct();

    $this->stringValidator = new Rules\AllOf(
      new Rules\Alnum(),
      new Rules\Length(2, 20),
      new Rules\stringType()
    );

  }

    /**
    * Muestra los usuarios con permisos activos y removidos para la administracion y creacion de
    * publicaciones
    *
    */
    public function showUsers(ServerRequest $request,ResponseInterface $handler):HtmlResponse
    {
    $users = Manager::select('SELECT user.id_user, user.access_admin, user.id_rol , user.first_name, cms_rol.name_rol, count(post.id_owner) AS tickets
                            FROM user 
                                INNER JOIN cms_rol ON 
                                user.id_rol = cms_rol.id_rol
                                LEFT JOIN post ON 
                                user.id_user = post.id_owner
                            GROUP BY user.id_user
                            ORDER BY user.access_admin AND user.id_rol DESC
                             ');

    $data = [
      'users' => $users
    ];

    return $this->renderHTML('users.twig',$data);
    }

    public function addUser(ServerRequest $request,ResponseInterface $handler) {
    $response = '';
    $new_user = $request->getParsedBody();

    if ( ($this->stringValidator->validate($new_user['user_first_name'])) &&
          $this->stringValidator->validate($new_user['user_last_name']) &&
          Validator::numeric()->positive()->between(2, 5)->validate($new_user['user_rol']) ) {

        $user = new UserModel;
        $user->first_name = strtolower($new_user['user_first_name']);
        $user->last_name = strtolower($new_user['user_last_name']);
        $user->id_rol = $new_user['user_rol'];
        $user->email = $new_user['user_email'];
        $user->password = password_hash($new_user['user_password'], PASSWORD_DEFAULT);
        $user->avatar = '/img/users/default.png';
        $user->save();

        $response = 'Nuevo usuario agregado';
    } else {
      $response = 'Los datos ingresados no deben contener caracteres especiales';
    }
    return $this->showUsers($request, $handler, [
      'response' => $response,
    ]);
    }

    public function activeUser(ServerRequest $request,ResponseInterface $handler):HtmlResponse
    {

        $id_user = $request->getAttribute('id');

        if (Validator::alnum()->numeric()->validate($id_user)) {
            $user  = UserModel::where('id_user','=',$id_user)->first();
            if ($user->access_admin == 1) {
                $user->access_admin = 0;
            } else {
                $user->access_admin = 1;
            }
            $user->save();
            return $this->showUsers($request,$handler);

        }   else {
            throw new \Exception('Estas ingresando datos invalidos en el sistema', 400);
        }
    }
}

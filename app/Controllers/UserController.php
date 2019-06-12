<?php
namespace App\Controllers;

use App\Models\UserModel;
use Respect\Validation\{Rules,Validator};
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

  public function showUsers(ServerRequest $request, $handler,$data = [])
  {
    $users = UserModel::select('user.id_user', 'user.first_name', 'user.last_name', 'cms_rol.name_rol')
                        ->join('cms_rol', 'user.id_rol', '=', 'cms_rol.id_rol')
                        ->where('user.id_rol','<','6')
                        ->orderBy('first_name','asc')
                        ->get();
    $data = $data + [
      'users' => $users
    ];

    return $this->renderHTML('users.twig',$data);
  }

  public function addUser(ServerRequest $request,$handler) {
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

  public function rmUser(ServerRequest $request,$handler)
  {
        $id = $request->getQueryParams();
        $user= UserModel::where('id_user','=', $id )->Find(1);
        $user->id_rol = 6;
        $user->save();
        var_dump($user);
  }
}

<?php
namespace App\Controllers;

use App\Models\UserModel;
use Respect\Validation\{Rules,Validator};
/**
 *
 */
class UserController extends BaseController
{
  public function showUsers($request,$data = [])
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

  public function addUser($request) {
    $response = '';
    $new_user = $request->getParsedBody();

    $userValidator = new Rules\AllOf(
      new Rules\Alnum(),
      new Rules\Length(2, 20),
      new Rules\stringType()
    );
    if ( ($userValidator->validate($new_user['user_first_name'])) &&
          $userValidator->validate($new_user['user_last_name']) &&
         Validator::numeric()->positive()->between(2, 5)->validate($new_user['user_rol']) ) {

      $user = new UserModel;
      $user->first_name = strtolower($new_user['user_first_name']);
      $user->last_name = strtolower($new_user['user_last_name']);
      $user->id_rol = $new_user['user_rol'];
      $user->save();
      $response = 'Nuevo usuario agregado';
    } else {
      $response = 'Los datos ingresados no deben contener caracteres especiales';
    }
    return $this->showUsers($request, [
      'response' => $response,
    ]);
  }

  public function rmUser($request)
  {
        $id = $request->getQueryParams();
        $user= UserModel::where('id_user','=', $id )->Find(1);
        $user->id_rol = 6;
        $user->save();
        var_dump($user);
  }
}

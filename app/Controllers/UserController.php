<?php
namespace App\Controllers;

use App\Models\UserModel;
use Respect\Validation\Rules;
/**
 *
 */
class UserController extends BaseController
{
  public function showUsers($request,$data = [])
  {
    $users = UserModel::where('id_user', '>' ,'0')->orderBy('first_name','asc')->get();
    $data = $data + [
      'users' => $users
    ];

    return $this->renderHTML('users.twig',$data);
  }

  public function addUser($request) {
    $response = '';
    $new_user = $request->getParsedBody();
//    $new_user = strtolower($nameCategory);

    $userValidator = new Rules\AllOf(
      new Rules\Alnum(),
      new Rules\Length(2, 20),
      new Rules\stringType()
    );
    if ( ($categoryValidator->validate($new_user['first_name'])) &&
          $categoryValidator->validate($new_user['last_name']) ) {
      echo "vamos bien";
      die;

      $category = new CategoryModel;
      $category->name = $nameCategory;
      $category->save();
      $response = 'Nuevo usuario agregado';
    } else {
      $response = 'Error cuando se intento agregar usuario';
    }
    return $this->showCategories($request, [
      'response' => $response,
    ]);
  }

}

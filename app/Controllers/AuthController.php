<?php
namespace App\Controllers;

use App\Models\{UserModel,PostModel};
use Respect\Validation\Validator;
use Zend\Diactoros\Response\RedirectResponse;
use Zend\Diactoros\ServerRequest;

/**
 *
 */
class AuthController extends BaseController
{

  public function loginRender(ServerRequest $request)
  {
    return $this->renderHTML('login.twig');
  }

  public function loginUser(ServerRequest $request)
  {

    if ($request->getMethod() == 'POST') {

      $postData = $request->getParsedBody();
      if (Validator::email()->validate($postData['user_email'])) {
          $user = UserModel::select('*')
              ->join('cms_rol', 'user.id_rol', '=', 'cms_rol.id_rol')
              ->where('email','=',$postData['user_email'])
              ->orderBy('first_name','asc')
              ->first();

          $user = UserModel::select('*')
              ->join('cms_rol', 'user.id_rol', '=', 'cms_rol.id_rol')
              ->where('email','=',$postData['user_email'])
              ->orderBy('first_name','asc')
              ->first();

        if ($user) {
          if ($user->password == $postData['user_password']) {

            unset($_SESSION['user']);
            $_SESSION['user'] = [
                'id_user'       =>  $user->id_user,
                'first_name'    =>  $user->first_name,
                'last_name'     =>  $user->last_name,
                'id_rol'        =>  $user->id_rol,
                'user_name'     =>  $user->user_name,
                'avatar'        =>  $user->avatar,
                'name_rol'      =>  $user->name_rol,
            ];
            return new RedirectResponse('/dashboard/overview');
          }
        }
      }
      if ($user) {
        return $this->LoginUser($request,[
          'response' => 'El correo y contraseña no corresponden a ningun usuario'
        ]);
      }
    }
  }

  public function logoutUser( ServerRequest $request)
  {
      $_SESSION['user'] = [];
      return $this->loginRender($request);
  }
}

<?php
namespace App\Controllers;

use App\Models\UserModel;
use Respect\Validation\{Validator};
use Zend\Diactoros\Response\RedirectResponse;

/**
 *
 */
class AuthController extends BaseController
{

  public function loginRender($request)
  {
    return $this->renderHTML('login.twig');
  }

  public function loginUser($request)
  {

    if ($request->getMethod() == 'POST') {

      $postData = $request->getParsedBody();
//      var_dump(Validator::Alnum('- _')->Length(4, 20)->notBlank()->validate($post['user_name']));
      if (Validator::email()->validate($postData['user_email'])) {
        $user = UserModel::where('email','=',$postData['user_email'])->first();
        if ($user) {
          if ($user->password == $postData['user_password']) {

            unset($_SESSION['user']);
            $_SESSION['user'] = [
              'id_user'       => $user->id_user,
              'first_name'    => $user->first_name,
              'last_name'     => $user->last_name,
              'id_rol'        => $user->id_rol,
              'user_name'     => $user->user_name,
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

}

<?php
namespace App\Controllers;

use App\Models\{UserModel,PostModel};
use Psr\Http\Message\ResponseInterface;
use Respect\Validation\Validator;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Diactoros\Response\RedirectResponse;
use Zend\Diactoros\ServerRequest;

/**
 *
 */
class AuthController extends BaseController
{

  public function loginRender(ServerRequest $request,ResponseInterface $handler, $data = [])
  {
    return $this->renderHTML('login.twig', $data);
  }
  /**
   * Si el la combinacion de usuario y contraseña son correctos redirecciona al tablero
   * de lo contrario carga de nuevo el login y muestra un mensaje
   *
   * @return RedirectResponse
   * @return HtmlResponse
   */
  public function loginUser(ServerRequest $request,ResponseInterface $handler)
  {

    if ($request->getMethod() == 'POST') {

      $message = '';
      $postData = $request->getParsedBody();
      if (Validator::email()->validate($postData['user_email'])) {

          $user = UserModel::select('*')
              ->join('cms_rol', 'user.id_rol', '=', 'cms_rol.id_rol')
              ->where('email','=',$postData['user_email'])
              ->orderBy('first_name','asc')
              ->first();

        if ($user) {
          //if (password_verify($postData['user_password'], $user->password)) {
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
             $response = new RedirectResponse('/dashboard/overview');
          } else {
              $message = 'La combinacion de correo y cotraseña invalidos';
          }
        } else {
            $message = 'La combinacion de correo y cotraseña invalidos';
        }
        $response = $response ?? $this->loginRender($request,$handler,[ 'message' => $message]);

        return $response;
      }
    }
  }

  public function logoutUser( ServerRequest $request):RedirectResponse
  {
      $_SESSION['user'] = [];
      return new RedirectResponse('/login-cms');
  }
}

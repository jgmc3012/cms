<?php
  namespace App\Controllers;

  use App\Models\CategoryModel;
  use App\Models\UserModel;
  use Psr\Http\Message\ResponseInterface;
  use Respect\Validation\Rules;
  use Respect\Validation\Validator;
  use Zend\Diactoros\Response\EmptyResponse;
  use Zend\Diactoros\Response\HtmlResponse;
  use Zend\Diactoros\ServerRequest;
  use Zend\HttpHandlerRunner\Emitter\SapiEmitter;

  /**
   *
   */
  class CategoryController extends BaseController
  {
    /**
     *
     */
    public function active_remove_Category(ServerRequest $request,ResponseInterface $handler):HtmlResponse
    {
        $id_category = $request->getAttribute('id');
        if (Validator::alnum()->numeric()->validate($id_category)) {
            $category  = CategoryModel::where('id_category','=',$id_category)->first();
            if ($category->category_active == 1) {
                $category->category_active = 0;
            } else {
                $category->category_active = 1;
            }
            $category->save();
            return $this->showCategories($request,$handler);
        } else {
            throw new \Exception('Estas ingresando datos invalidos en el sistema', 400);
        }

    }

    /**
    *
    */
    public function showCategories(ServerRequest $request,ResponseInterface $handler, $data=[]):HtmlResponse
    {
      $categories = CategoryModel::where('id_category', '>' ,'0')->orderBy('name','asc')->get();
      $data = $data + [
        'categories' => $categories
      ];
      return $this->renderHTML('category.twig', $data);
    }
    /**
    *
    */
    public function addCategory(ServerRequest $request,ResponseInterface $handler):HtmlResponse
    {
      $response = '';
      $nameCategory = $request->getParsedBody()['category_name'];
      $nameCategory = strtolower($nameCategory);

      $categoryValidator = new Rules\AllOf(
        new Rules\Alnum(),
        new Rules\NoWhitespace(),
        new Rules\Length(2, 15),
        new Rules\stringType()
      );
      if ($categoryValidator->validate($nameCategory)) {
        $category = new CategoryModel;
        $category->name = $nameCategory;
        $category->save();
        $response = 'Nueva categoria Agregada exitosamente.';
      } else {
        $response = 'El nombre de la categoria no debe contener espacios ni caracteres especiales.
                    Y debe ser de un tamaño de entre 2 y 15 caracteres.';
      }
      return $this->showCategories($request,$handler , [
        'response' => $response,
      ]);
    }
  }

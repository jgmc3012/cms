<?php
  namespace App\Controllers;

  use App\Models\CategoryModel;
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
    public function activeRemoveCategory(ServerRequest $request,ResponseInterface $handler):HtmlResponse
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
            throw new \Exception('Estas ingresando datos invalidos en el sistema', 401);
        }

    }

    /**
    *
    */
    public function showCategories(ServerRequest $request,ResponseInterface $handler, $data=[]):HtmlResponse
    {
      $categories = CategoryModel::where('id_category', '>' ,'0')->orderBy('category_active','desc',',','name','asc')->get();
      $data = $data + [
        'categories' => $categories
      ];
      return $this->renderHTML('dashboard/category.twig', $data);
    }
    /**
    *
    */
    public function addCategory(ServerRequest $request,ResponseInterface $handler):HtmlResponse
    {
      $postData = $request->getParsedBody();
      $files = $request->getUploadedFiles();
      //Validar Entradas

      $categoryValidator = new Rules\AllOf(
        new Rules\Alnum(),
        new Rules\NoWhitespace(),
        new Rules\Length(2, 15),
        new Rules\stringType()
      );


        $background = $files['category_background'];
        $fileName = $background->getClientFileName();
        $infoFile = new \SplFileInfo($fileName);
        $extension = $infoFile->getExtension();

        if ($categoryValidator->validate($postData['category_name'])) {

          $category = new CategoryModel;
          $category->name = strtolower($postData['category_name']);
          $category->category_description = strtolower($postData['category_description']);

          $category->save();

          if($background->getError() == UPLOAD_ERR_OK) {

            $filePath = "img/category/$postData[category_name].$extension";
            $background->moveTo($filePath);
            $category->category_background = $filePath;
            $category->save();
          }

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

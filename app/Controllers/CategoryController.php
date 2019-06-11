<?php
  namespace App\Controllers;

  use App\Models\CategoryModel;
  use Respect\Validation\Rules;
  /**
   *
   */
  class CategoryController extends BaseController
  {

    /**
    *
    */
    public function showCategories($request, $data=[])
    {
      $categories = CategoryModel::where('id_category', '>' ,'0')->orderBy('name','asc')->get();
      $data = $data + [
        'categories' => $categories
      ];
      return $this->renderHTML('category.twig',$data);
    }


    /**
    *
    */
    public function addCategory($request)
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
      return $this->showCategories($request, [
        'response' => $response,
      ]);
    }
  }

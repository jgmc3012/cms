<?php


namespace App\Controllers;


use App\Models\CategoryModel;
use Illuminate\Database\Capsule\Manager;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Diactoros\ServerRequest;

class IndexController extends BaseController
{
    public function showIndex():HtmlResponse
    {
        $categories = CategoryModel::all();
        return $this->renderHTML('public/index.twig', [
            'categories' => $categories,
        ]);
    }

    public function showPostsCategory(ServerRequest $request):HtmlResponse
    {
        $categories = CategoryModel::all();
        $name = $request->getAttribute('name');

        // Hay que validar la entrada de la url para evitar inyeccion SQL

        $category = CategoryModel::where('name', '=', $name)->first();
        if ($category)
        {
            $posts = Manager::select("
                SELECT post.id_post AS id, post.title, post.body, DATE(post.date_created) AS date, user.first_name AS user_first_name,
                        user.last_name AS user_last_name  
                FROM post
                INNER JOIN category_post ON
                    post.id_post = category_post.id_post
                INNER JOIN category ON 
                    category.id_category = category_post.id_category
				INNER JOIN user ON
					post.id_owner = user.id_user                    
                WHERE category.id_category = $category->id_category
            ");

            return $this->renderHTML('public/posts_category.twig', [
                'posts' => $posts,
                'categories' => $categories,
                'category_current' =>$category,
            ]);

        } else {
            echo 'Hey, que intentas hacer?';
            // Crear un log
            //Crear un exepcion

        }
        die;

    }
}
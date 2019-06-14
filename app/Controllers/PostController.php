<?php
namespace App\Controllers;

use App\Models\CategoryModel;
use App\Models\Post;
use Illuminate\Database\Capsule\Manager;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Diactoros\ServerRequest;

/**
 *
 */
class PostController extends BaseController
{

  public function postAction()
  {
      return $this->renderHTML('post.twig');
  }

  public function postLayout()
  {
      return $this->renderHTML('post_layout.twig');
  }

  public function newPost(ServerRequest $request):HtmlResponse
  {
      $categories= CategoryModel::all();
      $data = [
          'categories' => $categories,
      ];
      return $this->renderHTML('post_new.twig',$data);
  }

  public function dashboardPost(ServerRequest $request):HtmlResponse
  {
      $posts = Manager::select('SELECT category.name AS category_name, post.id_post, post.title, post.published, user.id_user AS 
                                    id_owner, user.first_name, user.last_name, DATE(post.date_created) AS date, post.visits 
                                FROM post
                                    INNER JOIN user ON 
                                        post.id_owner = user.id_user
                                    INNER JOIN category_post ON
                                        category_post.id_post = post.id_post
									INNER JOIN category ON
										category_post.id_category = category.id_category
                                ORDER BY post.id_post ASC');
      $data = [
          'posts'    => $posts,
      ];
      return $this->renderHTML('dashboard_post.twig',$data);
  }

}

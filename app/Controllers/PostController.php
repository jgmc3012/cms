<?php
namespace App\Controllers;

use App\Models\CategoryModel;
use Illuminate\Database\Capsule\Manager;
use Respect\Validation\Validator;
use Zend\Diactoros\Response\EmptyResponse;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Diactoros\ServerRequest;

/**
 *
 */
class PostController extends BaseController
{

  public function postShow(ServerRequest $request)
  {
      $id_post = $request->getAttribute('id');
      if (Validator::intVal()->positive()->validate($id_post))
      {
          $post = Manager::select("
                    SELECT post.title, post.body, user.first_name AS user_first_name, user.last_name AS user_last_name
                    FROM post
                    INNER JOIN user ON
                        post.id_owner = user.id_user
                    WHERE post.id_post = $id_post                      
          ")[0] ?? null;

          $comments = Manager::select("
                    SELECT comment.body, comment.date_created, user.first_name AS author_first_name,
                            user.last_name AS author_last_name, user.avatar AS author_avatar
                    FROM comment
                    INNER JOIN user ON
                        comment.id_user = user.id_user
                    WHERE comment.id_user = $id_post                       
          ");

          $response = $this->renderHTML('public/post.twig', [
              'comments'    => $comments,
              'post'        => $post,
          ]);

      } else {
          $response = new EmptyResponse(400);
      }

      return $response;
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

  public function dashboardPost():HtmlResponse
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
      return $this->renderHTML('dashboard/post.twig',$data);
  }
}

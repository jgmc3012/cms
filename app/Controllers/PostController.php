<?php
namespace App\Controllers;

use App\Models\CategoryModel;
use App\Models\PostModel;
use Illuminate\Database\Capsule\Manager;
use Respect\Validation\Validator;
use Zend\Diactoros\Response\EmptyResponse;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Diactoros\Response\RedirectResponse;
use Zend\Diactoros\ServerRequest;

/**
 *
 */
class PostController extends BaseController
{

  public function postShow(ServerRequest $request)
  {
      $idPost = $request->getAttribute('id');
      if (Validator::intVal()->positive()->validate($idPost))
      {
          $post = Manager::select("
                    SELECT post.title, post.body, user.first_name AS user_first_name, user.last_name AS user_last_name
                    FROM post
                    INNER JOIN user ON
                        post.id_owner = user.id_user
                    WHERE post.id_post = $idPost                      
          ")[0] ?? null;

          $comments = Manager::select("
                    SELECT comment.body, comment.date_created, user.first_name AS author_first_name,
                            user.last_name AS author_last_name, user.avatar AS author_avatar
                    FROM comment
                    INNER JOIN user ON
                        comment.id_user = user.id_user
                    WHERE comment.id_user = $idPost                       
          ");

          $categories = CategoryModel::all();
          $response = $this->renderHTML('public/post.twig', [
              'comments'    => $comments,
              'post'        => $post,
              'categories'  => $categories,
          ]);

      } else {
          $response = new EmptyResponse(400);
      }

      return $response;
  }




    public function newPost(ServerRequest $request,$handler,$data = []):HtmlResponse
    {
      $categories= CategoryModel::where('category_active','=',1)->get();

      $data = $data + [
          'categories' => $categories,
      ];
      return $this->renderHTML('dashboard/dashboard_post.twig',$data);
    }

    public function newPostRequest(ServerRequest $request, $handler, $data = [])
    {

        //Validar Entradas
        $requestData = $request->getParsedBody();
        $visible = $data['published'] ?? 0;

        $post = new PostModel;
        $post->title    = $requestData['post_title'];
        $post->body     = $requestData['post_body'];
        $post->id_owner = $_SESSION['user']['id_user'];
        if ($visible == 0)
        {
            $post->published = 0;
        }else {
            $post->published = 1;
        }

        $post->save();
        Manager::insert('
            INSERT INTO category_post
                (id_post,
                id_category)
             VALUES
                (?,?)
        ', [$post->id_post, $requestData['post_category']]);

        return new RedirectResponse('/dashboard/overview');
    }


    public function modifyPost(ServerRequest $request):HtmlResponse
    {
        $idPost = $request->getAttribute('id');
        if (Validator::intVal()->positive()->validate($idPost))
        {
            $post = Manager::select("
                SELECT post.id_post AS id, post.title, post.body, category_post.id_post AS category 
                FROM post
                INNER JOIN category_post ON 
                    category_post.id_post = post.id_post
                WHERE post.id_post = $idPost
            ")[0] ?? null;
            if ($post)
            {
                $data = [
                    'post'  => $post,
                ];

                return $this->newPost($request,null,$data);
            }
        }

    }

    public function modifyPostRequest(ServerRequest $request,$handler,$data = [])
    {
        //Validar Entradas
        $requestData = $request->getParsedBody();
        $idPost = $request->getAttribute('id');
        $visible = $data['published'] ?? null;
        if (Validator::intVal()->positive()->validate($idPost))
        {
            $post = PostModel::where('id_post','=',$idPost)->first();

            $post->title    = $requestData['post_title'];
            $post->body     = $requestData['post_body'];
            if ($visible != null)
            {
                $post->published = $visible;
            }
            $post->save();

            return new RedirectResponse('/dashboard/overview');

        }
    }

    public function postPreview(ServerRequest $request):HtmlResponse
    {
        $postData = $request->getParsedBody();
        $post = [
            'title' =>  $postData['post_title'],
            'body'  =>  $postData['post_body'],
        ];
        return $this->renderHTML('public/post.twig', [
            'post'        => $post,
        ]);
    }

    public function postPublic(ServerRequest $request)
    {
        $idPost = $request->getAttribute('id');
        $data = ['published' => 1];

        if (Validator::intVal()->positive()->validate($idPost))
        {
            return $this->modifyPostRequest($request,null,$data);

        } elseif ($idPost === 'new') {

            return $this->newPostRequest($request,null,$data);

        }
    }

    public function dashboardPost():HtmlResponse
    {
        $posts = Manager::select('SELECT category.name AS category_name, post.id_post, post.title, post.published, user.id_user AS 
                                    id_owner, user.first_name, user.last_name, DATE(post.date_created) AS date, post.visits,
                                    COUNT(comment.id_post) AS comments
                                FROM post
                                    INNER JOIN user ON 
                                        post.id_owner = user.id_user
                                    INNER JOIN category_post ON
                                        category_post.id_post = post.id_post
                                    INNER JOIN category ON
                                        category_post.id_category = category.id_category
                                    LEFT JOIN comment ON 
                                        post.id_post = comment.id_post
                                GROUP BY post.id_post, category.id_category	    
                                ORDER BY post.id_post ASC');


      $data = [
          'posts'    => $posts,
      ];
      return $this->renderHTML('dashboard/post.twig',$data);
    }

    public function postDelete(ServerRequest $request)
    {
        //Falta validar entrada
        $id = intval($request->getAttribute('id'));
        $post = Manager::select("SELECT post.id_post, post.id_owner, user.id_rol AS rol_user
                                FROM post
                                INNER JOIN user ON
                                    post.id_owner = user.id_user
                                WHERE post.id_post = $id");     
        if (($_SESSION['user']['id_user'] == $post[0]->id_owner) OR ($_SESSION['user']['id_rol'] <= $post[0]->rol_user))
        {
            $res = PostModel::where('id_post','=',$post[0]->id_post)->get();
            if ($res[0]) 
            {
                $post =  Manager::delete("DELETE FROM category_post
                                        WHERE id_post = $id");
                $res[0]->delete();
            }
        }
        return new RedirectResponse('/dashboard/overview');
        
    }
}

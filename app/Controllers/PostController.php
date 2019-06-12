<?php
namespace App\Controllers;

use App\Models\CategoryModel;
use App\Models\Post;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Diactoros\ServerRequest;

/**
 *
 */
class PostController extends BaseController
{

  function __construct()
  {
    parent::__construct();
  }

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
      return $this->renderHTML('dashboard_post.twig');
  }

}

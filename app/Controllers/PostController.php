<?php
namespace App\Controllers;

use App\Models\Post;
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
    return $this->renderHTML('post_index.twig');
  }

  public function newPost($request)
  {
    return $this->renderHTML('dashboard_post.twig');
  }
}

<?php
namespace App\Controllers;

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
    return $this-> renderHTML('post.twig');
  }
}

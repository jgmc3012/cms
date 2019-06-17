<?php
namespace App\Controllers;

use Zend\Diactoros\Response\HtmlResponse;

/**
 *
 */
class DashboardController extends BaseController
{
  public function overviewAction():HtmlResponse
  {
    return $this->renderHTML('dashboard/overview.twig');
  }
}

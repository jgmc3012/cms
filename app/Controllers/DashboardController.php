<?php
namespace App\Controllers;

/**
 *
 */
class DashboardController extends BaseController
{
  public function overviewAction()
  {
    return $this->renderHTML('overview.twig');
  }
}

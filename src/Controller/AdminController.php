<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/{shopify_session_id}")
 */
class AdminController extends AbstractController
{
    /**
     * @Route("/dash", name="admin_dashboard")
     */
    public function showDash()
    {
        return $this->render('admin/dash.html.twig');
    }
}

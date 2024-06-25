<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PhpinfoController extends AbstractController
{

    #[Route('/phpinfo', name: 'app_phpinfo')]
    #[IsGranted('ROLE_ADMIN', message: 'Tu ne peut pas acces a cet ressource')]

    public function phpinfo(): Response
    {
        ob_start();
        phpinfo();
        $phpinfo = ob_get_clean();

        return new Response($phpinfo);
    }
}

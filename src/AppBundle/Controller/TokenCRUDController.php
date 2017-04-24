<?php
/**
 * Date: 01-Sep-16
 * Time: 22:21
 */

namespace AppBundle\Controller;

use AppBundle\Entity\Token;
use Sonata\AdminBundle\Controller\CRUDController as Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class TokenCRUDController extends Controller
{
    /**
     * @Route("/token/generate")
     */
    public function generateAction()
    {
        $token = new Token();
        $token->setToken(md5(random_bytes(10)));
        $token->setIsUsed(false);

        $this->admin->create($token);

        $this->addFlash('sonata_flash_success', 'Token Created');

        return new RedirectResponse($this->admin->generateUrl('list'/*, $this->admin->getFilterParameters()*/));
    }
}
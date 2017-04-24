<?php
/**
 * Date: 03-Sep-16
 * Time: 17:30
 */

namespace AppBundle\Controller;

use AppBundle\Entity\Strike;
use AppBundle\Entity\User;
use AppBundle\Form\StrikeForm;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Sonata\AdminBundle\Controller\CRUDController as Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;

class UserCRUDController extends Controller
{
    public function addStrikeAction(Request $request)
    {
        /**
         * @var User $user
         */
        $user = $this->admin->getSubject();

        if (!$user) {
            throw new NotFoundHttpException(sprintf('Unable to find the object with id : %s', $user->getId()));
        }

        $strike = new Strike();
        $strike->setStreamer($user);
        $strike->setExpires((new \DateTime())->add(new \DateInterval('P7D')));
        $form = $this->createForm(StrikeForm::class, $strike);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $strike->setIssuedOn(new \DateTime());
            $em = $this->getDoctrine()->getManager();
            $em->persist($strike);
            $em->flush();

            $this->addFlash('success', 'Strike added successfully.');

            return new RedirectResponse($this->admin->generateUrl('list'));
        }

        return $this->render('admin/addStrike.html.twig', [
            'form' => $form->createView(),
            'user' => $user
        ]);

    }
}
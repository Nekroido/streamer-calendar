<?php
/**
 * Date: 20-Aug-16
 * Time: 17:38
 */

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Form\RegistrationForm;
use ReCaptcha\ReCaptcha;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class RegistrationController extends Controller
{
    /**
     * @Route("/register/{token}", name="user_registration", defaults={"token": null}, requirements={"token": "[a-z0-9]{32}"})
     * @param Request $request
     * @param null $token
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function registerAction(Request $request, $token = null)
    {
        if ($this->container->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirectToRoute('homepage');
        }

        $recaptcha = new ReCaptcha($this->getParameter('recaptcha_secret'));
        $resp = $recaptcha->verify($request->request->get('g-recaptcha-response'), $request->getClientIp());

        $user = new User();
        $user->token = $token;
        $form = $this->createForm(RegistrationForm::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid() && $resp->isSuccess()) {
            $password = $this->get('security.password_encoder')->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password);
            $user->setCanSeeGlobalStreamkey(false);
            $user->setPersonalStreamkey("");
            $user->setDonationUrl("");
            $user->setPseudonyms("");
            $user->setLikes("");
            $user->setPreferredPlatforms("");
            $user->setAbout("");
            $user->setMotto("");
            $user->setRegistered(new \DateTime());
            $user->setRoles($this->getParameter('invite_only')
                ? [$this->getParameter('invited_role')]
                : [$this->getParameter('default_role')]);

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);

            if ($this->getParameter('invite_only')) {
                $token = $this->get('app.token_service')->isValid($user->token);
                $token->setIsUsed(true);
                $token->setUsedAt(new \DateTime());
                $token->setUsedBy($user);
            }

            $em->flush();

            $this->addFlash('success', 'Registered successfully.');

            return $this->redirectToRoute('homepage');
        }

        return $this->render(
            'registration/register.html.twig',
            ['form' => $form->createView()]
        );
    }
}
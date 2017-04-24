<?php
/**
 * Date: 20-Aug-16
 * Time: 11:00
 */

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Entity\UserEditType;
use AppBundle\Entity\UserProfileType;
use AppBundle\Form\UserForm;
use AppBundle\Form\UserProfileForm;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    /**
     * @Route("/user", name="user_dashboard")
     */
    public function indexAction()
    {
        $today = new \DateTime();
        $end = clone($today);
        $end->add(new \DateInterval('P5D'));

        $user = $this->getUser();

        $streamEntries = [];
        foreach ($this->get('app.event_service')->getStreamEntries($today, $end, null, $user->getId()) as $entry) {
            $streamEntries[$entry->getStart()->format('m-d')][] = $entry;
        }

        return $this->render('user/index.html.twig', [
            'entries' => $streamEntries,
            'user' => $user,
            'strikes' => $this->get('app.strike_service')->getStrikes($user)
        ]);
    }

    /**
     * @Route("/user/edit", name="user_edit")
     * @param Request $request
     * @return Response
     */
    public function editAction(Request $request)
    {
        /**
         * @var User $user
         */
        $user = $this->getUser();
        $userEditModel = new UserEditType();
        $userEditModel->username = $user->getUsername();
        $userEditModel->email = $user->getEmail();
        $userEditModel->name = $user->getName();

        $form = $this->createForm(UserForm::class, $userEditModel);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($userEditModel->plainPassword != null) {
                $password = $this->get('security.password_encoder')->encodePassword($user, $userEditModel->plainPassword);
                $user->setPassword($password);
            }
            $user->setUsername($userEditModel->username);
            $user->setEmail($userEditModel->email);
            $user->setName($userEditModel->name);
            $user->setDonationUrl($userEditModel->donationUrl);

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'Account updated successfully.');

            return $this->redirectToRoute('user_dashboard');
        }

        return $this->render('user/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/user/profile", name="profile_edit")
     * @param Request $request
     * @return Response
     */
    public function profileAction(Request $request)
    {
        /**
         * @var User $user
         */
        $user = $this->getUser();
        $userEditModel = new UserProfileType();
        $userEditModel->pseudonyms = $user->getPseudonyms();
        $userEditModel->likes = $user->getLikes();
        $userEditModel->preferredPlatforms = $user->getPreferredPlatforms();
        $userEditModel->about = $user->getAbout();
        $userEditModel->motto = $user->getMotto();

        $form = $this->createForm(UserProfileForm::class, $userEditModel);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($userEditModel->avatarFile != null) {
                $user->setImageFile($userEditModel->avatarFile);
            }
            $user->setPseudonyms($userEditModel->pseudonyms);
            $user->setLikes($userEditModel->likes);
            $user->setPreferredPlatforms($userEditModel->preferredPlatforms);
            $user->setAbout($userEditModel->about);
            $user->setMotto($userEditModel->motto);

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'Profile updated successfully.');

            return $this->redirectToRoute('user_dashboard');
        }

        return $this->render('user/profile.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
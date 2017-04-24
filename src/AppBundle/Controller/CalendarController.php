<?php
/**
 * Date: 27-Aug-16
 * Time: 19:00
 */

namespace AppBundle\Controller;

use AppBundle\Entity\EntryType;
use AppBundle\Entity\StreamEntry;
use AppBundle\Form\EntryForm;
use IntervalTree\DateRangeExclusive;
use IntervalTree\IntervalTree;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

class CalendarController extends Controller
{
    /**
     * @Route(
     *      "/calendar/{year}/{month}/{day}",
     *      name="calendar_entry",
     *      requirements={"year": "\d+", "month": "\d+", "day": "\d+"}
     * )
     * @param int $day
     * @param int $month
     * @param int $year
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function dayAction($day, $month, $year)
    {
        $day = $day ?: date('j');
        $month = $month ?: date('n');
        $year = $year ?: date('Y');

        $start = date_create(sprintf('%d-%d-%d 00:00:00', $year, $month, $day));
        $end = clone($start);
        $end->add(new \DateInterval('PT23H59M59S'));

        $status = $this->container->get('security.authorization_checker')->isGranted('ROLE_STREAMER') == false;

        $dayEntries = [];
        $streamEntries = [];
        $intervals = [];
        foreach ($this->get('app.event_service')->getStreamEntries($start, $end, $status) as $entry) {
            if ($entry->getStart()->format('Ymd') != $start->format('Ymd'))
                continue;

            if ($entry->getAllDay()) {
                $dayEntries[] = $entry;
            }
            else {
                $streamEntries[$entry->getStart()->format('G')][] = $entry;
                $intervals[] = new DateRangeExclusive(
                    $entry->getStart(), $entry->getEnd(), $entry->getDuration()
                );
            }
        }
        $tree = new IntervalTree($intervals);

        return $this->render('calendar/day.html.twig', [
            'day' => $day,
            'month' => $month,
            'year' => $year,
            'today' => $start,
            'entries' => $streamEntries,
            'dayEntries' => $dayEntries,
            'tree' => $tree
        ]);
    }

    /**
     * @Route(
     *      "/calendar/add/{year}/{month}/{day}",
     *      name="add_entry",
     *      defaults={"year": null, "month": null, "day": null},
     *      requirements={"year": "\d+", "month": "\d+", "day": "\d+"}
     * ))
     * @param Request $request
     * @param int $day
     * @param int $month
     * @param int $year
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addAction(Request $request, $day, $month, $year)
    {
        if (!$this->get('app.strike_service')->canAddEntry($this->getUser()))
            return $this->redirectToRoute('homepage');

        $day = $day ?: date('j');
        $month = $month ?: date('n');
        $year = $year ?: date('Y');
        $hours = (int)$request->query->get('hours') ?: date('H');
        $minutes = (int)$request->query->get('minutes') ?: date('i');

        $start = date_create(sprintf('%d-%d-%d %d:%d:00', $day, $month, $year, $hours, $minutes));
        $dateStart = $this->get('app.event_service')->roundMinutes($start, 30);

        $isApproved = $this->container->get('security.authorization_checker')->isGranted('ROLE_APPROVED_STREAMER');

        $streamEntry = new StreamEntry();
        $streamEntry->setCategory($this->getDoctrine()->getRepository('AppBundle:Project')->findOneBy([], ['id' => 'ASC']));
        $streamEntry->setAuthor($this->getUser());
        $streamEntry->setStart($dateStart);
        $streamEntry->setDuration(date_interval_create_from_date_string('3 hours'));
        $streamEntry->setAllDay(false);
        $streamEntry->setIsApproved(false);

        $entry = new EntryType($streamEntry);
        $entry->status = StreamEntry::STATUS_ACTIVE;
        $entry->isApproved = $isApproved;
        $entry->isApprovedStreamer = $isApproved;

        $form = $this->createForm(EntryForm::class, $entry);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $mappedEntry = $entry->mapEntry();
            $mappedEntry->setAdded(new \DateTime());
            $mappedEntry->setIsApproved($isApproved ? $entry->isApproved : false);
            $em->persist($mappedEntry);
            $em->flush();

            $this->addFlash('success', 'Entry created successfully.');

            return $this->redirectToRoute('homepage');
        }

        return $this->render('entry/add.html.twig', [
            'day' => $day,
            'month' => $month,
            'year' => $year,
            'form' => $form->createView(),
            'entry' => $streamEntry
        ]);
    }

    /**
     * @Route("/calendar/edit/{id}", name="edit_entry")
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $sm = $this->container->get('security.authorization_checker');
        /**
         * @var StreamEntry $streamEntry
         */
        $streamEntry = $this->getDoctrine()->getRepository('AppBundle:StreamEntry')->find($id);

        if ($streamEntry == null || !$this->get('app.strike_service')->canEditEntry($streamEntry, $this->getUser()))
            return $this->redirectToRoute('homepage');

        $entry = new EntryType($streamEntry);
        $entry->isApprovedStreamer = $sm->isGranted('ROLE_APPROVED_STREAMER');
        $form = $this->createForm(EntryForm::class, $entry);

        $previousStatus = $entry->status;

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Change approval status
            if (!$entry->isApprovedStreamer && $entry->status != $previousStatus) {
                $entry->isApproved = false;
            }
            $entry->mapEntry();
            $em->flush();

            $this->addFlash('success', 'Entry updated successfully.');

            return $this->redirectToRoute('homepage');
        }

        return $this->render('entry/add.html.twig', [
            'day' => $streamEntry->getStart()->format('d'),
            'month' => $streamEntry->getStart()->format('m'),
            'year' => $streamEntry->getStart()->format('Y'),
            'form' => $form->createView(),
            'entry' => $streamEntry
        ]);
    }

    /**
     * @Route("/calendar/delete/{id}", name="delete_entry")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        /**
         * @var StreamEntry $streamEntry
         */
        $streamEntry = $this->getDoctrine()->getRepository('AppBundle:StreamEntry')->find($id);

        if ($streamEntry == null || !$this->get('app.strike_service')->canDeleteEntry($streamEntry, $this->getUser()))
            return $this->redirectToRoute('homepage');

        $em->remove($streamEntry);
        $em->flush();

        $this->addFlash('success', 'Entry removed successfully.');

        return $this->redirectToRoute('homepage');
    }

    /**
     * @Route("/calendar/approve/{id}", name="approve_entry")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function approveAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        /**
         * @var StreamEntry $streamEntry
         */
        $streamEntry = $this->getDoctrine()->getRepository('AppBundle:StreamEntry')->find($id);

        if ($streamEntry == null || !$this->get('app.strike_service')->canApproveEntry($streamEntry, $this->getUser()))
            return $this->redirectToRoute('homepage');

        $streamEntry->setIsApproved(true);
        $em->flush();

        $this->addFlash('success', 'Entry approved successfully.');

        return $this->redirectToRoute('homepage');
    }
}
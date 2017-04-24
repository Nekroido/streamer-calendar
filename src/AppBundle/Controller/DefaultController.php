<?php

namespace AppBundle\Controller;

use AppBundle\Entity\StreamEntry;
use AppBundle\Helpers\CalendarHelper;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\DateTime;

class DefaultController extends Controller
{
    const TIME_START_HOURS = 3;
    const TIME_START_MINUTES = 0;

    /**
     * @Route("/{year}/{month}", name="homepage", defaults={"year": null, "month": null}, requirements={"year": "\d+", "month": "\d+"})
     * @param Request $request
     * @param int $year
     * @param int $month
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request, $year, $month)
    {
        return $this->renderCalendar($year, $month, 'default/index.html.twig', false, $request->get('streamer'));
    }

    /**
     * @Route("/info", name="info")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function infoAction()
    {
        $repository = $this->getDoctrine()->getRepository('AppBundle:Info');

        return $this->render('default/info.html.twig', [
            'info' => $repository->findAll()
        ]);
    }

    /**
     * @Route("/embed/{year}/{month}", name="embed", defaults={"year": null, "month": null}, requirements={"year": "\d+", "month": "\d+"})
     * @param int $year
     * @param int $month
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function embedAction($year, $month)
    {
        return $this->renderCalendar($year, $month, 'default/embed.html.twig', true);
    }

    /**
     * @Route("/stats/{start}", name="stats", defaults={"start": null}, requirements={"start": "\d+"})
     * @Route("/stats/", name="stats_current", defaults={"start": null})
     * @param int $start
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function statsAction($start)
    {
        $start = new \DateTime('@' . ($start === null ? time() : $start));

        $prev = clone($start);
        $prev->sub(new \DateInterval('P1D'));
        $next = clone($start);
        $next->add(new \DateInterval('P1D'));

        return $this->render('default/stats.html.twig', [
            'start' => $start,
            'prev' => $prev,
            'next' => $next
        ]);
    }

    private function renderCalendar($year, $month, $view, $isEmbed = false, $user = null)
    {
        $month = (int)$month ?: date('m');
        $year = (int)$year ?: date('Y');

        $currentMonth = date_create(sprintf('%d-%d-%d 00:00:00', $year, $month, 1));
        $previousMonth = clone($currentMonth);
        $previousMonth->sub(new \DateInterval('P1M'));
        $nextMonth = clone($currentMonth);
        $nextMonth->add(new \DateInterval('P1M'));

        $matrix = CalendarHelper::buildMonthMatrix($month, $year);

        $start = $matrix[0][0];
        $lastRow = end($matrix);
        $end = end($lastRow);

        $start->setTime(0, 0, 0);
        $end->setTime(23, 59, 59);

        $status = $this->container->get('security.authorization_checker')->isGranted('ROLE_STREAMER') == false;

        $dayEntries = [];
        $streamEntries = [];
        foreach ($this->get('app.event_service')->getStreamEntries($start, $end, $status) as $entry) {
            if ($entry->getAllDay()) {
                $dayEntries[$entry->getStart()->format('m-d')][] = $entry;
            } else {
                $streamEntries[$entry->getStart()->format('m-d')][] = $entry;
            }
        }

        return $this->render($view, [
            'prev' => $previousMonth,
            'next' => $nextMonth,
            'range' => $matrix,
            'month' => $month,
            'year' => $year,
            'entries' => $streamEntries,
            'dayEntries' => $dayEntries,
            'isEmbed' => $isEmbed,
            'today' => new \DateTime(),
            'user' => $user
        ]);
    }
}

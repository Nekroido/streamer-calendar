<?php
/**
 * Date: 04-Sep-16
 * Time: 10:09
 */

namespace AppBundle\Controller;

use AppBundle\Entity\Dashboard;
use AppBundle\Entity\StreamEntry;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ApiController extends Controller
{
    const TIME_START_HOURS = 3;
    const TIME_START_MINUTES = 0;

    /**
     * @Route("/api/day/{day}", requirements={"day": "\d+"})
     * @param int $day
     * @return JsonResponse
     */
    public function dayAction($day)
    {
        $start = new \DateTime("@{$day}");
        $start->setTime(self::TIME_START_HOURS, self::TIME_START_MINUTES);
        $end = clone($start);
        $end->add(new \DateInterval('PT23H59M59S'));

        $events = [];
        foreach ($this->get('app.event_service')->getStreamEntries($start, $end, true, null, [StreamEntry::STATUS_ACTIVE, StreamEntry::STATUS_TENTATIVE]) as $entry) {
            $events[] = [
                'id' => $entry->getId(),
                'title' => $entry->getTitleFormatted(),
                'start' => (int)$entry->getStart()->format('U'),
                'end' => (int)$entry->getEnd()->format('U'),
                'description' => $entry->getDescription(),
                'url' => $entry->getCategory()->getUrl()
            ];
        }

        $json = new JsonResponse();
        $json->setData([
            'events' => $events,
            'start' => (int)$start->format('U'),
            'end' => (int)$end->format('U')
        ]);

        return $json;
    }

    /**
     * @Route("/api/get_donation")
     * @return JsonResponse
     */
    public function getDonationAction()
    {
        $events = $this->get('app.event_service')->getCurrentStream();

        /**
         * @var StreamEntry $event
         */
        $event = array_pop($events);

        $user = $event->getAuthor();

        if (strlen($user->getDonationUrl()) == 0) {
            $users = $this->get('app.user_service')->getRandomUserWithDonation();
            $user = array_pop($users);
        }

        if ($user == null) {
            return new JsonResponse();
        }

        $json = new JsonResponse();
        $json->setData([
            'streamer' => $user->getName(),
            'url' => $user->getDonationUrl()
        ]);

        return $json;
    }

    /**
     * @Route("/api/get_streamers")
     * @param Request $request
     * @return JsonResponse
     * @throws \Exception
     */
    public function getStreamersAction(Request $request)
    {
        $helper = $this->container->get('vich_uploader.templating.helper.uploader_helper');
        $imagineCacheManager = $this->get('liip_imagine.cache.manager');

        $streamers = [];
        foreach ($this->get('app.user_service')->getStreamers() as $streamer) {
            $avatar = $helper->asset($streamer, 'imageFile');
            $streamers[] = [
                'id' => $streamer->getId(),
                'name' => $streamer->getName(),
                'pseudonyms' => $streamer->getPseudonyms(),
                'likes' => $streamer->getLikes(),
                'preferredPlatforms' => $streamer->getPreferredPlatforms(),
                'about' => $streamer->getAbout(),
                'motto' => $streamer->getMotto(),
                'donationUrl' => $streamer->getDonationUrl(),
                'avatarUrl' => strlen($avatar)
                    ? $imagineCacheManager->getBrowserPath($avatar, 'avatar')
                    : 'http://s.gravatar.com/avatar/' . md5(trim($streamer->getEmail())) . '?d=retro&s=140',
                'backgroundUrl' => strlen($avatar)
                    ? $imagineCacheManager->getBrowserPath($avatar, 'micro')
                    : 'http://s.gravatar.com/avatar/' . md5(trim($streamer->getEmail())) . '?d=retro&s=2'
            ];
        }

        $json = new JsonResponse();
        $json->setData($streamers);

        return $json;
    }

    /**
     * @Route("/api/dashboard/{channelId}", requirements={"channelId": "\d+"})
     * @param $channelId
     * @return JsonResponse
     */
    public function dashboardAction($channelId)
    {
        $repository = $this->getDoctrine()->getManager()->getRepository('AppBundle:Dashboard');

        $qb = $repository->createQueryBuilder('d')
            ->join('d.category', 'c')
            ->where('c.id = :channelId')
            ->orderBy('d.currentTime', 'DESC')
            ->setMaxResults(1)
            ->setParameter('channelId', $channelId);

        $query = $qb->getQuery();

        $data = [
            'channel' => '',
            'title' => '',
            'media' => '',
            'viewers' => 0,
            'is_live' => false,
            'broadcast_start' => null,
            'broadcast_end' => null,
            'timestamp' => time()
        ];

        try {
            /**
             * @var Dashboard $result
             */
            $result = $query->getSingleResult();

            if ($result != null) {
                $project = $result->getCategory();
                $data['channel'] = $project->getTitle();
                $data['link'] = $project->getUrl();
                $data['title'] = $result->getTitle();
                $data['media'] = $result->getMedia();
                $data['viewers'] = $result->getViewers();
                $data['is_live'] = $result->getIsLive();
                $data['broadcast_start'] = $result->getBroadcastStart() ? (int)$result->getBroadcastStart()->format('U') : null;
                $data['broadcast_end'] = $result->getBroadcastEnd() != null ? (int)$result->getBroadcastEnd()->format('U') : null;
            }
        } catch (\Exception $e) {
        }

        $json = new JsonResponse();
        $json->setData($data);

        return $json;
    }

    /**
     * @Route("/api/dashboard/stats/{start}-{end}", defaults={"start": null, "end": null}, requirements={"start": "\d+", "end": "\d+"})
     * @Route("/api/dashboard/stats/{start}", name="api_stats", defaults={"start": null, "end": null}, requirements={"start": "\d+"})
     * @Route("/api/dashboard/stats", defaults={"start": null, "end": null})
     * @param Request $request
     * @param int $start
     * @param int $end
     * @return JsonResponse
     * @throws \Exception
     */
    public function statsAction(Request $request, $start, $end)
    {
        $start = new \DateTime('@' . ($start === null ? time() : $start));
        $start->setTime(self::TIME_START_HOURS, self::TIME_START_MINUTES, 0);

        if ($end == null) {
            $end = clone($start);
        } else {
            $end = new \DateTime('@' . ($end > $start ? $end : time()));
        }
        $end->setTime(23, 59, 59);

        $timezone = new \DateTimeZone(date_default_timezone_get());
        $UTC = new \DateTimeZone('UTC');
        $data = [];
        $pingPong = [];
        foreach ($this->container->get('app.dashboard_service')->getDashboards($start, $end) as $entry) {
            $time = date_create($entry->getCurrentTime()->setTimezone($timezone)->format('Y-m-d H:i:s'), $UTC)->format('U') * 1000;
            $data[$entry->getCategory()->getId()]['title'] = $entry->getCategory()->getTitle();
            $data[$entry->getCategory()->getId()]['color'] = '#' . $entry->getCategory()->getColor();
            $data[$entry->getCategory()->getId()]['data'][] = [
                'x' => $time,
                'y' => $entry->getIsLive() ? $entry->getViewers() : null,
                'name' => $entry->getTitle()
            ];
            if (@$pingPong[$entry->getCategory()->getId()] !== $entry->getIsLive()) {
                $data[$entry->getCategory()->getId()]['zones'][] = [
                    'value' => $time,
                    'color' => !$entry->getIsLive() ? ('#' . $entry->getCategory()->getColor()) : '#FF0000',
                    'dashStyle' => !$entry->getIsLive() ? 'solid' : 'dot'
                ];
                @$pingPong[$entry->getCategory()->getId()] = $entry->getIsLive();
            }
        }

        $json = new JsonResponse();
        $json->setData($data);
        if (strlen($request->get('callback')))
            $json->setCallback($request->get('callback'));
        $json->setEncodingOptions(JSON_UNESCAPED_UNICODE);

        return $json;
    }
}
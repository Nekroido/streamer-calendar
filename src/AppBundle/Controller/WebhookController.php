<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Dashboard;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\DateTime;

class WebhookController extends Controller
{
    /**
     * @Route("/webhook/{type}/{key}", requirements={"type": "dashboard|announce","key": "[0-9A-F]{128}"})
     * @param Request $request
     * @param string $type
     * @param string $key
     * @return JsonResponse
     * @throws \Exception
     */
    public function webhookAction(Request $request, $type, $key)
    {
        $json = new JsonResponse();
        $status = 'error';

        if ($this->getParameter('webhook_key') == $key) {
            if ($type == 'dashboard')
                $status = $this->dashboard($request) ? 'ok' : 'error';
            else if ($type == 'announce')
                $status = $this->announce($request) ? 'ok' : 'error';
        }

        $json->setData([
            'status' => $status,
            'type' => $type
        ]);

        return $json;
    }

    private function dashboard(Request $request)
    {
        $data = @json_decode($request->getContent());

        if ($data != null) {
            try {
                $em = $this->getDoctrine()->getManager();

                $category = $em->getRepository('AppBundle:Project')->find($data->channel_id);

                if ($category == null)
                    return false;

                $dashboard = new Dashboard();
                $dashboard->setCategory($category);
                $dashboard->setTitle($data->title);
                $dashboard->setMedia($data->media);
                $dashboard->setViewers($data->viewers);
                $dashboard->setMaxViewers($data->max_viewers);
                $dashboard->setIsLive($data->is_live);
                $dashboard->setBroadcastStart(new \DateTime($data->broadcast_start));
                if ($data->broadcast_end != null)
                    $dashboard->setBroadcastEnd(new \DateTime($data->broadcast_end));
                $dashboard->setCurrentTime(new \DateTime('@' . $data->timestamp));

                $em->persist($dashboard);
                $em->flush();

                return true;
            } catch (\Exception $e) {
                return false;
            }
        }

        return false;
    }

    private function announce(Request $request)
    {
        return false;
    }
}

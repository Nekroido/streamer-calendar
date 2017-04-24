<?php
/**
 * Created by PhpStorm.
 * User: nekro
 * Date: 14-Feb-17
 * Time: 12:08
 */

namespace AppBundle\Services;


use AppBundle\Entity\StreamEntry;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Config\Definition\Exception\Exception;

class VkService
{
    protected $defaultProjectId = 1;

    protected $url = 'https://api.vk.com/method/';
    protected $version = '5.60';
    protected $language = 'ru';

    private $vkId;
    private $vkToken;

    protected $es;

    /**
     * @InjectParams({
     *    "es" = @Inject("app.event_service")
     * })
     * @param EventService $es
     * @param int $vkId
     * @param string $vkToken
     * @internal param EntityManager $em
     */
    public function __construct(EventService $es, $vkId, $vkToken)
    {
        $this->es = $es;

        $this->vkId = $vkId;
        $this->vkToken = $vkToken;
    }

    public function performDailyAnnounce()
    {
        $start = new \DateTime();
        $start->setTime(5, 0);
        $end = clone($start);
        $end->add(new \DateInterval('PT23H59M59S'));

        $message = 'Расписание стримов на сегодня, ' . $start->format('d.m') . ":\n\n";
        $urls = [];
        foreach ($this->es->getStreamEntries($start, $end, true, null, [StreamEntry::STATUS_ACTIVE, StreamEntry::STATUS_TENTATIVE]) as $entry) {
            $message .= "\n";
            $message .= $entry->getStart()->format('H:i') . ' ';
            $message .= $entry->getTitleFormatted();
            if ($entry->getCategory()->getId() != $this->defaultProjectId) {
                $message .= '(' . $entry->getCategory()->getUrl() . ')';
            }
            if (strlen($entry->getDescription())) {
                $message .= "\n" . $entry->getDescription() . "\n";
            }
            $urls[] = $entry->getCategory()->getUrl();
        }

        $result = $this->postToWall($message, [array_shift($urls)]);

        return isset($result->post_id);
    }

    public function postToWall($message, $attachments = [])
    {
        return $this->callApi(
            'wall.post',
            [
                'owner_id' => $this->vkId,
                'friends_only' => 0,
                'from_group' => (substr($this->vkId, 0, 1) == '-'), // Is group ID
                'message' => $message,
                'services' => 'twitter',
                'signed' => 0,
                'attachments' => join(',', $attachments)
            ]
        );
    }

    private function callApi($method, $parameters = [])
    {
        $parameters['lang'] = $this->language;
        $parameters['v'] = $this->version;
        $parameters['access_token'] = $this->vkToken;

        $parameters = $this->prepareParams($parameters);

        try {
            $responseRaw = file_get_contents($this->url . $method . (strlen($parameters) ? '?' . $parameters : ''));
            $responseRaw = mb_convert_encoding($responseRaw, 'UTF-8', mb_detect_encoding($responseRaw, 'UTF-8, ISO-8859-1', true));
        } catch (\Exception $e) {
            throw $e;
        }

        $response = null;
        if ($responseRaw != null && $responseRaw !== false && strlen($responseRaw) > 0) {
            $response = $this->decodeResponse($responseRaw);

            if ($response === null) {
                return $responseRaw;
            }
        }

        if (!isset($response->response) && isset($response->error)) {
            throw new \Exception($response->error->error_msg);
        } else if (!isset($response->response)) {
            return null;
        }

        return $response->response;
    }

    /**
     * Formats array of parameters into GET string
     * @param array $params
     * @return string
     */
    private function prepareParams($params)
    {
        return http_build_query($params);
    }

    /**
     * Parses JSON string
     * @param $jsonString
     * @return object|null
     */
    protected function decodeResponse($jsonString)
    {
        return json_decode($jsonString);
    }
}
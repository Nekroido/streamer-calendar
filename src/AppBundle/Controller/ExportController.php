<?php
/**
 * Date: 04-Sep-16
 * Time: 14:30
 */

namespace AppBundle\Controller;

use AppBundle\Entity\StreamEntry;
use Jsvrcek\ICS\CalendarExport;
use Jsvrcek\ICS\CalendarStream;
use Jsvrcek\ICS\Model\Calendar;
use Jsvrcek\ICS\Model\CalendarEvent;
use Jsvrcek\ICS\Model\Relationship\Attendee;
use Jsvrcek\ICS\Model\Relationship\Organizer;
use Jsvrcek\ICS\Utility\Formatter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class ExportController extends Controller
{
    /**
     * @Route("/calendar/export", name="calendar_export")
     */
    public function exportAction()
    {
        $start = date_create(date('Y-m-1 00:00:00'));
        $end = clone($start);
        $end->add(new \DateInterval('P3M'));

        $calendar = new Calendar();
        $calendar->setProdId('-//Kraken\'s Lair//Расписание стримов Kraken\'s Lair//RU');
        foreach ($this->get('app.event_service')->getStreamEntries($start, $end, true) as $entry) {
            $organizer = new Organizer(new Formatter());
            $organizer
                ->setValue($entry->getCategory()->getUrl())
                ->setName($entry->getAuthor()->getName());

            $event = new CalendarEvent();
            $event
                ->setSummary($entry->getTitleFormatted())
                ->setDescription($entry->getComment())
                ->setStart($entry->getStart())
                ->setEnd($entry->getEnd())
                ->setOrganizer($organizer);

            $calendar->addEvent($event);
        }
        $calendarExport = new CalendarExport(new CalendarStream, new Formatter());
        $calendarExport->addCalendar($calendar);

        return new Response($calendarExport->getStream(), 200, [
            'Content-Type' => 'text/calendar; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="calendar.ics"'
        ]);
    }
}
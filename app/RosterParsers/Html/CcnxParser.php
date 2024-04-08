<?php

declare(strict_types=1);

namespace App\RosterParsers\Html;

use App\RosterParsers\Parser;
use DateInterval;
use DatePeriod;
use DateTime;
use DOMDocument;
use DOMElement;
use DOMXPath;
use Exception;
use Illuminate\Validation\ValidationException;

class CcnxParser implements Parser
{
    protected string $lastRowDate = '';

    /**
     * @throws Exception
     */
    public function parse(string $fileContent): array
    {
        $xpath = new DOMXPath($this->getDom($fileContent));

        return $this->fillInDates(
            $this->getEvents($xpath, $this->getDaysInPeriod($fileContent))
        );
    }

    /**
     * @param string $fileContent
     * @return DOMDocument
     */
    protected function getDom(string $fileContent): DOMDocument
    {
        $dom = new DOMDocument;
        libxml_use_internal_errors(true);
        $dom->loadHTML($fileContent);
        libxml_clear_errors();

        return $dom;
    }

    protected function fillInDates(array $events): array
    {
        foreach ($events as $eventKey => $event) {
            $events[$eventKey] = $this->fillInEventDates($event);
        }

        return $events;
    }

    protected function fillInEventDates(array $event): array
    {
        $columnsToFillIn = [
            'check_in_time_utc',
            'check_out_time_utc',
            'departure_time_utc',
            'arrival_time_utc',
        ];

        foreach ($columnsToFillIn as $dateToFillIn) {
            if (!empty($event[$dateToFillIn])) {
                $event[$dateToFillIn] = $this->formatEventDate(
                    $event['date_obj'],
                    $event[$dateToFillIn]
                );
            }
        }

        return $event;
    }

    protected function formatEventDate(DateTime $date, string $hour): string
    {
        $formattedHour = sprintf('%s:%s:00', substr($hour, 0, -2), substr($hour, -2));
        return sprintf('%s %s', $date->format('Y-m-d'), $formattedHour);
    }

    /**
     * Parse all events (found in the first table)
     */
    protected function getEvents(DOMXPath $xpath, array $daysInPeriod): array
    {
        $table = $xpath->query('//table')->item(0);

        $columnsToCollect = $this->columnsToCollect();
        $events = [];
        if (!$table instanceof DOMElement) {
            return [];
        }

        $rowNr = 0;
        foreach ($table->getElementsByTagName('tr') as $row) {
            $rowNr++;
            if ($rowNr === 1) {
                continue;
            }

            $rowData = [];
            foreach ($row->getElementsByTagName('td') as $cell) {
                if (preg_match('|activitytablerow-([a-zA-Z0-9]+)|', $cell->getAttribute('class'), $matches)) {
                    $column = $matches[1] ?? null;

                    if (!isset($columnsToCollect[$column])) {
                        continue;
                    }

                    $value = $this->prepareData($cell->nodeValue);
                    $rowData[$columnsToCollect[$column]] = $value;

                    switch ($column) {
                        case 'date':
                            $thisRowDate = $this->prepareThisRowDate($daysInPeriod, $value);
                            $rowData['date_obj'] = $thisRowDate;
                            $rowData['date'] = $thisRowDate->format('Y-m-d');
                            break;
                        case 'activity':
                            $rowData['activity_type'] = $this->prepareType($value);
                            break;
                        case 'stdutc':
                        case 'stautc':
                        case 'checkinutc':
                        case 'checkoututc':
                            if (!$value || (isset($rowData['activity']) && $rowData['activity'] === 'OFF')) {
                                $rowData[$columnsToCollect[$column]] = null;
                            }

                            break;
                    }
                }
            }
            $events[] = $rowData;
        }

        return $events;
    }

    protected function columnsToCollect(): array
    {
        return [
            'date' => 'date',
            'dc' => 'dc',
            'checkinutc' => 'check_in_time_utc',
            'checkoututc' => 'check_out_time_utc',
            'activity' => 'activity',
            'activityRemark' => 'activity_remark',
            'fromstn' => 'departure_airport',
            'stdutc' => 'departure_time_utc',
            'tostn' => 'arrival_airport',
            'stautc' => 'arrival_time_utc',
            'AC/Hotel' => 'ac_hotel',
            'blockhours' => 'block_hours',
            'flighttime' => 'flight_time',
            'nighttime' => 'night_time',
            'duration' => 'duration',
            'counter1' => 'counter1',
            'Paxbooked' => 'pax_booked',
            'Tailnumber' => 'tail_number',
        ];
    }

    protected function prepareData(string $data): string
    {
        return trim(preg_replace('/\xc2\xa0/', '', $data));
    }

    protected function prepareThisRowDate(array $daysInPeriod, string $value): DateTime
    {
        if (isset($daysInPeriod[$value])) {
            $this->lastRowDate = $value;
            $thisRowDate = $daysInPeriod[$value];
        } elseif ($value === '') {
            $thisRowDate = $daysInPeriod[$this->lastRowDate];
        } else {
            throw ValidationException::withMessages([
                'error' => "Invalid date found",
            ]);
        }

        return $thisRowDate;
    }

    protected function prepareType(string $value): string
    {
        if (preg_match('|^[a-z]{2}\d+$|i', $value)) {
            return 'FLT';
        } else {
            return match ($value) {
                'OFF' => 'DO',
                'SBY' => 'SBY',
                default => 'UNK',
            };
        }
    }

    /**
     * Parse a period directly from the HTML from the heading "Period: 10Jan22 to 23Jan22 (ILV - Jan de Bosman)".
     * Why not from a select field? Because there are other filters like "Week", "Period", "Custom Period" etc.
     *
     * @throws Exception
     */
    protected function getDaysInPeriod(string $fileContent): array
    {
        preg_match(
            '|Period: (\d{1,2}[a-zA-Z]{3,4}\d{2}) to (\d{1,2}[a-zA-Z]{3,4}\d{2})|',
            $fileContent,
            $matches
        );

        if (empty($matches)) {
            throw ValidationException::withMessages([
                'error' => "HTML doesn't contain a period info in form of a heading",
            ]);
        }

        $days = [];
        $period = new DatePeriod(
            new DateTime($matches[1]),
            new DateInterval('P1D'),
            new DateTime($matches[2]),
            DatePeriod::INCLUDE_END_DATE
        );

        foreach ($period as $day) {
            $days[$day->format('D d')] = $day;
        }

        return $days;
    }
}

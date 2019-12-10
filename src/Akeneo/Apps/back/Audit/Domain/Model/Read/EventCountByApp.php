<?php

declare(strict_types=1);

namespace Akeneo\Apps\Audit\Domain\Model\Read;

/**
 * @author Romain Monceau <romain@akeneo.com>
 * @copyright 2019 Akeneo SAS (http://www.akeneo.com)
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */
final class EventCountByApp
{
    /** @var string */
    private $appLabel;

    /** @var string */
    private $eventType;

    /** @var array */
    private $eventCounts;

    public function __construct(string $appLabel, string $eventType, array $eventCounts)
    {
        $this->appLabel = $appLabel;
        $this->eventType = $eventType;

        foreach ($eventCounts as $eventDate => $eventCount) {
            $this->eventCounts[] = new EventCountByDate($eventCount, new \DateTime($eventDate, new \DateTimeZone('UTC')));
        }
//        $this->eventCounts = $eventCounts;
    }

    public function normalize()
    {
        $eventCounts = [];
        foreach ($this->eventCounts as $eventCount) {
            $eventCounts[] = $eventCount->normalize();
        }

        return [
            $this->appLabel => $eventCounts
        ];
    }
}

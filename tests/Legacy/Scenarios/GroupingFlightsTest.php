<?php

namespace Nekman\Collection\Tests\Legacy\Scenarios;

use Nekman\Collection\Collection;
use PHPUnit\Framework\TestCase;

/**
 * More advanced usage of collection pipeline, see
 * http://martinfowler.com/articles/refactoring-pipelines.html#GroupingFlightRecords for reference.
 */
final class GroupingFlightsTest extends TestCase
{
    private array $inputData = [
        [
            "origin" => "BOS",
            "dest" => "LAX",
            "date" => "2015-01-12",
            "number" => "25",
            "carrier" => "AA",
            "delay" => 10.0,
            "cancelled" => false,
        ],
        [
            "origin" => "BOS",
            "dest" => "LAX",
            "date" => "2015-01-13",
            "number" => "25",
            "carrier" => "AA",
            "delay" => 0.0,
            "cancelled" => true,
        ],
    ];

    public function testIt(): void
    {
        $collection = new Collection($this->inputData);

        $result = $collection
            ->groupBy(fn ($v) => $v["dest"])
            ->map([$this, "summarize"])
            ->map([$this, "buildResults"])
            ->toArray();

        $expected = [
            "LAX" => [
                "meanDelay" => 10,
                "cancellationRate" => 0.5,
            ],
        ];

        $this->assertEquals($expected, $result);
    }

    private function summarize(Collection $flights): array
    {
        $numCancellations = $flights
            ->filter(fn ($f) => $f["cancelled"])
            ->size();

        $totalDelay = $flights
            ->reject(fn ($f) => $f["cancelled"])
            ->reduce(
                fn ($tmp, $f) => $tmp + $f["delay"],
                0
            );

        return [
            "numFlights" => $flights->size(),
            "numCancellations" => $numCancellations,
            "totalDelay" => $totalDelay,
        ];
    }

    private function buildResults(array $airport): array
    {
        return [
            "meanDelay" => $airport["totalDelay"] / ($airport["numFlights"] - $airport["numCancellations"]),
            "cancellationRate" => $airport["numCancellations"] / $airport["numFlights"],
        ];
    }
}

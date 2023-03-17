<?php

declare(strict_types=1);

namespace Ordinary\SearchAlgos;

use Generator;
use Ordinary\Comparator\ComparatorInterface;
use Ordinary\Comparator\Ordering;
use PHPUnit\Framework\TestCase;
use Random\Randomizer;

class BinarySearchTest extends TestCase
{
    public static function searchProvider(): Generator
    {
        $randomizer = new Randomizer();
        $lengthIndexCompareCountMap = [
            0 => [],
            1 => [0 => 1],
            2 => [0 => 2, 1 => 1],
            3 => [0 => 2, 1 => 1, 2 => 2],
            4 => [
                0 => 3,
                1 => 2,
                2 => 1,
                3 => 2,
            ],
            5 => [
                0 => 3,
                1 => 2,
                2 => 1,
                3 => 3,
                4 => 2,
            ],
            6 => [
                0 => 3,
                1 => 2,
                2 => 3,
                3 => 1,
                4 => 3,
                5 => 2,
            ],
            7 => [
                0 => 3,
                1 => 2,
                2 => 3,
                3 => 1,
                4 => 3,
                5 => 2,
                6 => 3,
            ],
            8 => [
                0 => 4,
                1 => 3,
                2 => 2,
                3 => 3,
                4 => 1,
                5 => 3,
                6 => 2,
                7 => 3,
            ],
            9 => [
                0 => 4,
                1 => 3,
                2 => 2,
                3 => 3,
                4 => 1,
                5 => 4,
                6 => 3,
                7 => 2,
                8 => 3,
            ],
        ];

        foreach ($lengthIndexCompareCountMap as $length => $indexCompareCountMap) {
            $sequences = [
                self::generateIntSequence(0, $length),
                self::generateIntSequence(1, $length),
            ];

            while ($indexCompareCountMap && count($sequences) < 5) {
                $randomInt = $randomizer->nextInt();

                if (!$generated = self::generateIntSequence($randomInt, $length)) {
                    continue;
                }

                $sequences[] = $generated;
                $sequences[] = self::generateIntSequence(($randomInt * -1) + 1, $length);
            }

            foreach ($sequences as $sequence) {
                foreach ($indexCompareCountMap as $index => $expectedCompares) {
                    yield [
                        array_slice($sequence, $index, 1)[0], // sequence value to search for
                        $sequence,
                        $expectedCompares,
                        $index,
                    ];
                }

                yield [
                    ($sequences[0][0] ?? 0) - 1, // sequence value to search for
                    $sequences[0],
                    max($indexCompareCountMap ?: [0]),
                    null,
                ];
            }
        }
    }

    /** @return int[] */
    public static function generateIntSequence(int $start, int $count): array
    {
        if (PHP_INT_MAX - $count < $start || $count <= 0) {
            return [];
        }

        return range($start, $start + $count - 1);
    }

    /**
     * @param iterable<mixed> $haystack
     * @dataProvider searchProvider
     */
    public function testSearch(mixed $needle, iterable $haystack, int $expectedCompares, ?int $expectedIndex): void
    {
        $mock = self::createMock(ComparatorInterface::class);
        $mock->expects($this->exactly($expectedCompares))
            ->method('compare')
            ->willReturnCallback(
                static fn (mixed $left, mixed $right) => Ordering::fromInt($left <=> $right)
            );

        $algo = new BinarySearch($mock);

        $index = $algo->search($needle, $haystack);

        $this->assertSame($expectedIndex, $index);
    }
}

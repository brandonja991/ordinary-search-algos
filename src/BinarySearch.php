<?php

declare(strict_types=1);

namespace Ordinary\SearchAlgos;

use Ordinary\Comparator\Comparator;
use Ordinary\Comparator\ComparatorInterface;
use Ordinary\Comparator\Ordering;

class BinarySearch
{
    private readonly ComparatorInterface $comparator;

    public function __construct(callable|ComparatorInterface|null $comparator)
    {
        $this->comparator = $comparator instanceof ComparatorInterface ? $comparator : new Comparator($comparator);
    }

    /** @param iterable<mixed> $haystack */
    public function recursiveBinarySearch(
        mixed $needle,
        iterable $haystack,
        ?int $low = null,
        ?int $high = null,
    ): ?int {
        $indexedHaystack = array_values(!is_array($haystack) ? iterator_to_array($haystack) : $haystack);

        $low ??= 0;
        $high ??= count($indexedHaystack) - 1;

        if ($high < $low) {
            return null;
        }

        $mid = (int) ceil(($high + $low) / 2);
        $midValue = $indexedHaystack[$mid];

        if (($comparisonResult = $this->comparator->compare($needle, $midValue)) === Ordering::Equal) {
            return $mid;
        }

        [$newLow, $newHigh] = match ($comparisonResult) {
            Ordering::Less => [$low, $mid - 1],
            Ordering::Greater => [$mid + 1, $high],
            default => throw new LogicException('Unexpected Ordering case encountered: ' . $comparisonResult->name),
        };

        return $this->recursiveBinarySearch($needle, $indexedHaystack, $newLow, $newHigh);
    }

    /** @param iterable<mixed> $haystack */
    public function search(mixed $needle, iterable $haystack): ?int
    {
        return $this->recursiveBinarySearch($needle, $haystack);
    }
}

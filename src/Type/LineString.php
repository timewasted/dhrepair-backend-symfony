<?php

declare(strict_types=1);

namespace App\Type;

final class LineString
{
    /**
     * @var Point[]
     */
    private array $points = [];

    public function addPoint(Point $point): void
    {
        $this->points[] = $point;
    }

    /**
     * @return Point[]
     */
    public function getPoints(): array
    {
        return $this->points;
    }

    public function getPointCount(): int
    {
        return count($this->points);
    }
}

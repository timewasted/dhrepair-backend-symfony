<?php

declare(strict_types=1);

namespace App\Type\Doctrine;

use App\Type\LineString;
use App\Type\Point;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\Type;

class LineStringType extends Type
{
    /**
     * @throws ConversionException
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform): string
    {
        if (!$value instanceof LineString) {
            throw ConversionException::conversionFailedInvalidType($value, $this->getName(), [LineString::class]);
        }

        $lineString = '';
        foreach ($value->getPoints() as $point) {
            $lineString .= pack('dd', $point->getX(), $point->getY());
        }

        return pack('xxxxcLL', '0', 2, $value->getPointCount()).$lineString;
    }

    /**
     * @throws ConversionException
     */
    public function convertToPHPValue($value, AbstractPlatform $platform): ?LineString
    {
        if (!is_string($value)) {
            return null;
        }
        if (strlen($value) < 13) {
            throw ConversionException::conversionFailedFormat($value, $this->getName(), 'value must have at least 13 characters');
        }

        /** @var array{order: string, type: int, points: int, ...<array-key, int>} $data */
        $data = unpack('x/x/x/x/corder/Ltype/Lpoints/d*point_', $value);
        if (2 !== $data['type']) {
            throw ConversionException::conversionFailedFormat($value, $this->getName(), sprintf('expected type 2, received %s', $data['type']));
        }
        $maxPointIndex = $data['points'] * 2;
        if ($data['points'] > 0 && !isset($data['point_'.$maxPointIndex])) {
            throw ConversionException::conversionFailedFormat($value, $this->getName(), sprintf('expected %d points', $maxPointIndex));
        }

        $lineString = new LineString();
        for ($i = 1; $i < $maxPointIndex; $i += 2) {
            $point = new Point((int) $data['point_'.$i], (int) $data['point_'.($i + 1)]);
            $lineString->addPoint($point);
        }

        return $lineString;
    }

    public function getName(): string
    {
        return 'linestring';
    }

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return 'LINESTRING';
    }
}

<?php

declare(strict_types=1);

namespace App\DTO;

use App\Entity\Image;

trait ImageResponseTrait
{
    protected function getImageData(Image $image): array
    {
        return [
            'id' => $image->getId(),
            'image' => $image->getImage(),
            'hash' => $image->getImageHash(),
            'title' => $image->getTitle(),
            'fullWidth' => $image->getWidth(),
            'fullHeight' => $image->getHeight(),
            'thumbWidth' => $image->getThumbWidth(),
            'thumbHeight' => $image->getThumbHeight(),
        ];
    }
}

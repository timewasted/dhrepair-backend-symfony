<?php

declare(strict_types=1);

namespace App\Tests\unit\DTO;

use App\Entity\Image;

trait ImageTestTrait
{
    protected function createImage(): Image
    {
        return (new Image())
            ->setImage(bin2hex(random_bytes(16)))
            ->setImageHash(bin2hex(random_bytes(16)))
            ->setTitle(bin2hex(random_bytes(16)))
            ->setWidth(random_int(100, 10000))
            ->setHeight(random_int(100, 10000))
            ->setThumbWidth(random_int(100, 10000))
            ->setThumbHeight(random_int(100, 10000))
        ;
    }

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

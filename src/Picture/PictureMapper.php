<?php

namespace App\Picture;

use App\Entity\Picture;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;
use DateTimeImmutable;
use Exception;
use RuntimeException;

class PictureMapper
{
    public function __construct(private string $picturesDirectory, private SluggerInterface $slugger)
    {
    }

    public function mapToPicture(Picture $picture, ?UploadedFile $uploadedFile, array $formData): Picture
    {
        if ($uploadedFile) {
            $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = $this->slugger->slug($originalFilename);
            $newFilename = $safeFilename . '-' . uniqid() . '.' . $uploadedFile->guessExtension();

            try {
                $uploadedFile->move($this->picturesDirectory, $newFilename);
            } catch (Exception $e) {
                throw new RuntimeException('Une erreur est survenue lors du téléchargement de l\'image : ' . $e->getMessage());
            }

            $picture->setFilename($newFilename);
        }

        $picture->setTitle($formData['title'] ?? '');
        $picture->setDescription($formData['description'] ?? '');
        $picture->setCreatedAt(new DateTimeImmutable());
        $picture->setUpdatedAt(new DateTimeImmutable());

        return $picture;
    }
}

<?php

namespace App\Picture;

use App\Entity\Picture;
use App\Entity\PictureUser;
use App\Entity\User;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use RuntimeException;

class PictureMapper
{
    public function __construct(
        private string $picturesDirectory,
        private SluggerInterface $slugger,
        private EntityManagerInterface $em
    ) {
    }

    public function mapToPicture(Picture $picture, ?UploadedFile $uploadedFile, array $formData): void
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

        $this->em->persist($picture);
        $this->em->flush();
    }

    public function mapToPictureUser(Picture $picture, User $user): void
    {
        $pictureUser = $this->em->getRepository(PictureUser::class)->findOneBy([
            'picture' => $picture,
            'user' => $user,
        ]);

        if ($pictureUser) {
            $isLiked = $pictureUser->isLiked();
            $pictureUser->setIsLiked(!$isLiked);
        } else {
            $pictureUser = new PictureUser();
            $pictureUser->setPicture($picture);
            $pictureUser->setUser($user);
            $pictureUser->setIsLiked(true);
            $pictureUser->setCreatedAt(new DateTimeImmutable());

            $this->em->persist($pictureUser);
        }

        $this->em->flush();
    }
}

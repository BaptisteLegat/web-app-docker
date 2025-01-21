<?php

namespace App\Controller;

use App\Entity\Picture;
use App\Entity\PictureUser;
use App\Form\PictureType;
use App\Picture\PictureMapper;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PictureController extends AbstractController
{
    #[Route('/picture', name: 'app_picture')]
    public function index(
        Request $request,
        EntityManagerInterface $em,
        PictureMapper $pictureMapper
    ): Response {
        $pictures = $em->getRepository(Picture::class)->findAll();

        $picture = new Picture();
        $form = $this->createForm(PictureType::class, $picture);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $uploadedFile = $form->get('picture')->getData();
            $formData = $form->getData();

            try {
                $picture = $pictureMapper->mapToPicture($picture, $uploadedFile, [
                    'title' => $formData->getTitle(),
                    'description' => $formData->getDescription(),
                ]);

                $em->persist($picture);
                $em->flush();

                $this->addFlash('success', 'Image uploadée avec succès.');
            } catch (Exception $e) {
                $this->addFlash('danger', $e->getMessage());
            }

            return $this->redirectToRoute('app_picture');
        }

        return $this->render('picture/index.html.twig', [
            'form' => $form->createView(),
            'pictures' => $pictures,
        ]);
    }

    #[Route('/picture/{id}/like', name: 'app_picture_like', methods: ['POST'])]
    public function toggleLike(int $id, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        $picture = $em->getRepository(Picture::class)->find($id);

        if (!$picture) {
            throw $this->createNotFoundException('Image non trouvée.');
        }

        $pictureUser = $em->getRepository(PictureUser::class)->findOneBy([
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

            $em->persist($pictureUser);
        }

        $em->flush();

        return $this->redirectToRoute('app_picture');
    }
}

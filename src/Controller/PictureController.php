<?php

namespace App\Controller;

use App\Entity\Picture;
use App\Form\PictureType;
use App\Picture\PictureMapper;
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
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        $pictures = $em->getRepository(Picture::class)->findAll();

        $picture = new Picture();
        $form = $this->createForm(PictureType::class, $picture);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $uploadedFile = $form->get('picture')->getData();
            $formData = $form->getData();

            try {
                $pictureMapper->mapToPicture($picture, $uploadedFile, [
                    'title' => $formData->getTitle(),
                    'description' => $formData->getDescription(),
                ]);

                $this->addFlash('success', 'Image ajoutée.');
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
    public function toggleLike(
        int $id,
        EntityManagerInterface $em,
        PictureMapper $pictureMapper
    ): Response {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $picture = $em->getRepository(Picture::class)->find($id);
        if (!$picture) {
            throw $this->createNotFoundException('Image non trouvée.');
        }

        try {
            $pictureMapper->mapToPictureUser($picture, $user);
        } catch (Exception $e) {
            $this->addFlash('danger', 'Une erreur est survenue.');
        }

        return $this->redirectToRoute('app_picture');
    }
}

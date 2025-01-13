<?php 

namespace App\Controller;

use App\Entity\Newsletter;
use App\Form\NewsletterType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    #[Route('/newsletter/subscribe', name: 'newsletter_subscribe', methods: ['GET', 'POST'])]
    public function subscribe(Request $request, EntityManagerInterface $entityManager): Response
    {
        $newsletter = new Newsletter();
        $form = $this->createForm(NewsletterType::class, $newsletter);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Sauvegarder les données dans la base
            $entityManager->persist($newsletter);
            $entityManager->flush();

            // Message de succès
            $this->addFlash('success', 'Vous êtes inscrit à la newsletter.');

            // Redirection pour éviter le double envoi du formulaire
            return $this->redirectToRoute('newsletter_subscribe');
        }

        return $this->render('newsletter/subscribe.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
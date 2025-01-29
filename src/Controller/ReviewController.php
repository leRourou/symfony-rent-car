<?php
namespace App\Controller;

use App\Entity\Review;
use App\Entity\Reservation;
use App\Form\ReviewType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ReviewController extends AbstractController
{
    #[Route('/reservations/{id}/review', name: 'app_reservation_review', methods: ['POST'])]
    public function review(Request $request, EntityManagerInterface $entityManager, int $id): Response
    {
        $reservation = $entityManager->getRepository(Reservation::class)->find($id);
        if (!$reservation) {
            throw $this->createNotFoundException('Réservation non trouvée');
        }

        $review = new Review();
        $form = $this->createForm(ReviewType::class, $review);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $review->setCar($reservation->getCar());
            $review->setUser($this->getUser());
            $review->setDate(new \DateTime());
            $entityManager->persist($review);
            $entityManager->flush();

            return $this->redirectToRoute('app_my_reservations');
        }

        return $this->render('reservation/review.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}

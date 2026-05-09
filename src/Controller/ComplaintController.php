<?php

namespace App\Controller;

use App\Entity\Complaint;
use App\Entity\UserEmailNo;
use App\Form\ComplaintType;
use App\Repository\ComplaintRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/complaints')]
class ComplaintController extends AbstractController
{
    #[Route('', name: 'complaint_index', methods: ['GET'])]
    public function index(ComplaintRepository $complaintRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $user = $this->getUser();
        if (!$user instanceof UserEmailNo) {
            throw $this->createAccessDeniedException();
        }

        if ($this->isGranted('ROLE_ADMIN')) {
            $complaints = $complaintRepository->findAllOrderedByCreatedAtDesc();
        } else {
            $complaints = $complaintRepository->findByUser($user);
        }

        return $this->render('complaint/index.html.twig', [
            'complaints' => $complaints,
        ]);
    }

    #[Route('/new', name: 'complaint_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $user = $this->getUser();
        if (!$user instanceof UserEmailNo) {
            throw $this->createAccessDeniedException();
        }

        $complaint = new Complaint();
        $form = $this->createForm(ComplaintType::class, $complaint);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $complaint->setCreatedBy($user);
            $complaint->setStatus(Complaint::STATUS_NEW);

            $entityManager->persist($complaint);
            $entityManager->flush();

            $this->addFlash('success', 'Complaint submitted successfully.');

            return $this->redirectToRoute('complaint_index');
        }

        return $this->render('complaint/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'complaint_show', methods: ['GET'])]
    public function show(Complaint $complaint): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $this->denyAccessUnlessGrantedToView($complaint);

        return $this->render('complaint/show.html.twig', [
            'complaint' => $complaint,
            'allowed_statuses' => Complaint::STATUSES,
        ]);
    }

    #[Route('/{id}/status', name: 'complaint_update_status', methods: ['POST'])]
    public function updateStatus(
        Complaint $complaint,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if (!$this->isCsrfTokenValid('update_status'.$complaint->getId(), (string) $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Invalid CSRF token.');
        }

        $status = (string) $request->request->get('status');
        if (!in_array($status, Complaint::STATUSES, true)) {
            $this->addFlash('error', 'Invalid status value.');

            return $this->redirectToRoute('complaint_show', ['id' => $complaint->getId()]);
        }

        $complaint->setStatus($status);
        $complaint->setUpdatedAt(new \DateTimeImmutable());

        $entityManager->flush();

        $this->addFlash('success', 'Complaint status updated successfully.');

        return $this->redirectToRoute('complaint_show', ['id' => $complaint->getId()]);
    }

    private function denyAccessUnlessGrantedToView(Complaint $complaint): void
    {
        if ($this->isGranted('ROLE_ADMIN')) {
            return;
        }

        $user = $this->getUser();
        if (!$user instanceof UserEmailNo) {
            throw $this->createAccessDeniedException();
        }

        $owner = $complaint->getCreatedBy();
        if ($owner === null || $owner->getId() !== $user->getId()) {
            throw $this->createAccessDeniedException();
        }
    }
}


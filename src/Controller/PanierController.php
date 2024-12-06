<?php

namespace App\Controller;

use App\Entity\Panier;
use App\Form\PanierType;
use App\Repository\PanierRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/panier')]
final class PanierController extends AbstractController
{
    // #[Route(name: 'app_panier_index', methods: ['GET'])]
    // public function index(PanierRepository $panierRepository): Response
    // {
    //     return $this->render('panier/index.html.twig', [
    //         'paniers' => $panierRepository->findAll(),
    //     ]);
    // }

    #[Route('/', name: 'app_panier_index', methods: ['GET'])]
    public function index(PanierRepository $panierRepository): Response
    {
        $user = $this->getUser(); // Si l'utilisateur est connecté
        $panier = $panierRepository->findOneBy(['user' => $user, 'isActive' => true]);
    
        if (!$panier) {
            $panier = new Panier();
            $entityManager = $this->getDoctrine()->getManager();
            $panier->setUser($user);
            $entityManager->persist($panier);
            $entityManager->flush();
        }
    
        // Calculer le total
        $total = 0;
        $items = [];
        foreach ($panier->getLignedeCommandes() as $ligne) {
            $items[] = $ligne;
            $total += $ligne->getProduct()->getPrice() * $ligne->getQuantity();
        }
    
        return $this->render('panier/index.html.twig', [
            'items' => $items,
            'total' => $total,
        ]);
    }

    #[Route('/add/{productId}', name: 'app_panier_add', methods: ['GET'])]
    public function add(
        int $productId,
        ProductRepository $productRepository,
        PanierRepository $panierRepository,
        EntityManagerInterface $entityManager
    ): Response {
        // Récupérer le produit
        $product = $productRepository->find($productId);
        if (!$product) {
            throw $this->createNotFoundException('Produit introuvable.');
        }

        // Récupérer le panier actif (si l'utilisateur est connecté, sinon créez un panier)
        $user = $this->getUser(); // Si l'utilisateur est connecté
        $panier = $panierRepository->findOneBy(['user' => $user, 'isActive' => true]);

        if (!$panier) {
            $panier = new Panier();
            $panier->setUser($user);
            $entityManager->persist($panier);
        }

        // Créer la ligne de commande
        $ligneCommande = new LignedeCommande();
        $ligneCommande->setProduct($product);
        $ligneCommande->setQuantity(1);
        $ligneCommande->setPrice($product->getPrice()); // Ou calculer le prix total
        $ligneCommande->setIdPanier($panier);

        $entityManager->persist($ligneCommande);
        $panier->addLignedeCommande($ligneCommande);

        // Sauvegarder dans la base de données
        $entityManager->flush();

        // Rediriger vers le panier
        return $this->redirectToRoute('app_panier_index');
    }

    #[Route('/new', name: 'app_panier_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $panier = new Panier();
        $form = $this->createForm(PanierType::class, $panier);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($panier);
            $entityManager->flush();

            return $this->redirectToRoute('app_panier_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('panier/new.html.twig', [
            'panier' => $panier,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_panier_show', methods: ['GET'])]
    public function show(Panier $panier): Response
    {
        return $this->render('panier/show.html.twig', [
            'panier' => $panier,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_panier_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Panier $panier, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PanierType::class, $panier);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_panier_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('panier/edit.html.twig', [
            'panier' => $panier,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_panier_delete', methods: ['POST'])]
    public function delete(Request $request, Panier $panier, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$panier->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($panier);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_panier_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/remove/{ligneCommandeId}', name: 'app_panier_remove', methods: ['GET'])]
    public function remove(
        int $ligneCommandeId,
        LignedeCommandeRepository $lignedeCommandeRepository,
        EntityManagerInterface $entityManager
    ): Response {
        // Récupérer la ligne de commande
        $ligneCommande = $lignedeCommandeRepository->find($ligneCommandeId);
        if (!$ligneCommande) {
            throw $this->createNotFoundException('Ligne de commande introuvable.');
        }

        // Récupérer le panier associé à la ligne de commande
        $panier = $ligneCommande->getIdPanier();

        // Supprimer la ligne de commande
        $entityManager->remove($ligneCommande);
        $entityManager->flush();

        // Rediriger vers le panier
        return $this->redirectToRoute('app_panier_index');
    }

    #[Route('/panier/remove/{id}', name: 'panier_remove', methods: ['GET'])]
    public function removeProduct(int $id, PanierRepository $panierRepository, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser(); // Si l'utilisateur est connecté
        $panier = $panierRepository->findOneBy(['user' => $user, 'isActive' => true]);

        if (!$panier) {
            throw $this->createNotFoundException('Panier introuvable.');
        }

        // Trouver la ligne de commande associée au produit
        $ligneCommande = $panier->getLignedeCommandes()->filter(function ($item) use ($id) {
            return $item->getProduct()->getId() === $id;
        })->first();

        if ($ligneCommande) {
            $panier->removeLignedeCommande($ligneCommande);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_panier_index');
    }

}

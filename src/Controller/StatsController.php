<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\Marque;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/stats', name: 'admin_stats_')]
class StatsController extends AbstractController
{
    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        // Statistiques principales
        $totalProducts = $entityManager->getRepository(Product::class)->count([]);
        $totalBrands = $entityManager->getRepository(Marque::class)->count([]);

        // Obtenir la répartition des produits par catégorie
        $productsByCategory = $entityManager->createQueryBuilder()
            ->select('c.title AS categoryName', 'COUNT(p.id) AS productCount')
            ->from(Product::class, 'p')
            ->join('p.category', 'c')  // Assurez-vous que votre entité Product a un champ 'category' pour faire cette jointure
            ->groupBy('c.title')  // Groupement des produits par nom de catégorie
            ->getQuery()
            ->getResult();

        // Initialiser les tableaux pour les noms de catégories et les comptes de produits
        $categoryNames = [];
        $productCounts = [];

        foreach ($productsByCategory as $data) {
            $categoryNames[] = $data['categoryName'];
            $productCounts[] = $data['productCount'];
        }

        // Obtenir la répartition des produits par marque
        $productsByBrand = $entityManager->createQueryBuilder()
            ->select('m.nom_marque AS brandName', 'COUNT(p.id) AS productCount')
            ->from(Product::class, 'p')
            ->join('p.id_marque', 'm')  // Utilisation de 'p.marque' et non 'p.id_marque'
            ->groupBy('m.nom_marque')
            ->getQuery()
            ->getResult();

        // Initialiser les tableaux pour les noms de marques et les comptes de produits
        $brandNames = [];
        $brandProductCounts = [];

        foreach ($productsByBrand as $data) {
            $brandNames[] = $data['brandName'];
            $brandProductCounts[] = $data['productCount'];
        }

        // Transmettre toutes les données à la vue
        return $this->render('stats/index.html.twig', [
            'totalProducts' => $totalProducts,
            'totalBrands' => $totalBrands,
            'categoryNames' => $categoryNames,
            'productCounts' => $productCounts,
            'brandNames' => $brandNames,
            'brandProductCounts' => $brandProductCounts,
        ]);
    }

    /**
     * Récupère la distribution des produits par marque.
     */
    private function getProductsByBrand(EntityManagerInterface $entityManager): array
    {
        $result = $entityManager->getRepository(Marque::class)->createQueryBuilder('m')
            ->select('m.nom_marque AS brandName, COUNT(p.id) AS productCount')
            ->leftJoin('m.products', 'p')
            ->groupBy('m.id')
            ->getQuery()
            ->getResult();

        return $result;
    }

    /**
     * Calcule le prix moyen des produits.
     */
    private function getAverageProductPrice(EntityManagerInterface $entityManager): float
    {
        $result = $entityManager->getRepository(Product::class)->createQueryBuilder('p')
            ->select('AVG(p.price) AS avgPrice')
            ->getQuery()
            ->getSingleScalarResult();

        return (float) $result;
    }
}

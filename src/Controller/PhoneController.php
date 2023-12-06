<?php

namespace App\Controller;

use App\Repository\PhoneRepository;
use App\Entity\Phone;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use JMS\Serializer\SerializerInterface;
use JMS\Serializer\SerializationContext;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

class PhoneController extends AbstractController
{
    //Récupère la liste des téléphones
    #[Route('api/phones', name: 'phone', methods: ['GET'])]
    public function getPhoneList(PhoneRepository $phoneRepository, SerializerInterface $serializer, Request $request, TagAwareCacheInterface $cache): JsonResponse
    {
        $page = $request->query->get('page', 1);
        $limit = $request->get('limit', 5);

        // Génére une clé unique pour chaque combinaison de paramètres
        $cacheKey =  "phones-" . $page . "-" . $limit;

        // Essaye de réccupérer les données mises en cache
        $cachedData = $cache->get($cacheKey, function ($item) use ($phoneRepository, $serializer, $page, $limit) {
            // Si les données ne sont pas dans le cache, on effectue la requête pour la mettre en cache
            $phoneList = $phoneRepository->paginate($page, $limit);

            $phoneData = [];

            //Génère les données du téléphone
            foreach ($phoneList as $phone) {
                $phoneData[] = [
                    'id' => $phone->getId(),
                    'name' => $phone->getName(),
                    'phoneDetailsUrl' => '/api/phones/'.$phone->getId(),
                    'description' => $phone->getDescription(),
                    'price' => $phone->getPrice(),
                ];
            }

            $jsonPhoneList = $serializer->serialize($phoneData, 'json');
            // Met les données en cache
            $item->set($jsonPhoneList);

            return $jsonPhoneList;

        });

        //Permet d'éviter l'erreur "Variable non définie"
        $jsonPhoneList = $cachedData ?? '';

        return new JsonResponse($jsonPhoneList, Response::HTTP_OK, [], true);
    }

    // Récuppère le détail d'un téléphone
    #[Route('/api/phones/{id}', name: 'detailPhone', methods: ['GET'])]
    public function getDetailPhone(Phone $phone, SerializerInterface $serializer): JsonResponse 
    {
        $jsonPhone = $serializer->serialize($phone, 'json');
        return new JsonResponse($jsonPhone, Response::HTTP_OK, ['accept' => 'json'], true);
    }
}

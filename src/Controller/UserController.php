<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Client;
use App\Repository\UserRepository;
use App\Repository\ClientRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\SerializerInterface;
use JMS\Serializer\SerializationContext;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Contracts\Cache\CacheItemInterface;
use Symfony\Component\Cache\Adapter\TagAwareAdapter;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserController extends AbstractController
{       

    //On réccupère la liste des utilisateurs liés à un client
    #[Route('api/clients/{clientId}/users', name: 'clientUsers', methods: ['GET'])]
    #[ParamConverter("client", class: Client::class, options: ["id" => "clientId"])]
    public function getClientUsers(Client $client, UserRepository $userRepository, SerializerInterface $serializer,  Request $request, TagAwareCacheInterface $cache
    ): JsonResponse {
        //Pagination des résultats en ajoutant 'limit' et 'page'
        $page = $request->query->get('page', 1);
        $limit = $request->get('limit', 3);

        // Génére une clé unique pour chaque combinaison de paramètres
        $cacheKey =  "clientUsers-" . $page . "-" . $limit;

        // Essaye de réccupérer les données mises en cache
        $cachedData = $cache->get($cacheKey, function ($item) use ($client, $userRepository, $serializer, $page, $limit) {
            echo("L'élément n'est pas encore en cache");
            // Si les données ne sont pas dans le cache, on effectue la requête pour la mettre en cache
            $userList = $userRepository->paginate($client, $page, $limit);

            //Génère une url menant au détail de l'utilisateur
            foreach ($userList as $user) {
                $user->userDetailsUrl = '/api/clients/' . $client->getId() . '/users/' . $user->getId();
            }

            $userData = [];

            //Génère les données de l'utilisateur
            foreach ($userList as $user) {
                $userData[] = [
                    'id' => $user->getId(),
                    'username' => $user->getUsername(),
                    'email' => $user->getEmail(),
                    'userDetailsUrl' => $user->userDetailsUrl,
                    'client' => [
                        'id' => $client->getId(),
                        'name' => $client->getName(),
                    ],
                ];
            }

            $context = SerializationContext::create()->setGroups(['user_list']);
            $jsonUserList = $serializer->serialize($userData, 'json', $context);

            // Met les données en cache
            $item->set($jsonUserList);

            return $jsonUserList;
        });

        //Permet d'éviter l'erreur "Variable non définie"
        $jsonUserList = $cachedData ?? '';

        return new JsonResponse($jsonUserList, Response::HTTP_OK, [], true);
    }

    //On réccupère les détails d'un utilisateur lié à un client
    #[Route('api/clients/{clientId}/users/{userId}', name: 'userDetails', methods: ['GET'])]
    #[ParamConverter("client", class: Client::class, options: ["id" => "clientId"])] 
    #[ParamConverter("detailUser", class: User::class, options: ["id" => "userId"])]
    public function getClientUserDetails(Client $client, User $user, SerializerInterface $serializer): JsonResponse
    {

        $context = SerializationContext::create()->setGroups(['user_details']);
        $jsonUser = $serializer->serialize($user, 'json', $context);
    
        return new JsonResponse($jsonUser, Response::HTTP_OK, [], true);

    }

    //On supprime un utilisateur
    #[Route('api/clients/{clientId}/users/{userId}', name: 'deleteUser', methods: ['DELETE'])]
    #[ParamConverter("client", class: Client::class, options: ["id" => "clientId"])] 
    #[ParamConverter("user", class: User::class, options: ["id" => "userId"])]
    #[IsGranted('ROLE_ADMIN', message: 'Vous n\'avez pas les droits suffisants pour supprimer un utilisateur')]
    public function deleteUser(Client $client, User $user, EntityManagerInterface $em): JsonResponse 
    {
        $em->remove($user);
        $em->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    // Créer un nouvel utilisateur
    #[Route('api/clients/{clientId}/users', name: 'createUser', methods: ['POST'])]
    #[ParamConverter("client", class: Client::class, options: ["id" => "clientId"])]
    #[IsGranted('ROLE_ADMIN', message: 'Vous n\'avez pas les droits suffisants pour créer un utilisateur')]
    public function createUser(Request $request, Client $client, EntityManagerInterface $em, SerializerInterface $serializer, ValidatorInterface $validator): JsonResponse
    {
        $user = $serializer->deserialize($request->getContent(), User::class, 'json');
        $user->setClient($client);
        $user->setRoles(["ROLE_USER"]);

        // Vérification des données requises
        if (!$user->getUsername() || !$user->getEmail() || !$user->getPassword()) {
            $data = [
                'status' => 400,
                'message' => 'Elément requis manquant',
            ];

            return new JsonResponse($data, 400);
        }

        //Validation
        $errors = $validator->validate($user);

        if (count($errors) > 0) {
        $errorMessages = [];
        foreach ($errors as $error) {
            $errorMessages[] = $error->getMessage();
        }

        $data = [
            'status' => 400,
            'message' => 'Erreur de validation',
            'errors' => $errorMessages,
        ];

        return new JsonResponse($data, 400);
        }

        // Encodage du mot de passe
        $hashedPassword = password_hash($user->getPassword(), PASSWORD_BCRYPT);
        $user->setPassword($hashedPassword);

        $em->persist($user);
        $em->flush();

        $data = [
            'status' => 201,
            'message' => 'L\'utilisateur a bien été ajouté'
        ];
        return new JsonResponse($data, 201);
    }
}

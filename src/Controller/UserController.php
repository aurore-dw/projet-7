<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Client;
use App\Repository\UserRepository;
use App\Repository\ClientRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use JMS\Serializer\SerializerInterface;
use JMS\Serializer\SerializationContext;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Doctrine\ORM\EntityManagerInterface;

class UserController extends AbstractController
{
    //On réccupère la liste des utilisateurs liés à un client
    #[Route('api/clients/{clientId}/users', name: 'clientUsers', methods: ['GET'])]
    #[ParamConverter("client", class: Client::class, options: ["id" => "clientId"])]
    public function getClientUsers(Client $client, UserRepository $userRepository, SerializerInterface $serializer
    ): JsonResponse {

        $userList = $userRepository->findBy(['client' => $client]);

        // Itérer sur la liste des utilisateurs pour ajouter l'URL
        foreach ($userList as $user) {
            $user->userDetailsUrl = '/api/clients/' . $client->getId() . '/users/' . $user->getId();
        }

        $userData = [];

        foreach ($userList as $user) {
            $userData[] = [
                'id' => $user->getId(),
                'username' => $user->getUsername(),
                'email' => $user->getEmail(),
                'userDetailsUrl' => $user->userDetailsUrl, // Inclut l'URL dans les données à sérialiser
                'client' => [
                    'id' => $client->getId(),
                    'name' => $client->getName(),
                ],
            ];
        }

        $context = SerializationContext::create()->setGroups(['user_list']);
        $jsonUserList = $serializer->serialize($userData, 'json', $context);

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
    public function deleteUser(Client $client, User $user, EntityManagerInterface $em): JsonResponse 
    {
        $em->remove($user);
        $em->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}

<?php

namespace App\Controller;

use App\Repository\PhoneRepository;
use App\Entity\Phone;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use JMS\Serializer\SerializerInterface;
use JMS\Serializer\SerializationContext;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use Symfony\Component\HttpKernel\Event\ExceptionEvent;

class PhoneController extends AbstractController
{
    #[Route('api/phones', name: 'phone', methods: ['GET'])]
    public function getPhoneList(PhoneRepository $phoneRepository, SerializerInterface $serializer): JsonResponse
    {
        $phoneList = $phoneRepository->findAll();
        $jsonPhoneList = $serializer->serialize($phoneList, 'json');
        return new JsonResponse($jsonPhoneList, Response::HTTP_OK, [], true);
    }

    #[Route('/api/phones/{id}', name: 'detailPhone', methods: ['GET'])]
    public function getDetailPhone(Phone $phone, SerializerInterface $serializer): JsonResponse 
    {
        $jsonPhone = $serializer->serialize($phone, 'json');
        return new JsonResponse($jsonPhone, Response::HTTP_OK, ['accept' => 'json'], true);
    }
}

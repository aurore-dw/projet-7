<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class ExceptionSubscriber implements EventSubscriberInterface
{

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        
        if ($exception instanceof AccessDeniedHttpException) {
            //Erreur 403 - Forbidden
            $data = [
                'status' => $exception->getStatusCode(),
                //'message' => 'Accès refusé : ' . $exception->getMessage()
            ];
            $event->setResponse(new JsonResponse($data));
        } elseif ($exception instanceof UnauthorizedHttpException) {
            //Erreur 401 - Non authorisé
            $data = [
                'status' => $exception->getStatusCode(),
                //'message' => 'Authentification requise : ' . $exception->getMessage()
            ];
            $event->setResponse(new JsonResponse($data));
        } elseif ($exception instanceof NotFoundHttpException) {
            //Erreur 404 - Not found
            $url = $event->getRequest()->getRequestUri();
            $data = [
                'status' =>  $exception->getStatusCode(),
                //'message' => "Ressource introuvable : $url"
            ];
            $event->setResponse(new JsonResponse($data));
        } elseif ($exception instanceof BadRequestHttpException) {
            // Erreur 400 - Requête incorrecte
            $data = [
                'status' => $exception->getStatusCode(),
                //'message' => 'Requête incorrecte : ' . $exception->getMessage()
            ];
        } else {
            //Autres erreurs
            $data = [
                'status' => '500',
                'message' => $exception->getMessage()
            ];
            $event->setResponse(new JsonResponse($data));
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => 'onKernelException',
        ];
    }

}



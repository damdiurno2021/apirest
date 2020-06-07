<?php


namespace App\EventListener;

use DateTime;
use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTDecodedEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTEncodedEvent;
use Symfony\Component\HttpFoundation\RequestStack;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;

class JWTListener
{
    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @param RequestStack $requestStack
     */
    public function __construct(RequestStack $requestStack, EntityManagerInterface $em)
    {
        $this->requestStack = $requestStack;
        $this->em = $em;
    }


    /**
     * @param JWTCreatedEvent $event
     *
     * @return void
     */
    public function onJWTCreated(JWTCreatedEvent $event)
    {
        $expiration = new \DateTime('+1 day');
        $expiration->setTime(2, 0, 0);

        $payload        = $event->getData();
        $payload['exp'] = $expiration->getTimestamp();

        $event->setData($payload);
    }

    /**
     * @param JWTDecodedEvent $event
     *
     * @return void
     */
    public function onJWTDecoded(JWTDecodedEvent $event)
    {
        $request = $this->requestStack->getCurrentRequest();

        $payload = $event->getPayload();

        $user = $this->em->getRepository("App:User")->findOneBy(["username" => $payload["username"]]);
        if ( is_null($user) ) {
            $event->markAsInvalid();
        } else {
            if ( !$user->getEnabled()) $event->markAsInvalid();
        }
//        if (!isset($payload['ip']) || $payload['ip'] !== $request->getClientIp()) {
//            $event->markAsInvalid();
//        }
    }


    /* Si interesa hay activar el evento en services.yaml */
    /**
     * @param JWTEncodedEvent $event
     */
    public function onJWTEncoded(JWTEncodedEvent $event)
    {
        $token = $event->getJWTString();
        $request = $this->requestStack->getCurrentRequest();
        $username = $request->get("_username");
        $user = $this->em->getRepository("App:User")->findOneBy(["username" => $username]);
        if ( is_null($user) ) {
            // TODO: no debería pasar, pero hay que mirar qué hacer en este caso
        } else {
            if ( is_null($user->getApiToken()) ) {
                try {
                    $token = $event->getJWTString();

                    $user->setApiToken($token);
                    $this->em->persist($user);
                    $this->em->flush();
                } catch (\Exception $ex) {
                    // TODO: pensar qué hacer en este caso.
                    echo $ex->getMessage();
                }
            }

        }

    }
}
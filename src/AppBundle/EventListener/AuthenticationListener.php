<?php

namespace AppBundle\EventListener;


use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Event\AuthenticationFailureEvent;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Core\Security;

/**
 * Class AuthenticationListener
 * @package AppBundle\EventListener
 *
 * Logging User authentication Events
 */
class AuthenticationListener
{

    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var RequestStack
     */
    private $requestStack;

    public function __construct(LoggerInterface $logger, RequestStack $requestStack)
    {
        $this->logger = $logger;
        $this->requestStack = $requestStack;

    }

    /**
     * Log user authentication fail
     * @param AuthenticationFailureEvent $event
     */
    public function onAuthenticationFailure(AuthenticationFailureEvent $event)
    {
        $data = $this->getUserData();
        $message = 'Invalid credentials, for user: '. $data['username'] . ' from :' . $data['userIp'] ;
        $this->logger->alert($message);
    }

    /**
     * Log user authentication success
     * @param InteractiveLoginEvent $event
     */
    public function onAuthenticationSuccess(InteractiveLoginEvent $event)
    {
        $data = $this->getUserData();
        $message = 'Successful login for user : ' . $data['username'] . ' from : ' . $data['userIp'];
        $this->logger->info($message);
    }

    private function getUserData()
    {
        $request = $this->requestStack->getCurrentRequest();
        $clientIp = $request->getClientIp();
        $username = $request->getSession()->get(Security::LAST_USERNAME);

        return [
            'username' => $username,
            'userIp' => $clientIp,
        ];
    }
}
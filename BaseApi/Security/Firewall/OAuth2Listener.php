<?php

namespace OpenOrchestra\BaseApi\Security\Firewall;

use OpenOrchestra\BaseApi\Exceptions\HttpException\ClientAccessDeniedHttpException;
use OpenOrchestra\BaseApi\Security\Authentication\Token\OAuth2Token;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Firewall\ListenerInterface;

/**
 * Class OAuth2Listener
 */
class OAuth2Listener implements ListenerInterface
{
    protected $securityContext;
    protected $authenticationManager;

    /**
     * @param TokenStorageInterface          $securityContext
     * @param AuthenticationManagerInterface $authenticationManager
     */
    public function __construct(TokenStorageInterface $securityContext, AuthenticationManagerInterface $authenticationManager)
    {
        $this->securityContext       = $securityContext;
        $this->authenticationManager = $authenticationManager;
    }

    /**
     * @param GetResponseEvent $event
     *
     * @throws ClientAccessDeniedHttpException
     */
    public function handle(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        if ($this->securityContext->getToken() instanceof TokenInterface && $this->securityContext->getToken()->isAuthenticated()) {
            return;
        }

        if (!($accessToken = $request->get('access_token'))) {
            throw new ClientAccessDeniedHttpException();
        }

        $token = $this->authenticationManager->authenticate(OAuth2Token::create($accessToken));
        $this->securityContext->setToken($token);
    }
}

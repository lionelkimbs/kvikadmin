<?php

namespace Kvik\AdminBundle\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;


class LoggedUserListener
{
    private $router;
    private $authChecker;

    public function __construct(RouterInterface $router, AuthorizationCheckerInterface $authChecker)
    {
        $this->router = $router;
        $this->authChecker = $authChecker;
    }

    /**
     * Redirect user to homepage if tryes to access in anonymously path
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        $path = $request->getPathInfo();

        //*LK: Disables authentication for assets and the profiler, adapt it according to your needs
        if( !preg_match('/(_(profiler|wdt)|css|images|js)/', $request->getPathInfo()) ){
            if ($this->authChecker->isGranted('IS_AUTHENTICATED_FULLY') && $this->isAnonymouslyPath($path)) {
                $response = new RedirectResponse($this->router->generate('kvik_admin_index'));
                $event->setResponse($response);
            }
        }

    }

    /**
     * Check if $path is an anonymously path
     * @param string $path
     * @return bool
     */
    private function isAnonymouslyPath($path)
    {
        return preg_match('/\/login|\/register|\/resetting/', $path) ? true : false;
    }
}

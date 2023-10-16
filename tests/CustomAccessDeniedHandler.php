<?php
//
//use Symfony\Component\Security\Http\Authorization\AccessDeniedHandlerInterface;
//use Symfony\Component\HttpFoundation\RedirectResponse;
//use Symfony\Component\HttpFoundation\Request;
//use Symfony\Component\HttpFoundation\Response;
//use Symfony\Component\Security\Core\Exception\AccessDeniedException;
//use Symfony\Component\Routing\RouterInterface;
//use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
//
//class CustomAccessDeniedHandler implements AccessDeniedHandlerInterface
//{
//    private $templating;
//
//    public function __construct(EngineInterface $templating)
//    {
//        $this->templating = $templating;
//    }
//
//    public function handle(Request $request, AccessDeniedException $accessDeniedException)
//    {
//        if ($this->authorizationChecker->isGranted('ROLE_USER')) {
//            return new RedirectResponse($this->router->generate('app_homepage'));
//        } else {
//            return new RedirectResponse($this->router->generate('app_login'));
//        }
//    }
//}

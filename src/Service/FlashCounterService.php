<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class FlashCounterService
{
    private $session;

    public function __construct(
        private RequestStack $requestStack,
    ) {
        $this->session = $requestStack->getSession();
    }

    public function getFlashCounter()
    {
        return (int)$this->session->get('flash_counter', 0);
    }

    public function incrementFlashCounter()
    {   $session = $this->requestStack->getSession();

        $counter = $this->getFlashCounter();
        $this->session->set('flash_counter', $counter + 1);
    }

    public function resetFlashCounter()
    {
        $session = $this->requestStack->getSession();

        $this->session->set('flash_counter', 0);
    }
}

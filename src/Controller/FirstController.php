<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FirstController
{

    /**
     * @Route("/first")
     * @return Response
     */
    public function homepage(): Response
    {
        return new Response(json_encode($this->prepareData()));
    }

    #[Route('/blog', name: 'blog_list')]
    public function list(): Response
    {
        // ...
    }

    protected function prepareData(): array
    {
        return [
            'title' => 'My awesome blog',
            'posts' => [
                ['title' => 'Hello, world!' ],
                ['title' => 'Foo'],
                ['title' => 'Bar'],
            ],
        ];
    }

}

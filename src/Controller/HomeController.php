<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

//добавленные элементы
use Symfony\Component\HttpFoundation\Request;

class HomeController extends AbstractController
{
   #[Route(path :"/", name : "home")]
   public function index(Request $request): Response{
   //var_dump($request);
   //dump($request);
   //die;
   //dd($request);

   return $this->render('home/index.html.twig');
   }
}

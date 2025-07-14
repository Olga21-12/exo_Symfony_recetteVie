<?php
namespace App\Controller;

use App\Repository\RecipeRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

use App\Entity\User;

use Symfony\Component\HttpFoundation\Request;
use App\Entity\Recipe;
use App\Form\RecipeType;
use App\Data\SearchData;
use App\Entity\Comment;
use App\Form\CommentType;
use App\Form\SearchType;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\BrowserKit\Request as BrowserKitRequest;

use Symfony\Contracts\Translation\TranslatorInterface;

final class RecipeController extends AbstractController
{
    // вариант с несколькими страницами

    #[Route(path :'/recette', name : 'app_recipe_index')] // стартовая страница
   public function index(Request $request, 
                        RecipeRepository $repository, 
                        EntityManagerInterface $em, 
                        TranslatorInterface $translator,
                        PaginatorInterface $paginator): Response{

        $searchData = new SearchData();
    $form = $this->createForm(SearchType::class, $searchData);
    $form->handleRequest($request);

    $queryBuilder = $repository->createQueryBuilder('r');

    if ($searchData->query) {
        $queryBuilder
            ->andWhere('r.title LIKE :q')
            ->setParameter('q', '%' . $searchData->query . '%');
    }

    $recipes = $paginator->paginate(
        $queryBuilder->getQuery(),
        $request->query->getInt('page', 1),
        9
    );

    return $this->render('recipe/index.html.twig', [
        'recipes' => $recipes,
        'searchForm' => $form->createView(),
    ]);      

    if($this->getUser()){

         /**Add commentMore actions
            * @var User
            */

        $user = $this->getUser();
        if(!$user->isVerified()){
            $this->addFlash("info","Your email addres is not verified !");
        }
    }

         if ($this->getUser()) {
         /** @var \App\Entity\User $user */
             $user = $this->getUser();

        if (!$user->isVerified()) {
            $this->addFlash('info', $translator->trans('Your email address is not verified'));
    }
}


    $query = $repository->createQueryBuilder('r')
        ->orderBy('r.createdAt', 'DESC')
        ->getQuery();

    $recipes = $paginator->paginate(
        $query,
        $request->query->getInt('page', 1), // текущая страница из URL
        9 // количество рецептов на странице
    );



    //$recipes = $repository->findAll();
    //$recipes = $repository->findRecipeDurationLowerThan(90); // отображение рецептов которые готовятся менее или 90 минут, условия прописаны в репозитории
    //dd($recipes); // отображает все атрибуты

    //=========================================================

    // добавление рецепта  
    //$recipe = new Recipe;      
    //$recipe->setTitle('Crème glacée au chocolat')
    //    ->setSlug('creme-glacee-au-chocolat')
    //    ->setContent('Étape 1 Casser les carrés du chocolat et les faire fondre dans une casserole avec le lait, sans cesser de remuer.
    //                  Étape 2 Battre les jaunes d\'œufs avec le sucre. Y incorporer le chocolat fondu et faire chauffer le tout à feu doux jusqu\'à ce que la crème épaississe. Laisser refroidir.
    //                  Étape 3 Ajouter la crème fraîche et verser le tout dans une sorbetière pendant 4 h.')
    //    ->setDuration(35)
    //    ->setCreatedAt(new DateTimeImmutable())
    //   ->setUpdatedAt(new DateTimeImmutable());
    //$em->persist($recipe); // добавление в базу данных
    //$em->flush(); // отправка в базу данных

    //==================================================================

    // изменение названия второго (по счету) рецепта в базе данных, меняется при перезагрузки страницы 

    //$recipes[5]->setTitle("Rôti de bœuf au airfryer");
    //$em->flush(); // отправка в базу данных

    //$recipes[5]->setTitle("Profiteroles maison");
    //           ->setSlug("profiteroles-maison"); // изменение названия и слага 6-го рецепта в базе данных, меняется при перезагрузке страницы
    //           ->setContent("Étape 1 Préchauffez le four à 180°C (thermostat 6). Dans une casserole, faites chauffer l'eau, le beurre et le sel jusqu'à ébullition. Retirez du feu et ajoutez la farine d'un coup. Mélangez vigoureusement jusqu'à obtenir une pâte homogène. Remettez sur feu doux pendant 1 à 2 minutes pour dessécher la pâte.
    //                         Étape 2 Hors du feu, incorporez les œufs un par un en mélangeant bien entre chaque ajout. La pâte doit être lisse et brillante. Formez des boules de pâte sur une plaque de cuisson recouverte de papier sulfurisé. Enfournez pendant 20 à 25 minutes jusqu'à ce qu'elles soient dorées et gonflées. Laissez refroidir avant de les garnir de crème pâtissière ou de glace.");
    //           ->setDuration(45);
    //$em->flush(); // отправка в базу данных

    //=================================================================

    // удаление рецепта из базы данных
    
    //$em->remove($recipes[4]); // удаляем рецепт 5-ый по счету в базе данных
    //$em->flush(); // отправка в базу данных
    
    //=================================================================
    //comment récuperer nos recettes sans appeler le RecipeRepository
        //$recipes = $em->getRepository(Recipe::class)->findAll();


    return $this->render('recipe/index.html.twig', ['recipes' => $recipes]);
   }

    #[Route('/recette/{slug}-{id}', name: 'app_recipe_show', requirements: ['id'=>'\d+', 'slug'=> '[a-z0-9-]+'])] // страница списка рецептов и каждого рецепта отдельно
    //requirements: ['id'=>'\d+', 'slug'=> '[a-z0-9-]+']) = чтобы id содержал только цифры, все остальное в названии
   /* public function show(): Response
    {return new Response("Bienvenue sur la page des recettes !!!");}*/

    public function show(Request $request, string $slug, int $id, RecipeRepository $repository, EntityManagerInterface $em): Response{
        //dd($request); отображает все атрибуты
        //dd($slug, $id); // отображает только название и id

        $recipe = $repository->find($id);
        if ($recipe->getSlug() !==$slug){
            return $this->redirectToRoute('app_recipe_show', [
                'id' => $recipe->getId(),
                'slug' => $recipe->getSlug()
            ]);
        }
        
      /*  return $this->render('recipe/show.html.twig', [
            'recipe' => $recipe,
            'id' => $id,
            'user' => [
                "firstname" => "Olga",
                "lastname" => "P"
            ]
        ]);*/

           // === 💬 Добавление комментария ===
    $comment = new Comment();
$comment->setCreatedAt(new \DateTimeImmutable());
$comment->setRecipe($recipe);

$form = $this->createForm(CommentType::class, $comment, [
    'is_user' => $this->getUser() !== null,
]);

$form->handleRequest($request);

if ($form->isSubmitted() && $form->isValid()) {
    if ($this->getUser()) {
        $comment->setUser($this->getUser());
    }

    $em->persist($comment);
    $em->flush();
    $this->addFlash('success', 'Commentaire ajouté !');
    return $this->redirectToRoute('app_recipe_show', [
        'id' => $recipe->getId(),
        'slug' => $recipe->getSlug()
    ]);
}

return $this->render('recipe/show.html.twig', [
    'recipe' => $recipe,
    'commentForm' => $form->createView(),
    'comments' => $recipe->getComments()
]);


}

  // для страницы редактирования рецепта с помощью формы
  #[Route(path: "/recette/{id}/edit", name: "app_recipe_edit")]
        public function edit(Recipe $recipe, Request $request, EntityManagerInterface $em): Response{

            if($this->getUser()){
            /**
            * @var User
            */
            $user = $this->getUser();
            if(!$user->isVerified()){
                $this->addFlash("error", "You must confirm your email to edit a Recipe !");
                return $this->redirectToRoute('app_recipe_index');
            }
            if($user->getEmail() !== $recipe->getUser()->getEmail()){
                $this->addFlash("error", "You must to be ". $recipe->getUser()->getEmail() . " to edit this Recipe !");
                return $this->redirectToRoute('app_recipe_index');
            }    
        }else{
            $this->addFlash("error", "You must login to edit a Recipe !");
            return $this->redirectToRoute("app_login");
        }

            // dd($recipe);
            //cet methode prend en premier parametre le formulaire que l'on souhaite utiliser en second parametre elle prend les données
            $form = $this->createForm(RecipeType::class, $recipe);
            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid()){  // для работы кнопки и замены данных в базе данных
                $recipe->setUpdatedAt(new DateTimeImmutable());
                $em->flush();
                $this->addFlash('success','La recette a bien été modifiée'); //сообщение и успешном редактировании рецепта
              //  return $this->redirectToRoute('app_recipe_index'); // после редактирования переходит на страницу списка рецептов
                return $this->redirectToRoute('app_recipe_show', ['id' => $recipe->getId(),'slug' => $recipe->getSlug()]); // после редактирования переходит на страницу рецепта
            } 
            return $this->render('recipe/edit.html.twig', [
                'monForm' => $form,
                'recipe' => $recipe
            ]);


        }

    // для страницы создания рецепта с записью в базе данных
    #[Route(path : '/recette/create', name : 'app_recipe_create')]  
    public function create(Request $request, EntityManagerInterface $em) : Response {
        
        if($this->getUser()){
            /**
            * @var User
            */
            $user = $this->getUser();
            if(!$user->isVerified()){
                $this->addFlash("error", "You must confirm your email to create a Recipe !");
                return $this->redirectToRoute('app_recipe_index');
            }
        }else{
            $this->addFlash("error", "You must login to create a Recipe !");
            return $this->redirectToRoute("app_login");
        }
        
        $recipe = new Recipe;
        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $recipe->setUser($this->getUser()); // для того чтобы пользователь мог создавать свои рецепты
            $recipe->setCreatedAt(new DateTimeImmutable());
            $recipe->setUpdatedAt(new DateTimeImmutable());
            $em->persist($recipe);
            $em->flush();
            $this->addFlash('success', 'La recette '.$recipe->getTitle(). ' a bien été crée !');
            return $this->redirectToRoute('app_recipe_index');
        }
        return $this->render('recipe/create.html.twig', [
            'monForm'=>$form
        ]);
    }  

    //для кнопки удаления рецепта
    #[Route(path: "/recette/{id}/delete", name: "app_recipe_delete")]
        public function delete(Recipe $recipe, Request $request , EntityManagerInterface $em): Response{
            // $title = $recipe->getTitle();

            if($this->getUser()){
            /**
            * @var User
            */
            $user = $this->getUser();
            if(!$user->isVerified()){
                $this->addFlash("error", "You must confirm your email to delete a Recipe !");
                return $this->redirectToRoute('app_recipe_index');
            }
            if($user->getEmail() !== $recipe->getUser()->getEmail()){
                $this->addFlash("error", "You must to be ". $recipe->getUser()->getEmail() . " to delete this Recipe !");
                return $this->redirectToRoute('app_recipe_index');
            } 
        }else{
            $this->addFlash("error", "You must login to delete a Recipe !");
            return $this->redirectToRoute("app_login");
        }    
        
        
        $title = $recipe->getTitle();

            $em->remove($recipe);
            $em->flush();
            $this->addFlash('info','La recette '.$title. ' a bien été suprimée !');
            return $this->redirectToRoute('app_recipe_index'); // после редактирования переходит на страницу списка рецептов
                // return $this->redirectToRoute('app_recipe_show', ['id' => $recipe->getId(),'slug' => $recipe->getSlug()]); после редактирования переходит на страницу рецепта
            } 

            
    
}




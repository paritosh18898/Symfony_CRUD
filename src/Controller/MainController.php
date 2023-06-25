<?php

namespace App\Controller;
use App\Entity\Crud;
use App\Form\CrudType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

// use Doctrine\Bundle\DoctrineBundle\Registry;

class MainController extends AbstractController
{
    private $doctrine;
    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }
    

    // #[Route('/main/{slug}', name: 'app_main')]
    #[Route('/main/{page<\d+>}', name: 'app_main')]
    public function index()
    {
        $repository = $this->doctrine->getRepository(Crud::class);
        $data = $repository->findAll();
        return $this->render('main/index.html.twig', [
            'list' => $data,
        ]);
        
    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => null,
        ]);
    }
    
    
    #[Route('/create', name: 'create')]
    public function create(Request $request, EntityManagerInterface $entityManager,UrlGeneratorInterface $urlGenerator)
    {
        $crud = new Crud();
        $form = $this->createForm(CrudType::class, $crud);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($crud);
            $entityManager->flush();

            $this->addFlash('notice', 'Submitted Successfully');
               return $this->render('main/flash_message.html.twig');

            
            // after insertion done we redirect to homepage route
            // return $this->redirectToRoute('app_main');
        // return $this->render('main/successfulmessage.html.twig');
        // $url = $urlGenerator->generate('app_main', ['page' => 1]); // Provide the 'page' parameter
    
        // return $this->redirect($url);

        }
        
        return $this->render('main/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

 /**
     * @Route("/delete/{id}", name="delete")
     */
/**
 * @Route("/update/{id}", name="update")
 */

// ...

public function update(Request $request, $id, EntityManagerInterface $entityManager, SessionInterface $session, UrlGeneratorInterface $urlGenerator)
{
    $crud = $this->doctrine->getRepository(Crud::class)->find($id);
    $form = $this->createForm(CrudType::class, $crud);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $entityManager->flush();
        $this->addFlash('notice', 'Updated Successfully');

        // Return the flash message immediately to make it available in the current request
        // $session->getFlashBag()->peek('notice');

        // return $this->redirectToRoute('app_main');
        $url = $urlGenerator->generate('app_main', ['page' => 1]); // Provide the 'page' parameter
    
        // return $this->redirect($url);
        return $this->render('main/flash_message.html.twig');
    }

    return $this->render('main/update.html.twig', [
        'form' => $form->createView()
    ]);
}

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete($id, EntityManagerInterface $entityManager, SessionInterface $session, UrlGeneratorInterface $urlGenerator)
    {
        $crud = $this->doctrine->getRepository(Crud::class)->find($id);
        $entityManager->remove($crud);
        $entityManager->flush();
        $this->addFlash('notice', 'Deleted Successfully');
        // $session->getFlashBag()->peek('notice');
        // return $this->redirectToRoute('app_main');
        $url = $urlGenerator->generate('app_main', ['page' => 1]); // Provide the 'page' parameter
    
        // return $this->redirect($url);
        return $this->render('main/flash_message.html.twig');

    }



}


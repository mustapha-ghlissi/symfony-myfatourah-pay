<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Contact;
use App\Entity\Promise;
use App\Repository\UserRepository;
use App\Repository\ContactRepository;
use App\Repository\PromiseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        return $this->render('admin/admin_home.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    #[Route('/admin_message', name: 'admin_message')]
    #[Route('/contact/{id}/remove', name: 'removeContact')]
    public function message(Request $request, ContactRepository $repoContact,PaginatorInterface $paginator, EntityManagerInterface $manager, Contact $removeContact = null): Response
    {
        
        $colonnes = $manager->getClassMetadata(Contact::class)->getFieldNames();

        // remove contact
        
        if($removeContact) 
        {
            $manager->remove($removeContact);
            $manager->flush();
            $this->addFlash('success', "تم حذف الرسالة");

            return $this->redirectToRoute('admin_message');
        }

        $contacts = $repoContact->findAll();

        $contacts = $paginator->paginate(
            $contacts,
            $request->query->getInt('page', 1),
            10
        );


        
        return $this->render('admin/admin_message.html.twig', [
            'contacts' => $contacts,
            'colonnes' => $colonnes
            
        ]);
    }

    #[Route('/admin_membres', name: 'admin_membres')]
    #[Route('/admin_membres/user/{id}/edit', name: 'admin_membres_edit')]
    public function membres(UserRepository $repo, PaginatorInterface $paginator, EntityManagerInterface $manager, Request $request, User $user = null, $id = null): Response
    {
        $nomDeChamp = $this->getDoctrine()->getManager();

        $colonnes = $nomDeChamp->getClassMetadata(User::class)->getFieldNames();

        $users = $repo->findAll();

        $users = $paginator->paginate(
            $users,
            $request->query->getInt('page', 1),
            10
        );
       

// user midification
        if ($request->request->count()) {
            $user->setRoles([$request->request->get('roles')]);

            $manager->persist($user);
            $manager->flush();

            $this->addFlash('success1', 'User has been changed !');

            return $this->redirectToRoute('admin_membres');
        }

        return $this->render('admin/admin_membres.html.twig', [
            'users' => $users,
            'colonnes' => $colonnes,
            'user' => $user,
            'request' => $request   
        ]);
    }
    //  user remove

    #[Route('/admin_membres/user/{id}/remove', name: 'admin_membres_remove')]
    public function membresRemove(EntityManagerInterface $manager, $id = null, User $userRemove = null)
    {  
        if ($userRemove) {
            $id = $userRemove->getId();

            $manager->remove($userRemove);
            $manager->flush();

            $this->addFlash('success1', "User has been removed !");
            return $this->redirectToRoute('admin_membres');
        }
    }

    #[Route('/admin_promise', name: 'admin_promise')]
    public function adminPromise(EntityManagerInterface $manager,PaginatorInterface $paginator,  Request $request, PromiseRepository $repo ): Response
    {
        
        $nomDeChamp = $this->getDoctrine()->getManager();

        $colonnes = $nomDeChamp->getClassMetadata(Promise::class)->getFieldNames();

        $promises = $repo->findAll();

        
        $promises = $paginator->paginate(
            $promises,
            $request->query->getInt('page', 1),
            10
        );
        
 
        return $this->render('admin/admin_promise.html.twig', [
            'colonnes' => $colonnes,
            'promiseBDD' => $promises,

        ]);
    }


}
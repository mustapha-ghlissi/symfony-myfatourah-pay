<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Contact;
use App\Entity\Promise;
use App\Form\ContactType;
use App\Form\PromiseFormType;
use App\Form\RegistrationType;
use App\Service\Payment;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Mime\Email;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class SiteController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(): Response
    {
        return $this->render('site/index.html.twig', [
            'controller_name' => 'SiteController',
        ]);
    }

    #[Route('/about_us', name: 'about_us')]
    public function about(): Response
    {
        return $this->render('site/about_us.html.twig');
    }

    #[Route('/pdg', name: 'pdg')]
    public function administration(): Response
    {
        return $this->render('site/about_administration.html.twig');
    }

    
    #[Route('/direction', name: 'direction')]
    public function directionAssos(): Response
    {
        return $this->render('site/about_direction.html.twig');
    }

    
    #[Route('/membres', name: 'membres')]
    public function membres(): Response
    {
        return $this->render('site/about_membres.html.twig');
    }



    #[Route('/domain', name: 'domain')]
    public function domain(): Response
    {
        return $this->render('site/our_domain.html.twig');
    }

    #[Route('/services', name: 'services')]
    public function services(): Response
    {
        return $this->render('site/our_services.html.twig');

    }

    #[Route('/contact', name: 'contact')]
    public function contact(Request $request, EntityManagerInterface $manager, MailerInterface $mailer): Response
    {
        $contact = new Contact();

        $formContact = $this->createForm(ContactType::class, $contact);//créer le formulaire d'ajout
        $formContact->handleRequest($request); //recupère les données saisis et transmet
    
        if($formContact->isSubmitted() && $formContact->isValid())//si c'est valider alors...
        {
            $manager->persist($contact);//prepare
            $manager->flush();//execute
 //    prepar email
                 // $email = (new Email())
                 //     ->from($contact->getEmail())
                 //     ->to('alla.dumenil@mail.ru')
                 // // //->cc('cc@example.com')
                 // // //->bcc('bcc@example.com')
                 // // //->replyTo('fabien@example.com')
                 //     // ->priority(Email::PRIORITY_HIGH)
                 //     ->subject('Message from AUVT')
                 //     ->text($contact->getMessage());

                 // $mailer->send($email);

        $this->addFlash('success', "Your message has been successfully sent");

        return $this->redirectToRoute('contact');
        }
        return $this->render('site/contact_us.html.twig', [
                'formContact' => $formContact->createView(),
            ]);
    }

    #[Route('/to_register', name: 'to_register')]
    public function toRegister(Request $request,  EntityManagerInterface $manager, UserPasswordHasherInterface $encoder): Response
    { 
    
        $user = new User();
        $form = $this->createForm(RegistrationType::class, $user); //création formulaire

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            if(!$user->getId())
            {
                $user->setDate(new \Datetime());
            }

            $hash = $encoder->hashPassword($user, $user->getPassword());
            $user->setPassword($hash);

            $user->setRoles(["ROLE_USER"]);

            $manager->persist($user);
            $manager->flush();
            return $this->redirectToRoute('home');
        }


        return $this->render('site/to_register.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/profile', name: 'profile')]
    public function profile(): Response
    {
        return $this->render('site/profile.html.twig');
    }

    #[Route('/promise_done/user/{id}', name: 'promise_done')]
    public function promiseDone(User $user, Request $request, EntityManagerInterface $manager, Payment $payment): Response
    {
        $promise = new Promise();

        $formPromise = $this->createForm(PromiseFormType::class, $promise);

        $formPromise->handleRequest($request);

         // on renseigne setteur de date car nous n'avons pas champ dans formulairs

        if($formPromise->isSubmitted() && $formPromise->isValid())
        {
            $promise->setUser($user);
            $manager->persist($promise);
            $manager->flush();

            // Si le bouton Pay est cliqué
            if ($formPromise->get('pay')->isClicked()) {
                // Redirection vers la page de paiement
                return $payment->pay($user, $promise);
            }

            // Ajouter un message de succès
            $this->addFlash('success', 'تم تسجيل الوعد | Promise saved successfully');

            // Redirection vers la page Adhesion
            return $this->redirectToRoute('adhesion', [
                'id' => $promise->getId()
            ]);
        }

        return $this->render('site/promise_done.html.twig', [
            'formPromise' => $formPromise->createView()
        ]);
    }

    #[Route('/adhesion/{id}', name: 'adhesion')]
    public function adhesion(Promise $promise, Request $request, Payment $payment): Response
    {
        // S'il s'agit d'une redirection après paiement
        if ($request->headers->get('referer')) {
            if  ($request->query->has('paymentStatus') && $status = $request->query->get('paymentStatus')) {
                if ($status === 'success' && !$promise->getStatus()) {
                    $promise->setStatus(true);
                    $this->getDoctrine()->getManager()->flush();
                    $this->addFlash('success', 'تم الدفع بنجاح | Promise payment scheduled successfully');
                }
                elseif ($status === 'failure') {
                    $this->addFlash('danger', 'الدفع لم يتم, الرجاء التثبت من صحة المعطيات | Promise payment failed');
                }
            }
        }

        $formPayment = $this->createFormBuilder()
            ->add('pay', SubmitType::class, [
                'label' => 'أتم الدفع الآن | Finish payment',
                'attr' => [
                    'class' => 'btn btn-danger'
                ]
            ])
            ->getForm();

        $formPayment->handleRequest($request);

        if ($formPayment->isSubmitted() && $formPayment->isValid()) {
            // Redirection vers la page de paiement
            return $payment->pay($promise->getUser(), $promise);
        }

        return $this->render('site/adhesion.html.twig', [
            'promise' => $promise,
            'formPayment' => $formPayment->createView()
        ]);
    }
}

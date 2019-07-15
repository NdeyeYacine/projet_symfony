<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use App\Entity\Employe;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Service;
use App\Repository\EmployeRepository;
use App\Repository\ServiceRepository;
class EmploiController extends AbstractController
{
    /**
     * @Route("/emploi", name="emploi")
     */
    public function index( EmployeRepository $repos)
    {
        $employe = $repos->findAll();

        return $this->render('emploi/index.html.twig', [
            'controller_name' => 'EmploiController',
            'employe'=> $employe 
        ]);
    }

    /**
     * @Route("/", name="home")
     */
    public function home(){
        return $this->render('emploi/home.html.twig');
    }
        /**
     * @Route("/emploi/ajouter", name="ajouter")
     * @Route("/emploi/{id}edit", name="edit")
     */
    public function form(Employe $employe = null, Request $request, ObjectManager $manager ){
        if (!$employe) {
            $employe = new Employe();
        }
        

        $form = $this->createFormBuilder($employe)
                     ->add('matricule')
                     ->add('nomcomplet')
                     ->add('datenaissance', DateType::class, [
                         'widget'=> 'single_text',
                         'format'=>'yyyy-MM-dd'
                     ])
                     ->add('salaire', MoneyType::class)
                     ->add('service', EntityType::class,[
                         'class'=>Service::class, 'choice_label'=>'libelle'
                     ])
                     ->getForm();

        $form->handleRequest($request); 
        
        if($form->isSubmitted() && $form->isValid()){

            $manager->persist($employe);
            $manager->flush();
            return $this->redirectToRoute('emploi', ['id'=> $employe->getId()]);
        }
       
        return $this->render('/emploi/ajouter.html.twig', [
            'formEmploye'=> $form->createView(),
            'editMode'=> $employe->getId() !== null
        ]);
    }
    
    /**
     * @Route("/emploi/{id}/delete", name="supprimer")
     */
    public function supprimer(Employe $employe, ObjectManager $manager){
        $manager->remove($employe);
        $manager->flush();
        $this->addFlash('danger', 'suppression reussie');

        return $this->redirectToRoute('emploi', ['id'=> $employe->getId()]);
    }
}

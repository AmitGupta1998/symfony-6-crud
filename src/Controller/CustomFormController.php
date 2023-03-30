<?php 

namespace App\Controller;

use App\Entity\Project;
use App\Form\ProjectType;
use App\Service\FileUploader;
use App\Form\CustomFormType;
use App\Repository\ProjectRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;



class CustomFormController extends AbstractController
{
    #[Route('/', name: 'app_project_index', methods: ['GET'])]
    public function index(ProjectRepository $projectRepository): Response
    {
        return $this->render('project/index.html.twig', [
            'projects' => $projectRepository->findAll(),
        ]);
    }

    
    // @Route("/custom-form", name="custom_form");
    #[Route('/custom-form', name: 'custom_form', methods: ['GET', 'POST'])]

    public function customForm(Request $request, ProjectRepository $projectRepository, ParameterBagInterface $params): Response
    {
        $form = $this->createForm(CustomFormType::class);
        $project = new Project();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            
            $file = $form->get('file')->getData();

            // Do something with the file
            if ($file) {
                // dd($file);
                $fileUploader = new FileUploader($params->get('app.upload_directory'));
                $fileName = $fileUploader->upload($file);
                $project->setBrochureFilename($fileName);
                $projectRepository->save($project, true);
                // dd($project);
            }
            
            // return $this->redirectToRoute('success_page');
        }

        return $this->render('custom_form.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}

<?php

namespace App\Controller;

use App\Entity\Student;
use App\Repository\StudentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ApiStudentController extends AbstractController
{
    #[Route('/students', name:'create_students', methods:'POST')]
    public function create(Request $request, EntityManagerInterface $entityManager,SerializerInterface $serializer, TranslatorInterface $translator): Response
    {
        
        $data= $request->getcontent();
        $student= $serializer->deserialize($data,Student::class,'json');
        $entityManager->persist($student);
        $entityManager->flush();
        $translated = $translator->trans('your student profile has been successfully created');
        return new JsonResponse($translated,Response::HTTP_CREATED,["location"=>"/student".$student->getId()],true);
    }

    #[Route('/students', name: 'list_students', methods:'GET')]
    public function list(StudentRepository $repo,SerializerInterface $serializer): Response
    {
        $students = $repo->findAll();
        $resultat = $serializer->serialize($students,'json');
        return new JsonResponse($resultat,200,[],true);
    }

    #[Route('/students/{id}', name: 'show_students', methods:'GET')]
    public function show(Student $student,SerializerInterface $serializer): Response
    {
        $resultat = $serializer->serialize($student,'json');
        return new JsonResponse($resultat,200,[],true);
    }

    #[Route('/students/{id}', name: 'update_students', methods:'PUT')]
    public function update( Student $student, Request $request, EntityManagerInterface $entityManager,SerializerInterface $serializer,TranslatorInterface $translator): Response
    {
        $data= $request->getcontent();
        $serializer->deserialize($data,Student::class,'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $student]);
        $entityManager->persist($student);
        $entityManager->flush();
        $translated = $translator->trans('your student profile has been successfully update');
        return new JsonResponse($translated,Response::HTTP_OK,[],true);
    }

    #[Route('/students/{id}', name: 'delete_students', methods:'DELETE')]
    public function delete(Student $student, EntityManagerInterface $entityManager,TranslatorInterface $translator): Response
    {
        $entityManager->remove($student);
        $entityManager->flush();
        $translated = $translator->trans('your student profile has been successfully delete');
        return new JsonResponse($translated,200,[]);
    }
}  
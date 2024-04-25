<?php

namespace App\Controller;

use App\Entity\Embroidery;
use App\Repository\EmbroideryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;

date_default_timezone_set('Europe/Paris');


class EmbroideryController extends AbstractController
{
    #[Route('api/embroideries', name: 'app_api_embroideries', methods:['GET'])]
    public function index(EmbroideryRepository $embroideryRepository): JsonResponse
    {
        $data = $embroideryRepository->findAll();
        
        return $this->json($data,200,[], ["groups"=>['embroideryLinked']] );

    }

    #[Route('api/embroideries/{id}', name: 'app_api_embroideries_show', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function show(Embroidery $embroidery): JsonResponse
    {
        if (!$embroidery) {
            return $this->json([
                "fail" =>["this embroidery doesn't exist"]],Response::HTTP_NOT_FOUND);  
            }

        // we catch the embroidery frome the database
        return $this->json($embroidery, Response::HTTP_OK,[], ["groups"=>['embroideryLinked']]);
    }
    
    #[Route('api/embroideries/{name}', name: 'app_api_embroideries_name', methods: ['GET'], requirements: ['name' => '[a-zA-Z]+'])]
    public function findByName(EmbroideryRepository $embroideryRepository,$name): JsonResponse
    {
        $data = $embroideryRepository->findByName($name);

        return $this->json(
            $data, 
            Response::HTTP_OK, 
            [], 
            ["groups" => ['embroidery','productLinked']]
        );
    }

    #[Route('api/embroideries/{design}', name: 'app_api_embroideries_design', methods: ['GET'], requirements: ['design' => '[a-zA-Z]+'])]
    public function findByDesign(EmbroideryRepository $embroideryRepository,$design): JsonResponse
    {
        $data = $embroideryRepository->findByDesign($design);

        return $this->json(
            $data, 
            Response::HTTP_OK, 
            [], 
            ["groups" => ['embroidery','productLinked']]
        );
    }


    #[Route('api/embroideries/create', name: 'app_api_embroideries_create', methods: ['POST'])]
    public function create(Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManager, ValidatorInterface $validator): JsonResponse
    {

        // we catch the JSON in the request
        $data = $request->getContent();

        // we manage the case where the JSON is in the wrong format
        try {
            // we transform the brut JSON in embroidery entity
           
            $embroidery = $serializer->deserialize($data, Embroidery::class, 'json');
        } catch (NotEncodableValueException $exception) {
            return $this->json([
                "error" =>
                ["message" => $exception->getMessage()]
            ], Response::HTTP_BAD_REQUEST);
        }

        // we check if there is error
        $errors = $validator->validate($embroidery);
        if (count($errors) > 0) {

            $dataErrors = [];
            
            foreach ($errors as $error) {
                
            $dataErrors[$error->getPropertyPath()] = $error->getMessage();
            }

            return $this->json(["error" => ["message" => $dataErrors]], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $entityManager->persist($embroidery);

        $entityManager->flush();
        
        return $this->json($embroidery, Response::HTTP_CREATED, ["Location" => $this->generateUrl("app_api_embroideries")], ["groups"=>['embroideryLinked']]);
    }

    #[Route('/api/embroideries/delete/{id}', name: 'app_api_embroideries_delete', methods: ['DELETE'], requirements: ['id' => '\d+'])]
    public function delete(EmbroideryRepository $embroideryrepos,$id,EntityManagerInterface $em): JsonResponse
    {
            $embroidery=$embroideryrepos->find($id);
            if (empty($embroidery)){
                return $this->json([
                    "error"=>"There aren't any embroidery  with this id !"
                ]
                , Response::HTTP_BAD_REQUEST);
            }

            try {
                $em->remove($embroidery);
                $em->flush();
                return $this->json([
                    "success" =>"Item deleted with success !"
                ],
                Response::HTTP_OK);
            }
            catch(\Exception $e){
                return $this->json([
                    "error"=>"We encounter some errors with your deletion",
                    "reason"=>$e->getMessage()
                ]
                , Response::HTTP_INTERNAL_SERVER_ERROR);
            }
    }

    #[Route('api/embroideries/edit/{id}', name: 'app_api_embroideries_edit', methods: ['GET'])]
    public function edit(Embroidery $embroidery): JsonResponse
    {
        if (!$embroidery) {
            return $this->json([
                "fail" => ["this embroidery doesn't exist"]
            ], Response::HTTP_NOT_FOUND);
        }
        return $this->json(
            $embroidery, 
            Response::HTTP_OK, 
            [], 
            ["groups" => ['embroideryLinked']]
        );
    }


    #[Route('api/embroideries/update/{id}', name:"app_api_embroideries_update", methods:['PUT'])]
    public function update(Request $request, SerializerInterface $serializer, Embroidery $currentEmbroidery, EntityManagerInterface $em,ValidatorInterface $validator,EmbroideryRepository $embroideryRepository): JsonResponse 
    {   
        if (!$currentEmbroidery) {
            return $this->json([
                "fail" =>["this embroidery doesn't exist"]],Response::HTTP_NOT_FOUND);  
            }


        try {
        //we catch the JSON in the request
            $updatedEmbroidery = $serializer->deserialize($request->getContent(),
                Embroidery::class, 
                'json', 
            
                [AbstractNormalizer::OBJECT_TO_POPULATE => $currentEmbroidery]);
        }catch (NotEncodableValueException $exception) {
            
            return $this->json([
                "error" =>
                ["message" => $exception->getMessage()]
            ], Response::HTTP_BAD_REQUEST);
        }
        // we check if there is error
        $errors = $validator->validate($updatedEmbroidery);
        if (count($errors) > 0) {
            $dataErrors = [];            
            foreach ($errors as $error) {    
            $dataErrors[$error->getPropertyPath()] = $error->getMessage();
            }

        }
        
        $em->persist($updatedEmbroidery);
        $em->flush();
        return $this->json($updatedEmbroidery, Response::HTTP_CREATED,["Location" => $this->generateUrl("app_api_embroideries")],["groups"=>['embroideryLinked']]);
   }

}


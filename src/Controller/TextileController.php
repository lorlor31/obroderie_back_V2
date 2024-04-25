<?php

namespace App\Controller;

use App\Entity\Textile;
use App\Repository\TextileRepository;
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


class TextileController extends AbstractController
{
    #[Route('api/textiles', name: 'app_api_textiles', methods:['GET'])]
    public function index(TextileRepository $textileRepository): JsonResponse
    {
        $data = $textileRepository->findAll();
        return $this->json($data,200,[], ["groups"=>['textileLinked'] ]);
    }

    #[Route('/api/textiles/{id}', name: 'app_api_textiles_show', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function show(textile $textile): JsonResponse
    {
        return $this->json($textile, Response::HTTP_OK, [], ["groups"=>['textileLinked']] );
    }
    
    #[Route('/api/textiles/delete/{id}', name: 'app_api_textiles_delete', methods: ['DELETE'], requirements: ['id' => '\d+'])]
    public function delete(TextileRepository $textilerepos,$id,EntityManagerInterface $em): JsonResponse
    {
            $textile=$textilerepos->find($id);
            if (empty($textile)){
                return $this->json([
                    "error"=>"There aren't any textile with this id !"
                ]
                , Response::HTTP_BAD_REQUEST);
            }

            try {
                $em->remove($textile);
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

    #[Route('api/textiles/create', name: 'app_api_textiles_create', methods:['POST'])]
    public function create(Request $request, SerializerInterface $serializer, EntityManagerInterface $em , ValidatorInterface $validator): JsonResponse

    {
        $data = $request->getContent();
        $textile = $serializer->deserialize($data, Textile::class, 'json');
        //  check if the data are in the right format
        try {
            $textile = $serializer->deserialize($data, Textile::class, 'json');
        } catch (NotEncodableValueException $exception) {
            return $this->json([
                "error" =>
                ["message" => $exception->getMessage()]
            ], Response::HTTP_BAD_REQUEST);
        }
        //  check if the data respect the validation constraints
        $errors = $validator->validate($textile);
        
        if (count($errors) > 0) {
            $dataErrors = [];
            foreach ($errors as $error) {
                $dataErrors[$error->getPropertyPath()] = $error->getMessage();
            }
            return $this->json(["error" => ["message" => $dataErrors]], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        $em->persist($textile);
        $em->flush();
        return $this->json(
        $textile, 
        Response::HTTP_CREATED, 
        ["Location" => $this->generateUrl("app_api_textiles")],
        ["groups"=>['textileLinked']] 
        ); 
    }

    #[Route('api/textiles/edit/{id}', name: 'app_api_textiles_edit', methods: ['GET'])]
    public function edit(Textile $textile): JsonResponse
    {
        if (!$textile) {
            return $this->json([
                "fail" => ["this textile doesn't exist"]
            ], Response::HTTP_NOT_FOUND);
        }
        return $this->json(
            $textile, 
            Response::HTTP_OK, 
            [], 
            ["groups"=>['textileLinked']] 
        );
    }

    #[Route('api/textiles/update/{id}', name:"app_api_textiles_update", methods:['PUT'])]
    public function update(Request $request, SerializerInterface $serializer, Textile $currentTextile, EntityManagerInterface $em, ValidatorInterface $validator): JsonResponse 
    {   
        if (!$currentTextile) {
            return $this->json([
                "fail" =>["this textile doesn't exist"]],Response::HTTP_NOT_FOUND);  
            }


        try {
        //we catch the JSON in the request
            $updatedTextile = $serializer->deserialize($request->getContent(),
                Textile::class, 
                'json', 
            
                [AbstractNormalizer::OBJECT_TO_POPULATE => $currentTextile]);
        }catch (NotEncodableValueException $exception) {
            
            return $this->json([
                "error" =>
                ["message" => $exception->getMessage()]
            ], Response::HTTP_BAD_REQUEST);
        }
        // we check if there is error
        $errors = $validator->validate($updatedTextile);
        if (count($errors) > 0) {

            $dataErrors = [];
            
            foreach ($errors as $error) {
                
            $dataErrors[$error->getPropertyPath()] = $error->getMessage();
            }

        }

        $em->persist($updatedTextile);
        $em->flush();
        return $this->json($updatedTextile, 
        Response::HTTP_CREATED,
        ["Location" => $this->generateUrl("app_api_textiles")],
        ["groups"=>['textileLinked']] );
   }

}
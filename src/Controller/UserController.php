<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\Mapping\Entity;
use App\Repository\UserRepository;
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
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

date_default_timezone_set('Europe/Paris');

class UserController extends AbstractController
{
    #[Route('api/users', name: 'app_api_users', methods:['GET'])]
    public function index(UserRepository $userRepository): JsonResponse
    {
        $data = $userRepository->findAll();
        return $this->json($data,200,[], ["groups"=>['userLinked']] );
    }

    #[Route('/api/users/{id}', name: 'app_api_users_show', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function show(User $user): JsonResponse
    {
        if (!$user) {
            return $this->json([
                "fail" =>["this user doesn't exist"]],Response::HTTP_NOT_FOUND);  
            }
        // return a JSON with the User Object properties and its relations
        return $this->json($user, Response::HTTP_OK,[], ["groups"=>['userLinked']] );
    }

    #[Route('/api/users/create', name: 'app_api_users_create', methods: ['POST'])]
    public function create(Request $request, SerializerInterface $serializer,UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager, ValidatorInterface $validator): JsonResponse
    {
        // catch the JSON in the request
        $data = $request->getContent();
        //manage the case where the JSON is in the wrong format
        try {
            // convert the  JSON in User object
            $user = $serializer->deserialize($data, User::class, 'json');
        } catch (NotEncodableValueException $exception) {
            return $this->json([
                "error" =>
                ["message" => $exception->getMessage()]
            ], Response::HTTP_BAD_REQUEST);
        }
        //Receive the password from the object
        $receivedPassword = $user->getPassword() ;
        //Hash the password and set it to $user
        $user->setPassword($passwordHasher->hashPassword($user, $receivedPassword)) ;

        // errors checking
        $errors = $validator->validate($user);
        if (count($errors) > 0) {

            $dataErrors = [];
            foreach ($errors as $error) {           
            $dataErrors[$error->getPropertyPath()] = $error->getMessage();
            }

            return $this->json(["error" => ["message" => $dataErrors]], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $entityManager->persist($user);
        $entityManager->flush();
        return $this->json($user,
        Response::HTTP_CREATED, 
        ["Location" => $this->generateUrl("app_api_users")],
        ["groups"=>['userLinked']] 
    );
    }

    #[Route('/api/users/delete/{id}', name: 'app_api_users_delete', methods: ['DELETE'], requirements: ['id' => '\d+'])]
    public function delete(UserRepository $userrepos,$id,EntityManagerInterface $em): JsonResponse
    {
            $user=$userrepos->find($id);
            if (empty($user)){
                return $this->json([
                    "error"=>"There aren't any user with this id !"
                ]
                , Response::HTTP_BAD_REQUEST);
            }

            try {
                $em->remove($user);
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

    #[Route('api/users/edit/{id}', name: 'app_api_users_edit', methods: ['GET'])]
    public function edit(User $user): JsonResponse
    {
        if (!$user) {
            return $this->json([
                "fail" => ["this user doesn't exist"]
            ], Response::HTTP_NOT_FOUND);
        }
        return $this->json(
            $user, 
            Response::HTTP_OK, 
            [], 
            ["groups"=>['userLinked']]
        );
    }

    #[Route('/api/users/update/{id}', name:"app_api_users_update", methods:['PUT'])]

    public function update(Request $request, SerializerInterface $serializer, User $currentUser, EntityManagerInterface $em,ValidatorInterface $validator): JsonResponse 
    {   
        if (!$currentUser) {
            return $this->json([
                "fail" =>["this user doesn't exist"]],Response::HTTP_NOT_FOUND);  
            }

        try {
        //convert the JSON in the request into an User object
            $updatedUser = $serializer->deserialize($request->getContent(),
                User::class, 
                'json', 
                [AbstractNormalizer::OBJECT_TO_POPULATE => $currentUser]);
        }
        catch (NotEncodableValueException $exception) {
            return $this->json([
                "error" =>
                ["message" => $exception->getMessage()]
            ], Response::HTTP_BAD_REQUEST);
        }
        // errors checking
        $errors = $validator->validate($updatedUser);
        if (count($errors) > 0) {
            $dataErrors = [];
            foreach ($errors as $error) {  
            $dataErrors[$error->getPropertyPath()] = $error->getMessage();
            }
        }
        $em->persist($updatedUser);
        $em->flush();
        return $this->json($updatedUser, 
        Response::HTTP_CREATED,
        ["Location" => $this->generateUrl("app_api_users")],
        ["groups"=>['userLinked']] 
        );
   }
}


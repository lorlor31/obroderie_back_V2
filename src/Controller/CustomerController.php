<?php

namespace App\Controller;

use App\Entity\Customer;
use App\Repository\ContractRepository;
use App\Repository\CustomerRepository;
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


class CustomerController extends AbstractController
{
    #[Route('api/customers', name: 'app_api_customers', methods:['GET'])]
    public function index(CustomerRepository $customerRepository): JsonResponse
    {
        $data = $customerRepository->findAll();
        return $this->json($data,200,[], ["groups"=>['customerLinked']] );
    }

    #[Route('api/customers/{id}', name: 'app_api_customers_show', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function show(customer $customer): JsonResponse
    {
        // $response =  $this->json($customer, Response::HTTP_OK,[], ["groups"=>['customerLinked']] );
        // //Get the content of the response 
        // $jsonToSimplified =$response->getContent();
        // // Convert the string Json to Json object
        // $jsonObj = json_decode($jsonToSimplified, true);
        // // convert the json formatted ids to simple integers 
        // $contracts=[];
        // // dd($jsonObj);
        // foreach ($jsonObj['contracts']as $contract) {
        //     $contractId=intval($contract['id']) ;
        //     unset($contract['id']);
        //     $contracts[]=$contractId;
        // }
        // $jsonObj['contracts']=$contracts;
        return $this->json(
            $customer, 
            Response::HTTP_OK,[],     
            ["groups"=>['customerLinked']] );
    }


    #[Route('/api/customers/delete/{id}', name: 'app_api_customers_delete', methods: ['DELETE'], requirements: ['id' => '\d+'])]
    public function delete(CustomerRepository $customerrepos,$id,EntityManagerInterface $em): JsonResponse
    {
            $customer=$customerrepos->find($id);
            if (empty($customer)){
                return $this->json([
                    "error"=>"There aren't any customer with this id !"
                ]
                , Response::HTTP_BAD_REQUEST);
            }

            try {
                $em->remove($customer);
                $em->flush();
                return $this->json([
                    "success" =>"Item deleted with success !"
                ],
                Response::HTTP_OK);
            }
            catch(\Exception $e){
                return $this->json([
                    "error"=>"We encounter some errors with your deletion",
                    "reason"=>"customer enrolled",
                    "DB"=>$e->getMessage()
                ]
                , Response::HTTP_INTERNAL_SERVER_ERROR);
            }
    }

    #[Route('api/customers/create', name: 'app_api_customers_create', methods:['POST'])]
    public function create(Request $request, SerializerInterface $serializer, EntityManagerInterface $em , ValidatorInterface $validator): JsonResponse
     
    {
        $data = $request->getContent();
        $customer = $serializer->deserialize($data, Customer::class, 'json');
        //  check if the data are in the right format
        try {
            $customer = $serializer->deserialize($data, Customer::class, 'json');
        } catch (NotEncodableValueException $exception) {
            return $this->json([
                "error" =>
                ["message" => $exception->getMessage()]
            ], Response::HTTP_BAD_REQUEST);
        }
        //  check if the data respect the validation constraints
        $errors = $validator->validate($customer);
        if (count($errors) > 0) {
            $dataErrors = [];
            foreach ($errors as $error) {
                $dataErrors[$error->getPropertyPath()] = $error->getMessage();
            }
            return $this->json(["error" => ["message" => $dataErrors]], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        $em->persist($customer);
        $em->flush();
        return $this->json(
        $customer, 
        Response::HTTP_CREATED, 
        ["Location" => $this->generateUrl("app_api_customers")]
        ); 
    }
#[Route('api/customers/edit/{id}', name: 'app_api_customers_edit', methods: ['GET'])]
public function edit(Customer $customer): JsonResponse
{
    if (!$customer) {
        return $this->json([
            "fail" => ["this customer doesn't exist"]
        ], Response::HTTP_NOT_FOUND);
    }
    return $this->json(
        $customer, 
        Response::HTTP_OK, 
        [], 
        ["groups"=>['customerLinked']] 
    );
}

#[Route('api/customers/update/{id}', name:"app_api_customers_update", methods:['PUT'])]

    public function update(Request $request, SerializerInterface $serializer, Customer $currentCustomer, ValidatorInterface $validator, EntityManagerInterface $em): JsonResponse 

        {
            // Check if the customer exists
            if (!$currentCustomer) {
                throw $this->createNotFoundException('Le client n\'existe pas.');
            }
            // Convert the string Json to Json object
            $jsonReceived = json_decode($request->getContent(), true); 
            // convert the json formatted ids to simple integers for contracts
            // Given contracts is an array, loop to retrieve all the contracts'ids
            // $contractsId= [] ;
            // foreach ($jsonReceived['contracts'] as $contract) {
            //     $contractsId[]=$contract;
            // }
            // $jsonReceived['contracts']=$contractsId ;
            // // Convert the json object to string back
            $jsonToConvert=json_encode($jsonReceived) ;            
            try {
                //we catch the JSON in the request
                    $updatedCustomer = $serializer->deserialize($jsonToConvert,
                    Customer::class, 
                        'json', 
                        [AbstractNormalizer::OBJECT_TO_POPULATE => $currentCustomer]);
                }
            catch (NotEncodableValueException $exception) {
                
                return $this->json([
                    "error" =>
                    ["message" => $exception->getMessage()]
                ], Response::HTTP_BAD_REQUEST);
            }
            // we check if there is error
            $errors = $validator->validate($updatedCustomer);
            if (count($errors) > 0) {

                $dataErrors = [];
                
                foreach ($errors as $error) {
                    
                $dataErrors[$error->getPropertyPath()] = $error->getMessage();
                }
            }
            // Persist and flush the changes in database
            $em->persist($updatedCustomer);
            $em->flush();
            // Return the CREATED JSON response
            return $this->json($updatedCustomer, 
            Response::HTTP_CREATED,
            ["Location" => $this->generateUrl("app_api_customers")],
            ["groups"=>['customerLinked']] 
        );
        
   }

   #[Route('api/customers/customer/{name}', name: 'app_api_customers_customer', methods: ['GET'], requirements: ['name' => '[a-zA-Z]+'])]
    public function findCustomerByName(CustomerRepository $customerRepository,$name): JsonResponse
    {
        $data = $customerRepository->findCustomerByName($name);

        return $this->json(
            $data, 
            200, 
            [], 
            ["groups" => ['customerLinked']]
        );
    }
    #[Route('api/customers/customer/email/{email}', name: 'app_api_customers_email', methods: ['GET'], requirements: ['name' => '[a-zA-Z]+'])]
    public function findCustomerEmail(CustomerRepository $customerRepository,$email): JsonResponse
    {
        $data = $customerRepository->findCustomerEmail($email);
        if (empty($data)) {
        return $this->json(['Error' => 'No customer found for the provided email address'], JsonResponse::HTTP_NOT_FOUND);}
        $itemCount = count($data);
        $data[]= $itemCount ;

        return $this->json(
            $data,
            200, 
            [], 
            ["groups" => ['customerLinked']],
        );
    }

#[Route('api/customers/customer/phone_number/{phoneNumber}', name: 'app_api_customers_phone_number', methods: ['GET'], requirements: ['phone_number' => '[0-9]+'])]
    public function findByPhoneNumber(CustomerRepository $customerRepository,$phoneNumber): JsonResponse
    {
        $data = $customerRepository->findByPhoneNumber($phoneNumber);
        if (empty($data)) {
        return $this->json(['Error'=>'No customer found for the provided phone number'], JsonResponse::HTTP_NOT_FOUND);}
        $itemCount = count($data);
        $data[]= $itemCount ;

        return $this->json(
            $data, 
            200, 
            [], 
            ["groups" => ['customerLinked']]
        );
    }
}
<?php

namespace App\Controller;
use App\Entity\Contract as Contract;
use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Repository\ContractRepository;
use App\Repository\EmbroideryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Encoder\JsonDecode;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Mapping\Loader\AttributeLoader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;

date_default_timezone_set('Europe/Paris');

class ProductController extends AbstractController
{
    #[Route('api/products', name: 'app_api_products', methods:['GET'])]
    public function index(ProductRepository $productRepository): JsonResponse
    {
        $data = $productRepository->findAll();
        $productsJson = $this->json(
            $data,
            200,
            [], 
            ["groups"=>['product','contractLinkedId','textileLinkedId','embroideryLinkedId']] 
        );
        $productsJsonToSimplified =$productsJson->getContent();
        // Convert the string Json to Json object
        $jsonObj = json_decode($productsJsonToSimplified, true);
        $products=[];
        foreach($jsonObj as $product ) {
            // convert the json formatted ids to simple integers for user, customer and products json
            $textileId= $product['textile']['id'];
            $embroideryId= $product['embroidery']['id'];
            $contractId= $product['contract']['id'];
            $product['textile']=$textileId;
            $product['embroidery']=$embroideryId;
            $product['contract']=$contractId;
            $products[]=$product ;
        }
        return $this->json(
            $products, 
            Response::HTTP_OK,     
        );
    }

    #[Route('api/products/{id}', name: 'app_api_products_show', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function show(ProductRepository $productRepos, $id): JsonResponse
    {
        $product = $productRepos->find($id);
        if (!$product) {
            return $this->json([
                "fail" =>["this product doesn't exist"]],Response::HTTP_NOT_FOUND);  
            }
            $data = $this->json(
                $product, 
                Response::HTTP_OK, 
                [], 
                ["groups"=>['product','contractLinkedId','textileLinkedId','embroideryLinkedId']]             );
            
            $jsonToSimplified =$data->getContent();
            // Convert the string Json to Json object
            $jsonObj = json_decode($jsonToSimplified, true);
            // convert the json formatted ids to simple integers for user, customer and products json
            $textileId= $jsonObj['textile']['id'];
            $embroideryId= $jsonObj['embroidery']['id'];
            $contractId= $jsonObj['contract']['id'];
            $jsonObj['textile']=$textileId;
            $jsonObj['embroidery']=$embroideryId;
            $jsonObj['contract']=$contractId;
            // $jsonString=json_encode($jsonObj) ;
            // dd($jsonObj);
            return $this->json(
                $jsonObj, 
                Response::HTTP_OK,     
            );
    }


    #[Route('api/products/create', name: 'app_api_products_create', methods: ['POST'])]
    public function create(Request $request, ContractRepository $contractRepos, SerializerInterface $serializer, EntityManagerInterface $entityManager, ValidatorInterface $validator): JsonResponse
    {
        // we catch the JSON in the request
        $json = $request->getContent();
        $jsonObj= json_decode($json,true);
        // check if there is an existing contract at the specified id
        $contractId = $jsonObj['contract'];
        if(! $contractRepos->find($contractId)){
        // send a json error message if the contract does not exist    
            return $this->json(
                ["error" =>
                    ["message" => "The contract does not exist yet !"]
                ], 
                Response::HTTP_BAD_REQUEST
            );
        }
        // check if the JSON is in the wrong format
        try {
            // we transform the JSON in product object        
            $product = $serializer->deserialize($json, Product::class, 'json');
        } catch (NotEncodableValueException $exception) {
            return $this->json([
                "error" =>
                ["message" => $exception->getMessage()]
            ], Response::HTTP_BAD_REQUEST);
        }

        // check errors
        $errors = $validator->validate($product);
        if (count($errors) > 0) {

            $dataErrors = [];
            
            foreach ($errors as $error) {
            $dataErrors[$error->getPropertyPath()] = $error->getMessage();
            }

            return $this->json(["error" => ["message" => $dataErrors]], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        // Persist and flush created product in DB
        $entityManager->persist($product);
        $entityManager->flush();
        
        return $this->json(
            $product, 
            Response::HTTP_CREATED,
            ["Location" => $this->generateUrl("app_api_products")],
            ["groups"=>['product','textileLinked','embroideryLinked','contractLinkedId']]);
    }

    #[Route('/api/products/delete/{id}', name: 'app_api_products_delete', methods: ['DELETE'], requirements: ['id' => '\d+'])]
    public function delete(ProductRepository $productrepos,$id,EntityManagerInterface $em): JsonResponse
    {
            $product=$productrepos->find($id);
            if (empty($product)){
                return $this->json([
                    "error"=>"There aren't any product with this id !"
                ]
                , Response::HTTP_BAD_REQUEST);
            }

            try {
                $em->remove($product);
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
    #[Route('api/products/edit/{id}', name: 'app_api_products_edit', methods: ['GET'])]
    public function edit(Product $product): JsonResponse
    {
        if (!$product) {
            return $this->json([
                "fail" => ["this product doesn't exist"]
            ], Response::HTTP_NOT_FOUND);
        }
        // $receivedJson= $request->getContent();
        // $completeJson= $serializer->deserialize($receivedJson, Product::class, 'json');
        // dd($completeJson) ;
        $data = $this->json(
            $product, 
            Response::HTTP_OK, 
            [], 
            ["groups"=>['product','textileLinkedId','embroideryLinkedId','contractLinkedId']] 
        );
        
        $jsonToSimplified =$data->getContent();
        // Convert the string Json to Json object
        $jsonObj = json_decode($jsonToSimplified, true);
        // convert the json formatted ids to simple integers for user, customer and products json
        $textileId= $jsonObj['textile']['id'];
        $embroideryId= $jsonObj['embroidery']['id'];
        $contractId= $jsonObj['contract']['id'];
        $jsonObj['textile']=$textileId;
        $jsonObj['embroidery']=$embroideryId;
        $jsonObj['contract']=$contractId;
        // $jsonString=json_encode($jsonObj) ;
        // dd($jsonObj);
        return $this->json(
            $jsonObj, 
            Response::HTTP_OK,     
        );
    }
    
    #[Route('api/products/update/{id}', name:"app_api_products_update", methods:['PUT'])]

    public function update(Request $request, SerializerInterface $serializer, Product $currentProduct, EntityManagerInterface $em ,ValidatorInterface $validator): JsonResponse 
    {   
        if (!$currentProduct) {
            return $this->json([
                "fail" =>["this product doesn't exist"]],Response::HTTP_NOT_FOUND);  
            }

        try {
            //Convert the ids in integers
            // 1/ Convert the string Json to Json object
            $jsonReceived = json_decode($request->getContent(), true); 
            // 2/ Convert the json formatted ids to simple integers for user, customer and products json
            $textileId= $jsonReceived['textile'] ;
            $embroideryId= $jsonReceived['embroidery'] ;
            $jsonReceived['textile']=$textileId ;
            $jsonReceived['embroidery']=$embroideryId ;
            $jsonToConvert=json_encode($jsonReceived) ;
            // dd($jsonToConvert);
            //convert the JSON to a Product object
            $updatedProduct = $serializer->deserialize($jsonToConvert,
                Product::class, 
                'json', 
                [AbstractNormalizer::OBJECT_TO_POPULATE => $currentProduct]);
        }
        catch (NotEncodableValueException $exception) {
            
            return $this->json([
                "error" =>
                ["message" => $exception->getMessage()]
            ], Response::HTTP_BAD_REQUEST);
        }
        // we check if there is error
        $errors = $validator->validate($updatedProduct);
        if (count($errors) > 0) {

            $dataErrors = [];
            
            foreach ($errors as $error) { 
            $dataErrors[$error->getPropertyPath()] = $error->getMessage();
            }

        }

        $em->persist($updatedProduct);
        $em->flush();
        return $this->json($updatedProduct, 
        Response::HTTP_CREATED,
        ["Location" => $this->generateUrl("app_api_products")],
        ["groups"=>['product','contractLinkedId','textileLinkedId','embroideryLinkedId']] 
        );
    }

    //Route to get informations about embroidery stats
    #[Route('api/products/embroidery', name: 'app_api_products_embroidery', methods:['GET'])]
    // show the ids and quantitys of the products using the embroidery specified in the query string like ?id=1
    // to use the queryString , the route must be complete with ?id=1 for example
    public function productsUsingEmbroidery(ProductRepository $productRepository, EmbroideryRepository $embroideryRepos, Request $request): JsonResponse
    {
        //Retrieve id and stat parameters from querystring
        $idparameter = $request->query->get('id');
        $statParameter = $request->query->get('stat');
        // Case of the sum for one embroidery
        if ($statParameter == 'sum') {
            $data = $productRepository->sumOfquantitiesOfProductsUsingEmbroidery($idparameter);
        }
        // Case of the max of products made with one embroidery :
        else if ($idparameter==='all' & $statParameter==='max') {
            //Calculate the total of products for each embroidery
            $embroideries= $embroideryRepos->findAll();
            foreach ($embroideries as $embroidery) {
                $sumOfProducts = $productRepository->sumOfquantitiesOfProductsUsingEmbroidery($embroidery->getId());
                var_dump($sumOfProducts);

            }
            
        }
        else{
            // Return the id of  all the products using the embroidery 
            $data = $productRepository->productsUsingEmbroidery($idparameter);
        }
            return $this->json(
                $data,
                200,
                [], 
                ["groups"=>['product','textileLinked','embroideryLinked']] 
            );
        
        
    }





}

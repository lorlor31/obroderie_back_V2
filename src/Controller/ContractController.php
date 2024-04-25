<?php

namespace App\Controller;

use App\Entity\Contract;
use Knp\Snappy\Pdf as knpPdf ;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Part\File;
use App\Repository\ContractRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mime\Part\DataPart;
use Knp\Bundle\SnappyBundle\KnpSnappyBundle;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Knp\Bundle\SnappyBundle\DependencyInjection\KnpSnappyExtension;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;

date_default_timezone_set('Europe/Paris');

class ContractController extends AbstractController
{
    #[Route('api/contracts', name: 'app_api_contracts', methods: ['GET'])]
    public function index(ContractRepository $contractRepository): JsonResponse
    {
        $contracts = $contractRepository->findAll();
        // Convert the objects' array into a JSON Response
        return $this->json(
            $contracts, 
            200, 
            [], 
            ["groups" => ['contract','userLinked','productLinked','contractTextile','contractEmbroidery','customerLinked',]]
        );
        /* //Get the content of the response 
        $jsonToSimplified =$response->getContent();
        // Convert the string Json to Json object
        $jsonObj = json_decode($jsonToSimplified, true);
        $contracts=[];
        //loop through all the contracts :
        foreach ($jsonObj as $contract) {
            // convert the json formatted ids to simple integers for user, customer and products json
            $customerId= $contract['customer']['id'];
            $userId= $contract['user']['id'];
            $contract['customer']=$customerId;
            $contract['user']=$userId;
            $products=[];
            // dd($contract);
            foreach ($contract['products']as $product) {
                $productId=intval($product['id']) ;
                unset($product['id']);
                $products[]=$productId;
            }
            $contract['products']=$products;
            $contracts[]=$contract;
        }
    // dd($contracts);
        return $this->json(
            $contracts, 
            Response::HTTP_OK,     
        ); */
    }


    #[Route('api/contracts/{id}', name: 'app_api_contracts_show', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function show(Contract $contract): JsonResponse
    {
        if (!$contract) {
            return $this->json([
                "fail" => ["this contract doesn't exist"]
            ], Response::HTTP_NOT_FOUND);
        }

        $response = $this->json(
            $contract, 
            Response::HTTP_OK, 
            [], 
            ["groups" => ['contract','customerLinked']]
        );   
        
        //Get the content of the response 
        $jsonToSimplified =$response->getContent();
        // Convert the string Json to Json object
        $jsonObj = json_decode($jsonToSimplified, true);
        // convert the json formatted ids to simple integers for user, customer and products json
        $customerId= $jsonObj['customer']['id'];
        $userId= $jsonObj['user']['id'];
        $jsonObj['customer']=$customerId;
        $jsonObj['user']=$userId;
        // dd($jsonObj['products']);
        $products=[];
        // dd($jsonObj);
        foreach ($jsonObj['products']as $product) {
            $productId=intval($product['id']) ;
            unset($product['id']);
            $products[]=$productId;
        }
        $jsonObj['products']=$products;
        return $this->json(
            $jsonObj, 
            Response::HTTP_OK,     
        );
    }

    #[Route('api/contracts/create', name: 'app_api_contracts_create', methods: ['POST'])]
    public function create(Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManager, ValidatorInterface $validator): JsonResponse
    {
        // we catch the JSON in the request
        $data = $request->getContent();
        // we manage the case where the JSON is in the wrong format
        try {
            // we convert the JSON in contract object
            $contract = $serializer->deserialize($data, Contract::class, 'json');
        } catch (NotEncodableValueException $exception) {
            return $this->json([
                "error" =>
                ["message" => $exception->getMessage()]
            ], Response::HTTP_BAD_REQUEST);
        }

        // Error checking
        $errors = $validator->validate($contract);
        if (count($errors) > 0) {
            $dataErrors = [];
            foreach ($errors as $error) {
                $dataErrors[$error->getPropertyPath()] = $error->getMessage();
            }
            return $this->json(
                ["error" => ["message" => $dataErrors]],
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        $entityManager->persist($contract);
        $entityManager->flush();

        return $this->json(
            $contract, 
            Response::HTTP_OK, 
            [], 
            ["groups" => ['contract','customerLinked','userLinked','productLinked']]
        );
    }

    #[Route('/api/contracts/delete/{id}', name: 'app_api_contracts_delete', methods: ['DELETE'], requirements: ['id' => '\d+'])]
    public function delete(ContractRepository $contractrepos,$id,EntityManagerInterface $em): JsonResponse
    {
            $contract=$contractrepos->find($id);
            if (empty($contract)){
                return $this->json([
                    "error"=>"There aren't any contract with this id !"
                ]
                , Response::HTTP_BAD_REQUEST);
            }

            try {
                $em->remove($contract);
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

    #[Route('api/contracts/edit/{id}', name: 'app_api_contracts_edit', methods: ['GET'])]
    public function edit(Contract $contract): JsonResponse
    {
        if (!$contract) {
            return $this->json([
                "fail" => ["this contract doesn't exist"]
            ], Response::HTTP_NOT_FOUND);
        }
        return $this->json(
            $contract, 
            Response::HTTP_OK, 
            [], 
            ["groups" => ['contract','customerLinked','productLinked','userLinked']]
        );
    }

    #[Route('api/contracts/update/{id}', name: "app_api_contracts_update", methods: ['PUT'])]

    public function update(Request $request, SerializerInterface $serializer, Contract $currentContract, EntityManagerInterface $em, ValidatorInterface $validator): JsonResponse
    {
        //check if there is a contract with $id as id
        if (!$currentContract) {
            return $this->json([
                "fail" => ["this contract doesn't exist"]
            ], Response::HTTP_NOT_FOUND);
        }
        //todo to be improved
        //this feature was comment to allow update without products property 
        //check it there are at least one product in the contract
        /* if (json_decode($request->getContent(), true)['products']) {
            $products = json_decode($request->getContent(), true)['products'];
        }
        $type = json_decode($request->getContent(), true)['type']; 
        if (count($products)==0) {
            if ($type=='invoice' || $type=='order' ) {
                return $this->json([
                    "Warning" => ["You can not pass the contract into an invoice or an order as long as you don't have products"]
                ], Response::HTTP_NOT_FOUND);
            }
        } */
        // dd($products);
        // Convert the string Json to Json object
        $jsonReceived = json_decode($request->getContent(), true); 
        // convert the json formatted ids to simple integers for user, customer and products json
        $userId= $jsonReceived['user'] ;
        $customerId= $jsonReceived['customer'] ;
        $jsonReceived['user']=$userId ;
        $jsonReceived['customer']=$customerId ;
        // Given products is an array, loop to retrieve all the products'ids
/*          $productsId= [] ;
       foreach ($jsonReceived['products'] as $product) {
            $productsId[]=$product;
        }
        $jsonReceived['products']=$productsId ; */
        // Convert the json object to string back
        $jsonToConvert=json_encode($jsonReceived) ;
    
        try {
            //Convert the json to an object to update
            $updatedContract = $serializer->deserialize(
                $jsonToConvert,
                Contract::class,
                'json',
                [AbstractNormalizer::OBJECT_TO_POPULATE => $currentContract]
            );
        } 
        
        catch (NotEncodableValueException $exception) {
            return $this->json([
                "error" =>
                ["message" => $exception->getMessage()]
            ], Response::HTTP_BAD_REQUEST);
        }
        // Errors checking
        $errors = $validator->validate($updatedContract);
        if (count($errors) > 0) {
            $dataErrors = [];
            foreach ($errors as $error) {
                $dataErrors[$error->getPropertyPath()] = $error->getMessage();
            }
        }
        // Persist and flush the changes in the DB
        $em->persist($updatedContract);
        $em->flush();
        return $this->json(
            $updatedContract, 
            Response::HTTP_CREATED, 
            ["Location" => $this->generateUrl("app_api_contracts")],
            // ["groups" => ['contract', 'userLinked', 'customerLinked', 'productLinked','embroideryLinked']]
            //TODO check if it is ok without other groups
            ["groups" => ['contract']]
        );
    }

    #[Route('api/contracts/type/{type}', name: 'app_api_contracts_type', methods: ['GET'], requirements: ['type' => 'order|quotation|invoice'])]
    public function findByType(ContractRepository $contractRepository,$type): JsonResponse
    {
        $data = $contractRepository->findByType($type);

        // Convert the objects' array into a JSON Response
        return $this->json(
            $data, 
            200, 
            [], 
            ["groups" => ['contract','userLinked','productLinked','contractTextile','contractEmbroidery','customerLinked',]]
        );
/* 
        //Get the content of the response 
        $jsonToSimplified =$response->getContent();
        // Convert the string Json to Json object
        $jsonObj = json_decode($jsonToSimplified, true);
        $contracts=[];
        //loop through all the contracts :
        foreach ($jsonObj as $contract) {
            // convert the json formatted ids to simple integers for user, customer and products json
            $customerId= $contract['customer']['id'];
            $userId= $contract['user']['id'];
            $contract['customer']=$customerId;
            $contract['user']=$userId;
            $products=[];
            // dd($contract);
            foreach ($contract['products']as $product) {
                $productId=intval($product['id']) ;
                unset($product['id']);
                $products[]=$productId;
            }
            $contract['products']=$products;
            $contracts[]=$contract;
        }
    // dd($contracts);
        return $this->json(
            $contracts, 
            Response::HTTP_OK,     
        ); */
    }


    #[Route('api/contracts/customer/{name}', name: 'app_api_contracts_customer', methods: ['GET'], requirements: ['name' => '[a-zA-Z]+'])]
    public function findByCustomer(ContractRepository $contractRepository,$name): JsonResponse
    {
        $data = $contractRepository->findContractsByCustomerName($name);

        return $this->json(
            $data, 
            200, 
            [], 
            ["groups" => ['contract']]
        );
    }

    //route to render a preview of the contract in the navigator
    #[Route('api/contracts/{id}/viewpdf', name: 'app_api_contracts_viewpdf', methods: ['GET'])]
    public function pdfView(Contract $contract) 
    {
        return $this->render('contract/pdf.html.cssInline.twig', [
            'contract' => $contract
        ]);
    }
    
    
    //route to response with a pdf file for the contract => direct download on user HD
    #[Route('api/contracts/{id}/renderpdf', name: 'app_api_contracts_renderpdf', methods: ['GET'])]
    public function pdfRender(Contract $contract, knpPdf $knpSnappyPdf) 
    {
        //Retrieve infos to customize pdf file name
        $date = date('d-m-Y-H-i') ;
        $contractId = $contract->getId();
        $customer = $contract->getCustomer()->getName();
        $customerName =$contract->getCustomer()->getName() ;
        $customerASCII = iconv('UTF-8', 'ASCII//TRANSLIT', $customerName);
        $firstname = explode(" ", $customerName)[0] ;
        $firstnameASCII = iconv('UTF-8', 'ASCII//TRANSLIT', $firstname);
        $familyName = strtoupper(iconv('UTF-8', 'ASCII//TRANSLIT', explode(" ", $customerName)[1]) );
        $familyNameASCII = iconv('UTF-8', 'ASCII//TRANSLIT', $familyName);
        // Render the twig view
        $html = $this->renderView(
        'contract/pdf.html.cssInline.twig',
        array('contract'  => $contract));

        //convert the html to pdf response
        return new PdfResponse(
            $knpSnappyPdf->getOutputFromHtml($html),
            "contract{$contractId}-{$firstnameASCII}-{$familyNameASCII}-{$date}.pdf"
        );
    }

    #[Route('api/contracts/{id}/sendpdf', name: 'app_api_contracts_sendpdf', methods: ['GET'])]
    // the user should specify the customer mail in the query string like ?mail=user@provider.com
    public function pdfSend(Contract $contract, knpPdf $knpSnappyPdf,MailerInterface $mailerinterf, Request $request) 
    {
        // Get mail from queryString
        $customerMail = $request->query->get('mail');
        //Retrieve infos to customize pdf file name
        $date = date('d-m-Y-H-i') ;
        $contractId = $contract->getId();
        $customerName =$contract->getCustomer()->getName() ;
        $customerASCII = iconv('UTF-8', 'ASCII//TRANSLIT', $customerName);
        $firstname = explode(" ", $customerName)[0] ;
        $firstnameASCII = iconv('UTF-8', 'ASCII//TRANSLIT', $firstname);
        $familyName = strtoupper(iconv('UTF-8', 'ASCII//TRANSLIT', explode(" ", $customerName)[1]) );
        $familyNameASCII = iconv('UTF-8', 'ASCII//TRANSLIT', $familyName);

        //TODO to use with real customer mails :) 
        // $customerMail= $contract->getCustomer()->getEmail();

        // Render the twig view to html
        $html = $this->renderView(
        'contract/pdf.html.cssInline.twig',
        array('contract'  => $contract));
        //Create a pdf file from the html on the server HD
        $knpSnappyPdf->generateFromHtml(
            $html,
            "/home/student/contract{$contractId}-{$firstnameASCII}-{$familyNameASCII}-{$date}.pdf"
        );
        //Create an email to send
        $email = (new Email())
            ->from('lorlor@free.fr')
            ->to($customerMail)
            // ->to('fayis_78920@hotmail.fr')
            ->subject("Voici le pdf du contrat {$contract->getId()} ")
            // ->html('<h1>Un film a été crée</h1> <p>info du film TODO</p>');
            ->html("<h2>Bonjour {$firstname} {$familyName}  , </h2> 
            <p> Voici votre facture concernant le contrat n°{$contractId}. </p>
            <p> Bonne réception et bonne journée,  </p>
            <p> L'équipe de O'Broderie </p>
            ")
            ->addPart(new DataPart(new File("/home/student/contract{$contractId}-{$firstnameASCII}-{$familyNameASCII}-{$date}.pdf")));
        // Send the email
        $mailerinterf->send($email);
        // Return a json success
        return $this->json(["Success" => "Sent email with pdf !"],200);

    }

    //route to generate a pdf file for the contract on the server HD
    // the user should specify the absolute path expected for the pdf file in the query string like ?path=home/student
    // #[Route('api/contracts/{id}/renderpdfToHd', name: 'app_api_contracts_renderpdfToHD', methods: ['GET'])]
    // public function pdfRenderHD(Contract $contract, knpPdf $knpSnappyPdf,Request $request ) 
    // {
    //     $locationParameter = $request->query->get('path');
    //     $date = date('d-m-Y-H-i-s') ;
    //     // $path= 
    //     $knpSnappyPdf->generateFromHtml(
    //     $this->renderView(
    //     'contract/pdf.html.cssInline.twig',
    //     array('contract'  => $contract)),
    //     // "/home/student/contract{$date}.pdf"
    //     "/{$locationParameter}/contract{$date}.pdf"
    //     );
    //     return  $this->render('contract/pdf.html.cssInline.twig', [
    //         'contract' => $contract
    //     ]);
    // }

}

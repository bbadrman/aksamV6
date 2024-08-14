<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PaymentController extends AbstractController
{
    private const PASSWORD = 'testpassword_MMh9rwFCacbLYWo5zj3TNJ8CrspsPDaeX9YKivMobGTfn'; // Remplacez par votre mot de passe

    /**
     * @Route("/sogecommerce-ipn", name="sogecommerce_ipn", methods={"POST"})
     */
    public function handleIpn(Request $request): Response
    {
        $postData = $request->request->all();

        // STEP 1 : check the signature with the password
        if (!$this->checkHash($postData, self::PASSWORD)) {
            return new Response('Invalid signature.', Response::HTTP_FORBIDDEN);
        }

        $answer = [
            'kr-hash' => $postData['kr-hash'],
            'kr-hash-algorithm' => $postData['kr-hash-algorithm'],
            'kr-answer-type' => $postData['kr-answer-type'],
            'kr-answer' => json_decode($postData['kr-answer'], true),
        ];

        // STEP 2 : get some parameters from the answer
        $orderStatus = $answer['kr-answer']['orderStatus'];
        $orderId = $answer['kr-answer']['orderDetails']['orderId'];
        $transactionUuid = $answer['kr-answer']['transactions'][0]['uuid'];

        // I update my database if needed
        // Add here your custom code 

        // Message returned to the IPN caller
        return new Response('OK! OrderStatus is ' . $orderStatus);
    }

    private function checkHash(array $data, string $key): bool
    {
        $supportedSignAlgos = ['sha256_hmac'];
        if (!in_array($data['kr-hash-algorithm'], $supportedSignAlgos)) {
            return false;
        }

        $krAnswer = str_replace('\/', '/', $data['kr-answer']);
        $hash = hash_hmac('sha256', $krAnswer, $key);

        return $hash === $data['kr-hash'];
    }
}

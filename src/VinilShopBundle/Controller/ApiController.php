<?php

namespace VinilShopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use VinilShopBundle\Entity\Feedback;


class ApiController extends Controller
{
    /**
     * @Route("/feedback/create", name="feedback_create")
     *
     */

    public function createFeedbackAction(Request $request)
    {

        $email= $request->get('email');
        $sender_name = $request->get('name');
        $title = $request->get('title');
        $message = $request->get('message');
        $captcha = (int)$request->get('captcha');

////        dump($this->get('session')->get('feedback_captcha'));
////        $qwe = $this->get('session')->get('feedback_captcha');
////
////        die;
////        $q='not ok';
////        if ($captcha == $this->get('session')->get('feedback_captcha')){
////            $q='ok';
////        }
//
//            return  new JsonResponse([
//                'answer' => $this->get('session')->get('feedback_captcha'),
////                'answer' => 123,
//                'itemBlock' => $captcha
//            ],403);
//
//
//        die;

        if ($captcha != $this->get('session')->get('feedback_captcha')){
            return  new JsonResponse([
                'answer' => 'Неверное контрольное число',
                'itemBlock' => 'captcha'
            ],403);
        }

        if (!preg_match('/^[-._a-z0-9]+@(?:[a-z0-9][-a-z0-9]+\.)+[a-z]{2,6}$/',$email)){

            return  new JsonResponse([
                'answer' => 'Некорректный email',
                'itemBlock' => 'sender-email'
            ],403);
        }
        if(!preg_match('/^[А-ЯA-Z]{1}[a-zа-я]{1,50}$/u',$sender_name)){

            return  new JsonResponse([
                'answer' => 'Некорректное имя',
                'itemBlock' => 'sender-name'
            ],403);
        }
        if(strlen($title) < 4){

            return  new JsonResponse([
                'answer' => 'Короткое название темы',
                'itemBlock' => 'sender-message-title'
            ],403);
        }

        if(strlen($title) > 250){

            return  new JsonResponse([
                'answer' => 'Длинное название темы',
                'itemBlock' => 'sender-message-title'
            ],403);
        }

        if(strlen($message) < 50){

            return  new JsonResponse([
                'answer' => 'Маленькое сообщение',
                'itemBlock' => 'sender-message'
            ],403);
        }

        if(strlen($message) > 10000){

            return  new JsonResponse([
                'answer' => 'Длинное сообщение',
                'itemBlock' => 'sender-message'
            ],403);
        }

        $feedback = new Feedback();
        $feedback
            ->setEmail($email)
            ->setName($sender_name)
            ->setTitle($title)
            ->setMessage($message);

        $em = $this->getDoctrine()->getManager();
        $em->persist($feedback);
        $em->flush();

        return  new JsonResponse([
            'answer' => 'Сообщение отправлено',
        ],200);


    }


}

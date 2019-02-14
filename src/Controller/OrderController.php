<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\Orders;
use App\Entity\Products;
use App\Entity\UserLogin;

class OrderController extends AbstractController
{
    /**
     * @Route("/order", name="order")
     */
    public function index()
    {
        return new JsonResponse("yes");
    }

    /**
     * @Route("/order/create", name="order_create")
     */
    public function create(Request $request)
    {
    	if ($request->getMethod() !== "POST") {
            return new JsonResponse(
                "Mauvaise méthode, POST attendu");
        }

		$repository = $this->getDoctrine()
            ->getRepository(Orders::class);
        $prod_repo = $this->getDoctrine()
        	->getRepository(Products::class);
        $user_repo = $this->getDoctrine()
        	->getRepository(UserLogin::class);
        $entityManager = $this->getDoctrine()->getManager();

        $order = new Orders();
        $order->setOrderNumber(uniqid());
        $order->setOrderStatus(0);
        $order->setOrderDate(new \DateTime());

        $products = json_decode($request->get("products"));
        if (is_array($products)) {
        	foreach ($products as $value) {
        		$product = $prod_repo->findOneBy([
        			"nom_produit" => $value]);
        		if (is_object($product)) {
        			$order->addProduct($product);
        		}
        	}
        }

        $user = $user_repo->find($request->get("user_id"));
        $order->setUser($user);

        $entityManager->persist($order);
        $entityManager->flush();
        $id = $user->getId();

        $data = [
        	"id" => $id
        ];

        return new JsonResponse($data);
    }

    /**
     * @Route("/order/read/{id}", name="order_read")
     */
    public function read(Request $request, $id)
    {
        $data = array();
    	if ($request->getMethod() !== "GET") {
            return new JsonResponse(
                "Mauvaise méthode, GET attendu");
        }

        $repository = $this->getDoctrine()
            ->getRepository(Orders::class);

        $orders = $repository->findBy(["user" => $id]);
        foreach ($orders as $order) {
	        $products = $order->getProduct();
	        $prod_result = array();
	        $i = 0;
	        foreach ($products as $key => $value) {
	            $prod_result[$i]["id"] = $value->getId();
	            $prod_result[$i]["nom_produit"] =
	            	$value->getNomProduit();
	            $prod_result[$i]["stock"] = $value->getStock();
	            $prod_result[$i]["prix"] = $value->getPrix();
	            $prod_result[$i]["date"] = $value->getDate();
	            $prod_result[$i]["description"] =
	            	$value->getDescription();
	            $i++;
	        }

	        $data[] = [
	            "order_id" => $order->getId(),
	            "order_number" => $order->getOrderNumber(),
	            "order_status" => $order->getOrderStatus(),
	            "order_date" => $order->getOrderDate(),
	            "ordered_products" => $prod_result
	        ];
	    }

        $response = new JsonResponse($data);
        return $response;
    }

    /**
     * @Route("/order/update/{id}")
     */
    public function update(Request $request, $id)
    {
        if ($request->getMethod() !== "PUT") {
            return new JsonResponse(
                "Mauvaise méthode, PUT attendu");
        }

        $repository = $this->getDoctrine()
            ->getRepository(Orders::class);
        $entityManager = $this->getDoctrine()->getManager();

        $order = $repository->find($id);

        $req = json_decode($request->getContent());
        $params = get_object_vars($req);

        if (isset($params["order_status"])) {
        	$order->setStatus($params["order_status"]);
        }
        if (isset($params["products"])) {
            $products = $params["products"];
            if (is_array($products)) {
                foreach ($products as $value) {
                    $product = $prod_repo->findOneBy(
                        ["nom_produit" => $value[1]]);
                    if ($value[0] === false &&
                        is_object($product)) {
                        $product->removeCategory($product);
                    } elseif ($value[0] === true &&
                        is_object($product)) {
                        $product->addCategory($product);
                    }
                }
            }
        }

        $entityManager->flush();

        return new JsonResponse(["id" => $id]);
    }

    /**
     * @Route("/order/delete/{id}", name="order_delete")
     */
    public function delete(Request $request, $id) {
        if ($request->getMethod() !== "DELETE") {
            return new JsonResponse(
                "Mauvaise méthode, DELETE attendu");
        }

        $repository = $this->getDoctrine()
            ->getRepository(Orders::class);
        $entityManager = $this->getDoctrine()->getManager();

        $order = $repository->find($id);

        if ($order === NULL) {
            return new JsonResponse(
            	"Cette commande n'existe pas.");
        }

        $entityManager->remove($order);
        $entityManager->flush();

        return new JsonResponse("La commande " . $id .
            " a bien été supprimée");
    }
}

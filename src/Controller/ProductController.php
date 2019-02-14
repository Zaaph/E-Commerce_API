<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\Products;
use App\Entity\Category;

class ProductController extends AbstractController
{
    /**
     * @Route("/product", name="product")
     */
    public function index()
    {
        $repository = $this->getDoctrine()
        ->getRepository(Products::class);

        $products = $repository->findAll();
        $data = array();
        foreach ($products as $product) {
            $data["nom_produit"][] = $product->getNomProduit();
        }

        $response = new JsonResponse($data);
        return $response;
    }

    /**
     * @Route("/product/create", name="product_create")
	 */
    public function create(Request $request) 
    {
    	if ($request->getMethod() !== "POST") {
            return new JsonResponse(
                "Mauvaise méthode, POST attendu");
        }

        $repository = $this->getDoctrine()
            ->getRepository(Products::class);
        $cat_repo = $this->getDoctrine()
            ->getRepository(Category::class);
        $entityManager = $this->getDoctrine()->getManager();

        $product = $repository->findOneBy(
            ["nom_produit" => $request->get("nom_produit")]);
        if (is_object($product)) {
            $data = "Ce produit est déjà enregistré.";
            $response = new JsonResponse($data);
            return $response;
        }

        $product = new Products();
        $product->setNomProduit(htmlspecialchars(
            trim($request->get("nom_produit"))));
        $product->setStock($request->get("stock"));
        $product->setDate(new \DateTime());
        $product->setPrix($request->get("prix"));
        $product->setDescription($request->get("description"));
        if (null !== $request->get("image")) {
            $product->setImage($request->get("image"));
        }

        $cat_str = "Aucune catégorie ajoutée";
        $categories = json_decode($request->get("categories"));
        if (is_array($categories)) {
            $cat_str = "";
            foreach ($categories as $value) {
                $cat = $cat_repo->findOneBy(
                    ["category_name" => $value]);
                if (is_object($cat)) {
                    $product->addCategory($cat);
                    $cat_str .= $value . " ";
                }
            }
        }

        $entityManager->persist($product);
        $entityManager->flush();
        $id = $product->getId();
        $data = [
            "id" => $id,
            "categories_added" => $cat_str
        ];
        $response = new JsonResponse($data);
        return $response;
    }

    /**
     * @Route("/product/create_cat", name="product_create_cat")
     */
    public function create_cat(Request $request) {
        if ($request->getMethod() !== "POST") {
            return new JsonResponse(
                "Mauvaise méthode, POST attendu");
        }

        $repository = $this->getDoctrine()
            ->getRepository(Category::class);
        $entityManager = $this->getDoctrine()->getManager();

        $category = $repository->findOneBy(["category_name" => 
            $request->get("category_name")]);
        if (is_object($category)) {
            return new JsonResponse(
                "Cette catégorie existe déjà.");
        }

        $category = new Category();
        $category->setCategoryName(
            $request->get("category_name"));
        $entityManager->persist($category);
        $entityManager->flush();
        $id = $category->getId();

        return new JsonResponse(["id" => $id]);
    }

    /**
     * @Route("/product/read/{id}", name="product_read")
     */
    public function read(Request $request, $id) {
        if ($request->getMethod() !== "GET") {
            return new JsonResponse(
                "Mauvaise méthode, GET attendu");
        }

        $repository = $this->getDoctrine()
            ->getRepository(Products::class);

        $product = $repository->find($id);
        if (!is_object($product)) {
            return new JsonResponse("Ce produit n'existe pas");
        }

        $categories = $product->getCategories();
        $cat_result = array();
        foreach ($categories as $key => $value) {
            $cat_result[] = $value->getCategoryName();
        }

        $data = [
            "nom_produit" => $product->getNomProduit(),
            "stock" => $product->getStock(),
            "date" => $product->getDate(),
            "prix" => $product->getPrix(),
            "image" => $product->getImage(),
            "description" => $product->getDescription(),
            "categories" => $cat_result
        ];

        $response = new JsonResponse($data);
        return $response;
    }

    /**
     * @Route("/product/read_cat/{id}", name="product_read_cat")
     */
    public function read_cat(Request $request, $id) {
        if ($request->getMethod() !== "GET") {
            return new JsonResponse(
                "Mauvaise méthode, GET attendu");
        }

        $repository = $this->getDoctrine()
            ->getRepository(Category::class);

        $category = $repository->find($id);
        if (!is_object($category)) {
            return new JsonResponse(
                "Cette catégorie n'existe pas");
        }

        $products = $category->getProduct();
        $prod_result = array();
        $i = 0;
        foreach ($products as $key => $value) {
            $prod_result[$i]["id"] = $value->getId();
            $prod_result[$i]["nom_produit"]
                = $value->getNomProduit();
            $prod_result[$i]["stock"] = $value->getStock();
            $prod_result[$i]["date"] = $value->getDate();
            $prod_result[$i]["prix"] = $value->getPrix();
            $prod_result[$i]["image"] = $value->getImage();
            $prod_result[$i]["description"] = 
                $value->getDescription();
            $i++;
        }

        $data = [
            "category_name" => $category->getCategoryName(),
            "products" => $prod_result
        ];

        return new JsonResponse($data);
    }

    /**
     * @Route("/product/update/{id}", name="product_update")
     */

    public function update(Request $request, $id) {
        if ($request->getMethod() !== "PUT") {
            return new JsonResponse(
                "Mauvaise méthode, PUT attendu");
        }

        $repository = $this->getDoctrine()
            ->getRepository(Products::class);
        $cat_repo = $this->getDoctrine()
            ->getRepository(Category::class);
        $entityManager = $this->getDoctrine()->getManager();

        $product = $repository->find($id);

        $req = json_decode($request->getContent());
        $params = get_object_vars($req);

        if (isset($params["nom_produit"])) {
            if (!is_object($repository->findOneBy(
                ["nom_produit" => $params["nom_produit"]]))) {
                $product->setNomProduit($params["nom_produit"]);
            }
        }
        if (isset($params["stock"])) {
            $product->setStock($params["stock"]);
        }
        if (isset($params["prix"])) {
            $product->setPrix($params["prix"]);
        }
        if (isset($params["image"])) {
            $product->setImage($params["image"]);
        }
        if (isset($params["description"])) {
            $product->setDescription($params["description"]);
        }
        if (isset($params["categories"])) {
            $categories = $params["categories"];
            if (is_array($categories)) {
                foreach ($categories as $value) {
                    $category = $cat_repo->findOneBy(
                        ["category_name" => $value[1]]);
                    if ($value[0] === false &&
                        is_object($category)) {
                        $product->removeCategory($category);
                    } elseif ($value[0] === true &&
                        is_object($category)) {
                        $product->addCategory($category);
                    }
                }
            }
        }

        $entityManager->flush();

        $data = [
            "id" => $product->getId()
        ];
        return new JsonResponse($data);
    }

    /**
     * @Route("/product/delete/{id}", name="product_delete")
     */
    public function delete(Request $request, $id) {
        if ($request->getMethod() !== "DELETE") {
            return new JsonResponse(
                "Mauvaise méthode, DELETE attendu");
        }

        $repository = $this->getDoctrine()
            ->getRepository(Products::class);
        $entityManager = $this->getDoctrine()->getManager();

        $product = $repository->find($id);

        if ($product === NULL) {
            return new JsonResponse("Ce produit n'existe pas.");
        }

        $entityManager->remove($product);
        $entityManager->flush();

        return new JsonResponse("Le produit " . $id .
            " a bien été supprimé");
    }
}
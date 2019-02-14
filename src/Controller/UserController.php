<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\UserLogin;
use App\Entity\UserShippingData;

class UserController extends AbstractController
{
    /**
     * @Route("/user", name="user")
     */
    public function index()
    {
        $repository = $this->getDoctrine()
        ->getRepository(UserLogin::class);

        $users = $repository->findAll();
        $data = array();
        foreach ($users as $user) {
            $data["emails"][] = $user->getEmail();
        }

        $response = new JsonResponse($data);
        return $response;
    }

    /**
     * @Route("/user/create", name="user_create")
     */
    public function create(Request $request)
    {
        if ($request->getMethod() !== "POST") {
            return new JsonResponse(
                "Mauvaise méthode, POST attendu");
        }
        //Initialisation de l'ORM :
            //$repository pour chercher des infos dans la base
            //$entityManager pour modifier des infos dans la base
        $repository = $this->getDoctrine()
            ->getRepository(UserLogin::class);
        $entityManager = $this->getDoctrine()->getManager();

        //On regarde si l'email est déjà pris, dans ce cas
        //return une chaine
        $data = $repository->findOneBy(
            ["email" => $request->get("email")]);
        if (is_object($data)) {
            $data = "Cet email est déjà utilisé.";
            $response = new JsonResponse($data);
            return $response;
        }

        //Sinon, on crée un objet user et on set ses propriétés
        //selon la requête
        $user = new UserLogin();
        $user->setEmail(htmlspecialchars(
            trim($request->get("email"))));
        $user->setPassword(
            password_hash(
                $request->get("password"), PASSWORD_BCRYPT));
        $user->setEmailVerif(0);
        $user->setStatus(0);

        //On ajoute l'user dans la db et on return son id
        $entityManager->persist($user);
        $entityManager->flush();
        $id = $user->getId();
        $data = [
            "id" => $id
        ];
        $response = new JsonResponse($data);
        return $response;
    }

    /**
     * @Route("/user/read/{id}", name="user_read")
     */
    public function read(Request $request, $id) {
        if ($request->getMethod() !== "GET") {
            return new JsonResponse(
                "Mauvaise méthode, GET attendu");
        }

        $repository = $this->getDoctrine()
            ->getRepository(UserLogin::class);

        $user = $repository->find($id);
        if (!is_object($user)) {
            return new JsonResponse(
                "Cet utilisateur n'existe pas");
        }
        $user_data = $user->getUserShippingData();
        if ($user_data !== NULL) {
            $data = [
                "email" => $user->getEmail(),
                "nom" => $user_data->getNom(),
                "prenom" => $user_data->getPrenom(),
                "adresse" => $user_data->getAdresse(),
                "cpostal" => $user_data->getCpostal(),
                "status" => $user->getStatus()
            ];
        } else {
            $data = [
                "email" => $user->getEmail(),
                "status" => $user->getStatus(),
                "shipping" => 
                    "Cet utilisateur n'a pas d'infos de livraison"
            ];
        }
        return new JsonResponse($data);
    }

    /**
     * @Route("/user/delete/{id}", name="user_delete")
     */
    public function delete(Request $request, $id) {
        if ($request->getMethod() !== "DELETE") {
            return new JsonResponse(
                "Mauvaise méthode, DELETE attendu");
        }

        $repository = $this->getDoctrine()
            ->getRepository(UserLogin::class);
        $entityManager = $this->getDoctrine()->getManager();
        $user = $repository->find($id);

        if (!is_object($user)) {
            $data = "Cet utilisateur n'existe pas";
            $response = new JsonResponse($data);
            return $response;
        } elseif ($user->getStatus() === true) {
            $data = "Cet utilisateur a déjà été supprimé";
            $response = new JsonResponse($data);
            return $response;
        }

        $user->setStatus(1);
        $entityManager->flush();
        $data = "Votre compte a bien été supprimé!";
        $response = new JsonResponse($data);
        return $response;
    }

    /**
     * @Route("/user/ship/{id}", name="user_ship")
     */

    public function ship(Request $request, $id) {
        if ($request->getMethod() !== "POST") {
            return new JsonResponse(
                "Mauvaise méthode, POST attendu");
        }

        $repository = $this->getDoctrine()
            ->getRepository(UserLogin::class);
        $entityManager = $this->getDoctrine()->getManager();

        $user = $repository->find($id);
        if (!is_object($user)) {
            return new JsonResponse(
                "Cet utilisateur n'existe pas");
        }

        $ship = new UserShippingData();
        $ship->setNom(htmlspecialchars(
            trim($request->get("nom"))));
        $ship->setPrenom(htmlspecialchars(
            trim($request->get("prenom"))));
        $ship->setAdresse(htmlspecialchars(
            trim($request->get("adresse"))));
        $ship->setCpostal(htmlspecialchars(
            trim($request->get("cpostal"))));

        $user->setUserShippingData($ship);
        $entityManager->persist($user);
        $entityManager->flush();

        return new JsonResponse("Infos de livraisons ajoutées "
            . "pour l'utilisateur numéro " . $id);
    }

    /**
     * @Route("/user/update/{id}", name="user_update")
     */

    public function update(Request $request, $id) {
        if ($request->getMethod() !== "PUT") {
            return new JsonResponse(
                "Mauvaise méthode, PUT attendu");
        }

        $repository = $this->getDoctrine()
            ->getRepository(UserLogin::class);
        $entityManager = $this->getDoctrine()->getManager();

        $user = $repository->find($id);
        $ship = $user->getUserShippingData();

        $req = json_decode($request->getContent());
        $params = get_object_vars($req);

        if (isset($params["nom"])) {
            $ship->setNom($params["nom"]);
        }
        if (isset($params["prenom"])) {
            $ship->setPrenom($params["prenom"]);
        }
        if (isset($params["adresse"])) {
            $ship->setAdresse($params["adresse"]);
        }
        if (isset($params["cpostal"])) {
            $ship->setCpostal($params["cpostal"]);
        }
        if (isset($params["status"])) {
            $user->setStatus($params["status"]);
        }
        if (isset($params["email_verif"])) {
            $user->setEmailVerif($params["email_verif"]);
        }

        $entityManager->flush();

        $data = [
            "id" => $user->getId()
        ];

        return new JsonResponse($data);
    }

    /**
     * @Route("/user/check", name="user_check")
     */

    public function check_password(Request $request) {
        if ($request->getMethod() !== "POST") {
            return new JsonResponse(
                "Mauvaise méthode, POST attendu");
        }

        $repository = $this->getDoctrine()
            ->getRepository(UserLogin::class);
        $user = $repository->findOneBy(
            ["email" => $request->get("email")]);
        if ($user === NULL) {
            return new JsonResponse("Cet email n'existe pas");
        }

        if (password_verify($request->get("password"),
            $user->getPassword())) {
            $data = ["id" => $user->getId()];
        } else {
            $data = false;
        }

        return new JsonResponse($data);
    }
}

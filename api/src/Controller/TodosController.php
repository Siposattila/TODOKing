<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Todos;

class TodosController extends AbstractController
{
    #[Route('/todos', name: 'app_todos')]
    public function index(ManagerRegistry $doctrine): JsonResponse
    {
        $todos = $doctrine->getRepository(Todos::class)->findAll();

        return $this->json([
            $todos
        ]);
    }

    #[Route('/todos/create', name: 'app_todos')]
    public function createTodo(ManagerRegistry $doctrine, ValidatorInterface $validator, Request $request): JsonResponse
    {
        $newTodo = json_decode($request->getContent());
        if (empty($newTodo))
        {
            return $this->json([
                'message' => 'Something went wrong...',
                'error' => 'Invalid request body.',
                'errorCode' => 422
            ]);
        }

        $entityManager = $doctrine->getManager();

        $todo = new Todos();
        $todo->setTitle($newTodo->title);
        $todo->setContent($newTodo->content);
        $todo->setAttachment($newTodo->attachment);
        $todo->setUpdatedAt(date('now'));
        $todo->setCreatedAt(date('now'));

        $errors = $validator->validate($todo);
        if (count($errors) > 0)
        {
            return $this->json([
                'message' => 'Something went wrong...',
                'error' => (string) $errors,
                'errorCode' => 400
            ]);
        }

        // tell Doctrine you want to (eventually) save the Todo (no queries yet)
        $entityManager->persist($todo);

        // actually executes the queries (i.e. the INSERT query)
        $entityManager->flush();

        return $this->json([
            'message' => 'Saved new todo with id '.$todo->getId(),
            'error' => null,
            'errorCode' => null
        ]);
    }

    #[Route('/todos/{id}', name: 'product_show')]
    public function show(ManagerRegistry $doctrine, int $id): Response
    {
        $todo = $doctrine->getRepository(Todos::class)->find($id);

        if (!$todo) {
            throw $this->createNotFoundException(
                'No todo found for id '.$id
            );
        }

        return new Response($todo->getTitle());

        // or render a template
        // in the template, print things with {{ product.name }}
        // return $this->render('product/show.html.twig', ['product' => $product]);
    }
}

<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Todos;

class TodosController extends AbstractController
{
    #[Route('/todos')]
    public function index(ManagerRegistry $doctrine, SerializerInterface $serializer)
    {
        $todos = $doctrine->getRepository(Todos::class)->findAll();

        return new JsonResponse($serializer->serialize($todos, 'json'), 200, [], true);
    }

    #[Route('/todos/create')]
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
        $todo->setUpdatedAt(new \DateTimeImmutable());
        $todo->setCreatedAt(new \DateTimeImmutable());

        $errors = $validator->validate($todo);
        if (count($errors) > 0)
        {
            return $this->json([
                'message' => 'Something went wrong...',
                'error' => (string) $errors,
                'errorCode' => 400
            ]);
        }

        $entityManager->persist($todo);
        $entityManager->flush();

        return $this->json([
            'message' => 'Saved new todo with id '.$todo->getId(),
            'error' => null,
            'errorCode' => null
        ]);
    }

    #[Route('/todos/{id}')]
    public function show(ManagerRegistry $doctrine, int $id): JsonResponse
    {
        $todo = $doctrine->getRepository(Todos::class)->find($id);

        if (!$todo) {
            throw $this->createNotFoundException(
                'No todo found for id '.$id
            );
        }

        return $this->json([
            'id' => $todo->getId(),
            'title' => $todo->getTitle(),
            'content' => $todo->getContent(),
            'attachment' => $todo->getAttachment()
        ]);
    }

    #[Route('/todos/update/{id}')]
    public function update(ManagerRegistry $doctrine, ValidatorInterface $validator, Request $request, int $id): JsonResponse
    {
        $todoUpdated = json_decode($request->getContent());
        if (empty($todoUpdated))
        {
            return $this->json([
                'message' => 'Something went wrong...',
                'error' => 'Invalid request body.',
                'errorCode' => 422
            ]);
        }

        $entityManager = $doctrine->getManager();
        $needToUpdateTodo = $doctrine->getRepository(Todos::class)->find($id);

        if (!$needToUpdateTodo) {
            throw $this->createNotFoundException(
                'No todo found for id '.$id
            );
        }

        $needToUpdateTodo->setTitle($todoUpdated->title);
        $needToUpdateTodo->setContent($todoUpdated->content);
        $needToUpdateTodo->setAttachment($todoUpdated->attachment);
        $needToUpdateTodo->setUpdatedAt(new \DateTimeImmutable());

        $errors = $validator->validate($needToUpdateTodo);
        if (count($errors) > 0)
        {
            return $this->json([
                'message' => 'Something went wrong...',
                'error' => (string) $errors,
                'errorCode' => 400
            ]);
        }

        $entityManager->flush();

        return $this->json([
            'message' => 'Updated todo with id '.$needToUpdateTodo->getId(),
            'error' => null,
            'errorCode' => null
        ]);
    }

    #[Route('/todos/delete/{id}')]
    public function delete(ManagerRegistry $doctrine, int $id): JsonResponse
    {
        $entityManager = $doctrine->getManager();
        $todo = $doctrine->getRepository(Todos::class)->find($id);

        if (!$todo) {
            throw $this->createNotFoundException(
                'No todo found for id '.$id
            );
        }

        $id = $todo->getId();
        $entityManager->remove($todo);
        $entityManager->flush();

        return $this->json([
            'message' => 'Deleted todo with id '.$id,
            'error' => null,
            'errorCode' => null
        ]);
    }
}

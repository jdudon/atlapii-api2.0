<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

/**
 */
class UserController
{
   /**
    *@Route("users/", methods={"GET"}) 
    */
   public function readAll(UserRepository $repo, SerializerInterface $serializer)
   {

      $users = $repo->findAll();

      return JsonResponse::fromJsonString($serializer->serialize(
         $users,
         'json',
         [
            ObjectNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object) {
               return $object->getId();
            },
            AbstractNormalizer::IGNORED_ATTRIBUTES => ['password']
         ]
      ));
   }

   /**
    * @Route("users/{id}", methods={"GET"})
    */
   public function readOne(int $id, UserRepository $repo, SerializerInterface $serializer)
   {
      $user = $repo->find($id);

      return JsonResponse::fromJsonString($serializer->serialize(
         $user,
         'json',
         [
            ObjectNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object) {
               return $object->getId();
            },
            AbstractNormalizer::IGNORED_ATTRIBUTES => ['password']
         ]
      ));
   }

   /**
    * @Route("users", methods={"POST"})
    */
   public function create(Request $req, SerializerInterface $serializer, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder)
   {

      $user = $serializer->deserialize(
         $req->getContent(),
         User::class,
         'json'
      );
      $user->setPassword($encoder->encodePassword($user, $user->getPassword()));
      $manager->persist($user);
      $manager->flush();

      return JsonResponse::fromJsonString($serializer->serialize(
         $user,
         'json',
         [
            ObjectNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object) {
               return $object->getId();
            },
            AbstractNormalizer::IGNORED_ATTRIBUTES => ['password']
         ]
      ));
   }

   /**
    * @Route("users/{id}", methods={"PUT"})
    */
   public function update(int $id, Request $req, User $user, SerializerInterface $serializer, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder)
   {
      $update = $serializer->deserialize(
         $req->getContent(),
         User::class,
         'json'
      );

      $user->setEmail($update->getEmail());
      if ($update->getPassword()) {
         $user->setPassword($encoder->encodePassword($user, $update->getPassword()));
      }
      $user->setRoles($update->getRoles());
      $user->setUsername($update->getUsername());

      $manager->persist($user);
      $manager->flush();

      return new Response('', 204);
   }

   /**
    * @Route("users/{id}", methods={"DELETE"})
    */
   public function delete(User $user, EntityManagerInterface $manager)
   {
      $manager->remove($user);
      $manager->flush();

      return new Response('', 204);
   }
}

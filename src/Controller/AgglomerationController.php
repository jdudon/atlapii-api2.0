<?php

namespace App\Controller;

use App\Entity\Agglomeration;
use App\Repository\AgglomerationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Routing\Annotation\Route;


/**
 * @Route("api/")
 */
class AgglomerationController
{

   /**
    * @Route("agglomerations/", methods={"GET"})
    */
   public function readAll(AgglomerationRepository $repo, SerializerInterface $serializer)
   {
      $agglomerations = $repo->findAll();

      return JsonResponse::fromJsonString($serializer->serialize(
         $agglomerations,
         'json',
         [
            ObjectNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object) {
               return $object->getId();
            }
         ]
      ));
   }

   /**
    * @Route("agglomerations/{id}", methods={"GET"})
    */
   public function readOne(int $id, AgglomerationRepository $repo, SerializerInterface $serializer)
   {
      $agglomeration = $repo->find($id);

      return JsonResponse::fromJsonString($serializer->serialize(
         $agglomeration,
         'json',
         [
            ObjectNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object) {
               return $object->getId();
            }
         ]
      ));
   }

   /**
    * @Route("agglomerations", methods={"POST"})
    */
   public function create(Request $req, SerializerInterface $serializer, EntityManagerInterface $manager)
   {

      $agglomeration = $serializer->deserialize(
         $req->getContent(),
         Agglomeration::class,
         'json'
      );
      $manager->persist($agglomeration);
      $manager->flush();

      return JsonResponse::fromJsonString($serializer->serialize(
         $agglomeration,
         'json',
         [
            ObjectNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object) {
               return $object->getId();
            }
         ]
      ));
   }

   /**
    * @Route("agglomerations/{id}", methods={"PUT"})
    */
   public function update(int $id, Request $req, Agglomeration $agglomeration, SerializerInterface $serializer, EntityManagerInterface $manager)
   {
      $update = $serializer->deserialize(
         $req->getContent(),
         Agglomeration::class,
         'json'
      );

      $agglomeration->setName($update->getName());
      $agglomeration->setSize($update->getSize());
      $agglomeration->setLeader($update->getLeader());

      //Map Collection Update Management
      foreach ($agglomeration->getMap() as $depMap) {
         $agglomeration->removeMap($depMap);
      }
      foreach ($update->getMap() as $newMap) {
         $agglomeration->addMap($newMap);
      }

      //Building Collection Update Management
      foreach ($agglomeration->getBuildings() as $depBuildings) {
         $agglomeration->removeBuilding($depBuildings);
      }
      foreach ($update->getBuilding() as $newBuildings) {
         $agglomeration->addBuilding($newBuildings);
      }

      $manager->persist($agglomeration);
      $manager->flush();

      return new Response('', 204);
   }

   /**
    * @Route("agglomerations/{id}", methods={"DELETE"})
    */
   public function delete(Agglomeration $agglomeration, EntityManagerInterface $manager)
   {
      $manager->remove($agglomeration);
      $manager->flush();

      return new Response('', 204);
   }
}

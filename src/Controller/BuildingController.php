<?php

namespace App\Controller;

use App\Entity\Agglomeration;
use App\Entity\Building;
use App\Repository\AgglomerationRepository;
use App\Repository\BuildingRepository;
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
class BuildingController
{

   /**
    * @Route("buildings/", methods={"GET"})
    */
   public function readAll(BuildingRepository $repo, SerializerInterface $serializer)
   {
      $buildings = $repo->findAll();

      return JsonResponse::fromJsonString($serializer->serialize(
         $buildings,
         'json',
         [
            ObjectNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object) {
               return $object->getId();
            }
         ]
      ));
   }

   /**
    * @Route("buildings/{id}", methods={"GET"})
    */
   public function readOne(int $id, BuildingRepository $repo, SerializerInterface $serializer)
   {
      $building = $repo->find($id);

      return JsonResponse::fromJsonString($serializer->serialize(
         $building,
         'json',
         [
            ObjectNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object) {
               return $object->getId();
            }
         ]
      ));
   }

   /**
    * @Route("buildings", methods={"POST"})
    */
   public function create(Request $req, SerializerInterface $serializer, EntityManagerInterface $manager)
   {

      $building = $serializer->deserialize(
         $req->getContent(),
         Building::class,
         'json'
      );
      $manager->persist($building);
      $manager->flush();

      return JsonResponse::fromJsonString($serializer->serialize(
         $building,
         'json',
         [
            ObjectNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object) {
               return $object->getId();
            }
         ]
      ));
   }

   /**
    * @Route("buildings/{id}", methods={"PUT"})
    */
   public function update(int $id, Request $req, Building $building, SerializerInterface $serializer, EntityManagerInterface $manager)
   {
      $update = $serializer->deserialize(
         $req->getContent(),
         Building::class,
         'json'
      );

      $building->setName($update->getName());
      $building->setSize($update->getSize());
      $building->setLeader($update->getLeader());

      //Agglomeration Collection Update Management
      foreach ($building->getAgglomerations() as $depAgglomerations) {
         $building->removeAgglomeration($depAgglomerations);
      }
      foreach ($update->getAgglomeration() as $newAgglomerations) {
         $building->addAgglomeration($newAgglomerations);
      }

      $manager->persist($building);
      $manager->flush();

      return new Response('', 204);
   }

   /**
    * @Route("buildings/{id}", methods={"DELETE"})
    */
   public function delete(Building $building, EntityManagerInterface $manager)
   {
      $manager->remove($building);
      $manager->flush();

      return new Response('', 204);
   }
}

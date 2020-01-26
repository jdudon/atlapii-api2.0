<?php

namespace App\Controller;

use App\Entity\Map;
use App\Repository\MapRepository;
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
class MapController
{
   /**
    * @Route("maps/", methods={"GET"})
    */
   public function readAll(MapRepository $repo, SerializerInterface $serializer)
   {

      $maps = $repo->findAll();

      return JsonResponse::fromJsonString($serializer->serialize(
         $maps,
         'json',
         [
            ObjectNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object) {
               return $object->getId();
            }
         ]
      ));
   }

   /**
    * @Route("maps/{id}", methods={"GET"})
    */
   public function readOne(int $id, MapRepository $repo, SerializerInterface $serializer)
   {
      $map = $repo->find($id);

      return JsonResponse::fromJsonString($serializer->serialize(
         $map,
         'json',
         [
            ObjectNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object) {
               return $object->getId();
            }
         ]
      ));
   }

   /**
    * @Route("maps", methods={"POST"})
    */
   public function create(Request $req, SerializerInterface $serializer, EntityManagerInterface $manager)
   {

      $map = $serializer->deserialize(
         $req->getContent(),
         Map::class,
         'json'
      );
      $manager->persist($map);
      $manager->flush();

      return JsonResponse::fromJsonString($serializer->serialize(
         $map,
         'json',
         [
            ObjectNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object) {
               return $object->getId();
            }
         ]
      ));
   }

   /**
    * @Route("maps/{id}", methods={"PUT"})
    */
   public function update(int $id, Request $req, Map $map, SerializerInterface $serializer, EntityManagerInterface $manager)
   {
      $update = $serializer->deserialize(
         $req->getContent(),
         Map::class,
         'json'
      );

      $map->setName($update->getName());
      $map->setInterestPoints($update->getInterestPoints());
      $map->setUniverseType($update->getUniverseType());
      $map->setBiomes($update->getBiomes());

      //Agglomeration Collection Update Management
      foreach ($map->getAgglomerations() as $depAgglo) {
         $map->removeAgglomeration($depAgglo);
      }
      foreach ($update->getAgglomerations() as $newAgglo) {
         $map->addAgglomeration($newAgglo);
      }

      $manager->persist($map);
      $manager->flush();

      return new Response('', 204);
   }

   /**
    * @Route("maps/{id}", methods={"DELETE"})
    */
   public function delete(Map $map, EntityManagerInterface $manager)
   {
      $manager->remove($map);
      $manager->flush();

      return new Response('', 204);
   }
}

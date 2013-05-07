<?php

namespace AdfabReward\Mapper;

use Doctrine\ORM\EntityManager;
use ZfcBase\Mapper\AbstractDbMapper;
use Zend\Stdlib\Hydrator\HydratorInterface;
use AdfabReward\Options\ModuleOptions;

class Achievement extends AbstractDbMapper implements ActionInterface
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;

    /**
     * @var \Doctrine\ORM\EntityRepository
     */
    protected $er;

    /**
     * @var \AdfabReward\Options\ModuleOptions
     */
    protected $options;

    public function __construct(EntityManager $em, ModuleOptions $options)
    {
        $this->em      = $em;
        $this->options = $options;
    }

    public function findById($id)
    {
        $er = $this->em->getEntityRepository();

        return $er->find($id);
    }

    public function findBy($array, $sort=array())
    {
        $er = $this->getEntityRepository();

        return $er->findBy($array, $sort);
    }

    public function findOneBy($array, $sort=array())
    {
        $er = $this->getEntityRepository();

        return $er->findOneBy($array, $sort);
    }

    public function insert($entity, $tableName = null, HydratorInterface $hydrator = null)
    {
        return $this->persist($entity);
    }

    public function update($entity, $where = null, $tableName = null, HydratorInterface $hydrator = null)
    {
        return $this->persist($entity);
    }

    protected function persist($entity)
    {
        $this->em->persist($entity);
        $this->em->flush();

        return $entity;
    }

    public function findAll()
    {
        $er = $this->getEntityRepository($this->getRepository());

        return $er->findAll();
    }

    public function remove($entity)
    {
        $this->em->remove($entity);
        $this->em->flush();
    }

    public function getEntityRepository()
    {
        if (null === $this->er) {
            $this->er = $this->em->getRepository('AdfabReward\Entity\Achievement');
        }

        return $this->er;
    }
}

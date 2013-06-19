<?php

namespace AdfabCore\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;

use AdfabReward\Entity\LeaderboardType;

/**
 *
 * @author GrG
 * Use the command : php doctrine-module.php data-fixture:import --append
 * to install these data into database
 */
class LoadLeaderboardTypeData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * Load address types
     *
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $lbtAll = new LeaderboardType();
        $lbtAll->setName('all');
        $manager->persist($lbtAll);
        $manager->flush();

        $lbtGame = new LeaderboardType();
        $lbtGame->setName('game');
        $manager->persist($lbtGame);
        $manager->flush();

        $lbtSponsorship = new LeaderboardType();
        $lbtSponsorship->setName('sponsorship');
        $manager->persist($lbtSponsorship);
        $manager->flush();

        $lbtShare = new LeaderboardType();
        $lbtShare->setName('share');
        $manager->persist($lbtShare);
        $manager->flush();

        $this->addReference('lbtAll', $lbtAll);
        $this->addReference('lbtGame', $lbtGame);
        $this->addReference('lbtSponsorship', $lbtSponsorship);
        $this->addReference('lbtShare', $lbtShare);
    }

    public function getOrder()
    {
        return 550;
    }
}

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
        $leaderboardType = new LeaderboardType();
        $leaderboardType->setName('all');
        $manager->persist($leaderboardType);
        $manager->flush();

        $leaderboardType = new LeaderboardType();
        $leaderboardType->setName('game');
        $manager->persist($leaderboardType);
        $manager->flush();

        $leaderboardType = new LeaderboardType();
        $leaderboardType->setName('sponsorship');
        $manager->persist($leaderboardType);
        $manager->flush();

        $leaderboardType = new LeaderboardType();
        $leaderboardType->setName('share');
        $manager->persist($leaderboardType);
        $manager->flush();

    }

    public function getOrder()
    {
        return 550;
    }
}

<?php

namespace AdfabCore\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;

use AdfabReward\Entity\Action;
use Zend\Crypt\Password\Bcrypt;

/**
 *
 * @author GrG
 * Use the command : php doctrine-module.php data-fixture:import --append
 * to install these data into database
 */
class LoadActionData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * Load address types
     *
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $action = new Action();
        $action->setId(1);
        $action->setName('création de compte');
        $action->setPoints(200);
        $action->setRateLimit(0);
        $action->setRateLimitDuration(0);
        $action->setCountLimit(0);
        $action->setTeamCredit(0);

        $manager->persist($action);
        $manager->flush();

        $action = new Action();
        $action->setId(2);
        $action->setName('newsletter');
        $action->setPoints(150);
        $action->setRateLimit(0);
        $action->setRateLimitDuration(0);
        $action->setCountLimit(0);
        $action->setTeamCredit(0);

        $manager->persist($action);
        $manager->flush();

        $action = new Action();
        $action->setId(3);
        $action->setName('newsletter partenaires');
        $action->setPoints(150);
        $action->setRateLimit(0);
        $action->setRateLimitDuration(0);
        $action->setCountLimit(0);
        $action->setTeamCredit(0);

        $manager->persist($action);
        $manager->flush();

        $action = new Action();
        $action->setId(4);
        $action->setName('pseudo');
        $action->setPoints(100);
        $action->setRateLimit(0);
        $action->setRateLimitDuration(0);
        $action->setCountLimit(0);
        $action->setTeamCredit(0);

        $manager->persist($action);
        $manager->flush();

        $action = new Action();
        $action->setId(5);
        $action->setName('avatar');
        $action->setPoints(150);
        $action->setRateLimit(0);
        $action->setRateLimitDuration(0);
        $action->setCountLimit(0);
        $action->setTeamCredit(0);

        $manager->persist($action);
        $manager->flush();

        $action = new Action();
        $action->setId(6);
        $action->setName('adresse');
        $action->setPoints(150);
        $action->setRateLimit(0);
        $action->setRateLimitDuration(0);
        $action->setCountLimit(0);
        $action->setTeamCredit(0);

        $manager->persist($action);
        $manager->flush();

        $action = new Action();
        $action->setId(7);
        $action->setName('ville');
        $action->setPoints(75);
        $action->setRateLimit(0);
        $action->setRateLimitDuration(0);
        $action->setCountLimit(0);
        $action->setTeamCredit(0);

        $manager->persist($action);
        $manager->flush();

        $action = new Action();
        $action->setId(8);
        $action->setName('téléphone');
        $action->setPoints(150);
        $action->setRateLimit(0);
        $action->setRateLimitDuration(0);
        $action->setCountLimit(0);
        $action->setTeamCredit(0);

        $manager->persist($action);
        $manager->flush();

        $action = new Action();
        $action->setId(10);
        $action->setName('validation de compte');
        $action->setPoints(100);
        $action->setRateLimit(0);
        $action->setRateLimitDuration(0);
        $action->setCountLimit(0);
        $action->setTeamCredit(0);

        $manager->persist($action);
        $manager->flush();

        $action = new Action();
        $action->setId(11);
        $action->setName('centres d\'intérêts');
        $action->setPoints(100);
        $action->setRateLimit(0);
        $action->setRateLimitDuration(0);
        $action->setCountLimit(0);
        $action->setTeamCredit(0);

        $manager->persist($action);
        $manager->flush();

        $action = new Action();
        $action->setId(12);
        $action->setName('inscription à un jeu');
        $action->setPoints(100);
        $action->setRateLimit(0);
        $action->setRateLimitDuration(0);
        $action->setCountLimit(0);
        $action->setTeamCredit(0);

        $manager->persist($action);
        $manager->flush();

        $action = new Action();
        $action->setId(13);
        $action->setName('partage par mail');
        $action->setPoints(0);
        $action->setRateLimit(0);
        $action->setRateLimitDuration(0);
        $action->setCountLimit(0);
        $action->setTeamCredit(0);

        $manager->persist($action);
        $manager->flush();

        $action = new Action();
        $action->setId(14);
        $action->setName('partage par FB Wall');
        $action->setPoints(0);
        $action->setRateLimit(0);
        $action->setRateLimitDuration(0);
        $action->setCountLimit(0);
        $action->setTeamCredit(0);

        $manager->persist($action);
        $manager->flush();

        $action = new Action();
        $action->setId(15);
        $action->setName('partage par invitation FB');
        $action->setPoints(0);
        $action->setRateLimit(0);
        $action->setRateLimitDuration(0);
        $action->setCountLimit(0);
        $action->setTeamCredit(0);

        $manager->persist($action);
        $manager->flush();

        $action = new Action();
        $action->setId(16);
        $action->setName('partage par twitter');
        $action->setPoints(0);
        $action->setRateLimit(0);
        $action->setRateLimitDuration(0);
        $action->setCountLimit(0);
        $action->setTeamCredit(0);

        $manager->persist($action);
        $manager->flush();

        $action = new Action();
        $action->setId(17);
        $action->setName('partage par google');
        $action->setPoints(0);
        $action->setRateLimit(0);
        $action->setRateLimitDuration(0);
        $action->setCountLimit(0);
        $action->setTeamCredit(0);

        $manager->persist($action);
        $manager->flush();

        $action = new Action();
        $action->setId(20);
        $action->setName('parrainage inscription');
        $action->setPoints(250);
        $action->setRateLimit(0);
        $action->setRateLimitDuration(0);
        $action->setCountLimit(0);
        $action->setTeamCredit(0);

        $manager->persist($action);
        $manager->flush();

        $action = new Action();
        $action->setId(25);
        $action->setName('bonus anniversaire');
        $action->setPoints(250);
        $action->setRateLimit(0);
        $action->setRateLimitDuration(0);
        $action->setCountLimit(0);
        $action->setTeamCredit(0);

        $manager->persist($action);
        $manager->flush();

        $action = new Action();
        $action->setId(30);
        $action->setName('Quiz : Bonnes réponses');
        $action->setPoints(0);
        $action->setRateLimit(0);
        $action->setRateLimitDuration(0);
        $action->setCountLimit(0);
        $action->setTeamCredit(0);

        $manager->persist($action);
        $manager->flush();

        $action = new Action();
        $action->setId(100);
        $action->setName('badge bronze');
        $action->setPoints(200);
        $action->setRateLimit(0);
        $action->setRateLimitDuration(0);
        $action->setCountLimit(0);
        $action->setTeamCredit(0);

        $manager->persist($action);
        $manager->flush();

        $action = new Action();
        $action->setId(101);
        $action->setName('badge argent');
        $action->setPoints(200);
        $action->setRateLimit(0);
        $action->setRateLimitDuration(0);
        $action->setCountLimit(0);
        $action->setTeamCredit(0);

        $manager->persist($action);
        $manager->flush();

        $action = new Action();
        $action->setId(102);
        $action->setName('badge or');
        $action->setPoints(200);
        $action->setRateLimit(0);
        $action->setRateLimitDuration(0);
        $action->setCountLimit(0);
        $action->setTeamCredit(0);

        $manager->persist($action);
        $manager->flush();

    }

    public function getOrder()
    {
        return 500;
    }
}

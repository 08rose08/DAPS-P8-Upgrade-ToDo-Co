<?php

namespace App\DataFixtures;

use App\Entity\Task;
use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @codeCoverageIgnore
 */
class AppFixtures extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $faker = \Faker\Factory::create('fr_FR');
        $users = [];

        $anonymousUser = new User();
        $anonymousUser->setUsername('Anonyme')
                    ->setPassword($this->encoder->encodePassword($anonymousUser, 'test'))
                    ->setEmail('anonyme@test.fr')
                    ->setRoles(['ROLE_USER']);
        $users[] = $anonymousUser;
        $manager->persist($anonymousUser);

        $adminUser = new User();
        $adminUser->setUsername('Admin')
                ->setPassword($this->encoder->encodePassword($adminUser, 'test'))
                ->setEmail('admin@test.fr')
                ->setRoles(['ROLE_ADMIN']);
        $users[] = $adminUser;
        $manager->persist($adminUser);

        for($i = 0; $i < 2; $i++) {
            $user = new User();
            $username = $faker->userName;
            $user->setUsername($username)
                ->setPassword($this->encoder->encodePassword($user, 'test'))
                ->setEmail($username.'@test.fr')
                ->setRoles(['ROLE_USER']);
            $users[] = $user;
            $manager->persist($user);  
        }

        for($j = 0; $j < 10; $j++) {
            $task = new Task();
            $task->setCreatedAt($faker->dateTimeBetween('-6 months'))
                ->setTitle($faker->sentence($nbWords = 3, $variableNbWords = true))
                ->setContent($faker->text($maxNbChars = 200))
                ->setIsDone($faker->boolean)
                ->setUser($faker->randomElement($users));
            $manager->persist($task);
        }

        $manager->flush();
    }
}

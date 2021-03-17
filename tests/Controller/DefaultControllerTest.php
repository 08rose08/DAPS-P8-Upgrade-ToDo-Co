<?php

namespace App\Tests\Controller;

use App\Entity\User;
//use App\DataFixtures\AppFixtures;
use App\Tests\NeedLogin;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    use NeedLogin;

    public function testIndexAction()
    {
        $client = static::createClient();

        $client->request('GET', '/');
        $this->assertResponseRedirects('');

        $crawler = $client->followRedirect();
        //$this->assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('button', 'Se connecter');

    }

    public function testIndexActionLogged()
    {
        //$this->loadFixtures([AppFixtures::class]);
        //$user = $userRepository->findOneBy(['username' => 'Anonyme']);
        //$user = $this->getUser();
        self::bootKernel();
        $user = self::$container->get('doctrine')->getManager()->getRepository(User::class)->findOneBy(['username' => 'Anonyme']);
        self::ensureKernelShutdown();

        $client = static::createClient();

        $this->loginUser($client, $user);
        $client->request('GET', '/');
        //$this->assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Bienvenue sur Todo List');
    }
}
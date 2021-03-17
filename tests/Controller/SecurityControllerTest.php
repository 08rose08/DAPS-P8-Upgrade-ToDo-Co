<?php

namespace App\Tests\Controller;

use App\Entity\User;
use App\Tests\NeedLogin;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityControllerTest extends WebTestCase
{
    use NeedLogin;
    
    public function testDisplayLogin()
    {
        $client = static::createClient();
        $client->request('GET', '/login');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('button', 'Se connecter');
        $this->assertSelectorNotExists('.alert.alert-danger');
    }

    public function testDisplayLoginLogged()
    {   
        self::bootKernel();
        $user = self::$container->get('doctrine')->getManager()->getRepository(User::class)->findOneBy(['username' => 'Anonyme']);
        self::ensureKernelShutdown();

        $client = static::createClient();

        $this->loginUser($client, $user);
        $client->request('GET', '/login');

        $this->assertResponseRedirects();

        $client->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Bienvenue sur Todo List');    
    }  

    public function testLoginWithBadCredentials()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');

        $form = $crawler->selectButton('Se connecter')->form([
            '_username' => 'Anonyme',
            '_password' => 'failtest'
        ]);
        $client->submit($form);

        $this->assertResponseRedirects();
        $client->followRedirect();
        //$this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('.alert.alert-danger');
        $this->assertSelectorTextContains('button', 'Se connecter');
    }

    public function testLoginWithGoodCredentials()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');

        $form = $crawler->selectButton('Se connecter')->form([
            '_username' => 'Anonyme',
            '_password' => 'test'
        ]);
        $client->submit($form);

        $this->assertResponseRedirects();
        $client->followRedirect();
        //$this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Bienvenue sur Todo List');
    }

    public function testLinkLogout()
    {
        self::bootKernel();
        $user = self::$container->get('doctrine')->getManager()->getRepository(User::class)->findOneBy(['username' => 'Anonyme']);
        self::ensureKernelShutdown();

        $client = static::createClient();

        $this->loginUser($client, $user);

        $client->request('GET', '/');
        $client->clickLink('Se déconnecter');

        $this->assertResponseRedirects();
        //redirect to /, then to /login
        $client->followRedirect();
        $client->followRedirect();
        $this->assertSelectorTextContains('button', 'Se connecter');
    }

    public function testLogout()
    {
        self::bootKernel();
        $user = self::$container->get('doctrine')->getManager()->getRepository(User::class)->findOneBy(['username' => 'Anonyme']);
        self::ensureKernelShutdown();

        $client = static::createClient();

        $this->loginUser($client, $user);
        $client->request('GET', '/logout');

        $this->assertResponseRedirects();
        $client->followRedirect();
        $client->followRedirect();

        $this->assertSelectorTextContains('button', 'Se connecter');
    }
}
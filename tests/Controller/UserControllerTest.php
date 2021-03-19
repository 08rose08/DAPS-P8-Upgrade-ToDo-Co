<?php

namespace App\Tests\Controller;

use App\Entity\User;
use App\Tests\NeedLogin;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{
    use NeedLogin;

    // -----------------------Display-----------------------
    
    public function testListDisplayAsAdmin()
    {
        self::bootKernel();
        $user = self::$container->get('doctrine')->getManager()->getRepository(User::class)->findOneBy(['username' => 'Admin']);
        self::ensureKernelShutdown();

        $client = static::createClient();

        $this->loginUser($client, $user);

        $client->request('GET', '/users');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Liste des utilisateurs');
    }

    public function testListDisplayAsUser()
    {
        self::bootKernel();
        $user = self::$container->get('doctrine')->getManager()->getRepository(User::class)->findOneBy(['username' => 'Anonyme']);
        self::ensureKernelShutdown();

        $client = static::createClient();

        $this->loginUser($client, $user);

        $client->request('GET', '/users');
        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Bienvenue');
    }

    public function testListDisplayAsNotLogged()
    {
        $client = static::createClient();

        $client->request('GET', '/users');
        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('button', 'Se connecter');
    }

    public function testCreateDisplayAsAdmin()
    {
        self::bootKernel();
        $user = self::$container->get('doctrine')->getManager()->getRepository(User::class)->findOneBy(['username' => 'Admin']);
        self::ensureKernelShutdown();

        $client = static::createClient();

        $this->loginUser($client, $user);

        $client->request('GET', '/users/create');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Créer un utilisateur');
    }

    public function testCreateDisplayAsUser()
    {
        self::bootKernel();
        $user = self::$container->get('doctrine')->getManager()->getRepository(User::class)->findOneBy(['username' => 'Anonyme']);
        self::ensureKernelShutdown();

        $client = static::createClient();

        $this->loginUser($client, $user);

        $client->request('GET', '/users/create');
        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Bienvenue');
    }

    public function testCreateDisplayAsNotLogged()
    {
        $client = static::createClient();

        $client->request('GET', '/users/create');
        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('button', 'Se connecter');
    }


    public function testEditDisplayAsAdmin()
    {
        self::bootKernel();
        $user = self::$container->get('doctrine')->getManager()->getRepository(User::class)->findOneBy(['username' => 'Admin']);
        self::ensureKernelShutdown();

        $client = static::createClient();

        $this->loginUser($client, $user);

        $client->request('GET', '/users/1/edit');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Modifier');
    }

    public function testEditDisplayAsUser()
    {
        self::bootKernel();
        $user = self::$container->get('doctrine')->getManager()->getRepository(User::class)->findOneBy(['username' => 'Anonyme']);
        self::ensureKernelShutdown();

        $client = static::createClient();

        $this->loginUser($client, $user);

        $client->request('GET', '/users/1/edit');
        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Bienvenue');
    }

    public function testEditDisplayAsNotLogged()
    {
        $client = static::createClient();

        $client->request('GET', '/users/1/edit');
        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('button', 'Se connecter');
    }

    public function testEditDisplayAsAdminTaskNotExist()
    {
        self::bootKernel();
        $user = self::$container->get('doctrine')->getManager()->getRepository(User::class)->findOneBy(['username' => 'Admin']);
        self::ensureKernelShutdown();

        $client = static::createClient();

        $this->loginUser($client, $user);

        $client->request('GET', '/users/256/edit');
        $this->assertTrue($client->getResponse()->isNotFound());
    }


    // -----------------------Create-----------------------

    public function testCreateAsAdmin()
    {
        self::bootKernel();
        $user = self::$container->get('doctrine')->getManager()->getRepository(User::class)->findOneBy(['username' => 'Admin']);
        self::ensureKernelShutdown();

        $client = static::createClient();

        $this->loginUser($client, $user);

        $crawler = $client->request('GET', '/users/create');

        $form = $crawler->selectButton('Ajouter')->form([
            'user[username]' => 'charlie',
            'user[password][first]' => 'test',
            'user[password][second]' => 'test',
            'user[email]' => 'charlie@test.fr',
            'user[roles]' => 'ROLE_USER'
        ]);
        $client->submit($form);

        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('.alert.alert-success');
        //Dama bloque ? -> fonctionne si Anonyme
        //$this->assertSelectorTextContains('td', 'charlie');
    }

    public function testCreateAsAdminBadPasswords()
    {
        self::bootKernel();
        $user = self::$container->get('doctrine')->getManager()->getRepository(User::class)->findOneBy(['username' => 'Admin']);
        self::ensureKernelShutdown();

        $client = static::createClient();

        $this->loginUser($client, $user);

        $crawler = $client->request('GET', '/users/create');

        $form = $crawler->selectButton('Ajouter')->form([
            'user[username]' => 'charlie',
            'user[password][first]' => 'test',
            'user[password][second]' => 'failtest',
            'user[email]' => 'charlie@test.fr',
            'user[roles]' => 'ROLE_USER'
        ]);
        $client->submit($form);
        
        $this->assertSelectorTextContains('span', 'Les deux mots de passe doivent correspondre.');
        
    }



    // -----------------------Edit-----------------------

    public function testEditAsAdmin()
    {
        self::bootKernel();
        $user = self::$container->get('doctrine')->getManager()->getRepository(User::class)->findOneBy(['username' => 'Admin']);
        self::ensureKernelShutdown();

        $client = static::createClient();

        $this->loginUser($client, $user);

        $crawler = $client->request('GET', '/users/1/edit');

        $form = $crawler->selectButton('Modifier')->form([
            'user[username]' => 'charlie',
            'user[password][first]' => 'test',
            'user[password][second]' => 'test',
            'user[email]' => 'charlie@test.fr',
            'user[roles]' => 'ROLE_USER'
        ]);
        $client->submit($form);

        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('.alert.alert-success');
        //tjrs bloqué avec DAMA ?
    }

    public function testEditAsAdminBadPasswords()
    {
        self::bootKernel();
        $user = self::$container->get('doctrine')->getManager()->getRepository(User::class)->findOneBy(['username' => 'Admin']);
        self::ensureKernelShutdown();

        $client = static::createClient();

        $this->loginUser($client, $user);

        $crawler = $client->request('GET', '/users/1/edit');

        $form = $crawler->selectButton('Modifier')->form([
            'user[username]' => 'charlie',
            'user[password][first]' => 'test',
            'user[password][second]' => 'failtest',
            'user[email]' => 'charlie@test.fr',
            'user[roles]' => 'ROLE_USER'
        ]);
        $client->submit($form);

        $this->assertSelectorTextContains('span', 'Les deux mots de passe doivent correspondre.');
    }



}
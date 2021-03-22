<?php

namespace App\Tests\Controller;

use App\Entity\User;
use App\Tests\NeedLogin;
use App\Repository\UserRepository;
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

    public function testLoginWithBadUsername()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');

        $form = $crawler->selectButton('Se connecter')->form([
            '_username' => 'Lars',
            '_password' => 'test'
        ]);
        $client->submit($form);

        $this->assertResponseRedirects();
        $crawler = $client->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('.alert.alert-danger');
        //$this->assertSelectorTextContains('div', 'Zut! Identifiants invalides.');
        $this->assertSame(1, $crawler->filter('html:contains("Zut!")')->count());
        $this->assertSelectorTextContains('button', 'Se connecter');
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
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('.alert.alert-danger');
        $this->assertSelectorTextContains('button', 'Se connecter');
    }

    public function testLoginWithBadCsrfToken()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');

        $form = $crawler->selectButton('Se connecter')->form([
            '_username' => 'Anonyme',
            '_password' => 'failtest',
            '_csrf_token' => 'failToken'
        ]);
        $client->submit($form);

        $this->assertResponseRedirects();
        $client->followRedirect();
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
        $crawler = $client->followRedirect();
        $this->assertResponseIsSuccessful();
        //echo $crawler->html();
        $this->assertSelectorTextContains('h1', 'Bienvenue sur Todo List');
        //$this->assertSame(1, $crawler->filter('html:contains("Bienvenue")')->count());
    }

    public function testLoginWithGoodCredentialsFromTasksPath()
    {
        $client = static::createClient();
        $client->request('GET', '/tasks');
        $crawler = $client->followRedirect();
        $form = $crawler->selectButton('Se connecter')->form([
            '_username' => 'Anonyme',
            '_password' => 'test'
        ]);
        $client->submit($form);
        
        $this->assertResponseRedirects();
        $crawler = $client->followRedirect();
        $this->assertResponseIsSuccessful();
        //echo $crawler->html();
        $this->assertSelectorTextContains('h1', 'Liste des tâches');
        //$this->assertSame(1, $crawler->filter('html:contains("Bienvenue")')->count());
    }


    // public function testLoginWithGoodCredentialsPost()
    // {
    //     $client = static::createClient();
    //     $client->request('POST', '/login', [
    //         '_username' => 'Anonyme',
    //         '_password' => 'test'
    //     ]);

    //     // $form = $crawler->selectButton('Se connecter')->form([
    //     //     '_username' => 'Anonyme',
    //     //     '_password' => 'test'
    //     // ]);
    //     // $client->submit($form);

    //     $this->assertResponseRedirects();
    //     $client->followRedirect();
    //     $this->assertResponseIsSuccessful();
    //     $this->assertSelectorTextContains('h1', 'Bienvenue sur Todo List');
    // }

    public function testLinkLogout()
    {
        self::bootKernel();
        $user = self::$container->get(UserRepository::class)->findOneBy(['username' => 'Anonyme']);
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
<?php

namespace App\Tests\Controller;

use App\Entity\User;
use App\Tests\NeedLogin;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TaskControllerTest extends WebTestCase
{
    use NeedLogin;

    // -----------------------Display-----------------------
    
    public function testListDisplayAsUser()
    {
        self::bootKernel();
        $user = self::$container->get('doctrine')->getManager()->getRepository(User::class)->findOneBy(['username' => 'Anonyme']);
        self::ensureKernelShutdown();

        $client = static::createClient();

        $this->loginUser($client, $user);

        $crawler = $client->request('GET', '/tasks');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Liste des tâches');
        //+ verifier qu'il n' y pas d'elt span avec la classe glyphicon-ok
        $this->assertSelectorTextContains('button', 'Marquer comme faite');
        $this->assertSame(0, $crawler->filter('html:contains("Marquer non terminée")')->count());
        
    }

    public function testListDisplayAsNotLogged()
    {
        $client = static::createClient();

        $client->request('GET', '/tasks');
        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('button', 'Se connecter');
    }

    public function testListDoneDisplayAsUser()
    {
        self::bootKernel();
        $user = self::$container->get('doctrine')->getManager()->getRepository(User::class)->findOneBy(['username' => 'Anonyme']);
        self::ensureKernelShutdown();

        $client = static::createClient();

        $this->loginUser($client, $user);

        $crawler = $client->request('GET', '/tasks/done');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Liste des tâches');
        //+ verifier qu'il n' y pas d'elt span avec la classe glyphicon-remove
        $this->assertSelectorTextContains('button', 'Marquer non terminée');
        $this->assertSame(0, $crawler->filter('html:contains("Marquer comme faite")')->count());
    }

    public function testListDoneDisplayAsNotLogged()
    {
        $client = static::createClient();

        $client->request('GET', '/tasks/done');
        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('button', 'Se connecter');
    }

    public function testCreateDisplayAsUser()
    {
        self::bootKernel();
        $user = self::$container->get('doctrine')->getManager()->getRepository(User::class)->findOneBy(['username' => 'Anonyme']);
        self::ensureKernelShutdown();

        $client = static::createClient();

        $this->loginUser($client, $user);

        $crawler = $client->request('GET', '/tasks/create');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Nouvelle tâche');
        $this->assertSelectorTextContains('button', 'Ajouter');
        
    }

    public function testCreateDisplayAsNotLogged()
    {
        $client = static::createClient();

        $client->request('GET', '/tasks/create');
        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('button', 'Se connecter');
    }

    public function testEditDisplayAsUserAuthor()
    {
        self::bootKernel();
        $user = self::$container->get('doctrine')->getManager()->getRepository(User::class)->findOneBy(['username' => 'Anonyme']);
        self::ensureKernelShutdown();

        $client = static::createClient();

        $this->loginUser($client, $user);

        $crawler = $client->request('GET', '/tasks/3/edit');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Modifier la tâche');
        $this->assertSelectorTextContains('button', 'Modifier');
        
    }

    public function testEditDisplayAsUserNotAuthor()
    {
        self::bootKernel();
        $user = self::$container->get('doctrine')->getManager()->getRepository(User::class)->findOneBy(['username' => 'Anonyme']);
        self::ensureKernelShutdown();

        $client = static::createClient();

        $this->loginUser($client, $user);

        $crawler = $client->request('GET', '/tasks/1/edit');
        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('.alert.alert-danger');
        $this->assertSelectorTextContains('h1', 'Liste des tâches');
    }

    public function testEditDisplayAsAdminNotAuthorButAnonyme()
    {
        self::bootKernel();
        $user = self::$container->get('doctrine')->getManager()->getRepository(User::class)->findOneBy(['username' => 'Admin']);
        self::ensureKernelShutdown();

        $client = static::createClient();

        $this->loginUser($client, $user);

        $crawler = $client->request('GET', '/tasks/3/edit');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Modifier la tâche');
        $this->assertSelectorTextContains('button', 'Modifier');
    }

    public function testEditDisplayAsNotLogged()
    {
        $client = static::createClient();

        $client->request('GET', '/tasks/1/edit');
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

        $client->request('GET', '/tasks/256/edit');
        $this->assertTrue($client->getResponse()->isNotFound());
    }




// -----------------------Delete-----------------------

    // Verifier la BDD !!

    public function testDeleteAsUserAuthor()
    {
        self::bootKernel();
        $user = self::$container->get('doctrine')->getManager()->getRepository(User::class)->findOneBy(['username' => 'Anonyme']);
        self::ensureKernelShutdown();

        $client = static::createClient();

        $this->loginUser($client, $user);

        $client->request('GET', '/tasks/3/delete');
        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Liste des tâches');
        $this->assertSelectorExists('.alert.alert-success');
        
    }

    public function testDeleteAsUserNotAuthor()
    {
        self::bootKernel();
        $user = self::$container->get('doctrine')->getManager()->getRepository(User::class)->findOneBy(['username' => 'Anonyme']);
        self::ensureKernelShutdown();

        $client = static::createClient();

        $this->loginUser($client, $user);

        $client->request('GET', '/tasks/1/delete');
        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('.alert.alert-danger');
        $this->assertSelectorTextContains('h1', 'Liste des tâches');
    }

    public function testDeleteAsAdminNotAuthorButAnonyme()
    {
        self::bootKernel();
        $user = self::$container->get('doctrine')->getManager()->getRepository(User::class)->findOneBy(['username' => 'Admin']);
        self::ensureKernelShutdown();

        $client = static::createClient();

        $this->loginUser($client, $user);

        $client->request('GET', '/tasks/3/delete');
        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Liste des tâches');
        $this->assertSelectorExists('.alert.alert-success');
    }

    public function testDeleteAsNotLogged()
    {
        $client = static::createClient();

        $client->request('GET', '/tasks/1/delete');
        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('button', 'Se connecter');
    }

    public function testDeleteAsAdminTaskNotExist()
    {
        self::bootKernel();
        $user = self::$container->get('doctrine')->getManager()->getRepository(User::class)->findOneBy(['username' => 'Admin']);
        self::ensureKernelShutdown();

        $client = static::createClient();

        $this->loginUser($client, $user);

        $client->request('GET', '/tasks/256/delete');
        $this->assertTrue($client->getResponse()->isNotFound());
    }


    // -----------------------toggle-----------------------

    // Verifier la BDD !!

    public function testToggleAsUser()
    {
        self::bootKernel();
        $user = self::$container->get('doctrine')->getManager()->getRepository(User::class)->findOneBy(['username' => 'Anonyme']);
        self::ensureKernelShutdown();

        $client = static::createClient();

        $this->loginUser($client, $user);

        $client->request('GET', '/tasks/3/toggle');
        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Liste des tâches');
        $this->assertSelectorExists('.alert.alert-success');
        
    }

    public function testToggleAsNotLogged()
    {
        $client = static::createClient();

        $client->request('GET', '/tasks/3/toggle');
        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('button', 'Se connecter');
        
    }


        // -----------------------create-----------------------

    public function testCreateAsUser()
    {
        self::bootKernel();
        $user = self::$container->get('doctrine')->getManager()->getRepository(User::class)->findOneBy(['username' => 'Anonyme']);
        self::ensureKernelShutdown();

        $client = static::createClient();

        $this->loginUser($client, $user);

        $crawler = $client->request('GET', '/tasks/create');

        $form = $crawler->selectButton('Ajouter')->form([
            'task[title]' => 'Le titre',
            'task[content]' => 'le contenu'
        ]);
        $client->submit($form);

        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('.alert.alert-success');
        //Dama bloque ?
        //$this->assertSelectorTextContains('a', 'Le titre');
    }

    public function testCreateAsUserEmptyForm()
    {
        self::bootKernel();
        $user = self::$container->get('doctrine')->getManager()->getRepository(User::class)->findOneBy(['username' => 'Anonyme']);
        self::ensureKernelShutdown();

        $client = static::createClient();

        $this->loginUser($client, $user);

        $crawler = $client->request('GET', '/tasks/create');

        $form = $crawler->selectButton('Ajouter')->form([
            'task[title]' => '',
            'task[content]' => ''
        ]);
        $client->submit($form);

        $this->assertResponseIsSuccessful();
        //$this->assertSelectorTextContains('??', 'Vous devez saisir un titre.');
        //$this->assertSelectorTextContains('??', 'Vous devez saisir du contenu.');

    }

    public function testCreateAsNotLogged()
    {
        $client = static::createClient();

        $client->request('POST', '/tasks/create', [
            'task[title]' => 'Le titre',
            'task[content]' => 'le contenu'
        ]);

        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('button', 'Se connecter');
        
    }

    // -----------------------edit-----------------------

    public function testEditAsUser()
    {
        self::bootKernel();
        $user = self::$container->get('doctrine')->getManager()->getRepository(User::class)->findOneBy(['username' => 'Anonyme']);
        self::ensureKernelShutdown();

        $client = static::createClient();

        $this->loginUser($client, $user);

        $crawler = $client->request('GET', '/tasks/3/edit');

        $form = $crawler->selectButton('Modifier')->form([
            'task[title]' => 'Le titre',
            'task[content]' => 'le contenu'
        ]);
        $client->submit($form);

        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('.alert.alert-success');
        //Dama bloque ?
        //$this->assertSelectorTextContains('a', 'Le titre');
    }

    public function testEditAsUserEmptyForm()
    {
        self::bootKernel();
        $user = self::$container->get('doctrine')->getManager()->getRepository(User::class)->findOneBy(['username' => 'Anonyme']);
        self::ensureKernelShutdown();

        $client = static::createClient();

        $this->loginUser($client, $user);

        $crawler = $client->request('GET', '/tasks/create');

        $form = $crawler->selectButton('Ajouter')->form([
            'task[title]' => '',
            'task[content]' => ''
        ]);
        $client->submit($form);

        $this->assertResponseIsSuccessful();
        //$this->assertSelectorTextContains('??', 'Vous devez saisir un titre.');
        //$this->assertSelectorTextContains('??', 'Vous devez saisir du contenu.');

    }

    public function testEditAsNotLogged()
    {
        
        $client = static::createClient();

        $client->request('POST', '/tasks/3/edit', [
            'task[title]' => 'Le titre',
            'task[content]' => 'le contenu'
        ]);

        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('button', 'Se connecter');
        
    }

    public function testEditAsUserButBadAuthor()
    {
        
        self::bootKernel();
        $user = self::$container->get('doctrine')->getManager()->getRepository(User::class)->findOneBy(['username' => 'Anonyme']);
        self::ensureKernelShutdown();

        $client = static::createClient();

        $this->loginUser($client, $user);

        $client->request('POST', '/tasks/1/edit', [
            'task[title]' => 'Le titre',
            'task[content]' => 'le contenu'
        ]);

        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('.alert.alert-danger');
        $this->assertSelectorTextContains('h1', 'Liste des tâches');
        
    }

}
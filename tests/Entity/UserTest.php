<?php

namespace App\Tests\Entity;

use App\Entity\Task;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
//use Symfony\Component\Validator\ConstraintViolation;

class UserTest extends KernelTestCase
{
    public function getEntity(): User
    {        
        return (new User())
            ->setUsername('UserTest')
            ->setEmail('userTest@test.fr')
            ->setPassword('test') // = 'test'
            ->setRoles(['ROLE_USER']);
    }
    // public function getTask(): Task
    // {
    //     return (new Task())
    //         ->setTitle('TitleTest')
    //         ->setContent('Le content test');
    // }

    public function assertHasErrors(User $user, int $nb = 0)
    {
        self::bootKernel();

        $error = self::$container->get('validator')->validate($user);
        $this->assertCount($nb, $error);
    }

    public function testValidEntity() 
    {
        $this->assertHasErrors($this->getEntity(), 0);
    }


    
    public function testId()
    {
        $this->assertSame(null, $this->getEntity()->getId());
    }

    public function testUsername()
    {
        $this->assertSame('UserTest', $this->getEntity()->getUsername());
    }

    public function testPassword()
    {
        $this->assertSame('test', $this->getEntity()->getPassword());
    }

    public function testEmail()
    {
        $this->assertSame('userTest@test.fr', $this->getEntity()->getEmail());
    }

    public function testRole()
    {
        $this->assertSame(['ROLE_USER'], $this->getEntity()->getRoles());
    }

    public function testTask()
    {
        $this->assertEmpty($this->getEntity()->getTasks());        
    }
    
    public function testAddTask()
    {
        $task = new Task;
        $task->setTitle('TitleTest')
            ->setContent('Le content test');
        
        $this->assertCount(1, $this->getEntity()->addTask($task)->getTasks());
    }

    public function testRemoveTask()
    {
        $task = new Task;
        $user = $this->getEntity();
        $task->setUser($user);
        $this->assertCount(1, $user->addTask($task)->getTasks());
        $this->assertCount(0, $user->removeTask($task)->getTasks());
        $this->assertSame(null, $task->getUser());
    }

    public function testSalt()
    {
        $this->assertSame(null, $this->getEntity()->getSalt());
    }



    
    public function testInvalidUniqueUsernameEntity() 
    {  
        //Nom existant dans la fixture
        $this->assertHasErrors($this->getEntity()->setUserName("Anonyme"), 1);
    }

    public function testInvalidUniqueEmailEntity() 
    {  
         //Email existant dans la fixture
        $this->assertHasErrors($this->getEntity()->setEmail("anonyme@test.fr"), 1);
    }

    public function testBlankUsernameEntity()
    {
        $this->assertHasErrors($this->getEntity()->setUsername(''), 1); //Nom vide 
    }

    public function testInvalidFormatEmailEntity()
    {
        $this->assertHasErrors($this->getEntity()->setEmail('usertest@test'), 1);
    }

    public function testBlankEmailEntity()
    {
        $this->assertHasErrors($this->getEntity()->setEmail(''), 1); //Email vide
    }


    
}
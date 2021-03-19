<?php

namespace App\Tests\Entity;

use App\Entity\Task;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\ConstraintViolation;

class TaskTest extends KernelTestCase
{
    public function getEntity(): Task
    {      
        return (new Task())
        ->setTitle('TitleTest')
        ->setContent('Le content test'); 
    }
    
    public function assertHasErrors(Task $task, int $nb = 0)
    {
        self::bootKernel();

        $errors = self::$container->get('validator')->validate($task);
        $messages = [];

        /** @var ConstraintViolation $error */
        foreach ($errors as $error) {
            $messages[] = $error->getPropertyPath().' => '.$error->getMessage();
        }

        $this->assertCount($nb, $errors, implode(', ', $messages));
    }

    public function testValidEntity() 
    {
        $this->assertHasErrors($this->getEntity(), 0);
    }

    public function testId()
    {
        $this->assertSame(null, $this->getEntity()->getId());
    }

    public function testTitle()
    {
        $this->assertSame('TitleTest', $this->getEntity()->getTitle());
    }

    public function testContent()
    {
        $this->assertSame('Le content test', $this->getEntity()->getContent());
    }

    public function testIsDone()
    {
        $this->assertSame(false, $this->getEntity()->isDone());
        $this->assertSame(true, $this->getEntity()->setIsDone(true)->isDone());
        $entity = $this->getEntity();
        $entity->toggle(true);
        $this->assertSame(true, $entity->isDone());
    }
    public function testCreatedAt()
    {
        $date = new \Datetime();
        $this->assertSame($date, $this->getEntity()->setCreatedAt($date)->getCreatedAt());
    }

    public function testUser()
    {
        $user = new User;
        $this->assertSame(null, $this->getEntity()->getUser());
        $this->assertSame($user, $this->getEntity()->setUser($user)->getUser());
    }


    public function testBlankTitleEntity()
    {
        $this->assertHasErrors($this->getEntity()->setTitle(''), 1); //titre vide 
    }
    public function testBlankContentEntity()
    {
        $this->assertHasErrors($this->getEntity()->setContent(''), 1); //content vide 
    }
}
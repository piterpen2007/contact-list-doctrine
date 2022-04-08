<?php

namespace EfTech\ContactListTest;

use Doctrine\ORM\EntityManagerInterface;
use EfTech\ContactList\Config\ContainerExtensions;
use EfTech\ContactList\Entity\Recipient;
use EfTech\ContactList\Infrastructure\DI\SymfonyDiContainerInit;
use PHPUnit\Framework\TestCase;

class TestDoctrineRecipient extends TestCase
{
    /**
     * @throws \Exception
     */
    public function testGetCurrency(): void
    {
        //Arrange
        $diContainerFactory = new SymfonyDiContainerInit(
            new SymfonyDiContainerInit\ContainerParams(
                __DIR__ . '/../config/dev/di.xml',
                [
                    'kernel.project_dir' => __DIR__ . '/../'
                ],
                ContainerExtensions::httpAppContainerExtension()
            )
        );
        $diContainer = $diContainerFactory();
        /** @var EntityManagerInterface $em */
        $em = $diContainer->get(EntityManagerInterface::class);

        $recipient = $em->getRepository(Recipient::class)->findOneBy(['full_name' => 'Калинин Пётр Александрович']);

        $this->assertInstanceOf(Recipient::class, $recipient, 'Некорректный объект');
    }
}

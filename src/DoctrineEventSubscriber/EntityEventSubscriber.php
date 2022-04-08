<?php

namespace EfTech\ContactList\DoctrineEventSubscriber;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use Doctrine\ORM\UnitOfWork;
use EfTech\ContactList\Entity\Address;
use EfTech\ContactList\Entity\Address\Status;
use Psr\Log\LoggerInterface;

/**
 * Подписчик на события связанные с сущностью
 */
class EntityEventSubscriber implements EventSubscriber
{
    /**
     * Логгер
     *
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function getSubscribedEvents(): array
    {
        return [Events::preUpdate,Events::onFlush];
    }



    /**
     * Обработчик события onFlush
     *
     * @param OnFlushEventArgs $args
     */
    public function onFlush(OnFlushEventArgs $args): void
    {
        $uof = $args->getEntityManager()->getUnitOfWork();

        $entityInsert = $uof->getScheduledEntityInsertions();

        $em = $args->getEntityManager();

        foreach ($entityInsert as $item) {
            $this->dispatchInsertStatus($item, $uof);
            $this->dispatchInsertTextDocument($item, $uof, $em);
        }
    }

    private function dispatchInsertTextDocument($entityInsert, UnitOfWork $uof, EntityManagerInterface $em): void
    {
        if ($entityInsert instanceof Address) {
            $oldStatus = $entityInsert->getStatus();
            $entityStatus = $em->getRepository(Status::class)
                ->findOneBy(['name' => $oldStatus->getName()]);
            $uof->propertyChanged($entityInsert, 'status', $oldStatus, $entityStatus);
        }
    }

    private function dispatchInsertStatus($entityInsert, UnitOfWork $uof): void
    {
        if ($entityInsert instanceof Status) {
            $uof->scheduleForDelete($entityInsert);
        }
    }
}

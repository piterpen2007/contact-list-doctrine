<?php

namespace EfTech\ContactList\DoctrineEventSubscriber;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use Doctrine\ORM\UnitOfWork;
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
     * Автоматическая регистрация журнала в коллекции
     *
     * @param $entityForInsert
     * @param UnitOfWork $uof
     */
    private function autoRegisterMagazineNumber($entityForInsert, UnitOfWork $uof): void
    {
//        if ($entityForInsert instanceof MagazineNumber) {
//            $magazine = $entityForInsert->getMagazine();
//
//            /** @var Collection $magazineNumberCollection */
//            $magazineNumberCollection = $uof->getOriginalEntityData($magazine)['numbers'];
//
//            if (false === $magazineNumberCollection->contains($entityForInsert)) {
//                $magazineNumberCollection->add($entityForInsert);
//            }
//        }
    }


    /**
     * Обработчик события onFlush
     *
     * @param OnFlushEventArgs $args
     */
    public function onFlush(OnFlushEventArgs $args): void
    {
//        $uof = $args->getEntityManager()->getUnitOfWork();
//
//        $entityInsert = $uof->getScheduledEntityInsertions();
//
//        $em = $args->getEntityManager();
//
//        foreach ($entityInsert as $item) {
//            $this->dispatchInsertStatus($item, $uof);
//            $this->dispatchInsertTextDocument($item, $uof, $em);
//            $this->autoRegisterMagazineNumber($item, $uof);
//        }
    }

    private function dispatchInsertTextDocument($entityInsert, UnitOfWork $uof, EntityManagerInterface $em): void
    {
//        if ($entityInsert instanceof AbstractTextDocument) {
//            $oldStatus = $entityInsert->getStatus();
//            $entityStatus = $em->getRepository(Status::class)
//                ->findOneBy(['name' => $oldStatus->getName()]);
//            $uof->propertyChanged($entityInsert, 'status', $oldStatus, $entityStatus);
//        }
    }

    private function dispatchInsertStatus($entityInsert, UnitOfWork $uof): void
    {
//        if ($entityInsert instanceof Status) {
//            $uof->scheduleForDelete($entityInsert);
//        }
    }
    /**
     * Обработчик события preUpdate
     *
     * @param PreUpdateEventArgs $args
     */
    public function preUpdate(PreUpdateEventArgs $args): void
    {
//        $entity = $args->getEntity();
//
//        if ($entity instanceof AbstractTextDocument && $args->hasChangedField('status')) {
//            $entityStatus = $args->getEntityManager()
//                ->getRepository(Status::class)
//                ->findOneBy(['name' => $entity->getStatus()->getName()]);
//
//            $args->setNewValue('status', $entityStatus);
//        }
//
//        $this->logger->debug('Event postLoad:' . get_class($args->getEntity()));
    }
}

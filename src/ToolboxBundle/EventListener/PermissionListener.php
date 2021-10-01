<?php

namespace ToolboxBundle\EventListener;

use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use ToolboxBundle\Manager\PermissionManagerInterface;

class PermissionListener implements EventSubscriberInterface
{
    protected PermissionManagerInterface $permissionManager;

    public function __construct(PermissionManagerInterface $permissionManager)
    {
        $this->permissionManager = $permissionManager;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ConsoleEvents::COMMAND => 'onConsoleCommand'
        ];
    }

    public function onConsoleCommand(ConsoleCommandEvent $event): void
    {
        $command = $event->getCommand();

        if ($command === null) {
            return;
        }

        if ($command->getName() !== 'cache:clear') {
            return;
        }

        $this->permissionManager->synchroniseEditablePermissions();
    }
}

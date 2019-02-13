<?php

namespace ToolboxBundle\Command;

use Pimcore\Cache;
use Pimcore\Model\Element\Service;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DynamicLinkMigrationCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('toolbox:migrate-dynamic-link')
            ->setDescription('Migrate Dynamic Link Tag to Pimcore Link Tag');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $db = \Pimcore\Db::get();

        $elements = $db->fetchAll('SELECT * FROM documents_elements WHERE type = ?', ['dynamiclink']);

        $documentTagsToClear = [];

        foreach ($elements as $element) {
            $data = \Pimcore\Tool\Serialize::unserialize($element['data']);
            $newData = $data;

            if (!is_array($data)) {
                continue;
            }

            $documentTagsToClear[] = $element['documentId'];

            $output->writeln(sprintf('<comment>check "%s" for document %s</comment>', $element['name'], $element['documentId']));

            if (strpos($data['path'], '::') !== false) {
                $pathFragments = explode('::', $data['path']);

                if (count($pathFragments) === 2) {
                    $oldPath = $pathFragments[1];

                    $target = Service::getElementByPath('object', $oldPath);

                    if ($target) {
                        $newData['internal'] = true;
                        $newData['internalId'] = $target->getId();
                        $newData['internalType'] = 'object';
                        $newData['path'] = $target->getFullPath();
                        $newData['linktype'] = 'internal';
                    } else {
                        $newData['internal'] = false;
                        $newData['internalId'] = null;
                        $newData['internalType'] = null;
                        $newData['linktype'] = 'direct';
                        $newData['path'] = '';
                    }

                    unset($newData['type']);

                    $output->writeln(sprintf('  -> <info>tranform dynamic path "%s" to "%s"</info>', $oldPath, $newData['path']));
                }
            }

            $output->writeln('<question>update dynamic link in db...</question>');

            $dbData = \Pimcore\Tool\Serialize::serialize($newData);
            $db->update('documents_elements', ['type' => 'link', 'data' => $dbData], [
                'documentId' => $element['documentId'],
                'name'       => $element['name']
            ]);
        }

        if (count($documentTagsToClear) > 0) {
            $output->writeln('');
            $output->writeln('');
            $output->writeln('<info>clear document cache...</info>');

            foreach (array_unique($documentTagsToClear) as $tag) {
                Cache::clearTag('document_' . $tag);
            }
        }
    }
}

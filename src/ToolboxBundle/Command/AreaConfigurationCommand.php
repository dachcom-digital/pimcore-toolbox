<?php

namespace ToolboxBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableCell;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Helper\TableStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use ToolboxBundle\Manager\ConfigManager;
use ToolboxBundle\ToolboxConfig;

class AreaConfigurationCommand extends ContainerAwareCommand
{

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('toolbox:check-config')
            ->setDescription('Check configuration of a given area element. ')
            ->addOption('area', 'a',
                InputOption::VALUE_REQUIRED,
                'Area Brick Id ("image" for example")')
            ->addOption('context', 'c',
                InputOption::VALUE_OPTIONAL,
                'Context Name');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $brickId = $input->getOption('area');
        $contextId = $input->getOption('context');
        $hasContext = true;

        if (empty($contextId)) {
            $contextId = false;
            $hasContext = false;
        }

        if (empty($brickId)) {
            $output->writeln('<error>Please provide a valid Area Brick Id.</error>');
            return;
        }

        $configManager = $this->getContainer()->get(ConfigManager::class);

        $namespace = ConfigManager::AREABRICK_NAMESPACE_INTERNAL;
        if (!in_array($brickId, ToolboxConfig::TOOLBOX_TYPES)) {
            $namespace = ConfigManager::AREABRICK_NAMESPACE_EXTERNAL;
        }

        $configManager->setAreaNameSpace($namespace);
        $configManager->setContextNameSpace($contextId);

        $brickConfig = $configManager->getAreaConfig($brickId);

        if (empty($brickConfig)) {

            if ($hasContext) {
                $settings = $configManager->getCurrentContextSettings();
                if (in_array($brickId, $settings['disabled_areas'])) {
                    $output->writeln('');
                    $output->writeln(sprintf('<comment>Area Brick with Id "%s" is disabled in "%s" context.</comment>', $brickId, $contextId));
                    $output->writeln('');
                } elseif (!in_array($brickId, $settings['enabled_areas'])) {
                    $output->writeln('');
                    $output->writeln(sprintf('<comment>Area Brick with Id "%s" is not enabled in "%s" context.</comment>', $brickId, $contextId));
                    $output->writeln('');
                }
                return;
            }

            $output->writeln('');
            $output->writeln(sprintf('<error>Area Brick with Id "%s" not found.</error>', $brickId));
            $output->writeln('');
            return;
        }

        $configElements = $brickConfig['config_elements'];
        $configParameter = $brickConfig['config_parameter'];

        if (empty($configElements)) {
            $output->writeln('');
            $output->writeln(sprintf('<comment>Area Brick with Id "%s" does not have any configuration elements.</comment>', $brickId));
            $output->writeln('');
            return;
        }

        $contextHeadline = $hasContext ? ('in Context "' . $contextId . '"') : '';
        $headline = sprintf('Configuration for Area Brick "%s" %s', $brickId, $contextHeadline);
        $output->writeln('');
        $output->writeln('');
        $output->writeln(sprintf('<info>%s</info>', $headline));
        $output->writeln('<info>' . str_repeat('_', strlen($headline)) . '</info>');
        $output->writeln('');
        $output->writeln('');

        $rows = [];
        $c = 0;

        foreach ($configElements as $configName => $configData) {

            $elementConfigData = empty($configData['config']) ? '--' : $this->parseArrayForOutput($configData['config']);
            $conditionParameter = empty($configData['conditions']) ? '--' : $this->parseArrayForOutput($configData['conditions']);

            $rows[] = [
                $configName,
                $configData['type'],
                $configData['title'],
                $configData['description'],
                $conditionParameter,
                $elementConfigData,
            ];

            if (!empty($configParameter) || $c < count($configElements) - 1) {
                $rows[] = new TableSeparator();
            }

            $c++;
        }

        if (!empty($configParameter)) {
            $configParameterData = $this->parseArrayForOutput($configParameter);
            $configRow = [new TableCell("\n" . '<fg=magenta>config parameter for element:</>' . "\n\n" . $configParameterData, ['colspan' => 6])];
            $rows[] = $configRow;
        }

        $table = new Table($output);
        $table
            ->setHeaders(['name', 'type', 'title', 'description', 'conditions', 'config_elements'])
            ->setRows($rows);
        $table->render();


    }

    /**
     * @param array  $array
     * @param string $string
     * @param int    $depth
     * @return string
     */
    function parseArrayForOutput(array $array = [], $string = '', $depth = 0)
    {
        $depthStr = str_repeat(' ', $depth * 3);
        foreach ($array as $key => $value) {
            $dash = $depth === 0 ? '' : '- ';
            $displayValue = !is_array($value) ? ': ' . (is_bool($value) ? ($value ? 'true' : 'false') : (empty($value) ? '--' : $value)) : ':';
            $string .= $depthStr . $dash . $key . $displayValue . "\n";

            if (is_array($value)) {
                $depth++;
                return $this->parseArrayForOutput($value, $string, $depth);
            }
        }

        return $string;
    }
}

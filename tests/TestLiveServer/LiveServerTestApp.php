<?php

namespace WosClient\TestLiveServer;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;

/**
 * Live Server Test App
 *
 * Converts the Symfony Console App into a single-command application, per
 * https://symfony.com/doc/current/components/console/single_command_tool.html
 * 
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 */
class LiveServerTestApp extends Application
{
    /**
     * Gets the name of the command based on input.
     *
     * @param InputInterface $input The input interface
     *
     * @return string The command name
     */
    protected function getCommandName(InputInterface $input)
    {
        // This should return the name of your command.
        return LiveServerTestCommand::COMMAND_NAME;
    }

    /**
     * Gets the default commands that should always be available.
     *
     * @return array An array of default Command instances
     */
    protected function getDefaultCommands()
    {
        // Keep the core default commands to have the HelpCommand
        // which is used when using the --help option
        $defaultCommands = parent::getDefaultCommands();
        $defaultCommands[] = new LiveServerTestCommand();
        return $defaultCommands;
    }

    /**
     * Overridden so that the application doesn't expect the command
     * name to be the first argument.
     */
    public function getDefinition()
    {
        $inputDefinition = parent::getDefinition();
        // clear out the normal first argument, which is the command name
        $inputDefinition->setArguments();

        return $inputDefinition;
    }
}

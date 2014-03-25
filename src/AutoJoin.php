<?php
/*
    This file is part of Erebot.

    Erebot is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    Erebot is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with Erebot.  If not, see <http://www.gnu.org/licenses/>.
*/

namespace Erebot\Module;

/**
 * \brief
 *      A module which automatically joins some pre-configured
 *      IRC channels upon connection.
 */
class AutoJoin extends \Erebot\Module\Base implements \Erebot\Interfaces\HelpEnabled
{
    /**
     * This method is called whenever the module is (re)loaded.
     *
     * \param int $flags
     *      A bitwise OR of the Erebot::Module::Base::RELOAD_*
     *      constants. Your method should take proper actions
     *      depending on the value of those flags.
     *
     * \note
     *      See the documentation on individual RELOAD_*
     *      constants for a list of possible values.
     */
    public function reload($flags)
    {
        if ($this->channel === null) {
            return;
        }

        if ($flags & self::RELOAD_HANDLERS) {
            $handler = new \Erebot\EventHandler(
                new \Erebot\CallableWrapper(array($this, 'handleConnect')),
                new \Erebot\Event\Match\Type(
                    '\\Erebot\\Interfaces\\Event\\Connect'
                )
            );
            $this->connection->addEventHandler($handler);
        }
    }

    public function getHelp(
        \Erebot\Interfaces\Event\Base\TextMessage $event,
        \Erebot\Interfaces\TextWrapper $words
    ) {
        if ($event instanceof \Erebot\Interfaces\Event\Base\PrivateMessage) {
            $target = $event->getSource();
            $chan   = null;
        } else {
            $target = $chan = $event->getChan();
        }

        $fmt        = $this->getFormatter($chan);
        $moduleName = strtolower(get_class());
        $nbArgs     = count($words);

        if ($nbArgs == 1 && $words[0] == $moduleName) {
            $msg = $fmt->_(
                "This module does not provide any command, but ".
                "instructs the bot to join certain channels automatically ".
                "upon connecting to an IRC server."
            );
            $this->sendMessage($target, $msg);
            return true;
        }
    }

    /**
     * Handles a connection to some IRC server.
     * This method takes care of joining the IRC channels
     * it was configured for in the configuration file.
     *
     * \param Erebot::Interfaces::EventHandler $handler
     *      Handler that triggered this event.
     *
     * \param Erebot::Interfaces::Event::Connect $event
     *      Connection event.
     *
     * \return
     *      This method does not return anything.
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function handleConnect(
        \Erebot\Interfaces\EventHandler $handler,
        \Erebot\Interfaces\Event\Connect $event
    ) {
        if ($this->channel === null) {
            return;
        }

        $key = $this->parseString('key', '');
        $this->sendCommand(
            'JOIN '.$this->channel.
            ($key != '' ? ' '.$key : '')
        );
    }
}

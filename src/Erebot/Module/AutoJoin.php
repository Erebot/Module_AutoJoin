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

/**
 * \brief
 *      A module which automatically joins some pre-configured
 *      IRC channels upon connection.
 */
class   Erebot_Module_AutoJoin
extends Erebot_Module_Base
{
    /**
     * This method is called whenever the module is (re)loaded.
     *
     * \param int $flags
     *      A bitwise OR of the Erebot_Module_Base::RELOAD_*
     *      constants. Your method should take proper actions
     *      depending on the value of those flags.
     *
     * \note
     *      See the documentation on individual RELOAD_*
     *      constants for a list of possible values.
     */
    public function _reload($flags)
    {
        if ($this->_channel === NULL)
            return;

        if ($flags & self::RELOAD_HANDLERS) {
            $handler = new Erebot_EventHandler(
                new Erebot_Callable(array($this, 'handleConnect')),
                new Erebot_Event_Match_InstanceOf(
                    'Erebot_Interface_Event_Connect'
                )
            );
            $this->_connection->addEventHandler($handler);

            $cls = $this->getFactory('!Callable');
            $this->registerHelpMethod(new $cls(array($this, 'getHelp')));
        }
    }

    /// \copydoc Erebot_Module_Base::_unload()
    protected function _unload()
    {
    }

    /**
     * Provides help about this module.
     *
     * \param Erebot_Interface_Event_Base_TextMessage $event
     *      Some help request.
     *
     * \param Erebot_Interface_TextWrapper $words
     *      Parameters passed with the request. This is the same
     *      as this module's name when help is requested on the
     *      module itself (in opposition with help on a specific
     *      command provided by the module).
     */
    public function getHelp(
        Erebot_Interface_Event_Base_TextMessage $event,
        Erebot_Interface_TextWrapper            $words
    )
    {
        if ($event instanceof Erebot_Interface_Event_Base_Private) {
            $target = $event->getSource();
            $chan   = NULL;
        }
        else
            $target = $chan = $event->getChan();

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
            return TRUE;
        }
    }

    /**
     * Handles a connection to some IRC server.
     * This method takes care of joining the IRC channels
     * it was configured for in the configuration file.
     *
     * \param Erebot_Interface_EventHandler $handler
     *      Handler that triggered this event.
     *
     * \param Erebot_Interface_Event_Event_Connect $event
     *      Connection event.
     *
     * \return
     *      This method does not return anything.
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function handleConnect(
        Erebot_Interface_EventHandler   $handler,
        Erebot_Interface_Event_Connect  $event
    )
    {
        if ($this->_channel === NULL)
            return;

        $key = $this->parseString('key', '');
        $this->sendCommand(
            'JOIN '.$this->_channel.
            ($key != '' ? ' '.$key : '')
        );
    }
}


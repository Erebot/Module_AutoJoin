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
    /// \copydoc Erebot_Module_Base::_reload()
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
        }
    }

    /// \copydoc Erebot_Module_Base::_unload()
    protected function _unload()
    {
    }

    /**
     * Handles a connection to some IRC server.
     * This method takes care of joining the IRC channels
     * it was configured for in the configuration file.
     *
     * \param Erebot_Interface_Event_Event_Connect $event
     *      Connection event.
     *
     * \return
     *      This method does not return anything.
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


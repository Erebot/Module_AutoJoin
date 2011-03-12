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

require_once(
    dirname(__FILE__) .
    DIRECTORY_SEPARATOR . 'testenv' .
    DIRECTORY_SEPARATOR . 'bootstrap.php'
);

class   AutoJoinTest
extends ErebotModuleTestCase
{
    public function testAutoJoin()
    {
        $this->_module = new Erebot_Module_AutoJoin('#foo');
        $this->_module->reload(
            $this->_connection,
            Erebot_Module_Base::RELOAD_ALL
        );
        $event = new Erebot_Event_Connect($this->_connection);
        $this->_module->handleConnect($event);
        $this->assertSame(1, count($this->_outputBuffer));
        $this->assertSame("JOIN #foo", $this->_outputBuffer[0]);
        $this->_module->unload();
    }

    public function testAutoJoinWithoutAnyChannel()
    {
        $this->_module = new Erebot_Module_AutoJoin(NULL);
        $this->_module->reload(
            $this->_connection,
            Erebot_Module_Base::RELOAD_ALL
        );
        $event = new Erebot_Event_Connect($this->_connection);
        $this->_module->handleConnect($event);
        $this->assertSame(0, count($this->_outputBuffer));
        $this->_module->unload();
    }

    public function testPasswordedAutoJoin()
    {
        // Make it look as though the channel
        // uses "password" as its key.
        $this->_serverConfig
            ->expects($this->any())
            ->method('parseString')
            ->will($this->returnValue('password'));

        // Now, go through the same sequence
        // but look for a different outcome.
        $this->_module = new Erebot_Module_AutoJoin('#foo');
        $this->_module->reload(
            $this->_connection,
            Erebot_Module_Base::RELOAD_ALL
        );
        $event = new Erebot_Event_Connect($this->_connection);
        $this->_module->handleConnect($event);
        $this->assertSame(1, count($this->_outputBuffer));
        $this->assertSame("JOIN #foo password", $this->_outputBuffer[0]);
        $this->_module->unload();
    }
}


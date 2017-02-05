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

if (!class_exists('PHPUnit_Framework_TestCase')) {
    class_alias('\\PHPUnit\\Framework\\TestCase', 'PHPUnit_Framework_TestCase');
}

class   AutoJoinTest
extends Erebot_Testenv_Module_TestCase
{
    public function _getConnectMock()
    {
        $event = $this->getMockBuilder('\\Erebot\\Interfaces\\Event\\Connect')->getMock();
        $event
            ->expects($this->any())
            ->method('getConnection')
            ->will($this->returnValue($this->_connection));
        return $event;
    }

    public function testAutoJoin()
    {
        $this->_module = new \Erebot\Module\AutoJoin('#foo');
        $this->_injectStubs();
        $this->_module->reloadModule($this->_connection, 0);
        $this->_module->handleConnect(
            $this->_eventHandler,
            $this->_getConnectMock()
        );
        $this->assertSame(1, count($this->_outputBuffer));
        $this->assertSame("JOIN #foo", $this->_outputBuffer[0]);
        $this->_module->unloadModule();
    }

    public function testAutoJoinWithoutAnyChannel()
    {
        $this->_module = new \Erebot\Module\AutoJoin(null);
        $this->_injectStubs();
        $this->_module->reloadModule($this->_connection, 0);
        $this->_module->handleConnect(
            $this->_eventHandler,
            $this->_getConnectMock()
        );
        $this->assertSame(0, count($this->_outputBuffer));
        $this->_module->unloadModule();
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
        $this->_module = new \Erebot\Module\AutoJoin('#foo');
        $this->_injectStubs();
        $this->_module->reloadModule($this->_connection, 0);
        $this->_module->handleConnect(
            $this->_eventHandler,
            $this->_getConnectMock()
        );
        $this->assertSame(1, count($this->_outputBuffer));
        $this->assertSame("JOIN #foo password", $this->_outputBuffer[0]);
        $this->_module->unloadModule();
    }
}

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

class FakeHelper
{
    public function realRegisterHelpMethod(
        Erebot_Module_Base          $module,
        Erebot_Interface_Callable   $callable
    )
    {
    }
}

class   AutoJoinTest
extends Erebot_Testenv_Module_TestCase
{
    protected function _setConnectionExpectations()
    {
        parent::_setConnectionExpectations();
        $this->_connection
            ->expects($this->any())
            ->method('getModule')
            ->will($this->returnValue(new FakeHelper()));
    }

    public function _getConnectMock()
    {
        $event = $this->getMock(
            'Erebot_Interface_Event_Connect',
            array(), array(), '', FALSE, FALSE
        );

        $event
            ->expects($this->any())
            ->method('getConnection')
            ->will($this->returnValue($this->_connection));
        return $event;
    }

    public function testAutoJoin()
    {
        $this->_module = new Erebot_Module_AutoJoin('#foo');
        $this->_module->setFactory('!Callable', $this->_factory['!Callable']);
        $this->_module->reload($this->_connection, 0);
        $this->_module->handleConnect(
            $this->_eventHandler,
            $this->_getConnectMock()
        );
        $this->assertSame(1, count($this->_outputBuffer));
        $this->assertSame("JOIN #foo", $this->_outputBuffer[0]);
        $this->_module->unload();
    }

    public function testAutoJoinWithoutAnyChannel()
    {
        $this->_module = new Erebot_Module_AutoJoin(NULL);
        $this->_module->setFactory('!Callable', $this->_factory['!Callable']);
        $this->_module->reload($this->_connection, 0);
        $this->_module->handleConnect(
            $this->_eventHandler,
            $this->_getConnectMock()
        );
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
        $this->_module->setFactory('!Callable', $this->_factory['!Callable']);
        $this->_module->reload($this->_connection, 0);
        $this->_module->handleConnect(
            $this->_eventHandler,
            $this->_getConnectMock()
        );
        $this->assertSame(1, count($this->_outputBuffer));
        $this->assertSame("JOIN #foo password", $this->_outputBuffer[0]);
        $this->_module->unload();
    }
}


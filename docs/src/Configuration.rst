Configuration
=============

Options
-------

This module offers no configuration options.


Example
-------

..  parsed-code:: xml

    <?xml version="1.0" ?>
    <configuration
      xmlns="http://localhost/Erebot/"
      version="..."
      language="fr-FR"
      timezone="Europe/Paris"
      commands-prefix="!">

      <networks>
        <network name="localhost">
          <servers>
            <server url="irc://localhost:6667/" />
          </servers>

          <!--
            After it successfully connects to the IRC server,
            the bot will automatically join the #Erebot channel.
          -->
          <channel name="#Erebot">
            <modules>
              <module name="Erebot_Module_AutoJoin" />
            </modules>
          </channel>
        </network>
      </networks>
    </configuration>

.. vim: ts=4 et

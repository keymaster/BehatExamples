Behat Gherkin Parser
====================

This is the new Gherkin parser for Behat. It comes bundled with more than 40 native languages (see i18n) support & much cleaner architecture than previous one.

Gherkin parser from now on will be separate project from Behat core itself and has no foreign dependencies, which means, that you can use it in your DSL-specific projects.

Usage
-----

    $keywords = new Behat\Gherkin\Keywords\ArrayKeywords(array(
        'en' => array(
            'Feature'           => 'Feature',
            'Background'        => 'Background',
            'Scenario'          => 'Scenario',
            'Scenario Outline'  => 'Scenario Outline',
            'Examples'          => 'Examples',
            'Step Types'        => 'Given|When|Then|And|But'
        ),
        'ru' => array(
            'Feature'           => 'Функционал',
            'Background'        => 'Предыстория',
            'Scenario'          => 'Сценарий',
            'Scenario Outline'  => 'Структура сценария',
            'Examples'          => 'Значения',
            'Step Types'        => 'Допустим|То|Если|И|Но'
        )
    ));
    $lexer  = new Behat\Gherkin\Lexer($keywords);
    $parser = new Behat\Gherkin\Parser($lexer);
    
    $arrayOfFeatures = $parser->parse('/path/to/file.feature OR feature itself');


Note on Patches/Pull Requests
-----------------------------

* Fork the project `develop` branch (all new development happens here, master for releases & hotfixes only).
* Make your feature addition or bug fix.
* Add unit tests for it (look at tests/Behat/Gherkin for examples).
  This is important so I don't break it in a future version unintentionally.
* Commit
* Send me a pull request.

Running tests
-------------

	phpunit

If you get errors about missing dependencies - just run

	git submodule update --init

Gherkin Parser itself has no required dependencies, but test suite has.

Copyright
---------

Copyright (c) 2010 Konstantin Kudryashov (ever.zet). See LICENSE for details.

Contributors
------------

* Konstantin Kudryashov [everzet](http://github.com/everzet) [lead developer]

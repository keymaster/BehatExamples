<?php

namespace Behat\Behat\Exception;

/*
 * This file is part of the Behat.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Ambiguous exception.
 *
 * @author      Konstantin Kudryashov <ever.zet@gmail.com>
 */
class Ambiguous extends BehaviorException
{
    /**
     * Step description.
     *
     * @var     string
     */
    protected $text;
    /**
     * Matched definitions.
     *
     * @var     array
     */
    protected $matches = array();

    /**
     * Initializes abmiguous exception.
     *
     * @param   string  $text       step description
     * @param   array   $matches    ambigious matches (array of Definition's)
     */
    public function __construct($text, array $matches)
    {
        parent::__construct();

        $this->text     = $text;
        $this->matches  = $matches;

        $this->message = sprintf("Ambiguous match of \"%s\":", $this->text);
        foreach ($this->matches as $definition){
            $this->message .= sprintf("\n%s:%d:in `%s`",
                $definition->getFile(), $definition->getLine(), $definition->getRegex()
            );
        }
    }
}

<?php

/*
 * (c) Jean-François Lépine <https://twitter.com/Halleck45>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hal\Component\OOP\Reflected;


/**
 * Result (method)
 *
 * @author Jean-François Lépine <https://twitter.com/Halleck45>
 */
class ReflectedMethod {

    /**
     * @var string
     */
    private $name;

    /**
     * @var array
     */
    private $arguments = array();

    /**
     * @var array
     */
    private $tokens = array();

    /**
     * @var string
     */
    private $content;

    /**
     * @param string $name
     */
    public function __construct($name)
    {
        $this->name = (string) $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return \SplObjectStorage
     */
    public function getArguments()
    {
        return $this->arguments;
    }

    /**
     * Attach argument
     *
     * @param ReflectedArgument $arg
     * @return $this
     */
    public function pushArgument(ReflectedArgument $arg) {
        array_push($this->arguments, $arg);
        return $this;
    }

    /**
     * @param $content
     * @return $this
     */
    public function setContent($content)
    {
        $this->content = $content;
        return $this;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param $tokens
     * @return $this
     */
    public function setTokens($tokens)
    {
        $this->tokens = $tokens;
        return $this;
    }

    /**
     * @return array
     */
    public function getTokens()
    {
        return $this->tokens;
    }
};
<?php

namespace Mygento\Jeeves\IO;

class NullIO extends BaseIO
{
    /**
     * {@inheritdoc}
     */
    public function isInteractive()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function isVerbose()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function isVeryVerbose()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function isDebug()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function isDecorated()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function write($messages, $newline = true, $verbosity = self::NORMAL)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function writeError($messages, $newline = true, $verbosity = self::NORMAL)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function overwrite($messages, $newline = true, $size = 80, $verbosity = self::NORMAL)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function overwriteError($messages, $newline = true, $size = 80, $verbosity = self::NORMAL)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function ask($question, $default = null)
    {
        return $default;
    }

    /**
     * {@inheritdoc}
     */
    public function askConfirmation($question, $default = true)
    {
        return $default;
    }

    /**
     * {@inheritdoc}
     */
    public function askAndValidate($question, $validator, $attempts = false, $default = null)
    {
        return $default;
    }

    /**
     * {@inheritdoc}
     */
    public function askAndHideAnswer($question)
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function select($question, $choices, $default, $attempts = false, $errorMessage = 'Value "%s" is invalid', $multiselect = false)
    {
        return $default;
    }
}

<?php
namespace Mygento\Jeeves\IO;

class NullIO extends BaseIO
{
    /**
    * {@inheritDoc}
    */
    public function isInteractive()
    {
      return false;
    }
    /**
    * {@inheritDoc}
    */
    public function isVerbose()
    {
      return false;
    }
    /**
    * {@inheritDoc}
    */
    public function isVeryVerbose()
    {
      return false;
    }
    /**
    * {@inheritDoc}
    */
    public function isDebug()
    {
      return false;
    }
    /**
    * {@inheritDoc}
    */
    public function isDecorated()
    {
      return false;
    }
    /**
    * {@inheritDoc}
    */
    public function write($messages, $newline = true, $verbosity = self::NORMAL)
    {
    }
    /**
    * {@inheritDoc}
    */
    public function writeError($messages, $newline = true, $verbosity = self::NORMAL)
    {
    }
    /**
    * {@inheritDoc}
    */
    public function overwrite($messages, $newline = true, $size = 80, $verbosity = self::NORMAL)
    {
    }
    /**
    * {@inheritDoc}
    */
    public function overwriteError($messages, $newline = true, $size = 80, $verbosity = self::NORMAL)
    {
    }
    /**
    * {@inheritDoc}
    */
    public function ask($question, $default = null)
    {
      return $default;
    }
    /**
    * {@inheritDoc}
    */
    public function askConfirmation($question, $default = true)
    {
      return $default;
    }
    /**
    * {@inheritDoc}
    */
    public function askAndValidate($question, $validator, $attempts = false, $default = null)
    {
      return $default;
    }
    /**
    * {@inheritDoc}
    */
    public function askAndHideAnswer($question)
    {
      return null;
    }
    /**
    * {@inheritDoc}
    */
    public function select($question, $choices, $default, $attempts = false, $errorMessage = 'Value "%s" is invalid', $multiselect = false)
    {
      return $default;
    }
}

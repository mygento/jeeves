<?php
namespace Mygento\Jeeves\IO;

use Psr\Log\LoggerInterface;

abstract class BaseIO implements IOInterface, LoggerInterface
{
    /**
     * System is unusable.
     *
     * @param  string $message
     * @param  array  $context
     * @return null
     */
    public function emergency($message, array $context = array())
    {
        return $this->log(LogLevel::EMERGENCY, $message, $context);
    }

    /**
    * Action must be taken immediately.
    *
    * Example: Entire website down, database unavailable, etc. This should
    * trigger the SMS alerts and wake you up.
    *
    * @param  string $message
    * @param  array  $context
    * @return null
    */
    public function alert($message, array $context = array())
    {
      return $this->log(LogLevel::ALERT, $message, $context);
    }
    /**
    * Critical conditions.
    *
    * Example: Application component unavailable, unexpected exception.
    *
    * @param  string $message
    * @param  array  $context
    * @return null
    */
    public function critical($message, array $context = array())
    {
      return $this->log(LogLevel::CRITICAL, $message, $context);
    }
    /**
    * Runtime errors that do not require immediate action but should typically
    * be logged and monitored.
    *
    * @param  string $message
    * @param  array  $context
    * @return null
    */
    public function error($message, array $context = array())
    {
      return $this->log(LogLevel::ERROR, $message, $context);
    }
    /**
    * Exceptional occurrences that are not errors.
    *
    * Example: Use of deprecated APIs, poor use of an API, undesirable things
    * that are not necessarily wrong.
    *
    * @param  string $message
    * @param  array  $context
    * @return null
    */
    public function warning($message, array $context = array())
    {
      return $this->log(LogLevel::WARNING, $message, $context);
    }
    /**
    * Normal but significant events.
    *
    * @param  string $message
    * @param  array  $context
    * @return null
    */
    public function notice($message, array $context = array())
    {
      return $this->log(LogLevel::NOTICE, $message, $context);
    }
    /**
    * Interesting events.
    *
    * Example: User logs in, SQL logs.
    *
    * @param  string $message
    * @param  array  $context
    * @return null
    */
    public function info($message, array $context = array())
    {
      return $this->log(LogLevel::INFO, $message, $context);
    }
    /**
    * Detailed debug information.
    *
    * @param  string $message
    * @param  array  $context
    * @return null
    */
    public function debug($message, array $context = array())
    {
      return $this->log(LogLevel::DEBUG, $message, $context);
    }
    /**
    * Logs with an arbitrary level.
    *
    * @param  mixed  $level
    * @param  string $message
    * @param  array  $context
    * @return null
    */
    public function log($level, $message, array $context = array())
    {
      if (in_array($level, array(LogLevel::EMERGENCY, LogLevel::ALERT, LogLevel::CRITICAL, LogLevel::ERROR))) {
          $this->writeError('<error>'.$message.'</error>', true, self::NORMAL);
      } elseif ($level === LogLevel::WARNING) {
          $this->writeError('<warning>'.$message.'</warning>', true, self::NORMAL);
      } elseif ($level === LogLevel::NOTICE) {
          $this->writeError('<info>'.$message.'</info>', true, self::VERBOSE);
      } elseif ($level === LogLevel::INFO) {
          $this->writeError('<info>'.$message.'</info>', true, self::VERY_VERBOSE);
      } else {
          $this->writeError($message, true, self::DEBUG);
      }
    }
}

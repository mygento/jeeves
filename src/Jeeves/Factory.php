<?php

namespace Mygento\Jeeves;

use Symfony\Component\Console\Formatter\OutputFormatter;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Output\ConsoleOutput;

class Factory
{
    public static function createAdditionalStyles()
    {
        return [
            'highlight' => new OutputFormatterStyle('red'),
            'warning' => new OutputFormatterStyle('black', 'yellow'),
        ];
    }

    /**
     * Creates a ConsoleOutput instance.
     *
     * @return ConsoleOutput
     */
    public static function createOutput()
    {
        $styles = self::createAdditionalStyles();
        $formatter = new OutputFormatter(true, $styles);

        return new ConsoleOutput(ConsoleOutput::VERBOSITY_NORMAL, null, $formatter);
    }
}

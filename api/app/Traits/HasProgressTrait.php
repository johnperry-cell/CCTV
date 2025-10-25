<?php

namespace App\Traits;

use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\ConsoleOutput;

trait HasProgressTrait {
    protected static $progressBar = null;
    protected static $progressOptions = [
        "width" => 28,
        "characters" => [
            "empty" => "□",
            "done" => "<fg=green>■</fg=green>",
            "current" => "▣",
        ],
        "format" => "   %current%/%max% <fg=gray>%message%</fg=gray>\n\r   [%bar%] %percent:3s%%\n\r",
    ];

    private static function getProgressBar(int|string $title, $max = null) {
        $output = new ConsoleOutput();
        $t = "\n\r";
        $m = 0;

        if (gettype($title) == "string") {
            $t = "\n\r   <info>$title</info>";
            $m = $max ?? throw new \Exception("Max value not set!");
        } elseif (gettype($title) == "integer") {
            $m = $title;
        }

        $output->writeln($t);
        ProgressBar::setFormatDefinition("custom", self::$progressOptions["format"]);
        self::$progressBar = new ProgressBar($output, $m);
        self::$progressBar->setBarCharacter(self::$progressOptions["characters"]["done"]); // the finished part of the bar
        self::$progressBar->setEmptyBarCharacter(self::$progressOptions["characters"]["empty"]); // the unfinished part of the bar
        self::$progressBar->setProgressCharacter(self::$progressOptions["characters"]["current"]); // the progress character
        self::$progressBar->setBarWidth(self::$progressOptions["width"]); // the bar width
        self::$progressBar->setFormat("custom"); // the format of the progress bar
        self::$progressBar->setMessage("");
        return self::$progressBar;
    }
}

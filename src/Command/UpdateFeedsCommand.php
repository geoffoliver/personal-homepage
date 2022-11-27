<?php
declare(strict_types=1);

namespace App\Command;

use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;

/**
 * UpdateFeeds command.
 */
class UpdateFeedsCommand extends Command
{
    /**
     * Hook method for defining this command's option parser.
     *
     * @see https://book.cakephp.org/4/en/console-commands/commands.html#defining-arguments-and-options
     * @param \Cake\Console\ConsoleOptionParser $parser The parser to be defined
     * @return \Cake\Console\ConsoleOptionParser The built parser.
     */
    public function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
    {
        $parser = parent::buildOptionParser($parser);

        return $parser;
    }

    /**
     * Implement this method with your command's logic.
     *
     * @param \Cake\Console\Arguments $args The command arguments.
     * @param \Cake\Console\ConsoleIo $io The console io
     * @return null|void|int The exit code or null for success
     */
    public function execute(Arguments $args, ConsoleIo $io)
    {
        // load up the followings model
        $this->Followings = $this->fetchTable('Followings');
        $this->FeedItems = $this->fetchTable('FeedItems');

        // get all our followings
        $followings = $this->Followings->find()
            ->all();

        $start = microtime(true);

        $ts = date('Y-m-d H:i:s');
        $io->out("Syncing feeds at {$ts}");

        foreach ($followings as $following) {
            $io->out("Syncing feed for {$following->name}...");
            $s = microtime(true);

            $following->syncFeed();

            $e = microtime(true);
            $ms = $e - $s;
            $io->out("Took {$ms} seconds");
        }

        $now = microtime(true);

        $ms = $now - $start;
        $io->out("Synced all feeds in {$ms} seconds");

        $this->FeedItems->deleteAll([
            'created <=' => date('Y-m-d', strtotime('-30 days')),
        ]);

        $now = microtime(true);

        $ms = $now - $start;
        $io->out("Finished in {$ms} seconds");
    }
}

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
        // load up the friends model
        $this->Friends = $this->fetchTable('Friends');
        $this->FeedItems = $this->fetchTable('FeedItems');

        // get all our friends
        $friends = $this->Friends->find()->all();

        foreach ($friends as $friend) {
            $io->out("Syncing feed for {$friend->name}");
            $friend->syncFeed();
        }

        $this->FeedItems->deleteAll([
            'created <=' => date('Y-m-d', strtotime('-30 days')),
        ]);
    }
}

<?php

namespace LeeOvery\WordpressToLaravel\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Events\Dispatcher;
use LeeOvery\WordpressToLaravel\PostImported;
use LeeOvery\WordpressToLaravel\PostUpdated;
use LeeOvery\WordpressToLaravel\WordpressToLaravel;

class Importer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wordpress-to-laravel:import
                                {page? : The page number from WP to import.}
                                {per-page? : The number of posts per page to fetch.}
                                {--F|force-all : This option will grab every published post from the WP DB and sync them all (along with the other embedded bits) to your local DB}
                                {--T|truncate : This option will truncate your local posts/category/tags tables prior to the import action. It uses the models setup in config.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import Wordpress posts from WP DB to local DB, via WP API plugin.';

    /**
     * @var WordpressToLaravel
     */
    private $wordpressToLaravel;

    /**
     * @var Dispatcher
     */
    private $dispatcher;

    /**
     * @var int
     */
    private $importedPostCount = 0;

    /**
     * @var int
     */
    private $updatedPostCount = 0;

    /**
     * Create a new command instance.
     *
     * @param WordpressToLaravel $wordpressToLaravel
     */
    public function __construct(WordpressToLaravel $wordpressToLaravel, Dispatcher $dispatcher)
    {
        parent::__construct();
        $this->wordpressToLaravel = $wordpressToLaravel;
        $this->dispatcher = $dispatcher;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $page = $this->argument('page') ?: 1;
        $perPage = $this->argument('per-page') ?: 5;
        $truncate = $this->option('truncate') ?: false;
        $forceAll = $this->option('force-all') ?: false;

        $this->checkIfTruncatingWithoutForceAll($truncate, $forceAll);

        $this->registerListeners();

        $this->wordpressToLaravel->import(
            $page, $perPage, $truncate, $forceAll
        );

        $this->outputCounts();
    }

    protected function checkIfTruncatingWithoutForceAll($truncate, $forceAll)
    {
        if ($truncate && ! $forceAll && ! $this->confirm("Oops! Looks like you've passed the 'truncate' option (-T) but haven't passed the 'force-all' option (-F). This means we'll remove all posts etc from the local DB, and re-sync with just the first page of post results. Was this intentional?")) {

            $this->info("In that case we'll bail. Try again with the correct options.");
            exit();
        }
    }

    private function registerListeners()
    {
        $this->dispatcher->listen(PostImported::class, function (PostImported $event) {
            $this->importedPostCount++;
        });

        $this->dispatcher->listen(PostUpdated::class, function (PostUpdated $event) {
            $this->updatedPostCount++;
        });
    }

    private function outputCounts()
    {
        $this->info($this->importedPostCount . ' ' . str_plural('Post', $this->importedPostCount) . ' Imported.');
        $this->info($this->updatedPostCount . ' ' . str_plural('Post', $this->updatedPostCount) . ' Updated.');
    }
}
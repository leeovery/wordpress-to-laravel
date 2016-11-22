<?php

namespace LeeOvery\WordpressToLaravel\Commands;

use Illuminate\Console\Command;
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
                                        {--F|force-all : This option will grab every post from the WP DB and sync each one to your local posts table}
                                        {--T|truncate : This option will truncate your local posts table prior to the import action. It uses the Post model setup in config.}';

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
     * Create a new command instance.
     *
     * @param WordpressToLaravel $wordpressToLaravel
     */
    public function __construct(WordpressToLaravel $wordpressToLaravel)
    {
        parent::__construct();
        $this->wordpressToLaravel = $wordpressToLaravel;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $page = $this->argument('page') ?: 1;
        $perPage = $this->argument('per-page') ?: 5;
        $truncate = $this->option('truncate') ?: false;
        $this->wordpressToLaravel->import($page, $perPage, $truncate);
    }
}
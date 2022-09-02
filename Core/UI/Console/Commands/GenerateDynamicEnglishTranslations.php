<?php

namespace App\Console\Commands;

use App\Core\Application\Localization\GenerateDynamicEnglishTranslations\GenerateDynamicEnglishTranslationsHandler;
use Illuminate\Console\Command;

class GenerateDynamicEnglishTranslations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'localization:dynamic:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate and map english texts on translation API';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @param GenerateDynamicEnglishTranslationsHandler $generateDynamicEnglishTranslationsHandler
     *
     * @return void
     */
    public function handle(GenerateDynamicEnglishTranslationsHandler $generateDynamicEnglishTranslationsHandler)
    {
        $generateDynamicEnglishTranslationsHandler->handle();
    }
}

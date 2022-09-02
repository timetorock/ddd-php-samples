<?php

namespace App\Console\Commands;

use App\Core\Application\Localization\UpdateEnglishTexts\UpdateEnglishTextsHandler;
use Illuminate\Console\Command;

class UpdateEnglishTextsLocalization extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'localization:dynamic:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update english texts on translation API';

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
     * @param UpdateEnglishTextsHandler $updateEnglishTextsHandler
     */
    public function handle(UpdateEnglishTextsHandler $updateEnglishTextsHandler)
    {
        $updateEnglishTextsHandler->handle();
    }
}

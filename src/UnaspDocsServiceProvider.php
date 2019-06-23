<?php

namespace unaspbr;

use Config;
use Illuminate\Support\ServiceProvider;

/**
 * IuguServiceProvider ─ The provider. Woah!
 *
 * @author Mateus Felipe <mateusfccp@gmail.com>
 * @package UnaspDocsLaravel
 * @version 0.1
 */
class UnaspDocsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return  void
     */
    public function boot()
    {           
        // Publish configuration assets
        $this->publishes([
            __DIR__ . '/config/unasp_docs.php' => config_path('unasp_docs.php'),
        ]);
    }
}

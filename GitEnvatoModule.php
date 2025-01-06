<?php
namespace Modules\GitEnvato;

use Illuminate\Support\Facades\Event;
use App\Services\Module;

class GitEnvatoModule extends Module
{
    public function __construct()
    {
        parent::__construct( __FILE__ );
    }
}
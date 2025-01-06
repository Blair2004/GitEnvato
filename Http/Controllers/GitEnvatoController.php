<?php

/**
 * Github To Envato Controller
 * @since 1.0
 * @package modules/GitEnvato
**/

namespace Modules\GitEnvato\Http\Controllers;
use Illuminate\Support\Facades\View;
use App\Http\Controllers\Controller;
use Modules\GitEnvato\Crud\RepositoryCrud;
use Modules\GitEnvato\Models\Repository;

class GitEnvatoController extends Controller
{
    public function listRepositories()
    {
        return RepositoryCrud::table();
    }

    public function showCreateForm()
    {
        return RepositoryCrud::form();
    }

    public function showEditForm( Repository $repository )
    {
        return RepositoryCrud::form( $repository );
    }
}

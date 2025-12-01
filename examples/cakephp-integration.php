<?php

/**
 * CakePHP 5.x Integration Example
 */

// Create a Component (src/Controller/Component/VersioningComponent.php):
/*
<?php
declare(strict_types=1);

namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Cache\Cache;
use Williamug\Versioning\UniversalVersioning;

class VersioningComponent extends Component
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        // Configure UniversalVersioning
        UniversalVersioning::setRepositoryPath(ROOT);
        UniversalVersioning::setCacheAdapter(Cache::pool('default'));
        UniversalVersioning::setFallbackVersion(env('APP_VERSION', 'dev'));
        UniversalVersioning::setCacheTtl(3600);
    }

    public function getVersion(): string
    {
        return UniversalVersioning::tag();
    }

    public function getFull(): string
    {
        return UniversalVersioning::full();
    }

    public function getCommit(): string
    {
        return UniversalVersioning::commit();
    }

    public function clearCache(): void
    {
        UniversalVersioning::clearCache();
    }
}
*/

// Use in Controller (src/Controller/AppController.php):
/*
<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\Controller\Controller;

class AppController extends Controller
{
    public function initialize(): void
    {
        parent::initialize();

        $this->loadComponent('Versioning');
    }

    public function beforeRender(\Cake\Event\EventInterface $event)
    {
        parent::beforeRender($event);

        // Make version available to all views
        $this->set('appVersion', $this->Versioning->getVersion());
        $this->set('appCommit', $this->Versioning->getCommit());
    }
}
*/

// Create a Helper (src/View/Helper/VersionHelper.php):
/*
<?php
declare(strict_types=1);

namespace App\View\Helper;

use Cake\View\Helper;
use Williamug\Versioning\UniversalVersioning;

class VersionHelper extends Helper
{
    public function tag(): string
    {
        return UniversalVersioning::tag();
    }

    public function full(): string
    {
        return UniversalVersioning::full();
    }

    public function commit(): string
    {
        return UniversalVersioning::commit();
    }
}
*/

// Use in Views (templates/layout/default.php):
/*
<footer>
    <p>Version: <?= $this->Version->tag() ?></p>
    <p>Build: <?= $this->Version->commit() ?></p>
</footer>
*/

// API Endpoint (src/Controller/ApiController.php):
/*
<?php
declare(strict_types=1);

namespace App\Controller;

class ApiController extends AppController
{
    public function version()
    {
        $this->viewBuilder()->setOption('serialize', ['version']);

        $version = [
            'tag' => $this->Versioning->getVersion(),
            'full' => $this->Versioning->getFull(),
            'commit' => $this->Versioning->getCommit(),
        ];

        $this->set('version', $version);
    }
}
*/

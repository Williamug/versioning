<?php

/**
 * CodeIgniter 4 Integration Example
 */

namespace App\Libraries;

use Config\Cache;
use Williamug\Versioning\UniversalVersioning;

class Versioning
{
  protected $cache;

  public function __construct()
  {
    // Initialize cache
    $this->cache = \Config\Services::cache();

    // Configure UniversalVersioning
    UniversalVersioning::setRepositoryPath(ROOTPATH);
    UniversalVersioning::setCacheAdapter($this->cache);
    UniversalVersioning::setFallbackVersion(env('app.version', 'dev'));
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

// In your controller (app/Controllers/Home.php):
/*
<?php

namespace App\Controllers;

use App\Libraries\Versioning;

class Home extends BaseController
{
    public function index()
    {
        $versioning = new Versioning();

        $data = [
            'version' => $versioning->getVersion(),
            'commit' => $versioning->getCommit(),
        ];

        return view('welcome_message', $data);
    }

    public function apiVersion()
    {
        $versioning = new Versioning();

        return $this->response->setJSON([
            'version' => $versioning->getVersion(),
            'full' => $versioning->getFull(),
            'commit' => $versioning->getCommit(),
        ]);
    }
}
*/

// In your views (app/Views/welcome_message.php):
/*
<footer>
    <p>Version: <?= esc($version) ?></p>
    <p>Build: <?= esc($commit) ?></p>
</footer>
*/

// Create a helper (app/Helpers/version_helper.php):
/*
<?php

if (!function_exists('get_app_version')) {
    function get_app_version(string $format = 'tag'): string
    {
        $versioning = new \App\Libraries\Versioning();

        return match ($format) {
            'tag' => $versioning->getVersion(),
            'full' => $versioning->getFull(),
            'commit' => $versioning->getCommit(),
            default => $versioning->getVersion(),
        };
    }
}
*/

// Load the helper in BaseController or autoload:
/*
// In app/Config/Autoload.php
public $helpers = ['version'];

// Then use anywhere:
echo get_app_version(); // v1.0.0
*/

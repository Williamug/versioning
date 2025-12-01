<?php

/**
 * Symfony Framework Integration Example
 */

// In your Symfony service configuration (config/services.yaml):
/*
services:
    App\Service\VersioningService:
        arguments:
            $cache: '@cache.app'
            $projectDir: '%kernel.project_dir%'
*/

namespace App\Service;

use Symfony\Contracts\Cache\CacheInterface;
use Williamug\Versioning\UniversalVersioning;

class VersioningService
{
  public function __construct(
    private CacheInterface $cache,
    private string $projectDir
  ) {
    // Configure the versioning system
    UniversalVersioning::setRepositoryPath($this->projectDir);
    UniversalVersioning::setCacheAdapter($this->cache);
    UniversalVersioning::setFallbackVersion($_ENV['APP_VERSION'] ?? 'dev');
    UniversalVersioning::setCacheTtl(3600);
  }

  public function getVersion(): string
  {
    return UniversalVersioning::tag();
  }

  public function getFullVersion(): string
  {
    return UniversalVersioning::full();
  }

  public function getCommit(): string
  {
    return UniversalVersioning::commit();
  }
}

// In your controller:
/*
namespace App\Controller;

use App\Service\VersioningService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    #[Route('/')]
    public function index(VersioningService $versioning): Response
    {
        return $this->render('index.html.twig', [
            'version' => $versioning->getVersion(),
            'commit' => $versioning->getCommit(),
        ]);
    }

    #[Route('/api/version')]
    public function version(VersioningService $versioning): Response
    {
        return $this->json([
            'version' => $versioning->getVersion(),
            'full' => $versioning->getFullVersion(),
            'commit' => $versioning->getCommit(),
        ]);
    }
}
*/

// In your Twig templates:
/*
{# templates/base.html.twig #}
<footer>
    <p>Version: {{ versioning.version }}</p>
</footer>
*/

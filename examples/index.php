<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Application</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      max-width: 800px;
      margin: 50px auto;
      padding: 20px;
      background: #f5f5f5;
    }

    .container {
      background: white;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .version-info {
      background: #f8f9fa;
      padding: 15px;
      border-radius: 5px;
      margin: 20px 0;
      border-left: 4px solid #007bff;
    }

    .version-info code {
      background: #e9ecef;
      padding: 2px 6px;
      border-radius: 3px;
      font-family: monospace;
    }

    footer {
      margin-top: 40px;
      padding-top: 20px;
      border-top: 1px solid #dee2e6;
      color: #6c757d;
      font-size: 0.9em;
    }
  </style>
</head>

<body>
  <?php
  require __DIR__ . '/../vendor/autoload.php';

  use Williamug\Versioning\StandaloneVersioning;

  // Configure the versioning
  StandaloneVersioning::setRepositoryPath(__DIR__ . '/..');
  StandaloneVersioning::setCaching(true, 3600);
  StandaloneVersioning::setFallbackVersion('1.0.0');
  ?>

  <div class="container">
    <h1>🚀 My Application</h1>

    <div class="version-info">
      <h3>Version Information</h3>
      <p><strong>Current Version:</strong> <code><?php echo StandaloneVersioning::tag(); ?></code></p>
      <p><strong>Build Info:</strong> <code><?php echo StandaloneVersioning::full(); ?></code></p>
      <p><strong>Commit Hash:</strong> <code><?php echo StandaloneVersioning::commit(); ?></code></p>
    </div>

    <h2>About This Application</h2>
    <p>
      This is a demonstration of how to use the Versioning package in a vanilla PHP application.
      The version information is automatically retrieved from your Git repository.
    </p>

    <h2>Features</h2>
    <ul>
      <li>Automatic version detection from Git tags</li>
      <li>Multiple format options (tag, full, commit)</li>
      <li>Built-in caching for better performance</li>
      <li>Graceful fallback when Git is unavailable</li>
    </ul>

    <footer>
      <p>
        <strong>Application Version:</strong> <?php echo app_version(); ?>
        | <strong>Build:</strong> <?php echo app_version('commit'); ?>
      </p>
      <p>
        &copy; <?php echo date('Y'); ?> Your Company. All rights reserved.
      </p>
    </footer>
  </div>
</body>

</html>

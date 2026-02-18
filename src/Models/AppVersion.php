<?php

namespace Williamug\Versioning\Models;

use Illuminate\Database\Eloquent\Model;

class AppVersion extends Model
{
  protected $fillable = [
    'version_tag',
    'version_full',
    'commit_hash',
    'release_name',
    'deployed_at',
    'metadata',
  ];

  protected $casts = [
    'deployed_at' => 'datetime',
    'metadata' => 'array',
  ];

  /**
   * Get the current/latest version
   */
  public static function current(): ?self
  {
    return static::latest('deployed_at')->first();
  }

  /**
   * Store a new version
   */
  public static function store(
    string $tag,
    ?string $full = null,
    ?string $commit = null,
    ?string $releaseName = null,
    ?array $metadata = null
  ): self {
    return static::create([
      'version_tag' => $tag,
      'version_full' => $full,
      'commit_hash' => $commit,
      'release_name' => $releaseName,
      'deployed_at' => now(),
      'metadata' => $metadata,
    ]);
  }
}

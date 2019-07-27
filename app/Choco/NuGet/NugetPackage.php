<?php

namespace App\Choco\NuGet;

use Illuminate\Database\Eloquent\Model;
use App\Choco\Atom\AtomElement;
use App\Model\User;
use Illuminate\Support\Facades\Storage;

/**
 * @property string hash
 * @property string hash_algorithm
 * @property int size
 * @property int user_id
 * @property mixed version
 * @property mixed is_prerelease
 * @property mixed package_id
 * @property mixed authors
 * @property mixed updated_at
 * @property int download_count
 * @property string tags
 * @property bool is_absolute_latest_version
 * @property bool is_listed
 * @property int version_download_count
 * @property bool is_latest_version
 * @property mixed icon_url
 * @property mixed owners
 */
class NugetPackage extends Model
{
    protected $fillable = [
        'package_id', /* string */
        'version', /* string */
        'is_prerelease', /* boolean */
        'title', /* string */
        'authors', /* string */
        'owners', /* string */
        'icon_url', /* string */
        'license_url', /* string */
        'project_url', /* string */
        'download_count', /* integer */
        'require_license_acceptance', /* boolean */
        'development_dependency', /* boolean */
        'description', /* string */
        'summary', /* string */
        'release_notes', /* string */
        'published_date', /* calculatable */ /* date */
        //'last_updated_date', /* eloquent field */ /* date */
        'dependencies', /* string */
        'hash', /* string */
        'hash_algorithm', /* string */
        'size', /* long integer */
        'copyright', /* string */
        'tags', /* string */
        'is_absolute_latest_version', /* boolean */
        'is_latest_version', /* boolean */
        'is_listed', /* boolean */
        'version_download_count', /* integer */
        'min_client_version', /* string */
        'language', /* string */
        'user_id', /* uploader, relation */
    ];

    /**
     * Gets the user that uploaded this package.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return mixed
     */
    public function versions()
    {
        return static::where('package_id', $this->package_id)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * @return string
     */
    public function getNupkgPath()
    {
        return storage_path() . "/app/packages/{$this->package_id}.{$this->version}.nupkg";
    }

    /**
     * @return string
     */
    public function getNupkgName()
    {
        return "{$this->package_id}.{$this->version}.nupkg";
    }

    /**
     * @return bool|null
     * @throws \Exception
     */
    public function delete()
    {
        Storage::delete("packages/{$this->getNupkgName()}");
        return parent::delete();
    }

    /**
     * @return string
     */
    public function getGalleryUrl()
    {
        return route('packages.show', [$this->package_id]);
    }

    /**
     * @return string
     */
    public function getApiQuery()
    {
        return "Packages(Id='{$this->package_id}',Version='{$this->version}')";
    }

    /**
     * @return string
     */
    public function getApiUrl()
    {
        return route('api.package', ['id' => $this->package_id, 'version' => $this->version]);
    }

    /**
     * @return string
     */
    public function getDownloadUrl()
    {
        return route('api.download', ['id' => $this->package_id, 'version' => $this->version]);
    }

    /**
     * @return array
     */
    public function getOwners()
    {
        return array_map('trim', explode(',', $this->owners));
    }

    /**
     * @return array
     */
    public function getAuthors()
    {
        return array_map('trim', explode(',', $this->authors));
    }

    /**
     * @return mixed|string
     */
    public function getIconUrl()
    {
        return !empty($this->icon_url)
        && (strpos($this->icon_url, 'http://') === 0
            || strpos($this->icon_url, 'https://') === 0) ? $this->icon_url : '/images/packageDefaultIcon.png';
    }

    /**
     * @return AtomElement
     */
    public function getAtomElement()
    {
        return with(new AtomElement('entry', $this->getApiUrl(), $this->package_id, $this->updated_at, $this->package_id))
            ->addLink('edit', 'V2FeedPackage', $this->getApiQuery())
            ->addLink('edit-media', 'V2FeedPackage', $this->getApiQuery() . '/$value')
            ->setCategory('NuGetGallery.V2FeedPackage', 'http://schemas.microsoft.com/ado/2007/08/dataservices/scheme')
            ->setContent('application/zip', $this->getDownloadUrl())
            ->addAuthors($this->authors);
    }
}

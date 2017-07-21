<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNugetPackagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('nuget_packages', function(Blueprint $table)
        {
            $table->increments('id');
            $table->string('package_id')->default('');
            $table->string('version')->default('1.0.0');
            $table->boolean('is_prerelease')->default(false);
            $table->string('title')->nullable();
            $table->string('authors')->nullable();
            $table->string('owners')->nullable();
            $table->string('icon_url')->nullable();
            $table->string('license_url')->nullable();
            $table->string('project_url')->nullable();
            $table->boolean('require_license_acceptance')->default(false);
            $table->boolean('development_dependency')->default(false);
            $table->string('description')->nullable();
            $table->string('summary')->nullable();
            $table->string('release_notes')->nullable();
            $table->string('dependencies')->nullable();
            $table->string('hash');
            $table->string('hash_algorithm');
            $table->integer('size')->default(0);
            $table->string('copyright')->nullable();
            $table->string('tags')->default('');
            $table->boolean('is_absolute_latest_version')->default(true);
            $table->boolean('is_latest_version')->default(true);
            $table->boolean('is_listed')->default(true);
            $table->integer('version_download_count')->default(0);
            $table->string('min_client_version')->nullable();
            $table->string('language')->nullable();
            $table->integer('user_id')->default(0);
            $table->integer('download_count')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('nuget_packages');
    }
}

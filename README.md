# CAST

CAST is a Content Addressable Storage and Transfer library for MODX Revolution built around Git.

CAST requires PHP >= 5.3.3, MODX Revolution >= 2.1 and the Git binaries >= TBD to be installed and accessible where used.


## REQUIREMENTS

In order to use Cast, your environment must at least meet the following requirements:

* PHP >= 5.3.3
* Git >= TBD
* MODX Revolution >= 2.1

You must also be able to run PHP using the CLI SAPI with enough permissions to read and write files being managed in the Git repository.


## INSTALLATION

There are several methods for installing Cast. The easiest way to get started is by installing the Cast Phar distribution.

### Install Phar Distribution

Download the latest [`cast.phar`](http://modx.s3.amazonaws.com/releases/cast/cast.phar "cast.phar") distribution of Cast.

### Installing Source Distributions

Alternatively, you can install Cast using the source and [Composer](http://getcomposer.org/). Simply clone the repository or download a release of Cast and use `composer install` to install all the required dependencies.

### Cast in your PATH

With any of the installation methods you'll want to create an executable symlink called cast pointing to bin/cast or directly to the cast.phar. You can then simply type `cast` instead of `bin/cast` or `php cast.phar` to execute the Cast application from any directory.


## USAGE

Cast is meant to work as a wrapper for the git binary. Use it in place of git commands when you want to check for or make modifications to the MODX database before or after the corresponding git command is run. For example, calling `cast status` will first serialize all MODX database records to files in a .model directory before running the corresponding `git status` command and returning the results. Similarly, calling `cast checkout master` will update the database tables from the serialized files in the .model directory that are checked-out into the working copy by the corresponding `git checkout master` call, which is performed first.

In addition to wrapping existing git commands, Cast introduces two commands of it's own.

 * `cast serialize` - Serialize objects from the MODX database to files in a configurable directory (`.model/` by default). When working at the class or global level, existing files are removed from each class directory and new serialized copies are generated for all records. Specifying a specific model file will simply create a new or overwrite the existing file.

 * `cast unserialize` - Unserialize objects from the serialized model files into the database. When working at the class or global level, existing records are truncated from affected database tables before the new copies are injected. Specifying a specific model file will simply add or update that record in the target database table.

Both commands can work on the entire repository, or can be limited to specific model classes or individual objects by specifying one or more serialized paths on the command line.

__IMPORTANT: Cast does not currently support any git commands that require user interaction.__

### CONFIGURATION

There are a few configuration options you can set in Cast. These can be passed to the \Cast\Cast constructor or they can be set in your git config globally or per-repository.

 * `cast.git_path` - If git is not available in your path or you want to use a specific git binary, set this to the full path. The default value is `git`.
 * `cast.serializer_mode` - Set this to `0` (explicit) to only serialize/deserialize from the database if an argument is passed indicating to do so. The default is `1` (implicit).
 * `cast.serializer_class` - Indicates the format into which the database records are serialized. The default is `\Cast\Serialize\PHPSerializer`.
 * `cast.serialized_model_path` - Specifies the relative path from the repository root where the serialized database records are stored. The default is `.model/`.
 * `cast.serialized_model_excludes` - Specifies `xPDOObject` classes to exclude from serialization. If specified, the values are merged with the `defaultModelExcludes`.

__NOTE: The following xPDOObject classes are known as the `defaultModelExcludes` and are *always* excluded from serialization:__

 * `xPDOObject`
 * `xPDOSimpleObject`
 * `modAccess`
 * `modAccessibleObject`
 * `modAccessibleSimpleObject`
 * `modActiveUser`
 * `modDbRegisterQueue`
 * `modDbRegisterTopic`
 * `modDbRegisterMessage`
 * `modManagerLog`
 * `modPrincipal`
 * `modSession`


#### .CASTATTRIBUTES

You can fine-tune how Cast serializes and unserializes different model classes by providing a `.castattributes` file in the root of the serialized model directory. This allows you to specify criteria to limit which classes and/or objects are included when serializing/unserializing or to customize behavior by providing callbacks for specific actions taken on specific classes. For example, the following definition for modCategory helps make sure the modCategoryClosure table is truncated so the save action can recreate the proper closure records for each modCategory that is unserialized:

```php
    'modCategory' =>
    array(
        'criteria' =>
        array(
            0 => '1=1',
        ),
        'attributes' =>
        array(
            'before_class_unserialize_callback' => function(&$serializer, array $model, array &$processed)
            {
                if ($serializer->cast->modx->exec("TRUNCATE TABLE {$serializer->cast->modx->getTableName('modCategoryClosure')}") === false) {
                    $serializer->cast->modx->log(\modX::LOG_LEVEL_ERROR, "Could not truncate modCategoryClosure for Cast unserialization");
                }
            }
        ),
    ),

```

### SETTING UP A NEW REPOSITORY

### USING WITH AN EXISTING REPOSITORY

### CLONING A REMOTE REPOSITORY


## COPYRIGHT AND LICENSE

Copyright (C) 2013 by Jason Coward <jason@opengeek.com>

For the full copyright and license information, please view the LICENSE file that was distributed with this source code.

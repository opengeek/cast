<?php
/**
 * This file is part of the cast package.
 *
 * Copyright (c) Jason Coward <jason@opengeek.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cast;

use Cast\Git\Git;
use Symfony\Component\Finder\Finder;

/**
 * This class compiles Cast into a distributable Phar.
 *
 * @package Cast
 */
class Compiler
{
    private $version;
    private $versionDate;

    /**
     * Compile Cast into a Phar for distribution.
     *
     * @param string $into The name of the phar to build.
     */
    public function compile($into = 'cast.phar')
    {
        if (file_exists($into)) {
            unlink($into);
        }

        $git = new Git(__DIR__ . '/../../../');

        /* get version and version date */
        $versionData = $git->exec('log --pretty="%H" -n1 HEAD');
        $this->version = trim($versionData[1]);

        $versionDateData = $git->exec('log -n1 --pretty=%ci HEAD');
        $this->versionDate = new \DateTime(trim($versionDateData[1]));
        $this->versionDate->setTimezone(new \DateTimeZone('UTC'));
        $this->versionDate = $this->versionDate->format('Y-m-d H:i:s');

        $tagData = $git->exec('describe --tags HEAD');
        if ($tagData[0] == '0') {
            $this->version = trim($tagData[1]);
        }

        /* start building the Phar */
        $phar = new \Phar($into, 0, 'cast.phar');
        $phar->setSignatureAlgorithm(\Phar::SHA1);

        $phar->startBuffering();

        /* add src files */
        $finder = new Finder();
        $finder->files()
            ->ignoreVCS(true)
            ->ignoreDotFiles(true)
            ->name('*.php')
            ->notName('Compiler.php')
            ->in(__DIR__ . '/..');

        foreach ($finder as $file) {
            $this->addFile($phar, $file);
        }

        /* add composer autoloading infrastructure */
        $this->addFile($phar, new \SplFileInfo(__DIR__.'/../../vendor/autoload.php'));
        $this->addFile($phar, new \SplFileInfo(__DIR__.'/../../vendor/composer/autoload_namespaces.php'));
        $this->addFile($phar, new \SplFileInfo(__DIR__.'/../../vendor/composer/autoload_classmap.php'));
        $this->addFile($phar, new \SplFileInfo(__DIR__.'/../../vendor/composer/autoload_real.php'));
        if (file_exists(__DIR__.'/../../vendor/composer/include_paths.php')) {
            $this->addFile($phar, new \SplFileInfo(__DIR__.'/../../vendor/composer/include_paths.php'));
        }
        $this->addFile($phar, new \SplFileInfo(__DIR__.'/../../vendor/composer/ClassLoader.php'));
        $this->addCastBin($phar);

        /* set the stub */
        $phar->setStub($this->getStub());

        $phar->stopBuffering();

        /* add LICENSE */
        $this->addFile($phar, new \SplFileInfo(__DIR__.'/../../LICENSE'), false);

        unset($phar);
    }

    /**
     * Add a file to the Phar, optionally stripping whitespace.
     *
     * @param \Phar $phar
     * @param \SplFileInfo $file
     * @param bool $strip
     */
    private function addFile($phar, $file, $strip = true)
    {
        $path = str_replace(dirname(dirname(__DIR__)).DIRECTORY_SEPARATOR, '', $file->getRealPath());

        $content = file_get_contents($file);
        if ($strip) {
            $content = $this->stripWhitespace($content);
        } elseif ('LICENSE' === basename($file)) {
            $content = "\n".$content."\n";
        }

        $content = str_replace('@package_version@', $this->version, $content);
        $content = str_replace('@release_date@', $this->versionDate, $content);

        $phar->addFromString($path, $content);
    }

    /**
     * Add the bin/cast script.
     *
     * @param \Phar $phar
     */
    private function addCastBin($phar)
    {
        $content = file_get_contents(__DIR__.'/../../bin/cast');
        $content = preg_replace('{^#!/usr/bin/env php\s*}', '', $content);
        $phar->addFromString('bin/cast', $content);
    }

    /**
     * Removes whitespace from a PHP source string while preserving line numbers.
     *
     * @param  string $source A PHP string
     * @return string The PHP string with the whitespace removed
     */
    private function stripWhitespace($source)
    {
        if (!function_exists('token_get_all')) {
            return $source;
        }

        $output = '';
        foreach (token_get_all($source) as $token) {
            if (is_string($token)) {
                $output .= $token;
            } elseif (in_array($token[0], array(T_COMMENT, T_DOC_COMMENT))) {
                $output .= str_repeat("\n", substr_count($token[1], "\n"));
            } elseif (T_WHITESPACE === $token[0]) {
                // reduce wide spaces
                $whitespace = preg_replace('{[ \t]+}', ' ', $token[1]);
                // normalize newlines to \n
                $whitespace = preg_replace('{(?:\r\n|\r|\n)}', "\n", $whitespace);
                // trim leading spaces
                $whitespace = preg_replace('{\n +}', "\n", $whitespace);
                $output .= $whitespace;
            } else {
                $output .= $token[1];
            }
        }

        return $output;
    }

    private function getStub()
    {
        $stub = <<<'EOF'
#!/usr/bin/env php
<?php
/**
 * This file is part of the cast package.
 *
 * Copyright (c) Jason Coward <jason@opengeek.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

Phar::mapPhar('cast.phar');

EOF;

        // add warning once the phar is older than 30 days
        if (preg_match('{^[a-f0-9]+$}', $this->version)) {
            $warningTime = time() + 30*86400;
            $stub .= "define('COMPOSER_DEV_WARNING_TIME', $warningTime);\n";
        }

        return $stub . <<<'EOF'
require 'phar://cast.phar/bin/cast';

__HALT_COMPILER();
EOF;
    }
} 

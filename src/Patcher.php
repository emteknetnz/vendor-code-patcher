<?php

namespace emteknetnz\VendorCodePatcher;

use SilverStripe\Core\Config\Configurable;
use SilverStripe\Core\Flushable;

/**
 * Applies .patch files in the _vendor_patches directory to the modules in the vendor directory
 */
class Patcher implements Flushable
{
    use Configurable;

    private static $do_log = true;

    public static function flush()
    {
        $vendorPath = str_replace('//', '/', BASE_PATH . '/vendor');
        $patchesPath = str_replace('//', '/', BASE_PATH . '/_vendor_patches');
        $patchedPath = "$patchesPath/_patched";
        if (!self::assertDir($vendorPath)) {
            self::log('vendor directory does not exist');
            return;
        }
        if (!self::assertDir($patchesPath, true)) {
            self::log('_vendor_patches directory does not exist or is not writable');
            return;
        }
        $first = true;
        foreach (scandir($patchesPath) as $account) {
            if (!self::assertDir("$patchesPath/$account")) {
                continue;
            }
            if ($first) {
                self::log("\nRunning vendor-code-patcher");
                if (!file_exists($patchedPath)) {
                    mkdir($patchedPath);
                }
                if (!is_writable($patchedPath)) {
                    chmod($patchedPath, 0777);
                }
                $first = false;
            }
            foreach (scandir("$patchesPath/$account") as $module) {
                if (!self::assertDir("$patchesPath/$account/$module")) {
                    continue;
                }
                foreach (scandir("$patchesPath/$account/$module") as $patch) {
                    if (in_array($patch, ['.', '..'])) {
                        continue;
                    }
                    $vpp = "$patchesPath/$account/$module/$patch";
                    if (pathinfo($vpp, PATHINFO_EXTENSION) === 'patch') {
                        $vp = "$vendorPath/$account/$module";
                        // patch options
                        // -p1 => config for leading forward slashes
                        // -l => ignore whitespace
                        // -r - => don't create .rej files when a patch doesn't apply
                        // -B /tmp/ => don't create .orig files in place when a patch
                        //             does not apply, instead create in /tmp
                        // -d => vendor module path
                        $res = shell_exec("patch -p1 -l -r - -B /tmp/ -d $vp < '$vpp'");
                        self::log($res ?: "Attempting to apply patch $vpp returned no output");
                    } else {
                        self::log("$vpp is not a .patch file");
                    }
                    if (!file_exists("$patchedPath/$account/$module")) {
                        mkdir("$patchedPath/$account/$module", 0777, true);
                    }
                    rename("$patchesPath/$account/$module/$patch", "$patchedPath/$account/$module/$patch");
                }
                rmdir("$patchesPath/$account/$module");
            }
            rmdir("$patchesPath/$account");
        }
    }
    
    private static function log(string $str): void
    {
        if (!self::config()->get('do_log')) {
            return;
        }
        if (PHP_SAPI !== 'cli') {
            return;
        }
        echo $str . "\n";
    }

    private static function assertDir(string $path, bool $assertWritable = false): bool
    {
        $b = !preg_match('#/\.\.?$#', $path) && !preg_match('#/_patched$#', $path) && is_dir($path);
        if ($b && $assertWritable) {
            $b = is_writable($path);
        }
        return $b;
    }
}

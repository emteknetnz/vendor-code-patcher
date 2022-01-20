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
                self::log('Running vendor-code-patcher');
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
                    if (pathinfo("$patchesPath/$account/$module/$patch", PATHINFO_EXTENSION) !== 'patch') {
                        continue;
                    }
                    $p1 = "$vendorPath/$account/$module";
                    $p2 = "$patchesPath/$account/$module/$patch";
                    $res = shell_exec("patch -p1 -N -l -d $p1 < '$p2'");
                    self::log($res ?: "Attempting to patch $account/$module/$patch returned no output");
                }
            }
            rename("$patchesPath/$account", "$patchedPath/$account");
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

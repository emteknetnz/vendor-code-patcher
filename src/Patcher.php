<?php

namespace emteknetnz\VendorCodePatcher;

use SilverStripe\Core\Flushable;

/**
 * Applies .patch files in the _vendor_patches directory to the modules in the vendor directory
 */
class Patcher implements Flushable
{
    public static function flush()
    {
        $this->log('Running vendor-code-patcher');
        $vendorPath = str_replace('//', '/', BASE_PATH . '/vendor');
        $patchesPath = str_replace('//', '/', BASE_PATH . '/_vendor_patches');
        foreach (scandir($patchesPath) as $account) {
            if (!$this->assertDir("$patchesPath/$account")) {
                continue;
            }
            foreach (scandir("$patchesPath/$account") as $module) {
                if (!$this->assertDir("$patchesPath/$account/$module")) {
                    continue;
                }
                foreach (scandir("$patchesPath/$account/$module") as $patch) {
                    if (!pathinfo("$patchesPath/$account/$module/$patch", PATHINFO_EXTENSION) == '.patch') {
                        continue;
                    }
                    $p1 = "$vendorPath/$account/$module";
                    $p2 = "$patchesPath/$account/$module/$patch";
                    $res = shell_exec("patch -p1 -N -d $p1 < '$p2'");
                    $this->log($res);
                }
            }
        }
    }
    
    private function log($str)
    {
        if (PHP_SAPI !== 'cli') {
            return;
        }
        echo $str . "\n";
    }

    private function assertDir($path)
    {
        return !preg_match('#/\.\.?$#', $path) && is_dir($path);
    }
}

<?php

namespace emteknetnz\VendorCodePatcher;

use SilverStripe\Dev\BuildTask;

class VendorCodePatchTask extends BuildTask
{
    private static $segment = 'VendorCodePatchTask';

    protected $title = 'Vendor code patch task';

    protected $description = 'Applies .patch files to the modules in the vendor directory';

    public function run($request)
    {
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
                    echo '<pre> ' . shell_exec("patch -p1 -N -d $p1 < $p2") . '</pre>';
                }
            }
        }
    }

    private function assertDir($path)
    {
        return !preg_match('#/\.\.?$#', $path) && is_dir($path);
    }
}

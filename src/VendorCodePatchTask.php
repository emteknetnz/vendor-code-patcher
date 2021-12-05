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
        $path = $this->getVendorPatchesPath();
        foreach (scandir($path) as $account) {
            if (!$this->assertDir("$path/$account")) {
                continue;
            }
            foreach (scandir("$path/$account") as $module) {
                if (!$this->assertDir("$path/$account/$module")) {
                    continue;
                }
                foreach (scandir("$path/$account/$module") as $patch) {
                    if (!pathinfo("$path/$account/$module/$patch", PATHINFO_EXTENSION) == '.patch') {
                        continue;
                    }
                    echo shell_exec("patch -p1 -N -d vendor/$account/$module < $path/$account/$module/$patch");
                }
            }
        }
    }

    private function assertDir($path)
    {
        return !preg_match('#/\.\.?$#', $path) && is_dir($path);
    }

    private function getVendorPatchesPath()
    {
        return str_replace('//', '/', BASE_PATH . '/_vendor_patches');
    }
}

# vendor-code-patcher

Used to add private code patches directly to a private testing repo to bypass so make it easier to deploy to a testing environment.

Put your patches in BASE_PATH . '/_vendor_patches' so they match the modules they're in, e.g.

[project_root]/_vendor_patches/silverstripe/framework/0001-FIX-Multiple-files-changed.patch

## Requirements

The webserver you deploy requires the `patch` utility available.  This will be available on the typical debian/ubuntu webserver.

## Generating .patch files

Standard practice is to have a pull-request in a private repo for unreleased patches squashed down to a single commit.

Copy the sha from this single commit

```
cd vendor/silverstripe/framework
git remote add my-private-account git@github.com:my-private-account/silverstripe-framework.git
git fetch my-private-account
git format-patch -1 [sha]
```

This will generate a .patch file - copy this to correct directory in `_vendor_patches`


## Applying the patch

Login in to the CMS as admin

/dev/tasks/VendorCodePatchTask

In the future, functionality could be changed to be flushable and run on ?flush, so that it happens as part of deployment

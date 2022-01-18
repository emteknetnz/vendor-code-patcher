# vendor-code-patcher

Used to add private code patches directly to a private testing repo to bypass so make it easier to deploy to a testing environment.

Put your patches in BASE_PATH . '/_vendor_patches' so they match the modules they're in, e.g.

[project_root]/_vendor_patches/silverstripe/framework/0001-FIX-Multiple-files-changed.patch

## Requirements

The webserver you are deploying to requires the `patch` utility available.  This will be available on the typical debian/ubuntu webserver.

## Generating .patch files for GitHub pull requests

Simply access the patch url for the pull-request

https://patch-diff.githubusercontent.com/raw/silverstripe/silverstripe-admin/pull/1259.patch

## Generating .patch files from local files

Standard practice is to have a pull-request in a private repo for unreleased patches squashed down to a single commit.

Copy the sha from this single commit

```
cd vendor/silverstripe/framework
git remote add my-private-account git@github.com:my-private-account/silverstripe-framework.git
git fetch my-private-account
git format-patch -1 [sha]
```

## Applying patches

Patches are automatically applied on ?flush=1 - this will happen as part of deployment

The vendor-code-patcher will run on every flush, however it will only apply the patches on the first flush if the .patch file while any files do not have any patches applied.

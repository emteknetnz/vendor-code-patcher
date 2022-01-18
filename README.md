# vendor-code-patcher

Used to add private code patches directly to a private testing repo to bypass so make it easier to deploy to a testing environment.

Put your patches in BASE_PATH . '/_vendor_patches' so they match the modules they're in, e.g.

[project_root]/_vendor_patches/silverstripe/framework/0001-FIX-Multiple-files-changed.patch

## Requirements

The webserver you are deploying to requires the `patch` utility available.  This will be available on the typical debian/ubuntu webserver.

## Generating .patch files for GitHub pull-requests (recommneded method)

Simply suffix .diff to the pull-request url and copy the content to a new local .patch file

https://github.com/silverstripe/silverstripe-admin/pull/1280.diff

Note suffixing .patch only gets the last commit in a multi-commit pull-request

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

Patches are automatically applied on the first ?flush=1 - this should happen as part of a deployment

Patch files are then moved to [project_root]/_vendor_patches/_patched so they won't be run on subsequent flushes

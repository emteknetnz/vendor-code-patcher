# vendor-code-patcher

Used to add private code patches directly to a private testing repo to bypass so make it easier to deploy to a testing environment.

Put your patches in BASE_PATH . '/_vendor_patches' so they match the modules they're in, e.g.

[project_root]/_vendor_patches/silverstripe/framework/0001-FIX-Multiple-files-changed.patch

## Requirements

The webserver you are deploying to requires the `patch` utility available.  This will be available on the typical debian/ubuntu webserver.

## Generating .patch files from GitHub pull-requests using wget (recommneded method)

Simply suffix .diff to the pull-request url to get the target url, for instance

https://github.com/silverstripe/silverstripe-admin/pull/1259.diff

Note suffixing .patch only gets the last commit in a multi-commit pull-request

```
MYDIFF=https://github.com/silverstripe/silverstripe-admin/pull/1259.diff
MYDIR=_vendor_patches/silverstripe/admin
MYPATCH=0001-my.patch
mkdir -p $MYDIR
wget -O $MYDIR/$MYPATCH $MYDIFF
```

wget will resolve any redirects for you

**Important** - use wget to download the diff rather than copy pasting via your browser so that character encoding is retained. This is especially imporant for compressed bundle.js type of files when they contain a special space character e.g. nbsp.  Copy pasting from your browser will usually mistakenly convert these characters to regular spaces, causing the patch to not be applied.

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

## Note about javascript generated bundle files

You may have issues applying patches to generated bundle.js type of files due to minor differences when your local git checks them out

If you're experiencing issues, manually replacing your local copy of bundle.js with a copy pasted raw version from GitHub should resolve any issues.


## Testing patches on local project

To apply a patch to the local project to ensure the patch file is valid

`patch -p1 -l -r - -B /tmp/ -d vendor/silverstripe/framework < '/home/<username>/path/to/_vendor_patches/silverstripe/framework/0001.my-patch'`

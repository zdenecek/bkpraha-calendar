
# BK Praha Calendar

## Requirements

- PHP 7.4

## Deployment

Pushing to `main` deploys `api/` to `calendar.bkpraha.cz` via
[zip-ftp-php-unzip](https://github.com/zdenecek/zip-ftp-php-unzip): the workflow
runs `composer install`, zips `api/`, uploads the zip over FTP to the site root
and triggers a PHP script that extracts it there.

Extraction overwrites and merges — it never deletes. Files removed from the repo
stay on the server until you delete them manually.

### Secrets

| Secret | Value |
| --- | --- |
| `FTP_HOST` | FTP hostname |
| `FTP_USER` | FTP username |
| `FTP_PASSWORD` | FTP password |
| `GOOGLE_CALENDAR_ID` | ID of the Google calendar to read |
| `GOOGLE_KEY_BASE64` | base64 of the service account JSON key |

`api/.env` and `api/keys/key.json` are not in the repo. The workflow writes both
from the secrets above, so they exist only in the build and on the server.

To set `GOOGLE_KEY_BASE64` without printing the key:

```bash
base64 -i path/to/service-account.json | tr -d '\n' | pbcopy
```

Rotating the key means updating that secret and re-running the workflow.

### Protecting the uploaded zip

The zip is uploaded to the web root and deleted after extraction, but it
contains `.env` and the service account key while it is there. To keep it from
being downloadable during that window, put this in an `.htaccess` at the site
root — it must not cover `.php`, as the unzip script has to stay executable:

```apache
<FilesMatch "\.zip$">
    Require all denied
</FilesMatch>
```

## Embedding

```html
<div class="h_iframe">
  <iframe
    src="https://calendar.bkpraha.cz/api/?view=column&bg=%23ECEFF1"
    frameborder="0"
    allowfullscreen
  ></iframe>
</div>

<style>
  .h_iframe iframe {
    width: 100%;
    height: 1090px;
  }
  .h_iframe {
    height: 100%;
    width: 100%;
  }
</style>
```
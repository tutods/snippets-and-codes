<a href="https://github.com/TutoDS"><img src="../../images/daniel-sousa.png" alt="Daniel Sousa" width="100px" /></a>

# Fix Permissions <small>(using SSH)</small>

> **SOURCE:** [Adirael](https://gist.github.com/Adirael/3383404)

+ **File:** `fix-permissions.sh`

## How to use

1. Create this file on you server (not in WordPress installation folder)
2. Run using: `sudo bash fix-permissions.sh <folder> <owner> [group] [webserver-group]`
   * `<folder>`: is your WordPress folder installation (like `public_html`, etc.)
   * `<owner>`: user that owns the files
   * `[group]`: group of the files, defaults to `<owner>`
   * `[webserver-group]`: group the web server runs as (like `www-data`), defaults to `[group]`. It gets write access to `wp-content` and read-only access to `wp-config.php` (640), so a compromised plugin can't rewrite your database credentials. If a plugin needs to edit `wp-config.php` (some caching or security plugins do), run `chmod 660 wp-config.php`, let it make the change, then set it back to 640.

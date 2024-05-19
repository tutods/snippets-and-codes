<a href="https://github.com/TutoDS"><img src="../../images/daniel-sousa.png" alt="Daniel Sousa" width="100px" /></a>

# Fix Permissions <small>(using SSH)</small>

> **SOURCE:** [Adirael](https://gist.github.com/Adirael/3383404)

+ **File:** `fix-permissions.sh`

## How to use

1. Create this file on you server (not in WordPress installation folder)
2. Run using: `sh fix-permissions.sh <folder> <user:group>`
   * `<folder>`: is your WordPress folder installation (like `public_html`, etc.)
   * `<user:group>`: is your user and group
     * **Note:** I use same user and group, if your group is different of user change this lines:

		```bash
		WP_OWNER=$2 # <-- wordpress owner
		WP_GROUP=$3 # <-- wordpress group
		WP_ROOT=$1 # <-- wordpress root directory
		WS_GROUP=$3 # <-- webserver group
		```
		This way you running using: `sh fix-permissions.sh <folder> <user> <group>`

# aaascv
AAAs - Autism Ashram Awaaz
Updating AAAs from Desktop Local
# Github

## Fetch github key pair

This step is only needed if you're developing as the aaascvdev account.

1. Get the `aaascv` public key from [Account Passwords] and store as `~/.ssh/aaascvdev.pub`
1. Get the `aaascv` private key from [Account Passwords] and store as `~/.ssh/aaascvdev`
1. Update the permissions on the keys:
   ```
   chmod 600 ~/.ssh/aaascv*
   ```

## Clone and fork

1. Create the workspace:
   ```
   mkdir -p ~/git/forks
   ```
1. Fork the repository: https://github.com/autismashramawaaz/aaascv
1. Clone the fork
   ```
   cd ~/git/forks
   git@github.com:<you>/aaascv.git
   ```
1. Set up your remote
   ```
   cd aaascv
   git remote add upstream git@github.com:autismashramawaaz/aaascv.git
   ```

# Local development

1. Make sure Docker is running
1. Always make sure you have the latest from git before you make changes
   ```
   git pull upstream main
   git push origin main
   ```
1. Build the image
   ```
   make build
   ```
1. Run the image
   ```
   make start
   ```
1. Use a browser to navigate to http://127.0.0.1:8080/

To stop the service, run `make stop`

# Update website

1. Always make sure you have the latest from git
   ```
   git pull upstream main
   git push origin main
   ```
1. Commit your changes to git.
   ```
   git commit whateverchanged -m "what you changed"
   git push origin main
   ```
1. Open a PR
1. Merge your PR
1. Sync your changes locally
   ```
   git pull upstream main
   git push origin main
   ```
1. Get the FTP password from the [Account Passwords].
   It is under "Web Hosting" > FTP Password. The password begins with `5` and
   ends with `h`
1. Set the password in the environment as follows:
   ```
   export FTP_PASS=value from [Account Passwords]
   ```
1. Run `make mirror-dev` to push the changes to dev
1. Test your changes, go to https://dev.autismashramawaaz.org/
1. Run `make mirror-prod` to push the changes to production
   ```
   $ make mirror-prod
   docker build -t aaascv:latest -f Dockerfile .
   [...snip...]
   aaascv:latest
   Use 'docker scan' to run Snyk tests against images to find vulnerabilities and learn how to fix them
   scripts/run.sh "mirror-prod" "aaascv" "latest"
   lcd ok, local cwd=/usr/share/nginx/html
   cd ok, cwd=/public_html
   Total: 29 directories, 179 files, 0 symlinks
   ```
1. Test your changes, go to https://www.autismashramawaaz.org/


[Account Passwords]: https://docs.google.com/document/d/1beGVDCFjWMWgWmwGFcWUnPycNT6Sh2vNjWbKBIR-ssY/edit

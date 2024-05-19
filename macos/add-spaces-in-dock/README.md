<a href="https://github.com/TutoDS"><img src="../../images/daniel-sousa.png" alt="Daniel Sousa" width="100px" /></a>

# Add space in macOS Dock

In this "guide" you can find the commands to add space(s) in your **macOS Dock**. I use this to organize my Dock in "sections", like:
- Finder & Launchpad
- Browsers & Internet
- Email & Social
- Coding & Related
- Others (Spotify, AppStore, ...)
- Open Apps (apps not fixed in Dock)

## Result

In the image below you can see result of this "guide" and the "sections" I list above.

<a href="#"><img src="./../../images/dock.png" alt="macOS Dock"></a>

## How to add

To add that spaces in your **macOS Dock** you only need run this command:

```bash
defaults write com.apple.dock persistent-apps -array-add '{"tile-type"="spacer-tile";}';
```

And after that command you need force reload your Dock (`killall Dock`).custom-file-label

But, if you want add 4 spaces, you need run that command 4 times, to easy this step I create a *bash file* (`add-space-command-sh`), when you tell how much spaces you want. Example:

> I want **4 spaces**
> **RUN FILE:** `sh ./add-space-command.sh 4` w- here **4** is number of spaces

> **Note:** if you needs for any reason restore default dock you only need run `reset-dock-command.sh` (**`sh reset-dock-command.sh`**)
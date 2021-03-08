<a href="https://github.com/TutoDS"><img src="../../images/daniel-sousa.png" alt="Daniel Sousa" width="100px" /></a>

# How to Optimize Images

I use this guide to optimize images before upload to my **WordPress** projects.


## Installation

In this "guid" I use tree things:
- Imagemagick;
- JPEGOptim;
- OptiPNG.

### Linux
> Example to Linux Mint, Debian, Ubuntu, ...


```bash
sudo apt install imagemagick jpegoptim optipng
```

### macOS

>Using [Homebrew](https://brew.sh)

```bash
brew install imagemagick jpegoptim optipng
```

## Bulk Optimization

First I resize all files to max 1500px (maintain aspect ration), and add in name `_resize`.

> **Note:** to `.png` extension change `jpeg` to `png`


```bash
convert *jpeg -resize 1500x  \
-set filename:fname '%t_resized' +adjoin '%[filename:fname].jpeg'
```

After I resize all files (`png` and `jpg/jpeg`), I proceed to file size optimization:

### For `jpg/jpeg`:
```bash
jpegoptim [options] -s --size=200k *.jpeg
```
> Note: `--size=200k` is file size I pretend

#### Options

- `-s` or `--strip-all`: remove all metadata
- `-o`: overwrite existing file
- `-S[size]` or `--size=[size]`: file size after optimization
- `-m[quality]` or `--max=[quality]`: quality of optimization in 0-100 scale
- `-d[path]` or `--dest=[path]`: destination folder (after optimization)

### For `png`

```bash
optipng [options] *.png
```

#### Options

- `-o [value]`: optimization level (0 to 7)
- `-clobber`: overwrite existing file
- `-dir [folder]`: destination folder after optimization;
- `-strip`: remove all metadata

<br><br>

After this steps your images are ready to upload and use in your projects.
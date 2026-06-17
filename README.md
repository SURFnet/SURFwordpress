# WP SURF Theme 

![BETA release](https://github.com/VanOns/surf-os/actions/workflows/release-beta.yml/badge.svg?branch=staging)
![PRODUCTION release](https://github.com/VanOns/surf-os/actions/workflows/release-production.yml/badge.svg?branch=master)

- [Requirements](#requirements)
- [Installation](#installation)
    * [1. Install composer packages](#1-install-composer-packages)
    * [2. Install npm packages](#2-install-npm-packages)
- [Start work son!](#start-work-son-)
- [Staying up to date](#staying-up-to-date)
- [Running locally](#running-locally)
- [Glossary](#glossary)
- [Troubleshooting](#troubleshooting)

## Requirements

**You'll need to have the following items installed on your computer before continuing.** NOTE: You only have to this
once. If you already have all of this, please continue to 'Installation'.

* [SourceTree](http://sourcetreeapp.com/) or [Git](http://git-scm.com/downloads/): Use the installer provided on the
  SourceTree or GIT website.
* [PHP](https://www.php.net/downloads.php): Either use your OS' package manager, or download it from the php website
* [Composer](https://getcomposer.org/): Use the installer provided on the Composer website. **(Install GLOBALLY)**
* [Node.js](http://nodejs.org/): Use the installer provided on the NodeJS website.

## Installation

### 1. Install composer packages

Navigate to the theme folder (where composer.json is located) and install composer.

```bash
cd wp-content/themes/wp-surf-theme
composer install
```

### 2. Install npm packages

While still in the theme foler install the npm packages.

```bash
npm install
```

## Start work son!

Now you can start working. While still in the theme folder run and watch npm like this:

```bash
npm run watch
```

When building for live run this:

```bash
npm run build
```

Compiled files will be placed in `./wp-surf-theme/assets/**/*`. This why we keep our files structured. Keep in mind that our
css files `theme.css`, `admin.css`, `admin.editor.css` will also be located in this structure rather than using
the `./wp-surf-theme/style.css`.

## Staying up to date

Don't forget to update npm packages once in a while. While in the theme folder, run this to update:

```bash
npm update
```

## Running locally

You can use Docker + Docker Compose to run the site locally.

First, you have to install all the composer and npm dependencies, for this you will need to
install [PHP](https://www.php.net/manual/en/install.macosx.packages.php), [Node](https://nodejs.org/en/download/)
, [Composer](https://getcomposer.org/download/) and [Subversion](https://formulae.brew.sh/formula/subversion).

```bash
composer install
cd wp-content/plugins
composer install
cd ../themes/wp-surf-theme
npm install
npm run build
```

Make sure you have [Docker](https://docs.docker.com/get-docker/)
and [Docker Compose](https://docs.docker.com/compose/install/).

Once all dependencies are installed and Docker has been set up you can run (in the project root):

```bash
docker-compose up
```

This will start a WordPress and a MySQL instance.

On MacBooks with the M1 chipset you might run into issues when trying to start a MySQL container. In that case you can
try running:

```bash
docker pull --platform linux/x86_64 mysql
# Now docker-compose up should work
docker-compose up
```

The WordPress instance will be available at [localhost:8080](localhost:8080) and the MySQL instance will be available
at [localhost:13306](localhost:13306).
The first time you visit this website you'll have to click through the usual WordPress setup steps where you create an
admin user.

Before you can start development you will have to enable the starter-theme and the already provided plugins in the admin
interface.

Once that's done you'll have the following commands available for helping you during development:

```bash
./cowboy db:seed # Add test data to the database
./cowboy db:refresh # Remove all posts and postmeta from the database
./cowboy db:refresh --seed # Remove al posts and postmeta from the database and generate new test data
```

## Glossary

* [Composer](https://getcomposer.org/): Dependency manager for PHP.
* [Node.js](http://nodejs.org/): Lightweight and efficient real-time applications in JavaScript.
* [Webpack](https://webpack.js.org/): JavaScript bundler.

## Troubleshooting

Keep your Node.js and npm updated. Ask colleagues for help.

**WINDOWS**

Remove the 'node_modules' folder, run:

```
mkdir empty_dir
robocopy empty_dir node_modules /s /mir
rmdir /S empty_dir
rmdir /S node_modules
```

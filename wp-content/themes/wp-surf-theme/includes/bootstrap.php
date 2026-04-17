<?php

use SURF\Admin\BlockPatterns;
use SURF\Admin\Blocks;
use SURF\Admin\Crons;
use SURF\Admin\MetaBoxes;
use SURF\Admin\Pages;
use SURF\Admin\Shortcodes;
use SURF\Admin\Sidebars;
use SURF\Admin\Widgets;
use SURF\Api\ExportController;
use SURF\Api\SetupWizardController;
use SURF\Api\SyncCspController;
use SURF\Core\CLI;
use SURF\Hooks\AcfHooks;
use SURF\Hooks\AgendaHooks;
use SURF\Hooks\AssetHooks;
use SURF\Hooks\AuthorHooks;
use SURF\Hooks\ConfigHooks;
use SURF\Hooks\HeadingHooks;
use SURF\Hooks\ImageHooks;
use SURF\Hooks\MenuHooks;
use SURF\Hooks\PageHooks;
use SURF\Hooks\PostHooks;
use SURF\Hooks\SearchHooks;
use SURF\Hooks\TermHooks;

/*
 * Register any classes that need some kind of bootstrapping here
 */

/*
 * General
 */
CLI::init();

/*
 * Admin
 */
BlockPatterns::init();
Blocks::init();
Crons::init();
MetaBoxes::init();
Pages::init();
Shortcodes::init();
Sidebars::init();
Widgets::init();

/*
 * Hooks
 */
AgendaHooks::register();
AssetHooks::register();
ConfigHooks::register();
AcfHooks::register();
HeadingHooks::register();
ImageHooks::register();
PageHooks::register();
PostHooks::register();
TermHooks::register();
SearchHooks::register();
MenuHooks::register();
AuthorHooks::register();

/*
 * API
 */
( new SetupWizardController() )->register();
( new ExportController() )->register();
( new SyncCspController() )->register();

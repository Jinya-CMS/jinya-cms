#!/usr/bin/env bash
function composerInstall() {
    composer install
}

function designerBundle() {
    cd src/DesignerBundle/Resources/public
    yarn install --non-interactive
    tsc
}

function backendBundle() {
    cd src/BackendBundle/Resources/public
    yarn install --non-interactive
    tsc
}

function yarnInstall() {
    path=$(pwd)
    designerBundle
    cd ${path}
    backendBundle
    cd ${path}
}

function compileTheme() {
    php bin/console assetic:dump
}

composerInstall
yarnInstall
compileTheme
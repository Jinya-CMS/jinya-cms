pipeline {
  agent none
  stages {
    stage('Lint code') {
      agent {
        docker 'php:7.4-apache'
      }
      steps {
        sh '''php --version
php -r "copy(\'https://getcomposer.org/installer\', \'composer-setup.php\');"
php composer-setup.php
php -r "unlink(\'composer-setup.php\');"'''
        sh 'composer install --no-dev'
        sh '''apt update
apt install openjdk-11-jdk'''
        sh 'java -version'
      }
    }

  }
}
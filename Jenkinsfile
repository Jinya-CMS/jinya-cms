// Uses Declarative syntax to run commands inside a container.
pipeline {
    agent {
        kubernetes {
            // Rather than inline YAML, in a multibranch Pipeline you could use: yamlFile 'jenkins-pod.yaml'
            // Or, to avoid YAML:
            containerTemplate {
                name 'php'
                image 'php:7.4-apache'
                command 'sleep'
                args 'infinity'
            }
            // Can also wrap individual steps:
            // container('shell') {
            //     sh 'hostname'
            // }
            defaultContainer 'php'
        }
    }
    stages {
        stage('Lint code') {
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

pipeline {
  agent none
  stages {
    stage('Lint code') {
      agent {
        docker 'php:7.4-apache'
      }
      steps {
      }
    }

  }
}
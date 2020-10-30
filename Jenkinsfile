// Uses Declarative syntax to run commands inside a container.
pipeline {
    triggers {
        pollSCM("*/5 * * * *")
    }
    agent {
        kubernetes {
            yaml '''
apiVersion: v1
kind: Pod
spec:
  containers:
  - name: php
    image: php:7.4-apache
    command:
    - sleep
    args:
    - infinity
'''
            // Can also wrap individual steps:
            // container('shell') {
            //     sh 'hostname'
            // }
            defaultContainer 'shell'
        }
    }
    stages {
        stage('Lint code') {
            steps {
        sh '''php --version
php -r "copy(\'https://getcomposer.org/installer\', \'composer-setup.php\');"
php composer-setup.php
php -r "unlink(\'composer-setup.php\');"'''
        sh 'php composer.phar install --no-dev'
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
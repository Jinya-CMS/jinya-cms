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
            defaultContainer 'php'
        }
    }
    stages {
        stage('Lint code') {
            steps {
                sh "docker-php-ext-install pdo pdo_mysql zip unzip"
                sh "php --version"
                sh '''php -r "copy(\'https://getcomposer.org/installer\', \'composer-setup.php\');"'''
                sh "php composer-setup.php"
                sh '''php -r "unlink(\'composer-setup.php\');"'''
                sh 'php composer.phar install --no-dev'
                sh "apt update"
                sh "apt install openjdk-11-jdk"
                sh 'java -version'
            }
        }
    }
}

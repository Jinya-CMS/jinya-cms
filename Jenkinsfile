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
                sh "mkdir -p /usr/share/man/man1"
                sh "apt-get update"
                sh "apt-get install -y apt-utils"
                sh "apt-get install -y openjdk-11-jre-headless libzip-dev git wget unzip"
                sh "docker-php-ext-install pdo pdo_mysql zip"
                sh "php --version"
                sh '''php -r "copy(\'https://getcomposer.org/installer\', \'composer-setup.php\');"'''
                sh "php composer-setup.php"
                sh '''php -r "unlink(\'composer-setup.php\');"'''
                sh 'php composer.phar install --no-dev'
                sh 'java -version'
                sh 'wget -U "scannercli" -q -O /opt/sonar-scanner-cli.zip https://binaries.sonarsource.com/Distribution/sonar-scanner-cli/sonar-scanner-cli-4.5.0.2216.zip'
                sh "cd /opt && unzip sonar-scanner-cli.zip"
                sh "export SONAR_HOME /opt/sonar-scanner"
                sh 'export PATH "$PATH:/opt/sonar-scanner/bin"'
                sh "mv sonar-scanner-4.5.0.2216 /opt/sonar-scanner"
                sh "sed -i 's/#sonar\\.host\\.url=http:\\/\\/localhost:9000/sonar.host.url=https://sonarqube.imanuel.dev/g' /opt/sonar-scanner/sonar-scanner.properties"
            }
        }
    }
}

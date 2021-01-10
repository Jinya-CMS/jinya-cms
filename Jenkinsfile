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
  imagePullSecrets:
    - name: dev-imanuel-jenkins-regcred
  containers:
  - name: php
    image: registry.imanuel.dev/library/php:8.0-apache
    command:
    - sleep
    args:
    - infinity
'''
            defaultContainer 'php'
        }
    }
    stages {
        stage('Lint code') {
            steps {
                sh "mkdir -p /usr/share/man/man1"
                sh "apt-get update"
                sh "apt-get install -y apt-utils"
                sh "apt-get install -y openjdk-11-jre-headless libzip-dev git wget unzip zip nodejs yarn"
                sh "docker-php-ext-install pdo pdo_mysql zip"
                sh "php --version"
                sh '''php -r "copy(\'https://getcomposer.org/installer\', \'composer-setup.php\');"'''
                sh "php composer-setup.php"
                sh '''php -r "unlink(\'composer-setup.php\');"'''
                sh 'php composer.phar install --no-dev'
                sh 'ls -la'
                sh 'cd designer && yarn'
                sh 'cd designer && yarn build:prod'
                sh 'java -version'
                sh 'wget -U "scannercli" -q -O /opt/sonar-scanner-cli.zip https://binaries.sonarsource.com/Distribution/sonar-scanner-cli/sonar-scanner-cli-4.5.0.2216.zip'
                sh "cd /opt && unzip sonar-scanner-cli.zip"
                sh "export SONAR_HOME=/opt/sonar-scanner-4.5.0.2216"
                sh 'export PATH="$PATH:/opt/sonar-scanner-4.5.0.2216/bin"'
                sh "sed -i 's@#sonar\\.host\\.url=http:\\/\\/localhost:9000@sonar.host.url=https://sonarqube.imanuel.dev@g' /opt/sonar-scanner-4.5.0.2216/conf/sonar-scanner.properties"
                sh "/opt/sonar-scanner-4.5.0.2216/bin/sonar-scanner"
            }
        }
        stage('Archive artifact') {
            when {
                branch 'main'
            }
            steps {
                sh "zip -r ./jinya-cms.zip ./* --exclude .git/ --exclude .sonarwork/ --exclude sonar-project.properties"
                archiveArtifacts artifacts: 'jinya-cms.zip', followSymlinks: false, onlyIfSuccessful: true
            }
        }
        stage('Create and publish package') {
            when {
                buildingTag()
            }
            environment {
                JINYA_RELEASES_AUTH = credentials('releases.jinya.de')
            }
            steps {
                container('package') {
                    sh 'apt-get update'
                    sh 'apt-get install zip unzip -y'
                    sh 'cd jinya-cms && zip -r ../jinya-cms.zip ./*'
                    archiveArtifacts artifacts: 'jinya-cms.zip', followSymlinks: false
                    sh 'go run ./main.go'
                }
            }
        }
        stage('Build and push docker image') {
            when {
                buildingTag()
            }
            steps {
                container('docker') {
                    sh "docker build -t registry-hosted.imanuel.dev/jinya/jinya-cms:$TAG_NAME -f ./Dockerfile ."
                    sh "docker tag registry-hosted.imanuel.dev/jinya/jinya-cms:$TAG_NAME jinyacms/jinya-cms:$TAG_NAME"

                    withDockerRegistry(credentialsId: 'nexus.imanuel.dev', url: 'https://registry-hosted.imanuel.dev') {
                        sh "docker push registry-hosted.imanuel.dev/jinya/jinya-cms:$TAG_NAME"
                    }
                    withDockerRegistry(credentialsId: 'hub.docker.com', url: '') {
                        sh "docker push jinyacms/jinya-cms:$TAG_NAME"
                    }
                }
            }
        }
    }
}

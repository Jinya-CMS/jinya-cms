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
  - name: node
    image: registry.imanuel.dev/library/node:buster
    command:
    - sleep
    args:
    - infinity
'''
            defaultContainer 'node'
        }
    }
    stages {
        stage('Lint code') {
            steps {
                sh 'yarn'
                sh "mkdir -p /usr/share/man/man1"
                sh "apt-get update"
                sh "apt-get install -y apt-utils"
                sh "apt-get install -y openjdk-11-jre-headless libzip-dev git wget unzip zip"
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
                sh 'yarn'
                sh 'yarn build:prod'
                sh "zip -r ./jinya-designer.zip ./* --exclude .git/ --exclude src/ --exclude node_modules/ --exclude target/"
                archiveArtifacts artifacts: 'jinya-designer.zip', followSymlinks: false, onlyIfSuccessful: true
            }
        }
    }
}

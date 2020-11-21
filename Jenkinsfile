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
  - name: rust
    image: registry.imanuel.dev/library/rust:latest
    command:
    - sleep
    args:
    - infinity
'''
            defaultContainer 'rust'
        }
    }
    stages {
        stage('Lint code') {
            steps {
                sh 'rustup component add clippy'
                script {
                    try {
                        clippyOut = sh returnStdout: true, script: 'cargo clippy -- -D warnings'
                    } catch (Exception e) {
                        mail bcc: '', body: 'The build of Jinya Designer contains errors, please check.\r\n' + e.toString(), cc: '', from: 'noreply@imanuel.dev', replyTo: '', subject: '[jinya-designer] Errors in clippy check', to: 'developers@jinya.de'
                    }
                }
            }
        }
        stage('Archive artifact') {
            when {
                branch 'main'
            }
            steps {
                sh 'apt-get install nodejs'
                sh 'yarn'
                sh 'yarn build:prod'
                sh "zip -r ./jinya-designer.zip ./* --exclude .git/ --exclude src/ --exclude node_modules/ --exclude target/"
                archiveArtifacts artifacts: 'jinya-designer.zip', followSymlinks: false, onlyIfSuccessful: true
            }
        }
    }
}

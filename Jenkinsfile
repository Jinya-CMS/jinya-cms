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
  volumes:
    - name: docker-sock
      hostPath:
        path: /var/run/docker.sock
  imagePullSecrets:
    - name: dev-imanuel-jenkins-regcred
  containers:
  - name: php
    image: registry.imanuel.dev/library/php:7.4-apache
    command:
    - sleep
    args:
    - infinity
  - name: rust
    image: registry.imanuel.dev/library/rust:1.48
    command:
    - sleep
    args:
    - infinity
  - name: package
    image: registry.imanuel.dev/library/debian:buster
    command:
    - sleep
    args:
    - infinity
  - name: docker
    image: registry.imanuel.dev/library/docker:stable
    command:
    - cat
    tty: true
    volumeMounts:
    - mountPath: /var/run/docker.sock
      name: docker-sock
'''
        }
    }
    stages {
        stage('Build Jinya') {
            parallel {
                stage('Build designer') {
                    stages {
                        stage('Clone tag') {
                            when {
                                buildingTag()
                            }
                            steps {
                                container('rust') {
                                    sh "apt-get update"
                                    sh "apt-get install -y git"
                                    sh "git clone -b ${env.TAG_NAME} https://github.com/Jinya-CMS/jinya-designer.git"
                                }
                            }
                        }
                        stage('Clone main') {
                            when {
                                branch 'main'
                            }
                            steps {
                                container('rust') {
                                    sh "apt-get update"
                                    sh "apt-get install -y git"
                                    sh "git clone -b main https://github.com/Jinya-CMS/jinya-designer.git"
                                }
                            }
                        }
                        stage('Build rust') {
                            steps {
                                container('rust') {
                                    sh "apt-get update"
                                    sh 'apt-get install -y nodejs npm zip'
                                    sh 'npm install -g yarn'
                                    dir('jinya-designer') {
                                        sh 'yarn'
                                        sh 'yarn build:prod'
                                        stash excludes: '.git/**,src/**,node_modules/**,target/**', name: 'jinya-designer'
                                    }
                                }
                            }
                        }
                    }
                }
                stage('Build backend') {
                    stages {
                        stage('Clone tag') {
                            when {
                                buildingTag()
                            }
                            steps {
                                container('php') {
                                    sh "apt-get update"
                                    sh "apt-get install -y git"
                                    sh "git clone -b ${env.TAG_NAME} https://github.com/Jinya-CMS/jinya-backend.git"
                                }
                            }
                        }
                        stage('Clone main') {
                            when {
                                branch 'main'
                            }
                            steps {
                                container('php') {
                                    sh "apt-get update"
                                    sh "apt-get install -y git"
                                    sh "git clone -b main https://github.com/Jinya-CMS/jinya-backend.git"
                                }
                            }
                        }
                        stage('Build php') {
                            steps {
                                container('php') {
                                    sh "mkdir -p /usr/share/man/man1"
                                    sh "apt-get update"
                                    sh "apt-get install -y apt-utils"
                                    sh "apt-get install -y libzip-dev wget unzip zip"
                                    sh "docker-php-ext-install pdo pdo_mysql zip"
                                    sh "php --version"
                                    dir('jinya-backend') {
                                        sh '''php -r "copy(\'https://getcomposer.org/installer\', \'composer-setup.php\');"'''
                                        sh "php composer-setup.php"
                                        sh '''php -r "unlink(\'composer-setup.php\');"'''
                                        sh 'php composer.phar install --no-dev'
                                        stash excludes: '.git/**,.sonarwork/**,sonar-project.properties', name: 'jinya-backend'
                                    }
                                }
                            }
                        }
                    }
                }
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
                    unstash 'jinya-designer'
                    unstash 'jinya-backend'
                    sh 'mkdir -p ./jinya-backend/public/designer'
                    sh 'cp -r ./jinya-designer/pkg ./jinya-backend/public/'
                    sh 'cp -r ./jinya-designer/static ./jinya-backend/public/'
                    sh 'cp -r ./jinya-designer/index.html ./jinya-backend/public/designer/index.html'
                    sh 'rm -rf ./jinya-designer'
                    sh 'mv ./jinya-backend ./jinya-cms'
                    sh 'apt-get update'
                    sh 'apt-get install zip unzip curl -y'
                    sh 'cd jinya-cms && zip -r ../jinya-cms.zip ./*'
                    archiveArtifacts artifacts: 'jinya-cms.zip', followSymlinks: false
                    sh "curl -X POST -H \"Content-Type: application/octet-stream\" -H \"JinyaAuthKey: ${env.JINYA_RELEASES_AUTH}\" -d @jinya-cms.zip https://releases.jinya.de/cms/push/${env.TAG_NAME}"
                }
            }
        }
        stage('Build and push docker image') {
            when {
                buildingTag()
            }
            steps {
                container('docker') {
                    unstash 'jinya-designer'
                    unstash 'jinya-backend'
                    sh 'mkdir -p ./jinya-backend/public/designer'
                    sh 'pwd && ls -la ./jinya-cms'
                    sh "docker build -t registry-hosted.imanuel.dev/jinya/jinya-cms:$TAG_NAME ."
                    withDockerRegistry(credentialsId: 'nexus.imanuel.dev', url: 'https://registry-hosted.imanuel.dev') {
                        sh "docker push registry-hosted.imanuel.dev/jinya/jinya-cms:$TAG_NAME"
                    }

                    sh "docker tag registry-hosted.imanuel.dev/jinya/jinya-cms:$TAG_NAME jinyacms/jinya-cms:$TAG_NAME"
                    withDockerRegistry(credentialsId: 'hub.docker.com', url: '') {
                        sh "docker push jinyacms/jinya-cms:$TAG_NAME"
                    }
                }
            }
        }
    }
}


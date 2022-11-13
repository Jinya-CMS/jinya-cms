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
  containers:
    - name: php
      image: quay.imanuel.dev/dockerhub/library---php:8.1-apache
      command:
        - sleep
      args:
        - infinity
      env:
        - name: PHP_MEMORY_LIMIT
          value: 2048M
    - name: package
      image: quay.imanuel.dev/dockerhub/library---golang:latest
      command:
        - sleep
      args:
        - infinity
    - name: docker
      image: quay.imanuel.dev/dockerhub/library---docker:stable
      command:
        - cat
      tty: true
      volumeMounts:
        - mountPath: /var/run/docker.sock
          name: docker-sock
    - name: mysql
      image: quay.imanuel.dev/dockerhub/library---mysql:8
      ports:
        - containerPort: 3306
      args:
        - --transaction-isolation=READ-COMMITTED
        - --binlog-format=ROW
        - --max-connections=1000
        - --bind-address=0.0.0.0
      env:
        - name: MYSQL_DATABASE
          value: jinya
        - name: MYSQL_PASSWORD
          value: jinya
        - name: MYSQL_ROOT_PASSWORD
          value: jinya
        - name: MYSQL_USER
          value: jinya
    - name: mariadb
      image: quay.imanuel.dev/dockerhub/library---mariadb:10
      ports:
        - containerPort: 3316
      args:
        - --transaction-isolation=READ-COMMITTED
        - --binlog-format=ROW
        - --max-connections=1000
        - --bind-address=0.0.0.0
        - --port=3316
      env:
        - name: MYSQL_DATABASE
          value: jinya
        - name: MYSQL_PASSWORD
          value: jinya
        - name: MYSQL_ROOT_PASSWORD
          value: jinya
        - name: MYSQL_USER
          value: jinya
    - name: percona
      image: quay.imanuel.dev/dockerhub/percona---percona-server:8.0
      ports:
        - containerPort: 3326
      args:
        - --transaction-isolation=READ-COMMITTED
        - --binlog-format=ROW
        - --max-connections=1000
        - --bind-address=0.0.0.0
        - --port=3326
      env:
        - name: MYSQL_DATABASE
          value: jinya
        - name: MYSQL_PASSWORD
          value: jinya
        - name: MYSQL_ROOT_PASSWORD
          value: jinya
        - name: MYSQL_USER
          value: jinya
    - name: mailhog
      image: quay.imanuel.dev/dockerhub/mailhog---mailhog:latest
      command:
        - sleep
      args:
        - infinity
      ports:
        - containerPort: 1025
'''
            defaultContainer 'php'
        }
    }
    stages {
        stage('Install dependencies') {
            steps {
                sh "mkdir -p /usr/share/man/man1"
                sh "apt-get update"
                sh "apt-get install -y apt-utils"
                sh "apt-get install -y libzip-dev git wget unzip zip nodejs libicu-dev libmagickwand-dev libcurl4-openssl-dev"
                sh 'pecl install imagick pcov apcu'
                sh 'docker-php-ext-enable imagick pcov apcu'
                sh 'echo "apc.enable_cli=1" >> /usr/local/etc/php/php.ini'
                sh "docker-php-ext-install pdo pdo_mysql zip intl curl"
                sh "php --version"
                sh '''php -r "copy(\'https://getcomposer.org/installer\', \'composer-setup.php\');"'''
                sh "php composer-setup.php"
                sh '''php -r "unlink(\'composer-setup.php\');"'''
                sh 'php composer.phar install'
            }
        }
        stage('Quality assurance') {
            parallel {
                stage('Phpstan') {
                    steps {
                        sh './vendor/bin/phpstan --no-progress analyze ./src ./app ./cli ./public --memory-limit 1G'
                    }
                }
                stage('PHPUnit') {
                    steps {
                        print 'MySQL 8'
                        sh './vendor/bin/phpunit --log-junit=report.mysql.xml --configuration ./phpunit.jenkins.mysql.xml'

                        print 'MariaDB 10'
                        sh './vendor/bin/phpunit --log-junit=report.mariadb.xml --configuration ./phpunit.jenkins.mariadb.xml'

                        print 'Percona 8.0'
                        sh './vendor/bin/phpunit --log-junit=report.percona.xml --configuration ./phpunit.jenkins.percona.xml'

                        print 'Cleanup'
                        sh 'rm -rf docs guides phpdoc screenshots public/jinya-content/* public/*.webp public/*.png public/*.jpg installed.lock tmp/*'
                        dir('public') {
                            sh 'find . -type f -maxdepth 1 -name "*-*" -exec rm {} +'
                        }
                    }
                }
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
        stage('Release build') {
            parallel {
                stage('Create and publish package') {
                    when {
                        buildingTag()
                    }
                    environment {
                        JINYA_RELEASES_AUTH = credentials('releases.jinya.de')
                    }
                    steps {
                        container('package') {
                            sh "sed -i 's/%VERSION%/$TAG_NAME/g' ./defines.php"
                            sh 'apt-get update'
                            sh 'apt-get install zip unzip -y'
                            sh 'zip -r ./jinya-cms.zip ./*'
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
                            sh "sed -i 's/%VERSION%/$TAG_NAME/g' ./defines.php"

                            sh "docker build -t quay.imanuel.dev/jinya/jinya-cms:$TAG_NAME -f ./Dockerfile ."
                            sh "docker tag quay.imanuel.dev/jinya/jinya-cms:$TAG_NAME quay.imanuel.dev/jinya/jinya-cms:latest"

                            sh "docker tag quay.imanuel.dev/jinya/jinya-cms:$TAG_NAME jinyacms/jinya-cms:$TAG_NAME"
                            sh "docker tag quay.imanuel.dev/jinya/jinya-cms:$TAG_NAME jinyacms/jinya-cms:latest"

                            withDockerRegistry(credentialsId: 'quay.imanuel.dev', url: 'https://quay.imanuel.dev') {
                                sh "docker push quay.imanuel.dev/jinya/jinya-cms:$TAG_NAME"
                                sh "docker push quay.imanuel.dev/jinya/jinya-cms:latest"
                            }
                            withDockerRegistry(credentialsId: 'hub.docker.com', url: '') {
                                sh "docker push jinyacms/jinya-cms:$TAG_NAME"
                                sh "docker push jinyacms/jinya-cms:latest"
                            }
                        }
                    }
                }
            }
        }
        stage('Unstable build') {
            parallel {
                stage('Create and publish unstable package') {
                    when {
                        branch 'main'
                    }
                    environment {
                        JINYA_RELEASES_AUTH = credentials('releases.jinya.de')
                    }
                    steps {
                        container('package') {
                            sh 'sed -i "s/%VERSION%/25.1.$BUILD_NUMBER-unstable/g" ./defines.php'
                            sh 'apt-get update'
                            sh 'apt-get install zip unzip -y'
                            sh 'zip -r ./jinya-cms.zip ./*'
                            archiveArtifacts artifacts: 'jinya-cms.zip', followSymlinks: false
                            sh 'go run ./main.go -unstable'
                        }
                    }
                }
                stage('Build and push unstable docker image') {
                    when {
                        branch 'main'
                    }
                    steps {
                        container('docker') {
                            sh 'sed -i "s/%VERSION%/25.1.$BUILD_NUMBER-unstable/g" ./defines.php'
                            sh "docker build -t quay.imanuel.dev/jinya/jinya-cms:25.1.$BUILD_NUMBER-unstable -f ./Dockerfile ."

                            withDockerRegistry(credentialsId: 'quay.imanuel.dev', url: 'https://quay.imanuel.dev') {
                                sh "docker push quay.imanuel.dev/jinya/jinya-cms:25.1.$BUILD_NUMBER-unstable"
                            }
                        }
                    }
                }
            }
        }
    }
    post {
        always {
            junit 'report.mysql.xml'
            junit 'report.mariadb.xml'
            junit 'report.percona.xml'
        }
    }
}

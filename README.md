[![DeepScan grade](https://deepscan.io/api/teams/5478/projects/7310/branches/122065/badge/grade.svg)](https://deepscan.io/dashboard#view=project&tid=5478&pid=7310&bid=122065)
[![StyleCI](https://styleci.io/repos/107044619/shield?branch=develop)](https://styleci.io/repos/107044619)
[![CircleCI](https://circleci.com/gh/Jinya-CMS/Jinya-Gallery-CMS/tree/develop.svg?style=svg)](https://circleci.com/gh/Jinya-CMS/Jinya-Gallery-CMS/tree/develop)

# Jinya Gallery CMS
Jinya Gallery CMS is a simple Content Management System made for artists. The idea is to give easy ways to add your artworks and position them in galleries. The whole system is developed by me and my artist friend [Jenny Jinya](http://jenny-jinya.com). Her website is an example of the CMS in action.

# Technologies used
The CMS is based on PHP and Symfony. The frontend code is written in TypeScript and SCSS. Most of the styles are inspired by [Codrops](http://tympanus.net/codrops).

# Deploy
If you want to deploy the CMS just extract the release ZIP and navigate to the desired URL. After that the setup wizard starts and guides you through the installation.

# Update
To update the CMS just copy everything in the folder src, bin and themes to your webserver. After that delete the folder `var/cache` and then open the website.

Please see the changelog for the changes, in the case of database changes update the database via Symfony CLI with the command `php bin/console doctrine:schema:update --force`.

After that the site should be updated. 

# Contribute
If you want to contribute just fork the repository and create a pull request.

# License
[MIT License](LICENSE)
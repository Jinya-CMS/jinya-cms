# Changelog
## Release 8.0.0
### New Features
* [JGCMS-1](https://jinya.myjetbrains.com/youtrack/issue/JGCMS-1) Tiling gallery
* [JGCMS-23](https://jinya.myjetbrains.com/youtrack/issue/JGCMS-23) Replace first and lastname with artist name
* [JGCMS-38](https://jinya.myjetbrains.com/youtrack/issue/JGCMS-38) Convert artworks to png on upload
* [JGCMS-40](https://jinya.myjetbrains.com/youtrack/issue/JGCMS-40) Bulkimport for artworks 
* [JGCMS-47](https://jinya.myjetbrains.com/youtrack/issue/JGCMS-47) Menu selection in theme.yml
* [JGCMS-49](https://jinya.myjetbrains.com/youtrack/issue/JGCMS-49) Installer buggy needs rewrite
* [JGCMS-53](https://jinya.myjetbrains.com/youtrack/issue/JGCMS-53) Backbutton in video upload screen is not labeled
* [JGCMS-55](https://jinya.myjetbrains.com/youtrack/issue/JGCMS-55) YouTube is refered to as Youtube
* [JGCMS-58](https://jinya.myjetbrains.com/youtrack/issue/JGCMS-58) My jinya page 
* [JGCMS-60](https://jinya.myjetbrains.com/youtrack/issue/JGCMS-60) Updater buggy, needs rewrite
* [JGCMS-61](https://jinya.myjetbrains.com/youtrack/issue/JGCMS-61) Designer not redirected properly
* [JGCMS-62](https://jinya.myjetbrains.com/youtrack/issue/JGCMS-62) Remove slug from forms
* [JGCMS-63](https://jinya.myjetbrains.com/youtrack/issue/JGCMS-63) Generating cache kills menu in sub pages
* [JGCMS-64](https://jinya.myjetbrains.com/youtrack/issue/JGCMS-64) Page editor no links possible
* [JGCMS-68](https://jinya.myjetbrains.com/youtrack/issue/JGCMS-68) Create theme command
* [JGCMS-69](https://jinya.myjetbrains.com/youtrack/issue/JGCMS-69) Profile Picture api is broken
* [JGCMS-70](https://jinya.myjetbrains.com/youtrack/issue/JGCMS-70) Add option to select entities in theme config
* [JGCMS-72](https://jinya.myjetbrains.com/youtrack/issue/JGCMS-72) Commandline installer

## Release 2.0
### New Features
The whole designer got rewritten and is an entirely new application based on Vue.js. All features are also available via a REST api. 

## Release 1.1
### New features
* **[Trello 16](https://trello.com/c/ARW2WXCY)** Implemented the option to embed videos (e.g. YouTube) in the page via QuillJS editor
* **[Trello 17](https://trello.com/c/2AgYaDaM)** Implemented the option to embed images in the page via QuillJS editor

### Bugs fixed
* **[Trello 5](https://trello.com/c/nH73xvuZ)** The designer was partially not usable in Firefox
* **[Trello 12](https://trello.com/c/Ak4gPDiM)** Vertical galleries were not working properly in the designer
* **[Trello 13](https://trello.com/c/HNKWxzhd)** In the default frontend theme vertical galleries were missing styling
* **[Trello 15](https://trello.com/c/JbROoTs3)** In the default frontend theme the styling of horizontal galleries had issues with different heights of artworks 

### Overall improvements
* **[Trello 1](https://trello.com/c/0vsEaCWC)** Refactoring for controllers in `DesignerBundle`
* **[Trello 2](https://trello.com/c/LWXE4OB1)** Entities got a refactoring
* **[Trello 3](https://trello.com/c/E0E9nA77)** Services got their own bundle preparing them for the overall rewrite
* **[Trello 4](https://trello.com/c/B28HJy5G)** Refactoring for controllers in `BackendBundle`
* **[Trello 7](https://trello.com/c/WJlYZJ9Q)** Cleaned the TypeScript code and did first refactoring attempts 
* **[Trello 8](https://trello.com/c/8MhhQZB2)** Services got a refactoring
* **[Trello 11](https://trello.com/c/dGw6NsVu)** Refactoring for components in all bundles

## Release 1.0
Initial release shipping with the following features:

* Artist administration
* Artwork administration
* Gallery administration
* Page administration
* Theme administration
* Form administration
* Default frontend
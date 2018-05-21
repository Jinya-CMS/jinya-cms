# Changelog
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
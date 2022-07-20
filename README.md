![QUIQQER Product Presentation](bin/img/Readme.jpg)

Product Presentation
========

Business and marketing template for promoting your company or product online.


Paketname:

    quiqqer/template-presentation


Features
--------

- Smooth scroll by clicking on every element with #target href attribute (you can disable it by setting `data-qui-disableTemplateScroll="1"`)
- Responsive
- Integrated QUIQQER breadcrumb
- Page transition effects
- Search icon / input in the top navigation
- FontAwesome support
- Placeholder for your socials in the navigation and footer
    - Facebook
    - Twitter
    - Google Plus
    - YouTube
    - GitHub
    - GitLab
- Color management
- Header settings
- Each site can define if header, title and short description should be displayed (override the global setting)
- Template settings cache
- Extra layout to place the bricks on the top (for start page)


Installation
------------

The package name is: quiqqer/template-presentation


Contribute
----------

- Issue Tracker: https://dev.quiqqer.com/quiqqer/template-presentation/issues
- Source Code: https://dev.quiqqer.com/quiqqer/template-presentation


Support
-------

If you have found a bug or want to make improvements,
then you can write an e-mail to support@pcsg.de.


License
-------

PCSG QL-1.0, CC BY-NC-SA 4.0

Developer
---------

**Smooth scroll to an element**  
Smooth scroll by clicking on every `<a>` element with `href="#target"` attribute. 
You can disable it by setting `data-qui` attribute.

```html
<!-- smooth scroll on -->
<a href="www.example.com/subpage#myTarget">
  Scroll to "myTarget" element
</a>

<!-- smooth scroll off -->
<a href="www.example.com/subpage#myTarget" data-qui-disableTemplateScroll="1">
  Directly "jump" to "myTarget" element
</a>
```



**Smooth scroll on page load**  
By using `#anchor` in the url the browser jumps immediately to the desired element when
the page is loaded. You can us `#go_` anchor to auto smooth scroll to this element.

```html
<!-- standard browser behavior --> 
<a href="www.example.com/subpage#myTarget">
  Go to the address and jump to the target element
</a>

<!-- smooth scroll on page load -->
<a href="www.example.com/subpage#go_myTarget">
  Go to the address and scroll to the target element
</a>
```
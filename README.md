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

## Darstellung in Presentation v7

Die Projekteinstellung **Standarddarstellung** (`system`, `light`, `dark`) legt die
Darstellung der Website fest. System folgt der Geräteeinstellung und reagiert
nativ auf deren Änderungen. Hell und Dunkel bleiben unabhängig davon fest.

System ist die allgemeine Standarddarstellung, auch wenn die Einstellung fehlt,
leer oder ungültig ist. Die normalen `settings.xml`-Defaults enthalten abgestimmte
Hell-/Dunkelpaare für Text, Footer, Navigation und Bausteinflächen. Das reguläre
Projektsetup ergänzt fehlende Einstellungen; es gibt keinen gesonderten
Initialisierungs- oder Migrationshandler.

**Upgrade auf v7:** Die Standarddarstellung folgt nun bewusst der Geräteeinstellung.
Bereits gespeicherte Modi und konkrete Farbwerte bleiben erhalten. Ein einzelner
Farbwert gilt weiterhin in beiden Modi; ein abweichender Dunkelwert wird im
vorhandenen Farbfeld definiert. Vor dem Einsatz der dunklen Darstellung sollten
individuelle Farben, Bilder und Bausteine geprüft werden. Solange die Website dafür
noch nicht angepasst ist, kann **Hell** als feste Darstellung gewählt werden.

PHP normalisiert die Einstellung und gibt `color-scheme` früh im Kopf der Seite
als `light`, `dark` oder `light dark` aus. Diese frühe CSS-Regel bleibt vom
Zusammenfassen durch `quiqqer/cache` ausgenommen, damit die Darstellung vor dem
sichtbaren Seitenaufbau feststeht. Der Browser löst `light-dark()` passend dazu auf und
reagiert im Systemmodus selbst auf Änderungen der Geräteeinstellung. Dafür ist
kein JavaScript nötig. Voraussetzung ist ein Browser mit Unterstützung für
`color-scheme` und `light-dark()`.

### Abgrenzung zu Ticket #46

Die Einstellung **Besucher dürfen Darstellung wechseln** und ihr normalisierter
Wert bleiben als Grundlage für den späteren Umschalter erhalten. Aktuell gilt
immer die Betreiber-Vorgabe; Besucherwahl und Speicherung sind noch nicht umgesetzt.

Ticket #46 ergänzt eine minimale Inline-Initialisierung aus `localStorage` und
den Umschalterhandler im bestehenden `bin/init.js`. Eine globale Controller-API
ist dafür nicht vorgesehen.

### Farbdefaults und Grenzen

Die bestehenden Theme-Overrides behalten Vorrang vor Konfiguration und Defaults.
Flächen, Text, Rahmen, Fokus, Formulare, MegaMenu-Untermenüs, Footer und die
Template-Bausteinfarben besitzen abgestimmte Defaults. Marken- und Statusfarben
bleiben ohne expliziten Dunkelwert unverändert. Helle Varianten von Statusmeldungen,
Buttons und Badges behalten bewusst zusammenpassende Vorder-/Hintergrundfarben.

Konkrete Projekt-, Seiten- und Bausteinfarben sowie eigenes CSS werden nicht
umgedeutet. Ein ausdrücklich schwarzer Text bleibt beispielsweise schwarz und muss
bei Verwendung auf dunklen Flächen durch ein Farbpaar oder eine geeignete
Palettenrolle ergänzt werden. Hintergrundbilder, Inhaltsbilder und Logos bleiben
unverändert; es gibt keine automatische Farbumkehr. Die gesonderte Logo-Auswahl
gehört zu #47. Fremdpakete ohne semantische Farbvariablen bzw. mit festen eigenen
Farben brauchen eine eigene Anpassung. Bildüberlagerungen und transparente
Navigation müssen mit den tatsächlichen Bildern auf Lesbarkeit geprüft werden.

PHP-Tests einschließlich Persistenztests mit isoliertem Core-Bootstrap:
`./tools/phpunit --bootstrap ../core/tests/phpunit-bootstrap.php tests/QUI/TemplatePresentation`.

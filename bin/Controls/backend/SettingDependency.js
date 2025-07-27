/**
 * Control to manage dependencies between form inputs (checkboxes and select boxes) and their related options.
 * This control listens to changes on checkboxes or select boxes and shows or hides dependent table rows accordingly.
 *
 * When used with a checkbox, options are displayed when the checkbox is checked and hidden when unchecked.
 * When used with a select box, options are displayed if the selected value matches any of the accepted values
 * defined in the `data-dependency-options` attribute (comma-separated list of valid options).
 *
 * WARNING: it does not work with select boxes defined in bricks.xml...
 *
 * @module package/quiqqer/template-presentation/bin/Controls/backend/SettingDependency
 * @author Michael Danielczok
 *
 */

define('package/quiqqer/template-presentation/bin/Controls/backend/SettingDependency', [
    'qui/controls/Control'
], function(QUIControl)
{
    'use strict';

    return new Class({

        Extends: QUIControl,
        Type: 'package/quiqqer/template-presentation/bin/Controls/backend/SettingDependency',

        Binds: [
            '$onImport'
        ],

        initialize: function(options) {
            this.parent(options);

            this.type = null;
            this.variableOptions = null; // NodeList of options

            this.addEvents({
                onImport: this.$onImport
            });
        },

        /**
         * event: on import
         */
        $onImport: function() {
            this.$Input = this.getElm();
            const ParentTable = this.getElm().closest('table');

            if (!ParentTable) {
                return;
            }

            const target = 'templatePresentation-' + this.$Input.name;
            this.variableOptions = ParentTable.querySelectorAll('[data-dependency="' + target + '"]');

            if (this.$Input.type === 'checkbox') {
                this.$Input.addEventListener('change', (e) => {
                    if (e.target.checked) {
                        this.$showOptions();
                    } else {
                        this.$hideOptions();
                    }
                });

                // initial state
                if (!this.$Input.checked) {
                    this.$hideOptions();
                }

                return;
            }

            if (this.$Input.nodeName === 'SELECT') {
                this.$Input.addEventListener('change', (e) => {
                    const option = e.target.value;

                    const dependencyOptions = this.$Input.getAttribute('data-dependency-options') || '';

                    const acceptedValues = dependencyOptions.split(',').map(value => value.trim());

                    if (acceptedValues.includes(option)) {
                        this.$showOptions();
                    } else {
                        this.$hideOptions();
                    }
                });
            }
        },

        /**
         * Show options
         */
        $showOptions: function() {
            this.variableOptions.forEach((Option) => {
                const Node = Option.closest('tr');

                if (!Node) {
                    return;
                }

                Node.style.display = null;
            });
        },

        /**
         * Hide options
         */
        $hideOptions: function() {
            this.variableOptions.forEach((Option) => {
                const Node = Option.closest('tr');

                if (!Node) {
                    return;
                }

                Node.style.display = 'none';
            });
        }
    });
});

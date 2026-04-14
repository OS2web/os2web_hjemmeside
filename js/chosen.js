/**
 * @file
 * Attaches behaviors for the adjust Chosen module.
 */


(function (Drupal, once) {
  'use strict';

  Drupal.behaviors.os2webHjemmesideChosen = {
    attach(context) {
      once('os2webHjemmesideChosen', 'select#edit-menu-menu-parent', context)
          .forEach((element) => {
            if (typeof window.jQuery !== 'undefined' && typeof window.jQuery.fn.chosen !== 'undefined') {
              window.jQuery(element).chosen({
                width: '100%',
              });
            }
          });
    }
  };

})(Drupal, once);

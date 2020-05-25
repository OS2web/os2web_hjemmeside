/**
 * @file
 * Attaches behaviors for the adjust Chosen module.
 */

(function ($, Drupal, drupalSettings) {
  'use strict';

  Drupal.behaviors.os2webHjemmesideChosen = {
    attach: function (context, settings) {
      $('select#edit-menu-menu-parent').once('chosen').chosen({width: "100%"});
    }
  }
})(jQuery, Drupal, drupalSettings);

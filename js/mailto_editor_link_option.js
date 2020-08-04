(function (Drupal, $) {

  'use strict';

  Drupal.behaviors.mailto_editor_link_option = {
    attach: function (context, settings) {
      var $href = $('.editor-link-dialog input[data-drupal-selector="edit-attributes-href"]');
      var $mailto = $('.editor-link-dialog details[data-drupal-selector="edit-advanced"] .mailto-option');
      // Update href value with mailto prefix.
      $mailto.once('mailto_editor_link_option')
        .on('change', function () {
          var $value = $href.val().replace('mailto:', '');
          if (this.checked) {
            $value = 'mailto:' + $value
          }
          $href.val($value);
      });
      $href.once('mailto_editor_link_option')
        .on('keyup', function () {
          var $value = this.value.replace('mailto:', '');
          if (/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test($value)) {
            $mailto.removeAttr('disabled');
            return;
          }
          $mailto.attr('disabled', 1);
        }).keyup();

    }
  };

}(Drupal, jQuery));

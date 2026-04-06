(function ($) {
  'use strict';

  function post(data) {
    return $.post(toolverseAdmin.ajaxUrl, $.extend({ action: 'toolverse_admin_tool_settings', nonce: toolverseAdmin.nonce }, data));
  }

  $(document).on('change', '.tv-tool-enabled', function () {
    var $row = $(this).closest('tr');
    post({
      slug: $(this).data('slug'),
      is_enabled: $(this).is(':checked') ? 1 : 0,
      is_pro: $row.find('.tv-tool-pro').is(':checked') ? 1 : 0,
      daily_limit: $row.find('.tv-limit-input').val()
    });
  });

  $(document).on('change', '.tv-tool-pro', function () {
    var $row = $(this).closest('tr');
    post({
      slug: $(this).data('slug'),
      is_enabled: $row.find('.tv-tool-enabled').is(':checked') ? 1 : 0,
      is_pro: $(this).is(':checked') ? 1 : 0,
      daily_limit: $row.find('.tv-limit-input').val()
    });
  });

  $(document).on('change', '.tv-limit-input', function () {
    var $row = $(this).closest('tr');
    post({
      slug: $(this).data('slug'),
      is_enabled: $row.find('.tv-tool-enabled').is(':checked') ? 1 : 0,
      is_pro: $row.find('.tv-tool-pro').is(':checked') ? 1 : 0,
      daily_limit: $(this).val()
    });
  });

  function applyFilters() {
    var q = ($('#tool-search').val() || '').toLowerCase();
    var c = $('#category-filter').val() || '';
    $('#tools-table tbody tr').each(function () {
      var $tr = $(this);
      var name = $tr.data('name') || '';
      var cat = $tr.data('cat') || '';
      var ok = (!q || name.indexOf(q) !== -1) && (!c || cat === c);
      $tr.toggle(ok);
    });
  }

  $('#tool-search').on('input', applyFilters);
  $('#category-filter').on('change', applyFilters);
})(jQuery);

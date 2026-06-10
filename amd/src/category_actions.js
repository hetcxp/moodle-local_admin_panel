define(['jquery', 'core/notification'], function($, Notification) {
    var updateBulkButtonsState = function() {
        var selected = $('.ap-category-checkbox:checked');
        var count = selected.length;
        $('#ap-category-selected-count').text(count);
        
        if (count > 0) {
            $('.ap-bulk-actions').slideDown(200);
        } else {
            $('.ap-bulk-actions').slideUp(200);
        }
        
        var hasVisible = false;
        var hasHidden = false;
        var canDeleteAll = true;
        
        selected.each(function() {
            var isVisible = $(this).data('category-visible');
            if (isVisible == 1) {
                hasVisible = true;
            } else {
                hasHidden = true;
            }
            if ($(this).data('candelete') != 1) {
                canDeleteAll = false;
            }
        });
        
        if (hasVisible && hasHidden) {
            $('#ap-cat-bulk-hide-btn, #ap-cat-bulk-show-btn').hide();
        } else if (hasVisible) {
            $('#ap-cat-bulk-hide-btn').show();
            $('#ap-cat-bulk-show-btn').hide();
        } else if (hasHidden) {
            $('#ap-cat-bulk-hide-btn').hide();
            $('#ap-cat-bulk-show-btn').show();
        } else {
            $('#ap-cat-bulk-hide-btn, #ap-cat-bulk-show-btn').hide();
        }

        if (!canDeleteAll) {
            $('#ap-cat-bulk-delete-btn').hide();
        } else {
            $('#ap-cat-bulk-delete-btn').show();
        }
    };

    return {
        init: function() {
            // Select all toggle
            $('#ap-select-all-categories').on('change', function() {
                var isChecked = $(this).is(':checked');
                $('.ap-category-checkbox').prop('checked', isChecked);
                updateBulkButtonsState();
            });

            // Individual checkbox change
            $('.ap-category-checkbox').on('change', function() {
                var allChecked = $('.ap-category-checkbox').length === $('.ap-category-checkbox:checked').length;
                $('#ap-select-all-categories').prop('checked', allChecked);
                updateBulkButtonsState();
            });

            // Bulk actions
            $('.ap-category-bulk-btn').on('click', function(e) {
                e.preventDefault();
                var action = $(this).data('action');
                var selected = $('.ap-category-checkbox:checked');
                
                if (selected.length === 0) return;
                
                var executeBulkAction = function() {
                    var form = $('<form>', {
                        'action': 'action.php',
                        'method': 'POST'
                    });
                    
                    form.append($('<input>', { 'name': 'action', 'value': action, 'type': 'hidden' }));
                    form.append($('<input>', { 'name': 'tab', 'value': 'courses', 'type': 'hidden' }));
                    form.append($('<input>', { 'name': 'subtab', 'value': 'categories', 'type': 'hidden' }));
                    form.append($('<input>', { 'name': 'sesskey', 'value': M.cfg.sesskey, 'type': 'hidden' }));
                    
                    selected.each(function() {
                        form.append($('<input>', { 'name': 'categoryids[]', 'value': $(this).val(), 'type': 'hidden' }));
                    });
                    
                    $(document.body).append(form);
                    form.submit();
                };

                var titles = {
                    'deletecategory': 'Eliminar Categorías',
                    'hidecategory': 'Ocultar Categorías',
                    'showcategory': 'Mostrar Categorías'
                };
                var messages = {
                    'deletecategory': '¿Estás seguro de que deseas eliminar las categorías seleccionadas? Esta acción no se puede deshacer.',
                    'hidecategory': '¿Estás seguro de que deseas ocultar las categorías seleccionadas?',
                    'showcategory': '¿Estás seguro de que deseas mostrar las categorías seleccionadas?'
                };
                var confirmBtns = {
                    'deletecategory': 'Eliminar',
                    'hidecategory': 'Ocultar',
                    'showcategory': 'Mostrar'
                };

                Notification.confirm(
                    titles[action] || 'Confirmar Acción Masiva',
                    messages[action] || '¿Estás seguro de que deseas aplicar esta acción a las categorías seleccionadas?',
                    confirmBtns[action] || 'Confirmar',
                    'Cancelar',
                    executeBulkAction
                );
            });

            // Individual actions
            $('.ap-category-action').on('click', function(e) {
                var action = $(this).data('ap-action');
                var categoryid = $(this).data('categoryid');
                
                if (action === 'edit') {
                    e.preventDefault();
                    var name = $(this).data('categoryname');
                    var parent = $(this).data('categoryparent');
                    var description = $(this).data('categorydesc');
                    
                    $('#edit_categoryid').val(categoryid);
                    $('#edit_name').val(name);
                    $('#edit_parent').val(parent);
                    $('#edit_description').val(description);
                    
                    $('#apEditCategoryModal').modal('show');
                    return;
                }
                
                if (action === 'delete') {
                    e.preventDefault();
                    Notification.confirm(
                        'Eliminar Categoría',
                        '¿Estás seguro de que deseas eliminar esta categoría? Esta acción no se puede deshacer.',
                        'Eliminar',
                        'Cancelar',
                        function() {
                            // Proceed with deletion
                            var form = $('<form>', {
                                'action': 'action.php',
                                'method': 'POST'
                            }).append($('<input>', {
                                'name': 'action',
                                'value': 'deletecategory',
                                'type': 'hidden'
                            })).append($('<input>', {
                                'name': 'categoryids[]',
                                'value': categoryid,
                                'type': 'hidden'
                            })).append($('<input>', {
                                'name': 'tab',
                                'value': 'courses',
                                'type': 'hidden'
                            })).append($('<input>', {
                                'name': 'subtab',
                                'value': 'categories',
                                'type': 'hidden'
                            })).append($('<input>', {
                                'name': 'sesskey',
                                'value': M.cfg.sesskey,
                                'type': 'hidden'
                            }));
                            $(document.body).append(form);
                            form.submit();
                        }
                    );
                    return;
                }
                
                if (action === 'hide' || action === 'show') {
                    e.preventDefault();
                    var actionName = action === 'hide' ? 'hidecategory' : 'showcategory';
                    var actionStr = action === 'hide' ? 'ocultar' : 'mostrar';
                    Notification.confirm(
                        'Confirmación',
                        '¿Estás seguro de que deseas ' + actionStr + ' esta categoría?',
                        'Confirmar',
                        'Cancelar',
                        function() {
                            var form = $('<form>', {
                                'action': 'action.php',
                                'method': 'POST'
                            }).append($('<input>', {
                                'name': 'action',
                                'value': actionName,
                                'type': 'hidden'
                            })).append($('<input>', {
                                'name': 'categoryids[]',
                                'value': categoryid,
                                'type': 'hidden'
                            })).append($('<input>', {
                                'name': 'tab',
                                'value': 'courses',
                                'type': 'hidden'
                            })).append($('<input>', {
                                'name': 'subtab',
                                'value': 'categories',
                                'type': 'hidden'
                            })).append($('<input>', {
                                'name': 'sesskey',
                                'value': M.cfg.sesskey,
                                'type': 'hidden'
                            }));
                            $(document.body).append(form);
                            form.submit();
                        }
                    );
                }
            });
            
            updateBulkButtonsState(); // Initialize state
        }
    };
});

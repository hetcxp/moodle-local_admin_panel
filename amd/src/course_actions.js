define([
    'jquery',
    'core/notification'
], function($, Notification) {
    var Config = M.cfg;

    var init = function() {
        var updateBulkActionsState = function() {
            var selectedCheckboxes = $('.ap-course-checkbox:checked');
            var count = selectedCheckboxes.length;
            
            $('#ap-selected-count').text(count);
            
            if (count > 0) {
                $('.ap-bulk-actions').slideDown(200);
            } else {
                $('.ap-bulk-actions').slideUp(200);
            }
            
            // Comprobar estados mixtos para botones de visibilidad
            var hasVisible = false;
            var hasHidden = false;
            
            selectedCheckboxes.each(function() {
                var isVisible = $(this).data('course-visible');
                if (isVisible == 1) {
                    hasVisible = true;
                } else {
                    hasHidden = true;
                }
            });
            
            if (hasVisible && hasHidden) {
                $('#ap-bulk-hide-btn, #ap-bulk-show-btn').hide();
            } else if (hasVisible) {
                $('#ap-bulk-hide-btn').show();
                $('#ap-bulk-show-btn').hide();
            } else if (hasHidden) {
                $('#ap-bulk-hide-btn').hide();
                $('#ap-bulk-show-btn').show();
            } else {
                $('#ap-bulk-hide-btn, #ap-bulk-show-btn').hide();
            }
        };

        // Select All
        $('#ap-select-all-courses').on('change', function() {
            var isChecked = $(this).prop('checked');
            $('.ap-course-checkbox').prop('checked', isChecked);
            updateBulkActionsState();
        });

        // Individual checkboxes
        $('.ap-course-checkbox').on('change', function() {
            var allChecked = $('.ap-course-checkbox:not(:checked)').length === 0;
            $('#ap-select-all-courses').prop('checked', allChecked);
            updateBulkActionsState();
        });

        // Ejecutar acción de formulario
        var submitBulkAction = function(actionType, selectedIds, extraParams) {
            var form = $('<form>', {
                'method': 'POST',
                'action': Config.wwwroot + '/local/admin_panel/action.php'
            });
            form.append($('<input>', {'type': 'hidden', 'name': 'sesskey', 'value': Config.sesskey}));
            form.append($('<input>', {'type': 'hidden', 'name': 'action', 'value': actionType}));
            
            if (extraParams) {
                $.each(extraParams, function(key, value) {
                    form.append($('<input>', {'type': 'hidden', 'name': key, 'value': value}));
                });
            }
            
            $.each(selectedIds, function(i, id) {
                form.append($('<input>', {'type': 'hidden', 'name': 'courseids[]', 'value': id}));
            });
            
            $('body').append(form);
            form.submit();
        };

        // Bulk action buttons
        $('.ap-bulk-action-btn').on('click', function(e) {
            e.preventDefault();
            var trigger = $(this);
            var actionType = trigger.data('action');
            
            if (trigger.prop('disabled')) {
                return;
            }
            
            var selectedCheckboxes = $('.ap-course-checkbox:checked');
            var selectedIds = [];
            var courseNames = [];
            
            selectedCheckboxes.each(function() {
                selectedIds.push($(this).val());
                courseNames.push($(this).data('course-name'));
            });
            
            if (selectedIds.length === 0) {
                return;
            }
            
            var courseListHtml = '<ul style="text-align: left; max-height: 200px; overflow-y: auto;">';
            $.each(courseNames, function(i, name) {
                courseListHtml += '<li>' + name + '</li>';
            });
            courseListHtml += '</ul>';

            if (actionType === 'movecourse') {
                var uniqueId = 'ap-bulk-cat-select-' + Math.floor(Math.random() * 1000000);
                var modalBody = $('#ap-move-modal-template').html().replace(/__AP_CAT_ID__/g, uniqueId);
                modalBody = '<div>Moviendo ' + selectedIds.length + ' cursos:</div>' + courseListHtml + '<hr>' + modalBody;
                
                Notification.confirm(
                    'Mover cursos masivamente',
                    modalBody,
                    'Mover seleccionados',
                    'Cancelar',
                    function() {
                        var newCat = $('#' + uniqueId).val();
                        if (!newCat) {
                            newCat = $('.ap-new-category-select').val();
                        }
                        if (newCat) {
                            submitBulkAction(actionType, selectedIds, { 'newcategoryid': newCat });
                        }
                    }
                );
                return;
            }

            var acciones = {
                'deletecourse': 'eliminar',
                'hidecourse': 'ocultar',
                'showcourse': 'mostrar'
            };
            var accionStr = acciones[actionType] || 'modificar';
            var bodyString = '<p>¿Estás seguro de que deseas ' + accionStr + ' los siguientes ' + selectedIds.length + ' cursos?</p>' + courseListHtml;

            Notification.confirm(
                'Confirmación Masiva',
                bodyString,
                'Confirmar Acción',
                'Cancelar',
                function() {
                    submitBulkAction(actionType, selectedIds);
                }
            );
        });

        // Single actions logic preserved
        $('body').on('click', '.ap-course-action', function(e) {
            var trigger = $(this);
            var actionType = trigger.data('ap-action');
            var url = trigger.attr('href') || trigger.data('url');

            if (actionType === 'view') {
                return;
            }

            e.preventDefault();

            if (actionType === 'move') {
                var uniqueId = 'ap-cat-select-' + Math.floor(Math.random() * 1000000);
                var modalBody = $('#ap-move-modal-template').html().replace(/__AP_CAT_ID__/g, uniqueId);
                
                Notification.confirm(
                    'Mover curso de categoría',
                    modalBody,
                    'Mover',
                    'Cancelar',
                    function() {
                        var newCat = $('#' + uniqueId).val();
                        if (!newCat) {
                            newCat = $('.ap-new-category-select').val();
                        }
                        
                        if (newCat && url && url !== '#') {
                            var separator = url.indexOf('?') !== -1 ? '&' : '?';
                            var finalUrl = url + separator + 'newcategoryid=' + encodeURIComponent(newCat);
                            window.location.href = finalUrl;
                        }
                    }
                );
                return;
            }

            var acciones = {
                'delete': 'eliminar',
                'hide': 'ocultar',
                'show': 'mostrar'
            };
            var accionStr = acciones[actionType] || 'modificar';
            var bodyString = '¿Estás seguro de que deseas ' + accionStr + ' este curso?';

            Notification.confirm(
                'Confirmación',
                bodyString,
                'Confirmar',
                'Cancelar',
                function() {
                    if (url && url !== '#') {
                        window.location.href = url;
                    }
                }
            );
        });
    };

    return {
        init: init
    };
});

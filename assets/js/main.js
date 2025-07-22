(function($) {
    'use strict';

    const LDAnalyticsWidgets = {
        
        init: function() {
            this.widgets = $('.ld-analytics-widget:not(.ld-analytics-filters)');
            this.filterWidget = $('.ld-analytics-filters');
            this.dataTables = {}; // Armazena instâncias do DataTable

            if (this.filterWidget.length) {
                this.initFilters();
            } else {
                this.loadAllWidgets({});
            }
        },

        initFilters: function() {
            const self = this;
            const $courseSelect = self.filterWidget.find('.ld-course-filter');
            
            const endDate = new Date();
            const startDate = new Date();
            startDate.setDate(endDate.getDate() - 30);
            self.filterWidget.find('.ld-date-start').val(startDate.toISOString().split('T')[0]);
            self.filterWidget.find('.ld-date-end').val(endDate.toISOString().split('T')[0]);

            self.filterWidget.find('#ld-apply-filters').on('click', function() {
                self.applyFilters();
            });

            this.populateCourseFilter($courseSelect).done(function() {
                self.applyFilters();
            });
        },
        
        populateCourseFilter: function($selectElement) {
            return $.ajax({
                url: ldAnalytics.ajax_url,
                type: 'POST',
                data: {
                    action: 'ld_analytics_get_courses',
                    nonce: ldAnalytics.nonce
                },
                success: function(response) {
                    if (response.success) {
                        $.each(response.data, function(id, title) {
                            $selectElement.append(new Option(title, id));
                        });
                    }
                }
            });
        },

        applyFilters: function() {
            const filters = {
                start_date: this.filterWidget.find('.ld-date-start').val(),
                end_date: this.filterWidget.find('.ld-date-end').val(),
                course_id: this.filterWidget.find('.ld-course-filter').val(),
            };
            this.loadAllWidgets(filters);
        },

        loadAllWidgets: function(filters) {
            this.widgets.each((index, el) => {
                const $widget = $(el);
                $widget.addClass('is-loading');
                this.loadWidgetData($widget, filters);
            });
        },

        loadWidgetData: function($widget, filters) {
            const widgetType = $widget.data('widget-type');
            const metric = $widget.data('metric');

            $.ajax({
                url: ldAnalytics.ajax_url,
                type: 'POST',
                data: {
                    action: 'ld_analytics_get_widget_data',
                    nonce: ldAnalytics.nonce,
                    widget_type: widgetType,
                    metric: metric,
                    filters: filters
                },
                success: (response) => {
                    if (response.success && this.renderers[widgetType]) {
                        // Passa a 'metric' para a função de renderização
                        this.renderers[widgetType]($widget, response.data, metric);
                    } else {
                        console.error('Erro no Widget:', response.data ? response.data.message : 'Resposta sem sucesso.');
                    }
                },
                error: (jqXHR, textStatus, errorThrown) => {
                    console.error('Erro AJAX:', textStatus, errorThrown);
                },
                complete: () => {
                    $widget.removeClass('is-loading');
                }
            });
        },

        renderers: {
            kpi_card: function($widget, data) {
                $widget.find('.ld-kpi-label').text(data.label);
                let value = data.value;
                if (data.is_percentage) {
                    value = `${parseFloat(value).toFixed(1)}%`;
                } else {
                    value = !isNaN(parseInt(value, 10)) ? parseInt(value, 10).toLocaleString('pt-BR') : value;
                }
                $widget.find('.ld-kpi-value').text(value);
            },
            
            line_chart: function($widget, data) {
                const canvas = $widget.find('canvas')[0];
                if (!canvas) return;
                if (canvas.chartInstance) canvas.chartInstance.destroy();
                canvas.chartInstance = new Chart(canvas.getContext('2d'), { type: 'line', data: data, options: { responsive: true, maintainAspectRatio: false }});
            },

            bar_chart: function($widget, data) {
                const canvas = $widget.find('canvas')[0];
                if (!canvas) return;
                if (canvas.chartInstance) canvas.chartInstance.destroy();
                canvas.chartInstance = new Chart(canvas.getContext('2d'), { type: 'bar', data: data, options: { responsive: true, maintainAspectRatio: false, indexAxis: 'y' }});
            },
            
            data_table: function($widget, data, metric) {
                const tableId = $widget.find('table').attr('id');
                if (!tableId) return;

                let columns = [];

                // Define as colunas corretas com base na métrica recebida
                if (metric === 'student_report') {
                    columns = [
                        { data: 'display_name', title: 'Nome' },
                        { data: 'user_email', title: 'Email' },
                        { data: 'course_count', title: 'Cursos Inscritos' },
                        { data: 'last_activity', title: 'Última Atividade' }
                    ];
                } else if (metric === 'course_effectiveness') {
                    columns = [
                        { data: 'course_name', title: 'Curso' },
                        { data: 'enrollments', title: 'Inscrições' },
                        { data: 'completions', title: 'Conclusões' },
                        { 
                            data: 'completion_rate', 
                            title: 'Taxa de Conclusão',
                            render: function(data, type, row) {
                                return parseFloat(data).toFixed(2) + ' %';
                            }
                        }
                    ];
                }

                // Se a tabela já existe, destrói-a para poder recriá-la com as novas colunas
                if ($.fn.DataTable.isDataTable('#' + tableId)) {
                    LDAnalyticsWidgets.dataTables[tableId].destroy();
                    $('#' + tableId + ' thead').empty(); 
                }

                // Inicializa a DataTable com os dados e as colunas corretas
                LDAnalyticsWidgets.dataTables[tableId] = $('#' + tableId).DataTable({
                    data: data,
                    columns: columns,
                    responsive: true,
                    language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json' }
                });
            }
        }
    };

    $(document).ready(() => LDAnalyticsWidgets.init());
    $(window).on('elementor/frontend/init', () => {
        elementorFrontend.hooks.addAction('frontend/elementor_ready/global', () => LDAnalyticsWidgets.init());
    });

})(jQuery);

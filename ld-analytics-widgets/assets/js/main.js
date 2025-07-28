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
                // Se não houver widget de filtro na página, carrega todos os widgets
                this.loadAllWidgets({});
            }

            // Inicializa DataTables para tabelas existentes no carregamento da página
            this.initDataTables();
        },

        initFilters: function() {
            const self = this;
            const $courseSelect = self.filterWidget.find('.ld-course-filter');
            
            // Define as datas iniciais e finais padrão (últimos 30 dias)
            const endDate = new Date();
            const startDate = new Date();
            startDate.setDate(endDate.getDate() - 30); // 30 dias atrás

            // Formata as datas para o formato YYYY-MM-DD para o input type="date"
            self.filterWidget.find('.ld-date-start').val(startDate.toISOString().split('T')[0]);
            self.filterWidget.find('.ld-date-end').val(endDate.toISOString().split('T')[0]);

            // Adiciona o listener para o botão de aplicar filtros
            self.filterWidget.find('#ld-apply-filters').on('click', function() {
                self.applyFilters();
            });

            // Popula o filtro de cursos e então aplica os filtros iniciais
            this.populateCourseFilter($courseSelect).done(function() {
                self.applyFilters();
            });
        },
        
        populateCourseFilter: function($selectElement) {
            // Requisição AJAX para obter a lista de cursos
            return $.ajax({
                url: ldAnalytics.ajax_url, // URL de AJAX do WordPress
                type: 'POST',
                data: {
                    action: 'ld_analytics_get_courses', // Ação AJAX para buscar cursos
                    nonce: ldAnalytics.nonce // Nonce de segurança
                },
                success: function(response) {
                    if (response.success) {
                        // Adiciona a opção "Todos os Cursos"
                        $selectElement.html('<option value="all">' + ldAnalytics.all_courses_label + '</option>');
                        // Preenche o select com os cursos retornados
                        $.each(response.data, function(id, title) {
                            $selectElement.append($('<option>', {
                                value: id,
                                text: title
                            }));
                        });
                    } else {
                        console.error('Erro ao buscar cursos:', response.data.message);
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.error('Erro na requisição AJAX para cursos:', textStatus, errorThrown);
                }
            });
        },

        applyFilters: function() {
            const self = this;
            // Coleta os valores dos filtros
            const filters = {
                start_date: self.filterWidget.find('.ld-date-start').val(),
                end_date: self.filterWidget.find('.ld-date-end').val(),
                course_id: self.filterWidget.find('.ld-course-filter').val()
            };
            self.loadAllWidgets(filters); // Carrega/atualiza todos os widgets com os novos filtros
        },

        loadAllWidgets: function(filters) {
            const self = this;
            // Itera sobre todos os widgets de análise na página
            self.widgets.each(function() {
                const $widget = $(this);
                const widgetType = $widget.data('widget-type');
                const metric = $widget.data('metric');
                const $loader = $widget.find('.ld-analytics-loader');
                const $content = $widget.find('.widget-content, .ld-kpi-card-inner, .ld-data-table-container');

                $loader.show(); // Mostra o loader
                $content.hide(); // Esconde o conteúdo enquanto carrega

                // Faz a requisição AJAX para obter os dados do widget
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
                    success: function(response) {
                        if (response.success) {
                            self.renderWidget($widget, widgetType, metric, response.data);
                        } else {
                            console.error('Erro ao buscar dados para o widget ' + metric + ':', response.data.message);
                            $content.html('<p class="error-message">' + response.data.message + '</p>');
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.error('Erro na requisição AJAX para o widget ' + metric + ':', textStatus, errorThrown);
                        $content.html('<p class="error-message">Erro ao carregar dados. Tente novamente.</p>');
                    },
                    complete: function() {
                        $loader.hide(); // Esconde o loader
                        $content.show(); // Mostra o conteúdo
                    }
                });
            });
        },

        renderWidget: function($widget, widgetType, metric, data) {
            switch (widgetType) {
                case 'kpi_card':
                    // Renderiza o cartão KPI
                    $widget.find('.ld-kpi-label').text(data.label);
                    // Formata o valor se for porcentagem
                    let value = data.value;
                    if (data.is_percentage) {
                        value = parseFloat(value).toFixed(2) + '%';
                    }
                    $widget.find('.ld-kpi-value').text(value);
                    break;
                case 'line_chart':
                case 'bar_chart':
                    // Renderiza gráficos (Chart.js)
                    const canvasId = $widget.find('canvas').attr('id');
                    const ctx = document.getElementById(canvasId).getContext('2d');

                    // Destrói o gráfico existente antes de criar um novo
                    if (LDAnalyticsWidgets.charts[canvasId]) {
                        LDAnalyticsWidgets.charts[canvasId].destroy();
                    }

                    const chartType = (widgetType === 'line_chart') ? 'line' : 'bar';
                    LDAnalyticsWidgets.charts[canvasId] = new Chart(ctx, {
                        type: chartType,
                        data: data,
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: true,
                                    position: 'top',
                                },
                                title: {
                                    display: false, // O título já está no HTML do widget
                                },
                            },
                            scales: {
                                y: {
                                    beginAtZero: true
                                }
                            }
                        }
                    });
                    break;
                case 'data_table':
                    // Renderiza a tabela de dados (DataTables.js)
                    this.renderDataTable($widget, data);
                    break;
            }
        },

        // NOVO MÉTODO: Inicializa DataTables para tabelas já presentes no DOM
        initDataTables: function() {
            const self = this;
            $('.ld-analytics-data-table').each(function() {
                const $table = $(this);
                // Verifica se a tabela já foi inicializada para evitar re-inicialização
                if (!$.fn.DataTable.isDataTable($table)) {
                    // Carrega os dados para esta tabela específica
                    const widgetType = $table.data('widget-type');
                    const metric = $table.data('metric');
                    const columns = $table.data('columns'); // Obtém a configuração das colunas do HTML

                    if (!columns || columns.length === 0) {
                        console.warn('DataTables: Configuração de colunas não encontrada para o widget.', $table);
                        return;
                    }

                    // Define os filtros (data inicial, final, curso)
                    // Tenta encontrar os filtros no widget de filtros principal
                    let filters = {};
                    const $filterWidget = $('.ld-analytics-filters');
                    if ($filterWidget.length) {
                        filters = {
                            start_date: $filterWidget.find('.ld-date-start').val(),
                            end_date: $filterWidget.find('.ld-date-end').val(),
                            course_id: $filterWidget.find('.ld-course-filter').val(),
                        };
                    }

                    // Faz a requisição AJAX para obter os dados do widget
                    $.ajax({
                        url: ldAnalytics.ajax_url,
                        method: 'POST',
                        data: {
                            action: 'ld_analytics_get_widget_data',
                            nonce: ldAnalytics.nonce,
                            widget_type: widgetType,
                            metric: metric,
                            filters: filters
                        },
                        beforeSend: function() {
                            $table.closest('.ld-data-table-container').find('.ld-analytics-loader').show();
                            $table.hide();
                        },
                        success: function(response) {
                            if (response.success) {
                                const data = response.data;

                                // Destrói a instância existente do DataTables se houver, para poder recriá-la
                                if ($.fn.DataTable.isDataTable($table)) {
                                    $table.DataTable().destroy();
                                    $table.empty(); // Limpa o conteúdo da tabela
                                }

                                // Inicializa a DataTable com os dados e as colunas corretas
                                LDAnalyticsWidgets.dataTables[$table.attr('id')] = $table.DataTable({
                                    data: data,
                                    columns: columns, // Usa as colunas passadas do PHP
                                    responsive: true,
                                    language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json' },
                                    order: [], // Desabilita ordenação inicial para evitar erros de coluna
                                    // Adicione aqui outras opções que você precisar para o DataTables
                                });

                            } else {
                                console.error('Erro ao buscar dados:', response.data.message);
                                $table.closest('.ld-data-table-container').html('<p class="error-message">' + response.data.message + '</p>');
                            }
                        },
                        error: function(jqXHR, textStatus, errorThrown) {
                            console.error('Erro na requisição AJAX:', textStatus, errorThrown);
                            $table.closest('.ld-data-table-container').html('<p class="error-message">Erro de conexão ao buscar dados.</p>');
                        },
                        complete: function() {
                            $table.closest('.ld-data-table-container').find('.ld-analytics-loader').hide();
                            $table.show();
                        }
                    });
                }
            });
        },

        // Método para renderizar tabelas DataTables
        renderDataTable: function($widget, data) {
            const tableId = $widget.find('table').attr('id'); // Obtém o ID da tabela
            const $table = $('#' + tableId); // Seleciona a tabela
            const columns = $table.data('columns'); // Obtém a configuração das colunas do HTML

            if (!columns || columns.length === 0) {
                console.warn('DataTables: Configuração de colunas não encontrada para o widget.', $table);
                return;
            }

            // Destrói a instância existente do DataTables se houver, para poder recriá-la com os novos dados e colunas
            if ($.fn.DataTable.isDataTable($table)) {
                LDAnalyticsWidgets.dataTables[tableId].destroy();
                $table.empty(); // Limpa o conteúdo da tabela
            }

            // Inicializa a DataTable com os dados e as colunas corretas
            LDAnalyticsWidgets.dataTables[tableId] = $table.DataTable({
                data: data,
                columns: columns, // Usa as colunas passadas do PHP
                responsive: true,
                language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json' },
                order: [], // Desabilita ordenação inicial para evitar erros de coluna
            });
        }
    };

    // Inicializa o plugin quando o DOM estiver pronto
    $(document).ready(() => LDAnalyticsWidgets.init());

    // Garante que os widgets sejam inicializados também após o Elementor carregar totalmente no frontend
    $(window).on('elementor/frontend/init', () => {
        elementorFrontend.hooks.addAction('frontend/elementor_ready/global', () => LDAnalyticsWidgets.init());
    });

})(jQuery);

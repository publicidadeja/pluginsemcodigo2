<?php 
if (!defined('ABSPATH')) exit;

// Carrega o Media Uploader
wp_enqueue_media();

// Localiza os scripts para AJAX
wp_localize_script('jquery', 'gma_ajax', array(
    'ajaxurl' => admin_url('admin-ajax.php'),
    'nonce' => wp_create_nonce('gma_copy_suggestions')
));
?>

<div class="gma-create-wrap">
    <div class="gma-create-container">
        <h1 class="gma-create-title">🎨 Criar Novo Material</h1>

        <div class="gma-create-card">
            <form method="post" class="gma-create-form" id="gma-material-form">
                <?php wp_nonce_field('gma_novo_material', 'gma_novo_material_nonce'); ?>
                
                <div class="gma-form-grid">
                    <!-- Seleção de Campanha -->
                    <div class="gma-form-group">
                        <label for="campanha_id">
                            <i class="dashicons dashicons-megaphone"></i> Campanha
                        </label>
                        <select name="campanha_id" id="campanha_id" required>
                            <option value="">Selecione uma campanha</option>
                            <?php 
                            $campanhas = gma_listar_campanhas();
                            foreach ($campanhas as $campanha): 
                                $tipo = esc_attr($campanha->tipo_campanha);
                            ?>
                                <option value="<?php echo esc_attr($campanha->id); ?>" 
                                        data-tipo="<?php echo $tipo; ?>">
                                    <?php echo esc_html($campanha->nome); ?> 
                                    (<?php echo ucfirst($tipo); ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Seleção de Tipo de Mídia -->
                    <div class="gma-form-group">
                        <label for="tipo_midia">
                            <i class="dashicons dashicons-format-video"></i> Tipo de Mídia
                        </label>
                        <select name="tipo_midia" id="tipo_midia" required>
                            <option value="imagem">Imagem Única</option>
                            <option value="video">Vídeo</option>
                            <option value="carrossel">Carrossel de Imagens</option>
                        </select>
                    </div>

                    <!-- Upload de Imagem -->
                    <div class="gma-form-group" id="campo-imagem">
                        <label for="gma-imagem-url">
                            <i class="dashicons dashicons-format-image"></i> Imagem
                        </label>
                        <div class="gma-upload-container">
                            <input type="text" name="imagem_url" id="gma-imagem-url" 
                                   class="gma-input" readonly>
                            <input type="hidden" name="arquivo_id" id="gma-arquivo-id">
                            <button type="button" id="gma-upload-btn" class="gma-button secondary">
                                <i class="dashicons dashicons-upload"></i> Selecionar
                            </button>
                        </div>
                        <div id="gma-image-preview" class="gma-image-preview"></div>
                    </div>

                    <!-- Campo de Vídeo -->
                    <div class="gma-form-group" id="campo-video" style="display: none;">
                        <label for="gma-video-url">
                            <i class="dashicons dashicons-video-alt3"></i> Vídeo
                        </label>
                        <div class="gma-upload-container">
                            <input type="text" name="video_url" id="gma-video-url" 
                                   class="gma-input" readonly>
                            <input type="hidden" name="video_id" id="gma-video-id">
                            <button type="button" id="gma-video-upload-btn" class="gma-button secondary">
                                <i class="dashicons dashicons-upload"></i> Selecionar Vídeo
                            </button>
                        </div>
                        <div id="gma-video-preview" class="gma-video-preview"></div>
                    </div>

                    <!-- Campo de Carrossel -->
                    <div class="gma-form-group" id="campo-carrossel" style="display: none;">
                        <label>
                            <i class="dashicons dashicons-images-alt2"></i> Imagens do Carrossel
                        </label>
                        <div id="carrossel-container">
                            <button type="button" id="adicionar-imagem-carrossel" class="gma-button secondary">
                                <i class="dashicons dashicons-plus"></i> Adicionar Imagem
                            </button>
                            <div id="imagens-carrossel-preview"></div>
                        </div>
                    </div>

                    <!-- Copy do Material -->
                    <div class="gma-form-group full-width">
                        <label for="copy">
                            <i class="dashicons dashicons-editor-paste-text"></i> Copy
                        </label>
                        <textarea name="copy" id="copy" rows="5" required></textarea>
                        <div class="gma-character-count">
                            <span id="char-count">0</span> caracteres
                        </div>
                        <button type="button" id="get-suggestions" class="gma-button secondary">
                            <i class="dashicons dashicons-admin-customizer"></i> Obter Sugestões AI
                        </button>
                        <div id="suggestions-container" style="display: none;">
                            <h3>Sugestões da IA</h3>
                            <div id="suggestions-content"></div>
                        </div>
                    </div>

                    <!-- Link do Canva -->
                    <div class="gma-form-group full-width" id="canva-group" style="display: none;">
                        <label for="link_canva">
                            <i class="dashicons dashicons-art"></i> Link do Canva
                        </label>
                        <input type="url" name="link_canva" id="link_canva" 
                               class="gma-input" placeholder="https://www.canva.com/...">
                    </div>
                </div>

                <div class="gma-form-actions">
                    <button type="submit" name="criar_material" class="gma-button primary">
                        <i class="dashicons dashicons-saved"></i> Criar Material
                    </button>
                    <a href="<?php echo admin_url('admin.php?page=gma-materiais'); ?>" 
                       class="gma-button secondary">
                        <i class="dashicons dashicons-arrow-left-alt"></i> Voltar
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
/* Variáveis CSS */
:root {
    --primary-color: #4a90e2;
    --secondary-color: #2ecc71;
    --danger-color: #e74c3c;
    --text-color: #2c3e50;
    --background-color: #f5f6fa;
    --card-background: #ffffff;
    --border-radius: 10px;
    --transition: all 0.3s ease;
}

/* Estilos gerais */
.gma-create-wrap {
    padding: 20px;
    background: var(--background-color);
    min-height: 100vh;
}

.gma-create-container {
    max-width: 800px;
    margin: 0 auto;
}

.gma-create-title {
    font-size: 2.5em;
    color: var(--text-color);
    text-align: center;
    margin-bottom: 30px;
    font-weight: 700;
}

.gma-create-card {
    background: var(--card-background);
    border-radius: var(--border-radius);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    padding: 30px;
    animation: slideIn 0.5s ease;
}

/* Grid e formulário */
.gma-form-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 20px;
}

.gma-form-group {
    margin-bottom: 20px;
}

.gma-form-group.full-width {
    grid-column: 1 / -1;
}

/* Campos de formulário */
.gma-input, select, textarea {
    width: 100%;
    padding: 12px;
    border: 2px solid #e1e1e1;
    border-radius: var(--border-radius);
    font-size: 1em;
    transition: var(--transition);
}

.gma-input:focus, select:focus, textarea:focus {
    border-color: var(--primary-color);
    outline: none;
    box-shadow: 0 0 0 3px rgba(74, 144, 226, 0.2);
}

/* Upload e preview */
.gma-upload-container {
    display: flex;
    gap: 10px;
}

.gma-image-preview, .gma-video-preview {
    margin-top: 10px;
    max-width: 300px;
    border-radius: var(--border-radius);
    overflow: hidden;
}

.gma-image-preview img, .gma-video-preview video {
    width: 100%;
    height: auto;
    display: block;
}

/* Carrossel */
.carrossel-item {
    position: relative;
    display: inline-block;
    margin: 10px;
    max-width: 150px;
}

.carrossel-item img {
    width: 100%;
    height: auto;
    border-radius: var(--border-radius);
}

.remover-imagem {
    position: absolute;
    top: 5px;
    right: 5px;
    background: var(--danger-color);
    color: white;
    border: none;
    border-radius: 50%;
    width: 24px;
    height: 24px;
    cursor: pointer;
    padding: 0;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* Botões e ações */
.gma-button {
    padding: 12px 24px;
    border: none;
    border-radius: var(--border-radius);
    cursor: pointer;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: var(--transition);
    text-decoration: none;
}

.gma-button.primary {
    background: var(--primary-color);
    color: white;
}

.gma-button.secondary {
    background: var(--secondary-color);
    color: white;
}

.gma-button:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
}

.gma-form-actions {
    display: flex;
    gap: 15px;
    margin-top: 30px;
    justify-content: flex-end;
}

/* Responsividade */
@media (max-width: 768px) {
    .gma-form-grid {
        grid-template-columns: 1fr;
    }
    
    .gma-form-actions {
        flex-direction: column;
    }
    
    .gma-button {
        width: 100%;
        justify-content: center;
    }
}
  /* Adicione ao CSS existente */
#campo-video {
    margin-top: 15px;
}

.gma-video-preview {
    margin-top: 10px;
    max-width: 300px;
    border-radius: var(--border-radius);
    overflow: hidden;
    background: #f5f5f5;
    padding: 10px;
}

.gma-video-preview video {
    width: 100%;
    height: auto;
    display: block;
}
  
</style>

<script>
jQuery(document).ready(function($) {
    // Controle de exibição dos campos baseado no tipo de mídia
    $('#tipo_midia').on('change', function() {
        const tipoMidia = $(this).val();
        
        // Esconde todos os campos
        $('#campo-imagem, #campo-video, #campo-carrossel').hide();
        
        // Mostra o campo específico baseado na seleção
        switch(tipoMidia) {
            case 'imagem':
                $('#campo-imagem').show();
                break;
            case 'video':
                $('#campo-video').show();
                break;
            case 'carrossel':
                $('#campo-carrossel').show();
                break;
        }
    });

    // Upload de imagem
    $('#gma-upload-btn').click(function(e) {
        e.preventDefault();
        
        var custom_uploader = wp.media({
            title: 'Selecionar Imagem',
            button: {
                text: 'Usar esta imagem'
            },
            multiple: false
        });

        custom_uploader.on('select', function() {
            var attachment = custom_uploader.state().get('selection').first().toJSON();
            $('#gma-imagem-url').val(attachment.url);
            $('#gma-arquivo-id').val(attachment.id);
            $('#gma-image-preview').html('<img src="' + attachment.url + '" alt="Preview">');
        });

        custom_uploader.open();
    });

    // Upload de vídeo
    // Substitua o código existente do upload de vídeo por este:
$('#gma-video-upload-btn').click(function(e) {
    e.preventDefault();
    
    var video_uploader = wp.media({
        title: 'Selecionar Vídeo',
        button: {
            text: 'Usar este vídeo'
        },
        multiple: false,
        library: {
            type: [ 'video/mp4', 'video/webm', 'video/ogg' ]
        }
    });

    video_uploader.on('select', function() {
        var attachment = video_uploader.state().get('selection').first().toJSON();
        $('#gma-video-url').val(attachment.url);
        $('#gma-video-id').val(attachment.id);
        
        // Adiciona preview do vídeo
        $('#gma-video-preview').html(`
            <video width="300" controls>
                <source src="${attachment.url}" type="${attachment.mime}">
                Seu navegador não suporta o elemento de vídeo.
            </video>
        `);
    });

    video_uploader.open();
});

// Adicione esta validação antes do submit do formulário
$('#gma-material-form').on('submit', function(e) {
    const tipoMidia = $('#tipo_midia').val();
    
    if (tipoMidia === 'video' && !$('#gma-video-url').val()) {
        e.preventDefault();
        alert('Por favor, selecione um vídeo antes de criar o material.');
        return false;
    }
});

    // Gerenciamento do carrossel
    let imagensCarrossel = [];
    
    $('#adicionar-imagem-carrossel').click(function(e) {
        e.preventDefault();
        
        var custom_uploader = wp.media({
            title: 'Selecionar Imagem para Carrossel',
            button: {
                text: 'Usar esta imagem'
            },
            multiple: false
        });

        custom_uploader.on('select', function() {
            var attachment = custom_uploader.state().get('selection').first().toJSON();
            imagensCarrossel.push(attachment.url);
            atualizarPreviewCarrossel();
        });

        custom_uploader.open();
    });

    function atualizarPreviewCarrossel() {
        const container = $('#imagens-carrossel-preview');
        container.empty();
        
        imagensCarrossel.forEach((url, index) => {
            container.append(`
                <div class="carrossel-item">
                    <img src="${url}" alt="Imagem ${index + 1}">
                    <button type="button" class="remover-imagem" data-index="${index}">
                        <i class="dashicons dashicons-trash"></i>
                    </button>
                    <input type="hidden" name="imagens_carrossel[]" value="${url}">
                </div>
            `);
        });
    }

    // Remover imagem do carrossel
    $(document).on('click', '.remover-imagem', function() {
        const index = $(this).data('index');
        imagensCarrossel.splice(index, 1);
        atualizarPreviewCarrossel();
    });

    // Contador de caracteres
    $('#copy').on('input', function() {
        var charCount = $(this).val().length;
        $('#char-count').text(charCount);
    });

    // Sugestões AI
    $('#get-suggestions').on('click', function() {
        const copy = $('#copy').val();
        const button = $(this);
        
        if (!copy) {
            alert('Por favor, insira algum texto primeiro.');
            return;
        }
        
        button.prop('disabled', true).text('Obtendo sugestões...');
        
        $.ajax({
            url: gma_ajax.ajaxurl,
            type: 'POST',
            data: {
                action: 'gma_get_copy_suggestions',
                nonce: gma_ajax.nonce,
                copy: copy
            },
            success: function(response) {
                if (response.success) {
                    $('#suggestions-content').html(response.data.suggestions);
                    $('#suggestions-container').slideDown();
                } else {
                    alert('Falha ao obter sugestões. Tente novamente.');
                }
            },
            error: function() {
                alert('Erro ao conectar com o servidor.');
            },
            complete: function() {
                button.prop('disabled', false).text('Obter Sugestões AI');
            }
        });
    });

    // Controle de exibição do campo Canva
    $('#campanha_id').on('change', function() {
        var selectedOption = $(this).find('option:selected');
        var tipoCampanha = selectedOption.data('tipo');
        
        if (tipoCampanha === 'marketing') {
            $('#canva-group').show();
        } else {
            $('#canva-group').hide();
            $('#link_canva').val('');
        }
    });

    // Validação do formulário
    $('#gma-material-form').on('submit', function(e) {
        var isValid = true;
        
        $(this).find('[required]').each(function() {
            if (!$(this).val()) {
                isValid = false;
                $(this).addClass('error').shake();
            } else {
                $(this).removeClass('error');
            }
        });

        if (!isValid) {
            e.preventDefault();
            alert('Por favor, preencha todos os campos obrigatórios.');
        }
    });

    // Efeito shake para campos com erro
    $.fn.shake = function() {
        this.each(function() {
            $(this).css('position', 'relative');
            for(var i = 0; i < 3; i++) {
                $(this).animate({left: -10}, 50)
                       .animate({left: 10}, 50)
                       .animate({left: 0}, 50);
            }
        });
    };
});
  // Adicione este código antes do submit do formulário
$('#gma-material-form').on('submit', function(e) {
    const tipoMidia = $('#tipo_midia').val();
    
    if (tipoMidia === 'video') {
        if (!$('#gma-video-url').val()) {
            e.preventDefault();
            alert('Por favor, selecione um vídeo antes de criar o material.');
            return false;
        }
    }
});
</script>

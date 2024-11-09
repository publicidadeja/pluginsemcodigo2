<?php
if (isset($_GET['message'])) {
    if ($_GET['message'] == 'updated') {
        echo '<div class="notice notice-success is-dismissible"><p>Material atualizado com sucesso.</p></div>';
    } elseif ($_GET['message'] == 'error') {
        echo '<div class="notice notice-error is-dismissible"><p>Erro ao atualizar o material.</p></div>';
    }
}
?>
<div class="wrap">
    <h1>Editar Material</h1>

    <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
        <input type="hidden" name="action" value="gma_atualizar_material">
        <input type="hidden" name="material_id" value="<?php echo esc_attr($material->id); ?>">

        <table class="form-table">
            <tr>
                <th><label for="tipo_midia">Tipo de Mídia:</label></th>
                <td>
                    <select name="tipo_midia" id="tipo_midia">
                        <option value="imagem" <?php selected($material->tipo_midia ?? 'imagem', 'imagem'); ?>>Imagem</option>
                        <option value="video" <?php selected($material->tipo_midia ?? 'imagem', 'video'); ?>>Vídeo</option>
                    </select>
                </td>
            </tr>

            <tr class="campo-imagem">
                <th><label for="imagem_url">URL da Imagem:</label></th>
                <td>
                    <input type="text" name="imagem_url" id="imagem_url" value="<?php echo esc_attr($material->imagem_url); ?>" class="regular-text">
                    <button type="button" class="button" id="gma-upload-btn">Escolher Imagem</button>
                    <div id="gma-image-preview">
                        <?php if (!empty($material->imagem_url)): ?>
                            <img src="<?php echo esc_url($material->imagem_url); ?>" alt="Pré-visualização" style="max-width: 200px;">
                        <?php endif; ?>
                    </div>
                </td>
            </tr>

            <tr class="campo-video" style="display: none;">
                <th><label for="video_url">URL do Vídeo:</label></th>
                <td>
                    <input type="url" name="video_url" id="video_url" value="<?php echo esc_attr($material->video_url ?? ''); ?>" class="regular-text">
                    <p class="description">Insira a URL do vídeo (YouTube, Vimeo, etc)</p>
                </td>
            </tr>

            <tr>
                <th><label for="copy">Copy:</label></th>
                <td>
                    <textarea name="copy" id="copy" rows="5" cols="50" class="regular-text" required><?php echo esc_textarea($material->copy); ?></textarea>
                </td>
            </tr>

            <tr>
                <th><label for="link_canva">Link do Canva:</label></th>
                <td>
                    <input type="url" name="link_canva" id="link_canva" value="<?php echo esc_attr($material->link_canva); ?>" class="regular-text">
                </td>
            </tr>
        </table>

        <?php wp_nonce_field('gma_atualizar_material', 'gma_nonce'); ?>
        <input type="submit" name="submit" class="button button-primary" value="Atualizar Material">
    </form>
</div>

<script>
    jQuery(document).ready(function ($) {
        // Controle de visibilidade dos campos
        function toggleCampos() {
            var tipoMidia = $('#tipo_midia').val();
            if (tipoMidia === 'video') {
                $('.campo-video').show();
                $('.campo-imagem').hide();
            } else {
                $('.campo-video').hide();
                $('.campo-imagem').show();
            }
        }

        $('#tipo_midia').on('change', toggleCampos);
        toggleCampos(); // Executar na carga inicial

        // Upload de mídia
        var mediaUploader;
        $('#gma-upload-btn').click(function (e) {
            e.preventDefault();
            
            if (mediaUploader) {
                mediaUploader.open();
                return;
            }

            mediaUploader = wp.media.frames.file_frame = wp.media({
                title: 'Selecione ou envie uma imagem',
                button: {
                    text: 'Usar esta imagem'
                },
                multiple: false 
            });

            mediaUploader.on('select', function () {
                var attachment = mediaUploader.state().get('selection').first().toJSON();
                $('#imagem_url').val(attachment.url);
                $('#gma-image-preview').html('<img src="' + attachment.url + '" alt="Pré-visualização" style="max-width: 200px;">');
            });

            mediaUploader.open();
        });
    });
</script>
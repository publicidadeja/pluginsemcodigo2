<div class="wrap">
    <h1>Upload em Massa de Materiais</h1>
    
    <form method="post" enctype="multipart/form-data" class="gma-upload-massa-form">
        <?php wp_nonce_field('gma_upload_massa', 'gma_upload_massa_nonce'); ?>
        
        <select name="campanha_id" required>
            <option value="">Selecione a Campanha</option>
            <?php 
            $campanhas = gma_listar_campanhas();
            foreach ($campanhas as $campanha): ?>
                <option value="<?php echo esc_attr($campanha->id); ?>">
                    <?php echo esc_html($campanha->nome); ?>
                </option>
            <?php endforeach; ?>
        </select>
        
        <div class="gma-upload-area" id="gma-upload-area">
            <input type="file" name="arquivos[]" multiple accept="image/*,video/*" id="gma-upload-input">
            <label for="gma-upload-input">
                Arraste arquivos aqui ou clique para selecionar
            </label>
        </div>
        
        <div id="gma-preview-area"></div>
        
        <button type="submit" class="button button-primary">Fazer Upload</button>
    </form>
</div>

<style>
.gma-upload-area {
    border: 2px dashed #ccc;
    padding: 20px;
    text-align: center;
    margin: 20px 0;
    cursor: pointer;
}

.gma-upload-area.dragover {
    background: #f0f0f0;
    border-color: #999;
}

#gma-preview-area {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
    gap: 10px;
    margin: 20px 0;
}

.preview-item {
    position: relative;
    padding: 5px;
    border: 1px solid #ddd;
}

.preview-item img {
    max-width: 100%;
    height: auto;
}

.preview-item .remove-item {
    position: absolute;
    top: 5px;
    right: 5px;
    background: rgba(255,0,0,0.7);
    color: white;
    border: none;
    border-radius: 50%;
    padding: 2px 6px;
    cursor: pointer;
}
</style>

<script>
jQuery(document).ready(function($) {
    const uploadArea = $('#gma-upload-area');
    const uploadInput = $('#gma-upload-input');
    const previewArea = $('#gma-preview-area');
    
    // Drag and drop
    uploadArea.on('dragover dragenter', function(e) {
        e.preventDefault();
        $(this).addClass('dragover');
    });
    
    uploadArea.on('dragleave dragend drop', function(e) {
        e.preventDefault();
        $(this).removeClass('dragover');
    });
    
    uploadArea.on('drop', function(e) {
        e.preventDefault();
        const files = e.originalEvent.dataTransfer.files;
        handleFiles(files);
    });
    
    uploadInput.on('change', function() {
        handleFiles(this.files);
    });
    
    function handleFiles(files) {
        Array.from(files).forEach(file => {
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    addPreview(e.target.result, file.name);
                };
                reader.readAsDataURL(file);
            } else if (file.type.startsWith('video/')) {
                addPreview('video-thumbnail.png', file.name, true);
            }
        });
    }
    
    function addPreview(src, filename, isVideo = false) {
        const preview = $('<div class="preview-item">')
            .append(isVideo ? 
                '<img src="/wp-content/plugins/seu-plugin/assets/images/video-icon.png" alt="Video">' :
                `<img src="${src}" alt="Preview">`)
            .append(`<span>${filename}</span>`)
            .append('<button class="remove-item">×</button>');
        
        previewArea.append(preview);
    }
    
    $(document).on('click', '.remove-item', function() {
        $(this).closest('.preview-item').remove();
    });
});
</script>
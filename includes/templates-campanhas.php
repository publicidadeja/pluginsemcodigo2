<?php
if (!defined('ABSPATH')) exit;

// Função para registrar post type de templates
function gma_register_template_post_type() {
    $labels = array(
        'name'               => 'Templates de Campanha',
        'singular_name'      => 'Template de Campanha',
        'add_new'           => 'Adicionar Novo',
        'add_new_item'      => 'Adicionar Novo Template',
        'edit_item'         => 'Editar Template',
        'new_item'          => 'Novo Template',
        'view_item'         => 'Ver Template',
        'search_items'      => 'Buscar Templates',
        'menu_name'         => 'Templates'
    );

    register_post_type('gma_template', array(
        'labels'            => $labels,
        'public'            => false,
        'show_ui'           => true,
        'show_in_menu'      => 'edit.php?post_type=gma_campaign',
        'capability_type'   => 'post',
        'supports'          => array('title', 'editor', 'thumbnail'),
        'menu_icon'         => 'dashicons-layout'
    ));
}
add_action('init', 'gma_register_template_post_type');

// Função para salvar metadados do template
function gma_save_template_meta($template_id) {
    if (isset($_POST['template_copy'])) {
        update_post_meta($template_id, '_template_copy', sanitize_textarea_field($_POST['template_copy']));
    }
    if (isset($_POST['template_dimensoes'])) {
        update_post_meta($template_id, '_template_dimensoes', sanitize_text_field($_POST['template_dimensoes']));
    }
    if (isset($_POST['template_tipo_midia'])) {
        update_post_meta($template_id, '_template_tipo_midia', sanitize_text_field($_POST['template_tipo_midia']));
    }
}
add_action('save_post_gma_template', 'gma_save_template_meta');

// Função para aplicar template ao material
function gma_aplicar_template($material_id, $template_id) {
    $template = get_post($template_id);
    if (!$template || $template->post_type !== 'gma_template') {
        return false;
    }

    $copy = get_post_meta($template_id, '_template_copy', true);
    $dimensoes = get_post_meta($template_id, '_template_dimensoes', true);
    $tipo_midia = get_post_meta($template_id, '_template_tipo_midia', true);

    // Atualiza o material com os dados do template
    global $wpdb;
    $tabela = $wpdb->prefix . 'gma_materiais';
    
    return $wpdb->update(
        $tabela,
        array(
            'copy' => $copy,
            'dimensoes' => $dimensoes,
            'tipo_midia' => $tipo_midia,
            'data_modificacao' => current_time('mysql')
        ),
        array('id' => $material_id),
        array('%s', '%s', '%s', '%s'),
        array('%d')
    );
}

// Adiciona select de templates no formulário de criação de material
function gma_add_template_select() {
    $templates = get_posts(array(
        'post_type' => 'gma_template',
        'posts_per_page' => -1
    ));
    
    if (!empty($templates)) {
        echo '<div class="gma-field-group">';
        echo '<label for="template_id">Usar Template:</label>';
        echo '<select name="template_id" id="template_id">';
        echo '<option value="">Selecione um template</option>';
        foreach ($templates as $template) {
            echo sprintf(
                '<option value="%d">%s</option>',
                $template->ID,
                esc_html($template->post_title)
            );
        }
        echo '</select>';
        echo '</div>';
    }
}
add_action('gma_before_material_form', 'gma_add_template_select');
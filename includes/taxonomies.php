<?php
// taxonomies.php

if (!defined('ABSPATH')) {
    exit;
}

// Registro da taxonomia hierárquica para categorias
function gma_register_campaign_category_taxonomy() {
    $labels = array(
        'name'              => _x('Categorias de Campanha', 'taxonomy general name', 'gma-plugin'),
        'singular_name'     => _x('Categoria de Campanha', 'taxonomy singular name', 'gma-plugin'),
        'search_items'      => __('Buscar Categorias', 'gma-plugin'),
        'all_items'         => __('Todas as Categorias', 'gma-plugin'),
        'parent_item'       => __('Categoria Pai', 'gma-plugin'),
        'parent_item_colon' => __('Categoria Pai:', 'gma-plugin'),
        'edit_item'         => __('Editar Categoria', 'gma-plugin'),
        'update_item'       => __('Atualizar Categoria', 'gma-plugin'),
        'add_new_item'      => __('Adicionar Nova Categoria', 'gma-plugin'),
        'new_item_name'     => __('Nome da Nova Categoria', 'gma-plugin'),
        'menu_name'         => __('Categorias', 'gma-plugin'),
    );

    register_taxonomy('gma_campaign_category', 'gma_campaign', array(
        'hierarchical'      => true,
        'labels'            => $labels,
        'show_ui'          => true,
        'show_admin_column' => true,
        'query_var'        => true,
        'rewrite'          => array('slug' => 'categoria-campanha'),
        'capabilities'     => array(
            'manage_terms' => 'manage_categories',
            'edit_terms'   => 'manage_categories',
            'delete_terms' => 'manage_categories',
            'assign_terms' => 'edit_posts'
        )
    ));
}

// Registro da taxonomia não-hierárquica para tags
function gma_register_campaign_tags_taxonomy() {
    $labels = array(
        'name'                       => _x('Tags de Campanha', 'taxonomy general name', 'gma-plugin'),
        'singular_name'              => _x('Tag de Campanha', 'taxonomy singular name', 'gma-plugin'),
        'search_items'               => __('Buscar Tags', 'gma-plugin'),
        'popular_items'              => __('Tags Populares', 'gma-plugin'),
        'all_items'                  => __('Todas as Tags', 'gma-plugin'),
        'edit_item'                  => __('Editar Tag', 'gma-plugin'),
        'update_item'                => __('Atualizar Tag', 'gma-plugin'),
        'add_new_item'               => __('Adicionar Nova Tag', 'gma-plugin'),
        'new_item_name'              => __('Nova Tag', 'gma-plugin'),
        'separate_items_with_commas' => __('Separe as tags com vírgulas', 'gma-plugin'),
        'add_or_remove_items'        => __('Adicionar ou remover tags', 'gma-plugin'),
        'choose_from_most_used'      => __('Escolher entre as tags mais usadas', 'gma-plugin'),
        'menu_name'                  => __('Tags', 'gma-plugin'),
    );

    register_taxonomy('gma_campaign_tag', 'gma_campaign', array(
        'hierarchical'          => false,
        'labels'                => $labels,
        'show_ui'              => true,
        'show_admin_column'     => true,
        'update_count_callback' => '_update_post_term_count',
        'query_var'            => true,
        'rewrite'              => array('slug' => 'tag-campanha'),
        'show_in_rest'         => true,
        'meta_box_cb'          => 'post_tags_meta_box'
    ));
}

// Registro da taxonomia para status de aprovação
function gma_register_approval_status_taxonomy() {
    $labels = array(
        'name'              => _x('Status de Aprovação', 'taxonomy general name', 'gma-plugin'),
        'singular_name'     => _x('Status', 'taxonomy singular name', 'gma-plugin'),
        'menu_name'         => __('Status de Aprovação', 'gma-plugin'),
    );

    register_taxonomy('gma_approval_status', 'gma_campaign', array(
        'hierarchical'      => true,
        'labels'            => $labels,
        'show_ui'          => true,
        'show_admin_column' => true,
        'query_var'        => true,
        'rewrite'          => array('slug' => 'status-aprovacao'),
        'show_in_rest'     => true
    ));

    // Adiciona termos padrão para status de aprovação
    wp_insert_term('Pendente', 'gma_approval_status');
    wp_insert_term('Em Revisão', 'gma_approval_status');
    wp_insert_term('Aprovado', 'gma_approval_status');
    wp_insert_term('Reprovado', 'gma_approval_status');
}

// Inicialização das taxonomias
add_action('init', 'gma_register_campaign_category_taxonomy');
add_action('init', 'gma_register_campaign_tags_taxonomy');
add_action('init', 'gma_register_approval_status_taxonomy');
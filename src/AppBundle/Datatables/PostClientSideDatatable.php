<?php

namespace AppBundle\Datatables;

use Sg\DatatablesBundle\Datatable\View\AbstractDatatableView;
use Sg\DatatablesBundle\Datatable\View\Style;

/**
 * Class PostClientSideDatatable
 *
 * @package AppBundle\Datatables
 */
class PostClientSideDatatable extends AbstractDatatableView
{
    /**
     * {@inheritdoc}
     */
    public function buildDatatable()
    {
        $this->features->setFeatures(array(
            'auto_width' => true,
            'defer_render' => false,
            'info' => true,
            'jquery_ui' => false,
            'length_change' => true,
            'ordering' => true,
            'paging' => true,
            'processing' => true,
            'scroll_x' => true, // default: false
            'scroll_y' => '',
            'searching' => true,
            'server_side' => false, // default: true
            'state_save' => false,
            'delay' => 0
        ));

        $this->options->setOptions(array(
            'display_start' => 0,
            'defer_loading' => -1,
            'dom' => 'lfrtip', // default, but not used because 'use_integration_options' = true
            'length_menu' => array(10, 25, 50, 100),
            'order_classes' => true,
            'order' => [[0, 'asc']],
            'order_multi' => true,
            'page_length' => 10,
            'paging_type' => Style::FULL_NUMBERS_PAGINATION,
            'renderer' => '', // default, but not used because 'use_integration_options' = true
            'scroll_collapse' => false,
            'search_delay' => 0,
            'state_duration' => 7200,
            'stripe_classes' => array(),
            'responsive' => false,
            'class' => Style::BOOTSTRAP_3_STYLE . ' table-condensed', // default: Style::BASE_STYLE
            'individual_filtering' => true, // default: false
            'individual_filtering_position' => 'both', // default: 'foot'
            'use_integration_options' => true // default: false
        ));

        $users = $this->em->getRepository('AppBundle:User')->findAll();

        $this->columnBuilder
            ->add(null, 'multiselect', array(
                'start_html' => '<div class="wrapper" id="wrapper">',
                'end_html' => '</div>',
                'attributes' => array(
                    'class' => 'testclass',
                    'name' => 'testname',
                ),
                'actions' => array(
                    array(
                        'route' => 'post_bulk_delete',
                        'label' => 'Delete',
                        'role' => 'ROLE_USER',
                        'icon' => 'fa fa-times',
                        'attributes' => array(
                            'rel' => 'tooltip',
                            'title' => 'Delete',
                            'class' => 'btn btn-danger btn-xs',
                            'role' => 'button'
                        ),
                    ),
                    array(
                        'route' => 'post_bulk_invisible',
                        'label' => 'Invisible',
                        'role' => 'ROLE_USER',
                        'icon' => 'fa fa-eye-slash',
                        'attributes' => array(
                            'rel' => 'tooltip',
                            'title' => 'Invisible',
                            'class' => 'btn btn-primary btn-xs',
                            'role' => 'button'
                        ),
                    )
                )
            ))
            ->add('id', 'column', array(
                'class' => '',
                'padding' => '',
                'name' => '',
                'orderable' => true,
                'render' => null,
                'searchable' => true,
                'title' => 'Id',
                'type' => '',
                'visible' => true,
                'width' => '40px',
                'search_type' => 'like',
                'filter_type' => 'text',
                'filter_options' => array(),
                'filter_property' => '',
                'filter_search_column' => '',
                'default' => ''
            ))
            ->add('visible', 'boolean', array(
                'class' => '',
                'padding' => '',
                'name' => '',
                'orderable' => true,
                'render' => 'render_boolean',
                'searchable' => true,
                'title' => 'Visible',
                'type' => '',
                'visible' => true,
                'width' => '50px',
                'true_icon' => 'glyphicon glyphicon-ok',
                'false_icon' => '',
                'true_label' => 'yes',
                'false_label' => 'no',
                'search_type' => 'eq',
                'filter_type' => 'select',
                'filter_options' => ['' => 'Any', 'yes' => 'Yes', 'no' => 'No'],
                'filter_property' => '',
                'filter_search_column' => '',
            ))
            ->add('publishedAt', 'datetime', array(
                'class' => '',
                'padding' => '',
                'name' => 'daterange',
                'orderable' => true,
                'render' => 'render_datetime',
                'searchable' => true,
                'title' => "<span class='glyphicon glyphicon-calendar' aria-hidden='true'></span> Published",
                'type' => '',
                'visible' => true,
                'width' => '120px',
                'search_type' => 'like',
                'filter_type' => 'text',
                'filter_options' => array(),
                'filter_property' => '',
                'filter_search_column' => '',
                'date_format' => 'lll',
            ))
            ->add('title', 'column', array(
                'title' => "<span class='glyphicon glyphicon-book' aria-hidden='true'></span> Title",
                'width' => '120px',
            ))
            ->add('owner', 'virtual', array(
                'title' => 'Owner'
            ))
            ->add('authorEmail', 'column', array(
                'title' => "<span class='glyphicon glyphicon-user' aria-hidden='true'></span> Email",
                'filter_type' => 'select',
                'filter_options' => ['' => 'Any'] + $this->getCollectionAsOptionsArray($users, 'email', 'username'),
                'filter_property' => 'authorEmail',
            ))
            ->add('comments.title', 'array', array(
                'title' => 'Comments',
                'searchable' => false,
                'orderable' => false,
                'data' => 'comments[, ].title',
            ))
            ->add(null, 'action', array(
                'title' => 'Actions',
                'start_html' => '<div class="wrapper">',
                'end_html' => '</div>',
                'actions' => array(
                    array(
                        'route' => 'post_show',
                        'route_parameters' => array(
                            'id' => 'id'
                        ),
                        'label' => 'Show',
                        'icon' => 'glyphicon glyphicon-eye-open',
                        'attributes' => array(
                            'rel' => 'tooltip',
                            'title' => 'Show',
                            'class' => 'btn btn-default btn-xs',
                            'role' => 'button'
                        ),
                        'role' => 'ROLE_USER',
                        'render_if' => array('visible')
                    ),
                    array(
                        'route' => 'post_edit',
                        'route_parameters' => array(
                            'id' => 'id'
                        ),
                        'label' => 'Edit',
                        'icon' => 'glyphicon glyphicon-edit',
                        'attributes' => array(
                            'rel' => 'tooltip',
                            'title' => 'Edit',
                            'class' => 'btn btn-primary btn-xs',
                            'role' => 'button'
                        ),
                        'confirm' => true,
                        'confirm_message' => 'Are you sure?',
                        'role' => 'ROLE_ADMIN',
                    )
                )
            ));
    }

    /**
     * {@inheritdoc}
     */
    public function getEntity()
    {
        return 'AppBundle\Entity\Post';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'post_client_side_datatable';
    }
}

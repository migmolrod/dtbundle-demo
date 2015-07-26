<?php

namespace AppBundle\Datatables;

use Sg\DatatablesBundle\Datatable\View\AbstractDatatableView;
use Sg\DatatablesBundle\Datatable\View\Style;

/**
 * Class UserDatatable
 *
 * @package AppBundle\Datatables
 */
class UserDatatable extends AbstractDatatableView
{
    /**
     * {@inheritdoc}
     */
    public function buildDatatable()
    {
        $this->features->setFeatures(array(
            'scroll_x' => true,
        ));

        $this->ajax->setOptions(array(
            'url' => $this->router->generate('sg_user_results'),
            'type' => 'GET'
        ));

        $this->options->setOptions(array(
            'class' => Style::BOOTSTRAP_3_STYLE . ' table-condensed',
            'individual_filtering' => true,
            'individual_filtering_position' => 'both',
            'use_integration_options' => true
        ));

        $this->columnBuilder
            ->add('id', 'column', array(
                'title' => 'Id',
            ))
            ->add('username', 'column', array(
                'title' => 'Username',
            ))
            ->add('email', 'column', array(
                'title' => 'Email',
            ))
            ->add('lastLogin', 'datetime', array(
                'title' => 'Last login',
            ))
            ->add(null, 'action', array(
                'title' => 'Actions',
                'start_html' => '<div class="wrapper">',
                'end_html' => '</div>',
                'actions' => array(
                    array(
                        'route' => 'sg_user_show',
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
                    ),
                    array(
                        'route' => 'sg_user_edit',
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
                        )
                    )
                )
            ));
    }

    /**
     * {@inheritdoc}
     */
    public function getEntity()
    {
        return 'AppBundle\Entity\User';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'user_datatable';
    }
}

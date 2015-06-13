<?php

namespace AppBundle\Datatables;

use Sg\DatatablesBundle\Datatable\View\AbstractDatatableView;
use Sg\DatatablesBundle\Datatable\View\Style;

/**
 * Class PostDatatable
 *
 * @package AppBundle\Datatables
 */
class PostDatatable extends AbstractDatatableView
{
    /**
     * {@inheritdoc}
     */
    public function getLineFormatter()
    {
        $formatter = function($line) {
            $repository = $this->em->getRepository($this->getEntity());
            $entity = $repository->find($line['id']);

            // see if a User is logged in
            if ($this->authorizationChecker->isGranted('IS_AUTHENTICATED_FULLY')) {
                $user = $this->securityToken->getToken()->getUser();
                // is the given User the author of this Post?
                $line['owner'] = $entity->isAuthor($user); // render 'true' or 'false'
            } else {
                // render a twig template with login link
                $line['owner'] = $this->twig->render(':post:login_link.html.twig', array(
                    'entity' => $repository->find($line['id'])
                ));
            }

            return $line;
        };

        return $formatter;
    }

    /**
     * {@inheritdoc}
     */
    public function buildDatatableView()
    {
        // the default settings, except 'scroll_x'
        $this->features->setFeatures(array(
            'auto_width' => true,
            'defer_render' => false,
            'info' => true,
            'jquery_ui' => false,
            'length_change' => true,
            'ordering' => true,
            'paging' => true,
            'processing' => true,
            'scroll_x' => true,
            'scroll_y' => '',
            'searching' => true,
            'server_side' => true,
            'state_save' => false,
            'delay' => 0
        ));

        // the default settings, except 'url'
        $this->ajax->setOptions(array(
            'url' => $this->router->generate('post_results'),
            'type' => 'GET'
        ));

        // the default settings, except 'class', 'individual_filtering', individual_filtering_position and 'use_integration_options'
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
            'class' => Style::BOOTSTRAP_3_STYLE . ' table-condensed',
            'individual_filtering' => true,
            'individual_filtering_position' => 'both',
            'use_integration_options' => true
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
                        'label' => $this->translator->trans('dtbundle.post.actions.delete'),
                        'role' => 'ROLE_ADMIN',
                        'icon' => 'fa fa-times',
                        'attributes' => array(
                            'rel' => 'tooltip',
                            'title' => $this->translator->trans('dtbundle.post.actions.delete'),
                            'class' => 'btn btn-danger btn-xs',
                            'role' => 'button'
                        ),
                    ),
                    array(
                        'route' => 'post_bulk_invisible',
                        'label' => $this->translator->trans('dtbundle.post.actions.invisible'),
                        'icon' => 'fa fa-eye-slash',
                        'attributes' => array(
                            'rel' => 'tooltip',
                            'title' => $this->translator->trans('dtbundle.post.actions.invisible'),
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
                'default' => ''
            ))
            ->add('visible', 'boolean', array(
                'class' => '',
                'padding' => '',
                'name' => '',
                'orderable' => true,
                'render' => 'render_boolean',
                'searchable' => true,
                'title' => $this->translator->trans('dtbundle.post.titles.visible'),
                'type' => '',
                'visible' => true,
                'width' => '50px',
                'true_icon' => 'glyphicon glyphicon-ok',
                'false_icon' => '',
                'true_label' => 'yes',
                'false_label' => 'no',
                'search_type' => 'eq',
                'filter_type' => 'select',
                'filter_options' => ['' => $this->translator->trans('dtbundle.post.filter.any'), '1' => $this->translator->trans('dtbundle.post.filter.yes'), '0' => $this->translator->trans('dtbundle.post.filter.no')],
            ))
            ->add('publishedAt', 'datetime', array(
                'class' => '',
                'padding' => '',
                'name' => 'daterange',
                'orderable' => true,
                'render' => 'render_datetime',
                'date_format' => 'lll',
                'searchable' => true,
                'title' => "<span class='glyphicon glyphicon-calendar' aria-hidden='true'></span> " . $this->translator->trans('dtbundle.post.titles.published'),
                'type' => '',
                'visible' => true,
                'width' => '120px'
            ))
            ->add('title', 'column', array(
                'title' => "<span class='glyphicon glyphicon-book' aria-hidden='true'></span> " . $this->translator->trans('dtbundle.post.titles.title'),
                'width' => '120px',
            ))
            ->add('owner', 'virtual', array(
                'title' => $this->translator->trans('dtbundle.post.titles.owner')
            ))
            ->add('authorEmail', 'column', array(
                'class' => '',
                'padding' => '',
                'name' => '',
                'orderable' => true,
                'render' => null,
                'searchable' => true,
                'title' => "<span class='glyphicon glyphicon-user' aria-hidden='true'></span> " . $this->translator->trans('dtbundle.post.titles.email'),
                'type' => '',
                'visible' => true,
                'width' => '',
                'default' => '',
                'filter_type' => 'select',
                'filter_options' => ['' => $this->translator->trans('dtbundle.post.filter.any')] + $this->getCollectionAsOptionsArray($users, 'email', 'username'),
                'filter_property' => 'authorEmail',
            ))
            ->add('comments.title', 'array', array(
                'title' => $this->translator->trans('dtbundle.post.titles.comments'),
                'searchable' => false,
                'orderable' => false,
                'data' => 'comments[, ].title',
                ))
            ->add(null, 'action', array(
                'title' => $this->translator->trans('dtbundle.post.titles.actions'),
                'start_html' => '<div class="wrapper">',
                'end_html' => '</div>',
                'actions' => array(
                    array(
                        'route' => 'post_show',
                        'route_parameters' => array(
                            'id' => 'id'
                        ),
                        'label' => $this->translator->trans('dtbundle.post.actions.show'),
                        'icon' => 'glyphicon glyphicon-eye-open',
                        'attributes' => array(
                            'rel' => 'tooltip',
                            'title' => $this->translator->trans('dtbundle.post.actions.edit'),
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
                        'label' => $this->translator->trans('dtbundle.post.actions.edit'),
                        'icon' => 'glyphicon glyphicon-edit',
                        'attributes' => array(
                            'rel' => 'tooltip',
                            'title' => $this->translator->trans('dtbundle.post.actions.edit'),
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
        return 'post_datatable';
    }
}

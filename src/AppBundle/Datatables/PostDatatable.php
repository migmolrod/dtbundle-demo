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
            $repository = $this->container->get("doctrine.orm.entity_manager")->getRepository($this->getEntity());
            $entity = $repository->find($line["id"]);

            // see if a User is logged in
            if ($this->container->get("security.authorization_checker")->isGranted("IS_AUTHENTICATED_FULLY")) {
                $user = $this->container->get("security.token_storage")->getToken()->getUser();
                // is the given User the author of this Post?
                $line["owner"] = $entity->isAuthor($user); // render "true" or "false"
            } else {
                // render a twig template with login link
                $line["owner"] = $this->container->get("templating")->render(":post:login_link.html.twig", array(
                    "entity" => $repository->find($line["id"])
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
        // the default settings, except "scroll_x"
        $this->features->setFeatures(array(
            "auto_width" => true,
            "defer_render" => false,
            "info" => true,
            "jquery_ui" => false,
            "length_change" => true,
            "ordering" => true,
            "paging" => true,
            "processing" => true,
            "scroll_x" => true,
            "scroll_y" => "",
            "searching" => true,
            "server_side" => true,
            "state_save" => false,
            "delay" => 0
        ));

        // the default settings, except "url"
        $this->ajax->setOptions(array(
            "url" => $this->container->get("router")->generate("post_results"),
            "type" => "GET"
        ));

        // the default settings, except "class", "individual_filtering", individual_filtering_position and "use_integration_options"
        $this->options->setOptions(array(
            "display_start" => 0,
            "dom" => "lfrtip", // default, but not used because "use_integration_options" = true
            "length_menu" => array(10, 25, 50, 100),
            "order_classes" => true,
            "order" => [[0, "asc"]],
            "order_multi" => true,
            "page_length" => 10,
            "paging_type" => Style::FULL_NUMBERS_PAGINATION,
            "renderer" => "", // default, but not used because "use_integration_options" = true
            "scroll_collapse" => false,
            "search_delay" => 0,
            "state_duration" => 7200,
            "stripe_classes" => array(),
            "responsive" => false,
            "class" => Style::BOOTSTRAP_3_STYLE . " table-condensed",
            "individual_filtering" => true,
            "individual_filtering_position" => "both",
            "use_integration_options" => true
        ));

        $this->columnBuilder
            ->add(null, "multiselect", array(
                "start_html" => '<div class="wrapper" id="wrapper">',
                "end_html" => '</div>',
                "attributes" => array(
                    "class" => "testclass",
                    "name" => "testname",
                ),
                "actions" => array(
                    array(
                        "route" => "post_bulk_delete",
                        "label" => "Delete",
                        "role" => "ROLE_ADMIN",
                        "icon" => "<i class='fa fa-times'></i>"
                    ),
                    array(
                        "route" => "post_bulk_invisible",
                        "label" => "Invisible",
                        "icon" => "<i class='fa fa-eye-slash'></i>",
                    )
                )
            ))
            ->add("id", "column", array(
                "class" => "",
                "padding" => "",
                "name" => "",
                "orderable" => true,
                "render" => null,
                "searchable" => true,
                "title" => "Id",
                "type" => "",
                "visible" => true,
                "width" => "40px",
                "default" => ""
            ))
            ->add("visible", "boolean", array(
                "class" => "",
                "padding" => "",
                "name" => "",
                "orderable" => false,
                "render" => "render_boolean",
                "searchable" => false,
                "title" => "Visible",
                "type" => "",
                "visible" => true,
                "width" => "40px",
                "true_icon" => "glyphicon glyphicon-ok",
                "false_icon" => "",
                "true_label" => "yes",
                "false_label" => "no"
            ))
            ->add("publishedAt", "datetime", array(
                "class" => "",
                "padding" => "",
                "name" => "daterange",
                "orderable" => true,
                "render" => "render_datetime",
                "date_format" => "lll",
                "searchable" => true,
                "title" => "<span class='glyphicon glyphicon-calendar' aria-hidden='true'></span> Published",
                "type" => "",
                "visible" => true,
                "width" => "120px"
            ))
            ->add("title", "column", array(
                "title" => "<span class='glyphicon glyphicon-book' aria-hidden='true'></span> Title",
                "width" => "120px"
            ))
            ->add("owner", "virtual", array(
                "title" => "Your Post"
            ))
            ->add("authorEmail", "column", array(
                "class" => "",
                "padding" => "",
                "name" => "",
                "orderable" => true,
                "render" => null,
                "searchable" => true,
                "title" => "<span class='glyphicon glyphicon-user' aria-hidden='true'></span> Author",
                "type" => "",
                "visible" => true,
                "width" => "",
                "default" => ""
            ))
            ->add("comments.title", "array", array(
                "title" => "Kommentare",
                "searchable" => false,
                "orderable" => false,
                "data" => "comments[, ].title",
                ))
            ->add(null, "action", array(
                "title" => "Actions",
                "start_html" => '<div class="wrapper">',
                "end_html" => '</div>',
                "actions" => array(
                    array(
                        "route" => "post_show",
                        "route_parameters" => array(
                            "id" => "id"
                        ),
                        "label" => "Show",
                        "icon" => "glyphicon glyphicon-eye-open",
                        "attributes" => array(
                            "rel" => "tooltip",
                            "title" => "Show",
                            "class" => "btn btn-default btn-xs",
                            "role" => "button"
                        ),
                        "role" => "ROLE_USER",
                        "render_if" => array("visible")
                    ),
                    array(
                        "route" => "post_edit",
                        "route_parameters" => array(
                            "id" => "id"
                        ),
                        "label" => "Edit",
                        "icon" => "glyphicon glyphicon-edit",
                        "attributes" => array(
                            "rel" => "tooltip",
                            "title" => "Edit",
                            "class" => "btn btn-primary btn-xs",
                            "role" => "button"
                        ),
                        "confirm" => true,
                        "confirm_message" => "Are you sure?",
                        "role" => "ROLE_ADMIN",
                    )
                )
            ));
    }

    /**
     * {@inheritdoc}
     */
    public function getEntity()
    {
        return "AppBundle\Entity\Post";
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return "post_datatable";
    }
}

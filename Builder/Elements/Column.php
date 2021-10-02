<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Elements;

use Goomento\PageBuilder\Builder\Managers\Elements;
use Goomento\PageBuilder\Builder\Managers\Widgets;
use Goomento\PageBuilder\Helper\StaticObjectManager;

/**
 * Class Column
 * @package Goomento\PageBuilder\Builder\Elements
 */
class Column extends \Goomento\PageBuilder\Builder\Base\Element
{

    /**
     * Get column name.
     *
     * Retrieve the column name.
     *
     *
     * @return string Column name.
     */
    public function getName()
    {
        return 'column';
    }

    /**
     * Get element type.
     *
     * Retrieve the element type, in this case `column`.
     *
     *
     * @return string The type.
     */
    public static function getType()
    {
        return 'column';
    }

    /**
     * Get column title.
     *
     * Retrieve the column title.
     *
     *
     * @return string Column title.
     */
    public function getTitle()
    {
        return __('Column');
    }

    /**
     * Get column icon.
     *
     * Retrieve the column icon.
     *
     *
     * @return string Column icon.
     */
    public function getIcon()
    {
        return 'fas fa-columns';
    }

    /**
     * Get initial config.
     *
     * Retrieve the current section initial configuration.
     *
     * Adds more configuration on top of the controls list, the tabs assigned to
     * the control, element name, type, icon and more. This method also adds
     * section presets.
     *
     *
     * @return array The initial config.
     */
    protected function _getInitialConfig()
    {
        $config = parent::_getInitialConfig();

        $config['controls'] = $this->getControls();
        $config['tabs_controls'] = $this->getTabsControls();

        return $config;
    }

    /**
     * Register column controls.
     *
     * Used to add new controls to the column element.
     *
     */
    protected function registerControls()
    {
        // Section Layout.
        $this->startControlsSection(
            'layout',
            [
                'label' => __('Layout'),
                'tab' => \Goomento\PageBuilder\Builder\Managers\Controls::TAB_LAYOUT,
            ]
        );

        // Element Name for the Navigator
        $this->addControl(
            '_title',
            [
                'label' => __('Title'),
                'type' => \Goomento\PageBuilder\Builder\Managers\Controls::HIDDEN,
                'render_type' => 'none',
            ]
        );

        $this->addResponsiveControl(
            '_inline_size',
            [
                'label' => __('Column Width') . ' (%)',
                'type' => \Goomento\PageBuilder\Builder\Managers\Controls::NUMBER,
                'min' => 2,
                'max' => 98,
                'required' => true,
                'device_args' => [
                    \Goomento\PageBuilder\Builder\Base\ControlsStack::RESPONSIVE_TABLET => [
                        'max' => 100,
                        'required' => false,
                    ],
                    \Goomento\PageBuilder\Builder\Base\ControlsStack::RESPONSIVE_MOBILE => [
                        'max' => 100,
                        'required' => false,
                    ],
                ],
                'min_affected_device' => [
                    \Goomento\PageBuilder\Builder\Base\ControlsStack::RESPONSIVE_DESKTOP => \Goomento\PageBuilder\Builder\Base\ControlsStack::RESPONSIVE_TABLET,
                    \Goomento\PageBuilder\Builder\Base\ControlsStack::RESPONSIVE_TABLET => \Goomento\PageBuilder\Builder\Base\ControlsStack::RESPONSIVE_TABLET,
                ],
                'selectors' => [
                    '{{WRAPPER}}' => 'width: {{VALUE}}%',
                ],
            ]
        );

        $this->addResponsiveControl(
            'content_position',
            [
                'label' => __('Vertical Align'),
                'type' => \Goomento\PageBuilder\Builder\Managers\Controls::SELECT,
                'default' => '',
                'options' => [
                    '' => __('Default'),
                    'top' => __('Top'),
                    'center' => __('Middle'),
                    'bottom' => __('Bottom'),
                    'space-between' => __('Space Between'),
                    'space-around' => __('Space Around'),
                    'space-evenly' => __('Space Evenly'),
                ],
                'selectors_dictionary' => [
                    'top' => 'flex-start',
                    'bottom' => 'flex-end',
                ],
                'selectors' => [
                    // TODO: The following line is for BC since 2.7.0
                    '.gmt-bc-flex-widget {{WRAPPER}}.gmt-column .gmt-column-wrap' => 'align-items: {{VALUE}}',
                    // This specificity is intended to make sure column css overwrites section css on vertical alignment (content_position)
                    '{{WRAPPER}}.gmt-column.gmt-element[data-element_type="column"] > .gmt-column-wrap.gmt-element-populated > .gmt-widget-wrap' => 'align-content: {{VALUE}}; align-items: {{VALUE}};',
                ],
            ]
        );

        $this->addResponsiveControl(
            'align',
            [
                'label' => __('Horizontal Align'),
                'type' => \Goomento\PageBuilder\Builder\Managers\Controls::SELECT,
                'default' => '',
                'options' => [
                    '' => __('Default'),
                    'flex-start' => __('Start'),
                    'center' => __('Center'),
                    'flex-end' => __('End'),
                    'space-between' => __('Space Between'),
                    'space-around' => __('Space Around'),
                    'space-evenly' => __('Space Evenly'),
                ],
                'selectors' => [
                    '{{WRAPPER}}.gmt-column > .gmt-column-wrap > .gmt-widget-wrap' => 'justify-content: {{VALUE}}',
                ],
            ]
        );

        $this->addResponsiveControl(
            'space_between_widgets',
            [
                'label' => __('Widgets Space') . ' (px)',
                'type' => \Goomento\PageBuilder\Builder\Managers\Controls::NUMBER,
                'placeholder' => 20,
                'selectors' => [
                    '{{WRAPPER}} > .gmt-column-wrap > .gmt-widget-wrap > .gmt-widget:not(.gmt-widget__width-auto):not(.gmt-widget__width-initial):not(:last-child):not(.gmt-absolute)' => 'margin-bottom: {{VALUE}}px', //Need the full path for exclude the inner section
                ],
            ]
        );

        $possible_tags = [
            'div',
            'header',
            'footer',
            'main',
            'article',
            'section',
            'aside',
            'nav',
        ];

        $options = [
            '' => __('Default'),
        ] + array_combine($possible_tags, $possible_tags);

        $this->addControl(
            'html_tag',
            [
                'label' => __('HTML Tag'),
                'type' => \Goomento\PageBuilder\Builder\Managers\Controls::SELECT,
                'options' => $options,
                'render_type' => 'none',
            ]
        );

        $this->endControlsSection();

        $this->startControlsSection(
            'section_style',
            [
                'label' => __('Background'),
                'tab' => \Goomento\PageBuilder\Builder\Managers\Controls::TAB_STYLE,
            ]
        );

        $this->startControlsTabs('tabs_background');

        $this->startControlsTab(
            'tab_background_normal',
            [
                'label' => __('Normal'),
            ]
        );

        $this->addGroupControl(
            \Goomento\PageBuilder\Builder\Controls\Groups\Background::getType(),
            [
                'name' => 'background',
                'types' => [ 'classic', 'gradient', 'slideshow' ],
                'selector' => '{{WRAPPER}}:not(.gmt-motion-effects-element-type-background) > .gmt-element-populated, {{WRAPPER}} > .gmt-column-wrap > .gmt-motion-effects-container > .gmt-motion-effects-layer',
                'fields_options' => [
                    'background' => [
                        'frontend_available' => true,
                    ],
                ],
            ]
        );

        $this->endControlsTab();

        $this->startControlsTab(
            'tab_background_hover',
            [
                'label' => __('Hover'),
            ]
        );

        $this->addGroupControl(
            \Goomento\PageBuilder\Builder\Controls\Groups\Background::getType(),
            [
                'name' => 'background_hover',
                'selector' => '{{WRAPPER}}:hover > .gmt-element-populated',
            ]
        );

        $this->addControl(
            'background_hover_transition',
            [
                'label' => __('Transition Duration'),
                'type' => \Goomento\PageBuilder\Builder\Managers\Controls::SLIDER,
                'default' => [
                    'size' => 0.3,
                ],
                'range' => [
                    'px' => [
                        'max' => 3,
                        'step' => 0.1,
                    ],
                ],
                'render_type' => 'ui',
                'separator' => 'before',
            ]
        );

        $this->endControlsTab();

        $this->endControlsTabs();

        $this->endControlsSection();

        // Section Column Background Overlay.
        $this->startControlsSection(
            'section_background_overlay',
            [
                'label' => __('Background Overlay'),
                'tab' => \Goomento\PageBuilder\Builder\Managers\Controls::TAB_STYLE,
                'condition' => [
                    'background_background' => [ 'classic', 'gradient' ],
                ],
            ]
        );

        $this->startControlsTabs('tabs_background_overlay');

        $this->startControlsTab(
            'tab_background_overlay_normal',
            [
                'label' => __('Normal'),
            ]
        );

        $this->addGroupControl(
            \Goomento\PageBuilder\Builder\Controls\Groups\Background::getType(),
            [
                'name' => 'background_overlay',
                'selector' => '{{WRAPPER}} > .gmt-element-populated >  .gmt-background-overlay',
            ]
        );

        $this->addControl(
            'background_overlay_opacity',
            [
                'label' => __('Opacity'),
                'type' => \Goomento\PageBuilder\Builder\Managers\Controls::SLIDER,
                'default' => [
                    'size' => .5,
                ],
                'range' => [
                    'px' => [
                        'max' => 1,
                        'step' => 0.01,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} > .gmt-element-populated >  .gmt-background-overlay' => 'opacity: {{SIZE}};',
                ],
                'condition' => [
                    'background_overlay_background' => [ 'classic', 'gradient' ],
                ],
            ]
        );

        $this->addGroupControl(
            \Goomento\PageBuilder\Builder\Controls\Groups\CssFilter::getType(),
            [
                'name' => 'css_filters',
                'selector' => '{{WRAPPER}} > .gmt-element-populated >  .gmt-background-overlay',
            ]
        );

        $this->addControl(
            'overlay_blend_mode',
            [
                'label' => __('Blend Mode'),
                'type' => \Goomento\PageBuilder\Builder\Managers\Controls::SELECT,
                'options' => [
                    '' => __('Normal'),
                    'multiply' => 'Multiply',
                    'screen' => 'Screen',
                    'overlay' => 'Overlay',
                    'darken' => 'Darken',
                    'lighten' => 'Lighten',
                    'color-dodge' => 'Color Dodge',
                    'saturation' => 'Saturation',
                    'color' => 'Color',
                    'luminosity' => 'Luminosity',
                ],
                'selectors' => [
                    '{{WRAPPER}} > .gmt-element-populated > .gmt-background-overlay' => 'mix-blend-mode: {{VALUE}}',
                ],
            ]
        );

        $this->endControlsTab();

        $this->startControlsTab(
            'tab_background_overlay_hover',
            [
                'label' => __('Hover'),
            ]
        );

        $this->addGroupControl(
            \Goomento\PageBuilder\Builder\Controls\Groups\Background::getType(),
            [
                'name' => 'background_overlay_hover',
                'selector' => '{{WRAPPER}}:hover > .gmt-element-populated >  .gmt-background-overlay',
            ]
        );

        $this->addControl(
            'background_overlay_hover_opacity',
            [
                'label' => __('Opacity'),
                'type' => \Goomento\PageBuilder\Builder\Managers\Controls::SLIDER,
                'default' => [
                    'size' => .5,
                ],
                'range' => [
                    'px' => [
                        'max' => 1,
                        'step' => 0.01,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}}:hover > .gmt-element-populated >  .gmt-background-overlay' => 'opacity: {{SIZE}};',
                ],
                'condition' => [
                    'background_overlay_hover_background' => [ 'classic', 'gradient' ],
                ],
            ]
        );

        $this->addGroupControl(
            \Goomento\PageBuilder\Builder\Controls\Groups\CssFilter::getType(),
            [
                'name' => 'css_filters_hover',
                'selector' => '{{WRAPPER}}:hover > .gmt-element-populated >  .gmt-background-overlay',
            ]
        );

        $this->addControl(
            'background_overlay_hover_transition',
            [
                'label' => __('Transition Duration'),
                'type' => \Goomento\PageBuilder\Builder\Managers\Controls::SLIDER,
                'default' => [
                    'size' => 0.3,
                ],
                'range' => [
                    'px' => [
                        'max' => 3,
                        'step' => 0.1,
                    ],
                ],
                'render_type' => 'ui',
                'separator' => 'before',
            ]
        );

        $this->endControlsTab();

        $this->endControlsTabs();

        $this->endControlsSection();

        $this->startControlsSection(
            'section_border',
            [
                'label' => __('Border'),
                'tab' => \Goomento\PageBuilder\Builder\Managers\Controls::TAB_STYLE,
            ]
        );

        $this->startControlsTabs('tabs_border');

        $this->startControlsTab(
            'tab_border_normal',
            [
                'label' => __('Normal'),
            ]
        );

        $this->addGroupControl(
            \Goomento\PageBuilder\Builder\Controls\Groups\Border::getType(),
            [
                'name' => 'border',
                'selector' => '{{WRAPPER}} > .gmt-element-populated',
            ]
        );

        $this->addResponsiveControl(
            'border_radius',
            [
                'label' => __('Border Radius'),
                'type' => \Goomento\PageBuilder\Builder\Managers\Controls::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} > .gmt-element-populated, {{WRAPPER}} > .gmt-element-populated > .gmt-background-overlay, {{WRAPPER}} > .gmt-background-slideshow' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->addGroupControl(
            \Goomento\PageBuilder\Builder\Controls\Groups\BoxShadow::getType(),
            [
                'name' => 'box_shadow',
                'selector' => '{{WRAPPER}} > .gmt-element-populated',
            ]
        );

        $this->endControlsTab();

        $this->startControlsTab(
            'tab_border_hover',
            [
                'label' => __('Hover'),
            ]
        );

        $this->addGroupControl(
            \Goomento\PageBuilder\Builder\Controls\Groups\Border::getType(),
            [
                'name' => 'border_hover',
                'selector' => '{{WRAPPER}}:hover > .gmt-element-populated',
            ]
        );

        $this->addResponsiveControl(
            'border_radius_hover',
            [
                'label' => __('Border Radius'),
                'type' => \Goomento\PageBuilder\Builder\Managers\Controls::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}}:hover > .gmt-element-populated, {{WRAPPER}}:hover > .gmt-element-populated > .gmt-background-overlay' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->addGroupControl(
            \Goomento\PageBuilder\Builder\Controls\Groups\BoxShadow::getType(),
            [
                'name' => 'box_shadow_hover',
                'selector' => '{{WRAPPER}}:hover > .gmt-element-populated',
            ]
        );

        $this->addControl(
            'border_hover_transition',
            [
                'label' => __('Transition Duration'),
                'type' => \Goomento\PageBuilder\Builder\Managers\Controls::SLIDER,
                'separator' => 'before',
                'default' => [
                    'size' => 0.3,
                ],
                'range' => [
                    'px' => [
                        'max' => 3,
                        'step' => 0.1,
                    ],
                ],
                'conditions' => [
                    'relation' => 'or',
                    'terms' => [
                        [
                            'name' => 'background_background',
                            'operator' => '!==',
                            'value' => '',
                        ],
                        [
                            'name' => 'border_border',
                            'operator' => '!==',
                            'value' => '',
                        ],
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} > .gmt-element-populated' => 'transition: background {{background_hover_transition.SIZE}}s, border {{SIZE}}s, border-radius {{SIZE}}s, box-shadow {{SIZE}}s',
                    '{{WRAPPER}} > .gmt-element-populated > .gmt-background-overlay' => 'transition: background {{background_overlay_hover_transition.SIZE}}s, border-radius {{SIZE}}s, opacity {{background_overlay_hover_transition.SIZE}}s',
                ],
            ]
        );

        $this->endControlsTab();

        $this->endControlsTabs();

        $this->endControlsSection();

        // Section Typography.
        $this->startControlsSection(
            'section_typo',
            [
                'label' => __('Typography'),
                'type' => \Goomento\PageBuilder\Builder\Managers\Controls::SECTION,
                'tab' => \Goomento\PageBuilder\Builder\Managers\Controls::TAB_STYLE,
            ]
        );

        if (in_array(\Goomento\PageBuilder\Builder\Schemes\Color::getType(), \Goomento\PageBuilder\Builder\Managers\Schemes::getEnabledSchemes(), true)) {
            $this->addControl(
                'colors_warning',
                [
                    'type' => \Goomento\PageBuilder\Builder\Managers\Controls::RAW_HTML,
                    'raw' => __('Note: The following colors won\'t work if Default Colors are enabled.'),
                    'content_classes' => 'gmt-panel-alert gmt-panel-alert-warning',
                ]
            );
        }

        $this->addControl(
            'heading_color',
            [
                'label' => __('Heading Color'),
                'type' => \Goomento\PageBuilder\Builder\Managers\Controls::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .gmt-element-populated .gmt-heading-title' => 'color: {{VALUE}};',
                ],
                'separator' => 'none',
            ]
        );

        $this->addControl(
            'color_text',
            [
                'label' => __('Text Color'),
                'type' => \Goomento\PageBuilder\Builder\Managers\Controls::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} > .gmt-element-populated' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->addControl(
            'color_link',
            [
                'label' => __('Link Color'),
                'type' => \Goomento\PageBuilder\Builder\Managers\Controls::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .gmt-element-populated a' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->addControl(
            'color_link_hover',
            [
                'label' => __('Link Hover Color'),
                'type' => \Goomento\PageBuilder\Builder\Managers\Controls::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .gmt-element-populated a:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->addControl(
            'text_align',
            [
                'label' => __('Text Align'),
                'type' => \Goomento\PageBuilder\Builder\Managers\Controls::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => __('Left'),
                        'icon' => 'fas fa-align-left',
                    ],
                    'center' => [
                        'title' => __('Center'),
                        'icon' => 'fas fa-align-center',
                    ],
                    'right' => [
                        'title' => __('Right'),
                        'icon' => 'fas fa-align-right',
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} > .gmt-element-populated' => 'text-align: {{VALUE}};',
                ],
            ]
        );

        $this->endControlsSection();

        // Section Advanced.
        $this->startControlsSection(
            'section_advanced',
            [
                'label' => __('Advanced'),
                'type' => \Goomento\PageBuilder\Builder\Managers\Controls::SECTION,
                'tab' => \Goomento\PageBuilder\Builder\Managers\Controls::TAB_ADVANCED,
            ]
        );

        $this->addResponsiveControl(
            'margin',
            [
                'label' => __('Margin'),
                'type' => \Goomento\PageBuilder\Builder\Managers\Controls::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} > .gmt-element-populated' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->addResponsiveControl(
            'padding',
            [
                'label' => __('Padding'),
                'type' => \Goomento\PageBuilder\Builder\Managers\Controls::DIMENSIONS,
                'size_units' => [ 'px', 'em', '%' ],
                'selectors' => [
                    '{{WRAPPER}} > .gmt-element-populated' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->addControl(
            'z_index',
            [
                'label' => __('Z-Index'),
                'type' => \Goomento\PageBuilder\Builder\Managers\Controls::NUMBER,
                'min' => 0,
                'selectors' => [
                    '{{WRAPPER}}' => 'z-index: {{VALUE}};',
                ],
                'label_block' => false,
            ]
        );

        $this->addControl(
            '_element_id',
            [
                'label' => __('CSS ID'),
                'type' => \Goomento\PageBuilder\Builder\Managers\Controls::TEXT,
                'default' => '',
                'dynamic' => [
                    'active' => true,
                ],
                'title' => __('Add your custom id WITHOUT the Pound key. e.g: my-id'),
                'label_block' => false,
                'style_transfer' => false,
                'classes' => 'gmt-control-direction-ltr',
            ]
        );

        $this->addControl(
            'css_classes',
            [
                'label' => __('CSS Classes'),
                'type' => \Goomento\PageBuilder\Builder\Managers\Controls::TEXT,
                'default' => '',
                'dynamic' => [
                    'active' => true,
                ],
                'prefix_class' => '',
                'title' => __('Add your custom class WITHOUT the dot. e.g: my-class'),
                'label_block' => false,
                'classes' => 'gmt-control-direction-ltr',
            ]
        );

        // TODO: Backward comparability for deprecated controls
        $this->addControl(
            'screen_sm',
            [
                'type' => \Goomento\PageBuilder\Builder\Managers\Controls::HIDDEN,
            ]
        );

        $this->addControl(
            'screen_sm_width',
            [
                'type' => \Goomento\PageBuilder\Builder\Managers\Controls::HIDDEN,
                'condition' => [
                    'screen_sm' => [ 'custom' ],
                ],
                'prefix_class' => 'gmt-sm-',
            ]
        );
        // END Backward comparability

        $this->endControlsSection();

        $this->startControlsSection(
            'section_effects',
            [
                'label' => __('Motion Effects'),
                'tab' => \Goomento\PageBuilder\Builder\Managers\Controls::TAB_ADVANCED,
            ]
        );

        $this->addResponsiveControl(
            'animation',
            [
                'label' => __('Entrance Animation'),
                'type' => \Goomento\PageBuilder\Builder\Managers\Controls::ANIMATION,
                'frontend_available' => true,
            ]
        );

        $this->addControl(
            'animation_duration',
            [
                'label' => __('Animation Duration'),
                'type' => \Goomento\PageBuilder\Builder\Managers\Controls::SELECT,
                'default' => '',
                'options' => [
                    'slow' => __('Slow'),
                    '' => __('Normal'),
                    'fast' => __('Fast'),
                ],
                'prefix_class' => 'animated-',
                'condition' => [
                    'animation!' => '',
                ],
            ]
        );

        $this->addControl(
            'animation_delay',
            [
                'label' => __('Animation Delay') . ' (ms)',
                'type' => \Goomento\PageBuilder\Builder\Managers\Controls::NUMBER,
                'default' => '',
                'min' => 0,
                'step' => 100,
                'condition' => [
                    'animation!' => '',
                ],
                'render_type' => 'none',
                'frontend_available' => true,
            ]
        );

        $this->endControlsSection();

        $this->startControlsSection(
            '_section_responsive',
            [
                'label' => __('Responsive'),
                'tab' => \Goomento\PageBuilder\Builder\Managers\Controls::TAB_ADVANCED,
            ]
        );

        $this->addControl(
            'responsive_description',
            [
                'raw' => __('Responsive visibility will take effect only on preview or live page, and not while editing in Goomento.'),
                'type' => \Goomento\PageBuilder\Builder\Managers\Controls::RAW_HTML,
                'content_classes' => 'gmt-descriptor',
            ]
        );

        $this->addControl(
            'hide_desktop',
            [
                'label' => __('Hide On Desktop'),
                'type' => \Goomento\PageBuilder\Builder\Managers\Controls::SWITCHER,
                'default' => '',
                'prefix_class' => 'gmt-',
                'label_on' => 'Hide',
                'label_off' => 'Show',
                'return_value' => 'hidden-desktop',
            ]
        );

        $this->addControl(
            'hide_tablet',
            [
                'label' => __('Hide On Tablet'),
                'type' => \Goomento\PageBuilder\Builder\Managers\Controls::SWITCHER,
                'default' => '',
                'prefix_class' => 'gmt-',
                'label_on' => 'Hide',
                'label_off' => 'Show',
                'return_value' => 'hidden-tablet',
            ]
        );

        $this->addControl(
            'hide_mobile',
            [
                'label' => __('Hide On Mobile'),
                'type' => \Goomento\PageBuilder\Builder\Managers\Controls::SWITCHER,
                'default' => '',
                'prefix_class' => 'gmt-',
                'label_on' => 'Hide',
                'label_off' => 'Show',
                'return_value' => 'hidden-phone',
            ]
        );

        $this->endControlsSection();

        StaticObjectManager::get(\Goomento\PageBuilder\Builder\Managers\Controls::class)->addCustomCssControls($this);
    }

    /**
     * Render column output in the editor.
     *
     * Used to generate the live preview, using a Backbone JavaScript template.
     *
     */
    protected function contentTemplate()
    {
        ?>
		<div class="gmt-column-wrap">
			<div class="gmt-background-overlay"></div>
			<div class="gmt-widget-wrap"></div>
		</div>
		<?php
    }

    /**
     * Before column rendering.
     *
     * Used to add stuff before the column element.
     *
     */
    public function beforeRender()
    {
        $settings = $this->getSettingsForDisplay();

        $has_background_overlay = in_array($settings['background_overlay_background'], [ 'classic', 'gradient' ], true) ||
                                  in_array($settings['background_overlay_hover_background'], [ 'classic', 'gradient' ], true);

        $column_wrap_classes = [ 'gmt-column-wrap' ];

        if ($this->getChildren()) {
            $column_wrap_classes[] = ' gmt-element-populated';
        }

        $this->addRenderAttribute([
            '_inner_wrapper' => [
                'class' => $column_wrap_classes,
            ],
            '_widget_wrapper' => [
                'class' => [ 'gmt-widget-wrap' ],
            ],
            '_background_overlay' => [
                'class' => [ 'gmt-background-overlay' ],
            ],
        ]); ?>
		<<?= $this->getHtmlTag() . ' ' . $this->getRenderAttributeString('_wrapper'); ?>>
			<div <?= $this->getRenderAttributeString('_inner_wrapper'); ?>>
			<?php if ($has_background_overlay) : ?>
				<div <?= $this->getRenderAttributeString('_background_overlay'); ?>></div>
			<?php endif; ?>
		<div <?= $this->getRenderAttributeString('_widget_wrapper'); ?>>
		<?php
    }

    /**
     * After column rendering.
     *
     * Used to add stuff after the column element.
     *
     */
    public function afterRender()
    {
        ?>
				</div>
			</div>
		</<?= $this->getHtmlTag(); ?>>
		<?php
    }

    /**
     * Add column render attributes.
     *
     * Used to add attributes to the current column wrapper HTML tag.
     *
     */
    protected function _addRenderAttributes()
    {
        parent::_addRenderAttributes();

        $is_inner = $this->getData('isInner');

        $column_type = ! empty($is_inner) ? 'inner' : 'top';

        $settings = $this->getSettings();

        $this->addRenderAttribute(
            '_wrapper',
            'class',
            [
                'gmt-column',
                'gmt-col-' . $settings['_column_size'],
                'gmt-' . $column_type . '-column',
            ]
        );
    }

    /**
     * Get default child type.
     *
     * Retrieve the column child type based on element data.
     *
     * @param array $element_data Element ID.
     *
     */
    protected function _getDefaultChildType(array $element_data)
    {
        if ('section' === $element_data['elType']) {
            /** @var Elements $elements */
            $elements = StaticObjectManager::get(Elements::class);
            return $elements->getElementTypes('section');
        }
        /** @var Widgets $widgets */
        $widgets = StaticObjectManager::get(Widgets::class);
        return $widgets->getWidgetTypes($element_data['widgetType']);
    }

    /**
     * Get HTML tag.
     *
     * Retrieve the column element HTML tag.
     *
     *
     * @return string Column HTML tag.
     */
    private function getHtmlTag()
    {
        $html_tag = $this->getSettings('html_tag');

        if (empty($html_tag)) {
            $html_tag = 'div';
        }

        return $html_tag;
    }
}
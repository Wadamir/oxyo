<?php
class ControllerExtensionModuleOxyoLayerslider extends Controller
{
    public function index($setting)
    {
        static $module = 0;

        // Add required sources
        $this->document->addScript('catalog/view/theme/oxyo/js/masterslider.js');

        if ($this->request->server['HTTPS']) {
            $server = $this->config->get('config_ssl');
            $base_url = HTTPS_SERVER;
        } else {
            $server = $this->config->get('config_url');
            $base_url = HTTP_SERVER;
        }

        // Load Google Fonts		
        if (isset($setting['g_fonts'])) {
            $data['g_fonts'] = array();
            $import = '';
            foreach ($setting['g_fonts'] as $g_font) {
                $import .= $g_font['import'] . '%7C';
            }
            $this->document->addStyle('//fonts.googleapis.com/css?family=' . $import);
        }

        // General Settings
        $data['width'] = $setting['width'];
        $data['height'] = $setting['height'];
        $data['min_height'] = $setting['min_height'];
        $data['full_width'] = $setting['full_width'];
        $data['margin_bottom'] = $setting['margin_bottom'];
        $data['loop'] = $setting['loop'];
        $data['speed'] = $setting['speed'];
        $data['nav_buttons'] = $setting['nav_buttons'] ? 1 : '';
        $data['slide_transition'] = $setting['slide_transition'];
        $data['nav_timer_bar'] = $setting['nav_timer_bar'];
        $data['nav_bullets'] = $setting['nav_bullets'];

        // Slides & Layers
        if (isset($setting['sections'])) {
            $data['sections'] = array();

            //$section_row = 0;
            function sortSlides($a, $b)
            {
                return strcmp($a['sort_order'], $b['sort_order']);
            }
            usort($setting['sections'], 'sortSlides');
            foreach ($setting['sections'] as $section) {

                $groups = array();
                //$group_row = 0;

                if (isset($section['groups'])) {
                    foreach ($section['groups'] as $group) {
                        // var_dump($group);
                        $escape_attr = function ($value) {
                            return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
                        };

                        if (isset($group['description'][$this->config->get('config_language_id')])) {
                            $description = html_entity_decode($group['description'][$this->config->get('config_language_id')], ENT_QUOTES, 'UTF-8');
                        } else {
                            $description = false;
                        }

                        if (isset($group['image'][$this->config->get('config_language_id')])) {
                            $image = $base_url . 'image/' . $group['image'][$this->config->get('config_language_id')];
                        } else {
                            $image = false;
                        }

                        $left = (isset($group['left'])) ? $group['left'] : '';
                        $right = (isset($group['right'])) ? $group['right'] : '';
                        $top = (isset($group['top'])) ? $group['top'] : '';
                        $bottom = (isset($group['bottom'])) ? $group['bottom'] : '';

                        $font = (isset($group['font'])) ? $group['font'] : '';
                        $font_weight = (isset($group['font_weight'])) ? $group['font_weight'] : '';
                        $font_size = (isset($group['font_size'])) ? $group['font_size'] . 'px' : '';
                        $color = (isset($group['color'])) ? $group['color'] : '';
                        $min_height = (isset($group['min_height'])) ? $group['min_height'] . 'px' : '';
                        $bg_color = (isset($group['bg_color'])) ? $group['bg_color'] : '';
                        $padding = (isset($group['padding'])) ? $group['padding'] : '';
                        $border_radius = (isset($group['border_radius'])) ? $group['border_radius'] : '';

                        if (isset($group['customcss'])) {
                            $customcss = $group['customcss'];
                        } else {
                            $customcss = '';
                        }

                        if (isset($group['start'])) {
                            $start = $group['start'];
                        } else {
                            $start = '300';
                        }
                        if (isset($group['end'])) {
                            $end = $group['end'];
                        } else {
                            $end = '5700';
                        }
                        if (isset($group['durationin'])) {
                            $durationin = $group['durationin'];
                        } else {
                            $durationin = '500';
                        }
                        if (isset($group['durationout'])) {
                            $durationout = $group['durationout'];
                        } else {
                            $durationout = '500';
                        }
                        if (isset($group['easingin'])) {
                            $easingin = $group['easingin'];
                        } else {
                            $easingin = 'linear';
                        }
                        if (isset($group['easingout'])) {
                            $easingout = $group['easingout'];
                        } else {
                            $easingout = 'linear';
                        }
                        if (isset($group['easingout'])) {
                            $easingout = $group['easingout'];
                        } else {
                            $easingout = 'linear';
                        }
                        if (isset($group['transitionin'])) {
                            $transitionin = $group['transitionin'];
                        } else {
                            $transitionin = '';
                        }
                        if (isset($group['transitionout'])) {
                            $transitionout = $group['transitionout'];
                        } else {
                            $transitionout = '';
                        }

                        $group_attributes = array(
                            'data-origin="ml"',
                            'data-type="' . $escape_attr($group['type']) . '"',
                            'data-resize="false"',
                            'data-offset-x="' . $escape_attr($left) . '"',
                            'data-offset-y="' . $escape_attr($top) . '"',
                            // 'data-parallax="' . $escape_attr($p_index) . '"',
                            'data-duration="' . $escape_attr($durationin) . '"',
                            'data-delay="' . $escape_attr($start) . '"'
                        );

                        if ($transitionin && $transitionin !== 'none') {
                            $group_attributes[] = 'data-effect="' . $escape_attr($transitionin) . '"';
                        }

                        if ($easingin && $easingin !== 'none') {
                            $group_attributes[] = 'data-ease="' . $escape_attr($easingin) . '"';
                        }

                        if ($transitionout && $transitionout !== 'none') {
                            $group_attributes[] = 'data-hide-effect="' . $escape_attr($transitionout) . '"';
                            $group_attributes[] = 'data-hide-duration="' . $escape_attr($durationout) . '"';
                            $group_attributes[] = 'data-hide-time="' . $escape_attr($end) . '"';
                        }

                        if ($easingout && $easingout !== 'none') {
                            $group_attributes[] = 'data-hide-ease="' . $escape_attr($easingout) . '"';
                        }
                        if (isset($group['button_class'])) {
                            $button_class = $group['button_class'];
                        } else {
                            $button_class = 'ls_btn ls_btn_dark';
                        }
                        if (isset($group['button_target'])) {
                            $button_target = $group['button_target'];
                        } else {
                            $button_target = false;
                        }
                        if (isset($group['button_href'])) {
                            $button_href = $group['button_href'];
                        } else {
                            $button_href = '#';
                        }
                        if (isset($group['p_index'])) {
                            $p_index = $group['p_index'];
                        } else {
                            $p_index = '0';
                        }
                        if (isset($group['sort_order'])) {
                            $sort_order = $group['sort_order'];
                        } else {
                            $sort_order = '0';
                        }

                        // Group Style
                        $group_style = '';
                        $group_style .= ($left) ? 'left:' . $left . ';' : '';
                        $group_style .= ($right) ? 'right:' . $right . ';' : '';
                        $group_style .= ($top) ? 'top:' . $top . ';' : '';
                        $group_style .= ($bottom) ? 'bottom:' . $bottom . ';' : '';
                        $group_style .= ($font) ? 'font-family:' . $font . ';' : '';
                        $group_style .= ($font_weight) ? 'font-weight:' . $font_weight . ';' : '';
                        $group_style .= ($font_size) ? 'font-size:' . $font_size . ';' : '';
                        $group_style .= ($color) ? 'color:' . $color . ';' : '';
                        $group_style .= ($bg_color) ? 'background-color:' . $bg_color . ';' : '';
                        $group_style .= ($padding) ? 'padding:' . $padding . ';' : '';
                        $group_style .= ($border_radius) ? 'border-radius:' . $border_radius . ';' : '';

                        //$group_row++;

                        //$groups[$group['sort_order']] = array(
                        $groups[] = array(
                            //'id'          		=> $group_row,
                            'type'                  => $group['type'],
                            'description'           => $description,
                            'image'                 => $image,
                            'left'                  => $left,
                            'right'                 => $right,
                            'top'                   => $top,
                            'bottom'                => $bottom,
                            'font'                  => $font,
                            'p_index'               => $p_index,
                            'sort_order'            => $sort_order,
                            'min_height'            => $min_height,
                            'font_weight'           => $font_weight,
                            'font_size'             => $font_size,
                            'color'                 => $color,
                            'bg_color'              => $bg_color,
                            'padding'               => $padding,
                            'border_radius'         => $border_radius,
                            'customcss'             => $customcss,
                            'start'                 => $start,
                            'end'                   => $end,
                            'durationin'            => $durationin,
                            'durationout'           => $durationout,
                            'easingin'              => $easingin,
                            'easingout'             => $easingout,
                            'transitionin'          => $transitionin,
                            'transitionout'         => $transitionout,
                            'group_attributes'      => implode(' ', $group_attributes),
                            'button_class'          => $button_class,
                            'button_target'         => $button_target,
                            'button_href'           => $button_href,
                            'group_style'           => $group_style
                        );
                    }
                }

                usort($groups, function ($a, $b) {
                    return $a['sort_order'] - $b['sort_order'];
                });

                //$section_row++;

                //$data['sections'][$section['sort_order']] = array(
                $slide_style_array = array();
                if ($section['bg_color']) {
                    $slide_style_array[] = 'background-color:' . $section['bg_color'] . ';';
                }
                if ($section['thumb_image']) {
                    $slide_style_array[] = 'background-image:url(' . $base_url . 'image/' . $section['thumb_image'] . ');';
                }
                $slide_style = implode('; ', $slide_style_array);
                $data['sections'][] = array(
                    'link'              => $section['link'],
                    'link_new_window'   => $section['link_new_window'],
                    'duration'          => $section['duration'],
                    'slide_kenburns'    => $section['slide_kenburns'],
                    'bg_color'          => $section['bg_color'],
                    'style'             => $slide_style,
                    'is_bg'             => $section['thumb_image'],
                    'thumb_image'       => $base_url . 'image/' . $section['thumb_image'],
                    'sort_order'        => $section['sort_order'],
                    'groups'            => $groups
                );
            }

            usort($data['sections'], function ($a, $b) {
                return $a['sort_order'] - $b['sort_order'];
            });

            $data['active_slides_count'] = 0;
            foreach ($data['sections'] as $section_data) {
                if ((int)$section_data['sort_order'] > 0) {
                    $data['active_slides_count']++;
                }
            }

            //ksort($data['sections']);

            $data['module'] = $module++;

            if ($this->config->get('theme_default_directory') == 'oxyo')
                return $this->load->view('extension/module/oxyo_layerslider', $data);
        }
    }
}

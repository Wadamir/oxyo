<?php
class ControllerExtensionModuleBlogLatest extends Controller
{
    public function index($setting)
    {

        static $module = 0;

        $this->load->language('blog/blog');
        $this->load->model('extension/blog/blog');
        $this->load->model('tool/image');

        $data = array(
            'start' => 0,
            'limit' => $setting['limit']
        );

        // RTL support
        $data['direction'] = $this->language->get('direction');

        // Block title
        $data['block_title'] = $setting['use_title'];
        $data['title_preline'] = false;
        $data['title'] = false;
        $data['title_subline'] = false;

        if (!empty($setting['title_pl'][$this->config->get('config_language_id')])) {
            $data['title_preline'] = html_entity_decode($setting['title_pl'][$this->config->get('config_language_id')], ENT_QUOTES, 'UTF-8');
        }
        if (!empty($setting['title_m'][$this->config->get('config_language_id')])) {
            $data['title'] = html_entity_decode($setting['title_m'][$this->config->get('config_language_id')], ENT_QUOTES, 'UTF-8');
        }
        if (!empty($setting['title_b'][$this->config->get('config_language_id')])) {
            $data['title_subline'] = html_entity_decode($setting['title_b'][$this->config->get('config_language_id')], ENT_QUOTES, 'UTF-8');
        }

        $data['contrast'] = $setting['contrast'];
        $data['characters'] = $setting['characters'];
        $data['columns'] = $setting['columns'];
        $data['thumb'] = $setting['use_thumb'];
        $data['carousel'] = $setting['carousel'];
        $data['carousel_a'] = $setting['carousel_a'];
        $data['carousel_b'] = $setting['carousel_b'];
        $data['rows'] = $setting['rows'];
        $data['use_button'] = $setting['use_button'];
        $data['use_margin'] = $setting['use_margin'];
        $data['margin'] = $setting['margin'];
        $data['img_width'] = $setting['width'];

        foreach ($this->model_extension_blog_blog->getLatestBlogs($data) as $result) {

            if ($result['tags']) {
                $tags = explode(',', $result['tags']);
            } else {
                $tags = false;
            }

            if ($setting['characters']) {
                $description = utf8_substr(strip_tags(html_entity_decode($result['short_description'], ENT_QUOTES, 'UTF-8')), 0, $setting['characters']) . '..';
            } else {
                $description = false;
            }



            // <iframe src="https://vk.com/video_ext.php?oid=-224560176&id=456239120&hash=6fc1b1536ec7cdca" width="333" height="660" frameborder="0" allowfullscreen="1" allow="autoplay; encrypted-media; fullscreen; picture-in-picture"></iframe>
            // https://vk.com/clip-224560176_456239120
            $vk_video_src = '';
            $vk_video = isset($result['vk_video']) ? htmlspecialchars_decode($result['vk_video']) : '';
            if ($vk_video) {
                // get src from iframe
                preg_match('/src="([^"]+)"/', $vk_video, $matches);
                // https://vk.com/video_ext.php?oid=-224560176&id=456239120&hash=6fc1b1536ec7cdca
                $vk_video_src = $matches[1];
            }
            if ($vk_video_src) {
                $vk_video_url_parts = parse_url($vk_video_src);
                // var_dump($vk_video_url_parts);
                $vk_video_query = array();
                parse_str($vk_video_url_parts['query'], $vk_video_query);
                // var_dump($vk_video_query);
                $vk_video_oid = (isset($vk_video_query['oid']) ? $vk_video_query['oid'] : '');
                $vk_video_id = (isset($vk_video_query['id']) ? $vk_video_query['id'] : '');
                $vk_video_hash = (isset($vk_video_query['hash']) ? $vk_video_query['hash'] : '');
                if ($vk_video_oid && $vk_video_id && $vk_video_hash) {
                    $vk_video = '<iframe src="https://vk.com/video_ext.php?oid=' . $vk_video_oid . '&id=' . $vk_video_id . '&hash=' . $vk_video_hash . '&autoplay=1" frameborder="0" allow="autoplay; encrypted-media;"></iframe>';
                } else {
                    $vk_video = false;
                }
            }



            $data['posts'][] = array(
                'title' => $result['title'],
                'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
                'author' => $result['author'],
                'vk_video' => $vk_video,
                'comment_total' => $this->model_extension_blog_blog->getTotalCommentsByBlogId($result['blog_id']),
                'date_added_full' => $result['date_added'],
                'short_description' => $description,
                'count_read' => $result['count_read'],
                'tags'                 => $tags,
                'image'           => $this->model_tool_image->resize($result['image'], $setting['width'], $setting['height']),
                'href'  => $this->url->link('extension/blog/blog', 'blog_id=' . $result['blog_id'])
            );
        }

        $data['blog_show_all'] = $this->url->link('extension/blog/home');

        $data['text_show_all'] = $this->language->get('text_show_all');
        $data['text_posted_on'] = $this->language->get('text_posted_on');
        $data['text_posted_by'] = $this->language->get('text_posted_by');
        $data['text_read'] = $this->language->get('text_read');
        $data['text_comments'] = $this->language->get('text_comments');
        $data['text_not_found'] = $this->language->get('text_not_found');
        $data['heading_title_latest'] = $this->language->get('heading_title_latest');
        $data['text_read_more'] = $this->language->get('text_read_more');

        $author_status = $this->config->get('blogsetting_author');
        if (isset($author_status)) {
            $data['author_status'] = intval($this->config->get('blogsetting_author'));
        } else {
            $data['author_status'] = 0;
        }

        $data['date_added_status'] = $this->config->get('blogsetting_date_added');
        if (isset($data['date_added_status'])) {
            $data['date_added_status'] = intval($this->config->get('blogsetting_date_added'));
        } else {
            $data['date_added_status'] = 0;
        }

        $data['comments_count_status'] = $this->config->get('blogsetting_comments_count');
        if (isset($data['comments_count_status'])) {
            $data['comments_count_status'] = intval($this->config->get('blogsetting_comments_count'));
        } else {
            $data['comments_count_status'] = 0;
        }

        $data['page_view_status'] = $this->config->get('blogsetting_page_view');
        if (isset($data['page_view_status'])) {
            $data['page_view_status'] = intval($this->config->get('blogsetting_page_view'));
        } else {
            $data['page_view_status'] = 0;
        }


        $data['module'] = $module++;

        if ($this->config->get('theme_default_directory') == 'oxyo')
            return $this->load->view('extension/module/blog_latest', $data);
    }
}

// <iframe src="https://vk.com/video_ext.php?oid=-224560176&id=456239120&hash=6fc1b1536ec7cdca" width="333" height="660" frameborder="0" allowfullscreen="1" allow="autoplay; encrypted-media; fullscreen; picture-in-picture"></iframe>
// https://vk.com/clip-224560176_456239120

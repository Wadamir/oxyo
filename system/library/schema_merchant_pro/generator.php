<?php
class SchemaMerchantProGenerator {
    private $registry;
    private $config;
    private $tax;

    public function __construct($registry) {
        $this->registry = $registry;
        $this->config = $registry->get('config');
        $this->tax = $registry->get('tax');
    }

    public function renderPageMarkup(array $context = array()) {
        $output = '';
        $compatibility_mode = $this->getCompatibilityMode();

        if ($compatibility_mode !== 'open_graph_only' && $this->isEnabled('module_schema_merchant_pro_output_jsonld', true)) {
            $graph = $this->buildGraph($context);

            if (!empty($graph)) {
                $payload = array(
                    '@context' => 'https://schema.org',
                    '@graph' => $this->removeEmptyValues($graph)
                );

                $output .= '<script type="application/ld+json">' . json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>' . "\n";
            }
        }

        if ($compatibility_mode !== 'schema_only') {
            $output .= $this->renderSocialMeta($context);
        }

        return $output;
    }

    private function renderSocialMeta(array $context) {
        $tags = array();

        if ($this->isEnabled('module_schema_merchant_pro_output_open_graph', true)) {
            $tags = array_merge($tags, $this->buildOpenGraphTags($context));
        }

        if ($this->isEnabled('module_schema_merchant_pro_output_twitter', true)) {
            $tags = array_merge($tags, $this->buildTwitterTags($context));
        }

        if (empty($tags)) {
            return '';
        }

        $html = '';

        foreach ($tags as $tag) {
            if (isset($tag['property'])) {
                $html .= '<meta property="' . $this->escapeAttribute($tag['property']) . '" content="' . $this->escapeAttribute($tag['content']) . '" />' . "\n";
            } elseif (isset($tag['name'])) {
                $html .= '<meta name="' . $this->escapeAttribute($tag['name']) . '" content="' . $this->escapeAttribute($tag['content']) . '" />' . "\n";
            }
        }

        return $html;
    }

    public function buildGraph(array $context = array()) {
        $route = isset($context['route']) ? $context['route'] : '';
        $product = isset($context['product']) && is_array($context['product']) ? $context['product'] : array();
        $site = isset($context['site']) && is_array($context['site']) ? $context['site'] : array();

        $graph = array();
        $organization = array();

        if ($this->isEnabled('module_schema_merchant_pro_organization_enabled', true)) {
            $organization = $this->buildOrganization($site);

            if (!empty($organization)) {
                $graph[] = $organization;
            }
        }

        if ($this->isEnabled('module_schema_merchant_pro_website_enabled', true)) {
            $website = $this->buildWebsite($site);

            if (!empty($website)) {
                $graph[] = $website;
            }
        }

        if ($route !== 'product/product' || empty($product)) {
            return $graph;
        }

        if ($this->isEnabled('module_schema_merchant_pro_product_enabled', true)) {
            $product_schema = $this->buildProduct($product);

            if (!empty($product_schema)) {
                $graph[] = $product_schema;
            }
        }

        if ($this->isEnabled('module_schema_merchant_pro_breadcrumbs_enabled', true)) {
            $breadcrumb_schema = $this->buildBreadcrumbList($product);

            if (!empty($breadcrumb_schema)) {
                $graph[] = $breadcrumb_schema;
            }
        }

        return $graph;
    }

    private function buildOpenGraphTags(array $context) {
        $meta = $this->buildSocialContext($context);

        if (empty($meta)) {
            return array();
        }

        $native_open_graph = isset($context['native_open_graph']) && is_array($context['native_open_graph']) ? $context['native_open_graph'] : array();
        $tags = array();

        $this->addOpenGraphTag($tags, $native_open_graph, 'og:type', $meta['type']);
        $this->addOpenGraphTag($tags, $native_open_graph, 'og:title', $meta['title']);
        $this->addOpenGraphTag($tags, $native_open_graph, 'og:url', $meta['url']);
        $this->addOpenGraphTag($tags, $native_open_graph, 'og:site_name', $meta['site_name']);

        if (!empty($meta['description'])) {
            $this->addOpenGraphTag($tags, $native_open_graph, 'og:description', $meta['description']);
        }

        if (!empty($meta['image'])) {
            $this->addOpenGraphTag($tags, $native_open_graph, 'og:image', $meta['image']);
        }

        if (!empty($meta['price'])) {
            $this->addOpenGraphTag($tags, $native_open_graph, 'product:price:amount', $meta['price']);
            $this->addOpenGraphTag($tags, $native_open_graph, 'product:price:currency', $meta['currency']);
        }

        return $tags;
    }

    private function addOpenGraphTag(array &$tags, array $native_open_graph, $property, $content) {
        if ($content === '' || $this->hasNativeOpenGraphTag($native_open_graph, $property)) {
            return;
        }

        $tags[] = array('property' => $property, 'content' => $content);
    }

    private function hasNativeOpenGraphTag(array $native_open_graph, $property) {
        return !empty($native_open_graph[$property]);
    }

    private function buildTwitterTags(array $context) {
        $meta = $this->buildSocialContext($context);

        if (empty($meta)) {
            return array();
        }

        $tags = array(
            array('name' => 'twitter:card', 'content' => !empty($meta['image']) ? 'summary_large_image' : 'summary'),
            array('name' => 'twitter:title', 'content' => $meta['title'])
        );

        if (!empty($meta['description'])) {
            $tags[] = array('name' => 'twitter:description', 'content' => $meta['description']);
        }

        if (!empty($meta['image'])) {
            $tags[] = array('name' => 'twitter:image', 'content' => $meta['image']);
        }

        $handle = $this->cleanText($this->config->get('module_schema_merchant_pro_twitter_handle'));

        if ($handle !== '') {
            $tags[] = array('name' => 'twitter:site', 'content' => $handle);
        }

        return $tags;
    }

    private function buildSocialContext(array $context) {
        $route = isset($context['route']) ? $context['route'] : '';
        $product = isset($context['product']) && is_array($context['product']) ? $context['product'] : array();
        $site = isset($context['site']) && is_array($context['site']) ? $context['site'] : array();
        $site_url = $this->absoluteUrl($this->settingOrContext('module_schema_merchant_pro_website_url', $site, 'url'));
        $site_name = $this->cleanText($this->settingOrContext('module_schema_merchant_pro_website_name', $site, 'name'));

        if ($site_url === '' || $site_name === '') {
            return array();
        }

        if ($route === 'product/product' && !empty($product)) {
            $url = $this->absoluteUrl(isset($product['url']) ? $product['url'] : '');
            $title = $this->cleanText(!empty($product['heading_title']) ? $product['heading_title'] : (isset($product['name']) ? $product['name'] : ''));

            if ($url === '' || $title === '') {
                return array();
            }

            $description = $this->socialDescription(!empty($product['meta_description']) ? $product['meta_description'] : (isset($product['description']) ? $product['description'] : ''));
            $images = $this->absoluteUrlList(isset($product['images']) && is_array($product['images']) ? $product['images'] : array());
            $price = '';

            if (!empty($product['price_visible'])) {
                $raw_price = (isset($product['special']) && $product['special'] !== null && $product['special'] !== '' && (float)$product['special'] >= 0) ? $product['special'] : (isset($product['price']) ? $product['price'] : null);
                $price = $raw_price !== null ? $this->formatPrice($raw_price, isset($product['tax_class_id']) ? (int)$product['tax_class_id'] : 0) : '';
            }

            return array(
                'type' => 'product',
                'title' => $title,
                'description' => $description,
                'url' => $url,
                'image' => !empty($images) ? $images[0] : '',
                'site_name' => $site_name,
                'price' => $price,
                'currency' => isset($product['currency']) ? $product['currency'] : $this->config->get('config_currency')
            );
        }

        return array(
            'type' => 'website',
            'title' => $site_name,
            'description' => '',
            'url' => $site_url,
            'image' => $this->absoluteUrl($this->settingOrContext('module_schema_merchant_pro_organization_logo', $site, 'logo')),
            'site_name' => $site_name,
            'price' => '',
            'currency' => ''
        );
    }

    private function buildOrganization(array $site) {
        $url = $this->absoluteUrl($this->settingOrContext('module_schema_merchant_pro_organization_url', $site, 'url'));
        $name = $this->cleanText($this->settingOrContext('module_schema_merchant_pro_organization_name', $site, 'name'));

        if ($url === '' || $name === '') {
            return array();
        }

        $schema = array(
            '@type' => 'Organization',
            '@id' => rtrim($url, '/') . '/#organization',
            'name' => $name,
            'url' => $url
        );

        $logo = $this->absoluteUrl($this->settingOrContext('module_schema_merchant_pro_organization_logo', $site, 'logo'));

        if ($logo !== '') {
            $schema['logo'] = $logo;
            $schema['image'] = $logo;
        }

        $email = $this->cleanText($this->settingOrContext('module_schema_merchant_pro_organization_email', $site, 'email'));

        if ($email !== '') {
            $schema['email'] = $email;
        }

        $phone = $this->cleanText($this->settingOrContext('module_schema_merchant_pro_organization_phone', $site, 'phone'));

        if ($phone !== '') {
            $schema['telephone'] = $phone;
        }

        $address = $this->buildPostalAddress();

        if (!empty($address)) {
            $schema['address'] = $address;
        }

        $opening_hours = $this->buildOpeningHoursSpecification();

        if (!empty($opening_hours)) {
            $schema['openingHoursSpecification'] = $opening_hours;
        }

        $geo = $this->buildGeoCoordinates();

        if (!empty($geo)) {
            $schema['geo'] = $geo;
        }

        $same_as = $this->buildSameAs();

        if (!empty($same_as)) {
            $schema['sameAs'] = $same_as;
        }

        return $schema;
    }

    private function buildWebsite(array $site) {
        $url = $this->absoluteUrl($this->settingOrContext('module_schema_merchant_pro_website_url', $site, 'url'));
        $name = $this->cleanText($this->settingOrContext('module_schema_merchant_pro_website_name', $site, 'name'));

        if ($url === '' || $name === '') {
            return array();
        }

        $schema = array(
            '@type' => 'WebSite',
            '@id' => rtrim($url, '/') . '/#website',
            'url' => $url,
            'name' => $name
        );

        $language = $this->cleanText($this->settingOrContext('module_schema_merchant_pro_website_language', $site, 'language'));

        if ($language !== '') {
            $schema['inLanguage'] = $language;
        }

        if ($this->isEnabled('module_schema_merchant_pro_search_action_enabled', true)) {
            $search_url = $this->absoluteUrl(isset($site['search_url']) ? $site['search_url'] : '');

            if ($search_url !== '' && strpos($search_url, '{search_term_string}') !== false) {
                $schema['potentialAction'] = array(
                    '@type' => 'SearchAction',
                    'target' => $search_url,
                    'query-input' => 'required name=search_term_string'
                );
            }
        }

        return $schema;
    }

    private function buildProduct(array $product) {
        $url = $this->absoluteUrl(isset($product['url']) ? $product['url'] : '');

        if ($url === '') {
            return array();
        }

        $name = $this->cleanText(!empty($product['heading_title']) ? $product['heading_title'] : (isset($product['name']) ? $product['name'] : ''));

        if ($name === '') {
            return array();
        }

        $schema = array(
            '@type' => 'Product',
            '@id' => $url . '#product',
            'name' => $name,
            'url' => $url
        );

        $description = $this->cleanText(!empty($product['meta_description']) ? $product['meta_description'] : (isset($product['description']) ? $product['description'] : ''));

        if ($description !== '') {
            $schema['description'] = $description;
        }

        $images = $this->absoluteUrlList(isset($product['images']) && is_array($product['images']) ? $product['images'] : array());

        if (!empty($images)) {
            $schema['image'] = $images;
        }

        if ($this->isEnabled('module_schema_merchant_pro_identifiers_enabled', true)) {
            $this->addIdentifiers($schema, $product);
        }

        if ($this->isEnabled('module_schema_merchant_pro_brand_enabled', true) && !empty($product['manufacturer'])) {
            $schema['brand'] = array(
                '@type' => 'Brand',
                'name' => $this->cleanText($product['manufacturer'])
            );
        }

        if ($this->isEnabled('module_schema_merchant_pro_attributes_enabled', true)) {
            $properties = $this->buildAdditionalProperties($product);

            if (!empty($properties)) {
                $schema['additionalProperty'] = $properties;
            }
        }

        if ($this->isEnabled('module_schema_merchant_pro_offer_enabled', true)) {
            $offer = $this->buildOffer($product, $url);

            if (!empty($offer)) {
                $schema['offers'] = $offer;
            }
        }

        if ($this->isEnabled('module_schema_merchant_pro_rating_enabled', false)) {
            $aggregate_rating = $this->buildAggregateRating($product);

            if (!empty($aggregate_rating)) {
                $schema['aggregateRating'] = $aggregate_rating;
            }
        }

        if ($this->isEnabled('module_schema_merchant_pro_reviews_enabled', false)) {
            $reviews = $this->buildReviews($product);

            if (!empty($reviews)) {
                $schema['review'] = $reviews;
            }
        }

        return $schema;
    }

    private function buildBreadcrumbList(array $product) {
        $url = $this->absoluteUrl(isset($product['url']) ? $product['url'] : '');
        $breadcrumbs = isset($product['breadcrumbs']) && is_array($product['breadcrumbs']) ? $product['breadcrumbs'] : array();

        if ($url === '' || empty($breadcrumbs)) {
            return array();
        }

        $items = array();
        $position = 1;

        foreach ($breadcrumbs as $breadcrumb) {
            $name = $this->cleanText(isset($breadcrumb['text']) ? $breadcrumb['text'] : '');
            $item_url = $this->absoluteUrl(isset($breadcrumb['href']) ? $breadcrumb['href'] : '');

            if ($name === '' && $position === 1 && $item_url !== '') {
                $name = $this->getHomeBreadcrumbName();
            }

            if ($name === '' || $item_url === '') {
                continue;
            }

            $items[] = array(
                '@type' => 'ListItem',
                'position' => $position,
                'name' => $name,
                'item' => $item_url
            );

            $position++;
        }

        if (count($items) < 2) {
            return array();
        }

        return array(
            '@type' => 'BreadcrumbList',
            '@id' => $url . '#breadcrumb',
            'itemListElement' => $items
        );
    }

    private function buildOffer(array $product, $url) {
        if (empty($product['price_visible'])) {
            return array();
        }

        $raw_price = null;

        if (isset($product['special']) && $product['special'] !== null && $product['special'] !== '' && (float)$product['special'] >= 0) {
            $raw_price = $product['special'];
        } elseif (isset($product['price']) && $product['price'] !== null && $product['price'] !== '') {
            $raw_price = $product['price'];
        }

        if ($raw_price === null) {
            return array();
        }

        $price = $this->formatPrice($raw_price, isset($product['tax_class_id']) ? (int)$product['tax_class_id'] : 0);

        if ($price === '') {
            return array();
        }

        $offer = array(
            '@type' => 'Offer',
            'url' => $url,
            'price' => $price,
            'priceCurrency' => $this->getProductCurrency($product),
            'availability' => $this->getProductAvailability($product),
            'itemCondition' => 'https://schema.org/NewCondition'
        );

        $shipping_details = $this->buildShippingDetails($offer['priceCurrency']);

        if (!empty($shipping_details)) {
            $offer['shippingDetails'] = $shipping_details;
        }

        $return_policy = $this->buildMerchantReturnPolicy();

        if (!empty($return_policy)) {
            $offer['hasMerchantReturnPolicy'] = $return_policy;
        }

        return $offer;
    }

    private function getProductAvailability(array $product) {
        if (!empty($product['quantity'])) {
            return 'https://schema.org/InStock';
        }

        $stock_status_id = isset($product['stock_status_id']) ? (int)$product['stock_status_id'] : 0;
        $mapped = $stock_status_id > 0 ? $this->schemaAvailability($this->config->get('module_schema_merchant_pro_availability_stock_status_' . $stock_status_id)) : '';

        return $mapped !== '' ? $mapped : 'https://schema.org/OutOfStock';
    }

    private function schemaAvailability($value) {
        $value = trim((string)$value);

        return in_array($value, array(
            'https://schema.org/InStock',
            'https://schema.org/OutOfStock',
            'https://schema.org/PreOrder',
            'https://schema.org/BackOrder',
            'https://schema.org/Discontinued',
            'https://schema.org/LimitedAvailability'
        ), true) ? $value : '';
    }

    private function buildShippingDetails($fallback_currency) {
        if (!$this->isEnabled('module_schema_merchant_pro_shipping_enabled', false)) {
            return array();
        }

        $country = $this->countryCode($this->config->get('module_schema_merchant_pro_shipping_country'));
        $price = $this->positiveOrZeroNumber($this->config->get('module_schema_merchant_pro_shipping_price'));
        $currency = $this->currencyCode($this->config->get('module_schema_merchant_pro_shipping_currency'));

        if ($currency === '') {
            $currency = $this->currencyCode($fallback_currency);
        }

        if ($country === '' || $price === '' || $currency === '') {
            return array();
        }

        $handling_time = $this->buildQuantitativeTime(
            $this->config->get('module_schema_merchant_pro_shipping_handling_min_days'),
            $this->config->get('module_schema_merchant_pro_shipping_handling_max_days')
        );

        $transit_time = $this->buildQuantitativeTime(
            $this->config->get('module_schema_merchant_pro_shipping_transit_min_days'),
            $this->config->get('module_schema_merchant_pro_shipping_transit_max_days')
        );

        if (empty($handling_time) || empty($transit_time)) {
            return array();
        }

        return array(
            '@type' => 'OfferShippingDetails',
            'shippingDestination' => array(
                '@type' => 'DefinedRegion',
                'addressCountry' => $country
            ),
            'shippingRate' => array(
                '@type' => 'MonetaryAmount',
                'value' => $price,
                'currency' => $currency
            ),
            'deliveryTime' => array(
                '@type' => 'ShippingDeliveryTime',
                'handlingTime' => $handling_time,
                'transitTime' => $transit_time
            )
        );
    }

    private function getProductCurrency(array $product) {
        $currency = isset($product['currency']) ? $this->currencyCode($product['currency']) : '';

        if ($currency !== '') {
            return $currency;
        }

        $currency = $this->currencyCode($this->config->get('config_currency'));

        return $currency !== '' ? $currency : '';
    }

    private function buildMerchantReturnPolicy() {
        if (!$this->isEnabled('module_schema_merchant_pro_returns_enabled', false)) {
            return array();
        }

        $country = $this->countryCode($this->config->get('module_schema_merchant_pro_returns_country'));
        $category = $this->schemaUrlOption($this->config->get('module_schema_merchant_pro_returns_category'), array(
            'https://schema.org/MerchantReturnFiniteReturnWindow',
            'https://schema.org/MerchantReturnUnlimitedWindow',
            'https://schema.org/MerchantReturnNotPermitted'
        ));

        if ($country === '' || $category === '') {
            return array();
        }

        $policy = array(
            '@type' => 'MerchantReturnPolicy',
            'applicableCountry' => $country,
            'returnPolicyCategory' => $category
        );

        if ($category === 'https://schema.org/MerchantReturnFiniteReturnWindow') {
            $return_days = $this->positiveInteger($this->config->get('module_schema_merchant_pro_returns_days'));

            if ($return_days === '') {
                return array();
            }

            $policy['merchantReturnDays'] = (int)$return_days;
        }

        $return_fees = $this->schemaUrlOption($this->config->get('module_schema_merchant_pro_returns_fees'), array(
            'https://schema.org/FreeReturn',
            'https://schema.org/ReturnFeesCustomerResponsibility'
        ));

        if ($return_fees !== '') {
            $policy['returnFees'] = $return_fees;
        }

        $return_method = $this->schemaUrlOption($this->config->get('module_schema_merchant_pro_returns_method'), array(
            'https://schema.org/ReturnByMail',
            'https://schema.org/ReturnInStore'
        ));

        if ($return_method !== '') {
            $policy['returnMethod'] = $return_method;
        }

        return $policy;
    }

    private function buildAggregateRating(array $product) {
        $summary = isset($product['review_summary']) && is_array($product['review_summary']) ? $product['review_summary'] : array();
        $review_count = isset($summary['review_count']) ? (int)$summary['review_count'] : (isset($product['review_count']) ? (int)$product['review_count'] : 0);
        $rating = isset($summary['rating_average']) ? (float)$summary['rating_average'] : (isset($product['rating']) ? (float)$product['rating'] : 0);

        if ($review_count < 1 || $rating <= 0 || $rating > 5) {
            return array();
        }

        return array(
            '@type' => 'AggregateRating',
            'ratingValue' => $this->formatRating($rating),
            'reviewCount' => $review_count,
            'bestRating' => '5',
            'worstRating' => '1'
        );
    }

    private function buildReviews(array $product) {
        if (empty($product['reviews']) || !is_array($product['reviews'])) {
            return array();
        }

        $reviews = array();

        foreach ($product['reviews'] as $review) {
            $author = $this->cleanText(isset($review['author']) ? $review['author'] : '');
            $text = $this->cleanText(isset($review['text']) ? $review['text'] : '');
            $rating = isset($review['rating']) ? (int)$review['rating'] : 0;

            if ($author === '' || $text === '' || $rating < 1 || $rating > 5) {
                continue;
            }

            $schema = array(
                '@type' => 'Review',
                'author' => array(
                    '@type' => 'Person',
                    'name' => $author
                ),
                'reviewBody' => $text,
                'reviewRating' => array(
                    '@type' => 'Rating',
                    'ratingValue' => (string)$rating,
                    'bestRating' => '5',
                    'worstRating' => '1'
                )
            );

            $date_published = $this->formatDate(isset($review['date_added']) ? $review['date_added'] : '');

            if ($date_published !== '') {
                $schema['datePublished'] = $date_published;
            }

            $reviews[] = $schema;
        }

        return $reviews;
    }

    private function addIdentifiers(array &$schema, array $product) {
        if (!empty($product['sku'])) {
            $schema['sku'] = $this->cleanText($product['sku']);
        }

        if (!empty($product['mpn'])) {
            $schema['mpn'] = $this->cleanText($product['mpn']);
        } elseif (!empty($product['model'])) {
            $schema['mpn'] = $this->cleanText($product['model']);
        }

        if (!empty($product['upc'])) {
            $schema['gtin12'] = $this->cleanText($product['upc']);
        }

        if (!empty($product['ean'])) {
            $schema['gtin13'] = $this->cleanText($product['ean']);
        }

        if (!empty($product['jan'])) {
            $schema['gtin13'] = $this->cleanText($product['jan']);
        }

        if (!empty($product['isbn'])) {
            $schema['isbn'] = $this->cleanText($product['isbn']);
        }
    }

    private function buildAdditionalProperties(array $product) {
        $properties = array();

        if (empty($product['attribute_groups']) || !is_array($product['attribute_groups'])) {
            return $properties;
        }

        foreach ($product['attribute_groups'] as $group) {
            if (empty($group['attribute']) || !is_array($group['attribute'])) {
                continue;
            }

            foreach ($group['attribute'] as $attribute) {
                $name = $this->cleanText(isset($attribute['name']) ? $attribute['name'] : '');
                $value = $this->cleanText(isset($attribute['text']) ? $attribute['text'] : '');

                if ($name === '' || $value === '') {
                    continue;
                }

                $properties[] = array(
                    '@type' => 'PropertyValue',
                    'name' => $name,
                    'value' => $value
                );
            }
        }

        return $properties;
    }

    private function buildPostalAddress() {
        $address = array(
            '@type' => 'PostalAddress'
        );

        $fields = array(
            'streetAddress' => 'module_schema_merchant_pro_organization_street',
            'addressLocality' => 'module_schema_merchant_pro_organization_city',
            'addressRegion' => 'module_schema_merchant_pro_organization_region',
            'postalCode' => 'module_schema_merchant_pro_organization_postcode',
            'addressCountry' => 'module_schema_merchant_pro_organization_country'
        );

        foreach ($fields as $schema_key => $setting_key) {
            $value = $this->cleanText($this->config->get($setting_key));

            if ($value !== '') {
                $address[$schema_key] = $value;
            }
        }

        return count($address) > 1 ? $address : array();
    }

    private function buildOpeningHoursSpecification() {
        if (!$this->isEnabled('module_schema_merchant_pro_organization_opening_hours_enabled', false)) {
            return array();
        }

        $days = $this->parseOpeningDays($this->config->get('module_schema_merchant_pro_organization_opening_days'));
        $opens = $this->schemaTime($this->config->get('module_schema_merchant_pro_organization_opening_opens'));
        $closes = $this->schemaTime($this->config->get('module_schema_merchant_pro_organization_opening_closes'));

        if (empty($days) || $opens === '' || $closes === '') {
            return array();
        }

        return array(
            array(
                '@type' => 'OpeningHoursSpecification',
                'dayOfWeek' => $days,
                'opens' => $opens,
                'closes' => $closes
            )
        );
    }

    private function parseOpeningDays($value) {
        $map = array(
            'Mo' => 'https://schema.org/Monday',
            'Tu' => 'https://schema.org/Tuesday',
            'We' => 'https://schema.org/Wednesday',
            'Th' => 'https://schema.org/Thursday',
            'Fr' => 'https://schema.org/Friday',
            'Sa' => 'https://schema.org/Saturday',
            'Su' => 'https://schema.org/Sunday'
        );
        $days = array();
        $parts = explode(',', (string)$value);

        foreach ($parts as $part) {
            $code = trim($part);

            if (isset($map[$code])) {
                $days[] = $map[$code];
            }
        }

        return array_values(array_unique($days));
    }

    private function buildGeoCoordinates() {
        if (!$this->isEnabled('module_schema_merchant_pro_organization_geo_enabled', false)) {
            return array();
        }

        $latitude = $this->schemaCoordinate($this->config->get('module_schema_merchant_pro_organization_geo_latitude'), -90, 90);
        $longitude = $this->schemaCoordinate($this->config->get('module_schema_merchant_pro_organization_geo_longitude'), -180, 180);

        if ($latitude === null || $longitude === null) {
            return array();
        }

        return array(
            '@type' => 'GeoCoordinates',
            'latitude' => $latitude,
            'longitude' => $longitude
        );
    }

    private function buildSameAs() {
        $same_as = array();
        $lines = preg_split('/\r\n|\r|\n/', (string)$this->config->get('module_schema_merchant_pro_organization_same_as'));

        foreach ($lines as $line) {
            $url = $this->absoluteUrl($line);

            if ($url !== '') {
                $same_as[] = $url;
            }
        }

        return array_values(array_unique($same_as));
    }

    private function settingOrContext($setting_key, array $context, $context_key) {
        $value = $this->config->get($setting_key);

        if ($value !== null && $value !== '') {
            return $value;
        }

        return isset($context[$context_key]) ? $context[$context_key] : '';
    }

    private function formatPrice($price, $tax_class_id) {
        if (!is_numeric($price)) {
            return '';
        }

        $value = (float)$price;

        if ($this->tax) {
            $value = (float)$this->tax->calculate($value, $tax_class_id, $this->config->get('config_tax'));
        }

        if ($value < 0) {
            return '';
        }

        return number_format($value, 2, '.', '');
    }

    private function formatRating($rating) {
        if (!is_numeric($rating)) {
            return '';
        }

        $value = (float)$rating;

        if ($value <= 0 || $value > 5) {
            return '';
        }

        return rtrim(rtrim(number_format($value, 2, '.', ''), '0'), '.');
    }

    private function positiveOrZeroNumber($value) {
        if (!is_numeric($value)) {
            return '';
        }

        $value = (float)$value;

        if ($value < 0) {
            return '';
        }

        return number_format($value, 2, '.', '');
    }

    private function positiveInteger($value) {
        if (!is_numeric($value)) {
            return '';
        }

        $value = (int)$value;

        return $value > 0 ? (string)$value : '';
    }

    private function nonNegativeInteger($value) {
        if (!is_numeric($value)) {
            return '';
        }

        $value = (int)$value;

        return $value >= 0 ? (string)$value : '';
    }

    private function buildQuantitativeTime($min, $max) {
        $min = $this->nonNegativeInteger($min);
        $max = $this->nonNegativeInteger($max);

        if ($min === '' || $max === '' || (int)$max < (int)$min) {
            return array();
        }

        return array(
            '@type' => 'QuantitativeValue',
            'minValue' => (int)$min,
            'maxValue' => (int)$max,
            'unitCode' => 'DAY'
        );
    }

    private function countryCode($value) {
        $value = strtoupper($this->cleanText($value));

        return preg_match('/^[A-Z]{2}$/', $value) ? $value : '';
    }

    private function currencyCode($value) {
        $value = strtoupper($this->cleanText($value));

        return preg_match('/^[A-Z]{3}$/', $value) ? $value : '';
    }

    private function schemaUrlOption($value, array $allowed) {
        $value = $this->absoluteUrl($value);

        return in_array($value, $allowed, true) ? $value : '';
    }

    private function schemaTime($value) {
        $value = trim((string)$value);

        return preg_match('/^(?:[01][0-9]|2[0-3]):[0-5][0-9]$/', $value) ? $value : '';
    }

    private function schemaCoordinate($value, $min, $max) {
        $value = trim((string)$value);

        if (!preg_match('/^-?[0-9]+\\.[0-9]{5,}$/', $value)) {
            return null;
        }

        $number = (float)$value;

        if ($number < $min || $number > $max) {
            return null;
        }

        return $number;
    }

    private function formatDate($date) {
        $timestamp = strtotime((string)$date);

        if (!$timestamp) {
            return '';
        }

        return date('Y-m-d', $timestamp);
    }

    private function cleanText($value) {
        $value = (string)$value;

        for ($i = 0; $i < 3; $i++) {
            $decoded = html_entity_decode($value, ENT_QUOTES, 'UTF-8');

            if ($decoded === $value) {
                break;
            }

            $value = $decoded;
        }

        $value = strip_tags($value);
        $value = preg_replace('/\s+/u', ' ', $value);

        return trim($value);
    }

    private function socialDescription($value) {
        return $this->truncateText($this->cleanText($value), 200);
    }

    private function truncateText($value, $limit) {
        $value = (string)$value;
        $limit = (int)$limit;

        if ($value === '' || $limit <= 0) {
            return '';
        }

        if ($this->textLength($value) <= $limit) {
            return $value;
        }

        $suffix = '...';
        $cut_limit = max(0, $limit - strlen($suffix));
        $cut = $this->textSubstring($value, 0, $cut_limit);
        $space = $this->textLastPosition($cut, ' ');

        if ($space !== false && $space >= 60) {
            $cut = $this->textSubstring($cut, 0, $space);
        }

        return rtrim($cut) . $suffix;
    }

    private function textLength($value) {
        if (function_exists('mb_strlen')) {
            return mb_strlen($value, 'UTF-8');
        }

        return preg_match_all('/./us', $value, $matches);
    }

    private function textSubstring($value, $start, $length) {
        if (function_exists('mb_substr')) {
            return mb_substr($value, $start, $length, 'UTF-8');
        }

        if (!preg_match_all('/./us', $value, $matches)) {
            return '';
        }

        return implode('', array_slice($matches[0], $start, $length));
    }

    private function textLastPosition($value, $needle) {
        if (function_exists('mb_strrpos')) {
            return mb_strrpos($value, $needle, 0, 'UTF-8');
        }

        return strrpos($value, $needle);
    }

    private function getHomeBreadcrumbName() {
        $language = $this->registry->get('language');

        if ($language && $language->get('text_home') !== 'text_home') {
            return $this->cleanText($language->get('text_home'));
        }

        $language_code = (string)$this->config->get('config_language');

        if (stripos($language_code, 'ru') === 0) {
            return 'Главная';
        }

        return 'Home';
    }

    private function absoluteUrl($url) {
        $url = trim(html_entity_decode((string)$url, ENT_QUOTES, 'UTF-8'));

        if ($url === '' || !preg_match('#^https?://#i', $url)) {
            return '';
        }

        return $url;
    }

    private function absoluteUrlList(array $urls) {
        $result = array();

        foreach ($urls as $url) {
            $url = $this->absoluteUrl($url);

            if ($url !== '') {
                $result[] = $url;
            }
        }

        return array_values(array_unique($result));
    }

    private function isEnabled($key, $default = false) {
        $value = $this->config->get($key);

        if ($value === null || $value === '') {
            return (bool)$default;
        }

        return (bool)$value;
    }

    private function getCompatibilityMode() {
        $mode = (string)$this->config->get('module_schema_merchant_pro_compatibility_mode');
        $allowed = array('normal', 'careful', 'schema_only', 'open_graph_only');

        return in_array($mode, $allowed, true) ? $mode : 'careful';
    }

    private function escapeAttribute($value) {
        return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
    }

    private function removeEmptyValues($value) {
        if (is_array($value)) {
            $clean = array();

            foreach ($value as $key => $item) {
                $item = $this->removeEmptyValues($item);

                if ($item === null || $item === '' || $item === array()) {
                    continue;
                }

                $clean[$key] = $item;
            }

            return $clean;
        }

        return $value;
    }
}

<?php
namespace yamarket_fusion\Xml\Catalog\Sitemap;

class Yml extends \yamarket_fusion\Xml\Yml {
	const tagYml = 'urlset';
	const tagName = 'title';
	const tagUrl = 'link';

	public function getXML() {
		$str = '<?xml version="1.0" encoding="' . $this->encoding . '"?>'
				.	'<' . $this->tag('yml') . ' xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">'
				//.		self::renderTag($this->tag('name'), $this->shop_name)
				//.		self::renderTag($this->tag('url'), $this->url, array('rel' => 'alternate', 'type' => 'text/html'))
				//.		"<updated>" . date('c', strtotime($this->date)) . "</updated>"

				.		$this->renderOffers()

				. 		($this->custom_content === null ? '' : $this->custom_content)
				.	"</{$this->tag('yml')}>";

		return $str;
	}

	protected function renderOffers() {
		return implode('', $this->offers);
	}
}
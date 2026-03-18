<?php
namespace yamarket_fusion\Xml\Catalog\Facebook;

class Yml extends \yamarket_fusion\Xml\Yml {
	const tagYml = 'rss';
	const tagName = 'title';
	const tagUrl = 'link';

	public function getXML() {
		$str = '<?xml version="1.0"?>'
				.	'<' . $this->tag('yml') . ' xmlns:g="http://base.google.com/ns/1.0" version="2.0">'
				.	'<channel>'
				.		self::renderTag($this->tag('name'), $this->shop_name)
				.		self::renderTag($this->tag('url'), $this->url, array('rel' => 'alternate', 'type' => 'text/html'))
				//.		"<updated>" . date('c', strtotime($this->date)) . "</updated>"

				.		$this->renderOffers()

				. 		($this->custom_content === null ? '' : $this->custom_content)
				.	'</channel>'
				.	"</{$this->tag('yml')}>";

		return $str;
	}

	protected function renderOffers() {
		return implode('', $this->offers);
	}
}
<?php
namespace yamarket_fusion\Xml;

class PromoOffer extends Node {
	public $id;
	public $description;
	public $required_quantity = 1;

	protected $_offers = array();
	protected $_gifts = array();

	const tagOffers = 'purchase';
	const tagGifts = 'promo-gifts';
	const tagPromos = 'promos';
	const tagPromo = 'promo';

	public function __construct($id = null) {
		if ($id !== null)
			$this->id = $id;
	}

	public function addOffer($offer_id) {
		$this->_offers[$offer_id] = $offer_id;
	}

	public function addGift($id, $is_offer = true) {
		$this->_gifts[$id] = array('id' => $id, 'is_offer' => $is_offer);
	}

	protected function renderDescription() {
		if (!utf8_strlen($this->description))
			return '';

		$desc = new Node('description');
		$desc->setRawValue($this->description);

		return $desc->getXml();
	}

	protected function renderOffers() {
		if (!$this->_offers)
			return '';

		$output = static::beginTag(static::tagOffers);
		$output .= static::renderTag('required-quantity', $this->required_quantity);

		foreach ($this->_offers as $offer_id) {
			$output .= static::renderTag('product', '', array('offer-id' => $offer_id));
		}

		$output .= static::endTag(static::tagOffers);

		return $output;
	}

	protected function renderGifts() {
		if (!$this->_gifts)
			return '';

		$output = static::beginTag(static::tagGifts);

		foreach ($this->_gifts as $gift) {
			$type = $gift['is_offer'] ? 'offer-id' : 'gift-id';
			$output .= static::renderTag('promo-gift', '', array($type => $gift['id']));
		}

		$output .= static::endTag(static::tagGifts);

		return $output;
	}

	public function getXml() {
		$xml = static::beginTag(static::tagPromo, array('type' => 'gift with purchase', 'id' => $this->id));
		$xml .= $this->renderDescription();
		$xml .= $this->renderOffers();
		$xml .= $this->renderGifts();
		$xml .= static::endTag(static::tagPromo);

		return $xml;
	}
} 
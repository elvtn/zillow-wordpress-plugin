<?php

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Class for calling Zillow APIs documented here:
 *
 * https://www.zillow.com/mortgage/api/
 *
 * These APIs require a partnerId, which can be obtained from Zillow support. Each call
 * also takes a lenderRef, which is 
 */
class Elvtn_Zillow_Api
{
	private $partnerId;

	public function __construct($partnerId = null)
	{
		if ( ! empty($partnerId))
		{
			$this->partnerId = $partnerId;
		}
	}

	public function __set($property, $value)
	{
		$this->$property = $value;
	}

	public function __get($property)
	{
		if (isset($this->$property)) {
			return $this->$property;
		}
	}

	/**
	 * Calls the Zillow getPublishedLenderReviews API and returns a block of HTML
	 * with latest Zillow reviews for the given lenderRef.
	 * 
	 * $screenName: Zillow screen name for which to get reviews.
	 * $numReviews: Number of reviews (default 5)
	 */
	public function getPublishedLenderReviews($screenName , $numReviews = 5)
	{
		if ( empty($this->partnerId) )
		{
			throw new Exception('Zillow partnerId is required.');
		}

		if( empty($screenName) )
		{
			throw new Exception('Zillow screenName is required.');
		}

		$params = [
			"partnerId" => $this->partnerId,
			"lenderRef" => ["screenName" => $screenName],
			"fields"    => ["reviewerName" , "created" , "rating" , "content"],
			"pageSize"  => (int)$numReviews
		];

		$url = "https://mortgageapi.zillow.com/getPublishedLenderReviews";

		$curl_res = $this->curl_post($url, array(), array(), $params);

		return $this->convert_getPublishedLenderReviews_to_html($curl_res);
	}

	/**
	 * Helper method to execute curl POST calls to Zillow API server
	 */
	private function curl_post($url, array $get = NULL, array $options = array(), array $body = array()) 
	{
		$json_body = json_encode($body);

	    $defaults = array(
	        CURLOPT_URL            => $url . (strpos($url, '?') === FALSE ? '?' : '') . http_build_query($get),
	        CURLOPT_CUSTOMREQUEST  => "POST",
	        CURLOPT_POSTFIELDS     => $json_body,
	        //CURLOPT_HEADER         => 0,
	        CURLOPT_RETURNTRANSFER => TRUE,
	        //CURLOPT_TIMEOUT        => 4,
	        CURLOPT_SSL_VERIFYHOST => 0,
	        CURLOPT_SSL_VERIFYHOST => 0,
	        CURLOPT_HTTPHEADER     => array(
    			'Content-Type: application/json',
    			'Content-Length: ' . strlen($json_body)
    		)
	    );

	    $ch = curl_init();
	    curl_setopt_array($ch, ($options + $defaults));
	    if( ! $result = curl_exec($ch))
	    {
	        trigger_error(curl_error($ch));
	    }
	    curl_close($ch);

	    return $result;
	}

	/**
	 * Converts the JSON output of getPublishedLenderReviews to HTML.
	 */
	private function convert_getPublishedLenderReviews_to_html($json)
	{
		$obj = json_decode($json, true, JSON_NUMERIC_CHECK);

		if($obj == NULL || $obj['reviews'] == NULL)
		{
			return "<p class=\"elvtn-zillow-pro-review-error\">Unable to load Zillow reviews.</p>";
		}

		$html = "<div class=\"elvtn-zillow-pro-reviews\">";
		foreach($obj['reviews'] as $review)
		{
			// Pull out MM/DD/YYYY from the date/time field
			$reviewYear = substr($review['created'], 0, 4);
			$reviewMon  = substr($review['created'], 5, 2);
			$reviewDay  = substr($review['created'], 8, 2);

			$html .= "<div class=\"elvtn-zillow-pro-review\">";
			$html .= "<h4 class=\"elvtn-zillow-pro-review-meta\">" . $review['rating'] . " stars on " .  intval($reviewMon) . "/" . intval($reviewDay) . "/" . $reviewYear . "</h4>";
			$html .= "<p class=\"elvtn-zillow-pro-review-description\"><i>" . $review['content'] . "</i></p>";
			$html .= "<hr class=\"elvtn-zillow-pro-review-line\"/></div>";
		}
		$html .= "</div>";

		return $html;
	}
}

?>